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


      // TODO manage change of avatar
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
          tpl_assign('error', new Error(lang('invalid login data')));
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
        flash_error(lang('error delete client'));
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
  
  } // ContactController

?>
