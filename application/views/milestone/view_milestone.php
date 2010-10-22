<?php
  add_stylesheet_to_page('project/milestones.css');
?>
<?php if ($milestone->isCompleted()) { ?>
<div class="milestone success">
<?php } elseif ($milestone->isToday()) { ?>
<div class="milestone important">
<?php } elseif ($milestone->isLate()) { ?>
<div class="milestone important">
<?php } else { ?>
<div class="milestone hint">
<?php } // if?>

<?php if ($milestone->isPrivate()) { ?>
    <div class="private" title="<?php echo lang('private milestone') ?>"><span><?php echo lang('private milestone') ?></span></div>
<?php } // if ?>

    <div class="header">
<?php if ($milestone->canChangeStatus(logged_user())) { ?>
<?php if ($milestone->isCompleted()) { ?>
<?php echo checkbox_link($milestone->getOpenUrl(), true) ?>
<?php } else { ?>
<?php echo checkbox_link($milestone->getCompleteUrl(), false) ?>
<?php } // if ?>
<?php } // if?>

<?php if ($milestone->getAssignedTo() instanceof ApplicationDataObject) { ?>
        <span class="assignedTo"><?php echo clean($milestone->getAssignedTo()->getObjectName()) ?>:</span>
<?php } // if ?>
      <a href="<?php echo $milestone->getViewUrl() ?>"><?php echo clean($milestone->getName()) ?></a>
<?php if ($milestone->isUpcoming()) { ?>
 (<?php echo format_days('days left', $milestone->getLeftInDays()) ?>)
<?php } elseif ($milestone->isLate()) { ?>
 (<?php echo format_days('days late', $milestone->getLateInDays()) ?>)
<?php } elseif ($milestone->isToday()) { ?>
 (<?php echo lang('today') ?>)
<?php } // if ?>
    </div>
    <div class="content">
<?php if ($milestone->getDueDate()->getYear() > DateTimeValueLib::now()->getYear()) { ?>
      <div class="dueDate"><span><?php echo lang('due date') ?>:</span> <?php echo format_date($milestone->getDueDate(), null, 0) ?></div>
<?php } else { ?>
      <div class="dueDate"><span><?php echo lang('due date') ?>:</span> <?php echo format_descriptive_date($milestone->getDueDate(), 0) ?></div>
<?php } // if ?>
      
<?php if ($milestone->getDescription()) { ?>
      <div class="description"><?php echo plugin_manager()->apply_filters('milestone_description', do_textile($milestone->getDescription())) ?></div>
<?php } // if ?>

<!-- Milestones -->
<?php if (!$milestone->hasMessages() && !$milestone->hasTaskLists() && !$milestone->hasTickets()) { ?>
      <p><?php echo lang('empty milestone', $milestone->getAddMessageUrl(), $milestone->getAddTaskListUrl(), $milestone->getAddTicketUrl()) ?></p>
<?php } else { ?>
<?php if ($milestone->hasMessages()) { ?>
      <p><?php echo lang('messages') ?>:</p>
      <ul>
<?php foreach ($milestone->getMessages() as $message) { ?>
        <li><a href="<?php echo $message->getViewUrl() ?>"><?php echo clean($message->getTitle()) ?></a>
<?php if ($message->getCreatedBy() instanceof User) { ?>
        <span class="desc">(<?php echo lang('posted on by', format_date($message->getUpdatedOn()), $message->getCreatedByCardUrl(), clean($message->getCreatedByDisplayName())) ?>)</span>
<?php } // if ?>
<?php } // foreach ?>
      </ul>
<?php } // if?>

<!-- Task lists -->
<?php if ($milestone->hasTaskLists()) { ?>
      <p><?php echo lang('task lists') ?>:</p>
      <ul>
<?php foreach ($milestone->getTaskLists() as $task_list) { ?>
<?php if ($task_list->isCompleted()) { ?>
        <li><del datetime="<?php echo $task_list->getCompletedOn()->toISO8601() ?>"><a href="<?php echo $task_list->getViewUrl() ?>" title="<?php echo lang('completed task list') ?>"><?php echo clean($task_list->getName()) ?></a></del></li>
<?php } else { ?>
        <li><a href="<?php echo $task_list->getViewUrl() ?>"><?php echo clean($task_list->getName()) ?></a></li>
<?php } // if ?>
<?php } // foreach ?>
      </ul>
<?php } // if ?>

<!-- Tickets -->
<?php if ($milestone->hasTickets()) { ?>
  <div class="milestone-progress-wrapper">
      <div class="progress clearfix">
        <div style="width:<?php print $milestone->getPercentageByTicketStatus('closed'); ?>%;" class="resolved"><img height="14" width="1" src="<?php print image_url('clear.gif'); ?>" alt=""></div>
          <div style="width: <?php print $milestone->getPercentageByTicketStatus('pending'); ?>%;" class="in-progress"><img height="14" width="1" src="<?php print image_url('clear.gif'); ?>" alt=""></div>
          <div style="width: <?php print floor((($milestone->hasTicketsByStatus('new') + $milestone->hasTicketsByStatus('open')) / $milestone->getTotalTicketCount()) * 100) ?>%;" class="open"><img height="14" width="1" src="<?php print image_url('clear.gif'); ?>" alt=""></div>
      </div>
    <div class="ticket-details">
      <?php print $milestone->getPercentageByTicketStatus('closed'); ?>% completed -
      Tickets: <a href="<?php print get_url('ticket', 'index', array('active_project' => $milestone->getProjectId(), 'order' => 'ASC', 'status' => 'closed')); ?>">Closed (<?php print $milestone->hasTicketsByStatus('closed') ?>)</a>,
      <a href="<?php print get_url('ticket', 'index', array('active_project' => $milestone->getProjectId(), 'order' => 'ASC', 'status' => 'pending')); ?>">Pending (<?php print $milestone->hasTicketsByStatus('pending') ?>)</a>,
      <a href="<?php print get_url('ticket', 'index', array('active_project' => $milestone->getProjectId(), 'order' => 'ASC', 'status' => 'new,open')); ?>">New/Open (<?php print $milestone->hasTicketsByStatus('open') + $milestone->hasTicketsByStatus('new'); ?>)</a>
    </div>
  </div>
      <p><?php echo lang('tickets') ?>:</p>
      <ul class="milestone-tickets">
<?php foreach ($milestone->getTickets() as $ticket) { ?>
        <li><a href="<?php echo $ticket->getViewUrl() ?>"><?php echo clean($ticket->getTitle()) ?></a>
        <span class="ticket-meta-details">(
          <?php if ($ticket->getStatus()) { ?>
          <span class="ticket-status">Status: <?php echo $ticket->getStatus(); ?></span>
          <?php } ?>

          <?php if ($ticket->getAssignedToUserId() > 0) { ?>
          | <span class="ticket-assigned-to">Assigned To: <?php echo clean($ticket->getAssignedToUser()->getContact()->getDisplayName());  ?></span>
          <?php } ?>

          <?php if ($ticket->getDueDate()) { ?>
          | <span class="ticket-due-date">Due: <?php echo format_date($ticket->getDueDate())  ?></span>
          <?php } ?>)
        </span>

<?php } // foreach ?>
      </ul>
<?php } // if?>

<?php } // if ?>

  <p><span><?php echo lang('tags') ?>:</span> <?php echo project_object_tags($milestone, $milestone->getProject()) ?></p>

<?php
  $options = array();
  if ($milestone->canEdit(logged_user())) {
    $options[] = '<a href="' . $milestone->getEditUrl() . '">' . lang('edit') . '</a>';
  }
  if ($milestone->canDelete(logged_user())) {
    $options[] = '<a href="' . $milestone->getDeleteUrl() . '">' . lang('delete') . '</a>';
  }
?>
<?php if (count($options)) { ?>
      <div class="milestoneOptions"><?php echo implode(' | ', $options) ?></div>
<?php } // if ?>
    </div>
    
</div>
