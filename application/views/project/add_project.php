<?php 

  if ($project->isNew()) {
    set_page_title(lang('add project'));
    administration_tabbed_navigation(ADMINISTRATION_TAB_PROJECTS);
    administration_crumbs(array(
      array(lang('projects'), get_url('administration', 'projects')),
      array(lang('add project'))
    ));
  } else {
    set_page_title(lang('edit project'));
    project_tabbed_navigation(PROJECT_TAB_SETTINGS);
    project_crumbs(array(
      array(lang('settings'), get_url('project_settings')),
      array(lang('edit project'))
    ));
  } // if
  add_stylesheet_to_page('project/attachments.css');
  add_javascript_to_page('jquery/jquery-1.4.2.min.js');
  add_javascript_to_page('jquery/jquery-ui-1.8.2.custom.min.js');
  add_stylesheet_to_page('../jquery-ui-theme/jquery-ui-1.8.2.custom.css');
  add_javascript_to_page('modules/autocomplete.js');
  
?>
<?php if ($project->isNew()) { ?>
<form action="<?php echo get_url('project', 'add') ?>" method="post">
<?php } else { ?>
<form action="<?php echo $project->getEditUrl() ?>" method="post">
<?php } // if ?>

<?php tpl_display(get_template_path('form_errors')) ?>

  <div>
    <?php echo label_tag(lang('name'), 'projectFormName', true) ?>
    <?php echo text_field('project[name]', array_var($project_data, 'name'), array('class' => 'long', 'id' => 'projectFormName')) ?>
  </div>
  
  <div>
    <?php echo label_tag(lang('description'), 'projectFormDescription') ?>
    <?php echo textarea_field('project[description]', array_var($project_data, 'description'), array('id' => 'projectFormDescription')) ?>
  </div>
  
  <div>
    <?php echo label_tag(lang('show project desciption in overview')) ?>
    <?php echo yes_no_widget('project[show_description_in_overview]', 'projectFormShowDescriptionInOverview', array_var($project_data, 'show_description_in_overview'), lang('yes'), lang('no')) ?>
  </div>
<?php if (!$project->isNew()) { ?>
  <hr/>
  <div id="pageAttachments">
  <?php
  $counter = 0;
  ?>
  <div class="attachmentActions">
    <!-- TODO make these links less hard-coded -->
    <!-- TODO make a helper for these links -->
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add text snippet') ?></a> |
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'Contacts', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add contact') ?></a> |
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'Companies', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add company') ?></a> |
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectFiles', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add file') ?></a> |
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectMessages', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add message') ?></a> |
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectMilestones', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add milestone') ?></a> |
    <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectTickets', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add ticket') ?></a>
  </div>
  
<?php  foreach ($page_attachments as $page_attachment) {
    $counter++;
    ?>
    <div class="pageAttachment <?php echo $counter%2 ? 'odd':'even'; ?>">
      <?php echo label_tag(lang($page_attachment->getObjectLangName())); ?>
      <?php echo $page_attachment->render('project[page_attachments]['.$page_attachment->getId().'][text]'); ?>
      <?php echo $page_attachment->renderControl('project[page_attachments]['.$page_attachment->getId().'][rel_object_id]'); ?>
      <?php echo text_field('project[page_attachments]['.$page_attachment->getId().'][order]', $page_attachment->getOrder(), array('class' => 'short pageAttachmentOrder')) ?>
      <span class="pageAttachmentDeleteBlock">
      <?php echo label_tag(lang('delete'), 'project[page_attachments]['.$page_attachment->getId().'][delete]', false, array('class'=>'checkbox'));?>
      <?php echo checkbox_field('project[page_attachments]['.$page_attachment->getId().'][delete]', false, array('class'=>'checkbox pageAttachmentDelete', 'id' => 'project[page_attachments]['.$page_attachment->getId().'][delete]')); ?>
      <input type="hidden" name="<?php echo 'project[page_attachments]['.$page_attachment->getId().'][rel_object_manager]'; ?>" value="<?php echo $page_attachment->getRelObjectManager(); ?>"/>
      </span>
      <div class="clear"></div>
    </div>
    <div class="attachmentActions">
      <!-- TODO make these links less hard-coded -->
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add text snippet') ?></a> |
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'Contacts', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add contact') ?></a> |
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'Companies', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add company') ?></a> |
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectFiles', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add file') ?></a> |
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectMessages', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add message') ?></a> |
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectMilestones', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add milestone') ?></a> |
      <a href="<?php echo get_url('page_attachment', 'add_attachment', array('page_name'=>'project_overview', 'rel_object_manager'=>'ProjectTickets', 'order'=>$counter, 'redirect_to'=>get_url('project','edit',null,null,true)), null, true); ?>"><?php echo lang('add ticket') ?></a>
    </div>
<?php  } // foreach ?>
  </div>
<?php } // if ?>
  <?php echo submit_button($project->isNew() ? lang('add project') : lang('edit project')) ?>
</form>
