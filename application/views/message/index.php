<?php

  set_page_title(lang('messages'));
  project_tabbed_navigation(PROJECT_TAB_MESSAGES);
  project_crumbs(lang('messages'));
  if (ProjectMessage::canAdd(logged_user(), active_project())) {
    add_page_action(lang('add message'), get_url('message', 'add'));
  } // if

?>
<?php if (isset($messages) && is_array($messages) && count($messages)) { ?>
<div id="viewToggle">
  <a href="<?php echo get_url('message', 'index', array('view'=>'list', 'page' => $messages_pagination->getCurrentPage())); ?>"><img src="<?php if ($view_type=="list") { echo get_image_url("icons/list_on.png"); } else { echo get_image_url("icons/list_off.png"); } ?>" title="<?php echo lang('list view'); ?>" alt="<?php echo lang('list view'); ?>"/></a>
  <a href="<?php echo get_url('message', 'index', array('view'=>'detail', 'page' => $messages_pagination->getCurrentPage())); ?>"><img src="<?php if ($view_type=="detail") { echo get_image_url("icons/excerpt_on.png"); } else { echo get_image_url("icons/excerpt_off.png"); } ?>" title="<?php echo lang('detail view'); ?>" alt="<?php echo lang('detail view'); ?>"/></a>
</div>
<div id="messages">
  <div id="messagesPaginationTop"><?php echo advanced_pagination($messages_pagination, get_url('message', 'index', array('page' => '#PAGE#'))) ?></div>
  <?php if ($view_type == 'list') { ?>
  <table id="short_messages">
    <tr class="message short header"><th></th><th><?php echo lang('date') ?></th><th><?php echo lang('title') ?></th><th><?php echo lang('author'); ?></th><th class="center"><img src="<?php echo get_image_url("icons/comments.png"); ?>" title="<?php echo lang('comments'); ?>" alt="<?php echo lang('comments'); ?>"/></th><th class="center"><img src="<?php echo get_image_url("icons/attach.png"); ?>" title="<?php echo lang('attachments'); ?>" alt="<?php echo lang('attachments'); ?>"/></th></tr>
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
  <?php } else { ?>
    <?php foreach ($messages as $message) { ?>
    <?php 
      $this->assign('message', $message);
      $this->assign('on_message_page', false);
      $this->includeTemplate(get_template_path('view_message', 'message'));
    ?>
    <?php } // foreach ?>  
  <?php } // if ?>
  <div id="messagesPaginationBottom"><?php echo advanced_pagination($messages_pagination, get_url('message', 'index', array('page' => '#PAGE#'))) ?></div>
</div>
<?php } else { ?>
<p><?php echo lang('no messages in project') ?></p>
<?php } // if ?>
