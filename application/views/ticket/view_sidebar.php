<div class="sidebarBlock">
  <h2><?php echo lang('message subscribers') ?></h2>
  <div class="blockContent">
    <p><?php echo lang('subscribers ticket desc') ?></p>
<?php if(isset($subscribers) && is_array($subscribers) && count($subscribers)) { ?>
    <ul>
<?php foreach($subscribers as $user) { ?>
<?php if($user->getId() == logged_user()->getId()) { ?>
      <li><a href="<?php echo $user->getCardUrl() ?>"><?php echo clean($user->getDisplayName()) ?></a> (<a href="<?php echo $ticket->getUnsubscribeUrl() ?>" onclick="return confirm('<?php echo lang('confirm unsubscribe') ?>')"><?php echo lang('unsubscribe from message') ?></a>)</li>
<?php } else { ?>
      <li><a href="<?php echo $user->getCardUrl() ?>"><?php echo clean($user->getDisplayName()) ?></a></li>
<?php } // if ?>
<?php } // foreach ?>
    </ul>
<?php } else { ?>
    <p><?php echo lang('no ticket subscribers') ?></p>
<?php } // if ?>
    <form action="<?php echo $ticket->getSubscribeUrl(); ?>" method="POST">
<?php echo select_project_user('ticket[new_subscriber]', active_project(), logged_user()->getId(), $subscribers_ids); ?>
<?php echo submit_button(lang('subscribe to message'), null); ?>
    </form>
  </div>
</div>

<?php if($ticket->canUpdateOptions(logged_user())) { ?>
<div class="sidebarBlock">
  <h2><?php echo lang('options') ?></h2>
  <div class="blockContent">
    <form action="<?php echo $ticket->getUpdateOptionsUrl() ?>" method="post">
      <div class="formBlock">
        <div>
          <label><?php echo lang('private ticket') ?>:</label>
          <?php echo yes_no_widget('ticket[is_private]', 'ticketFormIsPrivate', $ticket->isPrivate(), lang('yes'), lang('no')) ?>
        </div>
      </div>
      <?php echo submit_button(lang('update ticket options'), null) ?>
    </form>
  </div>
  <div class="messageOptions">
<?php if($ticket->canDelete(logged_user())) { ?>
    <a href="<?php echo $ticket->getDeleteUrl() ?>"><?php echo lang('delete') ?></a>
<?php } // if ?>
  </div>
</div>
<?php } // if ?>
<br/>
<div class="sidebarBlock">
  <?php $this->includeTemplate(get_template_path('textile_help')); ?>
</div>