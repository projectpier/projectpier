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
  return preg_replace_callback('/\[wiki:([0-9]*)\]/', 'replace_wiki_link_callback', $content);	
} // wiki_links

function replace_wiki_link_callback($matches) {
  if (count($matches) < 2){
    return null;
  } // if

  $sql = 'SELECT name FROM ' . Revisions::instance()->getTableName(true) . ' WHERE page_id = ' . $matches[1] . ' AND project_id = ' . active_project()->getId();

  $row = DB::executeOne($sql);
  if (!count($row)){
    return null;
  } // if

  return '"' . $row['name']  . '(' . $row['name'] . ')":' . get_url('wiki', 'view', array('id' => $matches[1]));
} // replace_wiki_link_callback

?>
