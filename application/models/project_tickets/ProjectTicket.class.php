<?php

  /**
  * ProjectTicket class
  * Generated on Wed, 08 Mar 2006 15:51:26 +0100 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class ProjectTicket extends BaseProjectTicket {
    
    /**
    * Project tickets are searchable
    *
    * @var boolean
    */
    protected $is_searchable = true;
    
    /**
    * Array of searchable columns
    *
    * @var array
    */
    protected $searchable_columns = array('summary', 'description');
    
    /**
    * Ticket is file container
    *
    * @var boolean
    */
    protected $is_file_container = true;

    /**
    * Ticket is subscribible
    *
    * @var boolean
    */
    protected $is_subscribible = true;
        
    /**
    * Cached array of subscribers
    *
    * @var array
    */
    private $subscribers;
        
    /**
    * Cached array of changesets
    *
    * @var array
    */
    private $changesets;
    
    // ---------------------------------------------------
    //  Comments
    // ---------------------------------------------------
    
    /**
    * Handle on add comment event
    *
    * @param Comment $comment
    * @return null
    */
    function onAddComment(Comment $comment) {
      try {
        $this->setUpdated('comment');
        $this->save();
        
        $change = new TicketChange();
        $change->setTicketId($this->getId());
        $change->setType('comment');
        $change->setToData('#'.$this->countAllComments());
        $change->save();
        
        Notifier::newTicketComment($comment);
      } catch (Exception $e) {
        // nothing here, just suppress error...
      } // try
    } // onAddComment
    
    // ---------------------------------------------------
    //  Files
    // ---------------------------------------------------
    
    /**
    * Handle on attach file event
    *
    * @param array $files Attached files
    * @return null
    */
    function onAttachFiles($files) {
      try {
        $this->setUpdated('attachment');
        $this->save();
        
        $changeset = new TicketChangeset();
        $changeset->setTicketId($this->getId());
        $changeset->save();
        foreach ($files as $file) {
          $change = new TicketChange();
          $change->setType('attachment');
          $change->setToData($file->getFilename());
          $change->setChangesetId($changeset->getId());
          $change->save();
        } // foreach
        
        Notifier::attachFilesToTicket($this, $files);
      } catch (Exception $e) {
        // nothing here, just suppress error...
      } // try
    } // onAttachFiles
    
    /**
    * Handle on detach file event
    *
    * @param array $files Attached files
    * @return null
    */
    function onDetachFiles($files) {
      try {
        $this->setUpdated('attachment');
        $this->save();
        
        $changeset = new TicketChangeset();
        $changeset->setTicketId($this->getId());
        $changeset->save();
        foreach ($files as $file) {
          $change = new TicketChange();
          $change->setType('attachment');
          $change->setFromData($file->getFilename());
          $change->setChangesetId($changeset->getId());
          $change->save();
        } // foreach
        
        Notifier::detachFilesFromTicket($this, $files);
      } catch (Exception $e) {
        // nothing here, just suppress error...
      } // try
    } // onAttachFiles
    
    // ---------------------------------------------------
    //  Changesets
    // ---------------------------------------------------
    
    /**
    * Return array of changes
    *
    * @param void
    * @return array
    */
    function getChangesets() {
      if (is_null($this->changesets)) {
        $this->changesets = TicketChangesets::getChangesetsByTicket($this);
      }
      return $this->changesets;
    } // getChangesets
    
    // ---------------------------------------------------
    //  Subscriptions
    // ---------------------------------------------------
    
    /**
    * Return array of subscribers
    *
    * @param void
    * @return array
    */
    function getSubscribers() {
      if (is_null($this->subscribers)) {
        $this->subscribers = TicketSubscriptions::getUsersByTicket($this);
      }
      return $this->subscribers;
    } // getSubscribers
    
    /**
    * Check if specific user is subscriber
    *
    * @param User $user
    * @return boolean
    */
    function isSubscriber(User $user) {
      $subscription = TicketSubscriptions::findById(array(
        'ticket_id' => $this->getId(),
        'user_id' => $user->getId()
      )); // findById
      return $subscription instanceof TicketSubscription;
    } // isSubscriber
    
    /**
    * Subscribe specific user to this ticket
    *
    * @param User $user
    * @return boolean
    */
    function subscribeUser(User $user) {
      if ($this->isNew()) {
        throw new Error('Can\'t subscribe user to ticket that is not saved');
      } // if
      if ($this->isSubscriber($user)) {
        return true;
      } // if
      
      // New subscription
      $subscription = new TicketSubscription();
      $subscription->setTicketId($this->getId());
      $subscription->setUserId($user->getId());
      return $subscription->save();
    } // subscribeUser
    
    /**
    * Unsubscribe user
    *
    * @param User $user
    * @return boolean
    */
    function unsubscribeUser(User $user) {
      $subscription = TicketSubscriptions::findById(array(
        'ticket_id' => $this->getId(),
        'user_id' => $user->getId()
      )); // findById
      if ($subscription instanceof TicketSubscription) {
        return $subscription->delete();
      } else {
        return true;
      } // if
    } // unsubscribeUser
    
    /**
    * Clear all ticket subscriptions
    *
    * @param void
    * @return boolean
    */
    function clearSubscriptions() {
      return TicketSubscriptions::clearByTicket($this);
    } // clearSubscriptions
  
    // ---------------------------------------------------
    //  Operations
    // ---------------------------------------------------
    
    /**
    * Return object name
    *
    * @access public
    * @param void
    * @return string
    */
    function getTitle() {
      return $this->getSummary();
    } // getObjectName
    
    /**
    * Return owner project obj
    *
    * @access public
    * @param void
    * @return Project
    */
    function getProject() {
      return Projects::findById($this->getProjectId());
    } // getProject
    
    /**
    * Return user object of person who created this ticket
    *
    * @access public
    * @param void
    * @return User
    */
    function getClosedBy() {
      return Users::findById($this->getClosedById());
    } // getCreatedBy
    
    /**
    * Return owner user or company
    *
    * @access public
    * @param void
    * @return ApplicationDataObject
    */
    function getAssignedTo() {
      if ($this->getAssignedToUserId() > 0) {
        return $this->getAssignedToUser();
      } elseif ($this->getAssignedToCompanyId() > 0) {
        return $this->getAssignedToCompany();
      } else {
        return null;
      } // if
    } // getAssignedTo
    
    /**
    * Return owner company
    *
    * @access public
    * @param void
    * @return Company
    */
    function getAssignedToCompany() {
      return Companies::findById($this->getAssignedToCompanyId());
    } // getAssignedToCompany
    
    /**
    * Return owner user
    *
    * @access public
    * @param void
    * @return User
    */
    function getAssignedToUser() {
      return Users::findById($this->getAssignedToUserId());
    } // getAssignedToUser
    
    /**
    * Return owner user or company
    *
    * @access public
    * @param void
    * @return ApplicationDataObject
    */
    function getCategory() {
      if ($this->getCategoryId() > 0) {
        return Categories::findById($this->getCategoryId());
      } else {
        return null;
      } // if
    } // getAssignedTo
    
    /**
    * Returns associated milestone
    *
    * @access public
    * @param void
    * @return ProjectMilestone
    */
    function getMilestone() {
      if ($this->getMilestoneId() > 0) {
        return ProjectMilestones::findById($this->getMilestoneId());
      } else {
        return null;
      } // if
    } // getMilestone
    
    /**
    * Returns true if this ticket was not closed
    *
    * @access public
    * @param void
    * @return boolean
    */
    function isOpen() {
      return !$this->isClosed();
    } // isOpen
    
    /**
    * Returns true if this ticket is closed
    *
    * @access public
    * @param void
    * @return boolean
    */
    function isClosed() {
      // TODO remove hardcoded value from here
      return $this->getStatus() == 'closed';
    } // isClosed
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
    * Returns true if $user can access this ticket
    *
    * @param User $user
    * @return boolean
    */
    function canView(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false; // user have access to project
      } // if
      if ($this->isPrivate() && !$user->isMemberOfOwnerCompany()) {
        return false; // user that is not member of owner company can't access private objects
      } // if
      return true;
    } // canView
    
    /**
    * Check if specific user can add tickets to specific project
    *
    * @access public
    * @param User $user
    * @param Project $project
    * @return boolean
    */
    function canAdd(User $user, Project $project) {
      if (!$user->isProjectUser($project)) {
        return false; // user is on project
      } // if
      return true;
    } // canAdd
    
    /**
    * Check if specific user can update this ticket
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canChangeStatus(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false;
      } // if
      if ($this->isPrivate() && !$user->isMemberOfOwnerCompany()) {
        return false; // user that is not member of owner company can't access private objects
      } // if
      return true;
    } // canChangeStatus
    
    /**
    * Check if specific user can update this ticket
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canEdit(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false;
      } // if
      if ($user->isAdministrator()) {
        return true;
      } // if
      if ($this->isPrivate() && !$user->isMemberOfOwnerCompany()) {
        return false; // user that is not member of owner company can't access private objects
      } // if
      
      if ($this->getCreatedById() == $user->getId() && $this->getCreatedOn()->getTimestamp()+15*60>DateTimeValueLib::now()->getTimestamp()) {
        return true;
      } // if
      $assigned_to = $this->getAssignedTo();
      if ($assigned_to instanceof User) {
        if ($user->getId() == $assigned_to->getId()) {
          return true;
        } // if
      } elseif ($assigned_to instanceof Company) {
        if ($user->getCompanyId() == $assigned_to->getId()) {
          return true;
        } // if
      } // if
      
      return $user->getProjectPermission($this->getProject(), ProjectUsers::CAN_MANAGE_TICKETS);
    } // canEdit
    
    /**
    * Check if $user can update message options
    *
    * @param User $user
    * @return boolean
    */
    function canUpdateOptions(User $user) {
      return $user->isMemberOfOwnerCompany() && $this->canEdit($user);
    } // canUpdateOptions
  
    /**
    * Check if specific user can delete this task
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canDelete(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false;
      } // if
      if ($user->isAdministrator()) {
        return true;
      } // if
      
      return false; // no no
    } // canDelete
    
    // ---------------------------------------------------
    //  Operations
    // ---------------------------------------------------
    
    /**
    * Complete this task and check if we need to complete the list
    *
    * @access public
    * @param void
    * @return null
    */
    function closeTicket() {
      $this->setClosedOn(DateTimeValueLib::now());
      $this->setClosedById(logged_user()->getId());
      $this->setUpdated('closed');
      $this->save();
    } // completeTask
    
    /**
    * Open this task and check if we need to reopen list again
    *
    * @access public
    * @param void
    * @return null
    */
    function openTicket() {
      $this->setClosedOn(null);
      $this->setClosedById(0);
      $this->setUpdated('open');
      $this->save();
    } // openTask
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
    * Return view ticket URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getViewUrl() {
      return get_url('ticket', 'view', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getViewUrl
    
    /**
    * Return add ticket URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getAddUrl() {
      return get_url('ticket', 'add', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getEditUrl
    
    /**
    * Return edit ticket URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getEditUrl() {
      return get_url('ticket', 'edit', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getEditUrl
    
    /**
    * Return delete ticket URL
    *
    * @access public
    * @param void
    * @return string
    */
    function getDeleteUrl() {
      return get_url('ticket', 'delete', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getDeleteUrl
    
    /**
    * Return close ticket URL
    *
    * @access public
    * @param string $redirect_to Redirect to this URL (referrer will be used if this URL is not provided)
    * @return string
    */
    function getCloseUrl($redirect_to = null) {
      $params = array(
        'id' => $this->getId(), 
        'active_project' => $this->getProjectId()
      ); // array
      
      if (trim($redirect_to)) {
        $params['redirect_to'] = $redirect_to;
      } // if
      
      return get_url('ticket', 'close', $params);
    } // getCompleteUrl
    
    /**
    * Return open ticket URL
    *
    * @access public
    * @param string $redirect_to Redirect to this URL (referrer will be used if this URL is not provided)
    * @return string
    */
    function getOpenUrl($redirect_to = null) {
      $params = array(
        'id' => $this->getId(), 
        'active_project' => $this->getProjectId()
      ); // array
      
      if (trim($redirect_to)) {
        $params['redirect_to'] = $redirect_to;
      } // if
      
      return get_url('ticket', 'open', $params);
    } // getOpenUrl
    
    /**
    * Return update options URL
    *
    * @param void
    * @return string
    */
    function getUpdateOptionsUrl() {
      return get_url('ticket', 'update_options', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getUpdateOptionsUrl
    
    /**
    * Return save change URL
    *
    * @param void
    * @return string
    */
    function getSaveChangeUrl() {
      return get_url('ticket', 'save_change', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getSaveChangeUrl
    
    /**
    * Return subscribe URL
    *
    * @param void
    * @return boolean
    */
    function getSubscribeUrl() {
      return get_url('ticket', 'subscribe', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getSubscribeUrl
    
    /**
    * Return unsubscribe URL
    *
    * @param void
    * @return boolean
    */
    function getUnsubscribeUrl() {
      return get_url('ticket', 'unsubscribe', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getUnsubscribeUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
    * Validate before save
    *
    * @access public
    * @param array $errors
    * @return null
    */
    function validate(&$errors) {
      if (!$this->validatePresenceOf('summary')) {
        $errors[] = lang('ticket summary required');
      }
      if (!$this->validatePresenceOf('description')) {
        $errors[] = lang('ticket description required');
      }
    } // validate
    
    /**
    * Delete this task
    *
    * @access public
    * @param void
    * @return boolean
    */
    function delete() {
      $comments = $this->getComments();
      if (is_array($comments)) {
        foreach ($comments as $comment) {
          $comment->delete();
        }
      }
      $this->clearSubscriptions();
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
      return $this->getSummary();
    } // getObjectName
    
    /**
    * Return object type name
    *
    * @param void
    * @return string
    */
    function getObjectTypeName() {
      return lang('ticket');
    } // getObjectTypeName
    
    /**
    * Return object URl
    *
    * @access public
    * @param void
    * @return string
    */
    function getObjectUrl() {
      return $this->getViewUrl();
    } // getObjectUrl
    
  } // ProjectTicket 

?>