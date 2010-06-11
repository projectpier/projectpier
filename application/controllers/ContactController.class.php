<?php

  /**
  * Contact controller
  *
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class ContactController extends ApplicationController {
  
    /**
    * Construct the ContactController
    *
    * @access public
    * @param void
    * @return ContactController
    */
    function __construct() {
      parent::__construct();
      prepare_company_website_controller($this, 'administration');
    } // __construct
    
    /**
    * Contact management index
    *
    * @access public
    * @param void
    * @return null
    */
    function index() {
      
    } // index
    
    /**
    * Add contact
    *
    * @access public
    * @param void
    * @return null
    */
    function add() {
      $this->setTemplate('add_contact');
      
      $company = Companies::findById(get_id('company_id'));
      if (!($company instanceof Company)) {
        flash_error(lang('company dnx'));
        $this->redirectTo('administration');
      } // if
      
      if (!Contact::canAdd(logged_user(), $company)) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      $contact = new Contact();
      
      $contact_data = array_var($_POST, 'contact');
      if (!is_array($contact_data)) {
        $contact_data = array(
          'company_id' => $company->getId(),
        ); // array
      } // if
      
      tpl_assign('contact', $contact);
      tpl_assign('company', $company);
      tpl_assign('contact_data', $contact_data);

      $avatar = array_var($_FILES, 'new_avatar');
      if (is_array($avatar)) {
        try {
          if (!isset($avatar['name']) || !isset($avatar['type']) || !isset($avatar['size']) || !isset($avatar['tmp_name']) || !is_readable($avatar['tmp_name'])) {
            throw new InvalidUploadError($avatar, lang('error upload file'));
          } // if
          

          $valid_types = array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
          $max_width   = config_option('max_avatar_width', 50);
          $max_height  = config_option('max_avatar_height', 50);
          
          if ($avatar['size']) {
            if (!in_array($avatar['type'], $valid_types) || !($image = getimagesize($avatar['tmp_name']))) {
              throw new InvalidUploadError($avatar, lang('invalid upload type', 'JPG, GIF, PNG'));
            } elseif (!$contact->setAvatar($avatar['tmp_name'], $max_width, $max_height, false)) {
              throw new Error($avatar, lang('error edit avatar'));
              $contact->setAvatarFile('');
            } // if
          } // if
        } catch (Exception $e) {
          flash_error($e->getMessage());
        }
      } else {
        $contact->setAvatarFile('');
      } // if

      if (is_array(array_var($_POST, 'contact'))) {
        $contact->setFromAttributes($contact_data);
        $contact->setCompanyId($company->getId());

        try {          
          DB::beginWork();
          $contact->save();
          ApplicationLogs::createLog($contact, null, ApplicationLogs::ACTION_ADD);
          DB::commit();
          
          flash_success(lang('success add contact', $contact->getDisplayName()));
          $this->redirectToUrl($company->getViewUrl()); // Translate to profile page
          
        } catch (Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
        
      } // if
      
    } // add
    
    /**
    *
    * @access public
    * @param void
    * @return null
    */
    function edit() {
      $this->setTemplate('add_contact');
      
      $contact = Contacts::findById(get_id());
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('dashboard');
      } // if
      
      if (!$contact->canEdit(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if
      
      $contact_data = array_var($_POST, 'contact');
      $company = $contact->getCompany();
      if (!is_array($contact_data)) {
        $contact_data = array(
          'display_name' => $contact->getDisplayName(),
          'company_id' => $contact->getCompanyId(),
          'title' => $contact->getTitle(),
          'email' => $contact->getEmail(),
          'office_number' => $contact->getOfficeNumber(),
          'fax_number' => $contact->getFaxNumber(),
          'mobile_number' => $contact->getMobileNumber(),
          'home_number' => $contact->getHomeNumber()
        ); // array
      } // if
      
      tpl_assign('contact', $contact);
      tpl_assign('company', $company);
      tpl_assign('contact_data', $contact_data);

      
      $avatar = array_var($_FILES, 'new_avatar');
      if (is_array($avatar) && isset($avatar['size']) && $avatar['size'] != 0) {
        try {
          $old_file = $contact->getAvatarPath();
          if (!isset($avatar['name']) || !isset($avatar['type']) || !isset($avatar['size']) || !isset($avatar['tmp_name']) || !is_readable($avatar['tmp_name'])) {
            throw new InvalidUploadError($avatar, lang('error upload file'));
          } // if
          

          $valid_types = array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
          $max_width   = config_option('max_avatar_width', 50);
          $max_height  = config_option('max_avatar_height', 50);
          
          if ($avatar['size']) {
            if (!in_array($avatar['type'], $valid_types) || !($image = getimagesize($avatar['tmp_name']))) {
              throw new InvalidUploadError($avatar, lang('invalid upload type', 'JPG, GIF, PNG'));
            } elseif (!$contact->setAvatar($avatar['tmp_name'], $max_width, $max_height, false)) {
              throw new Error($avatar, lang('error edit avatar'));
              $contact->setAvatarFile('');
            } // if
            if (is_file($old_file)) {
              @unlink($old_file);
            } // if
          } // if
        } catch (Exception $e) {
          flash_error($e->getMessage());
        }
      } else if ($contact_data['delete_avatar'] == "checked") {
        $old_file = $contact->getAvatarPath();
        if (is_file($old_file)) {
          @unlink($old_file);
        } // if
        $contact->setAvatarFile('');
      } // if

      if (is_array(array_var($_POST, 'contact'))) {
        try {          
          DB::beginWork();
          
          $contact->setFromAttributes($contact_data);
          $contact->save();
          
          ApplicationLogs::createLog($contact, null, ApplicationLogs::ACTION_ADD);
          DB::commit();
          
          
          flash_success(lang('success edit contact', $contact->getDisplayName()));
          $this->redirectToUrl($contact->getCompany()->getViewUrl()); // Translate to profile page
          
        } catch (Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
        
      } // if
      
    } // edit
    
    
    /**
    * Delete specific contact
    *
    * @access public
    * @param void
    * @return null
    */
    function delete() {
      $this->setTemplate('del_contact');

      $contact = Contacts::findById(get_id());
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('administration');
      } // if
      
      if (!$contact->canDelete(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      $delete_data = array_var($_POST, 'deleteContact');
      tpl_assign('contact', $contact);
      tpl_assign('delete_data', $delete_data);

      if (!is_array($delete_data)) {
        $delete_data = array(
          'really' => 0,
          'password' => '',
        ); // array
        tpl_assign('delete_data', $delete_data);
      } else if ($delete_data['really'] == 1) {
        $password = $delete_data['password'];
        if (trim($password) == '') {
          tpl_assign('error', new Error(lang('password value missing')));
          return $this->render();
        }
        if (!logged_user()->isValidPassword($password)) {
          tpl_assign('error', new Error(lang('invalid password')));
          return $this->render();
        }
        try {

          DB::beginWork();
          $contact->delete();
          ApplicationLogs::createLog($contact, null, ApplicationLogs::ACTION_DELETE);
          DB::commit();

          flash_success(lang('success delete contact', $contact->getDisplayName()));

        } catch (Exception $e) {
          DB::rollback();
          flash_error(lang('error delete contact'));
        } // try

        $this->redirectToUrl($contact->getCompany()->getViewUrl());
      } else {
        flash_error(lang('error delete contact'));
        $this->redirectToUrl($contact->getCompany()->getViewUrl());
      }

    } // delete
    
    /**
    * Show contact card
    *
    * @access public
    * @param void
    * @return null
    */
    function card() {
      $this->setLayout('dashboard');
      
      $contact = Contacts::findById(get_id());
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectToReferer(get_url('dashboard', 'contacts'));
      } // if
      
      if (!logged_user()->canSeeContact($contact)) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('dashboard', 'contacts'));
      } // if
      
      tpl_assign('contact', $contact);
    } // card
  
    /**
    * Create and attach a user account to the contact
    * 
    * @access public
    * @param void
    * @return null
    */
    function add_user_account() {
      $this->setTemplate('add_user_to_contact');
      
      $contact = Contacts::findById(get_id());
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('dashboard');
      } // if
      
      if (!$contact->canAddUserAccount(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if
      
      if ($contact->hasUserAccount()) {
        flash_error(lang('contact already has user'));
        $this->redirectToUrl($contact->getCardUrl());
      }
      
      $user = new User();
      $company = $contact->getCompany();
            
      $user_data = array_var($_POST, 'user');
      if (!is_array($user_data)) {
        $user_data = array(
          'password_generator' => 'random',
          'company_id' => $company->getId(),
          'timezone' => $company->getTimezone(),
        ); // array
      } // if
      
      $projects = $company->getProjects();
      $permissions = ProjectUsers::getNameTextArray();
      
      tpl_assign('contact', $contact);
      tpl_assign('user', $user);
      tpl_assign('company', $company);
      tpl_assign('projects', $projects);
      tpl_assign('permissions', $permissions);
      tpl_assign('user_data', $user_data);
      
      if (is_array(array_var($_POST, 'user'))) {
        $user->setFromAttributes($user_data);
        // $user->setCompanyId($company->getId());
        
        try {
          // Generate random password
          if (array_var($user_data, 'password_generator') == 'random') {
            $password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
            
          // Validate user input
          } else {
            $password = array_var($user_data, 'password');
            if (trim($password) == '') {
              throw new Error(lang('password value required'));
            } // if
            if ($password <> array_var($user_data, 'password_a')) {
              throw new Error(lang('passwords dont match'));
            } // if
          } // if
          $user->setPassword($password);
          
          DB::beginWork();
          $user->save();
          $contact->setUserId($user->getId());
          $contact->save();
          ApplicationLogs::createLog($user, null, ApplicationLogs::ACTION_ADD);
          
          if (is_array($projects)) {
            foreach ($projects as $project) {
              if (array_var($user_data, 'project_permissions_' . $project->getId()) == 'checked') {
                $relation = new ProjectUser();
                $relation->setProjectId($project->getId());
                $relation->setUserId($user->getId());
                
                foreach ($permissions as $permission => $permission_text) {
                  $permission_value = array_var($user_data, 'project_permission_' . $project->getId() . '_' . $permission) == 'checked';
                  
                  $setter = 'set' . Inflector::camelize($permission);
                  $relation->$setter($permission_value);
                } // foreach
                
                $relation->save();
              } // if
            } // forech
          } // if
          
          DB::commit();
          
          // Send notification...
          try {
            if (array_var($user_data, 'send_email_notification')) {
              Notifier::newUserAccount($user, $password);
            } // if
          } catch(Exception $e) {
          
          } // try
          
          flash_success(lang('success add user', $user->getDisplayName()));
          $this->redirectToUrl($company->getViewUrl()); // Translate to profile page
          
        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
        
      } // if
      
    } // add_user_account
    
    /**
    * Edit the contact's user account
    * 
    * @access public
    * @param void
    * @return null
    */
    function edit_user_account() {
      $this->setTemplate('add_user_to_contact');
      
      $contact = Contacts::findById(get_id());
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('dashboard');
      } // if
      
      if (!$contact->canEditUserAccount(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if
      
      if (!$contact->hasUserAccount()) {
        flash_error(lang('user dnx'));
        $this->redirectToUrl($contact->getCompany()->getViewUrl());
      }
      
      $user = $contact->getUserAccount();
      $company = $contact->getCompany();
            
      $user_data = array_var($_POST, 'user');
      if (!is_array($user_data)) {
        $user_data = array(
          'username' => $user->getUsername(),
          'email' => $user->getEmail(),
          'timezone' => $user->getTimezone(),
          'is_admin' => $user->isAdministrator(),
          'auto_assign' => $user->getAutoAssign()
        ); // array
      } // if
      
      tpl_assign('contact', $contact);
      tpl_assign('user', $user);
      tpl_assign('company', $company);
      tpl_assign('user_data', $user_data);
      
      if (is_array(array_var($_POST, 'user'))) {
        $user->setFromAttributes($user_data);
        // $user->setCompanyId($company->getId());
        
        try {
          // Generate random password
          if (array_var($user_data, 'password_generator') == 'random') {
            $password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
            $user->setPassword($password);
            
          // Validate user input
          } else if (array_var($user_data, 'password_generator') == 'specify') {
            $password = array_var($user_data, 'password');
            if (trim($password) == '') {
              throw new Error(lang('password value required'));
            } // if
            if ($password <> array_var($user_data, 'password_a')) {
              throw new Error(lang('passwords dont match'));
            } // if
            $user->setPassword($password);
          } // if
          
          DB::beginWork();
          $user->save();
          ApplicationLogs::createLog($user, null, ApplicationLogs::ACTION_EDIT);
          
          DB::commit();
          
          // Send notification...
          try {
            if (array_var($user_data, 'send_email_notification')) {
              Notifier::updatedUserAccount($user, $password);
            } // if
          } catch(Exception $e) {
          
          } // try
          
          flash_success(lang('success edit user', $user->getDisplayName()));
          $this->redirectToUrl($company->getViewUrl()); // Translate to profile page
          
        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
        
      } // if
      
    } // edit_user_account
    
    /**
    * Delete the user account associated with that contact
    *
    * @param void
    * @return null
    */
    function delete_user_account() {
      $this->setTemplate('del_user_account');

      $contact = Contacts::findById(get_id());
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('administration');
      } // if
      
      $user = $contact->getUserAccount();
      if (!($user instanceof User)) {
        flash_error(lang('user dnx'));
        $this->redirectTo('administration');
      } // if
      
      if (!$contact->canDeleteUserAccount(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      $company = $contact->getCompany();
      
      $delete_data = array_var($_POST, 'deleteUser');
      tpl_assign('contact', $contact);
      tpl_assign('company', $company);
      tpl_assign('user', $user);
      tpl_assign('delete_data', $delete_data);

      if (!is_array($delete_data)) {
        $delete_data = array(
          'really' => 0,
          'password' => '',
        ); // array
        tpl_assign('delete_data', $delete_data);
      } else if ($delete_data['really'] == 1) {
        $password = $delete_data['password'];
        if (trim($password) == '') {
          tpl_assign('error', new Error(lang('password value missing')));
          return $this->render();
        }
        if (!logged_user()->isValidPassword($password)) {
          tpl_assign('error', new Error(lang('invalid password')));
          return $this->render();
        }
        try {

          DB::beginWork();
          $user->delete();
          $contact->setUserId('0');
          $contact->save();
          ApplicationLogs::createLog($user, null, ApplicationLogs::ACTION_DELETE);
          DB::commit();

          flash_success(lang('success delete user', $user->getDisplayName()));

        } catch (Exception $e) {
          DB::rollback();
          flash_error(lang('error delete user'));
        } // try

        $this->redirectToUrl($company->getViewUrl());
      } else {
        flash_error(lang('error delete user'));
        $this->redirectToUrl($company->getViewUrl());
      }

    } // delete_user_account
    
  } // ContactController

?>
