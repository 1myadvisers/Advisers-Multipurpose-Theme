<?php
/**
 * Block Patterns — Advisers Multipurpose.
 *
 * Provides lightweight page-section patterns using native WordPress blocks.
 * No page-builder plugin or external JavaScript dependency is required.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register the theme's block-pattern category.
 *
 * @return void
 */
function advisers_multipurpose_register_block_pattern_category() {

	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'advisers-multipurpose',
		array(
			'label'       => __( 'Advisers Multipurpose — Page Sections', 'advisers-multipurpose' ),
			'description' => __( 'Page sections designed for the Advisers Multipurpose theme.', 'advisers-multipurpose' ),
		)
	);
}
add_action(
	'init',
	'advisers_multipurpose_register_block_pattern_category'
);


/**
 * Register the theme's block patterns.
 *
 * @return void
 */
function advisers_multipurpose_register_block_patterns() {

	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}


	/**
	 * ---------------------------------------------------------------------
	 * Hero section.
	 * ---------------------------------------------------------------------
	 *
	 * The page's main H1 should normally appear only once.
	 */
	register_block_pattern(
		'advisers-multipurpose/hero',
		array(
			'title'         => __( 'Hero Section', 'advisers-multipurpose' ),
			'description'   => __( 'A full-width hero section with a main H1 heading, supporting text and call-to-action button.', 'advisers-multipurpose' ),
			'categories'    => array( 'advisers-multipurpose' ),
			'keywords'      => array(
				__( 'hero', 'advisers-multipurpose' ),
				__( 'header', 'advisers-multipurpose' ),
				__( 'banner', 'advisers-multipurpose' ),
				__( 'call to action', 'advisers-multipurpose' ),
			),
			'viewportWidth' => 1200,
			'content'       => <<<'BLOCK'
<!-- wp:group {"align":"full","className":"am-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull am-hero">
	<!-- wp:heading {"level":1,"textAlign":"center"} -->
	<h1 class="wp-block-heading has-text-align-center">Your Main Page Heading</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">Add a concise supporting statement that explains your service, product or purpose.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button">
			<a class="wp-block-button__link wp-element-button">Get Started</a>
		</div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
BLOCK,
		)
	);


	/**
	 * ---------------------------------------------------------------------
	 * Features grid.
	 * ---------------------------------------------------------------------
	 */
	register_block_pattern(
		'advisers-multipurpose/features-grid',
		array(
			'title'         => __( 'Features Grid — 3 Columns', 'advisers-multipurpose' ),
			'description'   => __( 'A responsive three-column section for presenting services, features or benefits.', 'advisers-multipurpose' ),
			'categories'    => array( 'advisers-multipurpose' ),
			'keywords'      => array(
				__( 'features', 'advisers-multipurpose' ),
				__( 'services', 'advisers-multipurpose' ),
				__( 'benefits', 'advisers-multipurpose' ),
				__( 'columns', 'advisers-multipurpose' ),
			),
			'viewportWidth' => 1200,
			'content'       => <<<'BLOCK'
<!-- wp:group {"align":"wide","className":"am-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide am-section">

	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center">Why Choose Us</h2>
	<!-- /wp:heading -->

	<!-- wp:columns -->
	<div class="wp-block-columns">

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Practical Solutions</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Describe the practical value your business or service provides.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Reliable Support</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Explain how customers receive guidance, support or ongoing assistance.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Technology Focused</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Describe the technology, tools or expertise relevant to your audience.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
BLOCK,
		)
	);


	/**
	 * ---------------------------------------------------------------------
	 * Call-to-action band.
	 * ---------------------------------------------------------------------
	 */
	register_block_pattern(
		'advisers-multipurpose/cta-band',
		array(
			'title'         => __( 'Call To Action Band', 'advisers-multipurpose' ),
			'description'   => __( 'A full-width call-to-action section for the end of a page or service section.', 'advisers-multipurpose' ),
			'categories'    => array( 'advisers-multipurpose' ),
			'keywords'      => array(
				__( 'CTA', 'advisers-multipurpose' ),
				__( 'call to action', 'advisers-multipurpose' ),
				__( 'contact', 'advisers-multipurpose' ),
				__( 'conversion', 'advisers-multipurpose' ),
			),
			'viewportWidth' => 1200,
			'content'       => <<<'BLOCK'
<!-- wp:group {"align":"full","className":"am-cta-band","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull am-cta-band">

	<!-- wp:heading {"level":2,"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center">Ready to Get Started?</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">Add a short, useful message explaining the next step for your visitor.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">

		<!-- wp:button -->
		<div class="wp-block-button">
			<a class="wp-block-button__link wp-element-button">Contact Us</a>
		</div>
		<!-- /wp:button -->

	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
BLOCK,
		)
	);


	/**
	 * ---------------------------------------------------------------------
	 * Testimonials.
	 * ---------------------------------------------------------------------
	 *
	 * Deliberately uses neutral placeholder copy rather than publishing
	 * fabricated customer claims.
	 */
	register_block_pattern(
		'advisers-multipurpose/testimonials',
		array(
			'title'         => __( 'Testimonials — 3 Columns', 'advisers-multipurpose' ),
			'description'   => __( 'A three-column testimonial layout. Replace all placeholder text with genuine customer feedback before publishing.', 'advisers-multipurpose' ),
			'categories'    => array( 'advisers-multipurpose' ),
			'keywords'      => array(
				__( 'testimonials', 'advisers-multipurpose' ),
				__( 'reviews', 'advisers-multipurpose' ),
				__( 'customer feedback', 'advisers-multipurpose' ),
			),
			'viewportWidth' => 1200,
			'content'       => <<<'BLOCK'
<!-- wp:group {"align":"wide","className":"am-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide am-section">

	<!-- wp:heading {"level":2,"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center">What Our Clients Say</h2>
	<!-- /wp:heading -->

	<!-- wp:columns -->
	<div class="wp-block-columns">

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:quote -->
			<blockquote class="wp-block-quote">
				<p>Add a genuine customer testimonial here.</p>
				<cite>Customer Name</cite>
			</blockquote>
			<!-- /wp:quote -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:quote -->
			<blockquote class="wp-block-quote">
				<p>Add a genuine customer testimonial here.</p>
				<cite>Customer Name</cite>
			</blockquote>
			<!-- /wp:quote -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:quote -->
			<blockquote class="wp-block-quote">
				<p>Add a genuine customer testimonial here.</p>
				<cite>Customer Name</cite>
			</blockquote>
			<!-- /wp:quote -->
		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
BLOCK,
		)
	);


	/**
	 * ---------------------------------------------------------------------
	 * My Advisers annual pricing pattern.
	 * ---------------------------------------------------------------------
	 *
	 * Uses the current three audience tiers:
	 *
	 * - Individual / Blogger
	 * - Small Business
	 * - Medium Business
	 *
	 * No enterprise tier is included.
	 */
	register_block_pattern(
		'advisers-multipurpose/pricing',
		array(
			'title'         => __( 'My Advisers Annual Pricing — 3 Plans', 'advisers-multipurpose' ),
			'description'   => __( 'A three-column annual pricing layout for Individual/Blogger, Small Business and Medium Business customers.', 'advisers-multipurpose' ),
			'categories'    => array( 'advisers-multipurpose' ),
			'keywords'      => array(
				__( 'pricing', 'advisers-multipurpose' ),
				__( 'plans', 'advisers-multipurpose' ),
				__( 'annual pricing', 'advisers-multipurpose' ),
				__( 'business plans', 'advisers-multipurpose' ),
			),
			'viewportWidth' => 1200,
			'content'       => <<<'BLOCK'
<!-- wp:group {"align":"wide","className":"am-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide am-section">

	<!-- wp:heading {"level":2,"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center">Compare My Advisers Annual Plans</h2>
	<!-- /wp:heading -->

	<!-- wp:columns -->
	<div class="wp-block-columns">

		<!-- wp:column {"className":"am-pricing-card"} -->
		<div class="wp-block-column am-pricing-card">

			<!-- wp:heading {"level":3,"textAlign":"center"} -->
			<h3 class="wp-block-heading has-text-align-center">Individual / Blogger</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><strong>From ₹24,999/year</strong></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>Suitable for individuals, bloggers and creators. Choose the relevant service category for your requirements.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button">
					<a class="wp-block-button__link wp-element-button">View Plans</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->


		<!-- wp:column {"className":"am-pricing-card"} -->
		<div class="wp-block-column am-pricing-card">

			<!-- wp:heading {"level":3,"textAlign":"center"} -->
			<h3 class="wp-block-heading has-text-align-center">Small Business</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><strong>From ₹59,999/year</strong></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>Designed for small businesses looking for practical technology, marketing and digital growth solutions.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button">
					<a class="wp-block-button__link wp-element-button">View Plans</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->


		<!-- wp:column {"className":"am-pricing-card"} -->
		<div class="wp-block-column am-pricing-card">

			<!-- wp:heading {"level":3,"textAlign":"center"} -->
			<h3 class="wp-block-heading has-text-align-center">Medium Business</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><strong>From ₹119,999/year</strong></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>For growing businesses requiring broader technology, digital marketing and consulting support.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button">
					<a class="wp-block-button__link wp-element-button">View Plans</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
BLOCK,
		)
	);
}
add_action(
	'init',
	'advisers_multipurpose_register_block_patterns',
	20
);


/**
 * Remove WordPress's bundled core block patterns.
 *
 * The theme provides its own focused pattern collection. This does not
 * disable block functionality; it only removes the default core pattern
 * collection from the inserter.
 *
 * Priority 11 runs after themes have normally registered their setup
 * features, making the removal reliable without affecting the theme's
 * own patterns.
 *
 * @return void
 */
function advisers_multipurpose_remove_core_patterns() {

	if ( current_theme_supports( 'core-block-patterns' ) ) {
		remove_theme_support( 'core-block-patterns' );
	}
}
add_action(
	'after_setup_theme',
	'advisers_multipurpose_remove_core_patterns',
	11
);