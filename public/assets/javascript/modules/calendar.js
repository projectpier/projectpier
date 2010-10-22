App.modules.calendar = {
  
  // Hides milestones and tickets in the calendar
  // and changes style of project label
  toggleProject: function(project_id) {
    jQuery('.project'+project_id).toggle();
    jQuery('#projectLabel'+project_id).toggleClass('off');
  }, // toggleProject
  
  // Makes an element unselectable
  makeUnselectable: function(element) {
    if (typeof(element) == 'string')
      element = document.getElementById(element);
    if (elem) {
      element.onselectstart = function() { return false; };
      element.style.MozUserSelect = "none";
      element.style.KhtmlUserSelect = "none";
      element.unselectable = "on";
    } // if
  } // makeUnselectable
}