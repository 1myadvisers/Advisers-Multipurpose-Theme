<?php
/**
 * Template part — shown when no posts are found.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="no-results not-found">
	<header class="page-header">
		<h2><?php esc_html_e( 'Nothing Found', 'advisers-multipurpose' ); ?></h2>
	</header>

	<div class="page-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search terms. Please try again with different keywords.', 'advisers-multipurpose' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'It looks like there is no content here yet.', 'advisers-multipurpose' ); ?></p>
		<?php endif; ?>
	</div>
</section>
