App.modules.addContactToProjectForm = {
  
  /**
   * Switch attach contact forms based on selected option (attach existing or
   * attach new contact)
   */
  toggleAttachForms: function toggleAttachForms() {
    if($('contactFormExistingContact').checked) {
      $('contactFormExistingContactControls').style.display = 'block';
      $('contactFormNewContactControls').style.display = 'none';
    } else {
      $('contactFormExistingContactControls').style.display = 'none';
      $('contactFormNewContactControls').style.display = 'block';
    } // if
  } // toggleAttachForms
  
};
