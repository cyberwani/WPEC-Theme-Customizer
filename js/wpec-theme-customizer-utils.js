/**
 * Utility functions for WPEC Theme Customizer
 */
jQuery(document).ready(function() {
	jQuery('body').first().append('<div id="wpec-tc-loading-bar"></div>');
	jQuery('#wpec-tc-loading-bar').hide();

	jQuery("#wpec-tc-loading-bar").ajaxStart(function() {
		jQuery("#wpec-tc-loading-bar").show();
	});
	jQuery('#wpec-tc-loading-bar').ajaxStop(function() {
		jQuery("#wpec-tc-loading-bar").hide();
	});
});
