<?php
/**
 * Country List Array.
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
 *  Block Country Admin Menu.
 */
function block_country_admin_menu() {

	add_menu_page(
		esc_html__( 'Block Country', 'restrict-country' ),
		esc_html__( 'Block Country', 'restrict-country' ),
		'manage_options',
		'block-country',
		'block_country_menu_callback',
		'dashicons-admin-site-alt',
		100
	);
}

add_action( 'admin_menu', 'block_country_admin_menu' );

/**
 * Function for Submit Form data.
 */
function submit_data() {

	// Get Country From Form.
	$country = filter_input( INPUT_POST, 'country', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	// Get Page ID From Form.
	$page_id = filter_input( INPUT_POST, 'page_id', FILTER_SANITIZE_NUMBER_INT );
	$save    = filter_input( INPUT_POST, 'save', FILTER_SANITIZE_NUMBER_INT );

	if ( empty( $save ) ) {
		return;
	}

	// Add or Update data to database.
	update_option( 'country', $country );
	update_option( 'page_id', $page_id );

	// Display Admin Notice.
	add_action( 'admin_notices', 'restrict_country_success_notice' );

}

add_action( 'admin_init', 'submit_data', 10 );

/**
 * Function used to List All Country.
 *
 * @param  array $user_country_code Selected country Code from database.
 * @return array                    Countries dropdown
 */
function countries_dropdown( $user_country_code = array() ) {

	$option = '';

	foreach ( $GLOBALS['countries_list'] as $value ) {

		$selected = ( ! empty( $user_country_code ) && in_array( $value['code'], $user_country_code, true ) ? 'selected' : '' );
		$option  .= sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $value['code'] ),
			esc_attr( $selected ),
			esc_html( $value['name'] )
		);
	}

	return $option;

}

/**
 * Custom CSS/Js loader.
 */
function block_country_custom_scripts_loader() {

	wp_enqueue_style(
		'rca-style',
		trailingslashit( RCA_URL ) . 'assets/css/style.css',
		array(),
		RCA_VERSION
	);

	wp_enqueue_script(
		'rca-custom-script',
		trailingslashit( RCA_URL ) . 'assets/js/multiselect.js',
		array(),
		RCA_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'block_country_custom_scripts_loader' );

/**
 * Callback Function Of Custom Admin Menu.
 */
function block_country_menu_callback() {

	echo sprintf(
		'<dic class="warp"><h1>%s</h1><p></p></div>',
		esc_html__( 'Select Country to Block Access.', 'restrict-country' )
	);

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
							// listing all Contries in the select box function.
							$country_code = get_option( 'country' );

							// Calling countries_dropdown Function.
							echo countries_dropdown( $country_code );
							?>

						</select>
						<p class="description" id="tagline-description"> <?php esc_html_e( 'Select country where you want to restrict your site. ', 'restrict-country' ); ?> </p>
					</td>
				</tr>
				<tr>
					<th>
						<label for="page_id"><?php esc_html_e( 'Select Page', 'restrict-country' ); ?></label>
					</th>
					<td>
						<select name='page_id' id="page_id">
							<option default value=""><?php esc_html_e( 'None', 'restrict-country' ); ?></option>

							<?php
							// Query for listing all pages in the select box loop.
							$my_wp_query       = new WP_Query();
							$all_wp_pages      = $my_wp_query->query(
								array(
									'post_type'      => 'page',
									'posts_per_page' => 999,
								)
							);

							if ( ! empty( $all_wp_pages ) ) {

								$restrcted_page_id = get_option( 'page_id' );

								foreach ( $all_wp_pages as $value ) {
									$post    = get_post( $value );
									$title   = $post->post_title;
									$page_id = $post->ID;

									echo sprintf(
										'<option %1$s value="%2$s">%3$s</option>',
										selected( $restrcted_page_id, $page_id ),
										esc_attr( $page_id ),
										esc_html( $title )
									);
								};
							}
							?>

						</select>
						<p class="description" id="tagline-description"> <?php esc_html_e( 'Select Page Where you want to redirect for Blocked Country.', 'restrict-country' ); ?> </p>
					</td>
				</tr>
				<input type="hidden" name="page" value="block-country">
				<input type="hidden" name="save" value="1">
			</tbody>
		</table>
		<p class="submit">
			<input id="submitbtn" class="button button-primary" type="submit" />
		</p>
	</form>
	<script>jQuery('#country').multiselect({columns: 1,placeholder: 'Select Country'});</script>
	<?php
}

/**
 * Display Success Notice.
 */
function restrict_country_success_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Seetings Saved', 'restrict-country' ); ?></p>
	</div>
	<?php
}
