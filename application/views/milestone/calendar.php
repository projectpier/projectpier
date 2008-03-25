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
  $firstDayOfWeek = 1; // configurable?
  $daysInWeek = 7;
  $lastDayOfWeek = $firstDayOfWeek + $daysInWeek;
  $firstDayOfMonth = gmdate('w', $thisMonth);
?>
  <table width="100%">
    <tr valign="top">
<?php
  for ($dow = $firstDayOfWeek; $dow < $lastDayOfWeek; $dow++) {
    if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, array(1, 7))) {
      $dow_class = "weekend";
    } else {
      $dow_class = "weekday";
    }
?>
      <th class="<?php echo $dow_class; ?>"><?php echo clean(lang(sprintf('dow %u', $dow > $daysInWeek ? $dow - $daysInWeek : $dow))); ?></th>
<?php
  } // for
?>
    </tr>
    <tr valign="top">
<?php

  /*
   * Skip days from previous month.
   */

  for ($dow = $firstDayOfWeek; $dow <= $firstDayOfMonth; $dow++) {
    if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, array(1, 7))) {
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
      if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, array(1, 7))) {
        $dow_class = "weekend";
      } else {
        $dow_class = "weekday";
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
        <ul>
<?php
        } // if
?>
      </td>
<?php
    } // for
?>
<?php if ($dom <= $daysInMonth) { ?>
    </tr>
    <tr valign="top">
<?php
      $dow = $firstDayOfWeek;
    } // if
  } // for

  /*
   * Skip days from next month.
   */

  if ($dow < $lastDayOfWeek) {
    for (; $dow < $lastDayOfWeek; $dow++) {
      if (in_array($dow > $daysInWeek ? $dow - $daysInWeek : $dow, array(1, 7))) {
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
    <div class="prev-month"><a href="<?php echo get_url('milestone', 'calendar', gmdate('Ym', $prevMonth)); ?>"><?php echo clean(lang(sprintf('month %u', gmdate('m', $prevMonth)))); ?> <?php echo gmdate('Y', $prevMonth); ?></a></div>
    <div class="next-month"><a href="<?php echo get_url('milestone', 'calendar', gmdate('Ym', $nextMonth)); ?>"><?php echo clean(lang(sprintf('month %u', gmdate('m', $nextMonth)))); ?> <?php echo gmdate('Y', $nextMonth); ?></a></div>
  </div>
</div>
