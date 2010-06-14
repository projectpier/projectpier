<?php

  /**
  * Contact class
  * Generated on Sat, 25 Feb 2006 17:37:12 +0100 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class Contact extends BaseContact {
    
    /**
    * Cached associated user
    *
    * @var User
    */
    private $user = null;
    
    /**
    * True if user is member of owner company. This value is read on first request and cached
    *
    * @var boolean
    */
    private $is_member_of_owner_company = null;
    
    /**
    * Cached is_administrator value. First time value is requested it will be checked and cached. 
    * After that every request will return cached value
    *
    * @var boolean
    */
    private $is_administrator = null;
    
    /**
    * Cached is_account_owner value. Value is retrieved on first requests
    *
    * @var boolean
    */
    private $is_account_owner = null;
    
    /**
    * Check if this contact is member of specific company
    *
    * @access public
    * @param Company $company
    * @return boolean
    */
    function isMemberOf(Company $company) {
      return $this->getCompanyId() == $company->getId();
    } // isMemberOf
    
    /**
    * Usually we check if user is member of owner company so this is the shortcut method
    *
    * @param void
    * @return boolean
    */
    function isMemberOfOwnerCompany() {
      if (is_null($this->is_member_of_owner_company)) {
        $this->is_member_of_owner_company = $this->isMemberOf(owner_company());
      }
      return $this->is_member_of_owner_company;
    } // isMemberOfOwnerCompany
    
    /**
    * Check if this user is part of specific project
    *
    * @param Project $project
    * @return boolean
    */
    function isProjectUser(Project $project) {
      if (!isset($this->is_project_user_cache[$project->getId()])) {
        $project_user = ProjectUsers::findById(array(
          'project_id' => $project->getId(), 
          'user_id' => $this->getId())
        ); // findById
        $this->is_project_user_cache[$project->getId()] = $project_user instanceof ProjectUser;
      } // if
      return $this->is_project_user_cache[$project->getId()];
    } // isProjectUser
    
    /**
    * Check if this of specific company website. If must be member of that company and is_admin flag set to true
    *
    * @param void
    * @return boolean
    */
    function isAdministrator() {
      if ($this->getUserAccount()) {
        return $this->getUserAccount()->isAdministrator();
      }
      return false;
    } // isAdministrator
    
    /**
    * Account owner is user account that was created when company website is created
    *
    * @param void
    * @return boolean
    */
    function isAccountOwner() {
      if ($this->getUserAccount()) {
        return $this->getUserAccount()->isAccountOwner();
      }
      return false;
    } // isAccountOwner
    
    /**
    * Returns if contact is a favorite
    *
    * @param void
    * @return boolean
    */
    function isFavorite() {
      return $this->getIsFavorite();
    } // isFavorite
    
    // /**
    // * Check if this user have specific project permission. $permission is the name of table field that holds the value
    // *
    // * @param Project $project
    // * @param string $permission Name of the field where the permission value is stored. There are set of constants
    // *   in ProjectUser that hold field names (ProjectUser::CAN_MANAGE_MESSAGES ...)
    // * @return boolean
    // */
    // function hasProjectPermission(Project $project, $permission, $use_cache = true) {
    //   if ($use_cache) {
    //     if (isset($this->project_permissions_cache[$project->getId()]) && isset($this->project_permissions_cache[$project->getId()][$permission])) {
    //       return $this->project_permissions_cache[$project->getId()][$permission];
    //     } // if
    //   } // if
    //   
    //   $project_user = ProjectUsers::findById(array('project_id' => $project->getId(), 'user_id' => $this->getId()));
    //   if (!($project_user instanceof ProjectUser)) {
    //     if ($use_cache) {
    //       $this->project_permissions_cache[$project->getId()][$permission] = false;
    //     } // if
    //     return false;
    //   } // if
    //   
    //   $getter_method = 'get' . Inflector::camelize($permission);
    //   $project_user_methods = get_class_methods('ProjectUser');
    //   
    //   $value = in_array($getter_method, $project_user_methods) ? $project_user->$getter_method() : false;
    //   
    //   if ($use_cache) {
    //     $this->project_permissions_cache[$project->getId()][$permission] = $value;
    //   }
    //   return $value;
    // } // hasProjectPermission
    
    // /**
    // * This function will check if this user have all project permissions
    // *
    // * @param Project $project
    // * @param boolean $use_cache
    // * @return boolean
    // */
    // function hasAllProjectPermissions(Project $project, $use_cache = true) {
    //   $permissions = ProjectUsers::getPermissionColumns();
    //   if (is_array($permissions)) {
    //     foreach ($permissions as $permission) {
    //       if (!$this->hasProjectPermission($project, $permission, $use_cache)) {
    //         return false;
    //       }
    //     } // foreach
    //   } // if
    //   return true;
    // } // hasAllProjectPermissions
    
    // ---------------------------------------------------
    //  Retrieve
    // ---------------------------------------------------
    
    /**
    * Return owner company
    *
    * @access public
    * @param void
    * @return Company
    */
    function getCompany() {
      return Companies::findById($this->getCompanyId());
    } // getCompany
    
    /**
    * Return associated user account
    * 
    * @param void
    * @return User
    */
    function getUserAccount() {
      if (is_null($this->user)) {
        $this->user = Users::findById($this->getUserId());
      } // if
      return $this->user;
    } // getUser

    /**
    * True if contact has an associated user account
    *
    * @param void
    * @return boolean
    */
    function hasUserAccount() {
      return ($this->getUserAccount() ? true : false);
    } // hasUserAccount
    
    // /**
    // * Return all projects that this user is member of
    // *
    // * @access public
    // * @param void
    // * @return array
    // */
    // function getProjects() {
    //   if (is_null($this->projects)) {
    //     $this->projects = ProjectUsers::getProjectsByUser($this);
    //   } // if
    //   return $this->projects;
    // } // getProjects
    
    // /**
    // * Return array of active projects that this user have access
    // *
    // * @access public
    // * @param void
    // * @return array
    // */
    // function getActiveProjects() {
    //   if (is_null($this->active_projects)) {
    //     $this->active_projects = ProjectUsers::getProjectsByUser($this, '`completed_on` = ' . DB::escape(EMPTY_DATETIME));
    //   } // if
    //   return $this->active_projects;
    // } // getActiveProjects
    
    // /**
    // * Return array of finished projects
    // *
    // * @access public
    // * @param void
    // * @return array
    // */
    // function getFinishedProjects() {
    //   if (is_null($this->finished_projects)) {
    //     $this->finished_projects = ProjectUsers::getProjectsByUser($this, '`completed_on` > ' . DB::escape(EMPTY_DATETIME));
    //   } // if
    //   return $this->finished_projects;
    // } // getFinishedProjects
    
    // /**
    // * Return all active milestones assigned to this user
    // *
    // * @param void
    // * @return array
    // */
    // function getActiveMilestones() {
    //   if (is_null($this->all_active_milestons)) {
    //     $this->all_active_milestons = ProjectMilestones::getActiveMilestonesByUser($this);
    //   } // if
    //   return $this->all_active_milestons;
    // } // getActiveMilestones
    
    // /**
    // * Return late milestones that this user have access to
    // *
    // * @access public
    // * @param void
    // * @return array
    // */
    // function getLateMilestones() {
    //   if (is_null($this->late_milestones)) {
    //     $this->late_milestones = ProjectMilestones::getLateMilestonesByUser($this);
    //   } // if
    //   return $this->late_milestones;
    // } // getLateMilestones
    // 
    // /**
    // * Return today milestones that this user have access to
    // *
    // * @access public
    // * @param void
    // * @return array
    // */
    // function getTodayMilestones() {
    //   if (is_null($this->today_milestones)) {
    //     $this->today_milestones = ProjectMilestones::getTodayMilestonesByUser($this);
    //   } // if
    //   return $this->today_milestones;
    // } // getTodayMilestones
    
    /**
    * Return display name for this account. If there is no display name associated username will be used
    *
    * @access public
    * @param void
    * @return string
    */
    function getDisplayName() {
      $display = parent::getDisplayName();
      return trim($display) == '' ? $this->getUserAccount()->getUsername() : $display;
    } // getDisplayName
    
    /**
    * Returns true if we have title value set
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasTitle() {
      return trim($this->getTitle()) <> '';
    } // hasTitle
    
    // ---------------------------------------------------
    //  IMs
    // ---------------------------------------------------
    
    /**
    * Return true if this contact have at least one IM address
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasImValue() {
      return ContactImValues::count('`contact_id` = ' . DB::escape($this->getId()));
    } // hasImValue
    
    /**
    * Return all IM values
    *
    * @access public
    * @param void
    * @return array
    */
    function getImValues() {
      return ContactImValues::getByContact($this);
    } // getImValues
    
    /**
    * Return value of specific IM. This function will return null if IM is not found
    *
    * @access public
    * @param ImType $im_type
    * @return string
    */
    function getImValue(ImType $im_type) {
      $im_value = ContactImValues::findById(array('contact_id' => $this->getId(), 'im_type_id' => $im_type->getId()));
      return $im_value instanceof ContactImValue && (trim($im_value->getValue()) <> '') ? $im_value->getValue() : null;
    } // getImValue
    
    /**
    * Return default IM value. If value was not found NULL is returned
    *
    * @access public
    * @param void
    * @return string
    */
    function getDefaultImValue() {
      $default_im_type = $this->getDefaultImType();
      return $this->getImValue($default_im_type);
    } // getDefaultImValue
    
    /**
    * Return default contact IM type. If there is no default contact IM type NULL is returned
    *
    * @access public
    * @param void
    * @return ImType
    */
    function getDefaultImType() {
      return ContactImValues::getDefaultContactImType($this);
    } // getDefaultImType
    
    /**
    * Clear all IM values
    *
    * @access public
    * @param void
    * @return boolean
    */
    function clearImValues() {
      return ContactImValues::instance()->clearByContact($this);
    } // clearImValues
    
    // ---------------------------------------------------
    //  Avatars
    // ---------------------------------------------------
    
    /**
    * Set contact avatar from $source file
    *
    * @param string $source Source file
    * @param integer $max_width Max avatar width
    * @param integer $max_height Max avatar height
    * @param boolean $save Save contact object when done
    * @return string
    */
    function setAvatar($source, $max_width = 50, $max_height = 50, $save = true) {
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
          $this->setAvatarFile($public_filename);
          if ($save) {
            $this->save();
          } // if
        } // if
        
        $result = true;
      } catch (Exception $e) {
        $result = false;
      } // try
      
      // Cleanup
      if (!$result && $public_filename) {
        PublicFiles::deleteFile($public_filename);
      } // if
      @unlink($temp_file);
      
      return $result;
    } // setAvatar
    
    /**
    * Delete avatar
    *
    * @param void
    * @return null
    */
    function deleteAvatar() {
      if ($this->hasAvatar()) {
        PublicFiles::deleteFile($this->getAvatarFile());
        $this->setAvatarFile('');
      } // if
    } // deleteAvatar
    
    /**
    * Return path to the avatar file. This function just generates the path, does not check if file really exists
    *
    * @access public
    * @param void
    * @return string
    */
    function getAvatarPath() {
      return PublicFiles::getFilePath($this->getAvatarFile());
    } // getAvatarPath
    
    /**
    * Return URL of avatar
    *
    * @access public
    * @param void
    * @return string
    */
    function getAvatarUrl() {
      return $this->hasAvatar() ? PublicFiles::getFileUrl($this->getAvatarFile()) : get_image_url('avatar.gif');
    } // getAvatarUrl
    
    /**
    * Check if this contact has uploaded an avatar
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasAvatar() {
      return (trim($this->getAvatarFile()) <> '') && is_file($this->getAvatarPath());
    } // hasAvatar
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    // /**
    // * This function will generate new user password, set it and return it
    // *
    // * @param boolean $save Save object after the update
    // * @return string
    // */
    // function resetPassword($save = true) {
    //   $new_password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
    //   $this->setPassword($new_password);
    //   if ($save) {
    //     $this->save();
    //   } // if
    //   return $new_password;
    // } // resetPassword
    // 
    // /**
    // * Set password value
    // *
    // * @param string $value
    // * @return boolean
    // */
    // function setPassword($value) {
    //   do {
    //     $salt = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
    //     $token = sha1($salt . $value);
    //   } while (Users::tokenExists($token));
    //   
    //   $this->setToken($token);
    //   $this->setSalt($salt);
    //   $this->setTwister(StringTwister::getTwister());
    // } // setPassword
    // 
    // /**
    // * Return twisted token
    // *
    // * @param void
    // * @return string
    // */
    // function getTwistedToken() {
    //   return $this->getUser()->getTwistedToken();
    // } // getTwistedToken
    // 
    // /**
    // * Check if $check_password is valid user password
    // *
    // * @param string $check_password
    // * @return boolean
    // */
    // function isValidPassword($check_password) {
    //   return sha1($this->getSalt() . $check_password) == $this->getToken();
    // } // isValidPassword
    // 
    // /**
    // * Check if $twisted_token is valid for this user account
    // *
    // * @param string $twisted_token
    // * @return boolean
    // */
    // function isValidToken($twisted_token) {
    //   return StringTwister::untwistHash($twisted_token, $this->getTwister()) == $this->getToken();
    // } // isValidToken
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
    * Can specific user add contact to specific company
    *
    * @access public
    * @param User $user
    * @param Company $to Can user add user to this company
    * @return boolean
    */
    function canAdd(User $user, Company $to) {
      if ($user->isAccountOwner()) {
        return true;
      } // if
      return $user->isAdministrator();
    } // canAdd
    
    /**
    * Check if specific user can update this contact
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canEdit(User $user) {
      if ($user->getId() == $this->getId()) {
        return true; // account owner
      } // if
      if ($user->isAccountOwner()) {
        return true;
      } // if
      return $user->isAdministrator();
    } // canEdit
    
    /**
    * Check if specific user can delete specific contact
    *
    * @param User $user
    * @return boolean
    */
    function canDelete(User $user) {
      if ($this->isAccountOwner()) {
        return false; // can't delete accountowner
      } // if
      
      if ($this->getId() == $user->getId()) {
        return false; // can't delete self
      } // if
      
      return $user->isAdministrator();
    } // canDelete
    
    /**
    * Returns if this user can add a user account to that contact
    *
    * @param User $user
    * @return boolean
    */
    function canAddUserAccount(User $user) {
      if ($user->isAccountOwner()) {
        return true; // account owner can manage users
      } // if
      
      return $user->isAdministrator();
    } // canAddUserAccount
    
    /**
    * Returns if this user can edit the user account linked to that contact
    *
    * @param User $user
    * @return boolean
    */
    function canEditUserAccount(User $user) {
      if ($user->isAccountOwner()) {
        return true;
      } // if
      
      if ($this->getUserId() == $user->getId()) {
        return true;
      } // can edit your own user account
      
      return $user->isAdministrator();
    } // canEditUserAccount

    /**
    * Returns if this user can delete the user account linked to that contact
    *
    * @param User $user
    * @return boolean
    */
    function canDeleteUserAccount(User $user) {
      if ($this->isAccountOwner()) {
        return false; // can't delete accountowner
      } // if
      
      if ($this->getUserId() == $user->getId()) {
        return false;
      } // can not delete your own user account
      
      return $user->isAdministrator();
    } // canEditUserAccount
    
    // /**
    // * Returns true if this user can see $user
    // *
    // * @param User $user
    // * @return boolean
    // */
    // function canSeeUser(User $user) {
    //   if ($this->isMemberOfOwnerCompany()) {
    //     return true; // see all
    //   } // if
    //   if ($user->getCompanyId() == $this->getCompanyId()) {
    //     return true; // see members of your own company
    //   } // if
    //   if ($user->isMemberOfOwnerCompany()) {
    //     return true; // see members of owner company
    //   } // if
    //   return false;
    // } // canSeeUser
    
    // /**
    // * Returns true if this user can see $company. Members of owner company and
    // * coworkers are visible without project check! Also, members of owner company
    // * can see all clients without any prior check!
    // *
    // * @param Company $company
    // * @return boolean
    // */
    // function canSeeCompany(Company $company) {
    //   if ($this->isMemberOfOwnerCompany()) {
    //     return true;
    //   } // if
    //   
    //   if (isset($this->visible_companies[$company->getId()])) {
    //     return $this->visible_companies[$company->getId()];
    //   } // if
    //   
    //   if ($company->isOwner()) {
    //     $this->visible_companies[$company->getId()] = true;
    //     return true;
    //   } // if
    //   
    //   if ($this->getCompanyId() == $company->getId()) {
    //     $this->visible_companies[$company->getId()] = true;
    //     return true;
    //   } // if
    //   
    //   // Lets companye projects for company of this user and for $company and 
    //   // compare if we have projects where both companies work together
    //   $projects_1 = DB::executeAll("SELECT `project_id` FROM " . ProjectCompanies::instance()->getTableName(true) . " WHERE `company_id` = ?", $this->getCompanyId());
    //   $projects_2 = DB::executeAll("SELECT `project_id` FROM " . ProjectCompanies::instance()->getTableName(true) . " WHERE `company_id` = ?", $company->getId());
    //   
    //   if (!is_array($projects_1) || !is_array($projects_2)) {
    //     $this->visible_companies[$company->getId()] = false;
    //     return false;
    //   } // if
    //   
    //   foreach ($projects_1 as $project_id) {
    //     if (in_array($project_id, $projects_2)) {
    //       $this->visible_companies[$company->getId()] = true;
    //       return true;
    //     } // if
    //   } // foreach
    //   
    //   $this->visible_companies[$company->getId()] = false;
    //   return false;
    // } // canSeeCompany
    
    /**
    * Check if specific user can update this profile
    *
    * @param User $user
    * @return boolean
    */
    function canUpdateProfile(User $user) {
      if ($this->getId() == $user->getId()) {
        return true;
      } // if
      if ($user->isAdministrator()) {
        return true;
      } // if
      return false;
    } // canUpdateProfile
    
    // /**
    // * Check if this user can update this users permissions
    // *
    // * @param User $user
    // * @return boolean
    // */
    // function canUpdatePermissions(User $user) {
    //   if ($this->isAccountOwner()) {
    //     return false; // noone will touch this
    //   } // if
    //   return $user->isAdministrator();
    // } // canUpdatePermissions
    
    // /**
    // * Check if this user is company administration (used to check many other permissions). User must
    // * be part of the company and have is_admin stamp set to true
    // *
    // * @access public
    // * @param Company $company
    // * @return boolean
    // */
    // function isCompanyAdmin(Company $company) {
    //   return ($this->getCompanyId() == $company->getId()) && $this->getIsAdmin();
    // } // isCompanyAdmin
    
    // /**
    // * Return project permission for specific user if he is on project. In case of any error $default is returned
    // *
    // * @access public
    // * @param Project $project
    // * @param string $permission Permission name
    // * @param boolean $default Default value
    // * @return boolean
    // */
    // function getProjectPermission(Project $project, $permission, $default = false) {
    //   static $valid_permissions = null;
    //   if (is_null($valid_permissions)) {
    //     $valid_permissions = ProjectUsers::getPermissionColumns();
    //   } // if
    //   
    //   if (!in_array($permission, $valid_permissions)) {
    //     return $default;
    //   } // if
    //   
    //   $project_user = ProjectUsers::findById(array(
    //     'project_id' => $project->getId(),
    //     'user_id' => $this->getId()
    //   )); // findById
    //   if (!($project_user instanceof ProjectUser)) {
    //     return $default;
    //   } // if
    //   
    //   $getter = 'get' . Inflector::camelize($permission);
    //   return $project_user->$getter();
    // } // getProjectPermission
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    // /**
    // * Return view account URL of this user
    // *
    // * @access public
    // * @param void
    // * @return string
    // */
    // function getAccountUrl() {
    //   return get_url('account', 'index');
    // } // getAccountUrl
    
    /**
    * Show contact card page
    *
    * @access public
    * @param void
    * @return null
    */
    function getCardUrl() {
      return get_url('contact', 'card', $this->getId());
    } // getCardUrl
    
    /**
    * Return edit contact URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getEditUrl() {
      return get_url('contact', 'edit', $this->getId());
    } // getEditUrl
    
    /**
    * Return delete contact URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getDeleteUrl() {
      return get_url('contact', 'delete', $this->getId());
    } // getDeleteUrl
    
    /**
    * Return edit profile URL
    *
    * @param string $redirect_to URL where we need to redirect user when he updates profile
    * @return string
    */
    function getEditProfileUrl($redirect_to = null) {
      $attributes = array('id' => $this->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('account', 'edit_profile', $attributes);
    } // getEditProfileUrl
    
    /**
    * Returns URL to attach a User account to that contact
    *
    * @param void
    * @return string
    */
    function getAddUserAccountUrl() {
      return get_url('contact', 'add_user_account', $this->getId());
    } // getAddUserUrl
    
    /**
    * Returns URL to edit User account linked to that contact
    *
    * @param void
    * @return string
    */
    function getEditUserAccountUrl() {
      return get_url('contact', 'edit_user_account', $this->getId());
    } // getEditUserAccountUrl
    
    /**
    * Returns URL to delete User account linked to that contact
    *
    * @param void
    * @return string
    */
    function getDeleteUserAccountUrl() {
      return get_url('contact', 'delete_user_account', $this->getId());
    } // getDeleteUserAccountUrl
    
    
    // /**
    // * Edit users password
    // *
    // * @param string $redirect_to URL where we need to redirect user when he updates password
    // * @return null
    // */
    // function getEditPasswordUrl($redirect_to = null) {
    //   $attributes = array('id' => $this->getId());
    //   if (trim($redirect_to) <> '') {
    //     $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
    //   } // if
    //   
    //   return get_url('account', 'edit_password', $attributes);
    // } // getEditPasswordUrl
    
    // /**
    // * Return update user permissions page URL
    // *
    // * @param string $redirect_to
    // * @return string
    // */
    // function getUpdatePermissionsUrl($redirect_to = null) {
    //   $attributes = array('id' => $this->getId());
    //   if (trim($redirect_to) <> '') {
    //     $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
    //   } // if
    //   
    //   return get_url('account', 'update_permissions', $attributes);
    // } // getUpdatePermissionsUrl
    
    /**
    * Return update avatar URL
    *
    * @param string
    * @return string
    */
    function getUpdateAvatarUrl($redirect_to = null) {
      $attributes = array('id' => $this->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('account', 'edit_avatar', $attributes);
    } // getUpdateAvatarUrl
    
    /**
    * Return delete avatar URL
    *
    * @param void
    * @return string
    */
    function getDeleteAvatarUrl($redirect_to = null) {
      $attributes = array('id' => $this->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('account', 'delete_avatar', $attributes);
    } // getDeleteAvatarUrl
    
    /**
    * Return toggle favorite URL
    *
    * @param void
    * @return string
    */
    function getToggleFavoriteUrl($redirect_to = null) {
      $attributes = array('id' => $this->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('contact', 'toggle_favorite', $attributes);
    } // getToggleFavoriteUrl
    
    // /**
    // * Return recent activities feed URL
    // * 
    // * If $project is valid project instance URL will be limited for that project only, else it will be returned for 
    // * overall feed
    // *
    // * @param Project $project
    // * @return string
    // */
    // function getRecentActivitiesFeedUrl($project = null) {
    //   $params = array(
    //     'id' => $this->getId(),
    //     'token' => $this->getTwistedToken(),
    //   ); // array
    //   
    //   if ($project instanceof Project) {
    //     $params['project'] = $project->getId();
    //     return get_url('feed', 'project_activities', $params, null, false);
    //   } else {
    //     return get_url('feed', 'recent_activities', $params, null, false);
    //   } // if
    // } // getRecentActivitiesFeedUrl
    
    // /**
    // * Return iCalendar URL
    // * 
    // * If $project is valid project instance calendar will be rendered just for that project, else it will be rendered 
    // * for all active projects this user is involved with
    // *
    // * @param Project $project
    // * @return string
    // */
    // function getICalendarUrl($project = null) {
    //   $params = array(
    //     'id' => $this->getUserId(),
    //     'token' => $this->getTwistedToken(),
    //   ); // array
    //   
    //   if ($project instanceof Project) {
    //     $params['project'] = $project->getId();
    //     return get_url('feed', 'project_ical', $params, null, false);
    //   } else {
    //     return get_url('feed', 'user_ical', $params, null, false);
    //   } // if
    // } // getICalendarUrl
    
    // ---------------------------------------------------
    //  System functions
    // ---------------------------------------------------
    
    /**
    * Validate data before save
    *
    * @access public
    * @param array $errors
    * @return void
    */
    function validate(&$errors) {
      
      // Validate display_name if present
      if (!$this->validatePresenceOf('display_name')) {
        $errors[] = lang('name value required');
      } // if
      
      // Company ID
      if (!$this->validatePresenceOf('company_id')) {
        $errors[] = lang('company value required');
      }
    } // validate
    
    /**
    * Delete this object
    *
    * @param void
    * @return boolean
    */
    function delete() {
      if ($this->isAccountOwner()) {
        return false;
      } // if
      
      // TODO check all things that need to be deleted
      // ticket subscriptions
      // message subscriptions
      // project-user association

      $this->deleteAvatar();
      $this->clearImValues();
      ProjectUsers::clearByUser($this);
      MessageSubscriptions::clearByUser($this);
      if ($this->hasUserAccount()) {
        $this->getUserAccount()->delete();
      }
      return parent::delete();
    } // delete
    
    // ---------------------------------------------------
    //  ApplicationDataObject implementation
    // ---------------------------------------------------
    
    /**
    * Return object name
    *
    * @access public
    * @param void
    * @return string
    */
    function getObjectName() {
      return $this->getDisplayName();
    } // getObjectName
    
    /**
    * Return object type name
    *
    * @param void
    * @return string
    */
    function getObjectTypeName() {
      return lang('contact');
    } // getObjectTypeName
    
    /**
    * Return object URl
    *
    * @access public
    * @param void
    * @return string
    */
    function getObjectUrl() {
      return $this->getCardUrl();
    } // getObjectUrl
  
  } // Contact 

?>
