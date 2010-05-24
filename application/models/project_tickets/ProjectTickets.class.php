<?php

  /**
  * ProjectTickets, generated on Wed, 08 Mar 2006 15:51:26 +0100 by 
  * DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class ProjectTickets extends BaseProjectTickets {    
    
    /**
    * Return tickets that belong to specific project
    *
    * @param Project $project
    * @param boolean $include_private Include private tickets in the result
    * @return array
    */
    static function getProjectTickets(Project $project, $include_private = false) {
      if($include_private) {
        $conditions = array('`project_id` = ?', $project->getId());
      } else {
        $conditions = array('`project_id` = ? AND `is_private` = ?', $project->getId(), false);
      } // if
      
      return self::findAll(array(
        'conditions' => $conditions,
        'order' => '`created_on` DESC',
      )); // findAll
    } // getProjectTickets
    
    /**
    * Return open tickets for specific project
    *
    * @param Project $project
    * @param boolean $include_private Include private tickets
    * @return array
    */
    static function getOpenProjectTickets(Project $project, $include_private = false) {
      if($include_private) {
        $conditions = array('`project_id` = ? AND `closed_on` = ?', $project->getId(), EMPTY_DATETIME);
      } else {
        $conditions = array('`project_id` = ? AND `closed_on` = ? AND `is_private` = ?', $project->getId(), EMPTY_DATETIME, false);
      } // if
      
      return self::findAll(array(
        'conditions' => $conditions,
        'order' => '`created_on` DESC',
      )); // findAll
    } // getOpenProjectTickets
    
    /**
    * Return closed tickets for specific project
    *
    * @param Project $project
    * @param boolean $include_private Include private tickets
    * @return array
    */
    static function getClosedProjectTickets(Project $project, $include_private = false) {
      if($include_private) {
        $conditions = array('`project_id` = ? AND `closed_on` > ?', $project->getId(), EMPTY_DATETIME);
      } else {
        $conditions = array('`project_id` = ? AND `closed_on` > ? AND `is_private` = ?', $project->getId(), EMPTY_DATETIME, false);
      } // if
      
      return self::findAll(array(
        'conditions' => $conditions,
        'order' => '`created_on` DESC',
      )); // findAll
    } // getClosedProjectTickets
  
    /**
    * Return ticket index page
    *
    * @param string $order_by
    * @param integer $page
    * @return string
    */
    static function getIndexUrl($closed = false) {
      if ($closed) {
        $options = array('closed' => true);
      } else {
        $options = array();
      } // if
      return get_url('ticket', 'index', $options);
    } // getIndexUrl

  } // ProjectTickets 

?>