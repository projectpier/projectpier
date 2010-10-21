(function(jQuery) {
	jQuery.widget("ui.combobox", {
		_create: function() {
			var self = this;
      var select = this.element.hide();
			var input = jQuery("<input>")
				.insertAfter(select)
				.attr('value', jQuery.merge(select.children("option[selected]"), select.children('optgroup').children('option[selected]')).html())
				.autocomplete({
					source: function(request, response) {
						var matcher = new RegExp(request.term, "i");
						response(jQuery.merge(
						  select.children("option"), select.children("optgroup").children("option")).map(function() {
						    var text = jQuery(this).text();
						    if (jQuery(this).parent().attr('tagName') == "OPTGROUP") {
						      var label = jQuery(this).text()+" @ "+this.parentElement.label;
						    } else {
                  var label = text;
						    }
  							if (this.value && !(this.value == 0 && text =="") && (!request.term || matcher.test(label))) {
  								return {
  									id: this.value,
  									label: label.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + jQuery.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>"),
  									value: text
  								};
								}
  						})
					  );
					},
					delay: 0,
					select: function(event, ui) {
						if (!ui.item) {
							// remove invalid value, as it didn't match anything
							jQuery(this).val("");
							return false;
						}
						select.val(ui.item.id);
						self._trigger("selected", event, {
							item: select.find("[value='" + ui.item.id + "']")
						});
						
					},
					minLength: 0
				})
				.click(function() {this.select();})
				.addClass("ui-widget ui-widget-content ui-corner-left");
			jQuery("<button>&nbsp;</button>")
			.attr("tabIndex", -1)
			.attr("title", "Show All Items")
			.insertAfter(input)
			.button({
				icons: {
					primary: "ui-icon-triangle-1-s"
				},
				text: false
			}).removeClass("ui-corner-all")
			.addClass("ui-corner-right ui-button-icon")
			.css('vertical-align', 'bottom')
			.click(function() {
				// close if already visible
				if (input.autocomplete("widget").is(":visible")) {
					input.autocomplete("close");
					return false;
				}
				// pass empty string as value to search for, displaying all results
				input.autocomplete("search", "");
				input.focus();
				input.select();
				return false;
			})
			.children('.ui-button-text').hide();
		}
	});

})(jQuery);
	
jQuery(function() {
	jQuery(".combobox").combobox();
});