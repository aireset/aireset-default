<?php

namespace Aireset\Default;

use Aireset\Default\Init;
use Aireset\Default\Admin_Options;
use Aireset\Default\Fields;
use Aireset\Default\Steps;
use Aireset\Default\Helpers;
use Aireset\Default\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handle AJAX events
 *
 * @since 1.0.0
 * @version 3.9.8
 */
class Ajax {

	/**
	 * Construct function
	 * 
	 * @since 1.0.0
	 * @version 3.9.8
	 * @return void
	 */
	public function __construct() {
		// get AJAX calls on change options
		add_action( 'wp_ajax_aireset_default_admin_ajax_save_options', array( $this, 'ajax_save_options_callback' ) );
	}


	/**
	 * Save options in AJAX
	 * 
	 * @since 1.0.0
	 * @version 3.9.7
	 * @return void
	 */
	// public function ajax_save_options_callback() {
	// 	if ( isset( $_POST['action'] ) && $_POST['action'] === 'aireset_default_admin_ajax_save_options' ) {
	// 		// Convert serialized data into an array
	// 		parse_str( $_POST['form_data'], $form_data );

	// 		$options = get_option('aireset_default_settings');
	// 		$options['enable_aireset_default'] = isset( $form_data['enable_aireset_default'] ) ? 'yes' : 'no';
	// 		$options['aireset_default_disable_add_to_cart_message'] = isset( $form_data['aireset_default_disable_add_to_cart_message'] ) && License::is_valid() ? 'yes' : 'no';
	// 		$options['aireset_default_order_pay_without_login'] = isset( $form_data['aireset_default_order_pay_without_login'] ) ? 'yes' : 'no';
	// 		$options['aireset_default_status_woocommerce'] = isset( $form_data['aireset_default_status_woocommerce'] ) ? 'yes' : 'no';
	// 		$options['aireset_default_auto_create_or_assign_customer_to_order'] = isset( $form_data['aireset_default_auto_create_or_assign_customer_to_order'] ) && License::is_valid() ? 'yes' : 'no';
	// 		$options['aireset_default_images'] = isset( $form_data['aireset_default_images'] ) && License::is_valid() ? 'yes' : 'no';
	// 		$options['aireset_default_yith_wcas_submit_label'] = isset( $form_data['aireset_default_yith_wcas_submit_label'] ) && License::is_valid() ? 'yes' : 'no';
	// 		$options['aireset_default_custom_orders_list_column_content'] = isset( $form_data['aireset_default_custom_orders_list_column_content'] ) && License::is_valid() ? 'yes' : 'no';

	// 		$options['aireset_default_enable_shipping_calculator'] = isset( $form_data['aireset_default_enable_shipping_calculator'] ) ? 'yes' : 'no';
	// 		$options['aireset_default_enable_auto_shipping_calculator'] = isset( $form_data['aireset_default_enable_auto_shipping_calculator'] ) ? 'yes' : 'no';

	// 		// Merge the form data with the default options
	// 		$updated_options = wp_parse_args( $form_data, $options );

	// 		// Save the updated options
	// 		$saved_options = update_option( 'aireset_default_settings', $updated_options );

	// 		if ( $saved_options ) {
	// 			$response = array(
	// 				'status' => 'success',
	// 				'toast_header_title' => esc_html__( 'Aireset - Salvo com sucesso', 'aireset-default-for-woocommerce' ),
	// 				'toast_body_title' => esc_html__( 'As configurações foram atualizadas!', 'aireset-default-for-woocommerce' ),
	// 				'options' => $updated_options,
	// 			);

	// 			wp_send_json( $response ); // Send JSON response
	// 		}
	// 	}
	// }
	public function ajax_save_options_callback() {
		if (isset($_POST['action']) && $_POST['action'] === 'aireset_default_admin_ajax_save_options') {
			if (!isset($_POST['form_data']) || !is_array($_POST['form_data'])) {
				wp_send_json_error(['message' => 'Dados inválidos recebidos.']);
				return;
			}
	
			$form_data = $_POST['form_data'];
			$saved_options = false;
	
			// Atualiza apenas as opções recebidas
			foreach ($form_data as $key => $value) {
				$current_value = get_option($key, '');
				if ($current_value !== $value) {
					update_option($key, sanitize_text_field($value));
					$saved_options = true;
				}
			}
	
			if ($saved_options) {
				$response = [
					'status' => 'success',
					'toast_header_title' => esc_html__('Configurações salvas', 'aireset-default-for-woocommerce'),
					'toast_body_title' => esc_html__('As configurações foram atualizadas!', 'aireset-default-for-woocommerce'),
				];
				wp_send_json($response);
			} else {
				wp_send_json_error(['message' => 'Nenhuma alteração detectada.']);
			}
		}
	}
	
}

new Ajax();

if ( ! class_exists('Aireset\Default\Ajax\Ajax') ) {
    class_alias( 'Aireset\Default\Ajax', 'Aireset\Default\Ajax\Ajax' );
}