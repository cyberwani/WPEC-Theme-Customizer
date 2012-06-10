/**
 * Utility functions for WPEC Theme Customizer
 */
jQuery(document).ready(function($) {
	
	//create loading bar and get jQuery object	
	$('body').first().append('<div id="wpec-tc-loading-bar"></div>');
	var loadingBar = $("#wpec-tc-loading-bar");
	//hide on init
	loadingBar.hide();
	
	//set onChange functions	
	loadingBar.ajaxStart(function() {
		this.show();
	});
	loadingBar.ajaxStop(function() {
		this.hide();
	});
});
