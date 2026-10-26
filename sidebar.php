<?php
/**
 * Sidebar template.
 *
 * Provides the primary widget sidebar with an accessible
 * collapse/expand control.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Do not render the sidebar when the theme configuration
 * disables it for the current page.
 */
if ( ! function_exists( 'advisers_multipurpose_show_sidebar' ) ) {
	return;
}

if ( ! advisers_multipurpose_show_sidebar() ) {
	return;
}

/**
 * Resolve the configured sidebar position.
 */
$sidebar_position = 'right';

if ( function_exists( 'advisers_multipurpose_get_sidebar_position' ) ) {
	$sidebar_position = advisers_multipurpose_get_sidebar_position();
}

/**
 * Sanitize the position for use as a CSS class.
 */
$sidebar_position = sanitize_html_class( $sidebar_position );

/**
 * Sidebar content ID.
 *
 * A fixed ID is used because the theme currently exposes one
 * primary sidebar and the toggle button references this element
 * through aria-controls.
 */
$sidebar_id = 'secondary-content';

/**
 * Check whether the registered sidebar contains widgets.
 *
 * The sidebar is still rendered when the theme explicitly enables
 * it, even if no widgets are currently assigned. This keeps the
 * layout behavior controlled by the theme's sidebar settings.
 */
?>
<aside
	id="secondary"
	class="widget-area sidebar-<?php echo esc_attr( $sidebar_position ); ?>"
	aria-label="<?php esc_attr_e( 'Sidebar', 'advisers-multipurpose' ); ?>"
>
	<button
		type="button"
		class="sidebar-toggle"
		aria-expanded="true"
		aria-controls="<?php echo esc_attr( $sidebar_id ); ?>"
	>
		<span class="sidebar-toggle-label-show">
			<?php esc_html_e( 'Show Sidebar', 'advisers-multipurpose' ); ?>
		</span>

		<span class="sidebar-toggle-label-hide">
			<?php esc_html_e( 'Hide Sidebar', 'advisers-multipurpose' ); ?>
		</span>

		<span class="screen-reader-text sidebar-toggle-status">
			<?php esc_html_e( 'Sidebar is expanded', 'advisers-multipurpose' ); ?>
		</span>
	</button>

	<div
		id="<?php echo esc_attr( $sidebar_id ); ?>"
		class="sidebar-content"
	>
		<?php
		if ( is_active_sidebar( 'sidebar-primary' ) ) {
			dynamic_sidebar( 'sidebar-primary' );
		}
		?>
	</div>
</aside><!-- #secondary -->