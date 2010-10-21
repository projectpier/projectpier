<?php

  set_page_title(lang('messages'));
  project_tabbed_navigation(PROJECT_TAB_MESSAGES);
  project_crumbs(lang('messages'));
  if (ProjectMessage::canAdd(logged_user(), active_project())) {
    add_page_action(lang('add message'), get_url('message', 'add'));
  } // if

?>
<?php if (isset($messages) && is_array($messages) && count($messages)) { ?>
<div id="messages">
  <div id="messagesPaginationTop"><?php echo advanced_pagination($messages_pagination, get_url('message', 'index', array('page' => '#PAGE#'))) ?></div>
  <table width="100%">
    <tr><th></th><th>Date</th><th>Title</th><th>Author</th><th>Comments</th><th>Attachments</th></tr>
<?php $odd_or_even = "even"; ?>
<?php foreach ($messages as $message) { ?>
<?php 
  $this->assign('message', $message);
  $this->assign('on_message_page', false);
  $this->assign('odd_or_even', $odd_or_even);
  $this->includeTemplate(get_template_path('view_message_short', 'message'));
  $odd_or_even = ($odd_or_even == "odd" ? "even" : "odd");
?>
<?php } // foreach ?>
  </table>
  <div id="messagesPaginationBottom"><?php echo advanced_pagination($messages_pagination, get_url('message', 'index', array('page' => '#PAGE#'))) ?></div>
</div>
<?php } else { ?>
<p><?php echo lang('no messages in project') ?></p>
<?php } // if ?>
