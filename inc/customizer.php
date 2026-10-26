<?php
/**
 * Customizer — global theme controls and per-content layout options.
 *
 * Provides controls for:
 * - Global layout
 * - Header
 * - Footer
 * - Brand colors
 * - Typography
 * - Blog/archive layout
 * - Social icons
 * - Per-page/post layout overrides
 *
 * No external dependencies are required.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* ==========================================================================
 * 1. CUSTOMIZER HELPERS
 * ========================================================================== */

/**
 * Sanitize a select setting against the choices registered for its control.
 *
 * @param mixed                $input   Submitted value.
 * @param WP_Customize_Setting $setting Customizer setting.
 * @return string
 */
function advisers_multipurpose_sanitize_select( $input, $setting ) {

	$control = $setting->manager->get_control( $setting->id );

	if ( ! $control || empty( $control->choices ) ) {
		return $setting->default;
	}

	$input = is_string( $input ) ? $input : '';

	if ( array_key_exists( $input, $control->choices ) ) {
		return $input;
	}

	return $setting->default;
}


/**
 * Sanitize a positive integer within a specified range.
 *
 * @param mixed $value   Submitted value.
 * @param int   $minimum Minimum allowed value.
 * @param int   $maximum Maximum allowed value.
 * @return int
 */
function advisers_multipurpose_sanitize_range( $value, $minimum, $maximum ) {

	$value = absint( $value );

	if ( $value < $minimum ) {
		$value = $minimum;
	}

	if ( $value > $maximum ) {
		$value = $maximum;
	}

	return $value;
}


/**
 * Sanitize social URLs.
 *
 * Allows normal URLs and mailto: links.
 *
 * @param string $value Submitted value.
 * @return string
 */
function advisers_multipurpose_sanitize_social_url( $value ) {

	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	/*
	 * Email links.
	 */
	if ( 0 === stripos( $value, 'mailto:' ) ) {
		$email = sanitize_email( substr( $value, 7 ) );

		return $email ? 'mailto:' . $email : '';
	}

	/*
	 * Normal HTTP/HTTPS URLs.
	 */
	$url = esc_url_raw( $value );

	if ( '' === $url ) {
		return '';
	}

	$scheme = wp_parse_url( $url, PHP_URL_SCHEME );

	if ( ! in_array( strtolower( (string) $scheme ), array( 'http', 'https' ), true ) ) {
		return '';
	}

	return $url;
}


/**
 * Allowed HTML for footer text. Used for both saving and output.
 *
 * @return array
 */
function advisers_multipurpose_footer_allowed_html() {
	return array(
		'a'      => array(
			'href'   => true,
			'title'  => true,
			'target' => true,
			'rel'    => true,
			'class'  => true,
		),
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'span'   => array( 'class' => true ),
	);
}

/**
 * Sanitise footer text with the restricted tag list.
 *
 * @param string $value Submitted value.
 * @return string
 */
function advisers_multipurpose_sanitize_footer_text( $value ) {
	return wp_kses( (string) $value, advisers_multipurpose_footer_allowed_html() );
}


/**
 * Calculate a readable foreground color for a background.
 *
 * Uses WCAG relative luminance rather than a simple RGB brightness formula.
 *
 * @param string $hex_color Hex color.
 * @return string
 */
function advisers_multipurpose_contrast_color( $hex_color ) {

	$hex_color = sanitize_hex_color( $hex_color );

	if ( ! $hex_color ) {
		return '#ffffff';
	}

	$hex = ltrim( $hex_color, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0]
			. $hex[1] . $hex[1]
			. $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) ) {
		return '#ffffff';
	}

	$rgb = array(
		hexdec( substr( $hex, 0, 2 ) ) / 255,
		hexdec( substr( $hex, 2, 2 ) ) / 255,
		hexdec( substr( $hex, 4, 2 ) ) / 255,
	);

	$rgb = array_map(
		static function ( $value ) {
			return ( $value <= 0.03928 )
				? $value / 12.92
				: pow( ( $value + 0.055 ) / 1.055, 2.4 );
		},
		$rgb
	);

	$luminance =
		( 0.2126 * $rgb[0] ) +
		( 0.7152 * $rgb[1] ) +
		( 0.0722 * $rgb[2] );

	/*
	 * Compare both white and dark text contrast ratios.
	 */
	$white_contrast = ( 1.05 ) / ( $luminance + 0.05 );
	$dark_luminance = 0.013;
	$dark_contrast  = ( $luminance + 0.05 ) / ( $dark_luminance + 0.05 );

	return ( $white_contrast >= $dark_contrast )
		? '#ffffff'
		: '#111827';
}


/**
 * Return the configured font stacks.
 *
 * All fonts are local/system fonts. No external font request is generated.
 *
 * @param string $choice Font choice.
 * @return array
 */
function advisers_multipurpose_font_stacks( $choice ) {

	$stacks = array(

		'system' => array(
			'body'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
			'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
		),

		'modern' => array(
			'body'    => '-apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif',
			'heading' => '-apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif',
		),

		'classic' => array(
			'body'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
			'heading' => 'Georgia, "Times New Roman", Times, serif',
		),

		'rounded' => array(
			'body'    => 'ui-rounded, "SF Pro Rounded", "Segoe UI", Verdana, sans-serif',
			'heading' => 'ui-rounded, "SF Pro Rounded", "Segoe UI", Verdana, sans-serif',
		),
	);

	return isset( $stacks[ $choice ] )
		? $stacks[ $choice ]
		: $stacks['system'];
}


/* ==========================================================================
 * 2. CUSTOMIZER REGISTRATION
 * ========================================================================== */

/**
 * Register Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function advisers_multipurpose_customize_register( $wp_customize ) {

	/* ----------------------------------------------------------------------
	 * Panel: Theme Layout
	 * -------------------------------------------------------------------- */

	$wp_customize->add_panel(
		'advisers_multipurpose_layout_panel',
		array(
			'title'       => __( 'Advisers Multipurpose — Theme Options', 'advisers-multipurpose' ),
			'description' => __( 'Configure the layout, branding, typography, footer, blog and social settings for your website.', 'advisers-multipurpose' ),
			'priority'    => 160,
		)
	);


	/* ----------------------------------------------------------------------
	 * Section: Global Layout
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_global_layout',
		array(
			'title'       => __( 'Global Layout', 'advisers-multipurpose' ),
			'description' => __( 'Control the overall content width, sidebar behavior and breadcrumbs.', 'advisers-multipurpose' ),
			'panel'       => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_container_width',
		array(
			'default'           => 'contained',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_container_width',
		array(
			'label'       => __( 'Container Width', 'advisers-multipurpose' ),
			'description' => __( 'Controls the maximum horizontal area used by the main theme container.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_global_layout',
			'type'        => 'select',
			'choices'     => array(
				'contained' => __( 'Contained — 95%', 'advisers-multipurpose' ),
				'wide'      => __( 'Wide — 97%', 'advisers-multipurpose' ),
				'fluid'     => __( 'Fluid — 100%', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_sidebar_position',
		array(
			'default'           => 'right',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_sidebar_position',
		array(
			'label'       => __( 'Default Sidebar Position', 'advisers-multipurpose' ),
			'description' => __( 'This can be overridden for individual pages and posts.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_global_layout',
			'type'        => 'select',
			'choices'     => array(
				'right' => __( 'Right Sidebar', 'advisers-multipurpose' ),
				'left'  => __( 'Left Sidebar', 'advisers-multipurpose' ),
				'none'  => __( 'No Sidebar — Full Width', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_show_breadcrumbs',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_show_breadcrumbs',
		array(
			'label'       => __( 'Show Breadcrumbs', 'advisers-multipurpose' ),
			'description' => __( 'Display breadcrumb navigation on supported pages and posts.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_global_layout',
			'type'        => 'checkbox',
		)
	);


	/* ----------------------------------------------------------------------
	 * Section: Header
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_header_section',
		array(
			'title'       => __( 'Header Settings', 'advisers-multipurpose' ),
			'description' => __( 'Configure the header background, navigation and optional call-to-action button.', 'advisers-multipurpose' ),
			'panel'       => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_header_bg_color',
		array(
			'default'           => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'advisers_multipurpose_header_bg_color',
			array(
				'label'   => __( 'Header Background Color', 'advisers-multipurpose' ),
				'section' => 'advisers_multipurpose_header_section',
			)
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_header_cta_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_header_cta_text',
		array(
			'label'       => __( 'Header CTA Text', 'advisers-multipurpose' ),
			'description' => __( 'Leave empty to hide the header CTA button.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_header_section',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_header_cta_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_header_cta_url',
		array(
			'label'   => __( 'Header CTA URL', 'advisers-multipurpose' ),
			'section' => 'advisers_multipurpose_header_section',
			'type'    => 'url',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_nav_bg_color',
		array(
			'default'           => '#1E3A8A',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'advisers_multipurpose_nav_bg_color',
			array(
				'label'       => __( 'Navigation Background Color', 'advisers-multipurpose' ),
				'description' => __( 'Navigation text automatically switches between light and dark text for contrast.', 'advisers-multipurpose' ),
				'section'     => 'advisers_multipurpose_header_section',
			)
		)
	);


	/* ----------------------------------------------------------------------
	 * Section: Footer
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_footer_section',
		array(
			'title' => __( 'Footer Settings', 'advisers-multipurpose' ),
			'panel' => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_footer_columns',
		array(
			'default'           => 4,
			'sanitize_callback' => static function ( $value ) {
				$value = absint( $value );

				return in_array( $value, array( 0, 1, 2, 3, 4 ), true ) ? $value : 4;
			},
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_footer_columns',
		array(
			'label'       => __( 'Footer Widgets: Maximum Columns Per Row', 'advisers-multipurpose' ),
			'description' => __( 'The footer has 3 widget rows with up to 4 columns each (12 widget areas). Rows with no widgets are hidden. On tablets rows show 2 columns and on phones 1 column.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_footer_section',
			'type'        => 'select',
			'choices'     => array(
				0 => __( 'None', 'advisers-multipurpose' ),
				1 => __( '1 Column', 'advisers-multipurpose' ),
				2 => __( '2 Columns', 'advisers-multipurpose' ),
				3 => __( '3 Columns', 'advisers-multipurpose' ),
				4 => __( '4 Columns', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_footer_copyright',
		array(
			'default'           => sprintf(
				'© %1$s %2$s. All rights reserved.',
				wp_date( 'Y' ),
				get_bloginfo( 'name' )
			),
			'sanitize_callback' => 'advisers_multipurpose_sanitize_footer_text',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_footer_copyright',
		array(
			'label'       => __( 'Copyright Text', 'advisers-multipurpose' ),
			'description' => __( 'Basic HTML links and formatting are allowed.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_footer_section',
			'type'        => 'textarea',
		)
	);


	/* Footer top bar. */
	$footer_bar_settings = array(
		'advisers_multipurpose_footer_topbar_enable' => array( false, 'wp_validate_boolean' ),
		'advisers_multipurpose_footer_topbar_text'   => array( '', 'sanitize_text_field' ),
		'advisers_multipurpose_footer_topbar_link_text' => array( '', 'sanitize_text_field' ),
		'advisers_multipurpose_footer_topbar_link_url'  => array( '', 'esc_url_raw' ),
		'advisers_multipurpose_footer_topbar_phone'  => array( '', 'sanitize_text_field' ),
		'advisers_multipurpose_footer_topbar_email'  => array( '', 'sanitize_email' ),
		'advisers_multipurpose_footer_topbar_social' => array( true, 'wp_validate_boolean' ),
		'advisers_multipurpose_footer_bottom_menu'   => array( true, 'wp_validate_boolean' ),
		'advisers_multipurpose_footer_bottom_credit' => array( true, 'wp_validate_boolean' ),
	);

	foreach ( $footer_bar_settings as $setting_id => $config ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $config[0],
				'sanitize_callback' => $config[1],
				'transport'         => 'refresh',
			)
		);
	}

	$footer_bar_controls = array(
		'advisers_multipurpose_footer_topbar_enable'    => array( __( 'Show footer top bar', 'advisers-multipurpose' ), 'checkbox', '' ),
		'advisers_multipurpose_footer_topbar_text'      => array( __( 'Top bar message', 'advisers-multipurpose' ), 'text', '' ),
		'advisers_multipurpose_footer_topbar_link_text' => array( __( 'Top bar button text', 'advisers-multipurpose' ), 'text', '' ),
		'advisers_multipurpose_footer_topbar_link_url'  => array( __( 'Top bar button URL', 'advisers-multipurpose' ), 'url', '' ),
		'advisers_multipurpose_footer_topbar_phone'     => array( __( 'Top bar phone number', 'advisers-multipurpose' ), 'text', '' ),
		'advisers_multipurpose_footer_topbar_email'     => array( __( 'Top bar email address', 'advisers-multipurpose' ), 'email', '' ),
		'advisers_multipurpose_footer_topbar_social'    => array( __( 'Show social icons in top bar', 'advisers-multipurpose' ), 'checkbox', __( 'Uses the social links and position set in the Social section.', 'advisers-multipurpose' ) ),
		'advisers_multipurpose_footer_bottom_menu'      => array( __( 'Show footer menu in bottom bar', 'advisers-multipurpose' ), 'checkbox', '' ),
		'advisers_multipurpose_footer_bottom_credit'    => array( __( 'Show theme credit in bottom bar', 'advisers-multipurpose' ), 'checkbox', '' ),
	);

	foreach ( $footer_bar_controls as $setting_id => $config ) {
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => $config[0],
				'description' => $config[2],
				'section'     => 'advisers_multipurpose_footer_section',
				'type'        => $config[1],
			)
		);
	}


	/* ----------------------------------------------------------------------
	 * Section: Brand Colors
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_colors_section',
		array(
			'title' => __( 'Brand Colors', 'advisers-multipurpose' ),
			'panel' => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_color_primary',
		array(
			'default'           => '#0B5FFF',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'advisers_multipurpose_color_primary',
			array(
				'label'   => __( 'Primary Color', 'advisers-multipurpose' ),
				'section' => 'advisers_multipurpose_colors_section',
			)
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_color_secondary',
		array(
			'default'           => '#111827',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'advisers_multipurpose_color_secondary',
			array(
				'label'   => __( 'Secondary / Heading Color', 'advisers-multipurpose' ),
				'section' => 'advisers_multipurpose_colors_section',
			)
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_color_scheme',
		array(
			'default'           => 'light',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_color_scheme',
		array(
			'label'       => __( 'Color Scheme', 'advisers-multipurpose' ),
			'description' => __( 'Controls body content, cards, borders and other theme surfaces. Header and navigation colors remain controlled separately.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_colors_section',
			'type'        => 'select',
			'choices'     => array(
				'light' => __( 'Light', 'advisers-multipurpose' ),
				'auto'  => __( 'Auto — Follow Device', 'advisers-multipurpose' ),
				'dark'  => __( 'Dark', 'advisers-multipurpose' ),
			),
		)
	);


	/* ----------------------------------------------------------------------
	 * Section: Typography
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_typography_section',
		array(
			'title'       => __( 'Typography', 'advisers-multipurpose' ),
			'description' => __( 'Choose lightweight system-font stacks and responsive type sizes.', 'advisers-multipurpose' ),
			'panel'       => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_font_stack',
		array(
			'default'           => 'system',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_font_stack',
		array(
			'label'       => __( 'Font Style', 'advisers-multipurpose' ),
			'description' => __( 'All options use system fonts and do not require external font downloads.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_typography_section',
			'type'        => 'select',
			'choices'     => array(
				'system'  => __( 'System Default', 'advisers-multipurpose' ),
				'modern'  => __( 'Modern Sans', 'advisers-multipurpose' ),
				'classic' => __( 'Classic Serif Headings', 'advisers-multipurpose' ),
				'rounded' => __( 'Rounded / Friendly', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_base_font_size',
		array(
			'default'           => 'medium',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_base_font_size',
		array(
			'label'   => __( 'Base Text Size', 'advisers-multipurpose' ),
			'section' => 'advisers_multipurpose_typography_section',
			'type'    => 'select',
			'choices' => array(
				'small'  => __( 'Small — 16px', 'advisers-multipurpose' ),
				'medium' => __( 'Medium — 17px', 'advisers-multipurpose' ),
				'large'  => __( 'Large — 18px', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_heading_scale',
		array(
			'default'           => 'default',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_heading_scale',
		array(
			'label'   => __( 'Heading Size Scale', 'advisers-multipurpose' ),
			'section' => 'advisers_multipurpose_typography_section',
			'type'    => 'select',
			'choices' => array(
				'compact' => __( 'Compact', 'advisers-multipurpose' ),
				'default' => __( 'Default', 'advisers-multipurpose' ),
				'bold'    => __( 'Bold & Large', 'advisers-multipurpose' ),
			),
		)
	);


	/* ----------------------------------------------------------------------
	 * Section: Blog & Archive Layout
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_blog_section',
		array(
			'title'       => __( 'Blog & Archive Layout', 'advisers-multipurpose' ),
			'description' => __( 'Configure archive grid behavior and post excerpts.', 'advisers-multipurpose' ),
			'panel'       => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_blog_columns',
		array(
			'default'           => 'auto',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_blog_columns',
		array(
			'label'       => __( 'Post Grid Columns', 'advisers-multipurpose' ),
			'description' => __( 'Auto-fit adapts to available width. Fixed desktop columns collapse on smaller screens.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_blog_section',
			'type'        => 'select',
			'choices'     => array(
				'auto' => __( 'Auto-fit', 'advisers-multipurpose' ),
				'1'    => __( '1 Column', 'advisers-multipurpose' ),
				'2'    => __( '2 Columns', 'advisers-multipurpose' ),
				'3'    => __( '3 Columns', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_excerpt_length',
		array(
			'default'           => 30,
			'sanitize_callback' => static function ( $value ) {
				return advisers_multipurpose_sanitize_range( $value, 10, 60 );
			},
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_excerpt_length',
		array(
			'label'       => __( 'Excerpt Length', 'advisers-multipurpose' ),
			'description' => __( 'Number of words shown in archive excerpts.', 'advisers-multipurpose' ),
			'section'     => 'advisers_multipurpose_blog_section',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 10,
				'max'  => 60,
				'step' => 5,
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_read_more_text',
		array(
			'default'           => __( 'Read More', 'advisers-multipurpose' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_read_more_text',
		array(
			'label'   => __( 'Read More Link Text', 'advisers-multipurpose' ),
			'section' => 'advisers_multipurpose_blog_section',
			'type'    => 'text',
		)
	);


	/* ----------------------------------------------------------------------
	 * Section: Social Icons
	 * -------------------------------------------------------------------- */

	$wp_customize->add_section(
		'advisers_multipurpose_social_section',
		array(
			'title'       => __( 'Social Icons', 'advisers-multipurpose' ),
			'description' => __( 'Configure social links and choose where the icon group appears.', 'advisers-multipurpose' ),
			'panel'       => 'advisers_multipurpose_layout_panel',
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_social_position',
		array(
			'default'           => 'footer',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_social_position',
		array(
			'label'   => __( 'Show Social Icons In', 'advisers-multipurpose' ),
			'section' => 'advisers_multipurpose_social_section',
			'type'    => 'select',
			'choices' => array(
				'none'   => __( 'Hidden', 'advisers-multipurpose' ),
				'header' => __( 'Header', 'advisers-multipurpose' ),
				'footer' => __( 'Footer', 'advisers-multipurpose' ),
				'both'   => __( 'Header and Footer', 'advisers-multipurpose' ),
			),
		)
	);

	$wp_customize->add_setting(
		'advisers_multipurpose_social_icon_size',
		array(
			'default'           => 'medium',
			'sanitize_callback' => 'advisers_multipurpose_sanitize_select',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'advisers_multipurpose_social_icon_size',
		array(
			'label'   => __( 'Social Icon Size', 'advisers-multipurpose' ),
			'section' => 'advisers_multipurpose_social_section',
			'type'    => 'select',
			'choices' => array(
				'small'  => __( 'Small', 'advisers-multipurpose' ),
				'medium' => __( 'Medium', 'advisers-multipurpose' ),
				'large'  => __( 'Large', 'advisers-multipurpose' ),
			),
		)
	);

	$social_networks = array(
		'facebook'  => __( 'Facebook URL', 'advisers-multipurpose' ),
		'twitter'   => __( 'Twitter / X URL', 'advisers-multipurpose' ),
		'linkedin'  => __( 'LinkedIn URL', 'advisers-multipurpose' ),
		'instagram' => __( 'Instagram URL', 'advisers-multipurpose' ),
		'youtube'   => __( 'YouTube URL', 'advisers-multipurpose' ),
		'whatsapp'  => __( 'WhatsApp URL', 'advisers-multipurpose' ),
		'pinterest' => __( 'Pinterest URL', 'advisers-multipurpose' ),
		'tiktok'    => __( 'TikTok URL', 'advisers-multipurpose' ),
		'telegram'  => __( 'Telegram URL', 'advisers-multipurpose' ),
		'email'     => __( 'Email / Mailto URL', 'advisers-multipurpose' ),
	);

	foreach ( $social_networks as $network => $label ) {

		$wp_customize->add_setting(
			'advisers_multipurpose_social_' . $network,
			array(
				'default'           => '',
				'sanitize_callback' => 'advisers_multipurpose_sanitize_social_url',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'advisers_multipurpose_social_' . $network,
			array(
				'label'       => $label,
				'description' => ( 'email' === $network )
					? __( 'Example: mailto:you@example.com', 'advisers-multipurpose' )
					: __( 'Use the complete HTTPS URL.', 'advisers-multipurpose' ),
				'section'     => 'advisers_multipurpose_social_section',
				'type'        => 'url',
			)
		);
	}
}
add_action(
	'customize_register',
	'advisers_multipurpose_customize_register'
);


/* ==========================================================================
 * 3. CUSTOMIZER CSS VARIABLES
 * ========================================================================== */

/**
 * Output Customizer values as CSS custom properties.
 *
 * Printed after the main stylesheet so the Customizer values override
 * theme defaults without requiring a generated CSS file.
 *
 * @return void
 */
function advisers_multipurpose_customizer_css_vars() {

	$primary = sanitize_hex_color(
		get_theme_mod(
			'advisers_multipurpose_color_primary',
			'#0B5FFF'
		)
	);

	$secondary = sanitize_hex_color(
		get_theme_mod(
			'advisers_multipurpose_color_secondary',
			'#111827'
		)
	);

	$header_bg = sanitize_hex_color(
		get_theme_mod(
			'advisers_multipurpose_header_bg_color',
			'#ffffff'
		)
	);

	$nav_bg = sanitize_hex_color(
		get_theme_mod(
			'advisers_multipurpose_nav_bg_color',
			'#1E3A8A'
		)
	);

	$primary   = $primary ? $primary : '#0B5FFF';
	$secondary = $secondary ? $secondary : '#111827';
	$header_bg = $header_bg ? $header_bg : '#ffffff';
	$nav_bg    = $nav_bg ? $nav_bg : '#1E3A8A';


	/* Typography. */
	$font_choice = get_theme_mod(
		'advisers_multipurpose_font_stack',
		'system'
	);

	$fonts = advisers_multipurpose_font_stacks( $font_choice );


	$base_sizes = array(
		'small'  => '16px',
		'medium' => '17px',
		'large'  => '18px',
	);

	$base_size_choice = get_theme_mod(
		'advisers_multipurpose_base_font_size',
		'medium'
	);

	$base_size = isset( $base_sizes[ $base_size_choice ] )
		? $base_sizes[ $base_size_choice ]
		: '17px';


	$heading_scales = array(
		'compact' => '0.85',
		'default' => '1',
		'bold'    => '1.15',
	);

	$heading_scale_choice = get_theme_mod(
		'advisers_multipurpose_heading_scale',
		'default'
	);

	$heading_scale = isset( $heading_scales[ $heading_scale_choice ] )
		? $heading_scales[ $heading_scale_choice ]
		: '1';


	/* Color scheme. */
	$color_scheme = get_theme_mod(
		'advisers_multipurpose_color_scheme',
		'light'
	);

	if ( ! in_array( $color_scheme, array( 'light', 'auto', 'dark' ), true ) ) {
		$color_scheme = 'light';
	}

	?>
	<style id="advisers-multipurpose-customizer-vars">
		:root {
			--am-color-primary: <?php echo esc_html( $primary ); ?>;
			--am-color-secondary: <?php echo esc_html( $secondary ); ?>;
			--am-header-bg: <?php echo esc_html( $header_bg ); ?>;
			--am-header-text: <?php echo esc_html( advisers_multipurpose_contrast_color( $header_bg ) ); ?>;
			--am-nav-bg: <?php echo esc_html( $nav_bg ); ?>;
			--am-nav-text: <?php echo esc_html( advisers_multipurpose_contrast_color( $nav_bg ) ); ?>;
			--am-font-body: <?php echo esc_html( $fonts['body'] ); ?>;
			--am-font-heading: <?php echo esc_html( $fonts['heading'] ); ?>;
			--am-font-size-base: <?php echo esc_html( $base_size ); ?>;
			--am-heading-scale: <?php echo esc_html( $heading_scale ); ?>;
		}
	</style>
	<?php
}
add_action(
	'wp_head',
	'advisers_multipurpose_customizer_css_vars',
	20
);


/* ==========================================================================
 * 4. PER-PAGE / PER-POST LAYOUT META BOX
 * ========================================================================== */

/**
 * Add the layout meta box.
 *
 * @return void
 */
function advisers_multipurpose_add_layout_meta_box() {

	add_meta_box(
		'advisers_multipurpose_layout_options',
		__( 'Layout Options', 'advisers-multipurpose' ),
		'advisers_multipurpose_render_layout_meta_box',
		array(
			'page',
			'post',
		),
		'side',
		'default'
	);
}
add_action(
	'add_meta_boxes',
	'advisers_multipurpose_add_layout_meta_box'
);


/**
 * Render the layout meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function advisers_multipurpose_render_layout_meta_box( $post ) {

	wp_nonce_field(
		'advisers_multipurpose_save_layout',
		'advisers_multipurpose_layout_nonce'
	);

	$sidebar = get_post_meta(
		$post->ID,
		'_advisers_multipurpose_sidebar_position',
		true
	);

	$hide_header = get_post_meta(
		$post->ID,
		'_advisers_multipurpose_hide_header',
		true
	);

	$hide_footer = get_post_meta(
		$post->ID,
		'_advisers_multipurpose_hide_footer',
		true
	);

	$hide_title = get_post_meta(
		$post->ID,
		'_advisers_multipurpose_hide_title',
		true
	);

	?>
	<p>
		<label for="advisers_multipurpose_sidebar_position">
			<strong><?php esc_html_e( 'Sidebar', 'advisers-multipurpose' ); ?></strong>
		</label>
	</p>

	<select
		name="advisers_multipurpose_sidebar_position"
		id="advisers_multipurpose_sidebar_position"
		style="width:100%;"
	>
		<option value="" <?php selected( $sidebar, '' ); ?>>
			<?php esc_html_e( 'Use global default', 'advisers-multipurpose' ); ?>
		</option>

		<option value="right" <?php selected( $sidebar, 'right' ); ?>>
			<?php esc_html_e( 'Right Sidebar', 'advisers-multipurpose' ); ?>
		</option>

		<option value="left" <?php selected( $sidebar, 'left' ); ?>>
			<?php esc_html_e( 'Left Sidebar', 'advisers-multipurpose' ); ?>
		</option>

		<option value="none" <?php selected( $sidebar, 'none' ); ?>>
			<?php esc_html_e( 'No Sidebar', 'advisers-multipurpose' ); ?>
		</option>
	</select>

	<p>
		<label>
			<input
				type="checkbox"
				name="advisers_multipurpose_hide_header"
				value="1"
				<?php checked( $hide_header, '1' ); ?>
			>

			<?php esc_html_e( 'Hide site header', 'advisers-multipurpose' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input
				type="checkbox"
				name="advisers_multipurpose_hide_footer"
				value="1"
				<?php checked( $hide_footer, '1' ); ?>
			>

			<?php esc_html_e( 'Hide site footer', 'advisers-multipurpose' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input
				type="checkbox"
				name="advisers_multipurpose_hide_title"
				value="1"
				<?php checked( $hide_title, '1' ); ?>
			>

			<?php esc_html_e( 'Hide page title', 'advisers-multipurpose' ); ?>
		</label>
	</p>

	<p class="description">
		<?php esc_html_e( 'These settings affect only this page or post.', 'advisers-multipurpose' ); ?>
	</p>
	<?php
}


/**
 * Save per-page/post layout settings.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function advisers_multipurpose_save_layout_meta( $post_id ) {

	/*
	 * Nonce verification.
	 */
	if (
		! isset( $_POST['advisers_multipurpose_layout_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field(
				wp_unslash( $_POST['advisers_multipurpose_layout_nonce'] )
			),
			'advisers_multipurpose_save_layout'
		)
	) {
		return;
	}


	/*
	 * Autosave/revision protection.
	 */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}


	/*
	 * Capability check.
	 */
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}


	/*
	 * Sidebar setting.
	 */
	$sidebar = '';

	if ( isset( $_POST['advisers_multipurpose_sidebar_position'] ) ) {
		$sidebar = sanitize_key(
			wp_unslash(
				$_POST['advisers_multipurpose_sidebar_position']
			)
		);
	}

	if ( in_array( $sidebar, array( 'right', 'left', 'none' ), true ) ) {

		update_post_meta(
			$post_id,
			'_advisers_multipurpose_sidebar_position',
			$sidebar
		);

	} else {

		delete_post_meta(
			$post_id,
			'_advisers_multipurpose_sidebar_position'
		);
	}


	/*
	 * Header visibility.
	 */
	$hide_header = isset( $_POST['advisers_multipurpose_hide_header'] )
		? '1'
		: '';

	if ( $hide_header ) {

		update_post_meta(
			$post_id,
			'_advisers_multipurpose_hide_header',
			'1'
		);

	} else {

		delete_post_meta(
			$post_id,
			'_advisers_multipurpose_hide_header'
		);
	}


	/*
	 * Footer visibility.
	 */
	$hide_footer = isset( $_POST['advisers_multipurpose_hide_footer'] )
		? '1'
		: '';

	if ( $hide_footer ) {

		update_post_meta(
			$post_id,
			'_advisers_multipurpose_hide_footer',
			'1'
		);

	} else {

		delete_post_meta(
			$post_id,
			'_advisers_multipurpose_hide_footer'
		);
	}


	/*
	 * Page title visibility.
	 */
	$hide_title = isset( $_POST['advisers_multipurpose_hide_title'] )
		? '1'
		: '';

	if ( $hide_title ) {

		update_post_meta(
			$post_id,
			'_advisers_multipurpose_hide_title',
			'1'
		);

	} else {

		delete_post_meta(
			$post_id,
			'_advisers_multipurpose_hide_title'
		);
	}
}
add_action(
	'save_post',
	'advisers_multipurpose_save_layout_meta'
);


/**
 * Determine whether the page title should be hidden for the current
 * singular request, based on the "Hide page title" per-page/post
 * Layout Options checkbox.
 *
 * @return bool
 */
function advisers_multipurpose_is_page_title_hidden() {

	if ( ! is_singular() ) {
		return false;
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return false;
	}

	return '1' === get_post_meta(
		$post_id,
		'_advisers_multipurpose_hide_title',
		true
	);
}