<?php
/**
 * SEO integration and administration helpers.
 *
 * Advisers Multipurpose intentionally does not generate SEO metadata,
 * Open Graph markup, Twitter Cards, canonical URLs, or structured data.
 *
 * SEO metadata and schema should be handled by a dedicated SEO plugin such
 * as Rank Math, Yoast SEO, or All in One SEO.
 *
 * The theme itself focuses on:
 * - Semantic HTML5 markup.
 * - Correct document structure.
 * - Accessible headings.
 * - Performance-friendly markup.
 * - Theme compatibility with SEO plugins.
 * - Optional administrator guidance when no SEO plugin is detected.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* ==========================================================================
 * 1. SEO PLUGIN DETECTION
 * ========================================================================== */

/**
 * Determine whether a supported SEO plugin is active.
 *
 * This function is used only for administrator guidance.
 *
 * No frontend SEO output is enabled or disabled by this function.
 *
 * @return bool
 */
function advisers_multipurpose_seo_plugin_active() {

	/*
	 * Rank Math.
	 */
	if (
		defined( 'RANK_MATH_VERSION' ) ||
		class_exists( 'RankMath' )
	) {
		return true;
	}

	/*
	 * Yoast SEO.
	 */
	if (
		defined( 'WPSEO_VERSION' ) ||
		class_exists( 'WPSEO_Options' )
	) {
		return true;
	}

	/*
	 * All in One SEO.
	 */
	if (
		defined( 'AIOSEO_VERSION' ) ||
		class_exists( 'AIOSEO\\Plugin\\AIOSEO' )
	) {
		return true;
	}

	/*
	 * SEOPress.
	 */
	if (
		defined( 'SEOPRESS_VERSION' ) ||
		function_exists( 'seopress_get_service' )
	) {
		return true;
	}

	/*
	 * The SEO Framework.
	 */
	if (
		defined( 'THE_SEO_FRAMEWORK_VERSION' ) ||
		class_exists( 'The_SEO_Framework\\Load' )
	) {
		return true;
	}

	return false;
}


/* ==========================================================================
 * 2. ADMIN NOTICE
 * ========================================================================== */

/**
 * Display an optional SEO plugin recommendation in the WordPress admin.
 *
 * The notice:
 * - Appears only to administrators who can install plugins.
 * - Appears only on selected admin screens.
 * - Is not displayed when a supported SEO plugin is active.
 * - Can be permanently dismissed by the current user.
 *
 * @return void
 */
function advisers_multipurpose_admin_seo_notice() {

	/*
	 * Do not run in AJAX requests.
	 */
	if ( wp_doing_ajax() ) {
		return;
	}

	/*
	 * Do not run in REST requests.
	 */
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	/*
	 * Do not show the notice if an SEO plugin is already active.
	 */
	if ( advisers_multipurpose_seo_plugin_active() ) {
		return;
	}

	/*
	 * Installing plugins requires appropriate capability.
	 */
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	/*
	 * Only display on useful administration screens.
	 */
	$screen = function_exists( 'get_current_screen' )
		? get_current_screen()
		: false;

	if ( ! $screen ) {
		return;
	}

	$allowed_screens = array(
		'dashboard',
		'themes',
		'appearance_page_advisers-multipurpose',
	);

	if ( ! in_array( $screen->id, $allowed_screens, true ) ) {
		return;
	}

	/*
	 * Per-user dismissal.
	 */
	$user_id = get_current_user_id();

	if ( ! $user_id ) {
		return;
	}

	$dismissed = get_user_meta(
		$user_id,
		'advisers_multipurpose_dismissed_seo_notice',
		true
	);

	if ( $dismissed ) {
		return;
	}

	/*
	 * Create a nonce-protected dismissal URL.
	 */
	$dismiss_url = wp_nonce_url(
		add_query_arg(
			array(
				'advisers_multipurpose_dismiss_seo_notice' => '1',
			),
			admin_url( 'index.php' )
		),
		'advisers_multipurpose_dismiss_seo_notice'
	);

	?>
	<div class="notice notice-info">
		<p>
			<strong>
				<?php
				esc_html_e(
					'Advisers Multipurpose SEO integration',
					'advisers-multipurpose'
				);
				?>
			</strong>
			<br>
			<?php
			esc_html_e(
				'The theme provides semantic, performance-friendly markup but does not generate duplicate SEO metadata or schema. For titles, meta descriptions, XML sitemaps, schema and social metadata, use a dedicated SEO plugin.',
				'advisers-multipurpose'
			);
			?>
		</p>

		<p>
			<a
				class="button button-secondary"
				href="<?php echo esc_url( $dismiss_url ); ?>"
			>
				<?php
				esc_html_e(
					'Dismiss',
					'advisers-multipurpose'
				);
				?>
			</a>
		</p>
	</div>
	<?php
}
add_action(
	'admin_notices',
	'advisers_multipurpose_admin_seo_notice'
);


/* ==========================================================================
 * 3. ADMIN NOTICE DISMISSAL
 * ========================================================================== */

/**
 * Persist SEO notice dismissal for the current administrator.
 *
 * @return void
 */
function advisers_multipurpose_handle_dismiss_seo_notice() {

	if (
		! is_admin() ||
		! isset( $_GET['advisers_multipurpose_dismiss_seo_notice'] )
	) {
		return;
	}

	/*
	 * Capability check before processing the request.
	 */
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	/*
	 * Verify nonce.
	 */
	$nonce = isset( $_GET['_wpnonce'] )
		? sanitize_text_field(
			wp_unslash( $_GET['_wpnonce'] )
		)
		: '';

	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'advisers_multipurpose_dismiss_seo_notice' ) ) {
		return;
	}

	/*
	 * Store dismissal per user.
	 */
	$user_id = get_current_user_id();

	if ( $user_id ) {
		update_user_meta(
			$user_id,
			'advisers_multipurpose_dismissed_seo_notice',
			'1'
		);
	}

	/*
	 * Redirect to a clean admin URL.
	 */
	$redirect_url = remove_query_arg(
		array(
			'advisers_multipurpose_dismiss_seo_notice',
			'_wpnonce',
		),
		wp_get_referer()
			? wp_get_referer()
			: admin_url( 'index.php' )
	);

	/*
	 * Prevent an unsafe external redirect.
	 */
	$redirect_url = wp_validate_redirect(
		$redirect_url,
		admin_url( 'index.php' )
	);

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action(
	'admin_init',
	'advisers_multipurpose_handle_dismiss_seo_notice'
);


/* ==========================================================================
 * 4. SEO FRONTEND POLICY
 * ========================================================================== */

/**
 * Advisers Multipurpose deliberately does not output duplicate SEO markup.
 *
 * Do NOT add the following to this theme:
 *
 * - <meta name="description">
 * - <meta property="og:*">
 * - <meta name="twitter:*">
 * - JSON-LD schema
 * - custom canonical tags
 * - SEO title generation
 * - robots meta generation
 * - XML sitemap generation
 *
 * These features belong to dedicated SEO functionality.
 *
 * WordPress core and the selected SEO plugin should remain responsible
 * for their respective functionality.
 *
 * @return void
 */
function advisers_multipurpose_seo_frontend_policy() {
	/*
	 * Intentionally empty.
	 *
	 * This function exists as explicit documentation of the theme's SEO
	 * architecture and prevents future maintainers from accidentally
	 * introducing duplicate SEO markup.
	 */
}


/* ==========================================================================
 * 5. WordPress CORE HEAD HOOKS
 * ========================================================================== */

/**
 * Do not remove WordPress core head hooks from the theme.
 *
 * The theme leaves wp_generator, rsd_link, wlwmanifest_link, shortlink,
 * adjacent_posts_rel_link_wp_head and rel_canonical untouched. Removing the
 * version/discovery tags is plugin territory (see the readme for a snippet).
 *
 * No action is required here.
 */


/* ==========================================================================
 * 6. DEVELOPER DOCUMENTATION
 * ========================================================================== */

/**
 * SEO architecture for Advisers Multipurpose:
 *
 * THEME RESPONSIBILITIES
 * ----------------------
 * - Semantic HTML.
 * - Accessible navigation.
 * - Correct heading hierarchy.
 * - Image dimensions / CLS prevention.
 * - Performance-friendly markup.
 * - Breadcrumb presentation when enabled.
 * - Clean template structure.
 *
 * SEO PLUGIN RESPONSIBILITIES
 * ---------------------------
 * - SEO titles.
 * - Meta descriptions.
 * - Canonical URLs where required.
 * - Open Graph metadata.
 * - Twitter/X metadata.
 * - JSON-LD structured data.
 * - XML sitemaps.
 * - Robots directives.
 * - Social preview metadata.
 *
 * This separation prevents duplicate metadata and conflicting schema.
 */


/* ==========================================================================
 * 7. FALLBACK SEO OUTPUT (only when no SEO plugin is active)
 * ========================================================================== */

/**
 * Print a meta description, Open Graph / Twitter tags and basic JSON-LD.
 *
 * Skipped entirely when Rank Math, Yoast, AIOSEO, SEOPress or similar is
 * active, so metadata is never duplicated.
 *
 * @return void
 */
function advisers_multipurpose_fallback_seo_head() {

	if ( is_admin() || is_feed() || is_robots() || is_embed() ) {
		return;
	}

	if ( function_exists( 'advisers_multipurpose_seo_plugin_active' ) && advisers_multipurpose_seo_plugin_active() ) {
		return;
	}

	if ( ! apply_filters( 'advisers_multipurpose_fallback_seo', true ) ) {
		return;
	}

	$site_name   = wp_strip_all_tags( get_bloginfo( 'name', 'display' ) );
	$description = '';
	$title       = '';
	$url         = home_url( '/' );
	$type        = 'website';
	$image       = '';

	if ( is_singular() ) {

		$post = get_queried_object();

		if ( $post instanceof WP_Post && ! post_password_required( $post ) ) {

			$title       = wp_strip_all_tags( get_the_title( $post ) );
			$url         = get_permalink( $post );
			$type        = is_single() ? 'article' : 'website';
			$description = has_excerpt( $post )
				? $post->post_excerpt
				: wp_trim_words( strip_shortcodes( $post->post_content ), 30, '' );

			if ( has_post_thumbnail( $post ) ) {
				$image = (string) get_the_post_thumbnail_url( $post, 'large' );
			}
		}
	} elseif ( is_front_page() || is_home() ) {

		$title       = $site_name;
		$description = get_bloginfo( 'description', 'display' );
	} elseif ( is_category() || is_tag() || is_tax() ) {

		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$title       = $term->name;
			$description = term_description( $term );
			$link        = get_term_link( $term );
			$url         = is_wp_error( $link ) ? $url : $link;
		}
	}

	$description = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $description ) ) );
	$description = wp_html_excerpt( $description, 160, '' );

	if ( '' === $title ) {
		return;
	}

	if ( '' === $image ) {
		$image = (string) get_site_icon_url( 512 );
	}

	if ( '' !== $description ) {
		printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
	}

	printf( "<meta property=\"og:locale\" content=\"%s\">\n", esc_attr( get_locale() ) );
	printf( "<meta property=\"og:type\" content=\"%s\">\n", esc_attr( $type ) );
	printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
	printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( $site_name ) );

	if ( '' !== $description ) {
		printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $description ) );
	}

	if ( '' !== $image ) {
		printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image ) );
	}

	printf( "<meta name=\"twitter:card\" content=\"%s\">\n", esc_attr( '' !== $image ? 'summary_large_image' : 'summary' ) );

	if ( is_front_page() ) {

		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type' => 'WebSite',
					'@id'   => home_url( '/#website' ),
					'url'   => home_url( '/' ),
					'name'  => $site_name,
				),
				array(
					'@type' => 'Organization',
					'@id'   => home_url( '/#organization' ),
					'url'   => home_url( '/' ),
					'name'  => $site_name,
				),
			),
		);

		printf(
			"<script type=\"application/ld+json\">%s</script>\n",
			wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP )
		);
	}
}
add_action( 'wp_head', 'advisers_multipurpose_fallback_seo_head', 5 );

/**
 * Keep internal search results and 404s out of the index (fallback only).
 *
 * @param array $robots Robots directives.
 * @return array
 */
function advisers_multipurpose_fallback_robots( $robots ) {

	if ( function_exists( 'advisers_multipurpose_seo_plugin_active' ) && advisers_multipurpose_seo_plugin_active() ) {
		return $robots;
	}

	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'] );
	}

	return $robots;
}
add_filter( 'wp_robots', 'advisers_multipurpose_fallback_robots' );
