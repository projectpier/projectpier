<?php 

  // Set page title and set crumbs to index
  set_page_title(lang('company card of', $company->getName()));
  dashboard_tabbed_navigation(DASHBOARD_TAB_CONTACTS);
  dashboard_crumbs(array(
    array(lang('contacts'), get_url('dashboard', 'contacts')),
    array($company->getName())));
  if ($company->canEdit(logged_user())) {
    add_page_action(lang('edit company'), $company->getEditUrl());
    add_page_action(lang('edit company logo'), $company->getEditLogoUrl());
    if (!$company->isOwner()) {
      add_page_action(lang('update permissions'), $company->getUpdatePermissionsUrl());
    } // if
  } // if
  if (Contact::canAdd(logged_user(), $company)) {
    add_page_action(lang('add contact'), $company->getAddContactUrl());
  } // if

?>
<?php $this->includeTemplate(get_template_path('company_card', 'company')) ?>
