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
});
