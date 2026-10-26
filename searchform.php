<?php
/**
 * Search form template.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="search-field"><?php esc_html_e( 'Search for:', 'advisers-multipurpose' ); ?></label>
	<input type="search" id="search-field" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'advisers-multipurpose' ); ?>" value="<?php echo esc_attr( get_search_query( false ) ); ?>" name="s">
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'advisers-multipurpose' ); ?></button>
</form>
