<?php
/*
	language functions
*/

/**
 * Declare the Namespace.
 *
 * @since 1.0.0
 */
namespace azurecurve\Avatars;

/**
 * Load language files.
 *
 * @since 1.0.0
 */
function load_languages() {
	$plugin_rel_path = basename( dirname( __FILE__ ) ) . '../assets/languages';
	load_plugin_textdomain( 'azrcrv-a', false, $plugin_rel_path );
}
