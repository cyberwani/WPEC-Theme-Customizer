<?php
		
		//get link colors and fonts
		$color = get_option("_d_link_color");  
		$color_hover = get_option("_d_link_color_hover");
		$color_visited = get_option("_d_link_color_visited");
		$header_font = get_option('_d_impact_font');
		$body_font = get_option('_d_body_font');
?>
<!-- WPEC THEME CUSTOMIZER STYLES --> 
<style type='text/css'>
/**---------------------------------------------------------------------------
 * 	These styles are created in the wpec_theme_customizer_css.php file
 *  within the WPEC Theme Customzier Plugin directory. Modify them to reroute
 *  Theme Customizer options or alter their affect.
 ----------------------------------------------------------------------------*/
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
		//and finally get fonts
		<?php
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