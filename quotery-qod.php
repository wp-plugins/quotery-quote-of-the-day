<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:       Quotery: Quote of the day
 * Plugin URI:        http://www.quotery.com
 * Description:       Display a "Quote of the Day" on your site. Features a beautiful design, social sharing, category selection, author photos & more.
 * Version:           1.0.7
 * Author:            Quotery
 * Author URI:        http://www.quotery.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . '/includes/class-quotery-quote.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/widget-quotery-quote.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Quotery_Quote', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quotery_Quote', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Quotery_Quote', 'get_instance' ) );


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-quotery-qod-admin.php' );
	add_action( 'plugins_loaded', array( 'Quotery_Qod_Admin', 'get_instance' ) );

}


