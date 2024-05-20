<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission $permission
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); 
$user = $this->request->getAttribute('identity');
?>
<div class="permissions view large-9 medium-8 columns content">
<div class="row align-items-start">
        <div class="col">
            <h3>
                <?= $this->Html->link('', ['action' => 'index'], ['class' => 'bi bi-arrow-left-circle']) ?>
                <?= h($permission->name) ?> 
            </h3>
        </div>
        <div class="col text-end">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
            <?php if (!$permission->system) { ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $permission->id], ['confirm' => __('Are you sure you want to delete {0}?', $permission->name), 'title' => __('Delete'), 'class' => 'btn btn-danger btn-sm']) ?>
            <?php } ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Name') ?></th>
                <td><?= h($permission->name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Authorization Type') ?></th>
                <td><?= $permission->hasValue('authorization_type') ? $this->Html->link($permission->authorization_type->name, ['controller' => 'AuthorizationTypes', 'action' => 'view', $permission->authorization_type->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Require Membership') ?></th>
                <td><?= $this->Kmp->bool($permission->require_active_membership, $this->Html) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Require Background Check') ?></th>
                <td><?= $this->Kmp->bool($permission->require_active_background_check, $this->Html)?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Minimum Age') ?></th>
                <td><?= h($permission->require_min_age) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('System Permission') ?></th>
                <td><?= $this->Kmp->bool($permission->system, $this->Html)?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Is Super User') ?></th>
                <td><?= $this->Kmp->bool($permission->is_super_user, $this->Html)?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Roles') ?> : 
            <?php
                if ($user->can('addPermission', 'Roles')) { ?>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">Add Role</button>
                <?php } ?></h4>
        <?php if (!empty($permission->roles)) : ?> 
        <div class="table-responsive" >
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($permission->roles as $role): ?>
                <tr>
                    <td><?= h($role->name) ?></td>
                    <td class="actions">
                    <?php if ($user->can('deletePermission', 'Roles')) { ?>
                        <?= $this->Form->postLink( __('Remove'), ['controller' => 'Roles', 'action' => 'deletePermission'], ['confirm' => __('Are you sure you want to remove {0}?', $role->name), 'class' => 'btn btn-danger','data' =>['permission_id' => $permission->id, 'role_id'=>$role->id]]) ?>
                    <?php } ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php 
    //Start writing to modal block in layout
    $this->start('modals');
?>
    
    <?php 
    echo $this->Modal->create("Add Role to Permissions", ['id' => 'addRoleModal', 'close' => true]) ;
?>
    <fieldset>
        <?php
         echo $this->Form->create(null, ['id' => 'add_role__form', 'url' => ['controller' => 'Roles', 'action' => 'addPermission']]);
            echo $this->Form->control('role_id', ['options' => $roles, 'empty' => true, 'id' => 'add_role__role_id']);
            echo $this->Form->control('permission_id', ['type' => 'hidden', 'value' => $permission->id, 'id' => 'add_role__permission_id']);
         echo $this->Form->end()
                ?>
    </fieldset>
<?php
    echo $this->Modal->end([
        $this->Form->button('Submit',['class' => 'btn btn-primary', 'id' => 'add_role__submit', 'disabled' => 'disabled']),
        $this->Form->button('Close', ['data-bs-dismiss' => 'modal'])
    ]);
?>

<?php 
    echo $this->Modal->create("Edit Permissions", ['id' => 'editModal', 'close' => true]) ;
?>
    <fieldset>
        <?php
         echo $this->Form->create($permission, ['id' => 'edit_entity', 'url' => ['controller' => 'Permissions', 'action' => 'edit', $permission->id]]);
            if ($permission->system){
                echo $this->Form->control('name', ['disabled' => 'disabled']);
            }else{
                echo $this->Form->control('name');
            }
            echo $this->Form->control('authorization_type_id', ['options' => $authorizationTypes, 'empty' => true]);
            echo $this->Form->control('require_active_membership',['switch' => true, 'label' => 'Require Membership']);
            echo $this->Form->control('require_active_background_check',['switch' => true, 'label' => 'Require Background Check']);
            echo $this->Form->control('require_min_age',['label' => 'Minimum Age', 'type' => 'number']);
            if ($user->isSuperUser()){
                echo $this->Form->control('is_super_user',['switch' => true]);
            }else
            {
                echo $this->Form->control('is_super_user',['switch' => true, 'disabled' => 'disabled']);
            }
         echo $this->Form->end()
                ?>
    </fieldset>
<?php
    echo $this->Modal->end([
        $this->Form->button('Submit',['class' => 'btn btn-primary', 'id' => 'edit_entity__submit', 'onclick' => '$("#edit_entity").submit();']),
        $this->Form->button('Close', ['data-bs-dismiss' => 'modal'])
    ]);
?>

    
<?php    
//finish writing to modal block in layout
    $this->end(); ?>

<?php
    $this->append('script', $this->Html->script(['app/permissions/view.js']));
?>


