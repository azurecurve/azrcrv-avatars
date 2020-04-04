<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Avatars
 * Description: Allow users to upload their own avatar.
 * Version: 1.1.3
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/avatars/
 * Text Domain: avatars
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname( __FILE__).'/pluginmenu/menu.php');
register_activation_hook(__FILE__, 'azrcrv_create_plugin_menu_a');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// add actions
register_activation_hook(__FILE__, 'azrcrv_a_set_default_options');

// add actions
add_action('admin_menu', 'azrcrv_a_create_admin_menu');
add_action('admin_post_azrcrv_a_save_options', 'azrcrv_a_save_options');
add_action('admin_enqueue_scripts', 'azrcrv_a_load_css');
add_action('admin_enqueue_scripts', 'azrcrv_a_load_jquery');
add_action('show_user_profile', 'azrcrv_a_edit_user_profile_avatars');
add_action('edit_user_profile', 'azrcrv_a_edit_user_profile_avatars');
add_action('personal_options_update', 'azrcrv_a_save_user_profile_avatars');
add_action('edit_user_profile_update', 'azrcrv_a_save_user_profile_avatars');
add_action('admin_enqueue_scripts', 'media_uploader');
add_action('plugins_loaded', 'azrcrv_a_load_languages');

// add filters
add_filter('plugin_action_links', 'azrcrv_a_add_plugin_action_link', 10, 2);
add_filter( 'avatar_defaults', 'azrcrv_a_set_default_avatar' );
// get local avatar
add_filter('get_avatar', 'azrcrv_a_return_avatar', 10, 5);
		
add_filter('avatar_defaults', 'azrcrv_a_avatar_defaults');

// add shortcodes
add_shortcode('avatar', 'azrcrv_a_show_avatar');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('avatars', false, $plugin_rel_path);
}

function media_uploader() {
    global $post_type;
        if(function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        else {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
}

/**
 * Load CSS.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_load_css(){
	wp_register_style('azrcrv-a', plugins_url('assets/css/style.css', __FILE__), '', '1.0.0');
	wp_enqueue_style('azrcrv-a', plugins_url('assets/css/style.css', __FILE__), '', '1.0.0');
}

/**
 * Load JQuery.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_load_jquery($hook){
	/*if($hook != 'edit-user.php' AND $HOOK != 'profile.php') {
			return;
	}*/
	wp_enqueue_script( 'azrcrv-a', plugins_url('assets/jquery/jquery.js',__FILE__));
}

/**
 * Set default options for plugin.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_set_default_options($networkwide){
	
	$option_name = 'azrcrv-a';
	
	$new_options = array(
						'localonly' => 0,
			);
	
	// set defaults for multi-site
	if (function_exists('is_multisite') && is_multisite()){
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide){
			global $wpdb;

			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			$original_blog_id = get_current_blog_id();

			foreach ($blog_ids as $blog_id){
				switch_to_blog($blog_id);
				
				azrcrv_a_update_options($option_name, $new_options, false);
			}

			switch_to_blog($original_blog_id);
		}else{
			azrcrv_a_update_options( $option_name, $new_options, false);
		}
		if (get_site_option($option_name) === false){
			azrcrv_a_update_options($option_name, $new_options, true);
		}
	}
	//set defaults for single site
	else{
		azrcrv_a_update_options($option_name, $new_options, false);
	}
}

/**
 * Update options.
 *
 * @since 1.1.3
 *
 */
function azrcrv_a_update_options($option_name, $new_options, $is_network_site){
	if ($is_network_site == true){
		if (get_site_option($option_name) === false){
			add_site_option($option_name, $new_options);
		}else{
			update_site_option($option_name, azrcrv_a_update_default_options($new_options, get_site_option($option_name)));
		}
	}else{
		if (get_option($option_name) === false){
			add_option($option_name, $new_options);
		}else{
			update_option($option_name, azrcrv_a_update_default_options($new_options, get_option($option_name)));
		}
	}
}


/**
 * Add default options to existing options.
 *
 * @since 1.1.3
 *
 */
function azrcrv_a_update_default_options( &$default_options, $current_options ) {
    $default_options = (array) $default_options;
    $current_options = (array) $current_options;
    $updated_options = $current_options;
    foreach ($default_options as $key => &$value) {
        if (is_array( $value) && isset( $updated_options[$key ])){
            $updated_options[$key] = azrcrv_a_update_default_options($value, $updated_options[$key], true);
        } else {
            $updated_options[$key] = $value;
        }
    }
    return $updated_options;
}

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=azrcrv-a"><img src="'.plugins_url('/pluginmenu/images/Favicon-16x16.png', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'avatar').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_create_admin_menu(){
	//global $admin_page_hooks;
	
	add_submenu_page("azrcrv-plugin-menu"
						,esc_html__("Avatar Settings", "avatars")
						,esc_html__("Avatars", "avatars")
						,'manage_options'
						,'azrcrv-a'
						,'azrcrv_a_display_options');
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_display_options(){
	if (!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'avatars'));
    }
	
	// Retrieve plugin configuration options from database
	$options = get_option('azrcrv-a');
	?>
	<div id="azrcrv-a-general" class="wrap">
		<fieldset>
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<?php if(isset($_GET['settings-updated'])){ ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('Settings have been saved.', 'avatars'); ?></strong></p>
				</div>
			<?php } ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="azrcrv_a_save_options" />
				<input name="page_options" type="hidden" value="localonly" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field('azrcrv-a', 'azrcrv-a-nonce'); ?>
				<table class="form-table">
				
					<tr>
						<th scope="row" colspan="2">
							<label for="explanation">
								<?php esc_html_e('Avatars allows the admin to specify a default avatar and allows users to upload their own avatar.', 'avatars'); ?>
							</label>
						</th>
					</tr>
				
					<tr><th scope="row"><?php esc_html_e('Use local avatars only?', 'avatars'); ?></th><td>
						<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Use local avatars only?', 'avatars'); ?></span></legend>
						<label for="localonly"><input name="localonly" type="checkbox" id="localonly" value="1" <?php checked('1', $options['localonly']); ?> /></label>
						</fieldset>
					</td></tr>
				
				</table>
				<input type="submit" value="Save Changes" class="button-primary"/>
			</form>
		</fieldset>
	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_save_options(){
	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'code'));
	}
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-a', 'azrcrv-a-nonce')){
	
		// Retrieve original plugin options array
		$options = get_option('azrcrv-a');
		
		$option_name = 'localonly';
		if (isset($_POST[$option_name])){
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
		
		// Store updated options array to database
		update_option('azrcrv-a', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-a&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/**
 *
 * Change default gravatar.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_set_default_avatar ($default_avatar) {
	$avatar = plugins_url('/images/customavatar.png', __FILE__);
	$default_avatar[$avatar] = "Custom Avatar";
	return $default_avatar;
}


function azrcrv_a_display_avatar($atts, $content = null){
	return "<pre><span class='azrcrv-a-code'>".do_shortcode($content)."</code></pre>";
}

/**
 *
 * return avatar formatted as image
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_return_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	$user = false;
	
	// get user id from $id_or_email
	if (is_numeric( $id_or_email)){
		$id = (int) $id_or_email;
		$user = get_user_by( 'id' , $id );
	}elseif (is_object($id_or_email)){
		if (empty($id_or_email->user_id)){
			$id = (int) $id_or_email->user_id;
			$user = get_user_by('id', $id);
		}else{
			$user = get_user_by('id', $id_or_email->user_id);
		}
	}else{
		$user = get_user_by('email', $id_or_email);	
	}
	
	// get avatar options
	$options = get_option('azrcrv-a');
	// check if registered user
	if (empty($user)){
		// if not registered do they have an email
		if (strlen($id_or_email->comment_author_email) > 0){
			// if there is an email do they have a gravatar
			if (strlen(azrcrv_a_check_user_has_gravatar($id_or_email->comment_author_email)) > 0 AND $options['localonly'] == 0){
				return $avatar;
			}
		}
		$user_id = 0;
	}else{
		$user_id = $user->ID;
	}
	
	// get avatar url
	$avatar = azrcrv_a_get_avatar_url($user_id);
	
	// format avatar
	$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
	
	return $avatar;
}

/**
 *
 * Get avatar if set otherwise use default; override gravatar default if local only set in plugin options
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_get_avatar_url($userid){

	// get avatar options
	$options = get_option('azrcrv-a');
	
	// get user avatar
	$avatar = get_user_meta($userid, 'azrcrv_a_avatar', true);
	
	// get default avatar
	$avatar_default = get_option('avatar_default');
	if (empty($avatar_default)){
		$default = 'mystery';
	}else{
		$default = $avatar_default;
	}
	
	// if users avatar not set, use default
	if (strlen($avatar) == 0 AND $options['localonly'] == 0){
		// determine if host is SSL for Gravatar path
		$host = is_ssl() ? 'https://secure.gravatar.com' : 'http://0.gravatar.com';
		
		// set default avatar based on Discussion Options
		if ($default == 'mystery'){
			$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s";
		}elseif ($default == 'blank'){
			$default = includes_url( 'images/blank.gif' );
		}elseif ($default == 'gravatar_default'){
			$default = "$host/avatar/?s";
		}elseif (substr($default, 0, 4) == 'http'){
			$default = $avatar_default;
		}else{
			$default = "$host/avatar/?d=$default&amp;";
		}
		// set avatar to default
		$avatar = $default;
	}elseif (strlen($avatar) == 0){
		$avatar = plugins_url('/images/customavatar.png', __FILE__);
	}
	
	return $avatar;
}

/**
 * Remove the custom get_avatar hook for the default avatar list output on options-discussion.php
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_avatar_defaults($avatar_defaults){
	remove_action('get_avatar', 'azrcrv_a_return_avatar');
	return $avatar_defaults;
}

/**
 * Include user avatars section on profiles page
 *
 * @since 1.0.0
 *
 * @param WP_User $user User object.
 *
 */
function azrcrv_a_edit_user_profile_avatars($user){

    ?>
    <h3><?php esc_html_e('Profile Picture', 'avatars'); ?></h3>
	
	<p><?php esc_html_e('Uploading a new image will replace the current profile picture.', 'avatars'); ?></p>
	<table class="form-table">
		
        <tr>
            <th><label for="azrcrv-a-avatar-path"><?php esc_html_e( 'Upload a new profile picture', 'avatars' ); ?></label></th>
            <td>
                <!-- Outputs the image after save -->
				<?php
				$avatar = azrcrv_a_get_avatar_url($user->data->ID);
				?>
                <img src="<?php echo esc_url($avatar); ?>" id="azrcrv-a-avatar-image" style="width:212px;"><br />
                <!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
                <input type="hidden" name="azrcrv-a-avatar-path" id="azrcrv-a-avatar-path" value="<?php echo esc_url_raw( get_the_author_meta( 'azrcrv_a_avatar', $user->data->ID ) ); ?>" class="regular-text" />
                
				<input type='button' id="azrcrv-a-upload-avatar" class="button upload" value="<?php esc_html_e( 'Upload avatar', 'avatars' ); ?>" />
                <input type='button' id="azrcrv-a-remove-avatar" class="button remove" value="<?php esc_html_e( 'Remove avatar', 'avatars' ); ?>" /><br />
                <span class="description"><?php esc_html_e( 'Upload, choose or remove your profile picture; save your profile to confirm the change.', 'avatars' ); ?></span>
            </td>
        </tr>
		
    </table>
    <?php
}

/**
 * Save additional profile fields.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_save_user_profile_avatars($user_id) {
	
	if (!current_user_can('edit_user')){
		return false;
	}

	if (empty($_POST['azrcrv-a-avatar-path'])){
		delete_user_meta($user_id, 'azrcrv_a_avatar');
	}else{
		update_user_meta($user_id, 'azrcrv_a_avatar', $_POST['azrcrv-a-avatar-path']);
	}

    
    return true;
}

/**
 * Check if supplied email has gravatar.
 *
 * @since 1.0.0
 *
 */
function azrcrv_a_check_user_has_gravatar( $email ) {

	// create gravatar url to check
	$url = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?d=404';

	// get the url headers
	$headers = @get_headers($url);

	// if 200 is found return true as user has Gravatar
	if (preg_match('|200|', $headers[0])){
		return $url;
	}else{
		return '';
	}

} // end example_has_gravatar

?>