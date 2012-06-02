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
		//Products Page
		$product_page = $radagast -> add_section('wpec_product_page', 'Product Page');
			$product_page->add_radio('wpec_toapi_pp_rating', 'Show Product Ratings', false);
			$product_page->add_radio('wpec_toapi_pp_price', 'Show Product Price', true);
			$product_page->add_radio('wpec_toapi_pp_desc', 'Show Product Description', true);
			$product_page->add_radio('wpec_toapi_pp_add_desc', 'Show Product Additional Description', false);
			$product_page->add_radio('wpec_toapi_pp_add_to_cart', 'Show Add To Cart Button', true	);
			$product_page->add_radio('wpec_toapi_pp_link_title','Link The Product Title',true);
			$product_page->add_select('wpec_toapi_pp_sort_by','Sort Product By', array('Name', 'Price', 'Time Uploaded', 'Drag and Drop' ));
			$product_page->add_select('wpec_toapi_pp_main_display','Main Page Displays', array('All Products', 'User Selected', 'Categories' ));
			$product_page->add_textfield('wpec_toapi_pp_image_size_width', 'Thumbnail Width', 200);
			$product_page->add_textfield('wpec_toapi_pp_image_size_height', 'Thumbnail Height', 200);
		//Single Product Page			
		$single_products = $radagast->add_section('wpec_single_product', 'Single Product Page');
			$single_products->add_radio('wpec_toapi_sp_rating','Show Product Rating', false);
			$single_products->add_radio('wpec_toapi_sp_price','Show Product Price', true);
			$single_products->add_radio('wpec_toapi_sp_desc','Show Product Description', true);
			$single_products->add_radio('wpec_toapi_sp_add_desc','Show Additional Product Description', true);
			$single_products->add_radio('wpec_toapi_sp_add_to_cart','Show Add To Cart Button', true);
			$single_products->add_radio('wpec_toapi_sp_product_meta','Show Product Meta', false);
			$single_products->add_textfield('wpec_toapi_sp_image_size_width', 'Thumbnail Width', 300);
			$single_products->add_textfield('wpec_toapi_sp_image_size_height', 'Thumbnail Height', 300);
		//General Product Settings
		$general_products = $radagast->add_section('wpec_general_product','General Product Settings');
			$general_products->add_radio('wpec_toapi_gp_show_breadcrumbs','Show Breadcrumbs',false);
			$general_products->add_select('wpec_toapi_button_style', 'Button Styles',
			array('none' => __('None'), 
			'silver' => __('Silver'), 
			'blue' => __('Blue'), 
			'matt-green matt-button' => __('Matt Green'), 
			'matt-orange matt-button' => __('Matt Orange'), 
			'yellow' => __('Yellow'), 'red' => __('Red'), ));
		// //Gold Cart Settings
		if($this->has_goldcart()): 
			$gold_cart = $radagast->add_section('wpec_gold_cart','Gold Cart Settings');
				$gold_cart->add_radio('wpec_toapi_gc_search','Display Search',false);
				$gold_cart->add_select('wpec_toapi_gc_display_gallery','Display Thumbail Gallery', array('Off','Single Product','Main Product','Both'));
				$gold_cart->add_select('wpec_toapi_gc_product_display_view','Product Display View', array('Default','Grid','Grid'));
				$gold_cart->add_textfield('wpec_toapi_gc_gallery_image_size_width', 'Thumbnail Width', 50);
				$gold_cart->add_textfield('wpec_toapi_gc_gallery_image_size_height', 'Thumbnail Height', 50);
				$gold_cart->add_textfield('wpec_toapi_gc_grid_view_size_width', 'Grid Width', 50);
				$gold_cart->add_textfield('wpec_toapi_gc_grid_view_size_height', 'Grid Height', 50);
		endif;
		//Category Settings
		$categories = $radagast->add_section('wpec_categories','Category Settings');
			$categories->add_radio('wpec_toapi_cs_show_image','Show Category Image',false);
			$categories->add_radio('wpec_toapi_cs_desc','Show Category Description',false);
			$categories->add_textfield('wpec_toapi_cs_thumbnail_size_width', 'Category Thumbnail Width', 50);
			$categories->add_textfield('wpec_toapi_cs_thumbnail_size_height', 'Category Thumbnail Height', 50);
		//important call to tidy up all added controls
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