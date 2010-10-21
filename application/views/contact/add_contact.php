<?php

  set_page_title($contact->isNew() ? lang('add contact') : lang('edit contact'));
  if ($company->isOwner()) {
    administration_tabbed_navigation(ADMINISTRATION_TAB_COMPANY);
    administration_crumbs(array(
      array(lang('company'), $company->getViewUrl()),
      array($contact->isNew() ? lang('add contact') : lang('edit contact'))
    ));
  } else {
    administration_tabbed_navigation(ADMINISTRATION_TAB_CLIENTS);
    administration_crumbs(array(
      array(lang('clients'), get_url('administration', 'clients')),
      array($company->getName(), $company->getViewUrl()),
      array($contact->isNew() ? lang('add contact') : lang('edit contact'))
    ));
  } // if
  
  add_stylesheet_to_page('admin/user_permissions.css');

?>
<script type="text/javascript" src="<?php echo get_javascript_url('modules/addUserForm.js') ?>"></script>
<form action="<?php if ($contact->isNew()) { echo $company->getAddContactUrl(); } else { echo $contact->getEditUrl(); } ?>" method="post" enctype="multipart/form-data">

<?php tpl_display(get_template_path('form_errors')) ?>

  <div>
    <?php echo label_tag(lang('name'), 'contactFormDisplayName', true) ?>
    <?php echo text_field('contact[display_name]', array_var($contact_data, 'display_name'), array('class' => 'medium', 'id' => 'contactFormDisplayName')) ?>
  </div>
  
<?php if (!$contact->isNew() && logged_user()->isAdministrator()) { ?>
<?php if (!$contact->isAdministrator()) { ?>
  <div>
    <?php echo label_tag(lang('company'), 'contactFormCompany', true) ?>
    <?php echo select_company('contact[company_id]', array_var($contact_data, 'company_id'), array('id' => 'contactFormCompany')) ?>
  </div>
<?php } else { ?>
  <div>
    <?php echo label_tag(lang('company'), 'contactFormCompany', false) ?>
    <span><?php echo $company->getName()." (".lang('administrator').")"; ?></span>
  </div>
<?php } // if ?>
<?php } else { ?>
  <input type="hidden" name="contact[company_id]" value="<?php echo $company->getId()?>" />
<?php } // if ?>
  
  <div>
    <?php echo label_tag(lang('contact title'), 'contactFormTitle') ?>
    <?php echo text_field('contact[title]', array_var($contact_data, 'title'), array('id' => 'contactFormTitle')) ?>
  </div>

  <div>
    <?php echo label_tag(lang('email address'), 'contactFormEmail', false) ?>
    <?php echo text_field('contact[email]', array_var($contact_data, 'email'), array('class' => 'long', 'id' => 'contactFormEmail')) ?>
  </div>
  
  <div>
      <fieldset>
        <legend><?php echo lang('current avatar') ?></legend>
    <?php if ($contact->hasAvatar()) { ?>
        <img src="<?php echo $contact->getAvatarUrl() ?>" alt="<?php echo clean($contact->getDisplayName()) ?> avatar" />
        <p><?php echo checkbox_field('contact[delete_avatar]', false, array('id'=>'contactDeleteAvatar', 'class' => 'checkbox')) ?> <?php echo label_tag(lang('delete current avatar'), 'contactDeleteAvatar', false, array('class' => 'checkbox'), '') ?></p>
    <?php } else { ?>
        <?php echo lang('no current avatar') ?>
    <?php } // if ?>
      </fieldset>
    <?php echo label_tag(lang('avatar'), 'contactFormAvatar', false) ?>
    <?php echo file_field('new avatar', null, array('id' => 'contactFormAvatar')) ?>
    <?php if ($contact->hasAvatar()) { ?>
    <p class="desc"><?php echo lang('new avatar notice') ?></p>
    <?php } // if ?>
  </div>

  <fieldset>
    <legend><?php echo lang('phone numbers') ?></legend>
    
    <div>
      <?php echo label_tag(lang('office phone number'), 'contactFormOfficeNumber') ?>
      <?php echo text_field('contact[office_number]', array_var($contact_data, 'office_number'), array('id' => 'contactFormOfficeNumber')) ?>
    </div>
    
    <div>
      <?php echo label_tag(lang('fax number'), 'contactFormFaxNumber') ?>
      <?php echo text_field('contact[fax_number]', array_var($contact_data, 'fax_number'), array('id' => 'contactFormFaxNumber')) ?>
    </div>
    
    <div>
      <?php echo label_tag(lang('mobile phone number'), 'contactFormMobileNumber') ?>
      <?php echo text_field('contact[mobile_number]', array_var($contact_data, 'mobile_number'), array('id' => 'contactFormMobileNumber')) ?>
    </div>
    
    <div>
      <?php echo label_tag(lang('home phone number'), 'contactFormHomeNumber') ?>
      <?php echo text_field('contact[home_number]', array_var($contact_data, 'home_number'), array('id' => 'contactFormHomeNumber')) ?>
    </div>
    
  </fieldset>

  <?php echo submit_button($contact->isNew() ? lang('add contact') : lang('edit contact')) ?>
</form>
