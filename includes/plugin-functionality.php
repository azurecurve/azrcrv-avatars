<?php
/*
	tab output on settings page
*/

/**
 * Declare the Namespace.
 *
 * @since 2.0.0
 */
namespace azurecurve\Avatars;

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
 *
 * Change default gravatar.
 *
 * @since 1.0.0
 */
function set_default_avatar( $default_avatar ) {

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	// azurecurve avatar.
	$avatar                    = esc_url_raw( plugins_url( '../images/customavatar.png', __FILE__ ) );
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
	return '<pre><span class="' . PLUGIN_HYPHEN . '-avatar">' . do_shortcode( $content ) . '</code></pre>';
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
	$options = get_option_with_defaults( PLUGIN_HYPHEN );
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
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	// get user avatar.
	$avatar = get_user_meta( $userid, PLUGIN_UNDERSCORE . '_avatar', true );

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
		if ( strlen( $options['custom-default-avatar'] ) > 0 ) {
			$avatar = esc_url_raw( $options['custom-default-avatar'] );
		} else {
			$avatar = plugins_url( '../images/customavatar.png', __FILE__ );
		}
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
			<th><label for="<?php echo esc_attr( PLUGIN_HYPHEN ); ?>-avatar-path"><?php esc_html_e( 'Upload a new profile picture', 'azrcrv-a' ); ?></label></th>
			<td>
				<!-- Outputs the image after save -->
				<?php
				$avatar = get_avatar_url( $user->data->ID );
				?>
				<img src="<?php echo esc_url_raw( $avatar ); ?>" id="custom-default-avatar-src" style="width:212px;"><br />
				<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
				<input type="hidden" name="custom-default-avatar" id="custom-default-avatar" value="<?php echo esc_url_raw( get_the_author_meta( PLUGIN_UNDERSCORE . '_avatar', $user->data->ID ) ); ?>" class="regular-text" />
				
				<input type='button' id="<?php echo esc_attr( PLUGIN_HYPHEN ); ?>-upload-avatar" class="button upload" value="<?php esc_html_e( 'Upload avatar', 'azrcrv-a' ); ?>" />
				<input type='button' id="<?php echo esc_attr( PLUGIN_HYPHEN ); ?>-remove-avatar" class="button remove" value="<?php esc_html_e( 'Remove avatar', 'azrcrv-a' ); ?>" /><br />
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
	
	// phpcs:ignore.
	if ( empty( $_POST['custom-default-avatar'] ) ) {
		// phpcs:ignore.
		delete_user_meta( $user_id, PLUGIN_UNDERSCORE . '_avatar' );
	} else {
		// phpcs:ignore.
		update_user_meta( $user_id, PLUGIN_UNDERSCORE . '_avatar', esc_url_raw( wp_unslash( $_POST['custom-default-avatar'] ) ) );
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
	// phpcs:ignore.
	$headers = @get_headers( $url );

	// if 200 is found return true as user has Gravatar.
	if ( preg_match( '|200|', $headers[0] ) ) {
		return $url;
	} else {
		return '';
	}

}
