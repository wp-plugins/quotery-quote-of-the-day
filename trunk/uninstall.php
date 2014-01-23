<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Plugin_Name
 * @author    Quotery <contact@quotery.com>
 * @license   GPL-2.0+
 * @link      http://www.quotery.com
 * @copyright 2014 Quotery
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
