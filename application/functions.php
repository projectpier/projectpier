<?php

  // ---------------------------------------------------
  //  System callback functions, registered automatically
  //  or in application/application.php
  // ---------------------------------------------------

  /**
  * Gets called, when an undefined class is being instantiated
  *
  * @param_string $load_class_name
  */
  function __autoload($load_class_name) {
    static $loader = null;
    $class_name = strtoupper($load_class_name);
    
    // Try to get this data from index...
    if (isset($GLOBALS[AutoLoader::GLOBAL_VAR]) && 
      isset($GLOBALS[AutoLoader::GLOBAL_VAR][$class_name])) {
      return include $GLOBALS[AutoLoader::GLOBAL_VAR][$class_name];
    } // if
    
    if (!$loader) {
      $loader = new AutoLoader();
      $loader->addDir(ROOT . '/application');
      $loader->addDir(ROOT . '/environment');
      $loader->addDir(ROOT . '/library');
      $loader->setIndexFilename(ROOT . '/cache/autoloader.php');
    } // if
    
    try {
      $loader->loadClass($class_name);
    } catch(Exception $e) {
      die('Caught Exception in AutoLoader: ' . $e->__toString());
    } // try
  } // __autoload
  
  /**
  * ProjectPier shutdown function
  *
  * @param void
  * @return null
  */
  function __shutdown() {
    $logger_session = Logger::getSession();
    if (($logger_session instanceof Logger_Session) && !$logger_session->isEmpty()) {
      Logger::saveSession();
    } // if
  } // __shutdown
  
  /**
  * This function will be used as error handler for production
  *
  * @param integer $code
  * @param string $message
  * @param string $file
  * @param integer $line
  * @return null
  */
  function __production_error_handler($code, $message, $file, $line) {
    // Skip non-static method called staticly type of error...
    if ($code == 2048) {
      return;
    } // if
    
    Logger::log("Error: $message in '$file' on line $line (error code: $code)", Logger::ERROR);
  } // __production_error_handler
  
  /**
  * This function will be used as exception handler in production environment
  *
  * @param Exception $exception
  * @return null
  */
  function __production_exception_handler($exception) {
    Logger::log($exception, Logger::FATAL);
  } // __production_exception_handler

  // ---------------------------------------------------
  //  Get URL
  // ---------------------------------------------------
  
  /**
  * Return an application URL
  * 
  * If $include_project_id variable is present active_project variable will be added to the list of params if we have a 
  * project selected (active_project() function returns valid project instance)
  *
  * @param string $controller_name
  * @param string $action_name
  * @param array $params
  * @param string $anchor
  * @param boolean $include_project_id
  * @return string
  */
  function get_url($controller_name = null, $action_name = null, $params = null, $anchor = null, $include_project_id = true) {
    $controller = trim($controller_name) ? $controller_name : DEFAULT_CONTROLLER;
    $action = trim($action_name) ? $action_name : DEFAULT_ACTION;
    if (!is_array($params) && !is_null($params)) {
      $params = array('id' => $params);
    } // if
    
    $url_params = array('c=' . $controller, 'a=' . $action);
    
    if ($include_project_id) {
      if (function_exists('active_project') && (active_project() instanceof Project)) {
        if (!(is_array($params) && isset($params['active_project']))) {
          $url_params[] = 'active_project=' . active_project()->getId();
        } // if
      } // if
    } // if
    
    if (is_array($params)) {
      foreach ($params as $param_name => $param_value) {
        if (is_bool($param_value)) {
          $url_params[] = $param_name . '=1';
        } else {
          $url_params[] = $param_name . '=' . urlencode($param_value);
        } // if
      } // foreach
    } // if
    
    if (trim($anchor) <> '') {
      $anchor = '#' . $anchor;
    } // if
    
    return with_slash(ROOT_URL) . 'index.php?' . implode('&amp;', $url_params) . $anchor;
  } // get_url
  
  // ---------------------------------------------------
  //  Product
  // ---------------------------------------------------
  
  /**
  * Return product name. This is a wrapper function that abstracts the product name
  *
  * @param void
  * @return string
  */
  function product_name() {
    return PRODUCT_NAME;
  } // product_name
  
  /**
  * Return product version, wrapper function.
  *
  * @param void
  * @return string
  */
  function product_version() {
    // 0.6 is the last version that comes without PRODUCT_VERSION constant that is set up
    return defined('PRODUCT_VERSION') ? PRODUCT_VERSION : '0.8.0';
  } // product_version
  
  /**
  * Returns product signature (name and version). If user is not logged in and
  * is not member of owner company he will see only product name
  *
  * @param void
  * @return string
  */
  function product_signature() {
    if (function_exists('logged_user') && (logged_user() instanceof User) && logged_user()->isMemberOfOwnerCompany()) {
      $result = lang('footer powered', 'http://www.projectpier.org/', clean(product_name()) . ' ' . product_version());
      if (Env::isDebugging()) {
        ob_start();
        benchmark_timer_display(false);
        $result .= '. ' . ob_get_clean();
        if (function_exists('memory_get_usage')) {
          $result .= '. ' . format_filesize(memory_get_usage());
        } // if
      } // if
      return $result;
    } else {
      return lang('footer powered', 'http://www.ProjectPier.org/', clean(product_name()));
    } // if
  } // product_signature
  
  // ---------------------------------------------------
  //  Request, routes replacement methods
  // ---------------------------------------------------
  
  /**
  * Return matched requst controller
  *
  * @access public
  * @param void
  * @return string
  */
  function request_controller() {
    $controller = trim(array_var($_GET, 'c', DEFAULT_CONTROLLER));
    return $controller && is_valid_function_name($controller) ? $controller : DEFAULT_CONTROLLER;
  } // request_controller
  
  /**
  * Return matched request action
  *
  * @access public
  * @param void
  * @return string
  */
  function request_action() {
    $action = trim(array_var($_GET, 'a', DEFAULT_ACTION));
    return $action && is_valid_function_name($action) ? $action : DEFAULT_ACTION;
  } // request_action
  
  // ---------------------------------------------------
  //  Controllers and stuff
  // ---------------------------------------------------

  /**
  * Set internals of specific company website controller
  *
  * @access public
  * @param PageController $controller
  * @param string $layout Project or company website layout. Or any other...
  * @return null
  */
  function prepare_company_website_controller(PageController $controller, $layout = 'dashboard') {
    
    // If we don't have logged user prepare referer params and redirect user to login page
    if (!(logged_user() instanceof User)) {
      $ref_params = array();
      foreach ($_GET as $k => $v) {
        $ref_params['ref_' . $k] = $v;
      }
      $controller->redirectTo('access', 'login', $ref_params);
    } // if
    
    $controller->setLayout($layout);
    $controller->addHelper('form', 'breadcrumbs', 'pageactions', 'tabbednavigation', 'company_website', 'project_website');
  } // prepare_company_website_controller
  
  // ---------------------------------------------------
  //  Company website interface
  // ---------------------------------------------------

  /**
  * Return owner company object if we are on company website and it is loaded
  *
  * @access public
  * @param void
  * @return Company
  */
  function owner_company() {
    return CompanyWebsite::instance()->getCompany();
  } // owner_company
  
  /**
  * Return logged user if we are on company website
  *
  * @access public
  * @param void
  * @return User
  */
  function logged_user() {
    return CompanyWebsite::instance()->getLoggedUser();
  } // logged_user
  
  /**
  * Return active project if we are on company website
  *
  * @access public
  * @param void
  * @return Project
  */
  function active_project() {
    return CompanyWebsite::instance()->getProject();
  } // active_project
  
  // ---------------------------------------------------
  //  Config interface
  // ---------------------------------------------------
  
  /**
  * Return config option value
  *
  * @access public
  * @param string $name Option name
  * @param mixed $default Default value that is returned in case of any error
  * @return mixed
  */
  function config_option($option, $default = null) {
    return ConfigOptions::getOptionValue($option, $default);
  } // config_option
  
  /**
  * Set value of specific configuration option
  *
  * @param string $option_name
  * @param mixed $value
  * @return boolean
  */
  function set_config_option($option_name, $value) {
    $config_option = ConfigOptions::getByName($option_name);
    if (!($config_option instanceof ConfigOption)) {
      return false;
    } // if
    
    $config_option->setValue($value);
    return $config_option->save();
  } // set_config_option
  
  /**
  * This function will return object by the manager class and object ID
  *
  * @param integer $object_id
  * @param string $manager_class
  * @return ApplicationDataObject
  */
  function get_object_by_manager_and_id($object_id, $manager_class) {
    $object_id = (integer) $object_id;
    $manager_class = trim($manager_class);
    
    if (!is_valid_function_name($manager_class) || !class_exists($manager_class, true)) {
      throw new Error("Class '$manager_class' does not exist");
    } // if
    
    $code = "return $manager_class::findById($object_id);";
    $object = eval($code);
    
    return $object instanceof DataObject ? $object : null;
  } // get_object_by_manager_and_id

?>
