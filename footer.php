<?php
/**
 * Footer template.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether the footer is hidden for the current singular object.
 */
$hide_footer = '';

if ( is_singular() ) {
	$hide_footer = get_post_meta(
		get_queried_object_id(),
		'_advisers_multipurpose_hide_footer',
		true
	);
}

$footer_hidden = ( '1' === (string) $hide_footer );

/**
 * Footer widget layout: 3 rows x 4 columns (12 widget areas).
 *
 * The setting is the maximum number of columns per row (0 hides all
 * footer widgets). Only widget areas that contain widgets are rendered,
 * and each row is sized to the number of active areas so no blank gaps
 * appear. Rows without widgets are skipped completely.
 */
$footer_max_columns = min(
	4,
	max(
		0,
		absint( get_theme_mod( 'advisers_multipurpose_footer_columns', 4 ) )
	)
);

$footer_rows = array();

if ( $footer_max_columns > 0 ) {

	for ( $row = 1; $row <= 3; $row++ ) {

		$active = array();

		for ( $col = 1; $col <= $footer_max_columns; $col++ ) {

			$sidebar_id = 'footer-' . ( ( $row - 1 ) * 4 + $col );

			if ( is_active_sidebar( $sidebar_id ) ) {
				$active[] = $sidebar_id;
			}
		}

		if ( $active ) {
			$footer_rows[ $row ] = $active;
		}
	}
}

/**
 * Top bar.
 */
$topbar_enabled = (bool) get_theme_mod( 'advisers_multipurpose_footer_topbar_enable', false );
$topbar_text    = (string) get_theme_mod( 'advisers_multipurpose_footer_topbar_text', '' );
$topbar_link    = esc_url( (string) get_theme_mod( 'advisers_multipurpose_footer_topbar_link_url', '' ), array( 'http', 'https', 'mailto', 'tel' ) );
$topbar_link_tx = (string) get_theme_mod( 'advisers_multipurpose_footer_topbar_link_text', '' );
$topbar_phone   = (string) get_theme_mod( 'advisers_multipurpose_footer_topbar_phone', '' );
$topbar_email   = sanitize_email( (string) get_theme_mod( 'advisers_multipurpose_footer_topbar_email', '' ) );
$topbar_social  = $topbar_enabled
	&& (bool) get_theme_mod( 'advisers_multipurpose_footer_topbar_social', true )
	&& function_exists( 'advisers_multipurpose_show_social_icons' )
	&& advisers_multipurpose_show_social_icons( 'footer' );

$topbar_has_content = $topbar_text || ( $topbar_link && $topbar_link_tx ) || $topbar_phone || $topbar_email || $topbar_social;

/**
 * Copyright text. Output is restricted to a safe subset of HTML.
 */
$default_copyright = sprintf(
	/* translators: 1: current year, 2: site name. */
	__( '© %1$s %2$s. All rights reserved.', 'advisers-multipurpose' ),
	wp_date( 'Y' ),
	get_bloginfo( 'name' )
);

$copyright = get_theme_mod( 'advisers_multipurpose_footer_copyright', $default_copyright );

$copyright_allowed_html = function_exists( 'advisers_multipurpose_footer_allowed_html' )
	? advisers_multipurpose_footer_allowed_html()
	: array( 'a' => array( 'href' => true, 'rel' => true ), 'br' => array(), 'em' => array(), 'strong' => array() );

$show_bottom_menu   = (bool) get_theme_mod( 'advisers_multipurpose_footer_bottom_menu', true );
$show_bottom_credit = (bool) get_theme_mod( 'advisers_multipurpose_footer_bottom_credit', true );
?>

	</div><!-- .layout-wrap -->
</div><!-- #content -->

<?php if ( ! $footer_hidden ) : ?>

	<footer id="colophon" class="site-footer">

		<?php if ( $topbar_enabled && $topbar_has_content ) : ?>

			<div class="footer-topbar">
				<div class="footer-topbar-inner">

					<?php if ( $topbar_text || ( $topbar_link && $topbar_link_tx ) ) : ?>
						<p class="footer-topbar-message">
							<?php if ( $topbar_text ) : ?>
								<span><?php echo esc_html( $topbar_text ); ?></span>
							<?php endif; ?>
							<?php if ( $topbar_link && $topbar_link_tx ) : ?>
								<a class="footer-topbar-button" href="<?php echo esc_url( $topbar_link ); ?>"><?php echo esc_html( $topbar_link_tx ); ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>

					<?php if ( $topbar_phone || $topbar_email ) : ?>
						<ul class="footer-topbar-contact">
							<?php if ( $topbar_phone ) : ?>
								<li><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $topbar_phone ) ); ?>"><?php echo esc_html( $topbar_phone ); ?></a></li>
							<?php endif; ?>
							<?php if ( $topbar_email ) : ?>
								<li><a href="<?php echo esc_url( 'mailto:' . $topbar_email ); ?>"><?php echo esc_html( $topbar_email ); ?></a></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $topbar_social && function_exists( 'advisers_multipurpose_social_icons' ) ) : ?>
						<div class="footer-topbar-social">
							<?php advisers_multipurpose_social_icons( 'footer' ); ?>
						</div>
					<?php endif; ?>

				</div>
			</div><!-- .footer-topbar -->

		<?php endif; ?>

		<?php if ( $footer_rows ) : ?>

			<div class="footer-widgets-wrap">

				<?php foreach ( $footer_rows as $row_number => $row_sidebars ) : ?>

					<div class="footer-widgets footer-row footer-row-<?php echo esc_attr( $row_number ); ?> footer-columns-<?php echo esc_attr( count( $row_sidebars ) ); ?>">

						<?php foreach ( $row_sidebars as $sidebar_id ) : ?>

							<section
								class="footer-widget-area footer-widget-area-<?php echo esc_attr( str_replace( 'footer-', '', $sidebar_id ) ); ?>"
								aria-label="<?php echo esc_attr( sprintf( /* translators: %s: widget area number. */ __( 'Footer widgets %s', 'advisers-multipurpose' ), str_replace( 'footer-', '', $sidebar_id ) ) ); ?>"
							>
								<?php dynamic_sidebar( $sidebar_id ); ?>
							</section>

						<?php endforeach; ?>

					</div><!-- .footer-row -->

				<?php endforeach; ?>

			</div><!-- .footer-widgets-wrap -->

		<?php endif; ?>

		<?php
		if ( ! $topbar_social && function_exists( 'advisers_multipurpose_show_social_icons' ) && advisers_multipurpose_show_social_icons( 'footer' ) ) :
			?>
			<div class="footer-social">
				<?php
				if ( function_exists( 'advisers_multipurpose_social_icons' ) ) {
					advisers_multipurpose_social_icons( 'footer' );
				}
				?>
			</div><!-- .footer-social -->
		<?php endif; ?>

		<div class="footer-bottombar">
			<div class="site-info">

				<div class="copyright">
					<span class="copyright-text"><?php echo wp_kses( $copyright, $copyright_allowed_html ); ?></span>

					<?php if ( $show_bottom_credit ) : ?>
						<span class="theme-credit">
							<?php
							echo wp_kses(
								sprintf(
									/* translators: %s: theme author link. */
									__( 'Theme by %s', 'advisers-multipurpose' ),
									sprintf(
										'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
										esc_url( 'https://myadvisers.in' ),
										esc_html__( 'My Advisers', 'advisers-multipurpose' )
									)
								),
								array(
									'a' => array(
										'href'   => true,
										'target' => true,
										'rel'    => true,
									),
								)
							);
							?>
						</span>
					<?php endif; ?>
				</div><!-- .copyright -->

				<?php if ( $show_bottom_menu && has_nav_menu( 'footer' ) ) : ?>
					<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'advisers-multipurpose' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'menu_id'        => 'footer-menu',
								'menu_class'     => 'footer-menu',
								'container'      => false,
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav><!-- .footer-navigation -->
				<?php endif; ?>

			</div><!-- .site-info -->
		</div><!-- .footer-bottombar -->

	</footer><!-- #colophon -->

<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>
