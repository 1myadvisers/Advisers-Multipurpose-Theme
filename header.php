<?php
/**
 * Header template.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether the header is hidden for the current singular object.
 *
 * Use the queried object ID rather than get_the_ID() so this remains reliable
 * outside the main Loop.
 */
$hide_header = '';

if ( is_singular() ) {
	$hide_header = get_post_meta(
		get_queried_object_id(),
		'_advisers_multipurpose_hide_header',
		true
	);
}

$header_hidden = ( '1' === (string) $hide_header );

/**
 * Site branding.
 */
$site_name        = get_bloginfo( 'name', 'display' );
$site_description = get_bloginfo( 'description', 'display' );
$home_url         = home_url( '/' );

/**
 * Header CTA.
 *
 * Only render the CTA when both values are present and the URL is valid.
 */
$header_cta_text = get_theme_mod(
	'advisers_multipurpose_header_cta_text',
	''
);

$header_cta_url = get_theme_mod(
	'advisers_multipurpose_header_cta_url',
	''
);

$header_cta_url = $header_cta_url
	? esc_url( $header_cta_url, array( 'http', 'https', 'mailto', 'tel' ) )
	: '';

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php
/**
 * Required WordPress hook immediately after the opening body tag.
 */
wp_body_open();
?>

<a
	class="skip-link screen-reader-text"
	href="#primary"
>
	<?php esc_html_e( 'Skip to content', 'advisers-multipurpose' ); ?>
</a>

<?php if ( ! $header_hidden ) : ?>

	<header id="masthead" class="site-header">

		<div class="header-top">

			<div class="container header-top-inner">

				<div class="header-branding-group">

					<div class="header-branding-logo">

						<?php if ( has_custom_logo() ) : ?>

							<?php the_custom_logo(); ?>

						<?php elseif ( $site_name ) : ?>

							<a
								class="site-logo-fallback"
								href="<?php echo esc_url( $home_url ); ?>"
								rel="home"
							>
								<?php echo esc_html( $site_name ); ?>
							</a>

						<?php endif; ?>

					</div><!-- .header-branding-logo -->


					<div class="header-branding-center">

						<?php if ( $site_name ) : ?>

							<?php if ( is_front_page() && is_home() ) : ?>

								<h1 class="site-title">
									<a
										href="<?php echo esc_url( $home_url ); ?>"
										rel="home"
									>
										<?php echo esc_html( $site_name ); ?>
									</a>
								</h1>

							<?php else : ?>

								<p class="site-title">
									<a
										href="<?php echo esc_url( $home_url ); ?>"
										rel="home"
									>
										<?php echo esc_html( $site_name ); ?>
									</a>
								</p>

							<?php endif; ?>

						<?php endif; ?>


						<?php if ( $site_description ) : ?>

							<p class="site-description">
								<?php echo esc_html( $site_description ); ?>
							</p>

						<?php endif; ?>

					</div><!-- .header-branding-center -->

				</div><!-- .header-branding-group -->


				<div class="header-right-group">

					<?php if ( $header_cta_text && $header_cta_url ) : ?>

						<div class="header-branding-cta">

							<a
								class="header-cta-button"
								href="<?php echo esc_url( $header_cta_url ); ?>"
							>
								<?php echo esc_html( $header_cta_text ); ?>
							</a>

						</div><!-- .header-branding-cta -->

					<?php endif; ?>


					<?php if ( function_exists( 'advisers_multipurpose_show_social_icons' ) && advisers_multipurpose_show_social_icons( 'header' ) ) : ?>

						<?php
						if ( function_exists( 'advisers_multipurpose_social_icons' ) ) {
							advisers_multipurpose_social_icons( 'header' );
						}
						?>

					<?php endif; ?>

				</div><!-- .header-right-group -->

			</div><!-- .header-top-inner -->

		</div><!-- .header-top -->


		<nav
			id="site-navigation"
			class="main-navigation"
			aria-label="<?php esc_attr_e( 'Primary Menu', 'advisers-multipurpose' ); ?>"
		>

			<div class="container nav-inner">

				<?php if ( has_nav_menu( 'primary' ) ) : ?>

					<button
						class="menu-toggle"
						type="button"
						aria-controls="primary-menu"
						aria-expanded="false"
					>
						<span
							class="menu-toggle-bar"
							aria-hidden="true"
						></span>

						<span
							class="menu-toggle-bar"
							aria-hidden="true"
						></span>

						<span
							class="menu-toggle-bar"
							aria-hidden="true"
						></span>

						<span class="screen-reader-text">
							<?php esc_html_e( 'Open primary menu', 'advisers-multipurpose' ); ?>
						</span>
					</button>

					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'menu',
							'container'      => false,
							'fallback_cb'    => false,
							'depth'          => 0,
						)
					);
					?>

				<?php endif; ?>

			</div><!-- .nav-inner -->

		</nav><!-- #site-navigation -->

	</header><!-- #masthead -->

<?php endif; ?>


<?php
/**
 * Breadcrumb navigation.
 *
 * The helper itself handles Rank Math integration and fallback breadcrumbs.
 * Keep the Customizer setting here as the top-level visibility switch.
 */
if (
	get_theme_mod(
		'advisers_multipurpose_show_breadcrumbs',
		true
	)
	&& ! is_front_page()
) :
	?>

	<div class="breadcrumbs-wrap container">

		<?php
		if ( function_exists( 'advisers_multipurpose_breadcrumbs' ) ) {
			advisers_multipurpose_breadcrumbs();
		}
		?>

	</div><!-- .breadcrumbs-wrap -->

<?php endif; ?>


<div id="content" class="site-content">

	<div class="container layout-wrap">