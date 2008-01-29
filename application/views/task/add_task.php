<?php

  set_page_title($task->isNew() ? lang('add task') : lang('edit task'));
  project_tabbed_navigation(PROJECT_TAB_TASKS);
  project_crumbs(array(
    array(lang('tasks'), get_url('task')),
    array($task_list->getName(), $task_list->getViewUrl()),
    array($task->isNew() ? lang('add task') : lang('edit task'))
  ));
  add_page_action(lang('add task list'), get_url('task', 'add_list'));

?>
<?php if ($task->isNew()) { ?>
<form action="<?php echo $task_list->getAddTaskUrl($back_to_list) ?>" method="post">
<?php } else { ?>
<form action="<?php echo $task->getEditUrl() ?>" method="post">
<?php } // if ?>

<?php tpl_display(get_template_path('form_errors')) ?>

<?php if (!$task->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('task list'), 'addTaskTaskList', true) ?>
    <?php echo select_task_list('task[task_list_id]', active_project(), array_var($task_data, 'task_list_id'), false, array('id' => 'addTaskTaskList')) ?>
  </div>
<?php } // if ?>

  <div>
    <?php echo label_tag(lang('text'), 'addTaskText', true) ?>
    <?php echo textarea_field("task[text]", array_var($task_data, 'text'), array('id' => 'addTaskText', 'class' => 'short')) ?>
  </div>
  <div>
    <label><?php echo lang('assign to') ?>:</label>
    <?php echo assign_to_select_box("task[assigned_to]", active_project(), array_var($task_data, 'assigned_to')) ?>
  </div>
  
  <?php echo submit_button($task->isNew() ? lang('add task') : lang('edit task')) ?>
</form>
