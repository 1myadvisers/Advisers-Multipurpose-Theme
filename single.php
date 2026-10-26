<?php
/**
 * The template for displaying single blog posts.
 *
 * Unlike page.php, single posts DO get a theme-printed H1 (the post title),
 * since blog articles have no block-builder content that would otherwise
 * supply one — RankMath still owns meta/schema output, just not the H1 markup.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main single-post-main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php $am_content = advisers_multipurpose_get_the_content_html(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-wrap' ); ?>>

			<header class="entry-header">
				<?php advisers_multipurpose_singular_title( $am_content ); ?>
				<div class="entry-meta">
					<?php advisers_multipurpose_posted_on(); ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="post-featured-image">
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

			<footer class="entry-footer">
				<?php
				$categories = get_the_category_list( ', ' );
				if ( $categories ) {
					echo '<span class="cat-links">' . esc_html__( 'Categories:', 'advisers-multipurpose' ) . ' ' . wp_kses_post( $categories ) . '</span>';
				}
				$tags = get_the_tag_list( '', ', ' );
				if ( $tags ) {
					echo '<span class="tag-links">' . esc_html__( 'Tags:', 'advisers-multipurpose' ) . ' ' . wp_kses_post( $tags ) . '</span>';
				}
				?>
			</footer>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>

		</article>

	<?php endwhile; ?>

</main><!-- #primary -->

<?php
if ( advisers_multipurpose_show_sidebar() ) {
	get_sidebar();
}

get_footer();
