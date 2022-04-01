<?php

add_action( 'template_redirect', 'block_country' );

function block_country() {
	$ip        = $_SERVER['REMOTE_ADDR'];
	$ip_test   = '103.250.164.246';
	$query     = 'http://ip-api.com/json/' . $ip_test;
	$trans_key = wp_json_encode( 'cached_country' . $ip_test );

	if ( false === ( $check = get_transient( $trans_key ) ) ) {

		$response = wp_remote_get( $query );

		if ( is_wp_error( $response ) ) {
			return;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body );
		$status        = $result->status;
		$country       = isset( $result->countryCode ) ? $result->countryCode : '';
		set_transient( $trans_key, $country, 12 * HOUR_IN_SECONDS );

	} else {

		$country = get_transient( $trans_key );
		$status  = 'success';
	}

	$selected_country = get_option( 'country' );
	$id               = get_option( 'page_id' );
	$current_page_id  = get_the_ID();
	$url              = get_permalink( $id );

	if ( $status && 'success' == $status ) {
		if ( in_array( $country, $selected_country ) && $id != $current_page_id && ! empty( $id ) ) {
			wp_redirect( $url );
			exit;
		}
	}
}
