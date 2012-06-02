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
require('wpec_theme_customizer_base.php');  	  
require('wpec_theme_customizer_radagast.php');  	


class WPEC_Theme_Customizer extends WPEC_Theme_Customizer_Base{
	
	public function __construct(){
		parent::__construct();
		//hook for customizer
		add_action('customize_register', array($this, 'populate_gandalf'));     
	}
	/**
	 * Action hook for Theme Customizer (Gandalf)
	 * Uses Radagast class to add controls in a cleaner way
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
$wpec_theme_customizer = new WPEC_Theme_Customizer();
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