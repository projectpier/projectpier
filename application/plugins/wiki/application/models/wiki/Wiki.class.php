<?php

/**
 * Wiki
 * 
 * @package ProjectPier Wiki
 * @author Alex Mayhew
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class Wiki extends BaseWiki {

  /**
  * Get a wiki page by its ID
  * 
  * @param int Page Id
  * @param Project $project Active project
  * @return
  */
  function getPageById($wiki_page_id, Project $project) {
    $params = array(
      'conditions' => array(
        '`id` = ? AND `project_id` = ?', 
        $wiki_page_id, 
        $project->getId()
      )
    );
    return parent::findOne($params);		
	} // getPageById

  /**
  * Get the index page of a project
  * 
  * @param Project $project Instance of project
  * @return
  */
  function getProjectIndex(Project $project) {
    $params = array(
      'conditions' => array(
        'project_id = ? AND project_index = 1',
        $project->getId()
      )
    );
		return parent::findone($params);
	} // getProjectIndex

  /**
  * Get the sidebar for a project
  * 
  * @param Project $project
  * @return
  */
  function getProjectSidebar($project = null) {
    $params = array(
      'conditions' => array(
        'project_id = ? AND project_sidebar = 1',
        (instance_of($project, 'Project') ? $project->getId() : 0)
      )
    );

    return parent::findone($params);
  } // getProjectSidebar

  /**
  * Get a list of pages for a project
  * 
  * @param mixed $project
  * @return
  */
  function getPagesList(Project $project) {

    $sql = 'SELECT p.id, r.name FROM ' . Wiki::instance()->getTableName() . ' AS p, ' . Revisions::instance()->getTableName() . ' AS r WHERE p.project_id = ' . $project->getId() . ' AND p.id = r.page_id AND r.revision = p.revision';
    $return = array();

    $result = Db::executeAll($sql);

    if (is_array($result) && count($result)) {
      foreach ($result as $page) {
        $return[] = array('name' 	=> $page['name'],
          'view_url'		=> get_url('wiki', 'view', array('id' => $page['id']))
          );
      } // foreach
    } // if
    return $return;
  } // getPagesList
} // Wiki

?>
