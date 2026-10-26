<?php
/**
 * Template helper functions.
 *
 * Provides shared presentation helpers for Advisers Multipurpose:
 *
 * - Entry/page titles.
 * - Body/layout classes.
 * - Sidebar resolution.
 * - Breadcrumbs.
 * - Post metadata.
 * - Social icon rendering.
 *
 * SEO metadata, schema, Open Graph and Twitter/X cards are intentionally
 * handled by dedicated SEO functionality rather than this theme.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* ==========================================================================
 * 1. ENTRY TITLES
 * ========================================================================== */

/**
 * Determine whether the theme should automatically output an entry title.
 *
 * Pages are intentionally excluded because the theme supports block-built
 * pages where the H1 is authored inside the page content.
 *
 * Posts, archives, search and 404 templates receive a theme-generated H1.
 *
 * @return bool
 */
function advisers_multipurpose_should_output_title() {

	if ( is_page() ) {
		return false;
	}

	return true;
}


/**
 * Does rendered content already contain an H1?
 *
 * Checked on the FINAL rendered HTML (after blocks, patterns and shortcodes
 * run), so a Hero pattern, a Heading block set to H1, a shortcode or a page
 * builder that prints its own H1 are all detected.
 *
 * @param string $html Rendered post content.
 * @return bool
 */
function advisers_multipurpose_html_has_h1( $html ) {
	return (bool) preg_match( '/<h1[\s>]/i', (string) $html );
}

/**
 * Render the_content() into a string so the template can decide about the
 * title before printing anything.
 *
 * @return string
 */
function advisers_multipurpose_get_the_content_html() {
	ob_start();
	the_content();
	return (string) ob_get_clean();
}

/**
 * Print the title of a singular page/post so the document ALWAYS has exactly
 * one H1 (v1.3.3):
 *
 *  - Content has no H1  -> the title is the H1 (visible; or screen-reader-only
 *    when "Hide page title" is ticked, so the page is never left without one).
 *  - Content has its own H1 (e.g. Hero pattern) -> the title is shown as an H2
 *    (never a second H1), or omitted when "Hide page title" is ticked.
 *
 * @param string $content_html Rendered content (see advisers_multipurpose_get_the_content_html()).
 * @return void
 */
function advisers_multipurpose_singular_title( $content_html ) {

	$title = get_the_title();

	if ( '' === trim( wp_strip_all_tags( $title ) ) ) {
		return;
	}

	$hidden  = function_exists( 'advisers_multipurpose_is_page_title_hidden' ) && advisers_multipurpose_is_page_title_hidden();
	$has_h1  = advisers_multipurpose_html_has_h1( $content_html );

	if ( $has_h1 ) {
		if ( ! $hidden ) {
			printf( '<h2 class="entry-title">%s</h2>', wp_kses_post( $title ) );
		}
		return;
	}

	printf(
		'<h1 class="entry-title%2$s">%1$s</h1>',
		wp_kses_post( $title ),
		$hidden ? ' screen-reader-text' : ''
	);
}


/**
 * Output the contextual entry/page title.
 *
 * The default heading is H1. The tag is strictly validated through
 * tag_escape() before output.
 *
 * @param string $tag Heading tag. Default 'h1'.
 * @return void
 */
function advisers_multipurpose_entry_title( $tag = 'h1' ) {

	if ( ! advisers_multipurpose_should_output_title() ) {
		return;
	}

	$tag = tag_escape( $tag );

	if ( ! $tag ) {
		$tag = 'h1';
	}

	/*
	 * Singular posts/custom post types.
	 */
	if ( is_singular() ) {
		$title = get_the_title();

		if ( '' === trim( $title ) ) {
			return;
		}

		printf(
			'<%1$s class="entry-title">%2$s</%1$s>',
			esc_html( $tag ),
			esc_html( $title )
		);

		return;
	}

	/*
	 * Posts page.
	 */
	if ( is_home() && ! is_front_page() ) {

		$title = single_post_title( '', false );

		if ( '' === trim( $title ) ) {
			$title = get_the_archive_title();
		}

		printf(
			'<%1$s class="page-title">%2$s</%1$s>',
			esc_html( $tag ),
			wp_kses_post( $title )
		);

		return;
	}

	/*
	 * Archives.
	 */
	if ( is_archive() ) {

		printf(
			'<%1$s class="page-title">%2$s</%1$s>',
			esc_html( $tag ),
			wp_kses_post( get_the_archive_title() )
		);

		return;
	}

	/*
	 * Search results.
	 */
	if ( is_search() ) {

		$query = get_search_query();

		printf(
			'<%1$s class="page-title">%2$s</%1$s>',
			esc_html( $tag ),
			sprintf(
				/* translators: %s: search query. */
				esc_html__(
					'Search Results for: %s',
					'advisers-multipurpose'
				),
				'<span>' . esc_html( $query ) . '</span>'
			)
		);

		return;
	}

	/*
	 * 404.
	 */
	if ( is_404() ) {

		printf(
			'<%1$s class="page-title">%2$s</%1$s>',
			esc_html( $tag ),
			esc_html__(
				'Nothing Found',
				'advisers-multipurpose'
			)
		);
	}
}


/* ==========================================================================
 * 2. BODY / LAYOUT CLASSES
 * ========================================================================== */

/**
 * Add theme-specific classes to <body>.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function advisers_multipurpose_body_layout_classes( $classes ) {

	$sidebar_position = advisers_multipurpose_get_sidebar_position();

	$classes[] = 'layout-' . sanitize_html_class( $sidebar_position );

	/*
	 * The landing template always operates without a sidebar.
	 */
	if ( is_page_template( 'page-templates/template-landing.php' ) ) {
		$classes[] = 'no-sidebar';
	}

	/*
	 * Container width.
	 */
	$container = get_theme_mod(
		'advisers_multipurpose_container_width',
		'contained'
	);

	$allowed_container_values = array(
		'contained',
		'wide',
		'fluid',
	);

	if ( ! in_array( $container, $allowed_container_values, true ) ) {
		$container = 'contained';
	}

	$classes[] = 'container-' . sanitize_html_class( $container );

	/*
	 * Color scheme.
	 */
	$color_scheme = get_theme_mod(
		'advisers_multipurpose_color_scheme',
		'light'
	);

	$allowed_color_schemes = array(
		'light',
		'auto',
		'dark',
	);

	if ( ! in_array( $color_scheme, $allowed_color_schemes, true ) ) {
		$color_scheme = 'light';
	}

	$classes[] = 'color-scheme-' . sanitize_html_class( $color_scheme );

	/*
	 * Blog/archive columns.
	 */
	$blog_columns = get_theme_mod(
		'advisers_multipurpose_blog_columns',
		'auto'
	);

	$allowed_blog_columns = array(
		'auto',
		'1',
		'2',
		'3',
	);

	if ( ! in_array( $blog_columns, $allowed_blog_columns, true ) ) {
		$blog_columns = 'auto';
	}

	$classes[] = 'blog-columns-' . sanitize_html_class( $blog_columns );

	return $classes;
}
add_filter(
	'body_class',
	'advisers_multipurpose_body_layout_classes'
);


/* ==========================================================================
 * 3. SIDEBAR POSITION
 * ========================================================================== */

/**
 * Resolve the sidebar position for the current request.
 *
 * Priority:
 *
 * 1. Full-width/landing template.
 * 2. Per-post/page metadata.
 * 3. Customizer global setting.
 * 4. Right sidebar fallback.
 *
 * @return string 'right', 'left' or 'none'.
 */
function advisers_multipurpose_get_sidebar_position() {

	/*
	 * The landing template always has no sidebar.
	 */
	if ( is_page_template( 'page-templates/template-landing.php' ) ) {
		return 'none';
	}

	/*
	 * Per-content override.
	 */
	$meta_override = '';

	if ( is_singular() ) {
		$post_id = get_queried_object_id();

		if ( $post_id ) {
			$meta_override = get_post_meta(
				$post_id,
				'_advisers_multipurpose_sidebar_position',
				true
			);
		}
	}

	$allowed_positions = array(
		'left',
		'right',
		'none',
	);

	if ( in_array( $meta_override, $allowed_positions, true ) ) {
		return $meta_override;
	}

	/*
	 * Global Customizer setting.
	 */
	$global_position = get_theme_mod(
		'advisers_multipurpose_sidebar_position',
		'right'
	);

	if ( ! in_array( $global_position, $allowed_positions, true ) ) {
		$global_position = 'right';
	}

	return $global_position;
}


/**
 * Determine whether the sidebar should be rendered.
 *
 * @return bool
 */
function advisers_multipurpose_show_sidebar() {

	if ( 'none' === advisers_multipurpose_get_sidebar_position() ) {
		return false;
	}

	return is_active_sidebar( 'sidebar-primary' );
}


/* ==========================================================================
 * 4. BREADCRUMBS
 * ========================================================================== */

/**
 * Render breadcrumbs.
 *
 * Rank Math breadcrumbs are preferred when available.
 * Otherwise a lightweight accessible fallback is generated.
 *
 * The function respects the theme's Show Breadcrumbs Customizer setting.
 *
 * @return void
 */
function advisers_multipurpose_breadcrumbs() {

	/*
	 * Respect Customizer setting.
	 */
	if (
		! get_theme_mod(
			'advisers_multipurpose_show_breadcrumbs',
			true
		)
	) {
		return;
	}

	/*
	 * Never display breadcrumbs on the front page.
	 */
	if ( is_front_page() ) {
		return;
	}

	/*
	 * Prefer Rank Math's native breadcrumb implementation.
	 */
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
		return;
	}

	$items = array();

	/*
	 * Home.
	 */
	$items[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( home_url( '/' ) ),
		esc_html__(
			'Home',
			'advisers-multipurpose'
		)
	);

	/*
	 * Singular content.
	 */
	if ( is_singular() ) {

		$post_id = get_queried_object_id();

		/*
		 * Pages can have parent hierarchy.
		 */
		if ( is_page() && $post_id ) {

			$ancestors = get_post_ancestors( $post_id );

			if ( ! empty( $ancestors ) ) {

				$ancestors = array_reverse( $ancestors );

				foreach ( $ancestors as $ancestor_id ) {

					$ancestor_title = get_the_title( $ancestor_id );

					if ( '' === trim( $ancestor_title ) ) {
						continue;
					}

					$items[] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url(
							get_permalink( $ancestor_id )
						),
						esc_html( $ancestor_title )
					);
				}
			}
		}

		/*
		 * Posts/custom post types.
		 *
		 * Add the primary category for standard posts.
		 */
		if ( is_single() && ! is_page() ) {

			$categories = get_the_category( $post_id );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {

				$category = reset( $categories );

				if ( $category instanceof WP_Term ) {

					$category_link = get_category_link(
						$category->term_id
					);

					if ( ! is_wp_error( $category_link ) ) {
						$items[] = sprintf(
							'<a href="%1$s">%2$s</a>',
							esc_url( $category_link ),
							esc_html( $category->name )
						);
					}
				}
			}
		}

		$title = get_the_title( $post_id );

		if ( '' !== trim( $title ) ) {
			$items[] = '<span aria-current="page">' .
				esc_html( $title ) .
				'</span>';
		}
	}

	/*
	 * Archives.
	 */
	elseif ( is_archive() ) {

		$archive_title = get_the_archive_title();

		/*
		 * WordPress archive titles can contain markup such as "Category:".
		 * Strip that markup because the breadcrumb itself is already
		 * semantically labelled.
		 */
		$archive_title = wp_strip_all_tags(
			$archive_title
		);

		if ( '' !== trim( $archive_title ) ) {
			$items[] =
				'<span aria-current="page">' .
				esc_html( $archive_title ) .
				'</span>';
		}
	}

	/*
	 * Search.
	 */
	elseif ( is_search() ) {

		$items[] =
			'<span aria-current="page">' .
			esc_html__(
				'Search Results',
				'advisers-multipurpose'
			) .
			'</span>';
	}

	/*
	 * 404.
	 */
	elseif ( is_404() ) {

		$items[] =
			'<span aria-current="page">' .
			esc_html__(
				'Page Not Found',
				'advisers-multipurpose'
			) .
			'</span>';
	}

	/*
	 * Nothing useful to output.
	 */
	if ( count( $items ) < 2 ) {
		return;
	}

	?>
	<nav
		class="advisers-multipurpose-breadcrumbs"
		aria-label="<?php esc_attr_e( 'Breadcrumb', 'advisers-multipurpose' ); ?>"
	>
		<?php
		echo implode(
			' <span class="breadcrumb-separator" aria-hidden="true">&raquo;</span> ',
			$items
		);
		?>
	</nav>
	<?php
}


/* ==========================================================================
 * 5. POST META
 * ========================================================================== */

/**
 * Render post publication date and author metadata.
 *
 * @return void
 */
function advisers_multipurpose_posted_on() {

	$post_id = get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	$date = get_the_date(
		get_option( 'date_format' ),
		$post_id
	);

	$author = get_the_author_meta(
		'display_name',
		(int) get_post_field(
			'post_author',
			$post_id
		)
	);

	$permalink = get_permalink( $post_id );

	if ( ! $permalink ) {
		$permalink = '';
	}

	?>
	<span class="posted-on">
		<time
			class="entry-date published"
			datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>"
		>
			<?php echo esc_html( $date ); ?>
		</time>
	</span>

	<span class="byline">
		<?php
		esc_html_e(
			'by',
			'advisers-multipurpose'
		);
		?>
		<span class="author vcard">
			<?php echo esc_html( $author ); ?>
		</span>
	</span>
	<?php
}


/* ==========================================================================
 * 6. SOCIAL ICON VISIBILITY
 * ========================================================================== */

/**
 * Determine whether social icons should render in a context.
 *
 * @param string $context 'header' or 'footer'.
 * @return bool
 */
function advisers_multipurpose_show_social_icons( $context ) {

	$context = sanitize_key( $context );

	if ( ! in_array( $context, array( 'header', 'footer' ), true ) ) {
		return false;
	}

	$position = get_theme_mod(
		'advisers_multipurpose_social_position',
		'footer'
	);

	$allowed_positions = array(
		'none',
		'header',
		'footer',
		'both',
	);

	if ( ! in_array( $position, $allowed_positions, true ) ) {
		$position = 'footer';
	}

	if ( 'none' === $position ) {
		return false;
	}

	if ( 'both' === $position ) {
		return true;
	}

	return $position === $context;
}


/* ==========================================================================
 * 7. SOCIAL ICONS
 * ========================================================================== */

/**
 * Render configured social links.
 *
 * Inline SVG is used deliberately:
 *
 * - No external icon font.
 * - No JavaScript dependency.
 * - No additional HTTP request.
 * - SVG markup is theme-controlled.
 *
 * @param string $context Social context: header/footer.
 * @return void
 */
function advisers_multipurpose_social_icons( $context = 'footer' ) {

	$context = sanitize_key( $context );

	if ( ! in_array( $context, array( 'header', 'footer' ), true ) ) {
		$context = 'footer';
	}

	$networks = array(

		'facebook' => array(
			'label' => __( 'Facebook', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M13.5 21v-7.5H16l.5-3h-3V8.5c0-.9.25-1.5 1.55-1.5h1.45V4.35c-.3-.05-1.3-.15-2.5-.15-2.4 0-4 1.45-4 4.1v2.2H7.5v3H10V21h3.5z"/></svg>',
		),

		'twitter' => array(
			'label' => __( 'Twitter / X', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M18.9 3H21.6l-5.8 6.6 6.9 9.4h-5.4l-4.2-5.6-4.8 5.6H4.6l6.2-7-6.6-9h5.5l3.8 5.1L18.9 3zm-1 14.4h1.5L7.2 4.5H5.6l12.3 12.9z"/></svg>',
		),

		'linkedin' => array(
			'label' => __( 'LinkedIn', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.94 8.5H4V20h2.94V8.5zM5.47 4a1.7 1.7 0 100 3.4 1.7 1.7 0 000-3.4zM20 13.3c0-3-1.6-4.4-3.75-4.4-1.73 0-2.5.95-2.94 1.62V8.5H10.4c.04.85 0 11.5 0 11.5h2.94v-6.4c0-.35.02-.7.13-.94.28-.7.92-1.42 2-1.42 1.4 0 1.97 1.07 1.97 2.64V20H20v-6.7z"/></svg>',
		),

		'instagram' => array(
			'label' => __( 'Instagram', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8.4a3.6 3.6 0 100 7.2 3.6 3.6 0 000-7.2zm0 5.9a2.3 2.3 0 110-4.6 2.3 2.3 0 010 4.6zm4.6-6.1a.84.84 0 11-1.68 0 .84.84 0 011.68 0zM20 8c-.08-1.5-.42-2.83-1.5-3.9C17.42 3 16.1 2.67 14.6 2.6c-1.55-.08-6.2-.08-7.75 0C5.35 2.67 4.03 3 2.95 4.08 1.87 5.16 1.53 6.48 1.46 8c-.08 1.55-.08 6.2 0 7.75.07 1.5.41 2.83 1.49 3.9 1.08 1.08 2.4 1.42 3.9 1.49 1.55.08 6.2.08 7.75 0 1.5-.07 2.83-.41 3.9-1.49 1.08-1.07 1.42-2.4 1.5-3.9.08-1.55.08-6.2 0-7.75zM18.2 17.3c-.32.83-.96 1.47-1.8 1.8-1.24.49-4.2.38-5.6.38s-4.36.1-5.6-.38a3.14 3.14 0 01-1.8-1.8c-.5-1.24-.38-4.2-.38-5.6s-.11-4.36.38-5.6c.32-.83.96-1.47 1.8-1.8 1.24-.49 4.2-.38 5.6-.38s4.36-.11 5.6.38c.83.32 1.47.96 1.8 1.8.49 1.24.38 4.2.38 5.6s.11 4.36-.38 5.6z"/></svg>',
		),

		'youtube' => array(
			'label' => __( 'YouTube', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21.6 7.6s-.2-1.5-.85-2.15c-.8-.85-1.7-.85-2.1-.9C15.9 4.4 12 4.4 12 4.4s-3.9 0-6.65.15c-.4.05-1.3.05-2.1.9C2.6 6.1 2.4 7.6 2.4 7.6S2.2 9.35 2.2 11.1v1.55c0 1.75.2 3.5.2 3.5s.2 1.5.85 2.15c.8.85 1.85.82 2.3.9C7.1 19.4 12 19.45 12 19.45s3.9 0 6.65-.15c.4-.05 1.3-.05 2.1-.9.65-.65.85-2.15.85-2.15s.2-1.75.2-3.5V11.1c0-1.75-.2-3.5-.2-3.5zM9.95 14.6V8.85l5.6 2.88-5.6 2.87z"/></svg>',
		),

		'whatsapp' => array(
			'label' => __( 'WhatsApp', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 14.4c-.3-.15-1.7-.85-2-.95-.27-.1-.46-.15-.66.15-.2.3-.75.94-.92 1.14-.17.2-.34.22-.63.08-.3-.15-1.24-.46-2.36-1.46-.87-.78-1.46-1.73-1.63-2.03-.17-.3-.02-.46.13-.6.13-.13.3-.34.44-.5.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.66-1.6-.9-2.18-.24-.58-.48-.5-.66-.5-.17-.01-.37-.01-.56-.01-.2 0-.53.07-.8.38-.28.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.55.72.31 1.28.5 1.72.63.72.23 1.38.2 1.9.12.58-.08 1.7-.7 1.94-1.37.24-.68.24-1.26.17-1.38-.07-.13-.27-.2-.57-.35zM12 2a10 10 0 00-8.6 15.1L2 22l4.99-1.36A10 10 0 1012 2zm0 18.2a8.2 8.2 0 01-4.2-1.15l-.3-.18-3.03.83.8-2.96-.2-.3A8.2 8.2 0 1112 20.2z"/></svg>',
		),

		'pinterest' => array(
			'label' => __( 'Pinterest', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2a10 10 0 00-3.65 19.3c-.05-.8-.1-2 .02-2.87.11-.8.73-3.36.73-3.36s-.19-.37-.19-.92c0-.86.5-1.5 1.12-1.5.53 0 .78.4.78.87 0 .53-.34 1.32-.51 2.05-.15.62.3 1.12.9 1.12 1.08 0 1.9-1.14 1.9-2.78 0-1.45-1.05-2.47-2.54-2.47-1.73 0-2.75 1.3-2.75 2.63 0 .53.2 1.09.45 1.4a.18.18 0 01.04.17c-.05.19-.15.62-.18.7-.03.12-.1.15-.22.09-.85-.4-1.38-1.63-1.38-2.63 0-2.14 1.55-4.11 4.48-4.11 2.35 0 4.18 1.68 4.18 3.92 0 2.34-1.47 4.22-3.52 4.22-.69 0-1.33-.36-1.55-.78l-.42 1.6c-.15.58-.56 1.32-.83 1.76A10 10 0 1012 2z"/></svg>',
		),

		'tiktok' => array(
			'label' => __( 'TikTok', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M16.6 5.82a4.28 4.28 0 01-3.15-1.4V15.3a5.1 5.1 0 11-4.4-5.06v2.2a2.9 2.9 0 102.2 2.82V2h2.2a4.28 4.28 0 001.15 2.94 4.27 4.27 0 002.7 1.3V8.4a6.5 6.5 0 01-.7-.06z"/></svg>',
		),

		'telegram' => array(
			'label' => __( 'Telegram', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21.6 3.9L18.5 19c-.24 1.05-.86 1.3-1.75.8l-4.83-3.56-2.33 2.24c-.26.26-.47.47-.96.47l.34-4.86 8.85-8c.38-.35-.08-.54-.6-.2L6.06 12.6l-4.8-1.5c-1.04-.33-1.06-1.04.22-1.53L20.3 2.6c.87-.32 1.63.2 1.3 1.3z"/></svg>',
		),

		'email' => array(
			'label' => __( 'Email', 'advisers-multipurpose' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm8 7.4L20 6.5V6H4v.5l8 5.9zM4 8.8V18h16V8.8l-8 5.9-8-5.9z"/></svg>',
		),
	);

	/*
	 * Validate icon size.
	 */
	$icon_size = get_theme_mod(
		'advisers_multipurpose_social_icon_size',
		'medium'
	);

	$allowed_sizes = array(
		'small',
		'medium',
		'large',
	);

	if ( ! in_array( $icon_size, $allowed_sizes, true ) ) {
		$icon_size = 'medium';
	}

	/*
	 * Collect configured links.
	 */
	$links = array();

	foreach ( $networks as $key => $network ) {

		$value = get_theme_mod(
			'advisers_multipurpose_social_' . $key,
			''
		);

		if ( ! is_string( $value ) ) {
			continue;
		}

		$value = trim( $value );

		if ( '' === $value ) {
			continue;
		}

		/*
		 * The Customizer already sanitizes these settings, but validate again
		 * at render time because theme-mod values can be modified by filters
		 * or imported settings.
		 */
		$url = esc_url(
			$value,
			array(
				'http',
				'https',
				'mailto',
				'tel',
			)
		);

		if ( '' === $url ) {
			continue;
		}

		$links[ $key ] = array(
			'url'   => $url,
			'label' => $network['label'],
			'icon'  => $network['icon'],
		);
	}

	if ( empty( $links ) ) {
		return;
	}

	?>
	<div class="<?php echo esc_attr( $context ); ?>-social icon-size-<?php echo esc_attr( $icon_size ); ?>">
		<ul class="social-icons">
			<?php foreach ( $links as $network ) : ?>
				<li>
					<a
						href="<?php echo esc_url( $network['url'] ); ?>"
						aria-label="<?php echo esc_attr( $network['label'] ); ?>"
						<?php
						/*
						 * Open external social URLs in a new tab.
						 * mailto/tel links remain in the same browsing context.
						 */
						if ( preg_match( '#^https?://#i', $network['url'] ) ) :
							?>
							target="_blank"
							rel="noopener noreferrer"
							<?php
						endif;
						?>
					>
						<?php
						/*
						 * SVG markup is hard-coded by the theme and does not
						 * contain user-controlled data.
						 */
						echo $network['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}