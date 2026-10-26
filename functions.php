<?php
/**
 * Advisers Multipurpose — functions and definitions.
 *
 * @package Advisers_Multipurpose
 * @author  Biswajit Barman
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme constants.
 *
 * Keep the version in one place so stylesheet/script cache-busting remains
 * predictable across the entire theme.
 */
define( 'ADVISERS_MULTIPURPOSE_VERSION', '1.4.2' );
define( 'ADVISERS_MULTIPURPOSE_DIR', get_template_directory() );
define( 'ADVISERS_MULTIPURPOSE_URI', get_template_directory_uri() );

/**
 * Theme setup.
 *
 * @return void
 */
function advisers_multipurpose_setup() {

	/*
	 * Make translations available.
	 */
	load_theme_textdomain(
		'advisers-multipurpose',
	 ADVISERS_MULTIPURPOSE_DIR . '/languages'
	);

	/*
	 * Core WordPress theme features.
	 */
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	/*
	 * Default featured image size.
	 */
	set_post_thumbnail_size( 1200, 675, true );

	/*
	 * Additional responsive card image.
	 */
	add_image_size(
		'advisers-multipurpose-card',
		640,
		400,
		true
	);

	/*
	 * Native HTML5 markup.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	/*
	 * Custom logo.
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	/*
	 * Block editor support.
	 */
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	/*
	 * Load editor stylesheet only when available.
	 */
	$editor_style = ADVISERS_MULTIPURPOSE_DIR . '/assets/css/editor-style.css';

	if ( file_exists( $editor_style ) ) {
		add_editor_style( 'assets/css/editor-style.css' );
	}

	/*
	 * Custom background.
	 */
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ffffff',
		)
	);

	/*
	 * Navigation menus.
	 */
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'advisers-multipurpose' ),
			'footer'  => __( 'Footer Menu', 'advisers-multipurpose' ),
		)
	);

	/*
	 * Content width for embeds and oEmbed.
	 */
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1200;
	}
}
add_action( 'after_setup_theme', 'advisers_multipurpose_setup' );

/**
 * Register widget areas.
 *
 * @return void
 */
function advisers_multipurpose_widgets_init() {

	register_sidebar(
		array(
			'name'          => __( 'Primary Sidebar', 'advisers-multipurpose' ),
			'id'            => 'sidebar-primary',
			'description'   => __( 'Shown on posts and pages where the sidebar layout is enabled.', 'advisers-multipurpose' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	/*
	 * Twelve footer widget areas: 3 rows x 4 columns.
	 *
	 * IDs footer-1 to footer-4 are kept from earlier versions so existing
	 * widgets remain in place after updating.
	 */
	for ( $i = 1; $i <= 12; $i++ ) {

		$row    = (int) ceil( $i / 4 );
		$column = ( ( $i - 1 ) % 4 ) + 1;

		register_sidebar(
			array(
				'name'          => sprintf(
					/* translators: 1: footer row number, 2: footer column number. */
					__( 'Footer Row %1$d - Column %2$d', 'advisers-multipurpose' ),
					$row,
					$column
				),
				'id'            => 'footer-' . $i,
				'description'   => sprintf(
					/* translators: 1: footer row number, 2: footer column number. */
					__( 'Widget area for footer row %1$d, column %2$d.', 'advisers-multipurpose' ),
					$row,
					$column
				),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'advisers_multipurpose_widgets_init' );

/**
 * Return the version of a theme asset.
 *
 * File modification time is used when available so browsers automatically
 * receive a fresh asset after deployment.
 *
 * @param string $relative_path Asset path relative to the theme directory.
 * @return string
 */
function advisers_multipurpose_asset_version( $relative_path ) {

	$file = ADVISERS_MULTIPURPOSE_DIR . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $file ) ) {
		return (string) filemtime( $file );
	}

	return ADVISERS_MULTIPURPOSE_VERSION;
}

/**
 * Enqueue frontend styles and scripts.
 *
 * @return void
 */
function advisers_multipurpose_scripts() {

	$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

	$style_file = 'assets/css/main' . ( $script_debug ? '' : '.min' ) . '.css';
	$script_file = 'assets/js/main' . ( $script_debug ? '' : '.min' ) . '.js';

	/*
	 * Theme metadata stylesheet.
	 */
	wp_enqueue_style(
		'advisers-multipurpose-style',
		get_stylesheet_uri(),
		array(),
		advisers_multipurpose_asset_version( 'style.css' )
	);

	/*
	 * Main stylesheet.
	 *
	 * Only enqueue when the file exists to prevent unnecessary 404 requests
	 * during development or partial deployments.
	 */
	if ( file_exists( ADVISERS_MULTIPURPOSE_DIR . '/' . $style_file ) ) {

		wp_enqueue_style(
			'advisers-multipurpose-main',
			ADVISERS_MULTIPURPOSE_URI . '/' . $style_file,
			array( 'advisers-multipurpose-style' ),
			advisers_multipurpose_asset_version( $style_file )
		);
	}

	/*
	 * Main JavaScript.
	 *
	 * Defer execution so JavaScript does not unnecessarily block rendering.
	 */
	if ( file_exists( ADVISERS_MULTIPURPOSE_DIR . '/' . $script_file ) ) {

		wp_enqueue_script(
			'advisers-multipurpose-main',
			ADVISERS_MULTIPURPOSE_URI . '/' . $script_file,
			array(),
			advisers_multipurpose_asset_version( $script_file ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	/*
	 * Native threaded comments.
	 */
	if (
		is_singular()
		&& comments_open()
		&& get_option( 'thread_comments' )
	) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'advisers_multipurpose_scripts' );

/**
 * Set excerpt length.
 *
 * @param int $length Default WordPress excerpt length.
 * @return int
 */
function advisers_multipurpose_excerpt_length( $length ) {

	$value = get_theme_mod(
		'advisers_multipurpose_excerpt_length',
		30
	);

	$value = absint( $value );

	if ( $value < 1 ) {
		$value = 30;
	}

	return $value;
}
add_filter( 'excerpt_length', 'advisers_multipurpose_excerpt_length', 999 );

/**
 * Customize excerpt ending.
 *
 * @param string $more Default excerpt ending.
 * @return string
 */
function advisers_multipurpose_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'advisers_multipurpose_excerpt_more' );

/**
 * Load theme include modules.
 *
 * Each module is checked before loading to avoid fatal errors when a theme
 * package is temporarily incomplete during deployment.
 *
 * @return void
 */
function advisers_multipurpose_load_includes() {

	$includes = array(
		'inc/security.php',
		'inc/template-tags.php',
		'inc/customizer.php',
		'inc/seo-meta.php',
		'inc/performance.php',
		'inc/h1-guard.php',
		'inc/block-patterns.php',
	);

	foreach ( $includes as $include ) {

		$file = ADVISERS_MULTIPURPOSE_DIR . '/' . $include;

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

advisers_multipurpose_load_includes();

/**
 * Register block styles.
 *
 * @return void
 */
function advisers_multipurpose_register_block_styles() {

	register_block_style(
		'core/button',
		array(
			'name'  => 'am-soft',
			'label' => __( 'Soft', 'advisers-multipurpose' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'am-card',
			'label' => __( 'Card', 'advisers-multipurpose' ),
		)
	);
}
add_action( 'init', 'advisers_multipurpose_register_block_styles' );
