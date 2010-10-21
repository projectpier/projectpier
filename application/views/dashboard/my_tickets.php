<?php 

  // Set page title and set crumbs to index
  set_page_title(lang('my tickets'));
  dashboard_tabbed_navigation(DASHBOARD_TAB_MY_TICKETS);
  dashboard_crumbs(lang('my tickets')); 
  add_stylesheet_to_page('project/tickets.css');

?>
<?php 
  // If user have any assigned task or milestone this variable will be changed to TRUE
  // else it will remain false
  $has_assigned_tickets = false; 
?>
<?php if (isset($active_projects) && is_array($active_projects) && count($active_projects)) { ?>
<div id="tickets">
<?php foreach ($active_projects as $active_project) { ?>
<?php
  $tickets = $active_project->getUsersTickets(logged_user(), array('limit' => 5, 'order' => '`id` ASC'));
  $params['sort_by'] = 'id';
  $params['order'] = 'ASC';
?>
<?php if (is_array($tickets) && count($tickets)) { ?>
<?php $has_assigned_tickets = true ?>
  <div class="block">
    <div class="header"><h2><a href="<?php echo $active_project->getOverviewUrl() ?>"><?php echo clean($active_project->getName()) ?></a></h2></div>
    <div class="content">
      <p><a href="<?php echo $active_project->getTicketsUrl() ?>"><?php echo lang('tickets') ?></a>:</p>
      <div>
<?php
  $params['active_project'] = $active_project->getId();
  $this->assign('params', $params);
  $this->assign('tickets', $tickets);
  $this->includeTemplate(get_template_path('view_tickets', 'ticket'));
?>
      </div>
    </div>
  <a href="<?php echo $active_project->getTicketsUrl() ?>"><?php echo lang('see all tickets for project') ?></a>
  </div>
<?php } // if ?>

<?php } // foreach ?>
</div>
<?php } else { ?>
<p><?php echo lang('no active projects in db') ?></p>
<?php } // if  ?>

<?php if (!$has_assigned_tickets) { ?>
<p><?php echo lang('no my tickets') ?></p>
<?php } // if ?>
