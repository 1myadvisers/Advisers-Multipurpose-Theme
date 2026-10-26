<?php
/**
 * Performance optimisations for Advisers Multipurpose.
 *
 * Keeps the theme lightweight while relying on WordPress core's
 * native performance features wherever possible.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * -------------------------------------------------------------------------
 * Disable WordPress emoji assets.
 * -------------------------------------------------------------------------
 *
 * The theme does not require WordPress's legacy emoji detection script or
 * emoji stylesheet. Removing them reduces unnecessary requests and inline
 * output.
 */
function advisers_multipurpose_disable_emojis() {

	remove_action(
		'wp_head',
		'print_emoji_detection_script',
		7
	);

	remove_action(
		'admin_print_scripts',
		'print_emoji_detection_script'
	);

	remove_action(
		'wp_print_styles',
		'print_emoji_styles'
	);

	remove_action(
		'admin_print_styles',
		'print_emoji_styles'
	);

	remove_filter(
		'the_content_feed',
		'wp_staticize_emoji'
	);

	remove_filter(
		'comment_text_rss',
		'wp_staticize_emoji'
	);

	remove_filter(
		'wp_mail',
		'wp_staticize_emoji_for_email'
	);
}
add_action(
	'init',
	'advisers_multipurpose_disable_emojis'
);


/**
 * -------------------------------------------------------------------------
 * Image attributes.
 * -------------------------------------------------------------------------
 *
 * WordPress already provides native lazy loading where appropriate.
 *
 * We only add async decoding when an image does not already specify a
 * decoding strategy. Existing attributes are respected.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function advisers_multipurpose_image_attributes( $attr ) {

	if ( ! is_array( $attr ) ) {
		return $attr;
	}

	/*
	 * Do not override an existing loading attribute.
	 *
	 * WordPress core and plugins may deliberately specify eager/lazy
	 * loading depending on the image's position and context.
	 */
	if ( ! isset( $attr['loading'] ) || '' === $attr['loading'] ) {
		$attr['loading'] = 'lazy';
	}

	/*
	 * Async decoding is appropriate for normal content images and can
	 * reduce rendering delays.
	 */
	if ( ! isset( $attr['decoding'] ) || '' === $attr['decoding'] ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter(
	'wp_get_attachment_image_attributes',
	'advisers_multipurpose_image_attributes',
	10,
	1
);


/**
 * -------------------------------------------------------------------------
 * Prioritise the actual featured image on singular pages.
 * -------------------------------------------------------------------------
 *
 * The previous implementation marked the first attachment image encountered
 * during the main query as the LCP image. That is not sufficiently precise.
 *
 * Instead, only the queried post/page's actual featured image receives:
 *
 * - loading="eager"
 * - fetchpriority="high"
 *
 * This prevents unrelated images, logos, widgets or other attachment images
 * from accidentally receiving high loading priority.
 *
 * @param array        $attr       Image attributes.
 * @param WP_Post|null $attachment Attachment object.
 * @param string|int[] $size       Requested image size.
 * @return array
 */
function advisers_multipurpose_lcp_image_attributes(
	$attr,
	$attachment,
	$size
) {

	if ( ! is_array( $attr ) ) {
		return $attr;
	}

	if ( ! is_singular() || ! is_main_query() ) {
		return $attr;
	}

	if ( ! $attachment instanceof WP_Post ) {
		return $attr;
	}

	if ( 'attachment' === $attachment->post_type ) {
		/*
		 * Continue below. Attachment objects can still be used to identify
		 * the image, so do not return solely based on post type.
		 */
	}

	$queried_object_id = get_queried_object_id();

	if ( ! $queried_object_id ) {
		return $attr;
	}

	$featured_image_id = get_post_thumbnail_id(
		$queried_object_id
	);

	if ( ! $featured_image_id ) {
		return $attr;
	}

	if ( (int) $attachment->ID !== (int) $featured_image_id ) {
		return $attr;
	}

	/*
	 * The featured image is the candidate we want to prioritise.
	 */
	$attr['loading']       = 'eager';
	$attr['fetchpriority'] = 'high';

	/*
	 * Leave decoding unset so WordPress/browser can make the appropriate
	 * decision for this high-priority image.
	 */
	unset( $attr['decoding'] );

	return $attr;
}
add_filter(
	'wp_get_attachment_image_attributes',
	'advisers_multipurpose_lcp_image_attributes',
	20,
	3
);


/**
 * -------------------------------------------------------------------------
 * Ensure featured images have intrinsic dimensions.
 * -------------------------------------------------------------------------
 *
 * WordPress normally outputs width and height attributes for attachment
 * images. This helper provides a safe fallback for theme-generated
 * thumbnail HTML if those attributes are missing.
 *
 * We deliberately do NOT inject an inline aspect-ratio style. Inline CSS
 * can conflict with responsive image rules and is unnecessary when proper
 * intrinsic dimensions are available.
 *
 * @param string       $html              Thumbnail HTML.
 * @param int          $post_id           Post ID.
 * @param int          $post_thumbnail_id Thumbnail attachment ID.
 * @param string|array $size              Image size.
 * @param array        $attr              Image attributes.
 * @return string
 */
function advisers_multipurpose_thumbnail_html(
	$html,
	$post_id,
	$post_thumbnail_id,
	$size,
	$attr
) {

	if ( '' === $html || ! $post_thumbnail_id ) {
		return $html;
	}

	/*
	 * If WordPress already supplied width and height, leave the generated
	 * markup untouched.
	 */
	if (
		false !== strpos( $html, ' width="' ) &&
		false !== strpos( $html, ' height="' )
	) {
		return $html;
	}

	$metadata = wp_get_attachment_metadata(
		$post_thumbnail_id
	);

	if (
		! is_array( $metadata ) ||
		empty( $metadata['width'] ) ||
		empty( $metadata['height'] )
	) {
		return $html;
	}

	$width  = absint( $metadata['width'] );
	$height = absint( $metadata['height'] );

	if ( ! $width || ! $height ) {
		return $html;
	}

	/*
	 * Only add missing intrinsic dimensions.
	 */
	$replacement = '<img';

	$attributes = sprintf(
		' width="%d" height="%d"',
		$width,
		$height
	);

	return preg_replace(
		'/<img\b/i',
		$replacement . $attributes,
		$html,
		1
	);
}
add_filter(
	'post_thumbnail_html',
	'advisers_multipurpose_thumbnail_html',
	10,
	5
);


/**
 * -------------------------------------------------------------------------
 * Prevent duplicate attachment image attributes.
 * -------------------------------------------------------------------------
 *
 * No additional image manipulation is required here. This function exists
 * only as a clearly documented extension point for the theme and should not
 * modify WordPress-generated responsive image attributes.
 */


/**
 * -------------------------------------------------------------------------
 * Remove unnecessary WordPress resource hints where appropriate.
 * -------------------------------------------------------------------------
 *
 * Intentionally left untouched.
 *
 * WordPress core manages preload, preconnect, DNS-prefetch and responsive
 * image hints. Removing these globally can make performance worse rather
 * than better.
 */


/**
 * -------------------------------------------------------------------------
 * Performance compatibility.
 * -------------------------------------------------------------------------
 *
 * Do not disable:
 *
 * - srcset
 * - sizes
 * - responsive images
 * - native lazy loading
 * - fetchpriority
 * - WordPress image optimization
 * - block styles globally
 *
 * These are handled by WordPress core and should remain available to the
 * theme and plugins.
 */


/**
 * -------------------------------------------------------------------------
 * Lean front end (1.4.0).
 * -------------------------------------------------------------------------
 */

/**
 * Load core block styles only for blocks used on the page.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Remove assets the theme never uses on the front end.
 *
 * @return void
 */
function advisers_multipurpose_trim_frontend_assets() {

	if ( is_admin() ) {
		return;
	}

	// The oEmbed "wp-embed" script is only needed to be embedded on other sites.
	if ( apply_filters( 'advisers_multipurpose_dequeue_wp_embed', true ) ) {
		wp_dequeue_script( 'wp-embed' );
	}
}
add_action( 'wp_footer', 'advisers_multipurpose_trim_frontend_assets', 1 );

/**
 * Drop jQuery Migrate on the front end (saves a request and parse time).
 *
 * @param WP_Scripts $scripts Registered scripts.
 * @return void
 */
function advisers_multipurpose_remove_jquery_migrate( $scripts ) {

	if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
		return;
	}

	if ( ! apply_filters( 'advisers_multipurpose_remove_jquery_migrate', true ) ) {
		return;
	}

	$scripts->registered['jquery']->deps = array_diff(
		$scripts->registered['jquery']->deps,
		array( 'jquery-migrate' )
	);
}
add_action( 'wp_default_scripts', 'advisers_multipurpose_remove_jquery_migrate' );

/**
 * Tell the browser the theme colour (mobile address bar).
 *
 * @return void
 */
function advisers_multipurpose_theme_color_meta() {

	$color = sanitize_hex_color( get_theme_mod( 'advisers_multipurpose_nav_bg_color', '#1E3A8A' ) );

	if ( $color ) {
		printf( "<meta name=\"theme-color\" content=\"%s\">\n", esc_attr( $color ) );
	}
}
add_action( 'wp_head', 'advisers_multipurpose_theme_color_meta', 2 );

