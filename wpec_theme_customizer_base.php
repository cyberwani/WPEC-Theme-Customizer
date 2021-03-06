<?php
/**
 * Class that adds settings to the Wordpress Theme Customizer (Gandalf)
 * Also loads dependancies and creates hooks for capturing option changes
 */
class WPEC_Theme_Customizer_Base {
	
	public function __construct() {
		add_theme_support( 'custom-background' );
		//enque scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
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
 		add_action( 'admin_notices', array($this, 'activation_nag' ));
		//allow nag to be removed
		add_action('wp_loaded', array($this, 'remove_nag'));
		//enqueue admin stylesheet
		add_action('admin_head', array($this, 'admin_styles'));
		//enqueue admin scripts
		add_action('admin_enqueue_scripts', array($this,'admin_scripts'));
	}
	/**
	* See if gandalf features are available
	* TODO make test for valid wp
	*/
	protected function has_valid_wordpress(){
		global $wp_version;	
		return (floatval($wp_version) >= 3.4);
	}
	/**
	 * alert shown on plugin activation
	 */
	public function activation_nag(){
	if(!$this->has_valid_wordpress()):?>
	<div id="message" class="updated fade">
		<p>This version of Wordpress does not support the WPEC Theme Customizer features. Please upgrade Wordpress.</p>
	</div>
	<?php
	elseif (!get_option( 'wpec_theme_customizer_nag' )):
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
		include('option_page.php');
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

	public function has_goldcart(){
		return true; //TODO check for gold cart installation
	}

	/**
	 * echo out admin stylesheet
	 */
	public function admin_styles(){
        echo '<!-- styles for wpec_theme_customizer backend -->
              <link rel="stylesheet" type="text/css" href="'.WPEC_TC_DIRECTORY.'/css/admin-styles.css'. '">';
	} 
	/**
	 * enqueue admin scripts
	 */
	public function admin_scripts(){
		wp_enqueue_script('wpec-tc-admin-js', WPEC_TC_DIRECTORY . '/js/admin.js', array('jquery'));
	} 
	/**
	 * create 'Customize' button in wp admin bar
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar;
		$wp_admin_bar -> add_menu(array('id' => '_d_gandalf', 'href' => get_bloginfo('url') . '/wp-admin/customize.php', 'title' => '<span class="ab-icon ab-gandaf"></span><span class="ab-label">Customize</span>', 'meta' => array('title' => 'Customize theme live', ), ));
		
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
	
	

}

?>