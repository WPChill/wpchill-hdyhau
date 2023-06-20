<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpchill.com
 * @since             1.0.0
 * @package           Wpchill_Hdyhau
 *
 * @wordpress-plugin
 * Plugin Name:       WPChill - How Did You Hear About Us
 * Plugin URI:        https://wpchill.com
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            WPChill
 * Author URI:        https://wpchill.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpchill-hdyhau
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpchill_hdyhau() {
	define( 'DLM_HDYHAU_FILE', __FILE__ );
	define( 'DLM_HDYHAU_OPTIONS', array( 'YouTube', 'Google', 'WordPress Repository', 'Other' ) );

	add_action( 'edd_checkout_form_top', 'wpchill_hdyhau_checkout_fields', 12 );
	// add_filter( 'edd_payment_meta', 'wpchill_hdyhau_save_checkout_fields');
	add_action( 'edd_insert_payment', 'wpchill_hdyhau_insert_payment', 10, 2 );
	add_action( 'wp_footer', 'wpchill_hdyhau_output_js', 99 );
	add_action( 'wp_head', 'wpchill_hdyhau_output_css' );
	add_action( 'admin_init', 'wpchill_reports_page', 14 );

}
add_action( 'plugins_loaded', 'run_wpchill_hdyhau', 99 );


function wpchill_hdyhau_checkout_fields(){

	$options = DLM_HDYHAU_OPTIONS;

	?>
	<div class="hdyhau-wrapper">
		<p>How Did You Hear About Us ?</p>
		<div class="radio">
			<?php foreach ( $options as $option ) {
				echo '<label><input type="radio" value="' . esc_attr( $option ) . '" name="hdyhau-reason">' . esc_html( $option ) . '</label>';
			} ?>
		</div>
		<div class="hdyhau-reason-other" style="display: none">
			<input type="text" name="hdyhau-other">
		</div>
	</div>
	<?php

}

function wpchill_hdyhau_save_checkout_fields( $payment_meta ) {

	if ( ! isset( $_POST['hdyhau-reason'] ) ) {
		return;
	}

	if ( 0 !== did_action('edd_pre_process_purchase') ) {
		$payment_meta['hdyhau-reason'] = sanitize_text_field( $_POST['hdyhau-reason'] );

		if ( isset( $_POST['hdyhau-other'] ) && ! empty( $_POST['hdyhau-other'] ) ) {
			$payment_meta['hdyhau-other'] = sanitize_text_field( $_POST['hdyhau-other'] );
		}
	}

	return $payment_meta;
}

function wpchill_hdyhau_insert_payment( $payment_id, $payment_data ){

	if ( ! isset( $_POST['hdyhau-reason'] ) ) {
		return;
	}

	$hdyhaureason = sanitize_text_field( $_POST['hdyhau-reason'] );
	edd_update_payment_meta( $payment_id, 'hdyhau-reason', $hdyhaureason );

	if ( isset( $_POST['hdyhau-other'] ) && ! empty( $_POST['hdyhau-other'] ) ) {
		$hdyhauother = sanitize_text_field( $_POST['hdyhau-other'] );
		edd_update_payment_meta( $payment_id, 'hdyhau-other', $hdyhauother );
	}

}

function wpchill_hdyhau_output_js(){
	if ( ! edd_is_checkout() ) { return; }
	?>
	<script type="text/javascript">
		jQuery('body').on('change', 'input[name="hdyhau-reason"]', function(){
			if ( jQuery('input[name="hdyhau-reason"]:checked').val() == 'Other' ) {
				jQuery('.hdyhau-reason-other').show();
			}else{
				jQuery('.hdyhau-reason-other').hide();
			}
		});
	</script>
	<?php
}

function wpchill_hdyhau_output_css(){
	?>

	<style type="text/css">
		.hdyhau-wrapper {
		    margin: 35px 0;
		}
		.hdyhau-wrapper p {
			font-size: 1.25rem;
		    font-weight: 700;
		    line-height: 1.75rem;
		    text-transform: capitalize;
	        margin-bottom: 1.25rem;
		}
		.hdyhau-wrapper input {
			cursor: pointer;
			display: inline-block;
    		margin-right: 5px;
		}
		.hdyhau-wrapper label + label {
			margin-left: 15px;
		}
		.hdyhau-reason-other {
		    margin-top: 10px;
		}
	</style>

	<?php
}


function wpchill_reports_page() {

	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return;
	}

    global $wpdb;

    $wpdb->edd_ordermeta = "{$wpdb->prefix}edd_ordermeta";


    if ( ! class_exists( 'WPChill_EDD_Reports' ) ) {
        require_once( 'classes/class-wpchill-edd-reports.php' );
    }

    new WPChill_EDD_Reports();

}
