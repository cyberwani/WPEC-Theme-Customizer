<?php
class WPEC_Theme_Customizer_XML{
	private $options;
	
	public function __construct($option_list = 'wpec_tc_active_controls_option_list'){
		$this->options = get_option($option_list);	
	}	
	
	public function import_options($xml){
		
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