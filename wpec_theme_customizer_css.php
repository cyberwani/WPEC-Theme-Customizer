<?php
		
		//get link colors and fonts
		$color = get_option("_d_link_color");  
		$color_hover = get_option("_d_link_color_hover");
		$color_visited = get_option("_d_link_color_visited");
		$header_font = get_option('_d_impact_font');
		$body_font = get_option('_d_body_font');
		//body and header colors
		$body_text_color = get_option('_d_body_text_color');
		$header_text_color = get_option('_d_header_text_color');

class WPEC_Theme_Customizer_Style_Generator{
	
	public function __construct(){
		
	}
	
	public function add_style($option, $selectors, $css){
	}
	
}

?>
<!-- WPEC THEME CUSTOMIZER STYLES --> 
<style type='text/css'>
/**---------------------------------------------------------------------------
 * 	These styles are created in the wpec_theme_customizer_css.php file
 *  within the WPEC Theme Customzier Plugin directory. Modify them to reroute
 *  Theme Customizer options or alter their affect.
 ----------------------------------------------------------------------------*/
		<?php     
		
		
				$code_mirror = split('<LINEBREAK>', get_option('wpec_tc_css_coder_value'));
				foreach($code_mirror as $code)
				{
					echo "$code".PHP_EOL;
				}
		?>

		<?php if ($color != '' && $color != null):?>
			body table.list_productdisplay h2.prodtitle a:link, 
			body #content table.list_productdisplay h2.prodtitle a:link, 
			body a{
				color: #<?php echo $color;?>;
			}
		<?php endif; ?>
		<?php if ($color_hover != '' && $color_hover != null):?>
			body table.list_productdisplay h2.prodtitle a:hover, 
			body #content table.list_productdisplay h2.prodtitle a:hover,
			body a:hover{
				color: #<?php echo $color;?>; 
			}
		<?php endif; ?>
		<?php if ($color_visited != '' && $color_visited != null):?>
			body table.list_productdisplay h2.prodtitle a:visited, 
			body #content table.list_productdisplay h2.prodtitle a:visited,
			body a:visited{
				color: #<?php echo $color;?>; 
			}
		<?php endif;?>
		<?php if ($body_text_color != '' && $body_text_color != null):?>
			body {
				color: #<?php echo $body_text_color;?>; 
			}
		<?php endif;?>
		<?php if ($header_text_color != '' && $header_text_color != null):?>
			body h1,body h2,body h3,body h4,body h5,body h6,body h7,body h8 {
				color: #<?php echo $header_text_color;?>; 
			}
		<?php endif;?>
		
		<?php if(get_option('wpec_toapi_wpsc_grid_view_item_width')):?>
			#wpec-product-grid .wpsc-product{
				width: <?php echo get_option('wpec_toapi_wpsc_grid_view_item_width');?>px;
			}
		<?php endif;?>
		<?php		//and finally get fonts
		if ($body_font != '' && $body_font != null) {
			//echo elements with body before to override style.css
			$elem_str = "input[type='text'],select,textarea, html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td ";
			$elements = explode(',', $elem_str);
			$count = 0;
			foreach ($elements as $element) {
				if ($count > 0)
					echo ", 
					";
				echo "html " . $element;
				$count++;
			}
			echo "{font-family: '$body_font', sans-serif;}";
		}
	
		if ($header_font != '' && $header_font != null) {
	
			//echo elements with body before to override style.css
			$elements = array('h1', 'h2', 'h2 a', 'h2 a:visited', 'h3', 'h4', 'h5', 'h6', '.site-title');
			$count = 0;
			foreach ($elements as $element) {
				if ($count > 0)
					echo ", 
					";
				echo "body $element";
				$count++;
			}
			echo "{font-family: '$header_font', sans-serif;}";
			
		}
		?>
</style>
<!-- END STYLES -->