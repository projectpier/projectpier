<?php

/**
  * @author Alex Mayhew
  * @copyright 2008
  */

class WikiController extends ApplicationController {

  function __construct() {
    parent::__construct();
    prepare_company_website_controller($this, 'project_website');
    $this->addHelper('textile');
  } // __construct

  /**
  * Wiki index
  * 
  * @return void
  */
  function index() {
    // Here we show them the default wiki page
    $page = Wiki::getProjectIndex(active_project());

    if (!instance_of($page, 'WikiPage')) {
      // There isn't a wiki page at the moment
      // to prevent nasty errors, make a temp page
      $page = new WikiPage;
      // Make a revision for the page
      $revision = $page->makeRevision();
      // Fill in the default content
      $revision->setContent(lang('wiki default page content'));
      // Set the name of the page
      $revision->setName(lang('wiki default page name'));
    } else {
      // Fetch the latest revision of the page
      $revision = $page->getLatestRevision();

      if (!instance_of($revision, 'Revision')) {
        // If for some screwy reason there isn't a revision
        flash_error(lang('wiki revision dnx'));
        // Go to the dashboard
        $this->redirectTo();
      } // if
    } // if

    if (!$page->canView(logged_user())) {
      flash_error(lang('no access permissions'));
      $this->redirectTo();
    } // if

    tpl_assign('page', $page);
    tpl_assign('revision', $revision);
    $this->_load_sidebar();
  } // index

  /**
  * Delete a wiki page
  * 
  * @todo TODO Add password confirmation
  * @return void
  */
  function delete() {
    $page = Wiki::getPageById(get_id(), active_project());

    if (!instance_of($page, 'WikiPage')) {
      flash_error(lang('wiki page dnx'));
      $this->redirectTo('wiki');
    } // if

    if (!$page->canDelete(logged_user())) {
      flash_error(lang('no access permissions'));
      $this->redirectToReferer(get_url('wiki'));
    } // if

    // Check that the page isn't locked
    if ($page->isLocked() && !$page->canUnlock(logged_user())) {
      flash_error(lang('wiki page locked by', $page->getLockedByUser()->getUsername()));
      $this->redirectToUrl($page->getViewUrl());
    } // if
    
    $revision = $page->getLatestRevision();

    tpl_assign('page', $page);
    tpl_assign('revision', $revision);

    if (array_var($_POST, 'deleteWikiPage')) {
      try {
        DB::beginWork();

        $page->delete();
        ApplicationLogs::createLog($page, $page->getProject(), ApplicationLogs::ACTION_DELETE);

        DB::commit();				
        flash_success(lang('success delete wiki page'));
      } catch (Exception $e) {
        DB::rollback();
        flash_error(lang('failure delete wiki page', '(' . $e->getMessage() . ')'));
      } // try

      // Redirect to the wiki index either way
      $this->redirectTo('wiki');
    } // if
  } // delete

  /**
    * Loads the sidebar
    * 
    * @return void
    */
  function _load_sidebar() {
    // Quick error / XSS preventor
    if (request_action() == '_load_sidebar') {
      flash_error('no access permissions');
      $this->redirectTo();
    } // if

    // Get Sidebar stuff
    $sidebar_page = Wiki::getProjectSidebar(active_project());
    if (instance_of($sidebar_page, 'WikiPage')) {
      $sidebar_revision = $sidebar_page->getLatestRevision();		
    } else {
      // Make some default content which should help the user set stuff up
      $sidebar_page = new WikiPage;
      $sidebar_revision = new Revision;
      $sidebar_revision->setName(lang('wiki default sidebar name'));
      $sidebar_revision->setContent(lang('wiki default sidebar content'));
      $all_pages = Wiki::getPagesList(active_project());
      tpl_assign('sidebar_links', $all_pages);
    } // if

    tpl_assign('sidebar_page', $sidebar_page);
    tpl_assign('sidebar_revision', $sidebar_revision);

    $this->setSidebar(get_template_path('view_sidebar', 'wiki'));	
  } // _load_sidebar

  /**
  * View a wiki page
  * 
  * @return void
  */
  function view() {
    $page = Wiki::getPageById(get_id(), active_project());

    if (!instance_of($page, 'WikiPage')) {
      flash_error(lang('wiki page dnx'));
      $this->redirectTo('wiki');
    }

    if (!$page->canView(logged_user())) {
      flash_error(lang('no access permissions'));
      $this->redirectTo(get_url('wiki'));
    } // if

    // Get the revision the user wants. defaults to latest 
    $revision = $page->getRevision(array_var($_GET, 'revision'));

    if (!$revision instanceof Revision) {
      flash_error(lang('wiki revision dnx'));
      $this->redirectTo('wiki');
    } // if

    tpl_assign('iscurrev', (!(bool) array_var($_GET, 'revision', false)));
    tpl_assign('page', $page);
    tpl_assign('revision', $revision);
    $this->setTemplate('view');

    // Get Sidebar stuff
    $this->_load_sidebar();
  } // view

  /**
  * Add a wiki page
  *
  * @return void
  */
  function add() {
    $page = new WikiPage;

    if (!WikiPage::canAdd(logged_user(), active_project())) {
      flash_error(lang('no access permissions'));
      $this->redirectTo('wiki');
    } // if

    if (false !== ($data = array_var($_POST, 'wiki', false))) {
      $page->setProjectId(active_project()->getId());
      $page->setProjectIndex((logged_user()->isMemberOfOwnerCompany() ? $data['project_index'] : 0));
      $page->setProjectSidebar((logged_user()->isMemberOfOwnerCompany() ? $data['project_sidebar'] : 0));

      $revision = $page->makeRevision();
      
      // Check to see if we want to lock this page
      if (isset($data['locked'])) {
        if ($data['locked'] == 1 && $page->canLock(logged_user())) {
          // If we want to lock this page and the user has permissions to lock it, and the page is not already locked
          $page->setLocked(true);
          $page->setLockedById(logged_user()->getId());
          $page->setLockedOn(DateTimeValueLib::now());
        } // if
      } // if

      $revision->setFromAttributes($data);
      $revision->setCreatedbyId(logged_user()->getId());

      try {
        DB::beginWork();				
        $page->save();
        ApplicationLogs::createLog($page, active_project(), ApplicationLogs::ACTION_ADD);

        DB::commit();
        flash_success(lang('success add wiki page'));

        $this->redirectToUrl($page->getViewUrl());
      } catch (Exception $e) {
        DB::rollback();
        tpl_assign('error', $e);
      } // try
    } else {
      $revision = $page->makeRevision();
      $revision->setName(array_var($_GET, 'name', ''));
    } // if

    tpl_assign('page', $page);
    tpl_assign('revision', (isset($revision) && ($revision instanceof Revision) ? $revision : $page->makeRevision()));
    $this->setTemplate('edit');
    $this->setSidebar(get_template_path('textile_help_sidebar'));

  } // add

  /**
  * Edit a wiki page
  * 
  * @return void
  */
  function edit() {
    $page = Wiki::getPageById(get_id(), active_project());

    if (!($page instanceof WikiPage)) {
      flash_error(lang('wiki page dnx'));
      $this->redirectToReferer(get_url('wiki'));
    } // if

    // Check that the page isn't locked
    if ($page->isLocked() && !$page->canUnlock(logged_user())) {
      flash_error(lang('wiki page locked by', $page->getLockedByUser()->getUsername()));
      $this->redirectToUrl($page->getViewUrl());
    } // if

    if (!$page->canEdit(logged_user())) {
      flash_error(lang('no wiki page edit permissions'));
      $this->redirectToUrl(($page->isProjectIndex() ? get_url('wiki') : $page->getViewUrl()));
    } // if

    if (null !== ($data = array_var($_POST, 'wiki'))) {
      $revision = $page->makeRevision();
      $revision->setFromAttributes($data);

      $page->setProjectIndex($data['project_index']);
      $page->setProjectSidebar($data['project_sidebar']);

      // Check to see if we want to lock this page
      if (isset($data['locked'])) {
        if ($data['locked'] == 1 && $page->canLock(logged_user()) && !$page->isLocked()) {
          // If we want to lock this page and the user has permissions to lock it, and the page is not already locked
          $page->setLocked(true);
          $page->setLockedById(logged_user()->getId());
          $page->setLockedOn(DateTimeValueLib::now());
        } elseif ($data['locked'] == 0 & $page->canUnlock(logged_user()) && $page->isLocked()) {
          // Else if we want to unlock the page, and the user is allowed to, and the page is locked
          $page->setLocked(false);
        } // if
      } // if

      $revision->setCreatedById(logged_user()->getId());

      try {
        DB::beginWork();

        // Save the page and create revision
        // The page will make sure that the revision's project and page Id are correct 
        $page->save();

        ApplicationLogs::createLog($page, active_project(), ApplicationLogs::ACTION_EDIT);

        DB::commit();

        flash_success(lang('success edit wiki page'));

        $this->redirectToUrl($page->getViewUrl());
      } catch (Exception $e) {
        DB::rollback();
        tpl_assign('error', $e);
      } // try
    } elseif (array_var($_GET, 'revision')) {
      // If we want to make a new revision based off a revision
      $revision = $page->getRevision($_GET['revision']);
    } else {
      $revision = $page->getLatestRevision();
    } // if

    tpl_assign('revision', $revision);
    tpl_assign('page', $page);
    $this->setTemplate('edit');
    $this->setSidebar(get_template_path('textile_help_sidebar'));
  } // edit

  /**
  * View the revision history of a page
  * 
  * @return void
  */
  function history() {
    $page = Wiki::getPageById(get_id(), active_project());

    if (!($page instanceof WikiPage)) {
      flash_error('wiki page dnx');
      $this->redirectTo('wiki');
    } // if

    if (!$page->canView(logged_user())) {
      // If the user can't view a page, then they have no business looking at it's revisions :p
      flash_error('no access permissions');
      // Redirect to dashboard
      $this->redirectTo();
    } // if

    // Work out the page we are on
    $pnum = (integer) array_var($_GET, 'page', 1);
    if ($pnum < 0) {
      $pnum = 1;
    } // if

    // Get the revisions for this page
    list($revisions, $pagination) = $page->paginateRevisions(array(), 30, $pnum);

    // Assign template variables
    tpl_assign('page', $page);
    tpl_assign('cur_revision', $page->getLatestRevision());
    tpl_assign('revisions', $revisions);
    tpl_assign('pagination', $pagination);

    // Load the wiki sidebar
    $this->_load_sidebar();

  } // history

  /**
  * Reverts to a wiki page
  * 
  * @param void
  * @return null
  */
  function revert() {
    $page = Wiki::getPageById(get_id(), active_project());

    if (!instance_of($page, 'WikiPage')) {
      flash_error(lang('wiki page dnx'));
      $this->redirectTo('wiki');
    } // if

    if (!$page->canEdit(logged_user())) {
      flash_error(lang('no access permissions'));
      $this->redirectTo('wiki');
    } // if

    $old_revision = $page->getRevision(array_var($_GET, 'revision', -1));

    if (!($old_revision instanceof Revision)) {
      flash_error(lang('wiki page revision dnx'));
      $this->redirectTo('wiki');
    } // if

    $new_revision = $page->makeRevision();

    $new_revision->setContent($old_revision->getContent());
    $new_revision->setName($old_revision->getName());
    $new_revision->setLogMessage(lang('wiki page revision restored from', $old_revision->getRevision()));

    try {
      DB::beginWork();
      $page->save();
      DB::commit();
      flash_success(lang('success restore wiki page revision'));
      $this->redirectToUrl($page->getViewUrl());
    } catch (Exception $e) {
      DB::rollback();
      flash_error(lang('failure restore wiki page revision', $e->getMessage()));
      $this->redirectTo('wiki');
    } // try

  } // revert

  /**
  * Displays diff of two revisions
  *
  * @param void
  * @return null
  */
  function diff() {
    $page = Wiki::getPageById(get_id(), active_project());

    if (!($page instanceof WikiPage)) {
      flash_error('wiki page dnx');
      $this->redirectTo('wiki');
    } // if

    if (!$page->canView(logged_user())) {
      flash_error('no access permissions');
      $this->redirectTo('wiki');
    } // if

    $rev1 = $page->getRevision(array_var($_GET, 'rev1', -1));
    $rev2 = $page->getRevision(array_var($_GET, 'rev2', -1));

    if (!($rev1 instanceof Revision) || !($rev2 instanceof Revision)) {
      flash_error(lang('wiki page revision dnx'));
      $this->redirectTo('wiki');
    } // if

    if ($rev1->getId() == $rev2->getId()) {
      flash_error(lang('wiki no compare identical'));
      $this->redirectToReferer($rev1->getViewUrl());
    } // if
    
    $this->addHelper('textile');

    // Load text diff library
    Env::useLibrary('diff');

    $diff = new diff($rev1->getContent(), $rev2->getContent());

    $output = new diff_renderer_inline();

    // If there are no visible changes, then tell the user, as opposed to white screening them
    $output = trim($output = $output->render($diff)) == '' ? lang('wiki no visible changes') : $output;

    tpl_assign('diff', $output);
    tpl_assign('page', $page);
    tpl_assign('revision', $page->getLatestRevision());
    tpl_assign('rev1', $rev1);
    tpl_assign('rev2', $rev2);

  } // diff
  
  /**
    * View all wiki pages
    * 
    * @return void
    */
  function all_pages() {
    // There isn't a wiki page for all pages
    $page = new WikiPage;
    // Make a revision for the page
    $revision = $page->makeRevision();
    $revision->setName(lang('wiki all pages'));

    $all_pages = Wiki::getPagesList(active_project());
    tpl_assign('all_pages', $all_pages);
    tpl_assign('page', $page);
    tpl_assign('revision', $revision);
    $this->_load_sidebar();
  } // all_pages
 
} // WikiController

?>