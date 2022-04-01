<?php

add_action( 'admin_menu', 'block_country_admin_menu' );

/**
 *  Block Country Admin Menu.
 */
function block_country_admin_menu() {

	add_menu_page(
		'Block Country',
		'Block Country',
		'manage_options',
		'block-country',
		'callback_menu_func',
		'dashicons-admin-users',
		100
	);
}

add_action( 'admin_init', 'submit_data', 10 );

/**
 * Function for Submit data.
 */
function submit_data() {

	$country = isset( $_POST['country'] ) ? $_POST['country'] : '';
	$id      = isset( $_POST['page_id'] ) ? $_POST['page_id'] : '';
	$save    = isset( $_POST['save'] ) ? $_POST['save'] : '';

	if ( empty( $save ) ) {
		return;
	}

	update_option( 'country', $country );
	update_option( 'page_id', $id );
	add_action( 'admin_notices', 'success_notice' );

}



/**
 * @param user_country_code $user_country_code Gets Stored country Code.
 * Function used to List All Country.
 */
function countries_dropdown( $user_country_code = '' ) {
	$option = '';
	foreach ( $GLOBALS['countries_list'] as $key => $value ) {
		$selected = ( ! empty( $user_country_code ) && in_array( $value['code'], $user_country_code ) ? 'selected' : '' );
		$option  .= ' <option value="' . $value['code'] . '"' . $selected . '>' . $value['name'] . '</option>' . "\n";
	}
	return $option;
}

/**
 * Custom CSS/Js loader.
 */
function custom_scripts_loader() {

	wp_enqueue_style(
		'style',
		trailingslashit( MY_PLUGIN_URL ) . 'assets/css/style.css',
	);

	wp_enqueue_script(
		'custom-js',
		trailingslashit( MY_PLUGIN_URL ) . 'assets/js/multiselect.js',
	);
}
add_action( 'admin_enqueue_scripts', 'custom_scripts_loader' );

/**
 * Callback Function Of Custom Admin Menu.
 */
function callback_menu_func() {
	echo '<dic class="warp"><h1>Select Country to Block Access.</h1><p></p></div>';
	?>
	<form method="post" action="" id='select-country'>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="country"> <?php esc_html_e( 'Select Country', 'restrict-country' ); ?> </label>
					</th>
					<td>
						<select id="country" name="country[]" multiple >
							<?php
							$country_code      = get_option( 'country' );
							$user_country_code = $country_code;
							echo countries_dropdown( $user_country_code );
							?>
						</select>
						<p class="description" id="tagline-description"> <?php esc_html_e( 'Select country which you want to block. ', 'stop-war' ); ?> </p>
					</td>
				</tr>
				<tr>
					<th>
						<label for="page_id"> <?php esc_html_e( 'Select Page', 'restrict-country' ); ?> </label>
					</th>
					<td>
						<select name='page_id'>
							<option default value=""> <?php esc_html_e( 'None', 'restrict-country' ); ?> </option>
							<?php
							// Query for listing all pages in the select box loop.
							$my_wp_query  = new WP_Query();
							$page_id      = get_option( 'page_id' );
							$all_wp_pages = $my_wp_query->query(
								array(
									'post_type'      => 'page',
									'posts_per_page' => -1,
								)
							);

							foreach ( $all_wp_pages as $value ) {
								$post  = get_post( $value );
								$title = $post->post_title;
								$id    = $post->ID;
								echo '<option ' . selected( $page_id, $id ) . ' value="' . $id . '">' . $title . '</option>';
							};
							?>

						</select>
						<p class="description" id="tagline-description"> <?php esc_html_e( 'Select Page Where you want to redirect Blocked Country. ', 'stop-war' ); ?> </p>
					</td>
				</tr>
				<input type="hidden" name="page" value="block-country">
				<input type="hidden" name="save" value="1">
			</tbody>
		</table>
		<p class="submit">
			<input id="submitbtn" class="button button-primary" type="submit">
		</p>
	</form>
	<script>
			jQuery('#country').multiselect({
				columns: 1,
				placeholder: 'Select Country',
			});
	</script>
	<?php
}

/**
 * Display Success Notice.
 */
function success_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Seetings Saved', 'restrict-country' ); ?></p>
	</div>
	<?php
}
