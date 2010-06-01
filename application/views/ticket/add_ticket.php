<?php 

  set_page_title($ticket->isNew() ? lang('add ticket') : lang('edit ticket'));
  project_tabbed_navigation(PROJECT_TAB_TICKETS);
  project_crumbs(array(
    array(lang('tickets'), get_url('ticket')),
    array($ticket->isNew() ? lang('add ticket') : lang('edit ticket'))
  ));
  add_stylesheet_to_page('project/tickets.css');
?>
<script type="text/javascript" src="<?php echo get_javascript_url('modules/addMessageForm.js') ?>"></script>
<?php if ($ticket->isNew()) { ?>
<form action="<?php echo get_url('ticket', 'add') ?>" method="post" enctype="multipart/form-data">
<?php } else { ?>
<form action="<?php echo $ticket->getEditUrl() ?>" method="post" enctype="multipart/form-data">
<?php } // if?>

<?php tpl_display(get_template_path('form_errors')) ?>

  <div>
    <?php echo label_tag(lang('summary'), 'ticketFormSummary', true) ?>
    <?php echo text_field('ticket[summary]', array_var($ticket_data, 'summary'), array('id' => 'ticketFormSummary', 'class' => 'title')) ?>
  </div>
  
  <div class="description">
    <?php echo label_tag(lang('description'), 'ticketFormDescription', true) ?>
    <?php echo editor_widget('ticket[description]', array_var($ticket_data, 'description'), array('id' => 'ticketFormDescription')) ?>
  </div>
  
<?php if ($ticket->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('status'), 'ticketFormStatus') ?>
    <?php echo select_ticket_status("ticket[status]", array_var($ticket_data, 'status'), array('id' => 'ticketFormStatus')) ?>
  </div>
<?php } // if?>
  
<?php if ($ticket->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('type'), 'ticketFormType') ?>
    <?php echo select_ticket_type("ticket[type]", array_var($ticket_data, 'type'), array('id' => 'ticketFormType')) ?>
  </div>
<?php } // if?>
  
<?php if ($ticket->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('category'), 'ticketFormCategory') ?>
    <?php echo select_ticket_category("ticket[category_id]", $ticket->getProject(), array_var($ticket_data, 'category_id'), array('id' => 'ticketFormCategory')) ?>
  </div>
<?php } // if?>
  
<?php if ($ticket->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('priority'), 'ticketFormPriority') ?>
    <?php echo select_ticket_priority("ticket[priority]", array_var($ticket_data, 'priority'), array('id' => 'ticketFormPriority')) ?>
  </div>
<?php } // if?>

<?php if ($ticket->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('milestone'), 'ticketFormMilestone') ?>
    <?php echo select_milestone('ticket[milestone_id]', active_project(), array_var($ticket_data, 'milestone_id'), array('id' => 'ticketFormMilestone')) ?>
  </div>
<?php } // if?>

<?php if ($ticket->isNew()) { ?>
  <div>
    <?php echo label_tag(lang('assigned to'), 'ticketFormAssignedTo') ?>
    <?php echo assign_to_select_box("ticket[assigned_to]", active_project(), array_var($ticket_data, 'assigned_to'), array('id' => 'ticketFormAssignedTo')) ?>
  </div>
<?php } // if?>

<?php if (logged_user()->isMemberOfOwnerCompany()) { ?>
  <fieldset>
    <legend><?php echo lang('options') ?></legend>
    
    <div class="objectOption">
      <div class="optionLabel"><label><?php echo lang('private ticket') ?>:</label></div>
      <div class="optionControl"><?php echo yes_no_widget('ticket[is_private]', 'ticketFormIsPrivate', array_var($ticket_data, 'is_private'), lang('yes'), lang('no')) ?></div>
      <div class="optionDesc"><?php echo lang('private ticket desc') ?></div>
    </div>
  </fieldset>
<?php } // if ?>
  
<?php if ($ticket->isNew() && $ticket->canAttachFile(logged_user(), active_project())) { ?>
  <?php echo render_attach_files() ?>
<?php } // if ?>

  <fieldset id="emailNotification">
    <legend><?php echo lang('email notification') ?></legend>
    <p><?php echo lang('email notification ticket desc') ?></p>
<?php foreach (active_project()->getCompanies() as $company) { ?>
    <script type="text/javascript">
      App.modules.addMessageForm.notify_companies.company_<?php echo $company->getId() ?> = {
        id          : <?php echo $company->getId() ?>,
        checkbox_id : 'notifyCompany<?php echo $company->getId() ?>',
        users       : []
      };
    </script>
<?php if (is_array($users = $company->getUsersOnProject(active_project())) && count($users)) { ?>
    <div class="companyDetails">
      <div class="companyName"><?php echo checkbox_field('ticket[notify_company_' . $company->getId() . ']', array_var($ticket_data, 'notify_company_' . $company->getId()), array('id' => 'notifyCompany' . $company->getId(), 'onclick' => 'App.modules.addMessageForm.emailNotifyClickCompany(' . $company->getId() . ')')) ?> <label for="notifyCompany<?php echo $company->getId() ?>" class="checkbox"><?php echo clean($company->getName()) ?></label></div>
      <div class="companyMembers">
        <ul>
<?php foreach ($users as $user) { ?>
          <li><?php echo checkbox_field('ticket[notify_user_' . $user->getId() . ']', array_var($ticket_data, 'notify_user_' . $user->getId()), array('id' => 'notifyUser' . $user->getId(), 'onclick' => 'App.modules.addMessageForm.emailNotifyClickUser(' . $company->getId() . ', ' . $user->getId() . ')')) ?> <label for="notifyUser<?php echo $user->getId() ?>" class="checkbox"><?php echo clean($user->getDisplayName()) ?></label></li>
          <script type="text/javascript">
            App.modules.addMessageForm.notify_companies.company_<?php echo $company->getId() ?>.users.push({
              id          : <?php echo $user->getId() ?>,
              checkbox_id : 'notifyUser<?php echo $user->getId() ?>'
            });
          </script>
<?php } // foreach ?>
        </ul>
      </div>
    </div>
<?php } // if ?>
<?php } // foreach ?>
  </fieldset>

  <?php echo submit_button($ticket->isNew() ? lang('add ticket') : lang('edit ticket')) ?>
</form>