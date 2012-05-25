<?php
/*
Plugin Name: WPEC Theme Customizer
Plugin URI: http://www.instinct.co.nz
Description: Customize WPEC using a refined WP Theme Customizer
Author: Instinct - Jack Mahoney
Author URI: http://www.jackmahoney.co.nz
License: A "Slug" license name e.g. GPL2

====== About ======
Contains all the settings for the Theme Customzier (Gandalf)

====== Usage ======
Plugin is object orientated. A new instance of WPEC_Theme_Customizer
calls its contructor. This adds actions and filters.

Most important action is 'customize_register' which calls 'populate_gandalf'

Fucntion populate_gandalf creates an instance of Radagast_The_Brown in the 
customize register hook that constructs itself with a pointer to the WP Core Gandalf
instance.

Radagst contains wrapper functions for adding sections and controls. Controls
require an option - this does not need to exist already as it will be checked
and created if null. Changes to controls in the front end update the passed 
option via ajax.
 
A filter for body classes adds every option that has wpec_toapi_ prefix in
its name plus its value to the body as a class. This allows for top down
css targeting.

Function header_output includes the WPEC_Theme_Customizer_Css.php file inside
the theme's <head></head>. Here you can use php conditionals for options to 
inject css.
 
-------------------------------------------------------------*/
 
//TODO change this to plugin directory
define('WPEC_TC_DIRECTORY', plugins_url().'/wpec-theme-customizer-plugin');
//require exporter class
require('wpec_theme_customizer_xml.php');  	
$wpec_theme_customizer = new WPEC_Theme_Customizer();

/**
 * Class that adds settings to the Wordpress Theme Customizer (Gandalf)
 * Also loads dependancies and creates hooks for capturing option changes
 */
class WPEC_Theme_Customizer {
	
	public function __construct() {
		add_theme_support( 'custom-background' );
		//enque scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		//hook for customizer
		add_action('customize_register', array($this, 'populate_gandalf'));
		//body classes filter
		add_filter('body_class', array($this, 'add_body_classes'));
		//output styles into head
		add_action('wp_head', array($this, 'header_output'));
		//admin bar 'customize' button
		add_action('wp_before_admin_bar_render', array($this, 'admin_bar_menu'));
		//add settings page
		add_action('admin_menu', array($this, 'add_settings_page'));
		//add hook to handle form submissions on settings page
		add_action( 'admin_init', array($this,'admin_init_hook') );
		//show nag if option unset
		if ( !get_option('wpec_theme_customizer_nag') )
 			add_action( 'admin_notices', array($this, 'activation_nag' ));
		//allow nag to be removed
		add_action('wp_loaded', array($this, 'remove_nag'));
		//echo admin stylesheet
		add_action('admin_head', array($this, 'admin_styles'));
		//theme switch files
		//add_filter( 'template', array($this, 'get_theme_template'));
		
	}
	/**
	 * alert shown on plugin activation
	 */
	public function activation_nag(){
	
	if (!get_option( 'wpec_theme_customizer_nag' )):
	?>
	  <div id="message" class="updated fade">
	   <p><?php printf(  '<strong>Configure WPEC Theme Customizer</strong><br /> 
	   The WPEC Theme Customizer requires various template files to be located in your theme directory to function correctly. <a href="%1s">Click here</a> to configure the plugin 
	   to take advantage of these new features.', admin_url( 'options-general.php?page=wpec_theme_customizer_settings&wpec_theme_customizer_notices=gc_ignore' ) ) ?></p>
	  </div> <?php
	endif;
	}
	/**
	 * remove the alert
	 */ 
	public function remove_nag(){
	  if ( isset( $_REQUEST['wpec_theme_customizer_notices'] ) && $_REQUEST['wpec_theme_customizer_notices'] == 'gc_ignore' ) {
	   update_option( 'wpec_theme_customizer_nag', true );
	   wp_redirect( remove_query_arg( 'wpec_theme_customizer_notices' ) );
	  exit();
	  }
	 }
	/**
	 * add settings page
	 */	
	public function add_settings_page(){
		add_options_page('WPEC Theme Customizer', 'WPEC Theme Customizer', 'manage_options', 'wpec_theme_customizer_settings', array($this ,'settings_page_callback'));
	}			
	/**
	 * copy plugin template files into current themes wp-e-commerce folder. If this folder
	 * doesn't exist then it will be created
	 */
	public function migrate_files(){
		//theme directory	
		$theme_dir = get_template_directory().'/';
		$theme_files = scandir($theme_dir);
		//look for wpec folder, create if missing
		$wpec_folder_present = in_array('wp-e-commerce', $theme_files); //bool
		if($wpec_folder_present==false) //is wp-e-commerce folder present?
		{
			mkdir($theme_dir.'wp-e-commerce');
		}
		//path to plugin template files
		$destination = $theme_dir.'wp-e-commerce/';
		$plugin_templates = dirname(__FILE__).'/templates/';
		foreach($_POST['wp_tc_checkboxes'] as $file)
		{
			copy($plugin_templates.$file,$destination.$file);
		}
	}
	
	/**
	 * callback for settings page - will migrate files if $_POST['wp_tc_checkboxes'] contains 
	 * checked files
	 */
	public function settings_page_callback(){
	?>
	
	<?php 
	if(isset($_POST['wp_tc_checkboxes']))
	{
		$this->migrate_files();
		unset($_POST['wp_tc_checkboxes']);
	}
	?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32">
				<br>
			</div><h2>WPEC Theme Customizer</h2>
				<div id="metabox-holder" class="metabox-holder">
					<div id="wpec-taxes-rates-container" class="postbox">
						<h3 class="hndle" style="cursor: default">File Migration</h3>
						<div class="inside">
							<p>
								The WPEC Theme Customizer alters options in the <code>
									WPEC Theme Options API</code>
								.
								Your theme must listen for these option for the customizer to have an affect. The customizer
								includes a series of standard WPEC template files that have been modified to include these
								options. You will need to move these templates into <code><?php bloginfo('template_url');
									?>/
									wp-e-commerce</code>
							</p>
							<table>
								<tr>
								<td>
								<p>
									<?php
									/**
									 * Form submits POST variable 'wp_tc_checkboxes' as an array of checked values
									 */
									?>
									<form name='wpec_tc_template_form' action='options-general.php?page=wpec_theme_customizer_settings' method='post'>
									<b>Template Files</b>
									<?php 
									$this->list_templates(); 
									?>
									<input type='submit' class='button' value='Migrate Template Files to Theme' />
									</form>
									<img src="http://jackmahoney.co.nz/_dollars/wp-admin/images/wpspin_light.gif" class="ajax-feedback" title="" alt="">
								</p>
								</td>
								<td>
								<p>
								<b>Current Theme Setup</b><br/>
								<?php $this->scan_theme_dir();?>
								</p>	
								</td>
								</tr>
							</table>
						</div>
					</div>
				<div id="metabox-holder" class="metabox-holder">
					<div id="wpec-taxes-rates-container" class="postbox">
						<h3 class="hndle" style="cursor: default">Import / Export</h3>
						<div class="inside">
							<p>
								Import your options and their values for the current controls
							</p>
							<p>
								<?php
								$options = get_option('wpec_tc_active_controls_option_list');
								if(!$options):
									echo 'No controls initialized';
								else:
									echo "
									<form action='' method='get'>
									<input name='page' value='wpec_theme_customizer_settings' hidden='hidden'/>
									<input type='submit' name='export' class='button' value='Export' />
									</form>";
									echo '<form enctype="multipart/form-data" action="" method="POST">
									<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
									<input name="uploaded_wpectc_file" type="file" />
									<input class="button" type="submit" value="Upload File" />
									</form> ';
								endif;
								?>
							</p>
						</div>
					</div>
			</div>
		</div>
	<?php
	}

	/**
	 * add the hooks to handle form submissions on the settings page
	 */
	public function admin_init_hook(){
		if($_GET['page'] == 'wpec_theme_customizer_settings'):	
			if($_GET['export']):
				$exporter = new WPEC_Theme_Customizer_XML();
				$exporter->export_options();	
			endif;
			if($_FILES['uploaded_wpectc_file']):
				$xml = $_FILES['uploaded_wpectc_file']['tmp_name'];
				$importer = new WPEC_Theme_Customizer_XML();
				$importer->import_options($xml);
				unset($_FILES['uploaded_wpectc_file']);
			endif;    
		endif;
	}

	/**
	 * echo out admin stylesheet
	 */
	public function admin_styles(){
        echo '<!-- styles for wpec_theme_customizer backend -->
              <link rel="stylesheet" type="text/css" href="'.WPEC_TC_DIRECTORY.'/css/admin-styles.css'. '">';
	} 
	/**
	 * create 'Customize' button in wp admin bar
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar;
	    $theme_title = strtolower(get_current_theme());
		$wp_admin_bar -> add_menu(array('id' => '_d_gandalf', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?customize=on&theme='.$theme_title, 'title' => '<span class="ab-icon ab-gandaf"></span><span class="ab-label">Customize</span>', 'meta' => array('title' => 'Customize theme live', ), ));
		
	}
	/**
	 * enqueue the scripts and styles
	 */
	public function enqueue_scripts() {
		//css
		wp_enqueue_style('wpsc-custom-buttons', WPEC_TC_DIRECTORY . '/css/custom-buttons.css');
		wp_enqueue_style('wpsc-gandalf-styles', WPEC_TC_DIRECTORY . '/css/gandalf-styles.css');
		//js  
		if(get_option('wpec_toapi_wpsc_grid_view_masonry') == 1)
		{
			//masonry scripts if preference enabled
			wp_enqueue_script('masonry', WPEC_TC_DIRECTORY.'/js/jquery.masonry.min.js', array('jquery'));
			wp_enqueue_script('imagesLoaded', WPEC_TC_DIRECTORY.'/js/jquery.imagesloaded.min.js', array('masonry'));
			wp_enqueue_script('wpec-masonry', WPEC_TC_DIRECTORY . '/js/wpec-masonry.js', array('masonry','imagesLoaded'));
		}
	} 
	/**
	 * add a body class for every option with a 'wpec_toapi_' prefix 
	 * and append its current value
	 */
	public function add_body_classes($classes) {
		$all_options = get_alloptions();
		$options = array_keys($all_options);
		foreach($options as $option)
		{
			$found = strpos($option,'wpec_toapi');
			if($found===0)//if is a subset of theme options api
			{  
				$classes[] = $option.'-'.get_option($option);
			}
		}
			
		return $classes;
	}
	/**
	 * scan the current theme directory for template files and
	 * print out those found
	 * @param $echo bool if false will return a true or false for whether wp-e-commerce folder is present for this theme
	 */
	public function scan_theme_dir($echo = true){
		$theme_dir = get_template_directory().'/';
		$theme_files = scandir($theme_dir);
		$wpec_folder_present = in_array('wp-e-commerce', $theme_files); //bool
		if($wpec_folder_present) //is wp-e-commerce folder present?
		{
			$templates = scandir($theme_dir.'wp-e-commerce/');
			if($echo == true)
			{
			foreach($templates as $template)
				if(strpos($template, '.php')!=false)
					echo "<div class='file-wrapper'><span class='file-icon'></span> $template</div>";
			}
			else 
			{
				return true;
			}
		}
		else
		{
			if($echo == true)			
				echo 'No <code>wp-e-commerce</code> folder found in theme. It will be created upon template migration.';
			else
				return false;
		}
	}
	/**
	 * list templates included with the plugin
	 */
	public function list_templates(){
		$templates = scandir(dirname(__FILE__).'/templates/');
		echo "<div id='template-checkboxes'>";
		foreach($templates as $template)
		{
			if(strpos($template,'.php') !== false) //if file is .php
			{
				echo "<input type='checkbox' name='wp_tc_checkboxes[]' value='$template'/> $template<br/>";
			}
		}
		echo "</div>";
	}
	/**
	 * echo raw styles into header
	 */
	public function header_output() {
		//arrange sidebars
		$current_sidebar_order = wp_get_sidebars_widgets(); //constuct a proper array from this
		$preferred_sidebar_order = get_option('wpec_toapi_wpsc_sortable_widget_order');
		//a string following format sidebar1==widget1,widget2,widget3||sidebar2==widget1,widget2 ... etc
		$sidebars = explode('||',$preferred_sidebar_order);
		foreach($sidebars as $sidebar)
		{
			$sidebar_explosion = explode('==',$sidebar);
			$current_name = $sidebar_explosion[0];
			$widgets = $sidebar_explosion[1];
			if($current_sidebar_order[$current_name] && $widgets)
			{
				$current_sidebar_order[$current_name] = explode(', ',$widgets);
			}
		}
		if($current_sidebar_order)
			wp_set_sidebars_widgets($current_sidebar_order);
		
		include('wpec_theme_customizer_css.php');
	}
	
	
	
	
	/**
	 * Action hook for Theme Customizer (Gandalf)
	 * Use Radagast class to add controls in a cleaner way
	 * Add sections with $gandalf
	 * @param $gandalf instance of ThemeCustomizer passed by WPCore
	 */
	public function populate_gandalf($gandalf) {
		$radagast = new Radagast_The_Brown($gandalf);
		//--------------------wpec help --------------------//		
		$gandalf -> add_section('wpec_help_page', array('title' => __('WPEC Help'), 'priority' => 1));
		$radagast -> add_warning('wpec_help_page');
		$radagast->add_info('<p><img style="float:left;margin:0px 10px 5px 0px;" src="http://jackmahoney.co.nz/_dollars/wp-content/uploads/getshopped4.png"/>
		Welcome to the WPEC Theme Customizer. Some options will taking longer to update than others. Make sure you save changes when you
		are finished. See <a href="#" target="blank">getshopped.org/wpec-theme-customizer</a> for more information.','wpec_help_page');
		//--------------------theme section--------------------//	
		$gandalf -> add_section('wpec_theme_section', array('title' => __('Theme Settings'), 'priority' => 0));
	    $radagast -> add_css_coder('wpec_theme_section');
		$radagast-> add_sortable_sidebar_control('wpec_toapi_wpsc_sortable_widget_order', 'Sortable Widgets', 'wpec_theme_section');
		
		//--------------------wpec products --------------------//
		$gandalf -> add_section('wpec_product_settings', array('title' => __('WPEC Product Settings'), 'priority' => 1));

		$radagast -> add_checkbox('wpec_toapi_product_ratings', 'Product Ratings', 'wpec_product_settings');
		$radagast -> add_checkbox('wpec_toapi_link_in_title', 'Link in Title', 'wpec_product_settings');
		//button styles
		$radagast -> add_select('wpec_toapi_button_style', 'Button Styles', 'wpec_product_settings', array('none' => __('None'), 'silver' => __('Silver'), 'blue' => __('Blue'), 'matt-green matt-button' => __('Matt Green'), 'matt-orange matt-button' => __('Matt Orange'), 'yellow' => __('Yellow'), 'red' => __('Red'), ));
		//--------------------wpec products page--------------------//
	
		$gandalf -> add_section('wpec_product_page', array('title' => __('WPEC Products Page'), 'priority' => 2));
		//wpsc_category_grid_view
		$radagast -> add_select('wpec_toapi_taxonomy_view', 'Product Display Format', 'wpec_product_page', array('list' => __('List'), 'grid' => __('Grid')));
		//grid item width slider
		$radagast-> add_slider_control('wpec_toapi_wpsc_grid_view_item_width', 'Grid View Item Width', 'wpec_product_page', array('min'=>50,'max'=>500));
		//add sortable widget control
		
		
		//grid description 	
		$radagast -> add_checkbox('wpec_toapi_wpsc_products_page_description', 'Description', 'wpec_product_page');
		$radagast -> add_checkbox('wpec_toapi_wpsc_grid_view_masonry', 'Grid View Masonry', 'wpec_product_page');
		//show categories and breadcrumbs
		$radagast -> add_checkbox('wpec_toapi_show_categories', 'Show categories', 'wpec_product_page', 0);
		$radagast -> add_checkbox('wpec_toapi_show_breadcrumbs', 'Show Breadcrumbs', 'wpec_product_page');
		//Sort Product By
		$radagast -> add_select('wpsc_sort_by', 'Sort Product By', 'wpec_product_page', array('name' => __('Name'), 'price' => __('Price'), 'dragndrop' => __('Drag n Drop'), 'id' => __('Id')));
		
		//--------------------wpec thumbnails--------------------//
		$gandalf -> add_section('wpec_thumbnails', array('title' => __('WPEC Thumbnails'), 'priority' => 2));
		//crop thumbnails
		$radagast -> add_checkbox('wpsc_crop_thumbnails', 'Crop Thumbnails', 'wpec_thumbnails');
		//default image sizes
		$radagast -> add_textfield('product_image_width', 'Default Product Thumbnail Width', 'wpec_thumbnails');
		$radagast -> add_textfield('product_image_height', 'Default Product Thumbnail Height', 'wpec_thumbnails');
		//category image sizes
		$radagast -> add_subheader('Taxonomy Thumbnail', 'wpec_thumbnails');  
		$radagast -> add_textfield('category_image_width', 'Taxonomy Thumbnail Width', 'wpec_thumbnails');
		$radagast -> add_textfield('category_image_height', 'Taxonomy Thumbnail Height', 'wpec_thumbnails');
		//single product group image sizes
		$radagast -> add_textfield('single_view_image_width', 'Single Product Image Width', 'wpec_thumbnails');
		$radagast -> add_textfield('single_view_image_height', 'Single Product Image Height', 'wpec_thumbnails');
		
		//--------------------wpec category section--------------------//
		$gandalf -> add_section('wpec_categories', array('title' => __('WPEC Categories'), 'priority' => 3));
		//category description
		$radagast -> add_checkbox('wpsc_category_description', 'Show Product Category Description', 'wpec_categories');
		//category thumbnails
		$radagast -> add_checkbox('show_category_thumbnails', 'Show Product Category Thumbnails', 'wpec_categories');
		//category gridview
		$radagast -> add_checkbox('wpsc_category_grid_view', 'Category Grid View', 'wpec_categories');
		//--------------------header section--------------------//
		//add logo image
		$radagast -> add_image_control('_d_logo_image', 'Logo Image', 'header');
		//display logo checkbox
		$radagast -> add_checkbox('_d_display_logo_image', 'Display Logo', 'header');
		//add search checkbox
		$radagast -> add_checkbox('_d_header_search', 'Show Search Bar', 'header');

		//----------------text section-------------------//
		//add section
		$gandalf -> add_section('text', array('title' => __('Text Styles'), 'priority' => 4, ));
		//add link color
		$radagast -> add_color_control('_d_link_color', 'Link Color', 'text');
		//add link hover
		$radagast -> add_color_control('_d_link_color_hover', 'Link Color Hover', 'text');
		//add link visited
		$radagast -> add_color_control('_d_link_color_visited', 'Link Color Visited', 'text');
		//add font choices
		$font_choices = array('Helvetica Neue' => 'Helvetica Neue', 'Lato' => 'Lato', 'Arvo' => 'Arvo', 'Muli' => 'Muli', 'Play' => 'Play', 'Oswald' => 'Oswald');
		//add header font
		$radagast -> add_select('_d_impact_font', 'Header Font', 'text', $font_choices);
		//add body font
		$radagast -> add_select('_d_body_font', 'Body Font', 'text', $font_choices);
		//body color
		$radagast -> add_color_control('_d_header_text_color', 'Header Text Color', 'text');
		$radagast -> add_color_control('_d_body_text_color', 'Body Text Color', 'text');
		
		//import call to tidy up all added controls
		$radagast -> finish_run();

	}

}

/**
 * Convenience class for adding settings and controls to Gandalf
 */
class Radagast_The_Brown{
	protected $gandalf;
	protected $options;
	/**
	 * constucts Radagast with Gandalf pointer
	 *
	 * construct Radagast inside add_action( 'customize_register', 'your_function' );
	 * 'your_function' will be passed an instance of gandalf by WP Core
	 * ie: function your_function($gandalf)...
	 * and then call appropriate methods.
	 */
	public function __construct($gandalf) {
		include_once('wpec_custom_controls.php');  
		$this -> gandalf = $gandalf;
	}

	/**
	 * Update the list of options being used by active controls and their values at 
	 * the time of last construction
	 */
	public function finish_run(){
		update_option('wpec_tc_active_controls_option_list', $this->options);
	}

	/**
	 * check for options existence and set with passed value if null
	 * Then add the option to the running tally of options in the $options
	 * array. This is used in finish_run to update the list of export and 
	 * import settings
	 */
	public function check_option($option, $default = '') {
		$option_value = get_option($option);	
		if ($option_value == false) {
			add_option($option, $default);
		}
		$this->options[] = array('name' => $option, 'value' => $option_value);
	}

	/**
	 * add setting to gandalf
	 * @param $option does not exist in database if so it will be created
	 */
	public function add_setting($option, $default = '') {
		$this -> check_option($option, $default);
		$this -> gandalf -> add_setting($option, array('default' => get_option($option), 'type' => 'option', 'capability' => 'manage_options'));
	}

	/**
	 * add a radio with yes or now label
	 * @param $reverse set to true to reverse values of yes and no
	 * get_option returns 1 or 0 depending on $reverse parameter
	 */
	public function add_radio($option, $title, $section, $reverse = false) {
		$yes = ($reverse == false) ? '1' : '0';
		$no = ($reverse == false) ? '0' : '1';
		$this -> add_setting($option, $no);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'radio', 'choices' => array($yes => __('Yes'), $no => __('No'))));
	}

	/**
	 * add a checkbox
	 * get_option returns 1 for checked empty for unchecked
	 * @param $default value when option is first registered 
	 */
	public function add_checkbox($option, $title, $section, $default = 1) {
		$this -> add_setting($option, 1);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'checkbox'));
	}

	/**
	 * add a select for for given choices
	 */
	public function add_select($option, $title, $section, $choices) {
		$this -> add_setting($option);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'select', 'choices' => $choices));
	}

	/**
	 * add an input of type text
	 */
	public function add_textfield($option, $title, $section) {
		$this -> add_setting($option);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section));
	}

	/**
	 * add an image control
	 */
	public function add_image_control($option, $title, $section) {
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WP_Customize_Image_Control($this -> gandalf, $option, array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}

	/**
	 * add color control
	 */
	public function add_color_control($option, $title, $section) {
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WP_Customize_Color_Control($this -> gandalf, $option, array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}

	/**
	 * add info control
	 */
	public function add_info($body, $section){
		$this -> add_setting('wpec_tc_hidden_info_place_holder');
		$this -> gandalf -> add_control(
		new WPEC_Theme_Customizer_Info_Control($this -> gandalf, 'wpec_tc_hidden_info_place_holder', 
		array('settings' => 'wpec_tc_hidden_info_place_holder', 'label' => 'wpec_tc_hidden_info_place_holder', 'section' => $section), $body));
	} 
	/**
	 * add warning
	 */
	public function add_warning($section){
		$theme_dir = get_template_directory().'/';
		$theme_files = scandir($theme_dir);
		$wpec_folder_present = in_array('wp-e-commerce', $theme_files);
		if($wpec_folder_present == false):
			$body = '<div class="wpec-tc-warning">Warning no <code>wp-e-commerce</code> folder was found in your theme directory</div>';
		$this -> add_setting('wpec_tc_hidden_warning_place_holder');
		$this -> gandalf -> add_control(   
		new WPEC_Theme_Customizer_Info_Control($this -> gandalf, 'wpec_tc_hidden_warning_place_holder', 
		array('settings' => 'wpec_tc_hidden_warning_place_holder', 'label' => 'wpec_tc_hidden_warning_place_holder', 'section' => $section), $body));
		endif;
	}  
  
	/**
	 * add a subheader
	 */
	public function add_subheader($body, $section){
		$this -> add_setting('wpec_tc_hidden_subheader_place_holder');
		$this -> gandalf -> add_control(
		new WPEC_Theme_Customizer_Info_Control($this -> gandalf, 'wpec_tc_hidden_subheader_place_holder', 
		array('settings' => 'wpec_tc_hidden_subheader_place_holder', 'label' => 'wpec_tc_hidden_subheader_place_holder', 'section' => $section),
		 '<b class="wpec-tc-subheader">'.$body.'</b>'));
		 
	} 

	/**
	 * add slider control
	 */
	public function add_slider_control($option, $title, $section, $dimens = null){
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Slider_Control($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section, 'transport' => 'postMessage'),$dimens));
	} 
	 
	/**
	 * add sortable control
	 */
	public function add_sortable_control($option, $title, $section){
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Sortable_Control($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}  
	
	/**
	 * add sortable sidebar control
	 */
	public function add_sortable_sidebar_control($option, $title, $section){
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Sortable_Sidebar_Control($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}  
	  
	/**
	 * add theme switcher
	 */
	public function add_theme_switcher($section){
		$title = 'Theme Switcher';
		$themes = get_themes();
		$names = array();  
		foreach($themes as $theme){
			$names[$theme->stylesheet] = $theme->name;
		}     
		$option = 'wpec_tc_theme_switcher_selection';
		$this -> add_setting($option);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'radio', 
		'choices' => $names));
	}
	/**
	 * add theme switcher
	 */
	public function add_css_coder($section){
		$title = 'CSS Coder';
		$themes = get_themes();
		$names = array();  
		foreach($themes as $theme){
			$names[$theme->stylesheet] = $theme->name;
		}     
		$option = 'wpec_tc_css_coder_value';
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Code_Mirror($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}

}


/**--------------------------------------
 *  Echo file in use into html comments
 --------------------------------------*/
function _d_file_header($file, $echo = true) {
	$str = '<!-- using file ' . basename($file) . ' -->';
	if ($echo)
		echo $str;
	else
		return $str;
}
?>