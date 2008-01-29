<?php 

  // Set page title and set crumbs to index
  set_page_title(lang('my tasks'));
  dashboard_tabbed_navigation(DASHBOARD_TAB_MY_TASKS);
  dashboard_crumbs(lang('my tasks'));
  add_stylesheet_to_page('dashboard/my_tasks.css');

?>
<?php 
  // If user have any assigned task or milestone this variable will be changed to TRUE
  // else it will remain false
  $has_assigned_tasks = false; 
?>
<?php if (isset($active_projects) && is_array($active_projects) && count($active_projects)) { ?>
<div id="myTasks">
<?php foreach ($active_projects as $active_project) { ?>
<?php
  $assigned_milestones = $active_project->getUsersMilestones(logged_user());
  $assigned_tasks = $active_project->getUsersTasks(logged_user());
?>
<?php if ((is_array($assigned_milestones) && count($assigned_milestones)) || (is_array($assigned_tasks) && count($assigned_tasks))) { ?>
<?php $has_assigned_tasks = true ?>
  <div class="block">
    <div class="header"><h2><a href="<?php echo $active_project->getOverviewUrl() ?>"><?php echo clean($active_project->getName()) ?></a></h2></div>
    <div class="content">
<?php if (is_array($assigned_milestones) && count($assigned_milestones)) { ?>
      <p><a href="<?php echo $active_project->getMilestonesUrl() ?>"><?php echo lang('milestones') ?></a>:</p>
      <table class="blank">
<?php foreach ($assigned_milestones as $assigned_milestone) { ?>
        <tr>
          <td class="milestoneCheckbox"><?php echo checkbox_link($assigned_milestone->getCompleteUrl(), false) ?></td>
          <td class="milestoneText">
<?php $assigned_to = $assigned_milestone->getAssignedTo() ?>
<?php if ($assigned_to instanceof Company) { ?>
            <span class="assignedTo"><?php echo clean($assigned_to->getName()) ?>:</span> 
<?php } elseif ($assigned_to instanceof User) { ?>
            <span class="assignedTo"><?php echo clean($assigned_to->getDisplayName()) ?>:</span> 
<?php } else { ?>
            <span class="assignedTo"><?php echo lang('anyone') ?>:</span> 
<?php } // if ?>
            <a href="<?php echo $assigned_milestone->getViewUrl() ?>"><?php echo clean($assigned_milestone->getName()) ?></a> - 
<?php if ($assigned_milestone->isUpcoming()) { ?>
            <span><?php echo lang('days left', $assigned_milestone->getLeftInDays()) ?></span>
<?php } elseif ($assigned_milestone->isLate()) { ?>
            <span class="error"><?php echo lang('days late', $assigned_milestone->getLateInDays()) ?></span>
<?php } elseif ($assigned_milestone->isToday()) { ?>
            <span><?php echo lang('today') ?></span>
<?php } // if ?>
          </td>
        </tr>
<?php } // foreach?>
      </table>
<?php } // if ?>

<?php if (is_array($assigned_tasks) && count($assigned_tasks)) { ?>
      <p><a href="<?php echo $active_project->getTasksUrl() ?>"><?php echo lang('tasks') ?></a>:</p>
      <table class="blank">
<?php foreach ($assigned_tasks as $assigned_task) { ?>
        <tr>
          <td class="taskCheckbox"><?php echo checkbox_link($assigned_task->getCompleteUrl(), false, lang('mark task as completed')) ?></td>
          <td class="taskText">
<?php $assigned_to = $assigned_task->getAssignedTo() ?>
<?php if ($assigned_to instanceof Company) { ?>
            <span class="assignedTo"><?php echo clean($assigned_to->getName()) ?>:</span>
<?php } elseif ($assigned_to instanceof User) { ?>
            <span class="assignedTo"><?php echo clean($assigned_to->getDisplayName()) ?>:</span>
<?php } else { ?>
            <span class="assignedTo"><?php echo lang('anyone') ?>:</span>
<?php } // if ?>
            <?php echo clean($assigned_task->getText()) ?> 
<?php if ($assigned_task->getTaskList() instanceof ProjectTaskList) { ?>
            (<?php echo lang('in') ?> <a href="<?php echo $assigned_task->getTaskList()->getViewUrl() ?>"><?php echo clean($assigned_task->getTaskList()->getName()) ?></a>)
<?php } // if ?>
          </td>
        </tr>
<?php } // foreach ?>
      </table>
<?php } // if ?>
    </div>
  </div>
<?php } // if ?>

<?php } // foreach ?>
</div>
<?php } else { ?>
<p><?php echo lang('no active projects in db') ?></p>
<?php } // if  ?>

<?php if (!$has_assigned_tasks) { ?>
<p><?php echo lang('no my tasks') ?></p>
<?php } // if ?>
