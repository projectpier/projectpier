<?php

  /**
  * Project controller
  *
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class ProjectController extends ApplicationController {
    
    /**
    * Prepare this controller
    *
    * @param void
    * @return ProjectController
    */
    function __construct() {
      parent::__construct();
      prepare_company_website_controller($this, 'project_website');
    } // __construct
    
    /**
    * Call overview action
    *
    * @param void
    * @return null
    */
    function index() {
      $this->forward('overview');
    } // index
    
    /**
    * Show project overview
    *
    * @param void
    * @return null
    */
    function overview() {
      if (!logged_user()->isProjectUser(active_project())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if
      
      $this->addHelper('textile');
      
      $project = active_project();
      
      $page_attachments = PageAttachments::getAttachmentsByPageNameAndProject('project_overview', active_project());
      
      $this->setLayout('project_website');
      tpl_assign('page_attachments', $page_attachments);
      tpl_assign('project_log_entries', $project->getProjectLog(
        config_option('project_logs_per_page', 20)
      ));
      tpl_assign('late_milestones', $project->getLateMilestones());
      tpl_assign('today_milestones', $project->getTodayMilestones());
      tpl_assign('upcoming_milestones', $project->getUpcomingMilestones());
      
      // Sidebar
      tpl_assign('visible_forms', $project->getVisibleForms(true));
      tpl_assign('project_companies', $project->getCompanies());
      tpl_assign('important_messages', active_project()->getImportantMessages());
      tpl_assign('important_files', active_project()->getImportantFiles());
      
      $this->setSidebar(get_template_path('overview_sidebar', 'project'));
    } // overview
    
    /**
    * Execute search
    *
    * @param void
    * @return null
    */
    function search() {
      if (!logged_user()->isProjectUser(active_project())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if

      $search_for = array_var($_GET, 'search_for');
      $page = (integer) array_var($_GET, 'page', 1);
      if ($page < 1) {
        $page = 1;
      }
      
      if (trim($search_for) == '') {
        $search_results = null;
        $pagination = null;
      } else {
        list($search_results, $pagination) = SearchableObjects::searchPaginated($search_for, active_project(), logged_user()->isMemberOfOwnerCompany(), 10, $page);
      } // if
      
      tpl_assign('search_string', $search_for);
      tpl_assign('current_page', $page);
      tpl_assign('search_results', $search_results);
      tpl_assign('pagination', $pagination);
      
      tpl_assign('tag_names', active_project()->getTagNames());
      $this->setSidebar(get_template_path('search_sidebar', 'project'));
    } // search
    
    /**
    * Show tags page
    *
    * @param void
    * @return null
    */
    function tags() {
      if (!logged_user()->isProjectUser(active_project())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('dashboard');
      } // if
      
      tpl_assign('tag_names', active_project()->getTagNames());
    } // tags
    
    /**
    * List all companies and contacts involved in this project
    *
    * @param void
    * @return null
    */
    function people() {
      $page_attachments = PageAttachments::getAttachmentsByTypeAndProject(array('Contacts', 'Companies'), active_project());
      tpl_assign('page_attachments', $page_attachments);
      tpl_assign('project', active_project());
    } // people
    
    /**
    * Add project
    *
    * @param void
    * @return null
    */
    function add() {
      $this->setTemplate('add_project');
      $this->setLayout('administration');
      
      if (!Project::canAdd(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('dashboard'));
      } // if
      
      $project = new Project();
      
      $project_data = array_var($_POST, 'project');
      tpl_assign('project', $project);
      tpl_assign('project_data', $project_data);
      
      // Submited...
      if (is_array($project_data)) {
        $project->setFromAttributes($project_data);
        
        $default_folders_config = str_replace(array("\r\n", "\r"), array("\n", "\n"), config_option('default_project_folders', ''));
        if (trim($default_folders_config) == '') {
          $default_folders = array();
        } else {
          $default_folders = explode("\n", $default_folders_config);
        } // if
        
        try {
          DB::beginWork();
          $project->save();
          
          $permissions = ProjectUsers::getPermissionColumns();
          $auto_assign_users = owner_company()->getAutoAssignUsers();
          
          // We are getting the list of auto assign users. If current user is not in the list
          // add it. He's creating the project after all...
          if (is_array($auto_assign_users)) {
            $auto_assign_logged_user = false;
            foreach ($auto_assign_users as $user) {
              if ($user->getId() == logged_user()->getId()) {
                $auto_assign_logged_user = true;
              }
            } // if
            if (!$auto_assign_logged_user) {
              $auto_assign_users[] = logged_user();
            }
          } else {
            $auto_assign_users[] = logged_user();
          } // if
          
          foreach ($auto_assign_users as $user) {
            $project_user = new ProjectUser();
            $project_user->setProjectId($project->getId());
            $project_user->setUserId($user->getId());
            if (is_array($permissions)) {
              foreach ($permissions as $permission) {
                $project_user->setColumnValue($permission, true);
              }
            } // if
            $project_user->save();
          } // foreach
          
          if (count($default_folders)) {
            $added_folders = array();
            foreach ($default_folders as $default_folder) {
              $folder_name = trim($default_folder);
              if ($folder_name == '') {
                continue;
              } // if
              
              if (in_array($folder_name, $added_folders)) {
                continue;
              } // if
              
              $folder = new ProjectFolder();
              $folder->setProjectId($project->getId());
              $folder->setName($folder_name);
              $folder->save();
              
              $added_folders[] = $folder_name;
            } // foreach
          } // if
          
          ApplicationLogs::createLog($project, null, ApplicationLogs::ACTION_ADD, false, true);
          DB::commit();
          
          flash_success(lang('success add project', $project->getName()));
          $this->redirectToUrl($project->getPermissionsUrl());
          
        } catch(Exception $e) {
          tpl_assign('error', $e);
          DB::rollback();
        } // try
        
      } // if
      
    } // add
    
    /**
    * Edit project
    *
    * @param void
    * @return null
    */
    function edit() {
      // TODO find a more elegant solution for this parameter
      $page_name = 'project_overview';
      $this->setTemplate('add_project');
      $this->setLayout('administration');
      $this->setSidebar(get_template_path('textile_help_sidebar'));
      
      $project = Projects::findById(get_id());
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('administration', 'projects');
      } // if
      
      if (!$project->canEdit(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('administration', 'projects'));
      } // if
      
      $project_data = array_var($_POST, 'project');
      if (!is_array($project_data)) {
        $project_data = array(
          'name' => $project->getName(),
          'description' => $project->getDescription(),
          'show_description_in_overview' => $project->getShowDescriptionInOverview()
        ); // array
      } // if
      
      $page_attachments = PageAttachments::getAttachmentsByPageNameAndProject($page_name, $project);
      
      tpl_assign('project', $project);
      tpl_assign('project_data', $project_data);
      tpl_assign('page_attachments', $page_attachments);
      
      if (is_array(array_var($_POST, 'project'))) {
        $project->setFromAttributes($project_data);
        
        
        try {
          DB::beginWork();
          $project->save();
          ApplicationLogs::createLog($project, null, ApplicationLogs::ACTION_EDIT, false, true);
          
          $page_attachments = $project_data['page_attachments'];
          if (is_array($page_attachments)) {
            foreach ($page_attachments as $id => $page_attachment_data) {
              $page_attachment = PageAttachments::findById($id);
              if (($page_attachment_data['rel_object_manager'] != '' && $page_attachment_data['rel_object_id'] == 0) || $page_attachment_data['delete'] == "checked") {
                $page_attachment->delete();
              } else {
                $page_attachment->setFromAttributes($page_attachment_data);
                $page_attachment->save();
              } // if
            } // foreach
            PageAttachments::reorder($page_name, $project);
          } // if
          DB::commit();
          
          flash_success(lang('success edit project', $project->getName()));
          $this->redirectToUrl($project->getOverviewUrl());
        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
      } // if
    } // edit
    
    /**
    * Delete project
    *
    * @param void
    * @return null
    */
    function delete() {
      $this->setTemplate('del_project');
      $this->setLayout('administration');

      $project = Projects::findById(get_id());
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('administration', 'projects');
      } // if
      
      if (!$project->canDelete(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('administration', 'projects'));
      } // if

      $delete_data = array_var($_POST, 'deleteProject');
      tpl_assign('project', $project);
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
          $this->render();
        }
        if (!logged_user()->isValidPassword($password)) {
          tpl_assign('error', new Error(lang('invalid password')));
          $this->render();
        } // if
        try {

          DB::beginWork();
          $project->delete();
          CompanyWebsite::instance()->setProject(null);
          ApplicationLogs::createLog($project, null, ApplicationLogs::ACTION_DELETE);
          DB::commit();

          flash_success(lang('success delete project', $project->getName()));

        } catch(Exception $e) {
          DB::rollback();
          flash_error(lang('error delete project'));
        } // try

        $this->redirectTo('administration', 'projects');
      } else {
        flash_error(lang('error delete project'));
        $this->redirectTo('administration', 'projects');
      }
    } // delete
    
    /**
    * Complete this project
    *
    * @param void
    * @return null
    */
    function complete() {
      
      $project = Projects::findById(get_id());
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('administration', 'projects');
      } // if
      
      if (!$project->canChangeStatus(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('administration', 'projects'));
      } // if
      
      try {
        
        $project->setCompletedOn(DateTimeValueLib::now());
        $project->setCompletedById(logged_user()->getId());
        
        DB::beginWork();
        $project->save();
        ApplicationLogs::createLog($project, null, ApplicationLogs::ACTION_CLOSE);
        DB::commit();
        
        flash_success(lang('success complete project', $project->getName()));
        
      } catch(Exception $e) {
        DB::rollback();
        flash_error(lang('error complete project'));
      } // try
      
      $this->redirectToReferer(get_url('administration', 'projects'));
    } // complete
    
    /**
    * Reopen project
    *
    * @param void
    * @return null
    */
    function open() {
      $project = Projects::findById(get_id());
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('administration', 'projects');
      } // if
      
      if (!$project->canChangeStatus(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('administration', 'projects'));
      } // if
      
      try {
        
        $project->setCompletedOn(null);
        $project->setCompletedById(0);
        
        DB::beginWork();
        $project->save();
        ApplicationLogs::createLog($project, null, ApplicationLogs::ACTION_OPEN);
        DB::commit();
        
        flash_success(lang('success open project', $project->getName()));
        
      } catch(Exception $e) {
        DB::rollback();
        flash_error(lang('error open project'));
      } // try
      
      $this->redirectToReferer(get_url('administration', 'projects'));
    } // open
    
    
    /**
    * Adds contact to project (as a PageAttachment)
    *
    * @param void
    * @return null
    */
    function add_contact() {
      if (!active_project()->canChangePermissions(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(active_project()->getOverviewUrl());
      } // if
      
      $already_attached_contacts = PageAttachments::getAttachmentsByTypeAndProject(array('Contacts'), active_project());
      $already_attached_contacts_ids = null;
      if (is_array($already_attached_contacts)) {
        $already_attached_contacts_ids = array();
        foreach ($already_attached_contacts as $already_attached_contact) {
          $already_attached_contacts_ids[] = $already_attached_contact->getRelObjectId();
        } // foreach
      } // if
      
      $this->setTemplate('add_contact', 'project');
      
      $contact = new Contact();
      
      $im_types = ImTypes::findAll(array('order' => '`id`'));

      $contact_data = array_var($_POST, 'contact');
      if (!is_array($contact_data)) {
        $contact_data = array(); // array
      } // if
      
      tpl_assign('already_attached_contacts_ids', $already_attached_contacts_ids);
      tpl_assign('contact', $contact);
      tpl_assign('contact_data', $contact_data);
      tpl_assign('im_types', $im_types);
      tpl_assign('project', active_project());

      if (is_array(array_var($_POST, 'contact'))) {
        if ($_POST['contact']['what'] == 'existing') {
          $page_attachment = new PageAttachment();
          $page_attachment->setFromAttributes($contact_data['existing']);
          $page_attachment->setProjectId(active_project());
          $page_attachment->setPageName('people');
          $page_attachment->save();
          PageAttachments::reorder('people', active_project());
          flash_success(lang('success add contact', $page_attachment->getObject()->getDisplayName()));
          $this->redirectToUrl(get_url('project', 'people', active_project()));
        } else {
          // Save avatar
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
          
          $contact_data = $contact_data['new'];
          $contact->setFromAttributes($contact_data);
          try {
            DB::beginWork();
            $contact->save();

            $contact->clearImValues();
            foreach ($im_types as $im_type) {
              $value = trim(array_var($contact_data, 'im_' . $im_type->getId()));
              if ($value <> '') {

                $contact_im_value = new ContactImValue();

                $contact_im_value->setContactId($contact->getId());
                $contact_im_value->setImTypeId($im_type->getId());
                $contact_im_value->setValue($value);
                $contact_im_value->setIsDefault(array_var($contact_data, 'default_im') == $im_type->getId());

                $contact_im_value->save();
              } // if
            } // foreach

            ApplicationLogs::createLog($contact, null, ApplicationLogs::ACTION_ADD);
            
            $page_attachment = new PageAttachment();
            $page_attachment->setFromAttributes($contact_data);
            $page_attachment->setRelObjectId($contact->getId());
            $page_attachment->setProjectId(active_project());
            $page_attachment->setPageName('people');
            $page_attachment->save();
            PageAttachments::reorder('people', active_project());

            DB::commit();

            flash_success(lang('success add contact', $contact->getDisplayName()));
            $this->redirectToUrl(get_url('project', 'people', active_project()));

          } catch (Exception $e) {
            DB::rollback();
            tpl_assign('error', $e);
          } // try
          
        } // if

      } // if

    } // add_contact
    
    /**
    * Remove contact from project
    *
    * @param void
    * @return null
    */
    function remove_contact() {
      if (!active_project()->canChangePermissions(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(active_project()->getOverviewUrl());
      } // if
      
      $rel_object_manager = array_var($_GET, 'rel_object_manager', 'Contacts');
      
      $rel_object_id = array_var($_GET, 'rel_object_id');
      $contact = Contacts::findById($rel_object_id);
      if (!($contact instanceof Contact)) {
        flash_error(lang('contact dnx'));
        $this->redirectTo('project', 'people');
      } // if

      $project_id = array_var($_GET, 'project_id', active_project());
      $project = Projects::findById(get_id('project_id'));
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('project', 'people');
      } // if
      
      $page_attachments = PageAttachments::getAttachmentsByManagerIdAndProject($rel_object_manager, $rel_object_id, $project_id);
      foreach ($page_attachments as $page_attachment) {
        try {
          $page_attachment->delete();
          flash_success(lang('success remove contact from project'));
        } catch (Exception $e) {
          flash_error(lang('error remove contact from project'));
        } // try
      } // foreach
      
      $this->redirectTo('project', 'people');
      
    } // remove_contact
    
    /**
    * Remove user from project
    *
    * @param void
    * @return null
    */
    function remove_user() {
      if (!active_project()->canChangePermissions(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(active_project()->getOverviewUrl());
      } // if
      
      $user = Users::findById(get_id('user_id'));
      if (!($user instanceof User)) {
        flash_error(lang('user dnx'));
        $this->redirectTo('project', 'permissions');
      } // if
      
      if ($user->isAccountOwner()) {
        flash_error(lang('user cant be removed from project'));
        $this->redirectTo('project', 'permissions');
      } // if
      
      $project = Projects::findById(get_id('project_id'));
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('project', 'permissions');
      } // if
      
      $project_user = ProjectUsers::findById(array('project_id' => $project->getId(), 'user_id' => $user->getId()));
      if (!($project_user instanceof ProjectUser)) {
        flash_error(lang('user not on project'));
        $this->redirectTo('project', 'permissions');
      } // if
      
      try {
        $project_user->delete();
        flash_success(lang('success remove user from project'));
      } catch(Exception $e) {
        flash_error(lang('error remove user from project'));
      } // try
      
      $this->redirectTo('project', 'permissions');
    } // remove_user
    
    /**
    * Remove company from project
    *
    * @param void
    * @return null
    */
    function remove_company() {
      if (!active_project()->canChangePermissions(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(active_project()->getOverviewUrl());
      } // if
      
      $project = Projects::findById(get_id('project_id'));
      if (!($project instanceof Project)) {
        flash_error(lang('project dnx'));
        $this->redirectTo('project', 'people');
      } // if
      
      $company = Companies::findById(get_id('company_id'));
      if (!($company instanceof Company)) {
        flash_error(lang('company dnx'));
        $this->redirectTo('project', 'people');
      } // if
      
      $project_company = ProjectCompanies::findById(array('project_id' => $project->getId(), 'company_id' => $company->getId()));
      if (!($project_company instanceof ProjectCompany)) {
        flash_error(lang('company not on project'));
        $this->redirectTo('project', 'people');
      } // if
      
      try {
        
        DB::beginWork();
        $project_company->delete();
        $users = ProjectUsers::getCompanyUsersByProject($company, $project);
        if (is_array($users)) {
          foreach ($users as $user) {
            $project_user = ProjectUsers::findById(array('project_id' => $project->getId(), 'user_id' => $user->getId()));
            if ($project_user instanceof ProjectUser) {
              $project_user->delete();
            }
          } // foreach
        } // if
        DB::commit();
        
        flash_success(lang('success remove company from project'));
        
      } catch(Exception $e) {
        DB::rollback();
        flash_error(lang('error remove company from project'));
      } // try
      
      $this->redirectTo('project', 'people');
    } // remove_company
  
  } // ProjectController

?>
