<?php
/**
 * The template for displaying search results.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">

	<header class="page-header">
		<?php advisers_multipurpose_entry_title( 'h1' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="post-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>

	<?php else : ?>

		<?php get_template_part( 'template-parts/content', 'none' ); ?>

	<?php endif; ?>

</main><!-- #primary -->

<?php
if ( advisers_multipurpose_show_sidebar() ) {
	get_sidebar();
}

get_footer();
