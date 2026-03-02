<?php

namespace Aireset\Default;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for handling AJAX events.
 */
class Ajax {

	/**
	 * Register AJAX handlers.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_ajax_aireset_default_admin_ajax_save_options', array( $this, 'ajax_save_options_callback' ) );
	}

	/**
	 * Save plugin options from admin AJAX.
	 *
	 * @return void
	 */
	public function ajax_save_options_callback() {
		if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'aireset_default_admin_ajax_save_options' ) {
			wp_send_json_error( array( 'message' => 'Acao invalida.' ) );
			return;
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'aireset_default_save_options' ) ) {
			wp_send_json_error( array( 'message' => 'Falha de validacao de seguranca.' ) );
			return;
		}

		if ( ! isset( $_POST['form_data'] ) || ! is_array( $_POST['form_data'] ) ) {
			wp_send_json_error( array( 'message' => 'Dados invalidos recebidos.' ) );
			return;
		}

		$form_data      = $_POST['form_data'];
		$options        = get_option( 'aireset_default_settings', array() );
		$company_fields = Custom_Fields::get_fields();
		$company_fields = isset( $company_fields['dados_empresa']['fields'] ) && is_array( $company_fields['dados_empresa']['fields'] )
			? $company_fields['dados_empresa']['fields']
			: array();
		$company_keys   = array_keys( $company_fields );
		$changed_company_keys = array();
		$saved          = false;

		foreach ( $form_data as $field ) {
			if ( ! isset( $field['name'] ) ) {
				continue;
			}

			$name = sanitize_key( $field['name'] );
			if ( empty( $name ) ) {
				continue;
			}

			$value = isset( $field['value'] ) ? (string) $field['value'] : '';

			// "Dados da Empresa" uses standalone options (not aireset_default_settings).
			if ( in_array( $name, $company_keys, true ) ) {
				$field_type = isset( $company_fields[ $name ]['type'] ) ? (string) $company_fields[ $name ]['type'] : 'text';
				$value      = ( 'url' === $field_type ) ? esc_url_raw( $value ) : sanitize_text_field( $value );

				$current_company_value = (string) get_option( $name, '' );
				if ( $current_company_value !== $value ) {
					update_option( $name, $value );
					$changed_company_keys[] = $name;
					$saved = true;
				}

				continue;
			}

			$value         = sanitize_text_field( $value );
			$current_value = isset( $options[ $name ] ) ? $options[ $name ] : '';
			if ( $current_value !== $value ) {
				$options[ $name ] = $value;
				$saved = true;
			}
		}

		if ( $this->has_company_address_changes( $changed_company_keys ) ) {
			$this->sync_company_address_to_woocommerce();
		}

		if ( $saved ) {
			update_option( 'aireset_default_settings', $options );

			wp_send_json(
				array(
					'status'             => 'success',
					'toast_header_title' => esc_html__( 'Configuracoes salvas', 'aireset-default-for-woocommerce' ),
					'toast_body_title'   => esc_html__( 'As configuracoes foram atualizadas!', 'aireset-default-for-woocommerce' ),
				)
			);
			return;
		}

		wp_send_json_error( array( 'message' => 'Nenhuma alteracao detectada.' ) );
	}

	/**
	 * Check if current request changed company address fields.
	 *
	 * @param array $changed_keys Changed company keys.
	 * @return bool
	 */
	private function has_company_address_changes( array $changed_keys ) {
		$address_keys = array(
			'cep',
			'endereco',
			'endereco_linha_2',
			'cidade',
			'estado',
			'pais',
		);

		return count( array_intersect( $address_keys, $changed_keys ) ) > 0;
	}

	/**
	 * Sync company address fields with WooCommerce store address settings.
	 *
	 * @return void
	 */
	private function sync_company_address_to_woocommerce() {
		$address_1 = sanitize_text_field( (string) get_option( 'endereco', '' ) );
		$address_2 = sanitize_text_field( (string) get_option( 'endereco_linha_2', '' ) );
		$city      = sanitize_text_field( (string) get_option( 'cidade', '' ) );
		$postcode  = sanitize_text_field( (string) get_option( 'cep', '' ) );

		$country = strtoupper( sanitize_text_field( (string) get_option( 'pais', 'BR' ) ) );
		$state   = strtoupper( sanitize_text_field( (string) get_option( 'estado', '' ) ) );

		$country = preg_replace( '/[^A-Z]/', '', $country );
		if ( strlen( $country ) !== 2 ) {
			$country = 'BR';
		}

		$state = preg_replace( '/[^A-Z]/', '', $state );
		if ( 'BR' === $country ) {
			$state = substr( $state, 0, 2 );
		}

		update_option( 'woocommerce_store_address', $address_1 );
		update_option( 'woocommerce_store_address_2', $address_2 );
		update_option( 'woocommerce_store_city', $city );
		update_option( 'woocommerce_store_postcode', $postcode );
		update_option( 'woocommerce_default_country', empty( $state ) ? $country : $country . ':' . $state );
	}
}

new Ajax();

if ( ! class_exists( 'Aireset\Default\Ajax\Ajax' ) ) {
	class_alias( 'Aireset\Default\Ajax', 'Aireset\Default\Ajax\Ajax' );
}
