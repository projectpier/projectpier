<?php

/**
  * @author Alex Mayhew
  * @copyright 2008
  */

/**
  * Wiki link helper
  * Replaces wiki links in format [wiki:{PAGE_ID}] with a textile link to the page
  * 
  * @param mixed $content
  * @return
  */

function wiki_links($content) {
  $content = preg_replace_callback('/\[wiki:([0-9]*)\]/', 'replace_wiki_link_callback', $content);
  $content = preg_replace_callback('/\[wiki:(.*)\]/', 'replace_wiki_link_title_callback', $content);
  return $content;
} // wiki_links

/**
  * Call back function for wiki helper to link by title
  * 
  * @param mixed $matches
  * @return
  */
function replace_wiki_link_title_callback($matches) {
  if (count($matches) < 2) {
    return null;
  } // if

  $sql = 'SELECT page_id FROM ' . Revisions::instance()->getTableName(true) . ' WHERE  name = \'' . $matches[1] . '\' AND project_id = ' . active_project()->getId() . ' ORDER BY revision DESC';

  $row = DB::executeOne($sql);
  if (!count($row)) {
    return $matches[1] . ' <a href="' . get_url('wiki', 'add', array('name' => $matches[1])) . '" title="'.$matches[1].'">?</a>';
  } // if

  return '<a href="' . get_url('wiki', 'view', array('id' => $row['page_id'])) . '" title="'.$matches[1].'">' . $matches[1] . '</a>';
} // replace_wiki_link_title_callback

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

  $sql = 'SELECT name FROM ' . Revisions::instance()->getTableName(true) . ' WHERE page_id = ' . $matches[1] . ' AND project_id = ' . active_project()->getId();

  $row = DB::executeOne($sql);
  if (!count($row)){
    return null;
  } // if

  return '<a href="' . get_url('wiki', 'view', array('id' => $matches[1])) . '" title="'.$row['name'].'">' . $row['name'] . '</a>';
} // replace_wiki_link_callback

?>
