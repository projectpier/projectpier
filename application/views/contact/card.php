<?php 

  // Set page title and set crumbs to index
  set_page_title($contact->getDisplayName());
  dashboard_tabbed_navigation(DASHBOARD_TAB_CONTACTS);
  dashboard_crumbs(array(
    array(lang('contacts'), get_url('dashboard', 'contacts')),
    array($contact->getCompany()->getName(), $contact->getCompany()->getCardUrl()),
    array($contact->getDisplayName())));
  if ($contact->canUpdateProfile(logged_user())) {
    add_page_action(array(
      lang('update profile')  => $contact->getEditProfileUrl(),
      lang('change password') => $contact->getEditPasswordUrl(),
      lang('update avatar')   => $contact->getUpdateAvatarUrl()
    ));
  } // if
  
?>
<?php 
  $this->includeTemplate(get_template_path('contact_card', 'contact')) 
?>
