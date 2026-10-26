<?php
/**
 * Template Name: Landing Page (No Header/Footer)
 * Template Post Type: page
 *
 * Bare canvas for standalone landing pages (ad campaigns, lead-gen pages)
 * where the theme's site header/footer chrome should not appear at all.
 * Build the whole page from block patterns (Hero, Features, CTA, etc.).
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'landing-page-template' ); ?>>
<?php wp_body_open(); ?>

<main id="primary" class="site-main landing-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<?php
		$am_content = advisers_multipurpose_get_the_content_html();
		// v1.3.3: a landing page built without an H1 still gets one (screen-reader-only,
		// so the design is untouched). Content that has its own H1 is left alone.
		if ( ! advisers_multipurpose_html_has_h1( $am_content ) ) {
			printf( '<h1 class="screen-reader-text">%s</h1>', wp_kses_post( get_the_title() ) );
		}
		?>
		<div class="entry-content">
			<?php echo $am_content; // phpcs:ignore WordPress.Security.EscapeOutput -- already filtered by the_content. ?>
		</div>
		<?php
	endwhile;
	?>
</main>

<?php wp_footer(); ?>
</body>
</html>
