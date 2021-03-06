<?php

/**
 * WikiPage
 * 
 * @package ProjectPier Wiki
 * @author Alex Mayhew
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class WikiPage extends BaseWikiPage {

  /**
    * Cache of specific revisions
    * 
    * @var array
    */ 
  protected $revisions = array();

  /**
    * Cache of the current revision
    * 
    * @var object
    */
  protected $cur_revision;

  /**
    * object for the latest wiki revision
    * 
    * @var object
    */
  protected $new_revision;

  /**
    * We want to be able to tag this object
    * 
    * @var boolean
    */
  protected $is_taggable 	= true;

  /**
    * We want the user to be able to search wiki pages
    * 
    * @var boolean 
    */
  protected $is_searchable = true;

  /**
    * We want to search the content and title columns
    * 
    * @var array
    */
  protected $searchable_columns = array('name', 'content');

  /**
    * Holds the project object for this wiki page
    * 
    * @var object
    */

  protected $project;


  //////////////////////////////////////////
  //	Permissions
  //////////////////////////////////////////

  /**
  * Can the user add a wiki page?
  * 
  * @param User $user
  * @param Project $project
  * @return boolean
  */
  function canAdd(User $user, Project $project) {
    return $user->isAdministrator() || $user->isMemberOfOwnerCompany();
  } // canAdd

  /**
  * Can the user edit this page
  * 
  * @param User $user
  * @return boolean
  */
  function canEdit(User $user) {
    return $user->isAdministrator() || $user->isMemberOfOwnerCompany();
  } // canEdit

  /**
  * Can the user delete this page
  * 
  * @param User $user
  * @return boolean
  */
  function canDelete(User $user) {
    return $user->isAdministrator();
  } // canDelete

  /**
    * Can the user lock this page
    * 
    * @param User $user
    * @return boolean
    */
  function canLock(User $user) {
    // Only admins can lock a page
    return $user->isAdministrator();
  } // canLock

  /**
    * Can the user unlock this page
    * 
    * @param User $user
    * @return boolean
    */
  function canUnlock(User $user) {
    // Only admins can unlock a page
    return $user->isAdministrator();
  } // canUnlock
 
  /**
  * Can the user view this page
  * 
  * @param mixed $user
  * @return
  */
  function canView(User $user) {
    return $user->isProjectUser($this->getProject());
  } // canView

  //////////////////////////////////////////
  //	Urls
  //////////////////////////////////////////

  /**
  * Get url to the add wiki page
  * 
  * @return string
  */
  function getAddUrl() {
    return $this->makeUrl('add', array('active_project' => active_project()->getId()), false);
  } // getAddUrl
	
  /**
  * Get url to edit this wiki page
  * 
  * @return string
  */
  function getEditUrl() {
    return $this->makeUrl('edit');
  } // getEditUrl

  /**
  * Get url to delete this wiki page
  * 
  * @return string
  */
  function getDeleteUrl() {
    return $this->makeUrl('delete');
  } // getDeleteUrl

  /**
  * Get url to view page's revision history
  * 
  * @return string
  */
  function getViewHistoryUrl() {
    return $this->makeUrl('history');
  } // getViewHistoryUrl

  /**
  * Get url to view this wiki page
  * 
  * @return string
  */
  function getViewUrl() {
    return $this->makeUrl('view');
  } // getViewUrl

  /**
  * Get url to all wiki pages
  * 
  * @return string
  */
  function getAllPagesUrl() {
    return $this->makeUrl('all_pages', array('active_project' => active_project()->getId()), false);
  } // getAllPagesUrl
  
  /**
  * Generic function to make a url to a wiki page
  * 
  * @param string The action of the target page(e.g. view, delete etc.)
  * @param mixed Optional array of params 
  * @param bool Include the page id? Defaults true
  * @return
  */
  function makeUrl($action = 'index', $params = array(), $include_page_id = true) {
    // Merge params with the wiki page id
    $params = 	$include_page_id ?
      array_merge(array('id' => $this->getId(), 'active_project' => $this->getProjectId()), $params) :
      array_merge(array('active_project' => active_project()->getId()), $params);
    
    return get_url('wiki', $action, $params);
  } // makeUrl

  //////////////////////////////////////////
  //	Revisions
  //////////////////////////////////////////

  /**
  * Get a specific revision
  * 
  * @param mixed $revision
  * @return mixed
  */
  function getRevision($revision = null) {
    if ($revision == null && instance_of($this->cur_revision, 'Revision')) {
			return $this->cur_revision;
    } else if (isset($this->revisions[$revision])) {
      return $this->revisions[$revision];
    } else if ($revision === null) {
      // Update and return cache of latest revision
      $this->cur_revision = Revisions::getRevision($this->getId(), $revision);

      // Make another cache of it
      $this->revisions[$this->cur_revision->getId()] = $this->cur_revision;

      return $this->cur_revision;
    } else {
      // Cache and return the revision
      $revision = (int) $revision;
      return $this->revisions[$revision] = Revisions::getRevision($this->getId(), $revision);
		} // if

  } // getRevision

  /**
  * Get the latest revision of this page
  * 
  * @return mixed
  */
  function getLatestRevision() {
    return $this->getRevision(null);
  } // getLatestRevision

  /**
  * Makes a new revision of this page
  * 
  * @return Revision object
  */
  function makeRevision() {
    // Make a new revision
    $this->new_revision = new Revision;
    // Set the project ID
    $this->new_revision->setProjectId($this->getProjectId());

    // Return a reference to the revision
    return $this->new_revision;
  } // makeRevision

  //////////////////////////////////////////
  //	System
  //////////////////////////////////////////

  /**
  * Delete page & its revisions
  * 
  * @return
  */
  function delete() {
    $revisions = (array) Revisions::buildPageHistory($this->getId(), $this->getProject());
    foreach ($revisions as $revision) {
			$revision->delete();
    } // foreach
    return parent::delete();
  } // delete

  /**
  * Return object type name
  *
  * @param void
  * @return string
  */
  function getObjectTypeName() {
    return lang('wiki');
  } // getObjectTypeName

  /**
  * Get page name
  * 
  * @return
  */
  function getObjectName() {
    return instance_of($this->new_revision, 'Revision') ? $this->new_revision->getName() : $this->getLatestRevision()->getName();
  } // getObjectName

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

  /**
  * This function will return paginated result. Result is an array where first element is 
  * array of returned object and second populated pagination object that can be used for 
  * obtaining and rendering pagination data using various helpers.
  * 
  * Items and pagination array vars are indexed with 0 for items and 1 for pagination
  * because you can't use associative indexing with list() construct
  *
  * @access public
  * @param array $arguments Query arguments (@see find()) Limit and offset are ignored!
  * @param integer $items_per_page Number of items per page
  * @param integer $current_page Current page number
  * @return array
  */
  function paginateRevisions($arguments = array(), $items_per_page = 10, $current_page = 1) {

    if (is_array($arguments) && !isset($arguments['conditions'])) {
      $arguments['conditions'] = array('`project_id` = ? AND `page_id` = ?', $this->getProjectId(), $this->getId());
    } // if
    if (is_array($arguments) && !isset($arguments['order'])) {
      $arguments['order'] = '`revision` DESC';
    } // if

    return Revisions::instance()->paginate($arguments, $items_per_page, $current_page);
  } // paginate
  
  //////////////////////////////////////////
  //	Lockage
  //////////////////////////////////////////
  /**
    * Get the user object for the user which locked this page
    * 
    * Returns null if user DNX or page is not locked
    * 
    * @return
    */
  function getLockedByUser() {
    // Cache the user object
    static $user = null;
    return $this->getLocked() ? 
      // If the page is locked 
      (($user instanceof User) ? 
      // If we have cached the user's object
      $user : 
      // Else find it and cache it
      ($user = Users::findById($this->getLockedById()))) :
      // If the page is not locked, return null	
      null; 
 	} // getLockedByUser

  function isLocked() {
    return (bool) $this->getColumnValue('locked');
  } // isLocked

} // WikiPage

?>