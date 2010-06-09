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
    } // index
    
    /**
    * Edit logged user profile
    *
    * @access public
    * @param void
    * @return null
    */
    function edit_profile() {
      $user = Users::findById(get_id());
      if (!($user instanceof User)) {
        flash_error(lang('user dnx'));
        $this->redirectTo('dashboard');
      } // if
      
      $company = $user->getCompany();
      if (!($company instanceof Company)) {
        flash_error(lang('company dnx'));
        $this->redirectToReferer(get_url('administration'));
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
      
      $im_types = ImTypes::findAll(array('order' => '`id`'));
      
      $user_data = array_var($_POST, 'user');
      if (!is_array($user_data)) {
        $user_data = array(
          'username'      => $user->getUsername(),
          'email'         => $user->getEmail(),
          'display_name'  => $user->getDisplayName(),
          'title'         => $user->getTitle(),
          'office_number' => $user->getOfficeNumber(),
          'fax_number'    => $user->getFaxNumber(),
          'mobile_number' => $user->getMobileNumber(),
          'home_number'   => $user->getHomeNumber(),
          'timezone'      => $user->getTimezone(),
          'is_admin'      => $user->getIsAdmin(),
          'auto_assign'   => $user->getAutoAssign(),
          'company_id'    => $user->getCompanyId(),
        ); // array
        
        if (is_array($im_types)) {
          foreach ($im_types as $im_type) {
            $user_data['im_' . $im_type->getId()] = $user->getImValue($im_type);
          } // forech
        } // if
        
        $default_im = $user->getDefaultImType();
        $user_data['default_im'] = $default_im instanceof ImType ? $default_im->getId() : '';
        
      } // if
      
      tpl_assign('user', $user);
      tpl_assign('company', $company);
      tpl_assign('user_data', $user_data);
      tpl_assign('im_types', $im_types);
      
      if (is_array(array_var($_POST, 'user'))) {
        try {
          DB::beginWork();
          
          $user->setFromAttributes($user_data);
          $user->save();
          
          $user->clearImValues();
          
          foreach ($im_types as $im_type) {
            $value = trim(array_var($user_data, 'im_' . $im_type->getId()));
            if ($value <> '') {
              
              $user_im_value = new UserImValue();
              
              $user_im_value->setUserId($user->getId());
              $user_im_value->setImTypeId($im_type->getId());
              $user_im_value->setValue($value);
              $user_im_value->setIsDefault(array_var($user_data, 'default_im') == $im_type->getId());
              
              $user_im_value->save();
            } // if
          } // foreach
          
          DB::commit();
          
          flash_success(lang('success update profile'));
          $this->redirectToUrl($redirect_to);
        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
      } // if
    } // edit_profile
    
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
    
    /**
    * Edit logged user avatar
    *
    * @param void
    * @return null
    */
    function edit_avatar() {
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
        $redirect_to = $user->getUpdateAvatarUrl();
      } // if
      tpl_assign('redirect_to', $redirect_to);
      
      $avatar = array_var($_FILES, 'new_avatar');
      tpl_assign('user', $user);
      
      if (is_array($avatar)) {
        try {
          if (!isset($avatar['name']) || !isset($avatar['type']) || !isset($avatar['size']) || !isset($avatar['tmp_name']) || !is_readable($avatar['tmp_name'])) {
            throw new InvalidUploadError($avatar, lang('error upload file'));
          } // if
          
          $valid_types = array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
          $max_width   = config_option('max_avatar_width', 50);
          $max_height  = config_option('max_avatar_height', 50);
          
          if (!in_array($avatar['type'], $valid_types) || !($image = getimagesize($avatar['tmp_name']))) {
            throw new InvalidUploadError($avatar, lang('invalid upload type', 'JPG, GIF, PNG'));
          } // if
          
          try {
            $old_file = $user->getAvatarPath();
            DB::beginWork();
            
            if (!$user->setAvatar($avatar['tmp_name'], $max_width, $max_height)) {
              DB::rollback();
              flash_error(lang('error edit avatar'));
              $this->redirectToUrl($user->getUpdateAvatarUrl());
            } // if
            
            ApplicationLogs::createLog($user, null, ApplicationLogs::ACTION_EDIT);
            DB::commit();
            
            if (is_file($old_file)) {
              @unlink($old_file);
            } // if
            
            flash_success(lang('success edit avatar'));
          } catch(Exception $e) {
            flash_error(lang('error edit avatar'));
          } // try
          
          $this->redirectToUrl($redirect_to);
        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
      } // if
    } // edit_avatar
    
    /**
    * Delete avatar
    *
    * @param void
    * @return null
    */
    function delete_avatar() {
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
        $redirect_to = $user->getUpdateAvatarUrl();
      } // if
      tpl_assign('redirect_to', $redirect_to);
      
      if (!$user->hasAvatar()) {
        flash_error(lang('avatar dnx'));
        $this->redirectToUrl($redirect_to);
      } // if
      
      try {
        DB::beginWork();
        $user->deleteAvatar();
        $user->save();
        ApplicationLogs::createLog($user, null, ApplicationLogs::ACTION_EDIT);
        
        DB::commit();
        
        flash_success(lang('success delete avatar'));
      } catch(Exception $e) {
        DB::rollback();
        flash_error(lang('error delete avatar'));
      } // try
      
      $this->redirectToUrl($redirect_to);
    } // delete_avatar
  
  } // AccountController

?>
