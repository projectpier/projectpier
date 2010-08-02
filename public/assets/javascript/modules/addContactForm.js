App.modules.addContactForm = {
  
  /**
  * Change state on generate random password checkbox
  *
  * @param void
  * @return null
  */
  generateRandomPasswordClick: function() {
    if($('userFormRandomPassword').checked) {
      $('userFormPasswordInputs').style.display = 'none';
    } else {
      $('userFormPasswordInputs').style.display = 'block';
    } // if
  },
  
  generateSpecifyPasswordClick: function() {
    if($('userFormSpecifyPassword').checked) {
      $('userFormPasswordInputs').style.display = 'block';
    } else {
      $('userFormPasswordInputs').style.display = 'none';
    } // if
  },
  
  /**
   * Switch new/existing company forms based on selected option
   */
  toggleCompanyForms: function() {
    if($('contactFormExistingCompany').checked) {
      $('contactFormExistingCompanyControls').style.display = 'block';
      $('contactFormNewCompanyControls').style.display = 'none';
    } else {
      $('contactFormExistingCompanyControls').style.display = 'none';
      $('contactFormNewCompanyControls').style.display = 'block';
    } // if
  }, // toggleCompanyForms
  
  /**
   * Toggles user account sub-form based on selected option
   */
  toggleUserAccountForm: function() {
    if($('contactFormNoUserAccount').checked) {
      $('contactFormUserAccountControls').style.display = 'none';
    } else {
      $('contactFormUserAccountControls').style.display = 'block';
    } // if
  } // toggleUserAccountForm
}
