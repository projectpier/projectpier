<?php

  /**
  * PageAttachment class
  * Generated on Wed, 19 Jul 2006 22:17:32 +0200 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class PageAttachment extends BasePageAttachment {
    
    /**
    * Return object connected with this action
    *
    * @access public
    * @param void
    * @return ProjectDataObject
    */
    function getObject() {
      return get_object_by_manager_and_id($this->getRelObjectId(), $this->getRelObjectManager());
    } // getObject
    
    
    /**
    * Renders attachment based on Object type
    *
    * @param void
    * @return string
    */
    function render($element_name) {
      switch ($this->getRelObjectManager()) {
        case 'Companies':
        case 'Contacts':
        case 'ProjectFiles':
        case 'ProjectMessages':
        case 'ProjectMilestones':
        case 'ProjectTasks':
        case 'ProjectTaskLists':
        case 'ProjectTickets':
          return text_field($element_name, $this->getText());
          break;
        default:
          return textarea_field($element_name, $this->getText(), array('class'=>'short'));
          break;
      } // switch
    } // render
    
    /**
    * Renders attachment control based on Object type
    *
    * @param void
    * @return string
    */
    function renderControl($control_name) {
      switch ($this->getRelObjectManager()) {
        case 'Companies':
          return select_company($control_name, $this->getRelObjectId(), array('class'=>'combobox'));
          break;
        case 'Contacts':
          return select_contact($control_name, $this->getRelObjectId(), null, array('class'=>'combobox'));
          break;
        case 'ProjectFiles':
          return select_project_file($control_name, active_project(), $this->getRelObjectId());
          break;
        case 'ProjectMessages':
          return select_message($control_name, active_project(), $this->getRelObjectId());
          break;
        case 'ProjectMilestones':
          return select_milestone($control_name, active_project(), $this->getRelObjectId());
          break;
        case 'ProjectTasks':
          
          break;
        case 'ProjectTaskLists':
          return select_task_list($control_name, active_project(), $this->getRelObjectId());
          break;
        case 'ProjectTickets':
          return select_ticket($control_name, active_project(), $this->getRelObjectId());
          break;
        default:
          return '';
          break;
      } // switch
    } // renderControl
    
    /**
    * Return the interface name of attached object
    *
    * @param boolean $singular
    * @return string
    */
    function getObjectLangName($singular = true) {
      switch ($this->getRelObjectManager()) {
        case 'Companies':
          return $singular ? 'company' : 'companies';
          break;
        case 'Contacts':
          return $singular ? 'contact' : 'contacts';
          break;
        case 'ProjectFiles':
          return $singular ? 'file' : 'files';
          break;
        case 'ProjectMessages':
          return $singular ? 'message' : 'messages';
          break;
        case 'ProjectMilestones':
          return $singular ? 'milestone' : 'milestones';
          break;
        case 'ProjectTasks':
          return $singular ? 'task' : 'tasks';
          break;
        case 'ProjectTaskLists':
          return $singular ? 'task list' : 'task lists';
          break;
        case 'ProjectTickets':
          return $singular ? 'ticket' : 'tickets';
          break;
        default:
          return $singular ? 'text snippet' : 'text snippets';
          break;
      } // switch
    } // getObjectNameSingular
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
    * Return tag URL
    *
    * @param void
    * @return string
    */
    function getViewUrl() {
      $object = $this->getObject();
      return $object instanceof ProjectDataObject ? $object->getObjectUrl() . '#comment' . $this->getId() : '';
    } // getViewUrl
    
    /**
    * Return attach to page URL for specific object
    *
    * @param ProjectDataObject $object
    * @return string
    */
    static function getAddUrl(ProjectDataObject $object) {
      return get_url('page_attachment', 'add', array(
        'object_id' => $object->getObjectId(),
        'object_manager' => get_class($object->manager()),
        'active_project' => $object->getProject()->getId(),
      )); // get_url
    } // getAddUrl
    
    /**
    * Return edit URL
    *
    * @param void
    * @return string
    */
    function getEditUrl() {
      return get_url('page_attachment', 'edit', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getEditUrl
    
    /**
    * Return delete URL
    *
    * @param void
    * @return string
    */
    function getDeleteUrl() {
      return get_url('page_attachment', 'delete', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
    * Can $user view this object
    *
    * @param User $user
    * @return boolean
    */
    function canView(User $user) {
      $project = $this->getProject();
      if (!($project instanceof Project) || !$user->isProjectUser($this->getProject())) {
        return false;
      } // if
      $object = $this->getObject();
      if ($object instanceof ProjectDataObject) {
        return $object->canView($user);
      } else {
        return false;
      } // if
    } // canView
    
    /**
    * Empty implementation of static method.
    * 
    * Add tag permissions are done through ProjectDataObject::canComment() method. This
    * will return comment permissions for specified object
    *
    * @param User $user
    * @param Project $project
    * @return boolean
    */
    function canAdd(User $user, Project $project) {
      return false;
    } // canAdd
    
    /**
    * Empty implementation of static method. Update tag permissions are check by the taggable
    * object, not tag itself
    *
    * @param User $user
    * @return boolean
    */
    function canEdit(User $user) {
      $project = $this->getProject();
      if (!($project instanceof Project) || !$user->isProjectUser($this->getProject())) {
        return false;
      } // if
      $object = $this->getObject();
      if ($object instanceof ProjectDataObject) {
        if ($user->isAdministrator()) {
          return true;
        } // if
        if (!$user->isMemberOfOwnerCompany() && $this->isPrivate()) {
          return false; // private object
        } // if
        
        $edit_limit = DateTimeValueLib::now();
        $edit_limit->advance(180);
        if (($this->getCreatedById() == $user->getId()) && ($this->getCreatedOn()->getTimestamp() < $edit_limit->getTimestamp())) {
          return true; // author withing three minutes
        } // if
      } // if
      return false;
    } // canEdit
    
    /**
    * Empty implementation of static method. Update tag permissions are check by the taggable
    * object, not tag itself
    *
    * @param User $user
    * @return boolean
    */
    function canDelete(User $user) {
      $project = $this->getProject();
      if (!($project instanceof Project) || !$user->isProjectUser($this->getProject())) {
        return false;
      } // if
      $object = $this->getObject();
      if ($object instanceof ProjectDataObject) {
        return $user->isAdministrator();
      } // if
      return false;
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
    * Validate before save
    *
    * @param array $error
    * @return null
    */
    function validate(&$errors) {
    } // validate
    
    /**
    * Save the object
    *
    * @param void
    * @return boolean
    */
    function save() {
      $saved = parent::save();
      return $saved;
    } // save
    
    /**
    * Delete comment
    *
    * @param void
    * @return null
    */
    function delete() {
      $deleted = parent::delete();
      return $deleted;
    } // delete
    
    // ---------------------------------------------------
    //  ApplicationDataObject implementation
    // ---------------------------------------------------
    
    /**
    * Return object name
    *
    * @param void
    * @return string
    */
    function getObjectName() {
      $object = $this->getObject();
      return $object instanceof ProjectDataObject ? lang('comment on object', substr_utf($this->getText(), 0, 50) . '...', $object->getObjectName()) : $this->getObjectTypeName();
    } // getObjectName
    
    /**
    * Return object type name
    *
    * @param void
    * @return string
    */
    function getObjectTypeName() {
      return lang('page_attachment');
    } // getObjectTypeName
    
    /**
    * Return view attachment URL
    *
    * @param void
    * @return string
    */
    function getObjectUrl() {
      return $this->getViewUrl();
    } // getObjectUrl
    
  } // PageAttachment 

?>
