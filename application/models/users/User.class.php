<?php

  /**
  * User class
  * Generated on Sat, 25 Feb 2006 17:37:12 +0100 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class User extends BaseUser {
    
    /**
    * Cached project permission values. Two level array. First level are projects (project ID) and
    * second are permissions associated with permission name
    *
    * @var array
    */
    private $project_permissions_cache = array();
    
    /**
    * Associative array. Key is project ID and value is true if user has access to that project
    *
    * @var array
    */
    private $is_project_user_cache = array();
    
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
    * Cached value of all projects
    *
    * @var array
    */
    private $projects;
    
    /**
    * Cached value of active projects
    *
    * @var array
    */
    private $active_projects;
    
    /**
    * Cached value of finished projects
    *
    * @var array
    */
    private $finished_projects;
    
    /**
    * Array of all active milestones
    *
    * @var array
    */
    private $all_active_milestons;
    
    /**
    * Cached late milestones
    *
    * @var array
    */
    private $late_milestones;
    
    /**
    * Cached today milestones
    *
    * @var array
    */
    private $today_milestones;
    
    /**
    * Cached open tickets
    *
    * @var array
    */
    private $open_tickets;
    
    /**
    * Cached late tickets
    *
    * @var array
    */
    private $late_tickets;
    
    /**
    * Cached array of new objects
    *
    * @var array
    */
    private $whats_new;
    
    /**
    * canSeeCompany() method will cache its result here (company_id => visible as bool)
    *
    * @var array
    */
    private $visible_companies = array();
    
    /**
    * Construct user object
    *
    * @param void
    * @return User
    */
    function __construct() {
      parent::__construct();
      $this->addProtectedAttribute('password', 'salt', 'session_lifetime', 'token', 'twister', 'last_login', 'last_visit', 'last_activity');
    } // __construct
    
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
      if (is_null($this->is_administrator)) {
        $this->is_administrator = $this->isAccountOwner() || ($this->isMemberOfOwnerCompany() && $this->getIsAdmin());
      } // if
      return $this->is_administrator;
    } // isAdministrator
    
    /**
    * Account owner is user account that was created when company website is created
    *
    * @param void
    * @return boolean
    */
    function isAccountOwner() {
      if (is_null($this->is_account_owner)) {
        $this->is_account_owner = $this->isMemberOfOwnerCompany() && (owner_company()->getCreatedById() == $this->getId());
      } // if
      return $this->is_account_owner;
    } // isAccountOwner
    
    /**
    * Check if this user have specific project permission. $permission is the name of table field that holds the value
    *
    * @param Project $project
    * @param string $permission Name of the field where the permission value is stored. There are set of constants
    *   in ProjectUser that hold field names (ProjectUser::CAN_MANAGE_MESSAGES ...)
    * @return boolean
    */
    function hasProjectPermission(Project $project, $permission, $use_cache = true) {
      if ($use_cache) {
        if (isset($this->project_permissions_cache[$project->getId()]) && isset($this->project_permissions_cache[$project->getId()][$permission])) {
          return $this->project_permissions_cache[$project->getId()][$permission];
        } // if
      } // if
      
      $project_user = ProjectUsers::findById(array('project_id' => $project->getId(), 'user_id' => $this->getId()));
      if (!($project_user instanceof ProjectUser)) {
        if ($use_cache) {
          $this->project_permissions_cache[$project->getId()][$permission] = false;
        } // if
        return false;
      } // if
      
      $getter_method = 'get' . Inflector::camelize($permission);
      $project_user_methods = get_class_methods('ProjectUser');
      
      $value = in_array($getter_method, $project_user_methods) ? $project_user->$getter_method() : false;
      
      if ($use_cache) {
        $this->project_permissions_cache[$project->getId()][$permission] = $value;
      }
      return $value;
    } // hasProjectPermission
    
    /**
    * This function will check if this user have all project permissions
    *
    * @param Project $project
    * @param boolean $use_cache
    * @return boolean
    */
    function hasAllProjectPermissions(Project $project, $use_cache = true) {
      $permissions = ProjectUsers::getPermissionColumns();
      if (is_array($permissions)) {
        foreach ($permissions as $permission) {
          if (!$this->hasProjectPermission($project, $permission, $use_cache)) {
            return false;
          }
        } // foreach
      } // if
      return true;
    } // hasAllProjectPermissions
    
    // ---------------------------------------------------
    //  Retrieve
    // ---------------------------------------------------
    
    /**
    * Account owner has no creator per se, thus special handling
    * 
    * @param void
    * @return User
    */
    function getCreatedBy() {
      if ($this->isAccountOwner()) {
        return $this;
      }
      return parent::getCreatedBy();
    } // getCreatedBy
    
    /**
    * Return associated contact
    *
    * @param void
    * @return Contact
    */
    function getContact() {
      return Contacts::findOne(array('conditions' => array('`user_id` = ? ', $this->getId())));
    } // getContact
    
    /**
    * Return all projects that this user is member of
    *
    * @access public
    * @param void
    * @return array
    */
    function getProjects() {
      if (is_null($this->projects)) {
        $this->projects = ProjectUsers::getProjectsByUser($this);
      } // if
      return $this->projects;
    } // getProjects
    
    /**
    * Return array of active projects that this user have access
    *
    * @access public
    * @param void
    * @return array
    */
    function getActiveProjects() {
      if (is_null($this->active_projects)) {
        $this->active_projects = ProjectUsers::getProjectsByUser($this, '`completed_on` = ' . DB::escape(EMPTY_DATETIME));
      } // if
      return $this->active_projects;
    } // getActiveProjects
    
    /**
    * Return array of finished projects
    *
    * @access public
    * @param void
    * @return array
    */
    function getFinishedProjects() {
      if (is_null($this->finished_projects)) {
        $this->finished_projects = ProjectUsers::getProjectsByUser($this, '`completed_on` > ' . DB::escape(EMPTY_DATETIME));
      } // if
      return $this->finished_projects;
    } // getFinishedProjects
    
    /**
    * Return all active milestones assigned to this user
    *
    * @param void
    * @return array
    */
    function getActiveMilestones() {
      if (is_null($this->all_active_milestons)) {
        $this->all_active_milestons = ProjectMilestones::getActiveMilestonesByUser($this);
      } // if
      return $this->all_active_milestons;
    } // getActiveMilestones
    
    /**
    * Return late milestones that this user have access to
    *
    * @access public
    * @param void
    * @return array
    */
    function getLateMilestones() {
      if (is_null($this->late_milestones)) {
        $this->late_milestones = ProjectMilestones::getLateMilestonesByUser($this);
      } // if
      return $this->late_milestones;
    } // getLateMilestones
    
    /**
    * Return today milestones that this user have access to
    *
    * @access public
    * @param void
    * @return array
    */
    function getTodayMilestones() {
      if (is_null($this->today_milestones)) {
        $this->today_milestones = ProjectMilestones::getTodayMilestonesByUser($this);
      } // if
      return $this->today_milestones;
    } // getTodayMilestones
    
    /**
    * Returns all open tickets assigned to that user
    *
    * @access public
    * @param void
    * @return array
    */
    function getOpenTickets() {
      if (is_null($this->open_tickets)) {
        $this->open_tickets = ProjectTickets::getOpenTicketsByUser($this);
      } // if
      return $this->open_tickets;
    } // getOpenTickets

    /**
    * Returns late tickets assigned to that user
    *
    * @access public
    * @param void
    * @return array
    */
    function getLateTickets() {
      if (is_null($this->late_tickets)) {
        $this->late_tickets = ProjectTickets::getLateTicketsByUser($this);
      } // if
      return $this->late_tickets;
    } // getLateTickets

    
    
    // ---------------------------------------------------
    // Retrieve from associated Contact
    // Mostly for backward compatibility and convenience
    // following move of contact info to their own Contact
    // model.
    // ---------------------------------------------------
    
    /**
    * Check if this user is member of specific company
    *
    * @access public
    * @param Company $company
    * @return boolean
    */
    function isMemberOf(Company $company) {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->isMemberOf($company);
      }
      return false;
    } // isMemberOf

    /**
    * Usually we check if user is member of owner company so this is the shortcut method
    *
    * @param void
    * @return boolean
    */
    function isMemberOfOwnerCompany() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->isMemberOfOwnerCompany();
      }
      return false;
    } // isMemberOfOwnerCompany

    /**
    * Return user's company
    *
    * @access public
    * @param void
    * @return Company
    */
    function getCompany() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->getCompany();
      }
      return null;
    } // getCompany

    /**
    * Returns user's company's ID
    *
    * @access public
    * @param void
    * @return integer
    */
    function getCompanyId() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->getCompanyId();
      }
      return null;
    } // getCompanyId
    
    /**
    * Return associated contact's display name
    *
    * @param void
    * @return string
    */
    function getDisplayName() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->getDisplayName();
      }
      return $this->getUsername();
    } // getDisplayName

    /**
    * Returns true if we have title value set
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasTitle() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->hasTitle();
      }
      return false;
    } // hasTitle

    /**
    * Return path to the avatar file. This function just generates the path, does not check if file really exists
    *
    * @access public
    * @param void
    * @return string
    */
    function getAvatarPath() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->getAvatarPath();
      }
      return null;
    } // getAvatarPath

    /**
    * Return URL of avatar
    *
    * @access public
    * @param void
    * @return string
    */
    function getAvatarUrl() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->getAvatarUrl();
      }
      return null;
    } // getAvatarUrl

    /**
    * Check if this user has uploaded avatar
    *
    * @access public
    * @param void
    * @return boolean
    */
    function hasAvatar() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->hasAvatar();
      }
      return false;
    } // hasAvatar

    /**
    * Show user card page
    *
    * @access public
    * @param void
    * @return string
    */
    function getCardUrl() {
      if ($this->getContact() instanceof Contact) {
        return $this->getContact()->getCardUrl();
      }
      return null;
    } // getCardUrl
    
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
    * This function will generate new user password, set it and return it
    *
    * @param boolean $save Save object after the update
    * @return string
    */
    function resetPassword($save = true) {
      $new_password = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
      $this->setPassword($new_password);
      if ($save) {
        $this->save();
      } // if
      return $new_password;
    } // resetPassword
    
    /**
    * Set password value
    *
    * @param string $value
    * @return boolean
    */
    function setPassword($value) {
      do {
        $salt = substr(sha1(uniqid(rand(), true)), rand(0, 25), 13);
        $token = sha1($salt . $value);
      } while (Users::tokenExists($token));
      
      $this->setToken($token);
      $this->setSalt($salt);
      $this->setTwister(StringTwister::getTwister());
    } // setPassword
    
    /**
    * Return twisted token
    *
    * @param void
    * @return string
    */
    function getTwistedToken() {
      return StringTwister::twistHash($this->getToken(), $this->getTwister());
    } // getTwistedToken
    
    /**
    * Check if $check_password is valid user password
    *
    * @param string $check_password
    * @return boolean
    */
    function isValidPassword($check_password) {
      return sha1($this->getSalt() . $check_password) == $this->getToken();
    } // isValidPassword
    
    /**
    * Check if $twisted_token is valid for this user account
    *
    * @param string $twisted_token
    * @return boolean
    */
    function isValidToken($twisted_token) {
      return StringTwister::untwistHash($twisted_token, $this->getTwister()) == $this->getToken();
    } // isValidToken
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
    * Can specific user add a user to specific company
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
    * Check if specific user can update this user account
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
    * Check if specific user can delete specific account
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
    * Returns true if this user can see $user
    *
    * @param User $user
    * @return boolean
    */
    function canSeeUser(User $user) {
      if ($this->isMemberOfOwnerCompany()) {
        return true; // see all
      } // if
      if ($user->getCompanyId() == $this->getCompanyId()) {
        return true; // see members of your own company
      } // if
      if ($user->isMemberOfOwnerCompany()) {
        return true; // see members of owner company
      } // if
      return false;
    } // canSeeUser
    
    /**
    * Returns true if this user can see $contact
    * TODO shouldn't it be in the Contact model?
    *
    * @param Contact $contact
    * @return boolean
    */
    function canSeeContact(Contact $contact) {
      if ($this->isMemberOfOwnerCompany()) {
        return true; // see all
      } // if
      if ($contact->getCompanyId() == $this->getCompanyId()) {
        return true; // see members of your own company
      } // if
      if ($contact->isMemberOfOwnerCompany()) {
        return true; // see members of owner company
      } // if
      return false;
    } // canSeeContact
    
    /**
    * Returns true if this user can see $company. Members of owner company and
    * coworkers are visible without project check! Also, members of owner company
    * can see all clients without any prior check!
    * TODO shouldn't this be in the Company model?
    *
    * @param Company $company
    * @return boolean
    */
    function canSeeCompany(Company $company) {
      if ($this->isMemberOfOwnerCompany()) {
        return true;
      } // if
      
      if (isset($this->visible_companies[$company->getId()])) {
        return $this->visible_companies[$company->getId()];
      } // if
      
      if ($company->isOwner()) {
        $this->visible_companies[$company->getId()] = true;
        return true;
      } // if
      
      if ($this->getCompanyId() == $company->getId()) {
        $this->visible_companies[$company->getId()] = true;
        return true;
      } // if
      
      // Lets companye projects for company of this user and for $company and 
      // compare if we have projects where both companies work together
      $projects_1 = DB::executeAll("SELECT `project_id` FROM " . ProjectCompanies::instance()->getTableName(true) . " WHERE `company_id` = ?", $this->getCompanyId());
      $projects_2 = DB::executeAll("SELECT `project_id` FROM " . ProjectCompanies::instance()->getTableName(true) . " WHERE `company_id` = ?", $company->getId());
      
      if (!is_array($projects_1) || !is_array($projects_2)) {
        $this->visible_companies[$company->getId()] = false;
        return false;
      } // if
      
      foreach ($projects_1 as $project_id) {
        if (in_array($project_id, $projects_2)) {
          $this->visible_companies[$company->getId()] = true;
          return true;
        } // if
      } // foreach
      
      $this->visible_companies[$company->getId()] = false;
      return false;
    } // canSeeCompany
    
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
    
    /**
    * Check if this user can update this users permissions
    *
    * @param User $user
    * @return boolean
    */
    function canUpdatePermissions(User $user) {
      if ($this->isAccountOwner()) {
        return false; // noone will touch this
      } // if
      return $user->isAdministrator();
    } // canUpdatePermissions
    
    /**
    * Check if this user is company administration (used to check many other permissions). User must
    * be part of the company and have is_admin stamp set to true
    *
    * @access public
    * @param Company $company
    * @return boolean
    */
    function isCompanyAdmin(Company $company) {
      return ($this->getCompanyId() == $company->getId()) && $this->getIsAdmin();
    } // isCompanyAdmin
    
    /**
    * Return project permission for specific user if he is on project. In case of any error $default is returned
    *
    * @access public
    * @param Project $project
    * @param string $permission Permission name
    * @param boolean $default Default value
    * @return boolean
    */
    function getProjectPermission(Project $project, $permission, $default = false) {
      static $valid_permissions = null;
      if (is_null($valid_permissions)) {
        $valid_permissions = ProjectUsers::getPermissionColumns();
      } // if
      
      if (!in_array($permission, $valid_permissions)) {
        return $default;
      } // if
      
      $project_user = ProjectUsers::findById(array(
        'project_id' => $project->getId(),
        'user_id' => $this->getId()
      )); // findById
      if (!($project_user instanceof ProjectUser)) {
        return $default;
      } // if
      
      $getter = 'get' . Inflector::camelize($permission);
      return $project_user->$getter();
    } // getProjectPermission
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
    * Return view account URL of this user
    *
    * @access public
    * @param void
    * @return string
    */
    function getAccountUrl() {
      return get_url('account', 'index');
    } // getAccountUrl
    
    /**
    * Return edit user URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getEditUrl() {
      return get_url('user', 'edit', $this->getId());
    } // getEditUrl
    
    /**
    * Return delete user URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getDeleteUrl() {
      return get_url('user', 'delete', $this->getId());
    } // getDeleteUrl
    
    /**
    * Return edit profile URL
    *
    * @param string $redirect_to URL where we need to redirect user when he updates profile
    * @return string
    */
    function getEditProfileUrl($redirect_to = null) {
      $attributes = array('id' => $this->getContact()->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('contact', 'edit', $attributes);
    } // getEditProfileUrl
    
    /**
    * Edit users password
    *
    * @param string $redirect_to URL where we need to redirect user when he updates password
    * @return null
    */
    function getEditPasswordUrl($redirect_to = null) {
      $attributes = array('id' => $this->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('account', 'edit_password', $attributes);
    } // getEditPasswordUrl
    
    /**
    * Return update user permissions page URL
    *
    * @param string $redirect_to
    * @return string
    */
    function getUpdatePermissionsUrl($redirect_to = null) {
      $attributes = array('id' => $this->getId());
      if (trim($redirect_to) <> '') {
        $attributes['redirect_to'] = str_replace('&amp;', '&', trim($redirect_to));
      } // if
      
      return get_url('account', 'update_permissions', $attributes);
    } // getUpdatePermissionsUrl
    
    /**
    * Return recent activities feed URL
    * 
    * If $project is valid project instance URL will be limited for that project only, else it will be returned for 
    * overall feed
    *
    * @param Project $project
    * @return string
    */
    function getRecentActivitiesFeedUrl($project = null) {
      $params = array(
        'id' => $this->getId(),
        'token' => $this->getTwistedToken(),
      ); // array
      
      if ($project instanceof Project) {
        $params['project'] = $project->getId();
        return get_url('feed', 'project_activities', $params, null, false);
      } else {
        return get_url('feed', 'recent_activities', $params, null, false);
      } // if
    } // getRecentActivitiesFeedUrl
    
    /**
    * Return iCalendar URL
    * 
    * If $project is valid project instance calendar will be rendered just for that project, else it will be rendered 
    * for all active projects this user is involved with
    *
    * @param Project $project
    * @return string
    */
    function getICalendarUrl($project = null) {
      $params = array(
        'id' => $this->getId(),
        'token' => $this->getTwistedToken(),
      ); // array
      
      if ($project instanceof Project) {
        $params['project'] = $project->getId();
        return get_url('feed', 'project_ical', $params, null, false);
      } else {
        return get_url('feed', 'user_ical', $params, null, false);
      } // if
    } // getICalendarUrl
    
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
      
      // Validate username if present
      if ($this->validatePresenceOf('username')) {
        if (!$this->validateUniquenessOf('username')) {
          $errors[] = lang('username must be unique');
        }
      } else {
        $errors[] = lang('username value required');
      } // if
      
      if (!$this->validatePresenceOf('token')) {
        $errors[] = lang('password value required');
      }
      
      // Validate email if present
      if ($this->validatePresenceOf('email')) {
        if (!$this->validateFormatOf('email', EMAIL_FORMAT)) {
          $errors[] = lang('invalid email address');
        }
        if (!$this->validateUniquenessOf('email')) {
          $errors[] = lang('email address must be unique');
        }
      } else {
        $errors[] = lang('email value is required');
      } // if
      
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
      
      ProjectUsers::clearByUser($this);
      MessageSubscriptions::clearByUser($this);
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
      return lang('user');
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
  
  } // User 

?>
