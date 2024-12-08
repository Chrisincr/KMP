<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Warrant;
use App\Services\ActiveWindowManager\ActiveWindowManagerInterface;
use Cake\I18n\DateTime;

/**
 * Warrants Controller
 *
 * @property \App\Model\Table\WarrantsTable $Warrants
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class WarrantsController extends AppController
{
    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authorization.Authorization');

        $this->Authorization->authorizeModel("index", "deactivate");
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Warrants->find()
            ->contain(['Members', 'WarrantApprovalSets', 'MemberRoles']);
        $query = $this->Authorization->applyScope($query);
        $warrants = $this->paginate($query);

        $this->set(compact('warrants'));
    }

    public function allWarrants($state)
    {


        if ($state != 'current' && $state == 'pending' && $state == 'previous') {
            throw new \Cake\Http\Exception\NotFoundException();
        }
        $securityWarrant = $this->Warrants->newEmptyEntity();
        $this->Authorization->authorize($securityWarrant);
        $warrantsQuery = $this->Warrants->find()
            ->contain(['Members', 'WarrantApprovalSets', 'MemberRoles']);

        switch ($state) {
            case 'current':
                $warrantsQuery = $warrantsQuery->where(['Warrants.expires_on >=' => date('Y-m-d'), 'Warrants.start_on <=' => date('Y-m-d'), 'Warrants.status' => Warrant::STATUS_ACTIVE]);
                break;
            case 'previous':
                $warrantsQuery = $warrantsQuery->where(["OR" => ['Warrants.expires_on <' => date('Y-m-d'), 'Warrants.status' => Warrant::STATUS_DEACTIVATED]]);
                break;
        }
        $warrantsQuery = $this->addConditions($warrantsQuery);
        $warrants = $this->paginate($warrantsQuery);
        $this->set(compact('warrants', 'state'));
    }
    protected function addConditions($query)
    {
        return $query
            ->select(['id', 'member_id', 'warrant_for_model', 'start_on', 'expires_on', 'revoker_id', 'warrant_approval_set_id', 'status', 'revoked_reason'])
            ->contain([
                'Members' => function ($q) {
                    return $q->select(['id', 'sca_name']);
                },
                'RevokedBy' => function ($q) {
                    return $q->select(['id', 'sca_name']);
                },
            ]);
    }


    public function deactivate(ActiveWindowManagerInterface $awService, $id = null)
    {
        $this->request->allowMethod(["post"]);
        if (!$id) {
            $id = $this->request->getData("id");
        }
        $this->Warrants->getConnection()->begin();

        if (!$awService->stop("Warrants", (int)$id, $this->Authentication->getIdentity()->get("id"), "deactivated", "", DateTime::now())) {
            $this->Flash->error(
                __(
                    "The warrant could not be deactivated. Please, try again.",
                ),
            );
            $this->Warrants->getConnection()->rollback();
            return $this->redirect($this->referer());
        }

        $this->Flash->success(__("The warrant has been deactivated."));
        $this->Warrants->getConnection()->commit();
        return $this->redirect($this->referer());
    }
}
