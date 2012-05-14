<?php _d_file_header(__FILE__);?>
<?php
global $wp_query;

?>
<?php if ( wpsc_have_products() ) : ?>
	<?php if( get_option('wpec_toapi_show_breadcrumbs') ==1 ) wpsc_breadcrumb(); ?>
	<?php if( get_option('wpec_toapi_show_categories') ==1) :?>
	<ul class="wpsc_categories">
				<?php wpsc_start_category_query(array('category_group'=> 1, 'show_thumbnails'=> get_option('show_category_thumbnails'))); ?>
						<li>
							<?php wpsc_print_category_image(get_option('category_image_width'), get_option('category_image_height')); ?>
							
							<a href="<?php wpsc_print_category_url();?>" class="wpsc_category_link  <?php wpsc_print_category_classes_section(); ?>"><?php wpsc_print_category_name();?></a>
							<?php if(get_option('wpsc_category_description')) :?>
								<?php wpsc_print_category_description("<div class='wpsc_subcategory'>", "</div>"); ?>				
							<?php endif;?>
							
							<?php wpsc_print_subcategory("<ul>", "</ul>"); ?>
						</li>
				<?php wpsc_end_category_query(); ?>
	</ul>
	<?php endif;?>
	<?php wpsc_product_pagination( 'top' ); ?>
			<div id='wpec-product-grid' 
		<?php if( get_option('wpec_toapi_taxonomy_view') =='grid' ):?>
			class="<?php if(get_option('wpec_toapi_wpsc_grid_view_masonry')==1) echo 'masonry-container';?>">
			<?php wpsc_get_template_part( 'loop', 'grid-products' ); ?>
			</div>
		<?php else:?>
			<?php wpsc_get_template_part( 'loop', 'list-products' ); ?>
		<?php endif;?>
	<?php wpsc_product_pagination( 'bottom' ); ?>
<?php else : ?>
	<?php wpsc_get_template_part( 'feedback', 'no-products' ); ?>
<?php endif; ?> 