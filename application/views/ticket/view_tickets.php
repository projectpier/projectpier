<?php if (!isset($params) || !is_array($params)) {
  $params = array();
} // if ?>
  <table width="100%" cellpadding="2" border="0">
  <tr bgcolor="#f4f4f4">
    <th width="40">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'id' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'id', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("ticket") ?></a>
    </th>
    <th>
      <a href="<?php
        if (array_var($params, 'sort_by') == 'summary' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'summary', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'summary', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("summary") ?></a>
    </th>
    <th width="95">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'type' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'type', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'type', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("type") ?></a>
    </th>
    <th width="115">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'category_id' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'category_id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'category_id', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("category") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'assigned_to_user_id' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'assigned_to_user_id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'assigned_to_user_id', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("assigned to") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'status' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'status', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'status', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("status") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'priority' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'priority', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'priority', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("priority") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'due_date' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'due_date', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'due_date', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("due date") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if (array_var($params, 'sort_by') == 'updated_on' && array_var($params, 'order') == 'ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'updated_on', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'updated_on', 'order' => 'ASC')));
        }
          ?>"><?php echo ucfirst(lang('updated on')) ?></a>
    </th>
  </tr>
<?php foreach ($tickets as $ticket) { ?>
  <tr class="<?php echo $ticket->getPriority(); ?>">
    <td><a href="<?php echo $ticket->getViewUrl() ?>" title="<?php echo $ticket->getDescription() ?>"><?php echo $ticket->getId() ?></a></td>
    <td><a href="<?php echo $ticket->getViewUrl() ?>" title="<?php echo $ticket->getDescription() ?>"><?php echo $ticket->getSummary() ?></a></td>
    <td><?php echo lang($ticket->getType()) ?></td>
    <td>
<?php if ($ticket->getCategory()) { ?>
          <?php echo clean($ticket->getCategory()->getName()) ?>
<?php } // if{ ?>
    </td>
    <td>
<?php if ($ticket->getAssignedTo()) { ?>
          <?php echo "<a href=\"".$ticket->getAssignedTo()->getCardUrl()."\">".clean($ticket->getAssignedTo()->getObjectName())."</a>" ?>
<?php } // if{ ?>
    </td>
    <td><?php echo $ticket->getStatus(); ?></td>
    <td><?php echo $ticket->getPriority(); ?></td>
    <td><?php echo $ticket->hasDueDate() ? $ticket->getDueDate()->format("m/d/Y") : ''; ?></td>
    <td><?php echo $ticket->getUpdatedOn() ? $ticket->getUpdatedOn()->format("m/d/Y") : lang('n/a') ?></td>
  </tr>
<?php } // foreach ?>
  </table>