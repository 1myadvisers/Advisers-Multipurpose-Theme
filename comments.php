<?php
/**
 * Comments template.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			printf(
				/* translators: %s: comment count */
				esc_html( _n( '%s Comment', '%s Comments', $comment_count, 'advisers-multipurpose' ) ),
				esc_html( number_format_i18n( $comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>

		<?php the_comments_pagination(); ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'advisers-multipurpose' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply' => __( 'Leave a Reply', 'advisers-multipurpose' ),
		)
	);
	?>

</div><!-- #comments -->
