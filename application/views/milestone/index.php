<?php

  set_page_title(lang('milestones'));
  project_tabbed_navigation(PROJECT_TAB_MILESTONES);
  project_crumbs(lang('milestones'));
  if (ProjectMilestone::canAdd(logged_user(), active_project())) {
    add_page_action(lang('add milestone'), get_url('milestone', 'add'));
  } // if
  add_page_action(lang('view calendar'), get_url('milestone', 'calendar'));

?>
<?php if ($all_visible_milestones) { ?>
  <div id="viewToggle">
    <a href="<?php echo get_url('milestone', 'index', array('view'=>'list')); ?>"><img src="<?php if ($view_type=="list") { echo get_image_url("icons/list_on.png"); } else { echo get_image_url("icons/list_off.png"); } ?>" title="<?php echo lang('list view'); ?>" alt="<?php echo lang('list view'); ?>"/></a>
    <a href="<?php echo get_url('milestone', 'index', array('view'=>'detail')); ?>"><img src="<?php if ($view_type=="detail") { echo get_image_url("icons/excerpt_on.png"); } else { echo get_image_url("icons/excerpt_off.png"); } ?>" title="<?php echo lang('detail view'); ?>" alt="<?php echo lang('detail view'); ?>"/></a>
  </div>
  <div id="milestones">
<?php   if ($view_type == 'list') { ?>
    <table id="shortMilestones">
      <tr class="milestone short header"><th class="milestoneCompleted"></th><th class="milestoneDueDate"><?php echo lang('due date'); ?></th><th class="milestoneTitle"><?php echo lang('title'); ?></th><th class="milestoneDaysLeft"></th><th class="milestoneCommentsCount"><img src="<?php echo get_image_url("icons/comments.png"); ?>" title="Comments" alt="Comments"/></th></tr>
  <?php
    foreach ($all_visible_milestones as $milestone) {
      $this->assign('milestone', $milestone);
      $this->includeTemplate(get_template_path('view_milestone_short', 'milestone'));
    } // foreach
  ?>
    </table>
<?php   } else { ?>
<?php if (is_array($late_milestones) && count($late_milestones)) { ?>
  <div id="lateMilestones">
  <h2><?php echo lang('late milestones') ?></h2>
<?php 
  foreach ($late_milestones as $milestone) {
    $this->assign('milestone', $milestone);
    $this->includeTemplate(get_template_path('view_milestone', 'milestone'));
  } // foreach 
?>
  </div>
<?php } // if ?>

<?php if (is_array($today_milestones) && count($today_milestones)) { ?>
  <div id="todayMilestones">
  <h2><?php echo lang('today milestones') ?></h2>
<?php 
  foreach ($today_milestones as $milestone) {
    $this->assign('milestone', $milestone);
    $this->includeTemplate(get_template_path('view_milestone', 'milestone'));
  } // foreach 
?>
  </div>
<?php } // if ?>

<?php if (is_array($upcoming_milestones) && count($upcoming_milestones)) { ?>
  <div id="upcomingMilestones">
  <h2><?php echo lang('upcoming milestones') ?></h2>
<?php 
  foreach ($upcoming_milestones as $milestone) {
    $this->assign('milestone', $milestone);
    $this->includeTemplate(get_template_path('view_milestone', 'milestone'));
  } // foreach 
?>
  </div>
<?php } // if ?>

<?php if (is_array($completed_milestones) && count($completed_milestones)) { ?>
  <div id="completedMilestones">
  <h2><?php echo lang('completed milestones') ?></h2>
<?php 
  foreach ($completed_milestones as $milestone) {
    $this->assign('milestone', $milestone);
    $this->includeTemplate(get_template_path('view_milestone', 'milestone'));
  } // foreach 
?>
  </div>
<?php } // if ?>

<?php   } ?>
</div>
<?php } else { ?>
<p><?php echo clean(lang('no active milestones in project')) ?></p>
<?php } // if ?>
