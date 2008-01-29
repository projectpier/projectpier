<?php

  /**
  * Company class
  * Generated on Sat, 25 Feb 2006 17:37:12 +0100 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class Company extends BaseCompany {
    
    /**
    * Cached array of active company projects
    *
    * @var array
    */
    private $active_projects;
    
    /**
    * Cached array of completed projects
    *
    * @var array
    */
    private $completed_projects;
    
    /**
    * Return array of all company members
    *
    * @access public
    * @param void
    * @return array
    */
    function getUsers() {
      return Users::findAll(array(
        'conditions' => '`company_id` = ' . DB::escape($this->getId())
      )); // findAll
    } // getUsers
    
    /**
    * Return number of company users
    *
    * @access public
    * @param void
    * @return integer
    */
    function countUsers() {
      return Users::count('`company_id` = ' . DB::escape($this->getId()));
    } // countUsers
    
    /**
    * Return array of company users on specific project
    *
    * @access public
    * @param Project $project
    * @return array
    */
    function getUsersOnProject(Project $project) {
      return ProjectUsers::getCompanyUsersByProject($this, $project);
    } // getUsersOnProject
    
    /**
    * Return users that have auto assign value set to true
    *
    * @access public
    * @param void
    * @return array
    */
    function getAutoAssignUsers() {
      return Users::findAll(array(
        'conditions' => '`company_id` = ' . DB::escape($this->getId()) . ' AND `auto_assign` > ' . DB::escape(0)
      )); // findAll
    } // getAutoAssignUsers
    
    /**
    * Return all client companies
    *
    * @access public
    * @param void
    * @return array
    */
    function getClientCompanies() {
      return Companies::getCompanyClients($this);
    } // getClientCompanies
    
    /**
    * Return number of client companies
    *
    * @access public
    * @param void
    * @return integer
    */
    function countClientCompanies() {
      return Companies::count('`client_of_id` = ' . DB::escape($this->getId()));
    } // countClientCompanies
    
    /**
    * Return all projects that this company is member of
    *
    * @access public
    * @param void
    * @return array
    */
    function getProjects() {
      return $this->isOwner() ? Projects::getAll() : ProjectCompanies::getProjectsByCompany($this);
    } // getProjects
    
    /**
    * Return total number of projects
    *
    * @access public
    * @param void
    * @return integer
    */
    function countProjects() {
      if ($this->isOwner()) {
        return Projects::count(); // all
      } else {
        return ProjectCompanies::count('`company_id` = ' . DB::escape($this->getId()));
      } // if
    } // countProjects
    
    /**
    * Return active projects that are owned by this company
    *
    * @param void
    * @return null
    */
    function getActiveProjects() {
      if (is_null($this->active_projects)) {
        if ($this->isOwner()) {
          $this->active_projects = Projects::findAll(array(
            'conditions' => '`completed_on` = ' . DB::escape(EMPTY_DATETIME)
          )); // findAll
        } else {
          $this->active_projects = ProjectCompanies::getProjectsByCompany($this, '`completed_on` = ' . DB::escape(EMPTY_DATETIME));
        } // if
      } // if
      return $this->active_projects;
    } // getActiveProjects
    
    /**
    * Return all completed projects
    *
    * @param void
    * @return null
    */
    function getCompletedProjects() {
      if (is_null($this->completed_projects)) {
        if ($this->isOwner()) {
          $this->completed_projects = Projects::findAll(array(
            'conditions' => '`completed_on` > ' . DB::escape(EMPTY_DATETIME)
          )); // findAll
        } else {
          $this->completed_projects = ProjectCompanies::getProjectsByCompany($this, '`completed_on` > ' . DB::escape(EMPTY_DATETIME));
        } // if
      } // if
      return $this->completed_projects;
    } // getCompletedProjects
    
    /**
    * Return all milestones scheduled for today
    *
    * @param void
    * @return array
    */
    function getTodayMilestones() {
      return ProjectMilestones::getTodayMilestonesByCompany($this);
    } // getTodayMilestones
    
    /**
    * Return all late milestones
    *
    * @param void
    * @return array
    */
    function getLateMilestones() {
      return ProjectMilestones::getLateMilestonesByCompany($this);
    } // getLateMilestones
    
    /**
    * Check if this company is owner company
    *
    * @param void
    * @return boolean
    */
    function isOwner() {
      if ($this->isNew()) {
        return false;
      } else {
        return $this->getClientOfId() == 0;
      } // if
    } // isOwner
    
    /**
    * Check if this company is part of specific project
    *
    * @param Project $project
    * @return boolean
    */
    function isProjectCompany(Project $project) {
      if ($this->isOwner() && ($project->getCompanyId() == $this->getId())) {
        return true;
      } // uf
      return ProjectCompanies::findById(array('project_id' => $project->getId(), 'company_id' => $this->getId())) instanceof ProjectCompany;
    } // isProjectCompany
    
    /**
    * This function will return true if we have data to show company address (address, city, country and zipcode)
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasAddress() {
      return trim($this->getAddress()) <> '' &&
             trim($this->getCity()) <> '' &&
             //trim($this->getZipcode()) <> '' &&
             trim($this->getCountry()) <> '';
    } // hasAddress
    
    /**
    * Check if this company have valid homepage address set
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasHomepage() {
      return trim($this->getHomepage()) <> '' && is_valid_url($this->getHomepage());
    } // hasHomepage
    
    /**
    * Return name of country
    *
    * @access public
    * @param void
    * @return string
    */
    function getCountryName() {
      return lang('country ' . $this->getCountry());
    } // getCountryName
    
    /**
    * Returns true if company info is updated by the user since company is created. Company can be created
    * with empty company info
    *
    * @access public
    * @param void
    * @return boolean
    */
    function isInfoUpdated() {
      return $this->getCreatedOn()->getTimestamp() < $this->getUpdatedOn()->getTimestamp();
    } // isInfoUpdated
    
    /**
    * Set homepage URL
    * 
    * This function is simple setter but it will check if protocol is specified for given URL. If it is not than 
    * http will be used. Supported protocols are http and https for this type or URL
    *
    * @param string $value
    * @return null
    */
    function setHomepage($value) {
      if (trim($value) == '') {
        return parent::setHomepage('');
      } // if
      
      $check_value = strtolower($value);
      if (!str_starts_with($check_value, 'http://') && !str_starts_with($check_value, 'https://')) {
        return parent::setHomepage('http://' . $value);
      } else {
        return parent::setHomepage($value);
      } // if
    } // setHomepage
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
    * Check if specific user can update this company
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canEdit(User $user) {
      return $user->isAccountOwner() || $user->isAdministrator();
    } // canEdit
    
    /**
    * Check if specific user can delete this company
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canDelete(User $user) {
      return $user->isAccountOwner() || $user->isAdministrator();
    } // canDelete
    
    /**
    * Returns true if specific user can add clent company
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canAddClient(User $user) {
      if (!$user->isMemberOf($this)) {
        return false;
      }
      return $user->isAccountOwner() || $user->isAdministrator($this);
    } // canAddClient
    
    /**
    * Check if this user can add new account to this company
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canAddUser(User $user) {
      return User::canAdd($user, $this);
    } // canAddUser
    
    /**
    * Check if user can update permissions of this company
    *
    * @param User $user
    * @return boolean
    */
    function canUpdatePermissions(User $user) {
      if ($this->isOwner()) {
        return false; // owner company!
      } // if
      return $user->isAdministrator();
    } // canUpdatePermissions
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
    * Show company card page
    *
    * @access public
    * @param void
    * @return null
    */
    function getCardUrl() {
      return get_url('company', 'card', $this->getId());
    } // getCardUrl
    
    /**
    * Return view company URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getViewUrl() {
      if ($this->getId() == owner_company()->getId()) {
        return get_url('administration', 'company');
      } else {
        return get_url('company', 'view_client', $this->getId());
      } // if
    } // getViewUrl
    
    /**
    * Edit owner company
    *
    * @access public
    * @param void
    * @return null
    */
    function getEditUrl() {
      return $this->isOwner() ? get_url('company', 'edit') : get_url('company', 'edit_client', $this->getId());
    } // getEditUrl
    
    /**
    * Return delete company URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getDeleteClientUrl() {
      return get_url('company', 'delete_client', $this->getId());
    } // getDeleteClientUrl
    
    /**
    * Return update permissions URL
    *
    * @param void
    * @return string
    */
    function getUpdatePermissionsUrl() {
      return get_url('company', 'update_permissions', $this->getId());
    } // getUpdatePermissionsUrl
    
    /**
    * Return add user URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getAddUserUrl() {
      return get_url('user', 'add', array('company_id' => $this->getId()));
    } // getAddUserUrl
    
    /**
    * Return update avatar URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getEditLogoUrl() {
      return get_url('company', 'edit_logo', $this->getId());
    } // getEditLogoUrl
    
    /**
    * Return delete logo URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getDeleteLogoUrl() {
      return get_url('company', 'delete_logo', $this->getId());
    } // getDeleteLogoUrl
    
    // ---------------------------------------------------
    //  Logo
    // ---------------------------------------------------
    
    /**
    * Set logo value
    *
    * @param string $source Source file
    * @param integer $max_width
    * @param integer $max_height
    * @param boolean $save Save object when done
    * @return null
    */
    function setLogo($source, $max_width = 50, $max_height = 50, $save = true) {
      if (!is_readable($source)) {
        return false;
      }
      
      do {
        $temp_file = ROOT . '/cache/' . sha1(uniqid(rand(), true));
      } while (is_file($temp_file));
      
      try {
        Env::useLibrary('simplegd');
        
        $image = new SimpleGdImage($source);
        $thumb = $image->scale($max_width, $max_height, SimpleGdImage::BOUNDARY_DECREASE_ONLY, false);
        $thumb->saveAs($temp_file, IMAGETYPE_PNG);
        
        $public_filename = PublicFiles::addFile($temp_file, 'png');
        if ($public_filename) {
          $this->setLogoFile($public_filename);
          if ($save) {
            $this->save();
          } // if
        } // if
        
        $result = true;
      } catch(Exception $e) {
        $result = false;
      } // try
      
      // Cleanup
      if (!$result && $public_filename) {
        PublicFiles::deleteFile($public_filename);
      } // if
      @unlink($temp_file);
      
      return $result;
    } // setLogo
    
    /**
    * Delete logo
    *
    * @param void
    * @return null
    */
    function deleteLogo() {
      if ($this->hasLogo()) {
        PublicFiles::deleteFile($this->getLogoFile());
        $this->setLogoFile('');
      } // if
    } // deleteLogo
    
    /**
    * Returns path of company logo. This function will not check if file really exists
    *
    * @access public
    * @param void
    * @return string
    */
    function getLogoPath() {
      return PublicFiles::getFilePath($this->getLogoFile());
    } // getLogoPath
    
    /**
    * description
    *
    * @access public
    * @param void
    * @return string
    */
    function getLogoUrl() {
      return $this->hasLogo() ? PublicFiles::getFileUrl($this->getLogoFile()) : get_image_url('logo.gif');
    } // getLogoUrl
    
    /**
    * Returns true if this company have logo file value and logo file exists
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasLogo() {
      return trim($this->getLogoFile()) && is_file($this->getLogoPath());
    } // hasLogo
    
    // ---------------------------------------------------
    //  System functions
    // ---------------------------------------------------
  
    /**
    * Validate this object before save
    *
    * @param array $errors
    * @return boolean
    */
    function validate(&$errors) {
      if (!$this->validatePresenceOf('name')) {
        $errors[] = lang('company name required');
      } // if
      
      if ($this->validatePresenceOf('email')) {
        if (!is_valid_email($this->getEmail())) {
          $errors[] = lang('invalid email address');
        } // if
      } // if
      
      if ($this->validatePresenceOf('homepage')) {
        if (!is_valid_url($this->getHomepage())) {
          $errors[] = lang('company homepage invalid');
        } // if
      } // if
    } // validate
    
    /**
    * Delete this company and all related data
    *
    * @access public
    * @param void
    * @return boolean
    * @throws Error
    */
    function delete() {
      if ($this->isOwner()) {
        throw new Error(lang('error delete owner company'));
      } // if
      
      $users = $this->getUsers();
      if (is_array($users) && count($users)) {
        foreach ($users as $user) {
          $user->delete();
        }
      } // if
      
      ProjectCompanies::clearByCompany($this);
      
      $this->deleteLogo();
      return parent::delete();
    } // delete
    
    // ---------------------------------------------------
    //  ApplicationDataObject implementation
    // ---------------------------------------------------
    
    /**
    * Return object URl
    *
    * @access public
    * @param void
    * @return string
    */
    function getObjectUrl() {
      return logged_user()->isAdministrator() ? $this->getViewUrl() : $this->getCardUrl();
    } // getObjectUrl
    
    /**
    * Return object type name
    *
    * @param void
    * @return string
    */
    function getObjectTypeName() {
      return lang('company');
    } // getObjectTypeName
    
  } // Company 

?>
