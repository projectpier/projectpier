<?php

  /**
  * Feed controller is used to handle all feed related events - iCal, RSS etc
  *
  * @http://www.projectpier.org/
  */
  class FeedController extends PageController {
  
    /**
    * Construct the controller
    *
    * @param void
    * @return FeedController
    */
    function __construct() {
      parent::__construct();
      $this->setLayout('xml'); // default layout for this controller
    } // __construct
    
    // ---------------------------------------------------
    //  RSS
    // ---------------------------------------------------
    
    /**
    * List recent activities
    * 
    * This page will list recent activities. If project_id variable is present in get recent activities will be listed 
    * for that specific project. If that value is missing global activities will be listed
    *
    * @param void
    * @return null
    */
    function recent_activities() {
      $this->setLayout('xml');
      
      $logged_user = $this->loginUserByToken();

      $active_projects = $logged_user->getActiveProjects();
      $activity_log = null;
      if(is_array($active_projects) && count($active_projects)) {
        $include_private = $logged_user->isMemberOfOwnerCompany();
        $include_silent = $logged_user->isAdministrator();
        
        $project_ids = array();
        if(isset($_GET['project_id'])) {
          $project_ids[] = (integer) array_var($_GET, 'project_id');
        } else {
          foreach($active_projects as $active_project) {
            $project_ids[] = $active_project->getId();
          } // foreach
        } // if
        
        $activity_log = ApplicationLogs::getOverallLogs($include_private, $include_silent, $project_ids, config_option('feed_logs_count', 50));
      } // if
      
      $feed = new Angie_Feed(lang('recent activities feed'), undo_htmlspecialchars(ROOT_URL));
      $feed = $this->populateFeedFromLog($feed, $activity_log);
      
      $this->renderText($feed->renderRSS2(), true);
    } // recent_activities
    
    /**
    * List project activities as a RSS feed
    *
    * @param void
    * @return null
    */
    function project_activities() {
      $this->setLayout('xml');
      
      $logged_user = $this->loginUserByToken();
      if(!($logged_user instanceof User)) {
        header("HTTP/1.0 404 Not Found");
        die();
      } // if
      
      $project = Projects::findById(array_var($_GET, 'project'));
      if(!($project instanceof Project)) {
        header("HTTP/1.0 404 Not Found");
        die();
      } // if
      
      if(!$logged_user->isProjectUser($project)) {
        header("HTTP/1.0 404 Not Found");
        die();
      } // if
      
      $include_private = $logged_user->isMemberOfOwnerCompany();
      $include_silent = $logged_user->isAdministrator();
      
      $activity_log = ApplicationLogs::getOverallLogs($include_private, $include_silent, array($project->getId()), config_option('feed_logs_count', 50));
      $feed = new Angie_Feed(lang('recent project activities feed', $project->getName()), undo_htmlspecialchars($project->getOverviewUrl()));
      $feed = $this->populateFeedFromLog($feed, $activity_log);
      
      $this->renderText($feed->renderRSS2(), true);
    } // project_activities
    
    // ---------------------------------------------------
    //  Calendar
    // ---------------------------------------------------
    
    /**
    * Show iCalendar for specific user
    *
    * @param void
    * @return null
    */
    function user_ical() {
      $this->setLayout('ical');
      
      $user = $this->loginUserByToken();
      if(!($user instanceof User)) {
        header('HTTP/1.0 404 Not Found');
        die();
      } // if
      
      $this->renderCalendar($user, lang('user calendar', $user->getDisplayName()), $user->getActiveMilestones());
    } // user_ical
    
    /**
    * Show calendar for specific project
    *
    * @param void
    * @return null
    */
    function project_ical() {
      $this->setLayout('ical');
      
      $user = $this->loginUserByToken();
      if(!($user instanceof User)) {
        header('HTTP/1.0 404 Not Found');
        die();
      } // if
      
      $project = Projects::findById(array_var($_GET, 'project'));
      if(!($project instanceof Project)) {
        header('HTTP/1.0 404 Not Found');
        die();
      } // if
      
      if(!$user->isProjectUser($project)) {
        header('HTTP/1.0 404 Not Found');
        die();
      } // if
      
      $this->renderCalendar($user, lang('project calendar', $project->getName()), ProjectMilestones::getActiveMilestonesByUserAndProject($user, $project));
    } // project_ical
    
    /**
    * Render icalendar from milestones
    *
    * @param string $calendar_name
    * @param array $milestones
    * @return null
    */
    private function renderCalendar(User $user, $calendar_name, $milestones) {
      $calendar = new iCalendar_Calendar();
      $calendar->setPropertyValue('VERSION', '2.0');
      $calendar->setPropertyValue('PRODID', '-//Apple Computer\, Inc//iCal 1.5//EN');
      $calendar->setPropertyValue('X-WR-CALNAME', $calendar_name);
      $calendar->setPropertyValue('X-WR-TIMEZONE', 'GMT');
      
      if(is_array($milestones)) {
        foreach($milestones as $milestone) {
          if(!$user->isMemberOfOwnerCompany() && $milestone->isPrivate()) continue; // hide private milestone
          
          if(!$milestone->isCompleted()) {
            $event = new iCalendar_Event();
            
            $date = $milestone->getDueDate();
            $event->setPropertyValue('DTSTART', $date->format('Ymd'), array('VALUE' => 'DATE'));
            $date->advance(24 * 60 * 60);
            $event->setPropertyValue('DTEND', $date->format('Ymd'), array('VALUE' => 'DATE'));
            $event->setPropertyValue('UID', $milestone->getId());
            $event->setPropertyValue('SUMMARY', $milestone->getName() . ' (' . $milestone->getProject()->getName() . ')');
            $event->setPropertyValue('DESCRIPTION', $desc = $milestone->getDescription());
            /* pre_var_dump($desc); */
            
            $calendar->addComponent($event);
          } // if
        } // foreach
      } // if
      
      header('Content-Disposition: inline; filename=calendar.ics');
      $this->renderText(iCalendar::render($calendar), true);
      die();
    } // renderCalendar
    
    // ---------------------------------------------------
    //  Util methods
    // ---------------------------------------------------
    
    /**
    * Populate feed object with activity log entries
    *
    * @param Angie_Feed
    * @param array $activity_log
    * @return Angie_Feed
    */
    private function populateFeedFromLog(Angie_Feed $feed, $activity_log) {
      if(is_array($activity_log)) {
        foreach($activity_log as $activity_log_entry) {
          $item = $feed->addItem(new Angie_Feed_Item($activity_log_entry->getText(), undo_htmlspecialchars($activity_log_entry->getObjectUrl()), '', $activity_log_entry->getCreatedOn()));
          $taken_by = $activity_log_entry->getTakenBy();
          if($taken_by instanceof User) {
            $item->setAuthor(new Angie_Feed_Author($taken_by->getDisplayName(), $taken_by->getEmail()));
          } // if
        } // foreach
      } // if
      
      return $feed;
    } // populateFeedFromLog
    
    /**
    * Log user by token and ID provided through GET method
    *
    * @param void
    * @return User
    */
    private function loginUserByToken() {
      $user = Users::findById(array_var($_GET, 'id'));
      if(!($user instanceof User)) {
        header("HTTP/1.0 404 Not Found");
        die();
      } // if
      
      if(!$user->isValidToken(array_var($_GET, 'token'))) {
        header("HTTP/1.0 404 Not Found");
        die();
      } // if
      
      CompanyWebsite::instance()->setLoggedUser($user, false, false);
      return $user;
    } // loginUserByToken
  
  } // FeedController

?>
