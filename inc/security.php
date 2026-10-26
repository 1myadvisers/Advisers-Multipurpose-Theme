<?php
/**
 * Security hardening for Advisers Multipurpose.
 *
 * Follows WordPress.org Theme Review and WordPress Coding Standards:
 * escape late, sanitise early, verify capabilities and nonces, and keep
 * secrets out of the theme.
 *
 * Head clean-up (generator, RSD, shortlink) is plugin territory under the
 * WordPress.org Theme Review rules, so it is not done here.
 *
 * Every behaviour below can be switched off with a filter, so a site
 * owner can relax a rule if a specific plugin needs it.
 *
 * @package Advisers_Multipurpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
 * 1. XML-RPC, ENUMERATION AND LOGIN HARDENING
 * ========================================================================== */

/**
 * Disable XML-RPC (a common brute-force and DDoS amplification target).
 *
 * Disabled by default. Return true from the filter to keep it, for example
 * when the Jetpack or the WordPress mobile app is in use.
 */
if ( apply_filters( 'advisers_multipurpose_disable_xmlrpc', true ) ) {
	add_filter( 'xmlrpc_enabled', '__return_false' );
}

/**
 * Block author enumeration through /?author=1 and /author-sitemap style probes.
 *
 * @return void
 */
function advisers_multipurpose_block_user_enumeration() {

	if ( is_admin() || is_user_logged_in() ) {
		return;
	}

	if ( ! apply_filters( 'advisers_multipurpose_block_user_enumeration', true ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read only, public request probe.
	if ( isset( $_GET['author'] ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'advisers_multipurpose_block_user_enumeration', 0 );

/**
 * Hide the REST users list from anonymous visitors.
 *
 * @param array $endpoints Registered REST endpoints.
 * @return array
 */
function advisers_multipurpose_restrict_rest_users( $endpoints ) {

	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	if ( ! apply_filters( 'advisers_multipurpose_block_user_enumeration', true ) ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'advisers_multipurpose_restrict_rest_users' );

/**
 * Use one generic login error so attackers cannot tell whether a
 * username or an email address exists.
 *
 * @return string
 */
function advisers_multipurpose_generic_login_error() {
	return esc_html__( 'Login failed. Please check your details and try again.', 'advisers-multipurpose' );
}
add_filter( 'login_errors', 'advisers_multipurpose_generic_login_error' );


/* ==========================================================================
 * 2. SECURITY HEADERS
 * ========================================================================== */

/**
 * Send low-risk, widely supported security headers.
 *
 * A strict Content-Security-Policy is deliberately NOT sent from a theme,
 * because it would break plugins, analytics and page builders. Add one at
 * server level once you have listed every script source you use.
 *
 * @return void
 */
function advisers_multipurpose_security_headers() {

	if ( is_admin() || headers_sent() ) {
		return;
	}

	if ( ! apply_filters( 'advisers_multipurpose_send_security_headers', true ) ) {
		return;
	}

	$headers = array(
		'X-Content-Type-Options' => 'nosniff',
		'X-Frame-Options'        => 'SAMEORIGIN',
		'Referrer-Policy'        => 'strict-origin-when-cross-origin',
		'Permissions-Policy'     => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
	);

	if ( is_ssl() ) {
		$headers['Strict-Transport-Security'] = 'max-age=31536000';
	}

	/**
	 * Filter the security headers.
	 *
	 * @param array $headers Header name => value.
	 */
	$headers = apply_filters( 'advisers_multipurpose_security_header_list', $headers );

	foreach ( (array) $headers as $name => $value ) {

		if ( ! is_string( $name ) || ! is_string( $value ) || '' === $value ) {
			continue;
		}

		// Strip line breaks to rule out header injection.
		$name  = preg_replace( '/[\r\n]+/', '', $name );
		$value = preg_replace( '/[\r\n]+/', '', $value );

		header( $name . ': ' . $value );
	}
}
add_action( 'send_headers', 'advisers_multipurpose_security_headers' );


/* ==========================================================================
 * 3. SECRETS HELPERS
 *
 * The theme itself stores no API keys, passwords or tokens. If you add an
 * integration later, keep the secret in wp-config.php:
 *
 *     define( 'MY_SERVICE_KEY', 'value' );
 *
 * and read it with advisers_multipurpose_get_secret( 'MY_SERVICE_KEY' ).
 * If a value must live in the database, store it only after passing it
 * through advisers_multipurpose_encrypt().
 * ========================================================================== */

/**
 * Derive a 32 byte encryption key from the site's secret salts.
 *
 * @return string Binary key, or empty string when unavailable.
 */
function advisers_multipurpose_crypto_key() {

	if ( ! function_exists( 'sodium_crypto_secretbox' ) ) {
		return '';
	}

	return sodium_crypto_generichash(
		wp_salt( 'auth' ) . wp_salt( 'secure_auth' ),
		'',
		SODIUM_CRYPTO_SECRETBOX_KEYBYTES
	);
}

/**
 * Encrypt a string (authenticated encryption, XSalsa20-Poly1305).
 *
 * @param string $plain Plain text.
 * @return string Hex text, or empty string on failure.
 */
function advisers_multipurpose_encrypt( $plain ) {

	$key = advisers_multipurpose_crypto_key();

	if ( '' === $key || '' === (string) $plain ) {
		return '';
	}

	$nonce  = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$cipher = sodium_crypto_secretbox( (string) $plain, $nonce, $key );

	return bin2hex( $nonce . $cipher );
}

/**
 * Decrypt a value made by advisers_multipurpose_encrypt().
 *
 * @param string $encoded Hex text.
 * @return string Plain text, or empty string when invalid or tampered.
 */
function advisers_multipurpose_decrypt( $encoded ) {

	$key = advisers_multipurpose_crypto_key();

	if ( '' === $key || '' === (string) $encoded ) {
		return '';
	}

	if ( ! ctype_xdigit( (string) $encoded ) || 0 !== strlen( $encoded ) % 2 ) {
		return '';
	}

	$raw = hex2bin( (string) $encoded );

	if ( false === $raw || strlen( $raw ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
		return '';
	}

	$nonce  = substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$cipher = substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$plain  = sodium_crypto_secretbox_open( $cipher, $nonce, $key );

	return false === $plain ? '' : $plain;
}

/**
 * Read a secret from a wp-config.php constant.
 *
 * @param string $constant_name Constant name, e.g. MY_SERVICE_KEY.
 * @return string
 */
function advisers_multipurpose_get_secret( $constant_name ) {

	$constant_name = preg_replace( '/[^A-Z0-9_]/', '', strtoupper( (string) $constant_name ) );

	if ( '' !== $constant_name && defined( $constant_name ) ) {
		return (string) constant( $constant_name );
	}

	return '';
}
