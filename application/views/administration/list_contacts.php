<?php add_stylesheet_to_page('admin/contact_list.css') ?>
<?php if (isset($contacts) && is_array($contacts) && count($contacts)) { ?>
<div id="contactsList">
<?php $counter = 0; ?>
<?php foreach ($contacts as $contact) { ?>
<?php $counter++; ?>
  <div class="listedUser <?php echo $counter % 2 ? 'even' : 'odd' ?>">
    <div class="contactAvatar"><img src="<?php echo $contact->getAvatarUrl() ?>" alt="<?php echo clean($contact->getDisplayName()) ?> <?php echo lang('avatar') ?>" /></div>
    <div class="contactDetails">
      <div class="contactName"><a href="<?php echo $contact->getCardUrl() ?>"><?php echo clean($contact->getDisplayName()) ?></a><?php if ($contact->getTitle() != '') echo " &mdash; ".clean($contact->getTitle()) ?></div>
<?php
  $options = array();
  if ($contact->canEdit(logged_user())) {
    $options[] = '<a href="' . $contact->getEditUrl() . '">' . lang('edit') . '</a>';
  }
  if ($contact->canUpdateProfile(logged_user())) {
    $options[] = '<a href="' . $contact->getEditProfileUrl($company->getViewUrl()) . '">' . lang('update profile') . '</a>';
    $options[] = '<a href="' . $contact->getUpdateAvatarUrl($company->getViewUrl()) . '">' . lang('update avatar') . '</a>';
  } // if
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
