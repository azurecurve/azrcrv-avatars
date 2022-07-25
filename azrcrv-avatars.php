<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Avatars
 * Description: Allow users to upload their own avatar.
 * Version: 2.1.0
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/avatars/
 * Text Domain: azrcrv-a
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\Avatars;

/**
 * Define constants.
 */
const PLUGIN_NAME       = 'Avatars';
const PLUGIN_SLUG       = 'azrcrv-avatars';
const PLUGIN_HYPHEN     = 'azrcrv-a';
const PLUGIN_UNDERSCORE = 'azrcrv_a';

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Include plugin Menu Client.
 */
require_once dirname( __FILE__ ) . '/includes/azurecurve-menu-populate.php';
require_once dirname( __FILE__ ) . '/includes/azurecurve-menu-display.php';

/**
 * Include Update Client.
 */
require_once dirname( __FILE__ ) . '/libraries/updateclient/UpdateClient.class.php';

/**
 * Include setup of registration activation hook, actions, filters and shortcodes.
 */
require_once dirname( __FILE__ ) . '/includes/setup.php';

/**
 * Load styles functions.
 */
require_once dirname( __FILE__ ) . '/includes/functions-styles.php';

/**
 * Load scripts functions.
 */
require_once dirname( __FILE__ ) . '/includes/functions-scripts.php';

/**
 * Load menu functions.
 */
require_once dirname( __FILE__ ) . '/includes/functions-menu.php';

/**
 * Load language functions.
 */
require_once dirname( __FILE__ ) . '/includes/functions-language.php';

/**
 * Load plugin image functions.
 */
require_once dirname( __FILE__ ) . '/includes/functions-plugin-images.php';

/**
 * Load settings functions.
 */
require_once dirname( __FILE__ ) . '/includes/functions-settings.php';

/**
 * Load plugin functionality.
 */
require_once dirname( __FILE__ ) . '/includes/plugin-functionality.php';
