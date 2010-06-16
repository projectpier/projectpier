<?php 

  // Set page title and set crumbs to index
  set_page_title(lang('weekly schedule'));
  dashboard_tabbed_navigation(DASHBOARD_TAB_WEEKLY_SCHEDULE);
  dashboard_crumbs(lang('weekly schedule'));
  add_stylesheet_to_page('project/calendar.css');

?>
<?php if ($all_visible_milestones) { ?>
  <div id="viewToggle">
    <a href="<?php echo get_url('dashboard', 'weekly_schedule', array('view'=>'list')); ?>"><img src="<?php if ($view_type=="list") { echo get_image_url("icons/list_on.png"); } else { echo get_image_url("icons/list_off.png"); } ?>" title="<?php echo lang('list view'); ?>" alt="<?php echo lang('list view'); ?>"/></a>
    <a href="<?php echo get_url('dashboard', 'weekly_schedule', array('view'=>'detail')); ?>"><img src="<?php if ($view_type=="detail") { echo get_image_url("icons/excerpt_on.png"); } else { echo get_image_url("icons/excerpt_off.png"); } ?>" title="<?php echo lang('detail view'); ?>" alt="<?php echo lang('detail view'); ?>"/></a>
    <a href="<?php echo get_url('dashboard', 'weekly_schedule', array('view'=>'calendar')); ?>"><img src="<?php if ($view_type=="calendar") { echo get_image_url("icons/calendar_on.png"); } else { echo get_image_url("icons/calendar_off.png"); } ?>" title="<?php echo lang('view calendar'); ?>" alt="<?php echo lang('view calendar'); ?>"/></a>
  </div> <!-- // #viewToggle -->
  <div id="milestones">
<?php if ($view_type == 'list') { ?>
    <table id="shortMilestones">
      <tr class="milestone short header"><th class="milestoneCompleted"></th><th class="milestoneDueDate"><?php echo lang('due date'); ?></th><th class="milestoneTitle"><?php echo lang('title'); ?></th><th class="milestoneDaysLeft"></th><th class="milestoneCommentsCount"><img src="<?php echo get_image_url("icons/comments.png"); ?>" title="Comments" alt="Comments"/></th></tr>
  <?php
    foreach ($all_visible_milestones as $milestone) {
      $this->assign('milestone', $milestone);
      $this->includeTemplate(get_template_path('view_milestone_short', 'milestone'));
    } // foreach
  ?>
    </table>
<?php } elseif ($view_type == 'detail') { ?>
<?php 
  foreach ($all_visible_milestones as $milestone) {
    $this->assign('milestone', $milestone);
    $this->includeTemplate(get_template_path('view_milestone', 'milestone'));
  } // foreach 
?>
<?php } else { ?>
<?php if ($late_milestones && count($late_milestones)) { ?>
  <div id="lateMilestones">
    <h2><?php echo lang('late milestones'); ?></h2>
    <table id="shortMilestones">
      <tr class="milestone short header"><th class="milestoneCompleted"></th><th class="milestoneDueDate"><?php echo lang('due date'); ?></th><th class="milestoneTitle"><?php echo lang('title'); ?></th><th class="milestoneDaysLeft"></th><th class="milestoneCommentsCount"><img src="<?php echo get_image_url("icons/comments.png"); ?>" title="Comments" alt="Comments"/></th></tr>
  <?php 
    foreach ($late_milestones as $milestone) {
      $this->assign('milestone', $milestone);
      $this->includeTemplate(get_template_path('view_milestone_short', 'milestone'));
    } // foreach 
  ?>
    </table>
  </div><br/>
<?php } ?>
  <div class="calendar">
    <h2><?php echo clean(lang(sprintf('month %u', $month))); ?> <?php echo $year; ?></h2>
  <?php
    $calendar = array();
    if (is_array($all_visible_milestones) && count($all_visible_milestones)) {
      foreach ($all_visible_milestones as $milestone) {
        $due = $milestone->getDueDate();
        if ($due->getYear() != $year or $due->getMonth() != $month) {
          continue;
        }
        $calendar[$due->getDay()][] = $milestone;
      }
    } // if
    $thisMonth = gmmktime(0, 0, 0, $month, 1, $year);
    $prevMonth = strtotime('-1 month', $thisMonth);
    $nextMonth = strtotime('+1 month', $thisMonth);
    $daysInMonth = gmdate('d', strtotime('+1 month -1 day', $thisMonth));
    $firstDayOfWeek = 1; // configurable?
    $daysInWeek = 7;
    $weekendDays = array(6,7);
    $lastDayOfWeek = $firstDayOfWeek + $daysInWeek;
    $firstDayOfMonth = gmdate('w', $thisMonth);
    $firstDayOfMonth = $firstDayOfMonth ? $firstDayOfMonth : 7; // gmdate returns 0 for Sunday, but language file use 7 for Sunday
  ?>
    <table>
      <tr>
  <?php
    for ($dow = $firstDayOfWeek; $dow < $lastDayOfWeek; $dow++) {
      if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, $weekendDays)) {
        $dow_class = "weekend";
      } else {
        $dow_class = "weekday";
      } // if
  ?>
        <th class="<?php echo $dow_class; ?>"><?php echo clean(lang(sprintf('dow %u', $dow > $daysInWeek ? $dow - $daysInWeek : $dow))); ?></th>
  <?php } // for ?>
      </tr>
      <tr>
  <?php

    /*
     * Skip days from previous month.
     */

    for ($dow = $firstDayOfWeek; $dow < $firstDayOfMonth; $dow++) {
      if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, $weekendDays)) {
        $dow_class = "weekend";
      } else {
        $dow_class = "weekday";
      }
  ?>
        <td class="<?php echo $dow_class; ?>">&nbsp;</td>
  <?php
    } // for

    /*
     * Render the month's calendar.
     */

    for ($dom = 1; $dom <= $daysInMonth;) {
      for (; ($dow < $lastDayOfWeek) && ($dom <= $daysInMonth); $dow++, $dom++) {
        if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, $weekendDays)) {
          $dow_class = "weekend";
        } else {
          $dow_class = "weekday";
        }
        $today = gmdate('d');
        if (gmdate('m', $thisMonth) == gmdate('m')) {
          if ($dom == $today) {
            $dow_class .= " today";
          }
          if ($dom == $today-1) {
            $dow_class .= " daybefore";
          }
          if ($dom == $today+1) {
            $dow_class .= " dayafter";
          }
          if ($dom == $today-$daysInWeek) {
            $dow_class .= " weekbefore";
          }
        }


  ?>
        <td class="<?php echo $dow_class; ?>">
          <div class="date"><?php echo $dom; ?></div>
  <?php
        if (isset($calendar[$dom]) && is_array($calendar[$dom])
          && count($calendar[$dom])) {
  ?>
          <ul class="entries">
  <?php
            foreach ($calendar[$dom] as $m) {
              printf('<li><a href="%s">%s</a></li>'."\n",
                get_url("milestone", "view", $m->getId()),
                clean($m->getName()));
            }
  ?>
          </ul>
  <?php
          } // if
  ?>
        </td>
  <?php
      } // for
  ?>
  <?php if ($dom <= $daysInMonth) { ?>
      </tr>
      <tr>
  <?php
        $dow = $firstDayOfWeek;
      } // if
    } // for

    /*
     * Skip days from next month.
     */

    if ($dow < $lastDayOfWeek) {
      for (; $dow < $lastDayOfWeek; $dow++) {
        if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, $weekendDays)) {
          $dow_class = "weekend";
        } else {
          $dow_class = "weekday";
        }
  ?>
        <td class="<?php echo $dow_class; ?>">&nbsp;</td>
  <?php
      } // for
  ?>
      </tr>
  <?php
    } // if
  ?>
    </table>
    <div class="month-nav">
      <div class="prev-month"><a href="<?php echo get_url('dashboard', 'weekly_schedule', array('view'=>'calendar', 'month' => gmdate('Ym', $prevMonth))); ?>"><?php echo clean(lang(sprintf('month %u', gmdate('m', $prevMonth)))); ?> <?php echo gmdate('Y', $prevMonth); ?></a></div>
      <div class="next-month"><a href="<?php echo get_url('dashboard', 'weekly_schedule', array('view'=>'calendar', 'month' => gmdate('Ym', $nextMonth))); ?>"><?php echo clean(lang(sprintf('month %u', gmdate('m', $nextMonth)))); ?> <?php echo gmdate('Y', $nextMonth); ?></a></div>
    </div>
  </div>

<?php   } ?>
</div><!-- // #milestones -->
<?php } else { ?>
<p><?php echo clean(lang('no active milestones in project')) ?></p>
<?php } // if ?>
