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
  add_stylesheet_to_page('project/tickets.css');
?>
<?php if ($ticket->isPrivate()) { ?>
    <div class="private" title="<?php echo lang('private ticket') ?>"><span><?php echo lang('private ticket') ?></span></div>
<?php } // if ?>
<h2><?php echo lang('ticket #', $ticket->getId()).": ".$ticket->getTitle(); ?></h2>
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
      <th><?php echo lang('status') ?></th>
      <td><?php echo lang($ticket->getStatus()); ?></td>
      
      <th><?php echo lang('priority') ?></th>
      <td><?php echo lang($ticket->getPriority()); ?></td>
    </tr>
    <tr>
      <th><?php echo lang('due date') ?></th>
      <td><?php echo $ticket->hasDueDate() ? $ticket->getDueDate()->format("m/d/Y") : lang('none'); ?></td>
      <th><?php echo lang('milestone') ?></th>
      <td>
<?php if ($ticket->getMilestoneId()) {
  $milestone = ProjectMilestones::findById($ticket->getMilestoneId());
  ?>
        <a href="<?php echo $milestone->getViewUrl() ?>"><?php echo clean($milestone->getName()) ?></a> (<?php echo format_datetime($milestone->getDueDate()) ?>)
<?php } else {
  echo lang('none');
} ?>
      </td>
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
      <th><?php echo lang('assigned to') ?></th>
      <td>
<?php if ($ticket->getAssignedTo()) { ?>
          <a href="<?php echo $ticket->getAssignedTo()->getCardUrl() ?>"><?php echo clean($ticket->getAssignedTo()->getObjectName()) ?></a>
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
  <div class="ticketTags">
    <span class="bold"><?php echo lang('tags') ?>:</span>
    <?php echo project_object_tags($ticket, $ticket->getProject()) ?>
  </div>
</div>
<div>
  <?php echo render_object_files($ticket, $ticket->canEdit(logged_user())) ?>
</div>

<?php if ($ticket->canChangeStatus(logged_user())) { ?>
<fieldset>
  <form action="<?php echo $ticket->getSaveChangeUrl(); ?>" method="post" enctype="multipart/form-data">
  <div class="comment">
    <?php echo label_tag(lang('comment'), 'changesetFormComment', false) ?>
    <?php echo textarea_field('ticket[comment]', null, array('id' => 'ticketFormComment')) ?>
  </div>
  <table width="100%">
    <tr>
      <th><?php echo label_tag(lang('status'), 'ticketFormStatus') ?></th>
      <td><?php echo select_ticket_status("ticket[status]", array_var($ticket_data, 'status'), array('id' => 'ticketFormStatus')) ?></td>
      <th><?php echo label_tag(lang('priority'), 'ticketFormPriority') ?></th>
      <td><?php echo select_ticket_priority("ticket[priority]", array_var($ticket_data, 'priority'), array('id' => 'ticketFormPriority')) ?></td>
    </tr>
    <tr>
      <th><?php echo label_tag(lang('type'), 'ticketFormType') ?></th>
      <td><?php echo select_ticket_type("ticket[type]", array_var($ticket_data, 'type'), array('id' => 'ticketFormType')) ?></td>
      <th><?php echo label_tag(lang('category'), 'ticketFormCategory') ?></th>
      <td><?php echo select_ticket_category("ticket[category_id]", $ticket->getProject(), array_var($ticket_data, 'category_id'), array('id' => 'ticketFormCategory')) ?></td>
    </tr>
    <tr>
      <th><?php echo label_tag(lang('assigned to'), 'ticketFormAssignedTo') ?></th>
      <td><?php echo assign_to_select_box("ticket[assigned_to]", active_project(), array_var($ticket_data, 'assigned_to'), array('id' => 'ticketFormAssignedTo')) ?></td>
      <th><?php echo label_tag(lang('milestone'), 'ticketFormMilestone') ?></th>
      <td><?php echo select_milestone('ticket[milestone_id]', active_project(), array_var($ticket_data, 'milestone_id'), array('id' => 'ticketFormMilestone')) ?></td>
    </tr>
  </table>
  <?php echo submit_button(lang('update')) ?>
  </form>
</fieldset>
<?php } ?>
<h2><?php echo lang('history') ?> <a href="<?php echo get_url('ticket', 'view', array('id' => $ticket->getId(), 'active_project' => $ticket->getProjectId(), 'order' => (strtoupper(trim($params['order'])) == 'ASC' ? 'DESC':'ASC')), "changelog"); ?>"><img src="<?php echo get_image_url('icons/more_'.(strtoupper(trim($params['order'])) == 'ASC' ? 'down':'up').'.gif'); ?>"/></a></h2>
<?php if (isset($changesets) && is_array($changesets) && count($changesets)) { ?>
<div id="changelog">
  <table width="100%">
<?php $counter = 0; ?>
<?php foreach ($changesets as $changeset) { ?>
  <tr class="<?php echo $counter%2 ? 'odd':'even'; $counter++ ?>">
    <td><?php echo format_datetime($changeset->getCreatedOn()) ?>
    <td><a href="<?php echo $changeset->getCreatedBy()->getCardUrl(); ?>"><?php echo $changeset->getCreatedByDisplayName(); ?></a></td>
    <td>
<?php $changes = $changeset->getChanges(); ?>
      <ul>
<?php foreach ($changes as $change) { ?>
        <li><?php
        if (trim($change->getFromData()) == "") {
          if ($change->dataNeedsTranslation()) {
            echo lang('change set to', lang($change->getType()), lang($change->getToData()));
          } else {
            echo lang('change set to', lang($change->getType()), $change->getToData());
          } // if
        } elseif (trim($change->getToData()) == "") {
          if ($change->dataNeedsTranslation()) {
            echo lang('change from to', lang($change->getType()), lang($change->getFromData()), lang('n/a'));
          } else {
            echo lang('change from to', lang($change->getType()), $change->getFromData(), lang('n/a'));
          } // if
        } else {
          if ($change->dataNeedsTranslation()) {
            echo lang('change from to', lang($change->getType()), lang($change->getFromData()), lang($change->getToData()));
          } else {
            echo lang('change from to', lang($change->getType()), $change->getFromData(), $change->getToData());
          } // if
        } // if ?>
        </li>
<?php
  } // foreach
?>
      </ul>
<?php if (count($changes) && $changeset->getComment() != "") { ?>
      <hr/>
<?php } ?>
      <p><?php echo do_textile($changeset->getComment()) ?></p>
    </td>
<?php } // foreach ?>
  </table>
</div>
<?php } else { ?>
<p><?php echo lang('no changes in ticket') ?></p>
<?php } // if ?>