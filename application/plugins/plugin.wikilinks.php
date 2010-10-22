<?php

add_filter('project_description', 'wiki_links');
add_filter('all_messages_message_text', 'wiki_links');
add_filter('message_text', 'wiki_links');
add_filter('message_additional_text', 'wiki_links');
add_filter('task_list_description', 'wiki_links');
add_filter('open_task_text', 'wiki_links');
add_filter('completed_task_text', 'wiki_links');
add_filter('ticket_description', 'wiki_links');
add_filter('ticket_change_comment', 'wiki_links');
add_filter('milestone_description', 'wiki_links');
add_filter('comment_text', 'wiki_links');
add_filter('file_description', 'wiki_links');
add_filter('form_description', 'wiki_links');
add_filter('pageattachment_text', 'wiki_links');

function wiki_links($content) {
  $content = preg_replace_callback('/\[message:([0-9]*)\]/', 'replace_message_link_callback', $content);
  $content = preg_replace_callback('/\[task_list:([0-9]*)\]/', 'replace_task_list_link_callback', $content);
  $content = preg_replace_callback('/\[ticket:([0-9]*)\]/', 'replace_ticket_link_callback', $content);
  $content = preg_replace_callback('/\[milestone:([0-9]*)\]/', 'replace_milestone_link_callback', $content);
  $content = preg_replace_callback('/\[file:([0-9]*)\]/', 'replace_file_link_callback', $content);
  return $content;
} // wiki_links

/**
  * Call back function for message link
  * 
  * @param mixed $matches
  * @return
  */
function replace_message_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  if (!logged_user()->isMemberOfOwnerCompany()) {
    $object = ProjectMessages::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ? AND `is_private` = 0 ', $matches[1], active_project()->getId())));
  } else {
    $object = ProjectMessages::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ?', $matches[1], active_project()->getId())));
  } // if
  
  if (!($object instanceof ProjectMessage)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getViewUrl().'">'.$object->getTitle().'</a>';
  } // if
} // replace_message_link_callback

/**
  * Call back function for task list link
  * 
  * @param mixed $matches
  * @return
  */
function replace_task_list_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  if (!logged_user()->isMemberOfOwnerCompany()) {
    $object = ProjectTaskLists::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ? AND `is_private` = 0 ', $matches[1], active_project()->getId())));
  } else {
    $object = ProjectTaskLists::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ?', $matches[1], active_project()->getId())));
  } // if
  
  if (!($object instanceof ProjectTaskList)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getViewUrl().'">'.$object->getName().'</a>';
  } // if
} // replace_task_list_link_callback

/**
  * Call back function for ticket link
  * 
  * @param mixed $matches
  * @return
  */
function replace_ticket_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  if (!logged_user()->isMemberOfOwnerCompany()) {
    $object = ProjectTickets::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ? AND `is_private` = 0 ', $matches[1], active_project()->getId())));
  } else {
    $object = ProjectTickets::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ?', $matches[1], active_project()->getId())));
  } // if
  
  if (!($object instanceof ProjectTicket)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getViewUrl().'">'.$object->getTitle().'</a>';
  } // if
} // replace_ticket_link_callback

/**
  * Call back function for milestone link
  * 
  * @param mixed $matches
  * @return
  */
function replace_milestone_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  if (!logged_user()->isMemberOfOwnerCompany()) {
    $object = ProjectMilestones::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ? AND `is_private` = 0 ', $matches[1], active_project()->getId())));
  } else {
    $object = ProjectMilestones::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ?', $matches[1], active_project()->getId())));
  } // if
  
  if (!($object instanceof ProjectMilestone)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getViewUrl().'">'.$object->getName().'</a>';
  } // if
} // replace_milestone_link_callback

/**
  * Call back function for file link
  * 
  * @param mixed $matches
  * @return
  */
function replace_file_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  if (!logged_user()->isMemberOfOwnerCompany()) {
    $object = ProjectFiles::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ? AND `is_private` = 0 ', $matches[1], active_project()->getId())));
  } else {
    $object = ProjectFiles::findOne(array(
      'conditions' => array('`id` = ? AND `project_id` = ?', $matches[1], active_project()->getId())));
  } // if
  
  if (!($object instanceof ProjectFile)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getViewUrl().'">'.$object->getFilename().'</a>';
  } // if
} // replace_milestone_link_callback

?>