<?php

namespace Officers\Services;

use App\Model\Entity\Warrant;
use Cake\I18n\DateTime;
use App\Services\ActiveWindowManager\ActiveWindowManagerInterface;
use App\Services\WarrantManager\WarrantManagerInterface;
use Cake\ORM\TableRegistry;
use Officers\Model\Entity\Officer;
use App\Services\ServiceResult;
use App\Services\WarrantManager\WarrantRequest;

class DefaultOfficerManager implements OfficerManagerInterface
{
    public function __construct(ActiveWindowManagerInterface $activeWindowManager, WarrantManagerInterface $warrantManager)
    {
        $this->activeWindowManager = $activeWindowManager;
        $this->warrantManager = $warrantManager;
    }
    /**
     * Assigns a member to an office - Make sure to create a transaction before calling this service
     *
     * @param ActiveWindowManagerInterface $activeWindowManager
     * @param int $officeId
     * @param int $memberId
     * @param int $branchId
     * @param DateTime $startOn
     * @param string $deputyDescription
     * @param int $approverId
     * @return ServiceResult
     */
    public function assign(
        int $officeId,
        int $memberId,
        int $branchId,
        DateTime $startOn,
        ?DateTime $endOn,
        ?string $deputyDescription,
        int $approverId,
    ): ServiceResult {
        //get officer table
        $officerTable = TableRegistry::getTableLocator()->get('Officers.Officers');
        $newOfficer = $officerTable->newEmptyEntity();
        //get office table
        $officeTable = TableRegistry::getTableLocator()->get('Officers.Offices');
        //get the office
        $office = $officeTable->get($officeId);
        if ($office->requires_warrant) {
            $member = TableRegistry::getTableLocator()->get('Members')->get($memberId);
            if (!$member->warrantable) {
                return new ServiceResult(false, "Member is not warrantable");
            }
        }

        if ($endOn === null) {
            $endOn = $startOn->addYears($office->term_length);
        }
        $status = Officer::UPCOMING_STATUS;
        if ($startOn->isToday() || $startOn->isPast()) {
            $status = Officer::CURRENT_STATUS;
        }
        if ($endOn->isPast()) {
            $status = Officer::EXPIRED_STATUS;
        }
        $newOfficer->member_id = $memberId;
        $newOfficer->office_id = $officeId;
        $newOfficer->branch_id = $branchId;
        $newOfficer->approver_id = $approverId;
        $newOfficer->approval_date = DateTime::now();
        $newOfficer->status = $status;
        $newOfficer->reports_to_office_id = $officeId;
        if ($office->deputy_to_id != null) {
            $newOfficer->deputy_description = $deputyDescription;
            $newOfficer->reports_to_branch_id = $newOfficer->branch_id;
            $newOfficer->reports_to_office_id = $office->deputy_to_id;
        } else {
            $branchTable = TableRegistry::getTableLocator()->get('Branches');
            $branch = $branchTable->get($branchId);
            if ($branch->parent_id != null) {
                if (!$office->can_skip_report) {
                    $newOfficer->reports_to_branch_id = $branch->parent_id;
                } else {
                    //iterate through the parents till we find one that has this office or the root
                    $currentBranchId = $branch->parent_id;
                    $previousBranchId = $branchId;
                    $setReportsToBranch = false;
                    while ($currentBranchId != null) {
                        $officersCount = $branchTable->CurrentOfficers->find()
                            ->where(['branch_id' => $currentBranchId, 'office_id' => $officeId])
                            ->count();
                        if ($officersCount > 0) {
                            $newOfficer->reports_to_branch_id = $currentBranchId;
                            $setReportsToBranch = true;
                            break;
                        }
                        $previousBranchId = $currentBranchId;
                        $currentBranch = $branchTable->get($currentBranchId);
                        $currentBranchId = $currentBranch->parent_id;
                    }
                    if (!$setReportsToBranch) {
                        $newOfficer->reports_to_branch_id = $previousBranchId;
                    }
                }
            } else {
                $newOfficer->reports_to_branch_id = $branch->id;
            }
        }
        //release current officers if they exist for this office
        if ($office->only_one_per_branch) {
            $currentOfficers = $officerTable->find()
                ->where([
                    'office_id' => $officeId,
                    'branch_id' => $branchId,
                    'status' => Officer::CURRENT_STATUS
                ])
                ->all();
            foreach ($currentOfficers as $currentOfficer) {
                $oResult = $this->release($currentOfficer->id, $approverId, $startOn, "Replaced by new officer", Officer::REPLACED_STATUS);
                if (!$oResult->success) {
                    return new ServiceResult(false, $oResult->reason);
                }
            }
        }
        if (!$officerTable->save($newOfficer)) {
            return new ServiceResult(false, "Failed to save officer");
        }
        $awResult = $this->activeWindowManager->start('Officers.Officers', $newOfficer->id, $approverId, $startOn, $endOn, $office->term_length, $office->grants_role_id, $office->only_one_per_branch);
        if (!$awResult->success) {
            return new ServiceResult(false, $awResult->reason);
        }
        if ($office->requires_warrant) {
            $newOfficer = $officerTable->get($newOfficer->id);
            $warrantRequest = new WarrantRequest('Officers.Officers', $newOfficer->id, $approverId, $memberId, $startOn, $endOn, $newOfficer->granted_member_role_id);
            $member = TableRegistry::getTableLocator()->get('Members')->get($memberId);
            $wmResult = $this->warrantManager->request("$office->name : $member->sca_name", "", [$warrantRequest]);
            if (!$wmResult->success) {
                return new ServiceResult(false, $wmResult->reason);
            }
        }
        return new ServiceResult(true);
    }

    /**
     * Releases an officer from their office - Make sure to create a transaction before calling this service
     *
     * @param ActiveWindowManagerInterface $activeWindowManager
     * @param int $officerId
     * @param int $revokerId
     * @param DateTime $revokedOn
     * @param string $revokedReason
     * @return ServiceResult
     */
    public function release(
        int $officerId,
        int $revokerId,
        DateTime $revokedOn,
        ?string $revokedReason,
        ?string $releaseStatus = Officer::RELEASED_STATUS
    ): ServiceResult {
        $awResult = $this->activeWindowManager->stop('Officers.Officers', $officerId, $revokerId, $releaseStatus, $revokedReason, $revokedOn);
        if (!$awResult->success) {
            return new ServiceResult(false, $awResult->reason);
        }
        $officerTable = TableRegistry::getTableLocator()->get('Officers.Officers');
        $officer = $officerTable->get($officerId, ['contain' => ['Offices']]);
        if ($officer->office->requires_warrant) {
            $wmResult = $this->warrantManager->cancelByEntity('Officers.Officers', $officerId, $revokedReason, $revokerId, $revokedOn);
            if (!$wmResult->success) {
                return new ServiceResult(false, $wmResult->reason);
            }
        }
        return new ServiceResult(true);
    }
}
