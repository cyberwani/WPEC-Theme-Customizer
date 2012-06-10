/**
 * JS for admin controls
 */
jQuery(document).ready(function($){

	if(pagenow != 'settings_page_wpec_theme_customizer_settings')
		return; //proceed if on settings page
	
	//set up check buttons
	$('#wpec_tc_template_form .check-modifier-button').click(function(){
		var bool = ( $(this).attr('id') == 'check-all' );
		$('#wpec_tc_template_form input[type=checkbox]').attr('checked', bool);
	});
	
	
});

  