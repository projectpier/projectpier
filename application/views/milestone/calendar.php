<?php

  set_page_title(lang('milestones'));
  project_tabbed_navigation(PROJECT_TAB_MILESTONES);
  project_crumbs(array(
    array(lang('milestones'), get_url('milestone')),
    array(lang('view calendar'))
  ));
  if (ProjectMilestone::canAdd(logged_user(), active_project())) {
    add_page_action(lang('add milestone'), get_url('milestone', 'add'));
  } // if
  add_stylesheet_to_page('project/calendar.css');

?>
<div id="viewToggle">
  <a href="<?php echo get_url('milestone', 'index', array('view'=>'list')); ?>"><img src="<?php echo get_image_url("icons/list_off.png"); ?>" title="<?php echo lang('list view'); ?>" alt="<?php echo lang('list view'); ?>"/></a>
  <a href="<?php echo get_url('milestone', 'index', array('view'=>'detail')); ?>"><img src="<?php echo get_image_url("icons/excerpt_off.png"); ?>" title="<?php echo lang('detail view'); ?>" alt="<?php echo lang('detail view'); ?>"/></a>
  <a href="<?php echo get_url('milestone', 'calendar'); ?>"><img src="<?php echo get_image_url("icons/calendar_on.png"); ?>" title="<?php echo lang('view calendar'); ?>" alt="<?php echo lang('view calendar'); ?>"/></a>
</div>
<div class="calendar">
  <h2><?php echo clean(lang(sprintf('month %u', $month))); ?> <?php echo $year; ?></h2>
<?php
  $calendar = array();
  if (is_array($milestones) && count($milestones)) {
    foreach ($milestones as $milestone) {
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
  $firstDayOfWeek = 1; // TODO make this parameter configurable
  $daysInWeek = 7;
  $weekendDays = array(6,7); // TODO make this parameter configurable
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
          $dow_class .= " yesterday";
        }
        if ($dom == $today+1) {
          $dow_class .= " tomorrow";
        }
        if ($dom == $today-$daysInWeek) {
          $dow_class .= " lastweek";
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
    <div class="prev-month"><a href="<?php echo get_url('milestone', 'calendar', array('month' => gmdate('Ym', $prevMonth))); ?>"><?php echo clean(lang(sprintf('month %u', gmdate('m', $prevMonth)))); ?> <?php echo gmdate('Y', $prevMonth); ?></a></div>
    <div class="next-month"><a href="<?php echo get_url('milestone', 'calendar', array('month' => gmdate('Ym', $nextMonth))); ?>"><?php echo clean(lang(sprintf('month %u', gmdate('m', $nextMonth)))); ?> <?php echo gmdate('Y', $nextMonth); ?></a></div>
  </div>
</div>
