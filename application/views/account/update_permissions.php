<?php

  set_page_title(lang('update permissions'));
  if ($company->isOwner()) {
    administration_tabbed_navigation(ADMINISTRATION_TAB_COMPANY);
    administration_crumbs(array(
      array(lang('company'), $company->getViewUrl()),
      array($contact->getDisplayName(), $contact->getCardUrl()),
      array(lang('update permissions'))
    ));
  } else {
    administration_tabbed_navigation(ADMINISTRATION_TAB_CLIENTS);
    administration_crumbs(array(
      array(lang('clients'), get_url('administration', 'clients')),
      array($company->getName(), $company->getViewUrl()),
      array($contact->getDisplayName(), $contact->getCardUrl()),
      array(lang('update permissions'))
    ));
  } // if
  
  if ($contact->canEdit(logged_user())) {
    add_page_action(array(
      lang('update profile')  => $contact->getEditUrl(),
    ));
  } // if
  
  add_stylesheet_to_page('admin/user_permissions.css');

?>
<?php
  $quoted_permissions = array();
  foreach ($permissions as $permission_id => $permission_text) {
    $quoted_permissions[] = "'$permission_id'";
  } // foreach
?>
<script type="text/javascript" src="<?php echo get_javascript_url('modules/updateUserPermissions.js') ?>"></script>
<script type="text/javascript">
  App.modules.updateUserPermissions.project_permissions = new Array(<?php echo implode(', ', $quoted_permissions) ?>);
</script>

<?php if (isset($projects) && is_array($projects) && count($projects)) { ?>
<div id="userPermissions">
  <form action="<?php echo $user->getUpdatePermissionsUrl($redirect_to) ?>" method="post">
    <div id="userProjects">
<?php foreach ($projects as $project) { ?>
      <table class="blank">
        <tr>
          <td class="projectName">
            <?php echo checkbox_field('project_permissions_' . $project->getId(), $user->isProjectUser($project), array('id' => 'projectPermissions' . $project->getId(), 'onclick' => 'App.modules.updateUserPermissions.projectCheckboxClick(' . $project->getId() . ')')) ?> 
<?php if ($project->isCompleted()) { ?>
            <label for="projectPermissions<?php echo $project->getId() ?>" class="checkbox"><del class="help" title="<?php echo lang('project completed on by', format_date($project->getCompletedOn()), $project->getCompletedByDisplayName()) ?>"><?php echo clean($project->getName()) ?></del></label>
<?php } else { ?>
            <label for="projectPermissions<?php echo $project->getId() ?>" class="checkbox"><?php echo clean($project->getName()) ?></label>
<?php } // if ?>
          </td>
          <td class="permissionsList">
<?php if ($user->isProjectUser($project)) { ?>
            <div id="projectPermissionsBlock<?php echo $project->getId() ?>">
<?php } else { ?>
            <div id="projectPermissionsBlock<?php echo $project->getId() ?>" style="display: none">
<?php } // if ?>
              <div class="projectPermission">
                <?php echo checkbox_field('project_permissions_' . $project->getId() . '_all', $user->hasAllProjectPermissions($project), array('id' => 'projectPermissions' . $project->getId() . 'All', 'onclick' => 'App.modules.updateUserPermissions.projectAllCheckboxClick(' . $project->getId() . ')')) ?> <label for="projectPermissions<?php echo $project->getId() ?>All" class="checkbox"><?php echo lang('all') ?></label>
              </div>
<?php foreach ($permissions as $permission_name => $permission_text) { ?>
              <div class="projectPermission">
                <?php echo checkbox_field('project_permission_' . $project->getId() . '_' . $permission_name, $user->hasProjectPermission($project, $permission_name), array('id' => 'projectPermission' . $project->getId() . $permission_name, 'onclick' => 'App.modules.updateUserPermissions.projectPermissionCheckboxClick(' . $project->getId() . ')')) ?> <label for="projectPermission<?php echo $project->getId() . $permission_name ?>" class="checkbox normal"><?php echo clean($permission_text) ?></label>
              </div>
<?php } // foreach ?>
            </div>
          </td>
        </tr>
      </table>
<?php } // foreach ?>
    </div>
    <input type="hidden" name="submitted" value="submitted" />
    <?php echo submit_button(lang('update permissions')) ?>
  </form>
</div>
<?php } // if ?>
