/**
 * Utility functions for WPEC Theme Customizer
 */

//ensure no conflicts for all controls
jQuery.noConflict();

jQuery(document).ready(function($) {
	
	//create loading bar and get jQuery object	
	$('body').first().append('<div id="wpec-tc-loading-bar"></div>');
	var loadingBar = $("#wpec-tc-loading-bar");
	//hide on init
	loadingBar.hide();
	
	//set onChange functions	
	loadingBar.ajaxStart(function() {
		loadingBar.show();
	});
	loadingBar.ajaxStop(function() {
		loadingBar.hide();
	});
	
	//set key event
	var e = jQuery.Event("keyup");
	e.which = 50;
	

	//setup sliders
	$(".wpec-tc-slider").each(function() {
		var slider = $(this);
		var display = $("#" + slider.data('display'));
		slider.slider({
			range : "min",
			value : slider.data('value'),
			min : slider.data('min'),
			max : slider.data('max'),
			slide : function(event, ui) {
				//set value of display to match slider
				display.val(ui.value);
				//trigger typing event to prompt ajax
				display.trigger(e);
			}
		});
		//listen for typed input and adjust slider
		display.keyup(function() {
			slider.slider('value', display.val());
		});
	});

			
	// var myCodeMirror;
	// var e = jQuery.Event("keyup");
	// e.which = 50;
	// // Some key code value
	// var textarea = document.getElementById('wpec-tc-code-mirror');
// 
	// jQuery('.customize-section').click(function() {
		// if(jQuery(this).hasClass('open')) {
			// myCodeMirror = CodeMirror.fromTextArea(textarea, {
				// onUpdate : codemirrorcallback,
// 
			// });
		// }
	// });
	// function codemirrorcallback() {
		// myCodeMirror.save();
		// jQuery('#wpec-tc-code-mirror-hidden-value').val(escape(textarea.value));
	// }
// 
// 
	// jQuery('#code-mirror-refresh').click(function() {
		// var display = jQuery('#wpec-tc-code-mirror-hidden-value');
		// display.trigger(e);
		// //trigger typing event to prompt ajax
	// });


	
			
	
});
