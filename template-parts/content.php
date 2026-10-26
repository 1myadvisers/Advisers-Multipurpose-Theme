<?php
/**
 * Post card template part — used on index, archive, search.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<a class="post-card-thumb" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'advisers-multipurpose-card' ); ?>
		</a>
	<?php endif; ?>

	<div class="post-card-body">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<div class="entry-meta">
			<?php advisers_multipurpose_posted_on(); ?>
		</div>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>

		<a class="read-more" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_theme_mod( 'advisers_multipurpose_read_more_text', __( 'Read More', 'advisers-multipurpose' ) ) ); ?></a>
	</div>

</article>
