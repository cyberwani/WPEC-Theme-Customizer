<?php
/**
 * Content archive product template part
 *
 * @package wp-e-commerce
 * @subpackage theme_compat
 * @since Twenty Eleven 1.0
 */
?>

<?php if ( wpsc_have_products() ) : ?>
	<?php if( get_option('wpec_toapi_show_breadcrumbs') ==1 ) wpsc_breadcrumb(); ?>
	<?php wpsc_product_pagination( 'top' ); ?>
	<?php wpsc_get_template_part( 'loop', 'products' ); ?>
	<?php wpsc_product_pagination( 'bottom' ); ?>
<?php else : ?>

	<?php wpsc_get_template_part( 'feedback', 'no-products' ); ?>

<?php endif; ?>