<?php

  /**
  * ProjectFolder class
  * Generated on Tue, 04 Jul 2006 06:46:08 +0200 by DataObject generation tool
  *
  * @http://www.projectpier.org/
  */
  class ProjectFolder extends BaseProjectFolder {
    
    /**
    * Cached array of all folder files
    *
    * @var array
    */
    private $all_files;
    
    /**
    * Cached array of folder files filtered by user permissions
    *
    * @var array
    */
    private $files;
    
    /**
    * Return all project files
    *
    * @param void
    * @return array
    */
    function getAllFiles() {
      if (is_null($this->all_files)) {
        $this->all_files = ProjectFiles::getByFolder($this, true);
      } // if
      return $this->all_files;
    } // getAllFiles
    
    /**
    * Return files
    *
    * @param void
    * @return array
    */
    function getFiles() {
      if (is_null($this->files)) {
        $this->files = ProjectFiles::getByFolder($this, logged_user()->isMemberOfOwnerCompany());
      } // if
      return $this->files;
    } // getFiles
  
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
    * Return browse URL
    *
    * @param string $order_by
    * @param integer $page
    * @return string
    */
    function getBrowseUrl($order_by = null, $page = null) {
      
      // If page and order are not set use defaults
      if (($order_by <> ProjectFiles::ORDER_BY_NAME) && ($order_by <> ProjectFiles::ORDER_BY_POSTTIME)) {
        $order_by = ProjectFiles::ORDER_BY_POSTTIME;
      } // if
      
      // #PAGE# is reserved as a placeholder
      if ($page <> '#PAGE#') {
        $page = (integer) $page > 0 ? (integer) $page : 1;
      } // if
      
      return get_url('files', 'browse_folder', array(
        'id' => $this->getId(), 
        'active_project' => $this->getProjectId(),
        'order' => $order_by,
        'page' => $page
      )); // array
    } // getBrowseUrl
    
    /**
    * Return add file URL
    *
    * @param void
    * @return string
    */
    function getAddFileUrl() {
      return get_url('files', 'add_file', array(
        'folder_id' => $this->getId(), 
        'active_project' => $this->getProjectId()
      )); // array
    } // getAddFileUrl
    
    /**
    * Return edit folder URL
    *
    * @param void
    * @return string
    */
    function getEditUrl() {
      return get_url('files', 'edit_folder', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getEditUrl
    
    /**
    * Return delete folder URL
    *
    * @param void
    * @return string
    */
    function getDeleteUrl() {
      return get_url('files', 'delete_folder', array('id' => $this->getId(), 'active_project' => $this->getProjectId()));
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
    * Check CAN_MANAGE_MESSAGES permission
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canManage(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false;
      }
      return $user->getProjectPermission($this->getProject(), ProjectUsers::CAN_MANAGE_FILES);
    } // canManage
    
    /**
    * Empty implementation of abstract method. Message determins if user have view access
    *
    * @param void
    * @return boolean
    */
    function canView(User $user) {
      return $user->isProjectUser($this->getProject());
    } // canView
    
    /**
    * Empty implementation of abstract methods. Messages determine does user have
    * permissions to add comment
    *
    * @param void
    * @return null
    */
    function canAdd(User $user, Project $project) {
      if (!$user->isProjectUser($project)) {
        return false;
      }
      return $user->getProjectPermission($project, ProjectUsers::CAN_MANAGE_FILES);
    } // canAdd
    
    /**
    * Check if specific user can edit this file
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canEdit(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false;
      }
      return $user->isAdministrator() || $this->canManage($user);
    } // canEdit
    
    /**
    * Check if specific user can delete this comment
    *
    * @access public
    * @param User $user
    * @return boolean
    */
    function canDelete(User $user) {
      if (!$user->isProjectUser($this->getProject())) {
        return false;
      }
      return $user->isAdministrator() || $this->canManage($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
    * Validate before save
    *
    * @param array $errors
    * @return null
    */
    function validate(&$errors) {
      if ($this->validatePresenceOf('name')) {
        if (!$this->validateUniquenessOf('name', 'project_id')) {
          $errors[] = lang('folder name unique');
        }
      } else {
        $errors[] = lang('folder name required');
      } // if
    } // validate
    
    /**
    * Delete this folder
    *
    * @param void
    * @return boolean
    */
    function delete() {
      $files = $this->getAllFiles();
      if (is_array($files)) {
        foreach ($files as $file) {
          $file->delete();
        }
      } // if
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
      return $this->getName();
    } // getObjectName
    
    /**
    * Return object type name
    *
    * @param void
    * @return string
    */
    function getObjectTypeName() {
      return lang('folder');
    } // getObjectTypeName
    
    /**
    * Return object URl
    *
    * @access public
    * @param void
    * @return string
    */
    function getObjectUrl() {
      return $this->getBrowseUrl();
    } // getObjectUrl
    
  } // ProjectFolder 

?>
