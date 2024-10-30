<?php
/**
 * Plugin Name:       Login with Bondhumohal Network - India Ka Social Network - Social Login Plugin for Bondhumohal Network
 * Description:       Login and Register your users using Bondhumohal Api. Add social share button on your wordpress website. Create a Free Account on https://bondhumohal.com & Create your App via https://bondhumohal.com/create-app & Manage your Apps via https://bondhumohal.com/apps
 * Version:           1.0
 * Author:            senindiaonline
 * Author URI:        https://senindiaonline.in
 * Text Domain:       bondhumohal
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
 * Plugin constants
 */
if(!defined('BONDHUMOHAL_URL'))
	define('BONDHUMOHAL_URL', plugin_dir_url( __FILE__ ));
if(!defined('BONDHUMOHAL_PATH'))
	define('BONDHUMOHAL_PATH', plugin_dir_path( __FILE__ ));

/*
 * Import the plugin classes
 */
include (BONDHUMOHAL_PATH . 'classes/bondhumohal.php');
include (BONDHUMOHAL_PATH . 'classes/bondhumohaladmin.php');

/*
 * Redirect after activation
 */
function bondhumohal_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=bondhumohal_login' ) ) );
    }
}
add_action( 'activated_plugin', 'bondhumohal_activation_redirect' );