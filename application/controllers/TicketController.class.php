<?php

  /**
  * Issue tracker controller
  *
  * @version 1.0
  * @http://www.projectpier.org/
  */
  class TicketController extends ApplicationController {
    
    /**
    * Prepare this controller
    *
    * @access public
    * @param void
    * @return ProjectController
    */
    function __construct() {
      parent::__construct();
      prepare_company_website_controller($this, 'project_website');
    } // __construct
    
    function categories() {
      $page = (integer) array_var($_GET, 'page', 1);
      if ($page < 0) $page = 1;
      
      $conditions = array('`project_id` = ?', active_project()->getId());
      
      list($categories, $pagination) = Categories::paginate(
        array(
          'conditions' => $conditions,
          'order' => '`name`'
        ),
        config_option('categories_per_page', 25), 
        $page
      ); // paginate
      
      tpl_assign('categories', $categories);
      tpl_assign('categories_pagination', $pagination);
      
      $this->setSidebar(get_template_path('ticket_sidebar', 'ticket'));
    } // categories
    
    /**
    * Return project tickets
    *
    * @access public
    * @param void
    * @return array
    */
    function index() {
      $page = (integer) array_var($_GET, 'page', 1);
      if ($page < 0) {
        $page = 1;
      }
      
      $params = array();
      
      $params['sort_by'] = array_var($_GET, 'sort_by', Cookie::getValue('ticketsSortBy', 'id'));
      $expiration = Cookie::getValue('remember'.TOKEN_COOKIE_NAME) ? REMEMBER_LOGIN_LIFETIME : null;
      Cookie::setValue('ticketsSortBy', $params['sort_by'], $expiration);
      
      $conditions = DB::prepareString('`project_id` = ?', array(active_project()->getId()));
      if ($params['status'] = array_var($_GET, 'status')) {
        $conditions .= DB::prepareString(' AND `status` IN (?)', array(explode(',', $params['status'])));
      }
      if ($params['priority'] = array_var($_GET, 'priority')) {
        $conditions .= DB::prepareString(' AND `priority` IN (?)', array(explode(',', $params['priority'])));
      }
      if ($params['type'] = array_var($_GET, 'type')) {
        $conditions .= DB::prepareString(' AND `type` IN (?)', array(explode(',', $params['type'])));
      }
      if ($params['category_id'] = array_var($_GET, 'category_id')) {
        $conditions .= DB::prepareString(' AND `category_id` IN (?)', array(explode(',', $params['category_id'])));
      }
      if ($params['assigned_to_user_id'] = array_var($_GET, 'assigned_to_user_id')) {
        $conditions .= DB::prepareString(' AND `assigned_to_user_id` IN (?)', array(explode(',', $params['assigned_to_user_id'])));
      }
      $params['order'] = (array_var($_GET, 'order') != 'DESC' ? 'ASC' : 'DESC');
      
      $filtered = $params['status']!="" || $params['priority']!="" || $params['type']!="" || $params['category_id']!="" || $params['assigned_to_user_id']!="";

      // Clean up empty and malformed parameters
      foreach ($params as $key => $value) {
        $value = preg_replace("/,+/", ",", $value);
        $value = preg_replace("/^,?(.*),?$/", "$1", $value);
        $params[$key] = $value;
        if ($value=="") {
          unset($params[$key]);
        }
      }
      
      $order = '`'.$params['sort_by'].'` '.$params['order'].'';
      if (!logged_user()->isMemberOfOwnerCompany()) {
        $conditions .= DB::prepareString(' AND `is_private` = ?', array(0));
      } // if
            
      list($tickets, $pagination) = ProjectTickets::paginate(
        array(
          'conditions' => $conditions,
          'order' => $order
        ),
        config_option('tickets_per_page', 25), 
        $page
      ); // paginate
      
      tpl_assign('filtered', $filtered);
      tpl_assign('params', $params);
      tpl_assign('grouped_users', active_project()->getUsers(true));
      tpl_assign('categories', Categories::getProjectCategories(active_project()));
      tpl_assign('tickets', $tickets);
      tpl_assign('tickets_pagination', $pagination);
      
      $this->setSidebar(get_template_path('index_sidebar', 'ticket'));
    } // index
    
    /**
    * View ticket
    * 
    * @access public
    * @param void
    * @return null
    */
    function view() {
      $this->addHelper('textile');
      $this->addHelper('ticket');
      
      $params['order'] = array_var($_GET, 'order', Cookie::getValue('changesetOrder', 'DESC'));
      $expiration = Cookie::getValue('remember'.TOKEN_COOKIE_NAME) ? REMEMBER_LOGIN_LIFETIME : null;
      Cookie::setValue('changesetOrder', $params['order'], $expiration);
      
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canView(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket'));
      } // if
      
      $subscribers = $ticket->getSubscribers();
      $subscribers_ids = null;
      foreach ($subscribers as $subscriber) {
        $subscribers_ids[] = $subscriber->getId();
      } // foreach
      
      $ticket_data = array(
        'milestone_id' => $ticket->getMilestoneId(),
        'status' => $ticket->getStatus(),
        'is_private' => $ticket->isPrivate(),
        'summary' => $ticket->getSummary(),
        'description' => $ticket->getDescription(),
        'priority' => $ticket->getPriority(),
        'due_date' => $ticket->hasDueDate() ? $ticket->getDueDate():DateTimeValueLib::now(),
        'type' => $ticket->getType(),
        'category_id' => $ticket->getCategoryId(),
        'assigned_to' => $ticket->getAssignedToCompanyId() . ':' . $ticket->getAssignedToUserId()
      ); // array
      
      tpl_assign('params', $params);
      tpl_assign('ticket', $ticket);
      tpl_assign('ticket_data', $ticket_data);
      tpl_assign('subscribers', $subscribers);
      tpl_assign('subscribers_ids', $subscribers_ids);
      tpl_assign('changesets', $ticket->getChangesets($params['order']));
      
      $this->setSidebar(get_template_path('view_sidebar', 'ticket'));
    } // view

    /**
    * Save changes to ticket
    *
    * @access public
    * @param void
    * @return null
    */
    function save_change() {
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canChangeStatus(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket'));
      } // if
      
      $ticket_data = array_var($_POST, 'ticket');
      if (is_array(array_var($_POST, 'ticket'))) {
        if ($ticket_data['empty_due_date']) {
          $ticket_data['due_date'] = EMPTY_DATETIME;
        } else {
          $ticket_data['due_date'] = DateTimeValueLib::make(0, 0, 0, array_var($_POST, 'ticket_due_date_month', 1), array_var($_POST, 'ticket_due_date_day', 1), array_var($_POST, 'ticket_due_date_year', 1970));
        } // if
        try {
          $old_fields = array(
            'status' => $ticket->getStatus(),
            'priority' => $ticket->getPriority(),
            'type' => $ticket->getType(),
            'category' => $ticket->getCategory(),
            'assigned to' => $ticket->getAssignedTo(),
            'milestone' => $ticket->getMilestone(),
            'due date' => $ticket->getDueDate()
            );
          
          $ticket->setFromAttributes($ticket_data);
          $assigned_to = explode(':', array_var($ticket_data, 'assigned_to', ''));
          $ticket->setAssignedToCompanyId(array_var($assigned_to, 0, 0));
          $ticket->setAssignedToUserId(array_var($assigned_to, 1, 0));

          if ($ticket->getAssignedToUser()) {
            $ticket->subscribeUser($ticket->getAssignedToUser());
          } // if

          DB::beginWork();
          $ticket->save();
          ApplicationLogs::createLog($ticket, $ticket->getProject(), ApplicationLogs::ACTION_EDIT);
          DB::commit();
          
          $new_fields = array(
            'status' => $ticket->getStatus(),
            'priority' => $ticket->getPriority(),
            'type' => $ticket->getType(),
            'category' => $ticket->getCategory(),
            'assigned to' => $ticket->getAssignedTo(),
            'milestone' => $ticket->getMilestone(),
            'due date' => $ticket->getDueDate()
            );
          
          
          $changeset = new TicketChangeset();
          $changeset->setTicketId($ticket->getId());
          $changeset->setComment(array_var($ticket_data, 'comment'));
          $changeset->save();
          foreach ($old_fields as $type => $old_field) {
            $new_field = $new_fields[$type];
            if ($old_field === $new_field) {
              continue;
            } // if
            if ($old_field instanceof ApplicationDataObject) {
              $from_data = $old_field->getObjectName();
            } elseif ($old_field instanceof DateTimeValue) {
              $from_data = $old_field->format('m/d/Y');
            } else {
              $from_data = $old_field;
            } // if
            if ($new_field instanceof ApplicationDataObject) {
              $to_data = $new_field->getObjectName();
            } elseif ($new_field instanceof DateTimeValue) {
              $to_data = $new_field->format('m/d/Y');
            } else {
              $to_data = $new_field;
            } // if

            $change = new TicketChange();
            $change->setChangesetId($changeset->getId());
            $change->setType($type);
            $change->setFromData($from_data);
            $change->setToData($to_data);
            $change->save();
          } // foreach
          
          if ($changeset->isEmpty()) {
            $changeset->delete();
          } else {
            Notifier::ticketChange($ticket, $changeset, logged_user());
          } // if

          
          flash_success(lang('success edit ticket', $ticket->getSummary()));
        } catch(Exception $e) {
          flash_error(lang('error update ticket options'), $ticket->getSummary());
        } // try
      } // if
      $this->redirectToUrl($ticket->getViewUrl());
    } // save_change
    
    /**
    * Add ticket
    *
    * @access public
    * @param void
    * @return null
    */
    function add() {
      $this->addHelper('ticket');
      $this->addHelper('textile');
      $this->setTemplate('add_ticket');
      
      if (!ProjectTicket::canAdd(logged_user(), active_project())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket'));
      } // if
      
      $ticket = new ProjectTicket();
      $ticket->setProjectId(active_project()->getId());
      
      $ticket_data = array_var($_POST, 'ticket');
      if (!is_array($ticket_data)) {
        $ticket_data = array(
          'milestone_id' => array_var($_GET, 'milestone_id'),
          'due_date' => DateTimeValueLib::now(),
        ); // array
      } // if
      tpl_assign('ticket', $ticket);
      tpl_assign('ticket_data', $ticket_data);
      $this->setSidebar(get_template_path('textile_help_sidebar'));
      
      if (is_array(array_var($_POST, 'ticket'))) {
        if ($ticket_data['empty_due_date']) {
          $ticket_data['due_date'] = EMPTY_DATETIME;
        } else {
          $ticket_data['due_date'] = DateTimeValueLib::make(0, 0, 0, array_var($_POST, 'ticket_due_date_month', 1), array_var($_POST, 'ticket_due_date_day', 1), array_var($_POST, 'ticket_due_date_year', 1970));
        } // if
        try {
          $uploaded_files = ProjectFiles::handleHelperUploads(active_project());
        } catch (Exception $e) {
          $uploaded_files = null;
        } // try
        
        try {
          $ticket->setFromAttributes($ticket_data);
        
          $assigned_to = explode(':', array_var($ticket_data, 'assigned_to', ''));
          $ticket->setAssignedToCompanyId(array_var($assigned_to, 0, 0));
          $ticket->setAssignedToUserId(array_var($assigned_to, 1, 0));
          
          // Options are reserved only for members of owner company
          if (!logged_user()->isMemberOfOwnerCompany()) {
            $ticket->setIsPrivate(false); 
          } // if
          
          DB::beginWork();
          $ticket->save();
          $ticket->setTagsFromCSV(array_var($ticket_data, 'tags'));
          
          if (is_array($uploaded_files)) {
            foreach ($uploaded_files as $uploaded_file) {
              $ticket->attachFile($uploaded_file);
              $uploaded_file->setIsPrivate($ticket->isPrivate());
              $uploaded_file->setIsVisible(true);
              $uploaded_file->setExpirationTime(EMPTY_DATETIME);
              $uploaded_file->save();
            } // if
          } // if
          
          ApplicationLogs::createLog($ticket, active_project(), ApplicationLogs::ACTION_ADD);
          DB::commit();
          
          // Try to send notifications but don't break submission in case of an error
          try {
            if ($ticket->getAssignedToUserId()) {
              $ticket_data['notify_user_' . $ticket->getAssignedToUserId()] = 'checked';
            }
            
            $notify_people = array();
            $project_companies = active_project()->getCompanies();
            foreach ($project_companies as $project_company) {
              $company_users = $project_company->getUsersOnProject(active_project());
              if (is_array($company_users)) {
                foreach ($company_users as $company_user) {
                  if ((array_var($ticket_data, 'notify_company_' . $project_company->getId()) == 'checked') || (array_var($ticket_data, 'notify_user_' . $company_user->getId()))) {
                    $ticket->subscribeUser($company_user); // subscribe
                    $notify_people[] = $company_user;
                  } // if
                } // if
              } // if
            } // foreach
            
            Notifier::ticket($ticket, $notify_people, 'new_ticket', $ticket->getCreatedBy());
          } catch (Exception $e) {
          
          } // try
          
          flash_success(lang('success add ticket', $ticket->getSummary()));
          $this->redirectToUrl($ticket->getViewUrl());
          
        // Error...
        } catch(Exception $e) {
          DB::rollback();
          
          if (is_array($uploaded_files)) {
            foreach ($uploaded_files as $uploaded_file) {
              $uploaded_file->delete();
            } // foreach
          } // if
          
          $ticket->setNew(true);
          tpl_assign('error', $e);
        } // try
        
      } // if
    } // add
    
    /**
    * Edit specific ticket
    *
    * @access public
    * @param void
    * @return null
    */
    function edit() {
      $this->addHelper('textile');
      $this->addHelper('ticket');
      $this->setTemplate('add_ticket');
      
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canEdit(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('ticket');
      } // if
      
      $ticket_data = array_var($_POST, 'ticket');
      if (!is_array($ticket_data)) {
        $tag_names = $ticket->getTagNames();
        $ticket_data = array(
          'is_private' => $ticket->isPrivate(),
          'summary' => $ticket->getSummary(),
          'description' => $ticket->getDescription(),
          'due_date' => $ticket->hasDueDate() ? $ticket->getDueDate():DateTimeValueLib::now(),
          'tags' => is_array($tag_names) ? implode(', ', $tag_names) : '',
        ); // array
      } // if
      
      tpl_assign('ticket', $ticket);
      tpl_assign('ticket_data', $ticket_data);
      
      $this->setSidebar(get_template_path('textile_help_sidebar'));
      
      if (is_array(array_var($_POST, 'ticket'))) {
        if ($ticket_data['empty_due_date']) {
          $ticket_data['due_date'] = EMPTY_DATETIME;
        } else {
          $ticket_data['due_date'] = DateTimeValueLib::make(0, 0, 0, array_var($_POST, 'ticket_due_date_month', 1), array_var($_POST, 'ticket_due_date_day', 1), array_var($_POST, 'ticket_due_date_year', 1970));
        } // if
        $old_fields = array(
          'summary' => $ticket->getSummary(),
          'description' => $ticket->getDescription(),
          'private' => $ticket->isPrivate()?"private":"public",
          'due date' => $ticket->getDueDate()
          );

        try {
          $ticket->setSummary(array_var($ticket_data, 'summary'));
          $ticket->setDescription(array_var($ticket_data, 'description'));
          $ticket->setIsPrivate((boolean) array_var($ticket_data, 'is_private', $ticket->isPrivate()));
          $ticket->setDueDate(array_var($ticket_data, 'due_date'));
          $ticket->setUpdated('settings');

          // Options are reserved only for members of owner company
          if (!logged_user()->isMemberOfOwnerCompany()) {
            $ticket->setIsPrivate($old_fields['private']);
          } // if

          DB::beginWork();
          $ticket->save();
          $ticket->setTagsFromCSV(array_var($ticket_data, 'tags'));

          ApplicationLogs::createLog($ticket, $ticket->getProject(), ApplicationLogs::ACTION_EDIT);
          DB::commit();

          $new_fields = array(
            'summary' => $ticket->getSummary(),
            'description' => $ticket->getDescription(),
            'private' => $ticket->isPrivate() ? "private" : "public",
            'due date' => $ticket->getDueDate()
            );
          
          $changeset = new TicketChangeset();
          $changeset->setTicketId($ticket->getId());
          $changeset->save();
          foreach ($old_fields as $type => $old_field) {
            $new_field = $new_fields[$type];
            if ($old_field === $new_field) {
              continue;
            } // if
            if ($old_field instanceof ApplicationDataObject) {
              $from_data = $old_field->getObjectName();
            } elseif ($old_field instanceof DateTimeValue) {
              $from_data = $old_field->format('m/d/Y');
            } else {
              $from_data = $old_field;
            } // if
            if ($new_field instanceof ApplicationDataObject) {
              $to_data = $new_field->getObjectName();
            } elseif ($new_field instanceof DateTimeValue) {
              $to_data = $new_field->format('m/d/Y');
            } else {
              $to_data = $new_field;
            } // if

            $change = new TicketChange();
            $change->setChangesetId($changeset->getId());
            $change->setType($type);
            $change->setFromData($from_data);
            $change->setToData($to_data);
            $change->save();
          } // foreach

          try {
            if ($ticket->getAssignedToUserId()) {
              $ticket_data['notify_user_' . $ticket->getAssignedToUserId()] = 'checked';
            } // if
            
            $notify_people = array();
            $project_companies = active_project()->getCompanies();
            foreach ($project_companies as $project_company) {
              $company_users = $project_company->getUsersOnProject(active_project());
              if (is_array($company_users)) {
                foreach ($company_users as $company_user) {
                  if ((array_var($ticket_data, 'notify_company_' . $project_company->getId()) == 'checked') || (array_var($ticket_data, 'notify_user_' . $company_user->getId()))) {
                    $ticket->subscribeUser($company_user); // subscribe
                    $notify_people[] = $company_user;
                  } else {
                    $ticket->unsubscribeUser($company_user);
                  } // if
                } // if
              } // if
            } // foreach
            
            // Notify only if the ticket itself has changed (not subscribers)
            if ($changeset->isEmpty()) {
              $changeset->delete();
            } else {
              Notifier::ticket($ticket, $ticket->getSubscribers(), 'edit_ticket', $ticket->getUpdatedBy());
            } // if
          } catch (Exception $e) {
            // nothing here, just suppress error...
          } // try

          flash_success(lang('success edit ticket', $ticket->getSummary()));
          $this->redirectToUrl($ticket->getViewUrl());

        } catch(Exception $e) {
          DB::rollback();
          tpl_assign('error', $e);
        } // try
      } // if
    } // edit
    
    /**
    * Update message options. This is execute only function and if we don't have 
    * options in post it will redirect back to the message
    *
    * @param void
    * @return null
    */
    function update_options() {
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canUpdateOptions(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket'));
      } // if
      
      $ticket_data = array_var($_POST, 'ticket');
      if (is_array(array_var($_POST, 'ticket'))) {
        try {
          $old_private = $ticket->isPrivate();
          $ticket->setIsPrivate((boolean) array_var($ticket_data, 'is_private', $ticket->isPrivate()));
          
          DB::beginWork();
          $ticket->save();
          ApplicationLogs::createLog($ticket, $ticket->getProject(), ApplicationLogs::ACTION_EDIT);
          DB::commit();
          
          if ($old_private != $ticket->isPrivate()) {
            $changeset = new TicketChangeset();
            $changeset->setTicketId($ticket->getId());
            $changeset->save();
            $change = new TicketChange();
            $change->setChangesetId($changeset->getId());
            $change->setType('private');
            $change->setFromData($old_private ? 'yes' : 'no');
            $change->setToData($ticket->isPrivate() ? 'yes' : 'no');
            $change->save();
          }
          
          flash_success(lang('success edit ticket', $ticket->getSummary()));
        } catch(Exception $e) {
          flash_error(lang('error update ticket options'), $ticket->getSummary());
        } // try
      } // if
      $this->redirectToUrl($ticket->getViewUrl());
    } // update_options
    
    /**
    * Close specific ticket
    *
    * @access public
    * @param void
    * @return null
    */
    function close() {
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canChangeStatus(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket'));
      } // if
      
      $status = $ticket->isClosed() ? 'closed' : 'open';
      
      try {
        DB::beginWork();
        $ticket->closeTicket();
        ApplicationLogs::createLog($ticket, active_project(), ApplicationLogs::ACTION_CLOSE);
        DB::commit();
        
        if ($status != 'closed') {
          $change = new TicketChange();
          $change->setTicketId($ticket->getId());
          $change->setType('status');
          $change->setFromData($status);
          $change->setToData('closed');
          $change->save();
        }
        
        try {
          Notifier::ticket($ticket, $ticket->getSubscribers(), 'close_ticket', $ticket->getClosedBy());
        } catch(Exception $e) {
          // nothing here, just suppress error...
        } // try
        
        flash_success(lang('success close ticket'));
      } catch(Exception $e) {
        flash_error(lang('error close ticket'));
        DB::rollback();
      } // try
      
      $this->redirectToUrl(get_url('ticket'));
    } // close
    
    /**
    * Open specific ticket
    *
    * @access public
    * @param void
    * @return null
    */
    function open() {
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canChangeStatus(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket'));
      } // if
      
      $status = $ticket->isClosed() ? 'closed' : 'open';
      
      try {
        DB::beginWork();
        $ticket->openTicket();
        ApplicationLogs::createLog($ticket, active_project(), ApplicationLogs::ACTION_OPEN);
        DB::commit();
        
        if ($status != 'open') {
          $change = new TicketChange();
          $change->setTicketId($ticket->getId());
          $change->setType('status');
          $change->setFromData($status);
          $change->setToData('open');
          $change->save();
        }
        
        try {
          Notifier::ticket($ticket, $ticket->getSubscribers(), 'open_ticket', logged_user());
        } catch(Exception $e) {
          // nothing here, just suppress error...
        } // try
        
        flash_success(lang('success open ticket'));
      } catch(Exception $e) {
        flash_error(lang('error open ticket'));
        DB::rollback();
      } // try
      
      $this->redirectToUrl(get_url('ticket'));
    } // open
    
    /**
    * Delete specific ticket
    *
    * @access public
    * @param void
    * @return null
    */
    function delete() {
      $this->setTemplate('del_ticket');
      
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canDelete(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('ticket');
      } // if
      
      $delete_data = array_var($_POST, 'deleteTicket');
      tpl_assign('ticket', $ticket);
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
          return $this->render();
        }
        if (!logged_user()->isValidPassword($password)) {
          tpl_assign('error', new Error(lang('invalid password')));
          return $this->render();
        }
        try {

          DB::beginWork();
          $ticket->delete();
          ApplicationLogs::createLog($ticket, $ticket->getProject(), ApplicationLogs::ACTION_DELETE);
          DB::commit();

          flash_success(lang('success deleted ticket', $ticket->getSummary()));
        } catch (Exception $e) {
          DB::rollback();
          flash_error(lang('error delete ticket'));
        } // try

        $this->redirectToUrl($ticket->getViewUrl());
      } else {
        flash_error(lang('error delete ticket'));
        $this->redirectToUrl($ticket->getViewUrl());
      } // if

    } // delete
    
    /**
    * Add a new category
    *
    * @access public
    * @param void
    * @return null
    */
    function add_category() {
      if (!Category::canAdd(logged_user(), active_project())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket', 'categories'));
      } // if
      
      $category = new Category();
      $category_data = array_var($_POST, 'category');
      
      tpl_assign('category', $category);
      tpl_assign('category_data', $category_data);
      
      if (is_array(array_var($_POST, 'category'))) {
        try {
          $category->setFromAttributes($category_data);
          $category->setProjectId(active_project()->getId());
          
          DB::beginWork();
          $category->save();
          
          ApplicationLogs::createLog($category, active_project(), ApplicationLogs::ACTION_ADD);
          DB::commit();
          
          flash_success(lang('success add category', $category->getName()));
          $this->redirectTo('ticket', 'categories');
          
        // Error...
        } catch(Exception $e) {
          DB::rollback();
          
          $category->setNew(true);
          tpl_assign('error', $e);
        } // try
        
      } // if
    } // add_category
    
    /**
    * Edit specific category
    *
    * @access public
    * @param void
    * @return null
    */
    function edit_category() {
      $this->setTemplate('add_category');
      
      $category = Categories::findById(get_id());
      if (!($category instanceof Category)) {
        flash_error(lang('category dnx'));
        $this->redirectTo('ticket', 'categories');
      } // if
      
      if (!$category->canView(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket', 'categories'));
      } // if
      
      $category_data = array_var($_POST, 'category');
      if (!is_array($category_data)) {
        $category_data = array(
          'name' => $category->getName(),
          'description' => $category->getDescription()
        ); // array
      } // if
      
      tpl_assign('category', $category);
      tpl_assign('category_data', $category_data);
      
      if (is_array(array_var($_POST, 'category'))) {
        if (!$category->canEdit(logged_user())) {
          flash_error(lang('no access permissions'));
          $this->redirectTo('ticket', 'categories');
        } else {
          try {
            $category->setFromAttributes($category_data);
            
            DB::beginWork();
            $category->save();
            
            ApplicationLogs::createLog($category, $category->getProject(), ApplicationLogs::ACTION_EDIT);
            DB::commit();
            
            flash_success(lang('success edit category', $category->getName()));
            $this->redirectTo('ticket', 'categories');
            
          } catch(Exception $e) {
            DB::rollback();
            tpl_assign('error', $e);
          } // try
        } // if
      } // if
    } // edit_category
    
    /**
    * Delete specific category
    *
    * @access public
    * @param void
    * @return null
    */
    function delete_category() {
      $category = Categories::findById(get_id());
      if (!($category instanceof Category)) {
        flash_error(lang('category dnx'));
        $this->redirectTo('ticket', 'categories');
      } // if
      
      if (!$category->canDelete(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectToReferer(get_url('ticket', 'categories'));
      } // if
      
      try {
        
        DB::beginWork();
        $category->delete();
        ApplicationLogs::createLog($category, $category->getProject(), ApplicationLogs::ACTION_DELETE);
        DB::commit();
        
        flash_success(lang('success deleted category', $category->getName()));
      } catch(Exception $e) {
        DB::rollback();
        flash_error(lang('error delete category'));
      } // try
      
      $this->redirectTo('ticket', 'categories');
    } // delete
    
    // ---------------------------------------------------
    //  Subscriptions
    // ---------------------------------------------------
    
    /**
    * Subscribe to ticket
    *
    * @param void
    * @return null
    */
    function subscribe() {
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canView(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('ticket');
      } // if
      
      $ticket_data = array_var($_POST, 'ticket');
      if (is_array(array_var($_POST, 'ticket'))) {
        try {
          $user = Users::findById($ticket_data['new_subscriber']);
          if ($user instanceof User) {
            if ($ticket->subscribeUser($user)) {
              if ($user->getId() == logged_user()->getId()) {
                flash_success(lang('success subscribe to ticket'));
              } else {
                flash_success(lang('success subscribe user to ticket', $user->getDisplayName()));
              } // if
            } else {
              flash_error(lang('error subscribe to ticket'));
            } // if
          } else {
            flash_error(lang('user dnx'));
          } // if
        } catch (Exception $e) {
          flash_error(lang('error subscribe to ticket'));
        } // try
      } // if
      $this->redirectToUrl($ticket->getViewUrl());
    } // subscribe
    
    /**
    * Unsubscribe from message
    *
    * @param void
    * @return null
    */
    function unsubscribe() {
      $ticket = ProjectTickets::findById(get_id());
      if (!($ticket instanceof ProjectTicket)) {
        flash_error(lang('ticket dnx'));
        $this->redirectTo('ticket');
      } // if
      
      if (!$ticket->canView(logged_user())) {
        flash_error(lang('no access permissions'));
        $this->redirectTo('ticket');
      } // if
      
      if ($ticket->unsubscribeUser(logged_user())) {
        flash_success(lang('success unsubscribe to ticket'));
      } else {
        flash_error(lang('error unsubscribe to ticket'));
      } // if
      $this->redirectToUrl($ticket->getViewUrl());
    } // unsubscribe
  
  } // TicketController

?>