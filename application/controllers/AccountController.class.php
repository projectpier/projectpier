<?php

  /**
  * User account controller with all the parts related to it (profile update, private messages etc)
  *
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class AccountController extends ApplicationController {
  
    /**
    * Construct the AccountController
    *
    * @access public
    * @param void
    * @return AccountController
    */
    function __construct() {
      parent::__construct();
      prepare_company_website_controller($this, 'account');
    } // __construct
    
    /**
    * Show account index page
    *
    * @access public
    * @param void
    * @return null
    */
    function index() {
      tpl_assign('user', logged_user());
      tpl_assign('contact', logged_user()->getContact());
    } // index
    
    /**
    * Edit logged user password
    *
    * @access public
    * @param void
    * @return null
    */
    function edit_password() {
      $user = Users::findById(get_id());
      if (!($user instanceof User)) {
        flash_error(lang('user dnx'));
        $this->redirectTo('dashboard');
      } // if
      
      if (!$user->canUpdateProfile(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if
      
      $redirect_to = array_var($_GET, 'redirect_to');
      if ((trim($redirect_to)) == '' || !is_valid_url($redirect_to)) {
        $redirect_to = $user->getCardUrl();
      } // if
      tpl_assign('redirect_to', $redirect_to);
      
      $password_data = array_var($_POST, 'password');
      tpl_assign('user', $user);
      
      if (is_array($password_data)) {
        $old_password = array_var($password_data, 'old_password');
        $new_password = array_var($password_data, 'new_password');
        $new_password_again = array_var($password_data, 'new_password_again');
        
        try {
          if (!logged_user()->isAdministrator()) {
            if (trim($old_password) == '') {
              throw new Error(lang('old password required'));
            } // if
            if (!$user->isValidPassword($old_password)) {
              throw new Error(lang('invalid old password'));
            } // if
          } // if
          
          if (trim($new_password) == '') {
              throw new Error(lang('password value required'));
            } // if
          if ($new_password <> $new_password_again) {
            throw new Error(lang('passwords dont match'));
          } // if
          
          $user->setPassword($new_password);
          $user->save();
          
          ApplicationLogs::createLog($user, null, ApplicationLogs::ACTION_EDIT);
          flash_success(lang('success edit user', $user->getUsername()));
          $this->redirectToUrl($redirect_to);
          
        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
      } // if
    } // edit_password
    
    /**
    * Show update permissions page
    *
    * @param void
    * @return null
    */
    function update_permissions() {
      $user = Users::findById(get_id());
      if (!($user instanceof User)) {
        flash_error(lang('user dnx'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      if (!$user->canUpdatePermissions(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      $contact = $user->getContact();
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('dashboard');
      } // if
      
      $company = $contact->getCompany();
      if (!($company instanceof Company)) {
        flash_error(lang('company dnx'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      $projects = $company->getProjects();
      if (!is_array($projects) || !count($projects)) {
        flash_error(lang('no projects owned by company'));
        $this->redirectToReferer($company->getViewUrl());
      } // if
      
      $permissions = ProjectUsers::getNameTextArray();
      
      $redirect_to = array_var($_GET, 'redirect_to');
      if ((trim($redirect_to)) == '' || !is_valid_url($redirect_to)) {
        $redirect_to = $company->getViewUrl();
      } // if
      
      tpl_assign('user', $user);
      tpl_assign('contact', $contact);
      tpl_assign('company', $company);
      tpl_assign('projects', $projects);
      tpl_assign('permissions', $permissions);
      tpl_assign('redirect_to', $redirect_to);
      
      if (array_var($_POST, 'submitted') == 'submitted') {
        DB::beginWork();
        foreach ($projects as $project) {
          $relation = ProjectUsers::findById(array(
            'project_id' => $project->getId(),
            'user_id' => $user->getId(),
          )); // findById
          
          if (array_var($_POST, 'project_permissions_' . $project->getId()) == 'checked') {
            if (!($relation instanceof ProjectUser)) {
              $relation = new ProjectUser();
              $relation->setProjectId($project->getId());
              $relation->setUserId($user->getId());
            } // if
            
            foreach ($permissions as $permission => $permission_text) {
              $permission_value = array_var($_POST, 'project_permission_' . $project->getId() . '_' . $permission) == 'checked';
              
              $setter = 'set' . Inflector::camelize($permission);
              $relation->$setter($permission_value);
            } // foreach
            
            $relation->save();
          } else {
            if ($relation instanceof ProjectUser) {
              $relation->delete();
            } // if
          } // if
        } // if
        DB::commit();
        
        flash_success(lang('success user permissions updated'));
        $this->redirectToUrl($redirect_to);
      } // if
    } // update_permissions
  } // AccountController

?>
