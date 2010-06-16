<?php

  /**
  * Dashboard controller
  *
  * @http://www.projectpier.org/
  */
  class DashboardController extends ApplicationController {
    
    /**
    * Construct controller and check if we have logged in user
    *
    * @param void
    * @return null
    */
    function __construct() {
      parent::__construct();
      prepare_company_website_controller($this, 'dashboard');
    } // __construct
  
    /**
    * Show dashboard index page
    *
    * @param void
    * @return null
    */
    function index() {
      $logged_user = logged_user();
      
      $active_projects = $logged_user->getActiveProjects();
      $activity_log = null;
      if (is_array($active_projects) && count($active_projects)) {
        $include_private = $logged_user->isMemberOfOwnerCompany();
        $include_silent = $logged_user->isAdministrator();
        
        $project_ids = array();
        foreach ($active_projects as $active_project) {
          $project_ids[] = $active_project->getId();
        } // if
        
        $activity_log = ApplicationLogs::getOverallLogs($include_private, $include_silent, $project_ids, config_option('dashboard_logs_count', 15));
      } // if
      
      tpl_assign('today_milestones', $logged_user->getTodayMilestones());
      tpl_assign('late_milestones', $logged_user->getLateMilestones());
      tpl_assign('active_projects', $active_projects);
      tpl_assign('activity_log', $activity_log);
      
      // Sidebar
      tpl_assign('online_users', Users::getWhoIsOnline());
      tpl_assign('my_projects', $active_projects);
      $this->setSidebar(get_template_path('index_sidebar', 'dashboard'));
    } // index
    
    /**
    * Show my projects page
    *
    * @param void
    * @return null
    */
    function my_projects() {
      $this->addHelper('textile');
      tpl_assign('active_projects', logged_user()->getActiveProjects());
      tpl_assign('finished_projects', logged_user()->getFinishedProjects());
      $this->setSidebar(get_template_path('my_projects_sidebar', 'dashboard'));
    } // my_projects
    
    /**
    * Show milestones and tasks assigned to specific user
    *
    * @param void
    * @return null
    */
    function my_tasks() {
      tpl_assign('active_projects', logged_user()->getActiveProjects());
      $this->setSidebar(get_template_path('my_tasks_sidebar', 'dashboard'));
    } // my_tasks

    /**
      * Return all tickets assigned to this user
      *
      * @param void
      * @return array
      */
    function my_tickets() {
      tpl_assign('active_projects', logged_user()->getActiveProjects());
      $this->setSidebar(get_template_path('index_sidebar', 'ticket'));
    } // my_tickets


    /**
      * Lists all company contacts
      * 
      * @param void
      * @return null
      */
    function contacts() {
      if (!logged_user()->isMemberOfOwnerCompany()) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      }
      // TODO write controller code
    } // contacts

    /**
      * Shows weekly schedule in a calendar view
      * 
      * @param void
      * @return null
      */
    function weekly_schedule() {
      $this->addHelper('textile');
      // Gets desired view 'detail', 'list' or 'calendar'
      // $view_type is from URL, Cookie or set to default: 'calendar'
      $view_type = array_var($_GET, 'view', Cookie::getValue('weeklyScheduleViewType', 'calendar'));
      $expiration = Cookie::getValue('remember'.TOKEN_COOKIE_NAME) ? REMEMBER_LOGIN_LIFETIME : null;
      Cookie::setValue('weeklyScheduleViewType', $view_type, $expiration);
      
      $monthYear = array_var($_GET, 'month');
      if (!isset($monthYear) || trim($monthYear) == '' || preg_match('/^(\d{4})(\d{2})$/', $monthYear, $matches) == 0) {
        $year = gmdate('Y');
        $month = gmdate('m');
      } else {
        list(, $year, $month) = $matches;
      }
      tpl_assign('year', $year);
      tpl_assign('month', $month);

      tpl_assign('view_type', $view_type);
      tpl_assign('all_visible_milestones', logged_user()->getActiveMilestones());
      tpl_assign('late_milestones', logged_user()->getLateMilestones());
    } // weekly_schedule

    
  
  } // DashboardController

?>
