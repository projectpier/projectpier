<h4><a href="#" onclick="var s=document.getElementById('ticketsFiltersContent'); s.style.display = (s.style.display=='none'?'block':'none');"<?php echo lang('filters') ?></a></h4>
<div id="ticketsFiltersContent">
  <div id="statusFilters">
    <strong><?php echo lang('status'); ?>:</strong>
    <?php
    $statuses = get_ticket_statuses();
    echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('status'=> ''))).'">'.lang('all').'</a> ';
    
    foreach ($statuses as $status) {
      echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('status'=> $status))).'">'.lang($status).'</a> ';
      if (in_array($status, explode(",", $params['status']))) {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('status' => str_replace($status, '',$params['status'])))).'">-</a> ';
      } else {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('status' => $params['status'].','.$status))).'">+</a> ';
      }
    }
    ?>
  </div>
  <div id="priorityFilters">
    <strong><?php echo lang('priority'); ?>:</strong>
    <?php
    $priorities = get_ticket_priorities();
    echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('priority'=> ''))).'">'.lang('all').'</a> ';
    foreach ($priorities as $priority) {
      echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('priority'=> $priority))).'">'.lang($priority).'</a> ';
      if (in_array($priority, explode(",", $params['priority']))) {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('priority' => str_replace($priority, '',$params['priority'])))).'">-</a> ';
      } else {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('priority' => $params['priority'].','.$priority))).'">+</a> ';
      }
    }
    ?>
  </div>
  <div id="typeFilters">
    <strong><?php echo lang('type'); ?>:</strong>
    <?php
    $types = get_ticket_types();
    echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('type'=> ''))).'">'.lang('all').'</a> ';
    foreach ($types as $type) {
      echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('type'=> $type))).'">'.lang($type).'</a> ';
      if (in_array($type, explode(",", $params['type']))) {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('type' => str_replace($type, '',$params['type'])))).'">-</a> ';
      } else {
        echo '<a href="'.get_url('ticket', 'index', array_merge($params, array('type' => $params['type'].','.$type))).'">+</a> ';
      }
    }
    ?>
  </div>
  <div id="categoryFilters">
    <strong><?php echo lang('category'); ?>:</strong>
    
  </div>
</div><!-- // ticketsFiltersContent -->