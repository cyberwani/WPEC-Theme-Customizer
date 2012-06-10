<?php
/**
 * @var $this WPEC_Theme_Customizer
 */
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
					The WPEC Theme Customizer alters options in the
					<code>
						WPEC Theme Options API
					</code>
					.
					Your theme must listen for these option for the customizer to have an affect. The customizer
					includes a series of standard WPEC template files that have been modified to include these
					options. You will need to move these templates into <code><?php bloginfo('template_url');?>/
						wp-e-commerce</code>
				</p>
				<table>
					<tr>
						<td>
						<p>
							<?php /**
							 * Form submits POST variable 'wp_tc_checkboxes' as an array of checked values
							 */
							?>
							<form name='wpec_tc_template_form' id='wpec_tc_template_form' action='options-general.php?page=wpec_theme_customizer_settings' method='post'>
								<b>Template Files</b>
								<?php $this -> list_templates();?>
								<a class='button check-modifier-button' id='check-all'>Select All</a>
								<a class='button check-modifier-button' id='uncheck-all'>Deselect All</a>
								<input type='submit' class='button' value='Migrate Template Files to Theme' />
							</form>
							<img src="http://jackmahoney.co.nz/_dollars/wp-admin/images/wpspin_light.gif" class="ajax-feedback" title="" alt="">
						</p></td>
						<td>
						<p>
							<b>Current Theme Setup</b>
							<br/>
							<?php $this -> scan_theme_dir();?>
						</p></td>
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
						if (!$options) :
							echo 'No controls initialized';
						else :
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
