<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Plugin_Name
 * @author    Quotery <tech@quotery.com>
 * @license   GPL-2.0+
 * @link      http://quotery.com
 * @copyright 2014 Quotery
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}