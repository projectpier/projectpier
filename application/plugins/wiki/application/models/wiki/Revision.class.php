<?php

/**
 * Revision
 * 
 * @package ProjectPier Wiki
 * @author Alex Mayhew
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class Revision extends BaseRevision {
  
  /**
  * Get project
  *
  * @param void
  * @return Project
  */
  function getProject() {
    return Projects::findById($this->getProjectId());
  } // getProject
  
  /**
  * Get associated WikiPage object
  *
  * @param void
  * @return WikiPage
  */
  function getPage() {
    return Wiki::getPageById($this->getPageId(), $this->getProject());
  } // getPage

  /**
  * Get url to revert to this revision
  * 
  * @param void
  * @return string
  */
  function getRevertUrl() {
    return get_url('wiki', 'revert', array('id' => $this->getPageId(), 'revision' => $this->getRevision()));
  } // getRevertUrl
		
  /**
  * Get url to view this revision
  * 
  * @param void
  * @return string
  */
  function getViewUrl() {
    return get_url('wiki', 'view', array('id' => $this->getPageId(), 'revision' => $this->getRevision()));
  } // getViewUrl

  
  /**
  * Validate before save
  *
  * @access public
  * @param array $errors
  * @return null
  */
  function validate(&$errors) {
    if (!$this->validatePresenceOf('name')) { 
      $errors[] = lang('wiki page name required');
    } // if
    if (!$this->validatePresenceOf('content')) {
      $errors[] = lang('wiki page content required');
    } // if
    if (!$this->validatePresenceOf('project_id')) {
      $errors[] = lang('wiki project id required');
    } // if
    if (!$this->validatePresenceOf('page_id')) {
      $errors[] = lang('wiki page id required');
    } // if
  } // validate

} // Revision

?>
