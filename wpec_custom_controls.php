<?php
class WPEC_Theme_Customizer_Slider_Control extends WP_Customize_Control {
	public $type    = '';
	public $removed = '';
	public $context;
	public $min = 600;
	public $max = 1200;
	public $css;
	public function __construct( $manager, $id, $args ) {
		parent::__construct( $manager, $id, $args );
	}

	public function enqueue() {
		wp_enqueue_script('wpec-tc-utils', DIRECTORY . '/js/wpec-theme-customizer-utils.js', array('jquery','jquery-ui'));
		wp_enqueue_script('jquery-ui', DIRECTORY . '/js/jquery-ui-1.8.20.custom.min.js', array('jquery'));
		wp_enqueue_style('jquery-ui-aristo', DIRECTORY . '/css/aristo/aristo.css');
		wp_enqueue_style('wpec-tc-custom-controls', DIRECTORY . '/css/wpec-tc-custom-controls.css');
	}

	public function to_json() {
		parent::to_json();

		$this->json['removed'] = $this->removed;

		if ( $this->context )
			$this->json['context'] = $this->context;
	}

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class='wpec-tc-control-wrapper'>
				<p>
				<div class='wpec-tc-slider''></div>
				</p>
				<p>
					Value <input id='amount' value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?>/>
				</p>
			</div>
		</label>
		<script>
		jQuery(function() {
			jQuery( ".wpec-tc-slider" ).slider(
				{
				range: "min",
				value: <?php echo esc_attr( $this->value() ); ?>,
				min: <?php echo $this->min; ?>,
				max: <?php echo $this->max; ?>,
				slide: function( event, ui ) {
					jQuery("#amount").val(ui.value );
					var e = jQuery.Event("keyup");
					e.which = 50; // # Some key code value
					jQuery("#amount").trigger(e); //trigger typing event
							}
						}
					)
			});
		//show updates live without refresh
		wp.customize('<?php echo esc_attr( $this->value() ); ?>',function( value ) {
        value.bind(function(to) {
            jQuery('#access, #access .menu-header, div.menu, #colophon, #branding, #main, #wrapper').width(to);
	        });
	    });

		</script>
		<?php
	}
} 
?>