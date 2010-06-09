<?php

  set_page_title(lang('members'));
  administration_tabbed_navigation(ADMINISTRATION_TAB_MEMBERS);
  administration_crumbs(lang('members'));
  if (Contact::canAdd(logged_user(), owner_company())) {
    add_page_action(array(
      lang('add contact') => owner_company()->getAddContactUrl()
    ));
  } // if

?>
<?php $this->includeTemplate(get_template_path('list_contacts', 'administration')) ?>
