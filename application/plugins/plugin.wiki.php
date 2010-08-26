<?php

// add project tab
define('PROJECT_TAB_WIKI', 'wiki');
add_action('add_project_tab', 'wiki_add_project_tab');

// Add [wiki:234] filter to common pages
add_filter('project_description', 'wiki_wiki_links');
add_filter('all_messages_message_text', 'wiki_wiki_links');
add_filter('message_text', 'wiki_wiki_links');
add_filter('message_additional_text', 'wiki_wiki_links');
add_filter('task_list_description', 'wiki_wiki_links');
add_filter('open_task_text', 'wiki_wiki_links');
add_filter('completed_task_text', 'wiki_wiki_links');
add_filter('ticket_description', 'wiki_wiki_links');
add_filter('ticket_change_comment', 'wiki_wiki_links');
add_filter('milestone_description', 'wiki_wiki_links');
add_filter('comment_text', 'wiki_wiki_links');
add_filter('file_description', 'wiki_wiki_links');
add_filter('form_description', 'wiki_wiki_links');
add_filter('pageattachment_text', 'wiki_wiki_links');

// Make sure the other kind of wiki links are filtered in the wiki pages
add_filter('wiki_text', 'wiki_links');

function wiki_add_project_tab() {
  add_tabbed_navigation_item(new TabbedNavigationItem(
    PROJECT_TAB_WIKI,
    lang('wiki'),
    get_url('wiki')
    ));
}

// overview page
// add_action('project_overview_page_actions','wiki_project_overview_page_actions');
function wiki_project_overview_page_actions() {
  if (ProjectLink::canAdd(logged_user(), active_project())) {
    add_page_action(lang('add wiki page'), get_url('wiki', 'add'));
  } // if
}

/**
  * If you need an activation routine run from the admin panel
  * use the following pattern for the function:
  *   <name_of_plugin>_activate
  *
  * This is good for creation of database tables etc.
  */
function wiki_activate() {
  $sql = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."wiki_pages` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `revision` int(10) unsigned default NULL,
    `project_id` int(10) unsigned default NULL,
    `project_sidebar` tinyint(1) unsigned default '0',
    `project_index` tinyint(1) unsigned default '0',
    `locked` tinyint(1) default '0',
    `locked_by_id` int(10) unsigned default NULL,
    `locked_on` datetime default '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
    ) TYPE=InnoDB  DEFAULT CHARSET=".DB_CHARSET;
  // create table wiki_pages
  DB::execute($sql);
  $sql = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."wiki_revisions` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `project_id` int(10) unsigned default NULL,
    `page_id` int(10) unsigned default NULL,
    `revision` tinyint(3) unsigned default NULL,
    `name` varchar(50) NOT NULL,
    `content` text NOT NULL,
    `created_on` datetime default NULL,
    `created_by_id` int(10) unsigned default NULL,
    `log_message` varchar(255) default NULL,
    PRIMARY KEY  (`id`)
    ) TYPE=InnoDB  DEFAULT CHARSET=".DB_CHARSET;
  // create table wiki_revisions
  DB::execute($sql);
} // wiki_activate

/**
  * If you need a de-activation routine run from the admin panel
  * use the following pattern for the function:
*
  *   <name_of_plugin>_deactivate
  *
  * This is good for deletion of database tables etc.
  */
function wiki_deactivate($purge=false) {
  // sample drop table
  if ($purge) {
    DB::execute("DROP TABLE IF EXISTS `".TABLE_PREFIX."wiki_revisions`;");
    DB::execute("DROP TABLE IF EXISTS `".TABLE_PREFIX."wiki_pages`;");
    DB::execute("DELETE FROM ".TABLE_PREFIX."application_logs where rel_object_manager='Wiki';");
  } // if 
} // wiki_deactivate




/**
  * Wiki link helper
  * Replaces wiki links in format [wiki:{PAGE_ID}] with a textile link to the page
  * 
  * @param mixed $content
  * @return
  */

function wiki_wiki_links($content) {
  $content = preg_replace_callback('/\[wiki:([0-9]*)\]/', 'replace_wiki_link_callback', $content);
  $content = preg_replace_callback('/\[wiki:(.*)\]/', 'replace_wiki_link_title_callback', $content);
  return $content;
} // wiki_links

/**
  * Call back function for wiki helper
  * 
  * @param mixed $matches
  * @return
  */
function replace_wiki_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  $object = Revisions::findOne(array(
    'conditions' => array('`page_id` = ? AND `project_id` = ?', $matches[1], active_project()->getId()),
    'order' => '`revision` DESC'));
  
  if (!($object instanceof Revision)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getPage()->getViewUrl().'">'.$object->getName().'</a>';
  } // if
} // replace_wiki_link_callback

/**
  * Call back function for wiki helper to link by title
  * 
  * @param mixed $matches
  * @return
  */
function replace_wiki_link_title_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  $object = Revisions::findOne(array(
    'conditions' => array('`name` = ? AND `project_id` = ?', $matches[1], active_project()->getId()),
    'order' => '`revision` DESC'));
  
  if (!($object instanceof Revision)) {
    return '<del>'.lang('invalid reference').'</del>';
  } else {
    return '<a href="'.$object->getPage()->getViewUrl().'">'.$object->getName().'</a>';
  } // if
} // replace_wiki_link_title_callback

?>
