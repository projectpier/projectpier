<?php

  set_page_title(lang('delete ticket'));
  project_tabbed_navigation(PROJECT_TAB_TICKETS);
  project_crumbs(array(
    array(lang('tickets'), get_url('ticket')),
    array($ticket->getSummary(), $ticket->getViewUrl()),
    lang('delete ticket')));

?>
<form action="<?php echo $ticket->getDeleteUrl() ?>" method="post">
  <?php tpl_display(get_template_path('form_errors')) ?>

  <div><?php echo lang('about to delete') ?> <?php echo strtolower(lang('ticket')) ?> <b><?php echo clean($ticket->getTitle()) ?></b></div>

  <div>
    <label><?php echo lang('confirm delete ticket') ?></label>
    <?php echo yes_no_widget('deleteTicket[really]', 'deleteTicketReallyDelete', false, lang('yes'), lang('no')) ?>
  </div>

  <div>
    <?php echo label_tag(lang('password')) ?>
    <?php echo password_field('deleteTicket[password]', null, array('id' => 'loginPassword', 'class' => 'medium')) ?>
  </div>

  <?php echo submit_button(lang('delete ticket')) ?> <a href="<?php echo get_url('ticket') ?>"><?php echo lang('cancel') ?></a>
</form>
