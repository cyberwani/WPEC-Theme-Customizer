<?php
/**
 * Loop products template part for lists
 *
 */
 ?>

<?php while ( wpsc_have_products() ): wpsc_the_product(); ?>

	<?php wpsc_get_template_part( 'product', 'product-page-excerpt' ); ?>

<?php endwhile; ?>