<?php
/*
	setup
*/

/**
 * Declare the Namespace.
 *
 * @since 1.0.0
 */
namespace azurecurve\Avatars;

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 */

// add actions.
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
add_action( 'admin_post_' . PLUGIN_UNDERSCORE . '_save_options', __NAMESPACE__ . '\\save_options' );

	// add additional actions.
	add_action( 'show_user_profile', __NAMESPACE__ . '\\edit_user_profile_avatars' );
	add_action( 'edit_user_profile', __NAMESPACE__ . '\\edit_user_profile_avatars' );
	add_action( 'personal_options_update', __NAMESPACE__ . '\\save_user_profile_avatars' );
	add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save_user_profile_avatars' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\media_uploader' );

// add filters.
add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );
add_filter( 'codepotent_update_manager_image_path', __NAMESPACE__ . '\\custom_image_path' );
add_filter( 'codepotent_update_manager_image_url', __NAMESPACE__ . '\\custom_image_url' );

	// add additional filters.
	add_filter( 'avatar_defaults', __NAMESPACE__ . '\\set_default_avatar' );
	add_filter( 'get_avatar', __NAMESPACE__ . '\\return_avatar', 10, 5 );
	add_filter( 'avatar_defaults', __NAMESPACE__ . '\\avatar_defaults' );

// add shortcodes.
add_shortcode( 'avatar', __NAMESPACE__ . '\\show_avatar' );
