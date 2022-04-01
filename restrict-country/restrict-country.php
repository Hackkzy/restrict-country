<?php

/**
 * Plugin Name: Restrict Country
 * Plugin URI: https://biliplugins.com/
 * Description: Resrict WordPress site access by Country.
 * Version: 1.0
 * Author: Bili Plugins
 * Text Domain: restrict-country
 * Author URI: https://biliplugins.com/
 */

define( 'MY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

include plugin_dir_path( __FILE__ ) . '/includes/custom-settings.php';
include plugin_dir_path( __FILE__ ) . '/includes/block-country.php';
include plugin_dir_path( __FILE__ ) . '/includes/country-list.php';
