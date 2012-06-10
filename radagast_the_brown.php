<?php
/**
 * Convenience class for adding settings and controls to Gandalf
 */
class Radagast_The_Brown{
	protected $gandalf;
	protected $options;
	protected $priority_count;
	protected $section_id;
	/**
	 * constucts Radagast with Gandalf pointer
	 *
	 * construct Radagast inside add_action( 'customize_register', 'your_function' );
	 * 'your_function' will be passed an instance of gandalf by WP Core
	 * ie: function your_function($gandalf)...
	 * and then call appropriate methods.
	 */
	public function __construct($gandalf, $section_id = null) {
		include_once('custom_controls.php');  
		include_once('section.php');  
		$this -> gandalf = $gandalf;
		$this -> priority_count = 0;
		$this -> section_id = $section_id;
	}

	/**
	 * Update the list of options being used by active controls and their values at 
	 * the time of last construction
	 */
	public function finish_run(){
		update_option('wpec_tc_active_controls_option_list', $this->options);
	}
	/**
	 * set the section for section class
	 */
	private function set_section($section){
		return is_null($section) ? $this->section_id : $section;
	}
	/**
	 * check for options existence and set with passed value if null
	 * Then add the option to the running tally of options in the $options
	 * array. This is used in finish_run to update the list of export and 
	 * import settings
	 */
	private function check_option($option, $default = '') {
		$option_value = get_option($option);	
		if ($option_value == false) {
			add_option($option, $default);
		}
		$this->options[] = array('name' => $option, 'value' => $option_value);
	}
	/**
	 * add an options section and add to the priority count
	 * @param priority opitonal priority of section
	 * @return return a section object that extends Radagast for adding control
	 */
	public function add_section($id, $name, $priority = null){
		if($priority == null)
		{
			$this -> priority_count++;
			$priority = $this -> priority_count;
		}
		$this->gandalf -> add_section($id, array('title' => __($name), 'priority' => $priority));
		
		return new WPEC_Theme_Customizer_Section($this->gandalf, $name, $id);
	}
	/**
	 * add setting to gandalf
	 * @param $option does not exist in database if so it will be created
	 */
	private function add_setting($option, $default = '') {
		$this -> check_option($option, $default);
		$this -> gandalf -> add_setting($option, array('default' => get_option($option), 'type' => 'option', 'capability' => 'manage_options'));
	}

	/**
	 * add a radio with yes or now label
	 * @param $reverse set to true to reverse values of yes and no
	 * get_option returns 1 or 0 depending on $reverse parameter
	 */
	public function add_radio($option, $title, $default = false, $section = null) {
		$section = $this->set_section($section);
		$yes = '1';
		$no = '0';
		$default = $default ? $yes : $no;
		$this -> add_setting($option, $default);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'radio', 'choices' => array($yes => __('Yes'), $no => __('No'))));
	}
	
	/**
	 * add a radio with yes or now label
	 * @param $reverse set to true to reverse values of yes and no
	 * get_option returns 1 or 0 depending on $reverse parameter
	 */
	public function add_array($option, $title, $fields, $section = null) {
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Array_Control($this -> gandalf, 
		$option, array('settings' => $option, 'label' => __($title), 'section' => $section), $fields));
	}

	/**
	 * add a checkbox
	 * get_option returns 1 for checked empty for unchecked
	 * @param $default value when option is first registered 
	 */
	public function add_checkbox($option, $title, $default = 1, $section = null) {
		$section = $this->set_section($section);
		$this -> add_setting($option, 1);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'checkbox'));
	}

	/**
	 * add a select for for given choices
	 */
	public function add_select($option, $title, $choices, $section = null) {
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section, 'type' => 'select', 'choices' => $choices));
	}

	/**
	 * add an input of type text
	 */
	public function add_textfield($option, $title, $default = null,$section = null) {
		$section = $this->set_section($section, $default);
		$this -> add_setting($option);
		$this -> gandalf -> add_control($option, array('settings' => $option, 'label' => __($title), 'section' => $section));
	}

	/**
	 * add an image control
	 */
	public function add_image_control($option, $title, $section = null) {
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WP_Customize_Image_Control($this -> gandalf, $option, array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}

	/**
	 * add color control
	 */
	public function add_color_control($option, $title, $section = null) {
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WP_Customize_Color_Control($this -> gandalf, $option, array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}

	/**
	 * add info control
	 */
	public function add_info($body, $section = null){
		$section = $this->set_section($section);
		$placeholder = 'wpec_tc_hidden_info_place_holder'.substr(str_replace(trim($body), ' ', '-'),0,5);
		$this -> add_setting($placeholder);
		$this -> gandalf -> add_control(
		new WPEC_Theme_Customizer_Info_Control($this -> gandalf, $placeholder, array('settings' => $placeholder, 'label' => $placeholder, 'section' => $section), $body));
	} 
	/**
	 * add warning
	 */
	public function add_warning($section = null){
		$section = $this->set_section($section);
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
	public function add_subheader($body, $section = null){
		$section = $this->set_section($section);
		$this -> add_setting('wpec_tc_hidden_subheader_place_holder');
		$this -> gandalf -> add_control(
		new WPEC_Theme_Customizer_Info_Control($this -> gandalf, 'wpec_tc_hidden_subheader_place_holder', 
		array('settings' => 'wpec_tc_hidden_subheader_place_holder', 'label' => 'wpec_tc_hidden_subheader_place_holder', 'section' => $section),
		 '<b class="wpec-tc-subheader">'.$body.'</b>'));
		 
	} 

	/**
	 * add slider control
	 */
	public function add_slider($option, $title, $dimens = null, $section = null){
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Slider_Control($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section, 'transport' => 'postMessage'),$dimens));
	} 
	 
	/**
	 * add sortable control
	 */
	public function add_sortable_control($option, $title, $section = null){
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Sortable_Control($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}  
	
	/**
	 * add sortable sidebar control
	 */
	public function add_sortable_sidebar_control($option, $title, $section = null){
		$section = $this->set_section($section);
		$this -> add_setting($option);
		$this -> gandalf -> add_control(new WPEC_Theme_Customizer_Sortable_Sidebar_Control($this -> gandalf, 
		$option, 
		array('settings' => $option, 'label' => __($title), 'section' => $section)));
	}  
	  
	/**
	 * add theme switcher
	 */
	public function add_theme_switcher($section = null){
		$section = $this->set_section($section);
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
	public function add_css_coder($section = null){
		$section = $this->set_section($section);
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
?>