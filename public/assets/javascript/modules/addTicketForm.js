App.modules.addTicketForm = {
  notify_companies: {},
  
  /**
   * Click on company checkbox in email notification box. If checkbox is checked
   * all company members need to be checked. If not all members are unchecked
   *
   * @param integer company_id Company ID
   */
  emailNotifyClickCompany: function(company_id) {
    var company_details = App.modules.addTicketForm.notify_companies['company_' + company_id]; // get company details from hash
    if(!company_details) return;
    
    var company_checkbox = $(company_details.checkbox_id);
    
    for(var i = 0; i < company_details.users.length; i++) {
      $(company_details.users[i].checkbox_id).checked = company_checkbox.checked;
    } // if
  }, // emailNotifyClickCompany
  
  /**
   * Click on company member. If all members are checked company should be checked too,
   * false othervise
   *
   * @param integer company_id
   * @param integer user_id
   */
  emailNotifyClickUser: function(company_id, user_id) {
    var company_details = App.modules.addTicketForm.notify_companies['company_' + company_id]; // get company details from hash
    if(!company_details) return;
    
    // If we have all users checked check company box, else uncheck it... Simple :)
    var all_users_checked = true;
    for(var i = 0; i < company_details.users.length; i++) {
      if(!$(company_details.users[i].checkbox_id).checked) all_users_checked = false;
    } // if
    
    $(company_details.checkbox_id).checked = all_users_checked;
  }, // emailNotifyClickUser
  
  
  /**
   * Add click event on due date element to toggle radio button
   *
   * @param string due_date_base_id
   * @param string radio_button_id
   */
  setupRadioButtonToggle: function(due_date_base_id, radio_button_id) {
    document.getElementsByName(due_date_base_id+"_month")[0].onfocus = function(){ $(radio_button_id).checked = true;};
    document.getElementsByName(due_date_base_id+"_day")[0].onfocus = function(){ $(radio_button_id).checked = true;};
    document.getElementsByName(due_date_base_id+"_year")[0].onfocus = function(){ $(radio_button_id).checked = true;};
  } // setupRadioButtonToggle
};
