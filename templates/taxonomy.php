<?php _d_file_header(__FILE__);?>
<?php
global $wp_query;
?>
<?php if ( wpsc_have_products() ) : ?>
	<?php if( get_option('wpec_toapi_show_breadcrumbs') ==1 ) wpsc_breadcrumb(); ?>

		<?php wpsc_start_category_query(array('category_group'=> 1, 'show_thumbnails'=> get_option('wpec_toapi_cs_show_image'))); ?>
	
				<?php if( get_option('wpec_toapi_cs_show_image') == 1) :?>
					<?php wpsc_print_category_image(get_option('wpec_toapi_cs_thumbnail_size_width'), get_option('wpec_toapi_cs_thumbnail_size_height')); ?>
				<?php endif;?>

				<?php if( get_option('wpec_toapi_cs_desc') == 1) :?>
					<?php if(get_option('wpsc_category_description')) :?>
						<?php wpsc_print_category_description("<div class='wpsc_subcategory'>", "</div>"); ?>				
					<?php endif;?>
				<?php endif;?>
		<?php wpsc_end_category_query(); ?>
	
	<?php wpsc_product_pagination( 'top' ); ?>

		<?php if( get_option('wpec_toapi_gc_product_display_view') =='grid' ):?>
			<div id='wpec-product-grid' class="<?php if(get_option('wpec_toapi_wpsc_grid_view_masonry')==1) echo 'masonry-container';?>">
			<?php wpsc_get_template_part( 'loop', 'grid-products' ); ?>
			</div>
		<?php else:?>
			<?php wpsc_get_template_part( 'loop', 'list-products' ); ?>
		<?php endif;?>
	<?php wpsc_product_pagination( 'bottom' ); ?>
<?php else : ?>
	<?php wpsc_get_template_part( 'feedback', 'no-products' ); ?>
<?php endif; ?> 