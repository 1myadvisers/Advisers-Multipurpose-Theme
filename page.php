<?php
/**
 * The default template for displaying pages.
 *
 * This IS the "Default Template" WordPress assigns automatically to every
 * page unless the Landing Page template is chosen from Page Attributes —
 * giving easy, no-setup customisation out of the box,
 * with sidebar position and header/footer visibility still overridable
 * per page from the "Layout Options" box in the editor.
 *
 * SEO note (v1.3.3): every page now always has exactly ONE H1. The title is
 * printed as the H1 unless the page content already contains its own H1 (e.g.
 * the "Hero Section" pattern) - then the title is an H2, so there is never a
 * duplicate and never none. "Hide page title" keeps a screen-reader-only H1
 * when the content has none.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main page-main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php $am_content = advisers_multipurpose_get_the_content_html(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content-wrap' ); ?>>

			<header class="entry-header">
				<?php advisers_multipurpose_singular_title( $am_content ); ?>
			</header>

			<?php if ( has_post_thumbnail() && ! is_page_template( 'page-templates/template-landing.php' ) ) : ?>
				<div class="page-featured-image">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				echo $am_content; // phpcs:ignore WordPress.Security.EscapeOutput -- already filtered by the_content.

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'advisers-multipurpose' ),
						'after'  => '</div>',
					)
				);
				?>
			</div><!-- .entry-content -->

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>

		</article><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; ?>

</main><!-- #primary -->

<?php
if ( advisers_multipurpose_show_sidebar() ) {
	get_sidebar();
}

get_footer();
