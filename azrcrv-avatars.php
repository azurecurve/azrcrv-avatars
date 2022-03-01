<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Avatars
 * Description: Allow users to upload their own avatar.
 * Version: 2.0.0
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

// Declare the namespace.
namespace azurecurve\Avatars;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// include plugin menu.
require_once dirname( __FILE__ ) . '/pluginmenu/menu.php';
add_action( 'admin_init', 'azrcrv_create_plugin_menu_a' );

// include update client.
require_once dirname( __FILE__ ) . '/libraries/updateclient/UpdateClient.class.php';

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 */

// add actions.
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_action( 'admin_post_azrcrv_a_save_options', __NAMESPACE__ . '\\save_options' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
add_action( 'show_user_profile', __NAMESPACE__ . '\\edit_user_profile_avatars' );
add_action( 'edit_user_profile', __NAMESPACE__ . '\\edit_user_profile_avatars' );
add_action( 'personal_options_update', __NAMESPACE__ . '\\save_user_profile_avatars' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save_user_profile_avatars' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\media_uploader' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );

// add filters.
add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );
add_filter( 'avatar_defaults', __NAMESPACE__ . '\\set_default_avatar' );
add_filter( 'get_avatar', __NAMESPACE__ . '\\return_avatar', 10, 5 );
add_filter( 'avatar_defaults', __NAMESPACE__ . '\\avatar_defaults' );
add_filter( 'codepotent_update_manager_image_path', __NAMESPACE__ . '\\custom_image_path' );
add_filter( 'codepotent_update_manager_image_url', __NAMESPACE__ . '\\custom_image_url' );

// add shortcodes.
add_shortcode( 'avatar', __NAMESPACE__ . '\\show_avatar' );

/**
 * Load language files.
 *
 * @since 1.0.0
 */
function load_languages() {
	$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'azrcrv-a', false, $plugin_rel_path );
}

/**
 * Media uploader.
 *
 * @since 1.0.0
 */
function media_uploader() {
	global $post_type;

	if ( function_exists( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	} else {
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	}
}

/**
 * Register admin styles.
 *
 * @since 2.0.0
 */
function register_admin_styles() {
	wp_register_style( 'azrcrv-a-admin-styles', esc_url_raw( plugins_url( 'assets/css/admin.css', __FILE__ ) ), array(), '1.0.0' );
	wp_register_style( 'azrcrv-pluginmenu-admin-styles', esc_url_raw( plugins_url( 'pluginmenu/css/style.css', __FILE__ ) ), array(), '1.0.0' );
}

/**
 * Enqueue admin styles.
 *
 * @since 2.0.0
 */
function enqueue_admin_styles() {
	global $pagenow;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'azrcrv-a' ) || ( $pagenow == 'profile.php' ) ) {
		wp_enqueue_style( 'azrcrv-a-admin-styles' );
		wp_enqueue_style( 'azrcrv-pluginmenu-admin-styles' );
	}
}

/**
 * Register admin scripts.
 *
 * @since 2.0.0
 */
function register_admin_scripts() {
	wp_register_script( 'azrcrv-a-admin-jquery', esc_url_raw( plugins_url( 'assets/jquery/admin.js', __FILE__ ) ), array(), '1.0.0', true );
}

/**
 * Enqueue admin styles.
 *
 * @since 2.0.0
 */
function enqueue_admin_scripts() {
	global $pagenow;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'azrcrv-a' ) || ( $pagenow == 'profile.php' ) ) {
		wp_enqueue_script( 'azrcrv-a-admin-jquery' );
	}
}

/**
 * Custom plugin image path.
 *
 * @since 1.2.0
 */
function custom_image_path( $path ) {
	if ( strpos( $path, 'azrcrv-avatars' ) !== false ) {
		$path = plugin_dir_path( __FILE__ ) . 'assets/pluginimages';
	}
	return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.2.0
 */
function custom_image_url( $url ) {
	if ( strpos( $url, 'azrcrv-avatars' ) !== false ) {
		$url = esc_url_raw( plugin_dir_url( __FILE__ ) . 'assets/pluginimages' );
	}
	return $url;
}

/**
 * Get options including defaults.
 *
 * @since 1.2.0
 */
function get_option_with_defaults( $option_name ) {

	$defaults = array(
		'localonly'             => 0,
		'custom-default-avatar' => '',
	);

	$options = get_option( $option_name, $defaults );

	$options = wp_parse_args( $options, $defaults );

	return $options;

}

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 */
function add_plugin_action_link( $links, $file ) {
	static $this_plugin;

	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . esc_url_raw( admin_url( 'admin.php?page=azrcrv-a' ) . '"><img src="' . plugins_url( '/pluginmenu/images/logo.svg', __FILE__ ) ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />' . esc_html__( 'Settings', 'azrcrv-a' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 */
function create_admin_menu() {

	add_submenu_page(
		'azrcrv-plugin-menu',
		esc_html__( 'Avatar Settings', 'azrcrv-a' ),
		esc_html__( 'Avatars', 'azrcrv-a' ),
		'manage_options',
		'azrcrv-a',
		__NAMESPACE__ . '\\display_options'
	);
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 */
function display_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-a' ) );
	}

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( 'azrcrv-a' );

	echo '<div id="azrcrv-a-general" class="wrap">';

		echo '<h1>';
			echo '<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="' . esc_url_raw( plugins_url( '/pluginmenu/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
			echo esc_html( get_admin_page_title() );
		echo '</h1>';

	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible">
				<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-a' ) . '</strong></p>
			</div>';
	}

		$tab_1_label = esc_html__( 'Avatar Settings', 'azrcrv-a' );
		$tab_1       = '
		<table class="form-table azrcrv-settings">
				
			<tr>
			
				<th scope="row" colspan="2">
				
					<label for="explanation">
						' . esc_html__( 'Avatars allows the admin to specify a default avatar and allows users to upload their own avatar.', 'azrcrv-a' ) . '
					</label>
					
				</th>
				
			</tr>
		
			<tr>
			
				<th scope="row">
				
					' . esc_html__( 'Use local avatars only?', 'azrcrv-a' ) . '
					
				</th>
				
				<td>
				
					<fieldset>
						<legend class="screen-reader-text">
								' . esc_html__( 'Use local avatars only?', 'azrcrv-a' ) . '
						</legend>
						
						<label for="localonly">
							<input name="localonly" type="checkbox" id="localonly" value="1" ' . checked( '1', $options['localonly'], false ) . ' />
						</label>
						
					</fieldset>
					
				</td>
				
			</tr>
		
			<tr>
				<th scope="row">
				
					' . esc_html__( 'Upload custom default avatar?', 'azrcrv-a' ) . '
					
				</th>
				
				<td>

					<p>
					
						<img src="' . esc_url_raw( $options['custom-default-avatar'] ) . '" id="custom-default-avatar-src" style="width: 300px;"><br />
						
						<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
						<input type="hidden" name="custom-default-avatar" id="custom-default-avatar" value="' . esc_url_raw( $options['custom-default-avatar'] ) . '" class="regular-text" />
						
						<input type="button" id="azrcrv-a-upload-avatar" class="button upload" value="' . esc_html__( 'Upload avatar', 'azrcrv-a' ) . '" />
						<input type="button" id="azrcrv-a-remove-avatar" class="button remove" value="' . esc_html__( 'Remove avatar', 'azrcrv-a' ) . '" />
						
					</p>
					
					<p>
						<span class="description">' . esc_html__( 'Upload, choose or remove the custom avatar.', 'azrcrv-a' ) . '</span>
					</p>
			
				</td>
				
			</tr>
		
		</table>';

		$tab_3_label = esc_html__( 'Instructions', 'azrcrv-a' );
		$tab_3       = '
		<table class="form-table azrcrv-settings">

			<tr>
			
				<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
					
						<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'Custom Avatar', 'azrcrv-a' ) . '</h2>
					
				</th>

			</tr>

			<tr>
			
				<td scope="row" colspan=2>
				
					<p>' .
						sprintf( esc_html__( 'A custom avatar can be uploaded on the Avatar Settings page located on the %s menu.', 'azrcrv-a' ), '<strong>azurecurve</strong>' ) . '
							
					</p>
				
					<p>' .
						sprintf( esc_html__( 'Once a custom avatar has been uploaded, it can be selected as the default on the Discussions page on the Settings menu.', 'azrcrv-a' ), 'new text' ) . '
							
					</p>
				
				</td>
			
			</tr>

			<tr>
			
				<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
					
						<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'User Avatar', 'azrcrv-a' ) . '</h2>
					
				</th>

			</tr>

			<tr>
			
				<td scope="row" colspan=2>
				
					<p>' .
						esc_html__( 'Users can set an avatar of their own on their Profile page; administrators can set or change a user\'s avatar on the Edit User page.', 'azrcrv-a' ) . '
							
					</p>
					<p>' .
						esc_html__( 'If no user avatar is specified, the default avatar selected on the Discussions page will be used.', 'azrcrv-a' ) . '
							
					</p>
				
				</td>
			
			</tr>
			
		</table>';

		$plugin_array = get_option( 'azrcrv-plugin-menu' );

		$tab_4_plugins = '';
	foreach ( $plugin_array as $plugin_name => $plugin_details ) {
		if ( $plugin_details['retired'] == 0 ) {
			$alternative_color = '';
			if ( isset( $plugin_details['bright'] ) and $plugin_details['bright'] == 1 ) {
				$alternative_color = 'bright-';
			}
			if ( isset( $plugin_details['premium'] ) and $plugin_details['premium'] == 1 ) {
				$alternative_color = 'premium-';
			}
			if ( is_plugin_active( $plugin_details['plugin_link'] ) ) {
				$tab_4_plugins .= "<a href='{$plugin_details['admin_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
			} else {
				$tab_4_plugins .= "<a href='{$plugin_details['dev_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
			}
		}
	}

		$tab_4_label = esc_html__( 'Other Plugins', 'azrcrv-a' );
		$tab_4       = '
		<table class="form-table azrcrv-settings">

			<tr>
			
				<td scope="row" colspan=2>
				
					<p>' .
						sprintf( esc_html__( '%1$s was one of the first plugin developers to start developing for Classicpress; all plugins are available from %2$s and are integrated with the %3$s plugin for fully integrated, no hassle, updates.', 'azrcrv-a' ), '<strong>azurecurve | Development</strong>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve | Development</a>', '<a href="https://directory.classicpress.net/plugins/update-manager/">Update Manager</a>' )
					. '</p>
					<p>' .
						sprintf( esc_html__( 'Other plugins available from %s are:', 'azrcrv-a' ), '<strong>azurecurve | Development</strong>' )
					. '</p>
				
				</td>
			
			</tr>
			
			<tr>
			
				<td scope="row" colspan=2>
				
					' . $tab_4_plugins . '
					
				</td>

			</tr>
			
		</table>';

	?>
		<form method="post" action="admin-post.php">

				<input type="hidden" name="action" value="azrcrv_a_save_options" />

				<?php
					// <!-- Adding security through hidden referer field -->.
					wp_nonce_field( 'azrcrv-a', 'azrcrv-a-nonce' );
				?>
				
				
				<div id="tabs" class="azrcrv-ui-tabs">
					<ul class="azrcrv-ui-tabs-nav azrcrv-ui-widget-header" role="tablist">
						<li class="azrcrv-ui-state-default azrcrv-ui-state-active" aria-controls="tab-panel-1" aria-labelledby="tab-1" aria-selected="true" aria-expanded="true" role="tab">
							<a id="tab-1" class="azrcrv-ui-tabs-anchor" href="#tab-panel-1"><?php echo $tab_1_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-3" aria-labelledby="tab-3" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-3" class="azrcrv-ui-tabs-anchor" href="#tab-panel-3"><?php echo $tab_3_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-4" aria-labelledby="tab-4" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-4" class="azrcrv-ui-tabs-anchor" href="#tab-panel-4"><?php echo $tab_4_label; ?></a>
						</li>
					</ul>
					<div id="tab-panel-1" class="azrcrv-ui-tabs-scroll" role="tabpanel" aria-hidden="false">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_1_label; ?>
							</legend>
							<?php echo $tab_1; ?>
						</fieldset>
					</div>
					<div id="tab-panel-3" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_3_label; ?>
							</legend>
							<?php echo $tab_3; ?>
						</fieldset>
					</div>
					<div id="tab-panel-4" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_4_label; ?>
							</legend>
							<?php echo $tab_4; ?>
						</fieldset>
					</div>
				</div>

			<input type="submit" name="btn_save" value="<?php esc_html_e( 'Save Settings', 'azrcrv-a' ); ?>" class="button-primary"/>
		</form>
		<div class='azrcrv-donate'>
			<?php
				esc_html_e( 'Support', 'azrcrv-a' );
			?>
			azurecurve | Development
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="MCJQN9SJZYLWJ">
				<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
			</form>
			<span>
				<?php
				esc_html_e( 'You can help support the development of our free plugins by donating a small amount of money.', 'azrcrv-a' );
				?>
			</span>
		</div>
		
	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 */
function save_options() {
	// Check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-a' ) );
	}
	// Check that nonce field created in configuration form is present.
	if ( ! empty( $_POST ) && check_admin_referer( 'azrcrv-a', 'azrcrv-a-nonce' ) ) {

		// Retrieve original plugin options array.
		$options = get_option_with_defaults( 'azrcrv-a' );

		$option_name = 'localonly';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = 1;
		} else {
			$options[ $option_name ] = 0;
		}

		$option_name = 'custom-default-avatar';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		// Store updated options array to database.
		update_option( 'azrcrv-a', $options );

		// Redirect the page to the configuration form that was processed.
		wp_safe_redirect( add_query_arg( 'page', 'azrcrv-a&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}

/**
 *
 * Change default gravatar.
 *
 * @since 1.0.0
 */
function set_default_avatar( $default_avatar ) {

	$options = get_option_with_defaults( 'azrcrv-a' );

	// azurecurve avatar.
	$avatar                    = plugins_url( '/images/customavatar.png', __FILE__ );
	$default_avatar[ $avatar ] = 'Custom Avatar';

	// your custom avatar.
	if ( strlen( $options['custom-default-avatar'] ) > 0 ) {
		$avatar                    = esc_url_raw( $options['custom-default-avatar'] );
		$default_avatar[ $avatar ] = 'Your Custom Avatar';
	}

	return $default_avatar;
}


/**
 *
 * Display avatar shortcode.
 *
 * @since 1.0.0
 */
function display_avatar( $atts, $content = null ) {
	return "<pre><span class='azrcrv-a-avatar'>" . do_shortcode( $content ) . '</code></pre>';
}

/**
 *
 * return avatar formatted as image
 *
 * @since 1.0.0
 */
function return_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	$user = false;

	// get user id from $id_or_email.
	if ( is_numeric( $id_or_email ) ) {
		$id   = (int) $id_or_email;
		$user = get_user_by( 'id', $id );
	} elseif ( is_object( $id_or_email ) ) {
		if ( empty( $id_or_email->user_id ) ) {
			$id   = (int) $id_or_email->user_id;
			$user = get_user_by( 'id', $id );
		} else {
			$user = get_user_by( 'id', $id_or_email->user_id );
		}
	} else {
		$user = get_user_by( 'email', $id_or_email );
	}

	// get avatar options.
	$options = get_option_with_defaults( 'azrcrv-a' );
	// check if registered user.
	if ( empty( $user ) ) {
		// if not registered do they have an email.
		if ( strlen( $id_or_email->comment_author_email ) > 0 ) {
			// if there is an email do they have a gravatar.
			if ( strlen( check_user_has_gravatar( $id_or_email->comment_author_email ) ) > 0 and $options['localonly'] == 0 ) {
				return $avatar;
			}
		}
		$user_id = 0;
	} else {
		$user_id = $user->ID;
	}

	// get avatar url.
	$avatar = get_avatar_url( $user_id );

	// format avatar.
	$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

	return $avatar;
}

/**
 *
 * Get avatar if set otherwise use default; override gravatar default if local only set in plugin options
 *
 * @since 1.0.0
 */
function get_avatar_url( $userid ) {

	// get avatar options.
	$options = get_option_with_defaults( 'azrcrv-a' );

	// get user avatar.
	$avatar = get_user_meta( $userid, 'azrcrv_a_avatar', true );

	// get default avatar.
	$avatar_default = get_option( 'avatar_default' );
	if ( empty( $avatar_default ) ) {
		$default = 'mystery';
	} else {
		$default = $avatar_default;
	}

	// if users avatar not set, use default.
	if ( strlen( $avatar ) == 0 and $options['localonly'] == 0 ) {
		// determine if host is SSL for Gravatar path.
		$host = is_ssl() ? 'https://secure.gravatar.com' : 'http://0.gravatar.com';

		// set default avatar based on Discussion Options.
		if ( $default == 'mystery' ) {
			$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s";
		} elseif ( $default == 'blank' ) {
			$default = includes_url( 'images/blank.gif' );
		} elseif ( $default == 'gravatar_default' ) {
			$default = "$host/avatar/?s";
		} elseif ( substr( $default, 0, 4 ) == 'http' ) {
			$default = $avatar_default;
		} else {
			$default = "$host/avatar/?d=$default&amp;";
		}
		// set avatar to default.
		$avatar = $default;
	} elseif ( strlen( $avatar ) == 0 ) {
		$avatar = plugins_url( '/images/customavatar.png', __FILE__ );
	}

	return $avatar;
}

/**
 * Remove the custom get_avatar hook for the default avatar list output on options-discussion.php
 *
 * @since 1.0.0
 */
function avatar_defaults( $avatar_defaults ) {
	remove_action( 'get_avatar', __NAMESPACE__ . '\\return_avatar' );
	return $avatar_defaults;
}

/**
 * Include user avatars section on profiles page
 *
 * @since 1.0.0
 *
 * @param WP_User $user User object.
 */
function edit_user_profile_avatars( $user ) {

	?>
	<h3><?php esc_html_e( 'Profile Picture', 'azrcrv-a' ); ?></h3>
	
	<p><?php esc_html_e( 'Uploading a new image will replace the current profile picture.', 'azrcrv-a' ); ?></p>
	<table class="form-table">
		
		<tr>
			<th><label for="azrcrv-a-avatar-path"><?php esc_html_e( 'Upload a new profile picture', 'azrcrv-a' ); ?></label></th>
			<td>
				<!-- Outputs the image after save -->
				<?php
				$avatar = get_avatar_url( $user->data->ID );
				?>
				<img src="<?php echo esc_url_raw( $avatar ); ?>" id="custom-default-avatar-src" style="width:212px;"><br />
				<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
				<input type="hidden" name="custom-default-avatar" id="custom-default-avatar" value="<?php echo esc_url_raw( get_the_author_meta( 'azrcrv_a_avatar', $user->data->ID ) ); ?>" class="regular-text" />
				
				<input type='button' id="azrcrv-a-upload-avatar" class="button upload" value="<?php esc_html_e( 'Upload avatar', 'azrcrv-a' ); ?>" />
				<input type='button' id="azrcrv-a-remove-avatar" class="button remove" value="<?php esc_html_e( 'Remove avatar', 'azrcrv-a' ); ?>" /><br />
				<span class="description"><?php esc_html_e( 'Upload, choose or remove your profile picture; save your profile to confirm the change.', 'azrcrv-a' ); ?></span>
			</td>
		</tr>
		
	</table>
	<?php
}

/**
 * Save additional profile fields.
 *
 * @since 1.0.0
 */
function save_user_profile_avatars( $user_id ) {

	if ( ! current_user_can( 'edit_user' ) ) {
		return false;
	}

	if ( empty( $_POST['custom-default-avatar'] ) ) {
		delete_user_meta( $user_id, 'azrcrv_a_avatar' );
	} else {
		update_user_meta( $user_id, 'azrcrv_a_avatar', esc_url_raw( wp_unslash( $_POST['custom-default-avatar'] ) ) );
	}

	return true;
}

/**
 * Check if supplied email has gravatar.
 *
 * @since 1.0.0
 */
function check_user_has_gravatar( $email ) {

	// create gravatar url to check.
	$url = 'http://www.gravatar.com/avatar/' . md5( strtolower( trim( $email ) ) ) . '?d=404';

	// get the url headers.
	$headers = @get_headers( $url );

	// if 200 is found return true as user has Gravatar.
	if ( preg_match( '|200|', $headers[0] ) ) {
		return $url;
	} else {
		return '';
	}

}
