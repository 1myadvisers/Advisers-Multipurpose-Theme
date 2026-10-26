<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main error-404-main">

	<header class="page-header">
		<?php advisers_multipurpose_entry_title( 'h1' ); ?>
	</header>

	<div class="page-content">
		<p><?php esc_html_e( 'It looks like nothing was found at this location. Try a search below.', 'advisers-multipurpose' ); ?></p>
		<?php get_search_form(); ?>
	</div>

</main><!-- #primary -->

<?php get_footer();
