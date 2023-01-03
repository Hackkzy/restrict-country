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
function rca_block_country_admin_menu() {

	add_menu_page(
		esc_html__( 'Restrict Country', 'restrict-country' ),
		esc_html__( 'Restrict Country', 'restrict-country' ),
		'manage_options',
		'rca-restrict-country',
		'rca_block_country_menu_callback',
		'dashicons-admin-site-alt',
		100
	);
}

add_action( 'admin_menu', 'rca_block_country_admin_menu' );

/**
 * Function for Submit Form data.
 */
function rca_submit_data() {

	// Get Country From Form.
	$country = filter_input( INPUT_POST, 'rca_country', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	// Get Page ID From Form.
	$page_id = filter_input( INPUT_POST, 'rca_page_id', FILTER_SANITIZE_NUMBER_INT );
	$save    = filter_input( INPUT_POST, 'rca_save', FILTER_SANITIZE_NUMBER_INT );

	if ( empty( $save ) ) {
		return;
	}

	// Nonce Verification.
	if ( ! isset( $_POST['rca_nonce'] )
		|| ! wp_verify_nonce( $_POST['rca_nonce'], 'rca_nonce_action' )
	) {
		echo esc_html__( 'Invalid Submission', 'restrict-country' );
		die;
	}

	// Add or Update data to database.
	update_option( 'rca_country', $country );
	update_option( 'rca_page_id', $page_id );

	// Display Admin Notice.
	add_action( 'admin_notices', 'rca_block_country_success_notice' );

}

add_action( 'admin_init', 'rca_submit_data', 10 );

/**
 * Function used to List All Country.
 *
 * @param  array $user_country_code Selected country Code from database.
 * @return array                    Countries dropdown
 */
function rca_countries_dropdown( $user_country_code = array() ) {

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
function rca_block_country_custom_scripts_loader() {

	wp_enqueue_script(
		'rca-plugin-script',
		trailingslashit( RCA_URL ) . 'assets/js/plugin.js',
		array(),
		RCA_VERSION,
		false
	);

	wp_enqueue_style(
		'rca-style',
		trailingslashit( RCA_URL ) . 'assets/css/style.min.css',
		array(),
		RCA_VERSION
	);

	wp_enqueue_script(
		'rca-custom-script',
		trailingslashit( RCA_URL ) . 'assets/js/multiselect.min.js',
		array(),
		RCA_VERSION,
		false
	);
}
add_action( 'admin_enqueue_scripts', 'rca_block_country_custom_scripts_loader' );

/**
 * Callback Function Of Custom Admin Menu.
 */
function rca_block_country_menu_callback() {

	echo sprintf(
		'<dic class="warp"><h1>%s</h1><p></p></div>',
		esc_html__( 'Restrict Countries', 'restrict-country' )
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
						<select id="country" name="rca_country[]" multiple>

							<?php
							// listing all Contries in the select box function.
							$country_code = get_option( 'rca_country' );

							// Calling countries_dropdown Function.
							echo rca_countries_dropdown( $country_code );
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
						<select name="rca_page_id" id="page_id">
							<option default value=""><?php esc_html_e( 'None', 'restrict-country' ); ?></option>

							<?php
							// Query for listing all pages in the select box loop.
							$my_wp_query  = new WP_Query();
							$all_wp_pages = $my_wp_query->query(
								array(
									'post_type'      => 'page',
									'posts_per_page' => 999,
								)
							);

							if ( ! empty( $all_wp_pages ) ) {

								$restrcted_page_id = get_option( 'rca_page_id' );

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
				<input type="hidden" name="rca_save" value="1">
				<?php wp_nonce_field( 'rca_nonce_action', 'rca_nonce' ); ?>
			</tbody>
		</table>
		<p class="submit">
			<input id="submitbtn" class="button button-primary" type="submit" />
		</p>
	</form>
	<?php
}

/**
 * Display Success Notice.
 */
function rca_block_country_success_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Seetings Saved', 'restrict-country' ); ?></p>
	</div>
	<?php
}

/**
 * Post settings Metabox.
 */
function rca_post_settings() {
	$screens = array( 'post', 'page' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'rca_post_settings',
			'Restrict Country',
			'rca_post_settings_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'rca_post_settings' );

/**
 * Outputs the content of the meta box.
 *
 * @param object $post Post.
 */
function rca_post_settings_callback( $post ) {
	wp_nonce_field( 'rca_post_setting_nonce', 'rca_nonce' );
	$post_id = $post->ID;
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th>
					<label for="rca_selected_country"> <?php esc_html_e( 'Select Country', 'restrict-country' ); ?> </label>
				</th>
				<td>
					<select id="rca_selected_country" name="rca_selected_country[]" multiple>
						<?php
						$selected_country = get_post_meta( $post_id, 'rca_selected_country', true );
						// Calling countries_dropdown Function.
						echo rca_countries_dropdown( $selected_country );
						?>
					</select>
					<p class="description" id="tagline-description"> <?php esc_html_e( 'Select country where you want to restrict your site.', 'restrict-country' ); ?> </p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Saves the custom meta input.
 *
 * @param int $post_id Post ID.
 */
function rca_save_postdata( $post_id ) {
	// Checks save status.
	$is_autosave    = wp_is_post_autosave( $post_id );
	$is_revision    = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST['rca_nonce'] ) && wp_verify_nonce( $_POST['rca_nonce'], 'rca_post_setting_nonce' ) ) ? 'true' : 'false';

	// Exits script depending on save status.
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	// Checks for input and sanitizes/saves if needed.
	if ( isset( $_POST['rca_selected_country'] ) ) {
		update_post_meta( $post_id, 'rca_selected_country', $_POST['rca_selected_country'] );
	} else {
		delete_post_meta( $post_id, 'rca_selected_country' );
	}
}
add_action( 'save_post', 'rca_save_postdata' );
