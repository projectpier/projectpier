  <table width="100%" cellpadding="2" border="0">
  <tr bgcolor="#f4f4f4">
    <th width="40">
      <a href="<?php
        if ($params['sort_by']=='id' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'id', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("ticket") ?></a>
    </th>
    <th>
      <a href="<?php
        if ($params['sort_by']=='summary' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'summary', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'summary', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("summary") ?></a>
    </th>
    <th width="95">
      <a href="<?php
        if ($params['sort_by']=='type' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'type', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'type', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("type") ?></a>
    </th>
    <th width="115">
      <a href="<?php
        if ($params['sort_by']=='category_id' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'category_id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'category_id', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("category") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if ($params['sort_by']=='created_by_id' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'created_by_id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'created_by_id', 'order' => 'ASC')));
        }
          ?>"><?php echo ucfirst(lang("created by")) ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if ($params['sort_by']=='assigned_to_user_id' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'assigned_to_user_id', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'assigned_to_user_id', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("assigned to") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if ($params['sort_by']=='status' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'status', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'status', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("status") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if ($params['sort_by']=='priority' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'priority', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'priority', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("priority") ?></a>
    </th>
    <th width="60" align="center">
      <a href="<?php
        if ($params['sort_by']=='due_date' && $params['order']=='ASC') {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'due_date', 'order' =>'DESC')));
        } else {
          echo get_url('ticket', 'index', array_merge($params, array('sort_by' => 'due_date', 'order' => 'ASC')));
        }
          ?>"><?php echo lang("due date") ?></a>
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
    <td><a href="<?php echo $ticket->getCreatedBy()->getCardUrl(); ?>"><?php echo $ticket->getCreatedBy()->getDisplayName() ?></a></td>
    <td>
<?php if ($ticket->getAssignedTo()) { ?>
          <?php echo "<a href=\"".$ticket->getAssignedTo()->getCardUrl()."\">".clean($ticket->getAssignedTo()->getObjectName())."</a>" ?>
<?php } // if{ ?>
    </td>
    <td><?php echo $ticket->getStatus(); ?></td>
    <td><?php echo $ticket->getPriority(); ?></td>
    <td><?php echo $ticket->hasDueDate() ? $ticket->getDueDate()->format("m/d/Y") : ''; ?></td>
  </tr>
<?php } // foreach ?>
  </table>