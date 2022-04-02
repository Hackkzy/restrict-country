<?php
/**
 * Plugin Name: Restrict Country Access
 * Plugin URI:  https://biliplugins.com/
 * Description: Resrict WordPress site from specific Countries.
 * Version:     1.0.0
 * Author:      Bili Plugins
 * Text Domain: restrict-country
 * Author URI:  https://bhargavb.com/
 *
 * @package      Restrict_Country
 */

/**
 * Defining Constants.
 *
 * @package    Restrict_Country
 */
if ( ! defined( 'RCA_VERSION' ) ) {
	/**
	 * The version of the plugin.
	 */
	define( 'RCA_VERSION', '1.0.0' );
}

if ( ! defined( 'RCA_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory.
	 */
	define( 'RCA_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'RCA_URL' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'RCA_URL', plugin_dir_url( __FILE__ ) );
}

// Include Function Files.
require RCA_PATH . '/includes/custom-settings.php';
require RCA_PATH . '/includes/block-country.php';
require RCA_PATH . '/includes/country-list.php';
