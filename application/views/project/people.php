<?php

  set_page_title(lang('people'));
  project_tabbed_navigation(PROJECT_TAB_PEOPLE);
  project_crumbs(lang('people'));
  
  if (active_project()->canChangePermissions(logged_user())) {
    add_page_action(lang('add contact'), get_url('project', 'add_contact'));
  } // if
  
  add_stylesheet_to_page('project/people.css');
  add_stylesheet_to_page('project/attachments.css');

?>
<?php if (isset($page_attachments) && is_array($page_attachments) && count($page_attachments)) { ?>
<div id="pageAttachments">
<?php foreach ($page_attachments as $page_attachment) {
  tpl_assign('attachment', $page_attachment);
  if ($page_attachment->getRelObjectManager() != '' && $page_attachment->getObject()) {
    if (file_exists(get_template_path('view_'.$page_attachment->getRelObjectManager(), 'page_attachment'))) {
      $this->includeTemplate(get_template_path('view_'.$page_attachment->getRelObjectManager(), 'page_attachment'));
    } else {
      $this->includeTemplate(get_template_path('view_DefaultObject', 'page_attachment'));
    }
  } else {
    $this->includeTemplate(get_template_path('view_EmptyAttachment', 'page_attachment'));
  }
  if (active_project()->canChangePermissions(logged_user()) && $page_attachment->getPageName() == 'people') { ?>
    <div class="attachmentActions">
      <a href="<?php echo $project->getRemoveContactUrl($page_attachment->getRelObjectId()); ?>"><?php echo lang('remove contact'); ?></a>
    </div>
<?php } // if ?>
<?php } // foreach ?>
</div>
<?php } // if ?>