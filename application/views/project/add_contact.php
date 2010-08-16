<?php

  set_page_title(lang('add contact'));
  project_tabbed_navigation(PROJECT_TAB_PEOPLE);
  project_crumbs(array(
    array(lang('people'), get_url('project','people')),
    lang('add contact')));
  
  add_stylesheet_to_page('project/people.css');
  add_stylesheet_to_page('project/attachments.css');

?>
<script type="text/javascript" src="<?php echo get_javascript_url('modules/addContactToProjectForm.js') ?>"></script>
<script type="text/javascript" src="<?php echo get_javascript_url('modules/addContactForm.js') ?>"></script>
<form action="<?php echo ($project_init ? $project->getAddContactUrl(array('project_init' => '1')) : $project->getAddContactUrl()); ?>" method="post" enctype="multipart/form-data">
<?php tpl_display(get_template_path('form_errors')) ?>
  
  <div>
    <?php echo radio_field('contact[what]', !isset($contact_data['what']) || $contact_data['what'] == 'existing', array('value' => 'existing', 'id'=>'contactFormExistingContact', 'onclick' => 'App.modules.addContactToProjectForm.toggleAttachForms()')); ?>
    <?php echo label_tag(lang('attach existing contact'), 'contactFormExistingContact', false, array('class'=>'checkbox')); ?>
  </div>
  
  <div id="contactFormExistingContactControls">
    <fieldset>
      <legend><?php echo lang('select contact'); ?></legend>
      <div>
        <?php echo text_field('contact[existing][text]', lang('description')); ?>
        <?php echo select_contact('contact[existing][rel_object_id]', null, $already_attached_contacts_ids, array('id'=> 'contactFormSelectContact', 'class'=>'combobox')); ?>
        <input type="hidden" name="contact[existing][rel_object_manager]" value="Contacts"/>
      </div>
    </fieldset>
  </div>


  <div>
    <?php echo radio_field('contact[what]', isset($contact_data['what']) && $contact_data['what'] == 'new', array('value' => 'new', 'id'=>'contactFormNewContact', 'onclick' => 'App.modules.addContactToProjectForm.toggleAttachForms()')); ?>
    <?php echo label_tag(lang('new contact'), 'contactFormNewContact', false, array('class'=>'checkbox'))?>
  </div>

  <div id="contactFormNewContactControls">
    <fieldset>
      <legend><?php echo lang('new contact'); ?></legend>

      <div>
        <?php echo label_tag(lang('description'), 'contactFormDescription', false) ?>
        <?php echo text_field('contact[new][text]', lang('description')); ?>
        <input type="hidden" name="contact[new][rel_object_manager]" value="Contacts"/>
      </div>
      
      <div>
        <?php echo label_tag(lang('name'), 'contactFormDisplayName', true) ?>
        <?php echo text_field('contact[new][display_name]', array_var($contact_data, 'display_name'), array('class' => 'medium', 'id' => 'contactFormDisplayName')) ?>
      </div>

      <fieldset>
        <legend><?php echo lang('company'); ?></legend>
        <div>
          <?php echo radio_field('contact[new][company][what]', true, array('value' => 'existing', 'id'=>'contactFormExistingCompany', 'onclick' => 'App.modules.addContactForm.toggleCompanyForms()')); ?>
          <?php echo label_tag(lang('existing company'), 'contactFormExistingCompany', false, array('class' => 'checkbox')) ?>
        </div>
        <div id="contactFormExistingCompanyControls">
          <?php echo select_company('contact[new][company_id]', array_var($contact_data, 'company_id'), array('id' => 'contactFormCompany', 'class' => 'combobox')) ?>
        </div>

        <div>
          <?php echo radio_field('contact[new][company][what]', false, array('value' => 'new', 'id'=>'contactFormNewCompany', 'onclick' => 'App.modules.addContactForm.toggleCompanyForms()')); ?>
          <?php echo label_tag(lang('new company'), 'contactFormNewCompany', false, array('class'=>'checkbox'))?>
        </div>
        <div id="contactFormNewCompanyControls">
          <?php echo label_tag(lang('name'), 'contactFormNewCompanyName', true) ?>
          <?php echo text_field('contact[new][company][name]', null, array('id' => 'contactFormNewCompanyName')) ?>
          <?php echo label_tag(lang('timezone'), 'contactFormNewCompanyTimezone', true)?>
          <?php echo select_timezone_widget('contact[new][company][timezone]', owner_company()->getTimezone(), array('id' => 'contactFormNewCompanyTimezone', 'class' => 'long combobox')) ?>
        </div>
      </fieldset>
      <script type="text/javascript">
      App.modules.addContactForm.toggleCompanyForms();
      </script>

      <div>
        <?php echo label_tag(lang('contact title'), 'contactFormTitle') ?>
        <?php echo text_field('contact[new][title]', array_var($contact_data, 'title'), array('id' => 'contactFormTitle')) ?>
      </div>

      <div>
        <?php echo label_tag(lang('email address'), 'contactFormEmail', false) ?>
        <?php echo text_field('contact[new][email]', array_var($contact_data, 'email'), array('class' => 'long', 'id' => 'contactFormEmail')) ?>
      </div>

      <div>
        <?php echo label_tag(lang('avatar'), 'contactFormAvatar', false) ?>
        <?php echo file_field('new avatar', null, array('id' => 'contactFormAvatar')) ?>
      </div>

      <fieldset>
        <legend><?php echo lang('phone numbers') ?></legend>

        <div>
          <?php echo label_tag(lang('office phone number'), 'contactFormOfficeNumber') ?>
          <?php echo text_field('contact[new][office_number]', array_var($contact_data, 'office_number'), array('id' => 'contactFormOfficeNumber')) ?>
        </div>

        <div>
          <?php echo label_tag(lang('fax number'), 'contactFormFaxNumber') ?>
          <?php echo text_field('contact[new][fax_number]', array_var($contact_data, 'fax_number'), array('id' => 'contactFormFaxNumber')) ?>
        </div>

        <div>
          <?php echo label_tag(lang('mobile phone number'), 'contactFormMobileNumber') ?>
          <?php echo text_field('contact[new][mobile_number]', array_var($contact_data, 'mobile_number'), array('id' => 'contactFormMobileNumber')) ?>
        </div>

        <div>
          <?php echo label_tag(lang('home phone number'), 'contactFormHomeNumber') ?>
          <?php echo text_field('contact[new][home_number]', array_var($contact_data, 'home_number'), array('id' => 'contactFormHomeNumber')) ?>
        </div>

      </fieldset>

    <?php if (is_array($im_types) && count($im_types)) { ?>
      <fieldset>
        <legend><?php echo lang('instant messengers') ?></legend>
        <table class="blank">
          <tr>
            <th colspan="2"><?php echo lang('im service') ?></th>
            <th><?php echo lang('value') ?></th>
            <th><?php echo lang('primary im service') ?></th>
          </tr>
        <?php foreach ($im_types as $im_type) { ?>
          <tr>
            <td style="vertical-align: middle"><img src="<?php echo $im_type->getIconUrl() ?>" alt="<?php echo $im_type->getName() ?> icon" /></td>
            <td style="vertical-align: middle"><label class="checkbox" for="<?php echo 'profileFormIm' . $im_type->getId() ?>"><?php echo $im_type->getName() ?></label></td>
            <td style="vertical-align: middle"><?php echo text_field('contact[new][im_' . $im_type->getId() . ']', array_var($contact_data, 'im_' . $im_type->getId()), array('id' => 'profileFormIm' . $im_type->getId())) ?></td>
            <td style="vertical-align: middle"><?php echo radio_field('contact[new][default_im]', array_var($contact_data, 'default_im') == $im_type->getId(), array('value' => $im_type->getId())) ?></td>
          </tr>
        <?php } // foreach ?>
        </table>
        <p class="desc"><?php echo lang('primary im description') ?></p>
      </fieldset>
    <?php } // if ?>
    </fieldset>
  </div>

  <script type="text/javascript">
    App.modules.addContactToProjectForm.toggleAttachForms();
  </script>

  <?php echo submit_button(lang('add contact')) ?>
<?php if ($project_init) { ?>
  <button type="button" onclick="document.location='<?php echo $project->getPermissionsUrl(array('project_init' => 1)); ?>'" style="float: right;"><?php echo lang('done adding contacts') ?></button><div class="clear"></div>
<?php } // if ?>
</form>
