<?php
/**
 * Rectrict Country Function
 *
 * @package Restrict_Country
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function Which Restrict Countries.
 */
function block_country() {

	// Get IP Address of user.
	$ip = '103.250.164.210';  // isset( $_SERVER['REMOTE_ADDR'] )

	// API Used to Locate Country based on IP address.
	$query = 'http://ip-api.com/json/' . $ip;

	// Transient Key.
	$trans_key = wp_json_encode( 'cached_country' . $ip );

	// If Transeint does not exist than this block will Execute.
	if ( false === ( $check = get_transient( $trans_key ) ) ) {

		// Fetch IP's details from API.
		$response = wp_remote_get( $query );

		if ( is_wp_error( $response ) ) {
			return;
		}
		// Retrieve Body of Remote Response.
		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body );
		$status        = $result->status;

		// Get counryCode based on User's IP.
		$country = isset( $result->countryCode ) ? $result->countryCode : '';

		// Set Transient .
		set_transient( $trans_key, $country, 12 * HOUR_IN_SECONDS );

	} 
	// If Transeint exist than this block will Execute.
	else  {
		
		$country = get_transient( $trans_key );
		$status  = 'success';

	}

	// Get Selected Country of Plugin From database.
	$selected_country = !empty( get_option( 'country' ) ) ? get_option('country') : ['none'];

	// Get Selected Page id of Plugin From database.
	$page_id = get_option( 'page_id' );

	// Current page ID.
	$current_page_id = get_the_ID();

	// Get Permalink of page_id.
	$page_url = get_permalink( $page_id );

	if ( isset( $status ) && 'success' == $status ) {
		if ( in_array( $country, $selected_country ) && $page_id != $current_page_id && ! empty( $page_id ) ) {
			wp_redirect( $page_url );
			exit;
		}
	}
}

add_action( 'template_redirect', 'block_country' );
