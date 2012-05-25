<?php
/**
 * Class for importing and exporting the current options used by
 * WPEC Theme Customizer controls. The array of options and their 
 * values is updated each time the controls panel is built and only
 * their initial value is saved. Output is of the format
 * 
 * <WPEC-Theme-Customizer-Options>
 *     <option>
 * 			<name></name>
 * 			<value></value>
 * 	   </option>
 * </WPEC-Theme-Customizer-Options>
 * 
 */
 
 /**
 * this is my added comment
 */
 
class WPEC_Theme_Customizer_XML{
	private $options;
	
	public function __construct($option_list = 'wpec_tc_active_controls_option_list'){
		$this->options = get_option($option_list);	
	}	
	/**
	 * parse the given XML and update the option in the database
	 */
	public function import_options($xml){
		$xmlDoc = new SimpleXMLElement(file_get_contents($xml));
		$optionCount = 0;
		foreach($xmlDoc->option as $option)
		{
			$name = $option->name;
			$value = $option->value;
			if($name != '')
			{
				if($value = '')
				$value = 0;
				update_option($name,$value);
				$optionCount++;
			}
		}		
	}
	/**
	 * read used options and their values from array and force download
	 * of xml file
	 */
	public function export_options(){
		$xmlDoc = new DOMDocument();
		$xmlDoc->createComment('This file contains the current options used by the active 
		WPEC Theme Customizer controls and their corresponding values');
		//create root element
		$root = $xmlDoc->appendChild(
		$xmlDoc->createElement('WPEC-Theme-Customizer-Options'));
		foreach($this->options as $option)
		{
		  //create an option element
		  $option_tag = $root->appendChild(
		              $xmlDoc->createElement('option'));
		  //create the name element
		  $option_tag->appendChild(  
		    $xmlDoc->createElement('name', $option['name']));
		  //create the value element
		  $option_tag->appendChild(
		    $xmlDoc->createElement('value', $option['value']));

		}
		//make the output pretty
		$xmlDoc->formatOutput = true;
		//modify the headers to force xml download
		header('Content-Disposition: attachment;filename=wpec-theme-customizer-settings.xml');
    	header('Content-Type: text/xml');
		echo $xmlDoc->saveXML();
		//prevent further output
		exit(); 
	}
}
?>