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
    * Return all tickets that are assigned to the user
    *
    * @param User $user
    * @param boolean $include_company includes tickets assigned to whole company
    * @return array
    */
    function getOpenTicketsByUser(User $user, $include_company = false) {
      $projects = $user->getActiveProjects();
      if (!is_array($projects) || !count($projects)) {
        return null;
      } // if
      
      $project_ids = array();
      foreach ($projects as $project) {
        $project_ids[] = $project->getId();
      } // foreach
      
      // TODO This request contains an hard-coded value for status. Might need to be changed
      // if ticket properties are made more generic
      if ($include_company) {
        return self::findAll(array(
          'conditions' => array('(`assigned_to_user_id` = ? OR (`assigned_to_user_id` = ? AND `assigned_to_company_id` = ?)) AND `project_id` IN (?) AND `status` <> ?', $user->getId(), 0, $user->getCompanyId(), $project_ids, 'closed'),
          'order' => '`due_date`'
          )); // findAll
      } else {
        return self::findAll(array(
          'conditions' => array('`assigned_to_user_id` = ? AND `project_id` IN (?) AND `status` <> ?', $user->getId(), $project_ids, 'closed'),
          'order' => '`due_date`'
          )); // findAll
      } // if
      
    } // getOpenProjectTickets
    
    /**
    * Return late tickets that are assigned to the user
    *
    * @param User $user
    * @param boolean $include_company includes tickets assigned to whole company
    * @return array
    */
    function getLateTicketsByUser(User $user, $include_company = false) {
      $due_date = DateTimeValueLib::now()->beginningOfDay();

      $projects = $user->getActiveProjects();
      if (!is_array($projects) || !count($projects)) {
        return null;
      } // if
      
      $project_ids = array();
      foreach ($projects as $project) {
        $project_ids[] = $project->getId();
      } // foreach
      
      // TODO This request contains a hard-coded value for status. Might need to be changed
      // if ticket properties are made more generic
      if ($include_company) {
        return self::findAll(array(
          'conditions' => array('(`assigned_to_user_id` = ? OR (`assigned_to_user_id` = ? AND `assigned_to_company_id` = ?)) AND `project_id` IN (?) AND `status` <> ? AND `due_date` < ?', $user->getId(), 0, $user->getCompanyId(), $project_ids, 'closed', $due_date),
          'order' => '`due_date`'
          )); // findAll
      } else {
        return self::findAll(array(
          'conditions' => array('`assigned_to_user_id` = ? AND `project_id` IN (?) AND `status` <> ? AND `due_date` < ?', $user->getId(), $project_ids, 'closed', $due_date),
          'order' => '`due_date`'
        )); // findAll
      } // if
      
    } // getOpenProjectTickets
    
    /**
    * Return open tickets due in specified period
    *
    * @access public
    * @param User $user
    * @param DateTimeValue $from_date
    * @param DateTimeValue $to_date
    * @return array
    */
    function getOpenTicketsInPeriodByUser(User $user, DateTimeValue $from_date, DateTimeValue $to_date) {
      $projects = $user->getActiveProjects();
      if (!is_array($projects) || !count($projects)) {
        return null;
      }
      
      $project_ids = array();
      foreach ($projects as $project) {
        $project_ids[] = $project->getId();
      } // foreach
      
      // TODO status values hard-coded in query
      return self::findAll(array(
        'conditions' => array('`status` IN (?) AND (`due_date` >= ? AND `due_date` < ?) AND `project_id` IN (?)', array('new', 'open', 'pending'), $from_date, $to_date, $project_ids),
        'order' => '`due_date` ASC'
      )); // findAll
    } // getOpenTicketsInPeriodByUser

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
    static function getIndexUrl($options) {
      return get_url('ticket', 'index', $options);
    } // getIndexUrl

  } // ProjectTickets 

?>