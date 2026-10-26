<?php
/**
 * H1 Guard (v1.3.4).
 *
 * Safety net: if the FINISHED page HTML of a singular page/post/CPT (or the
 * static front page) contains no <h1> - e.g. a plugin or page-builder template
 * bypasses the theme templates, or content/shortcodes printed none - the page
 * title is added as the H1. Pages that already have an H1 are never touched,
 * so it never creates a duplicate. The theme's own templates already print one
 * H1 (see advisers_multipurpose_singular_title()); this covers everything else.
 *
 * Customizer: Layout > Global Layout > "Always have one H1 per page".
 *
 * v1.3.5: also recognises the full-width wrappers of the My Business / Lead Agency
 * plugin templates, so their pages (Search Partners, Apply Form ...) get a VISIBLE title
 * even before those plugins are updated.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Should the guard run on this request?
 *
 * @return bool
 */
function advisers_multipurpose_h1_guard_applies() {

	if ( ! (bool) get_theme_mod( 'advisers_multipurpose_h1_guard', true ) ) {
		return false;
	}

	if ( is_admin() || is_feed() || is_embed() || is_robots() || is_trackback() || is_customize_preview() || is_preview() || wp_doing_ajax() ) {
		return false;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}

	// Page-builder editors / previews.
	if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['et_fb'] ) || isset( $_GET['fl_builder'] ) || isset( $_GET['brizy-edit'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return false;
	}

	if ( ! is_singular() && ! is_front_page() ) {
		return false;
	}

	return (bool) apply_filters( 'advisers_multipurpose_h1_guard', true );
}

/**
 * Start buffering the page so the final HTML can be checked.
 *
 * @return void
 */
function advisers_multipurpose_h1_guard_start() {
	if ( advisers_multipurpose_h1_guard_applies() ) {
		ob_start( 'advisers_multipurpose_h1_guard_filter' );
	}
}
add_action( 'template_redirect', 'advisers_multipurpose_h1_guard_start', 1 );

/**
 * Does the HTML contain a real <h1>? Scripts, styles, templates, SVG and
 * comments are ignored so an "<h1" inside JSON or a script does not count.
 *
 * @param string $html Page HTML.
 * @return bool
 */
function advisers_multipurpose_page_has_h1( $html ) {
	$clean = preg_replace( '#<(script|style|noscript|template|svg)\b[^>]*>.*?</\1>#is', '', (string) $html );
	$clean = preg_replace( '#<!--.*?-->#s', '', (string) $clean );
	return (bool) preg_match( '/<h1[\s>]/i', (string) $clean );
}

/**
 * Output-buffer callback: add an H1 when the page has none.
 *
 * @param string $html Full page HTML.
 * @return string
 */
function advisers_multipurpose_h1_guard_filter( $html ) {

	if ( ! is_string( $html ) || false === stripos( $html, '<body' ) || advisers_multipurpose_page_has_h1( $html ) ) {
		return $html;
	}

	$front = is_front_page();
	$mode  = $front ? 'hidden' : get_theme_mod( 'advisers_multipurpose_h1_guard_mode', 'visible' );
	$title = $front ? get_bloginfo( 'name' ) : get_the_title( get_queried_object_id() );
	$title = trim( wp_strip_all_tags( html_entity_decode( (string) $title, ENT_QUOTES, 'UTF-8' ) ) );

	if ( '' === $title ) {
		return $html;
	}

	$class = 'entry-title advisers-h1-guard' . ( 'hidden' === $mode ? ' screen-reader-text' : '' );
	$h1    = '<h1 class="' . esc_attr( $class ) . '">' . esc_html( $title ) . '</h1>';
	$safe  = str_replace( array( '\\', '$' ), array( '\\\\', '\\$' ), $h1 );

	// Preferred spots: article body, content wrapper, article, main. Last resort: right after <body>.
	$patterns = array(
		// Full-width page wrappers of the My Business / Lead Agency plugins (their template skips page.php).
		'#(<div\b[^>]*class="[^"]*\b(?:lam|mb)-fullwidth-page-wrap\b[^"]*"[^>]*>)#i',
		'#(<div\b[^>]*class="[^"]*\bentry-content\b[^"]*"[^>]*>)#i',
		'#(<article\b[^>]*>)#i',
		'#(<main\b[^>]*>)#i',
	);

	foreach ( $patterns as $re ) {
		$out = preg_replace( $re, '$1' . $safe, $html, 1, $count );
		if ( $count ) {
			return $out;
		}
	}

	$sr_h1 = str_replace( array( '\\', '$' ), array( '\\\\', '\\$' ), '<h1 class="entry-title advisers-h1-guard screen-reader-text">' . esc_html( $title ) . '</h1>' );
	$out   = preg_replace( '#(<body\b[^>]*>)#i', '$1' . $sr_h1, $html, 1, $count );

	return $count ? $out : $html;
}

/**
 * Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function advisers_multipurpose_h1_guard_customize( $wp_customize ) {

	$section = $wp_customize->get_section( 'advisers_multipurpose_global_layout' ) ? 'advisers_multipurpose_global_layout' : 'title_tagline';

	$wp_customize->add_setting(
		'advisers_multipurpose_h1_guard',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $v ) {
				return (bool) $v;
			},
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_h1_guard',
		array(
			'label'       => __( 'Always have one H1 per page', 'advisers-multipurpose' ),
			'description' => __( 'If a page or post ends up with no H1, add its title as the H1. Pages that already have an H1 are never changed.', 'advisers-multipurpose' ),
			'section'     => $section,
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_h1_guard_mode',
		array(
			'default'           => 'visible',
			'sanitize_callback' => static function ( $v ) {
				return 'hidden' === $v ? 'hidden' : 'visible';
			},
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_h1_guard_mode',
		array(
			'label'   => __( 'Added H1 appearance', 'advisers-multipurpose' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'visible' => __( 'Visible heading', 'advisers-multipurpose' ),
				'hidden'  => __( 'Hidden (screen-reader only)', 'advisers-multipurpose' ),
			),
		)
	);
}
add_action( 'customize_register', 'advisers_multipurpose_h1_guard_customize', 20 );
