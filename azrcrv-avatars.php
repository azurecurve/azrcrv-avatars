<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name:		Avatars
 * Description:		Allow users to upload their own avatar.
 * Version:			2.2.5
 * Requires CP:		1.0
 * Requires PHP:	7.4
 * Author:			azurecurve
 * Author URI:		https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/avatars/
 * Donate link:		https://development.azurecurve.co.uk/support-development/
 * Text Domain:		azrcrv-a
 * Domain Path:		/assets/languages
 * License:			GPLv2 or later
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.html
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
const DEVELOPER_SHORTNAME = 'azurecurve';
const DEVELOPER_NAME      = DEVELOPER_SHORTNAME . ' | Development';
const DEVELOPER_URL_RAW  = 'https://development.azurecurve.co.uk/classicpress-plugins/';
const DEVELOPER_URL      = '<a href="' . DEVELOPER_URL_RAW . '">' . DEVELOPER_NAME . '</a>';

const PLUGIN_NAME       = 'Avatars';
const PLUGIN_SHORT_SLUG = 'avatars';
const PLUGIN_SLUG       = 'azrcrv-' . PLUGIN_SHORT_SLUG;
const PLUGIN_HYPHEN     = 'azrcrv-a';
const PLUGIN_UNDERSCORE = 'azrcrv_a';
const PLUGIN_FILE       = __FILE__;

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Include plugin Menu Client.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/azurecurve-menu-populate.php';
require_once dirname( PLUGIN_FILE ) . '/includes/azurecurve-menu-display.php';

/**
 * Include Update Client.
 */
require_once dirname( PLUGIN_FILE ) . '/libraries/updateclient/UpdateClient.class.php';

/**
 * Include setup of registration activation hook, actions, filters and shortcodes.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/setup.php';

/**
 * Load styles functions.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/functions-styles.php';

/**
 * Load scripts functions.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/functions-scripts.php';

/**
 * Load menu functions.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/functions-menu.php';

/**
 * Load language functions.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/functions-language.php';

/**
 * Load plugin image functions.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/functions-plugin-images.php';

/**
 * Load settings functions.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/functions-settings.php';

/**
 * Load plugin functionality.
 */
require_once dirname( PLUGIN_FILE ) . '/includes/plugin-functionality.php';
