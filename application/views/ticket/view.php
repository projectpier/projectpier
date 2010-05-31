<?php 
  $canEdit = $ticket->canEdit(logged_user());

  // Set page title and set crumbs to index
  set_page_title($ticket->getTitle());
  project_tabbed_navigation(PROJECT_TAB_TICKETS);
  $crumbs = array(array(lang('tickets'), get_url('ticket')));
  $crumbs[] = array($ticket->getTitle());
  project_crumbs($crumbs);
  
  if ($ticket->canEdit(logged_user(), active_project())) {
    add_page_action(lang('edit ticket'), $ticket->getEditUrl());
  }
  if ($ticket->canAdd(logged_user(), active_project())) {
    add_page_action(lang('add ticket'), $ticket->getAddUrl());
  }
  if (logged_user()->isAdministrator()) {
    add_page_action(lang('delete ticket'), $ticket->getDeleteUrl());
  }
  add_stylesheet_to_page('project/tickets.css');
?>
<?php if ($ticket->isPrivate()) { ?>
    <div class="private" title="<?php echo lang('private ticket') ?>"><span><?php echo lang('private ticket') ?></span></div>
<?php } // if ?>
<h2><?php echo lang('ticket #', $ticket->getId()).": ".$ticket->getTitle(); ?></h2>
<h3 class="status"><?php echo lang('status') ?>: <strong><?php echo lang($ticket->getStatus()); ?></strong></h3>
<div id="ticket">
  <div id="ticketSummary">
    <h2><?php echo lang('summary') ?>:</h2>
    <div class="title"><?php echo do_textile($ticket->getSummary()) ?></div>
  </div>
  <table class="properties">
    <tr>
      <th><span class="bold"><?php echo lang('reported by'); ?>:</span></th>
      <td><a href="<?php echo $ticket->getCreatedBy()->getCardUrl(); ?>"><?php echo $ticket->getCreatedByDisplayName(); ?></a></td>
      <th><span class="bold"><?php echo lang('edit by'); ?>:</span></th>
      <td>
<?php if ($ticket->getUpdated()) { ?>
        <?php echo lang('updated on by', format_datetime($ticket->getUpdatedOn()), $ticket->getUpdatedBy()->getCardUrl(), $ticket->getUpdatedByDisplayName(), lang($ticket->getUpdated())); ?>
<?php } else { ?>
        <?php echo lang('n/a') ?>
<?php } // if?>
      </td>
    </tr>
    <tr>
      <th><?php echo lang('assigned to') ?></th>
      <td>
<?php if ($ticket->getAssignedTo()) { ?>
          <a href="<?php echo $ticket->getAssignedTo()->getCardUrl() ?>"><?php echo clean($ticket->getAssignedTo()->getObjectName()) ?></a>
<?php } else { ?>
          <?php echo lang('none') ?>
<?php } // if{ ?>
      </td>
      <th><?php echo lang('priority') ?></th>
      <td><?php echo lang($ticket->getPriority()); ?></td>
    </tr>
    <tr>
      <th><?php echo lang('type') ?></th>
      <td><?php echo lang($ticket->getType()); ?></td>
      <th><?php echo lang('category') ?></th>
      <td>
<?php if ($ticket->getCategory()) { ?>
        <?php echo clean($ticket->getCategory()->getName()) ?>
<?php } else { ?>
          <?php echo lang('none') ?>
<?php } // if ?>
      </td>
    </tr>
    <tr>
      <th><?php echo lang('milestone') ?></th>
      <td>
<?php if ($ticket->getMilestoneId()) { ?>
        <a href="<?php echo ProjectMilestones::findById($ticket->getMilestoneId())->getViewUrl() ?>"><?php echo clean(ProjectMilestones::findById($ticket->getMilestoneId())->getName()) ?></a>
<?php } else { ?>
          <?php echo lang('none') ?>
<?php } // if ?>
      </td>
    </tr>
  </table>
  <div>
    <span class="bold"><?php echo lang('description') ?>:</span>
    <div class="desc"><?php echo do_textile($ticket->getDescription()); ?></div>
  </div>
</div>
<div>
  <?php echo render_object_files($ticket, $ticket->canEdit(logged_user())) ?>
</div>
    <tr>
      <th><?php echo label_tag(lang('assigned to'), 'ticketFormAssignedTo') ?></th>
<?php if ($canEdit) { ?>
      <td><?php echo assign_to_select_box("ticket[assigned_to]", active_project(), array_var($ticket_data, 'assigned_to'), array('id' => 'ticketFormAssignedTo')) ?></td>
<?php } else { ?>
      <td>
<?php if ($ticket->getAssignedTo()) { ?>
          <?php echo clean($ticket->getAssignedTo()->getObjectName()) ?>
<?php } // if{ ?>
      </td>
<?php } // if?>

      <th><?php echo label_tag(lang('priority'), 'ticketFormPriority') ?></th>
<?php if ($canEdit) { ?>
      <td><?php echo select_ticket_priority("ticket[priority]", array_var($ticket_data, 'priority'), array('id' => 'ticketFormPriority')) ?></td>
<?php } else { ?>
      <td><?php echo lang($ticket->getPriority()); ?></td>
<?php } // if?>
    </tr>
    
    <tr>
      <th><?php echo label_tag(lang('type'), 'ticketFormType') ?></th>
<?php if ($canEdit) { ?>
      <td><?php echo select_ticket_type("ticket[type]", array_var($ticket_data, 'type'), array('id' => 'ticketFormType')) ?></td>
<?php } else { ?>
      <td><?php echo lang($ticket->getType()); ?></td>
<?php } // if?>

      <th><?php echo label_tag(lang('category'), 'ticketFormCategory') ?></th>
<?php if ($canEdit) { ?>
      <td><?php echo select_ticket_category("ticket[category_id]", $ticket->getProject(), array_var($ticket_data, 'category_id'), array('id' => 'ticketFormCategory')) ?></td>
<?php } else { ?>
    <td>
<?php if ($ticket->getCategory()) { ?>
          <?php echo clean($ticket->getCategory()->getName()) ?>
<?php } // if{ ?>
    </td>
<?php } // if?>
    </tr>

<h2><?php echo lang('history') ?></h2>
<?php if (isset($changes) && is_array($changes) && count($changes)) { ?>
<div id="changelog">
  <table>
    <tr>
      <th><?php echo lang('field') ?></th>
      <th><?php echo lang('old value') ?></th>
      <th><?php echo lang('new value') ?></th>
      <th><?php echo lang('user') ?></th>
      <th><?php echo lang('change date') ?></th>
    </tr>
<?php foreach($changes as $change) { ?>
    <tr>
      <td><?php echo lang($change->getType()) ?></td>
<?php if ($change->dataNeedsTranslation()) { ?>
      <td><?php echo lang($change->getFromData()) ?></td>
      <td><?php echo lang($change->getToData()) ?></td>
<?php } else { ?>
      <td><?php echo $change->getFromData() ?></td>
      <td><?php echo $change->getToData() ?></td>
<?php } // if ?>
      <td><?php echo $change->getCreatedByDisplayName() ?></td>
      <td><?php echo format_datetime($change->getCreatedOn()) ?></td>
    </tr>
<?php } // foreach ?>
  </table>
</div>
<?php } else { ?>
<p><?php echo lang('no changes in ticket') ?></p>
<?php } // if ?>