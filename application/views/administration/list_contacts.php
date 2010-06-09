<?php add_stylesheet_to_page('admin/contact_list.css') ?>
<?php if (isset($contacts) && is_array($contacts) && count($contacts)) { ?>
<div id="contactsList">
<?php $counter = 0; ?>
<?php foreach ($contacts as $contact) { ?>
<?php
$counter++;
$user = $contact->getUserAccount();
?>
  <div class="listedUser <?php echo $counter % 2 ? 'even' : 'odd' ?>">
    <div class="contactAvatar"><img src="<?php echo $contact->getAvatarUrl() ?>" alt="<?php echo clean($contact->getDisplayName()) ?> <?php echo lang('avatar') ?>" /></div>
    <div class="contactDetails">
      <div class="contactName"><a href="<?php echo $contact->getCardUrl() ?>"><?php echo clean($contact->getDisplayName()) ?></a><?php if ($contact->getTitle() != '') echo " &mdash; ".clean($contact->getTitle()) ?></div>
<?php if ($company->isOwner() && $contact->hasUserAccount()) { ?>
      <div class="userIsAdmin"><span><?php echo lang('administrator') ?>:</span> <?php echo $user->isAdministrator() ? lang('yes') : lang('no') ?></div>
      <div class="userAutoAssign"><span><?php echo lang('auto assign') ?>:</span> <?php echo $user->getAutoAssign() ? lang('yes') : lang('no') ?></div>
<?php } // if  ?>
<?php
  $options = array();
  if ($contact->canEdit(logged_user())) {
    $options[] = '<a href="' . $contact->getEditUrl() . '">' . lang('edit') . '</a>';
  }
  if ($contact->canDelete(logged_user())) {
    $options[] = '<a href="' . $contact->getDeleteUrl() . '">' . lang('delete') . '</a>';
  } // if
  if ($contact->hasUserAccount()) {
    if ($contact->canEditUserAccount(logged_user())) {
      $options[] = '<a href="' . $contact->getEditUserAccountUrl() . '">' . lang('edit user account') . '</a>';
    }
    if ($contact->canDeleteUserAccount(logged_user())) {
      $options[] = '<a href="' . $contact->getDeleteUserAccountUrl() . '">' . lang('delete user account') . '</a>';
    }
    if ($user->canUpdatePermissions(logged_user())) {
      $options[] = '<a href="' . $user->getUpdatePermissionsUrl() . '">' . lang('update permissions') . '</a>';
    }
  } else {
    if ($contact->canAddUserAccount(logged_user())) {
      $options[] = '<a href="' . $contact->getAddUserAccountUrl() . '">' . lang('add user account') . '</a>';
    }
  }
?>
      <div class="contactOptions"><?php echo implode(' | ', $options) ?></div>
      <div class="clear"></div>
    </div>
  </div>  
<?php } // foreach ?>
</div>

<?php } else { ?>
<p><?php echo lang('no contacts in company') ?></p>
<?php } // if ?>
