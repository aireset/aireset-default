<?php

namespace Aireset\Default;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class for init plugin
 * 
 * @since 1.0.0
 * @version 3.8.0
 */
class Init {

	/**
	 * Construct function
	 * 
	 * @since 1.0.0
	 * @version 3.8.0
	 * @return void
	 */
	public function __construct() {
		// set default options
		add_action( 'admin_init', array( $this, 'aireset_default_set_default_options' ) );

		// Inicializa o gerenciador de CEP
		require_once AIRESET_DEFAULT_PATH . 'includes/classes/class-cep-manager.php';

		$this->initValidConfigs();
	}

	public function initValidConfigs(){

		include_once AIRESET_DEFAULT_INC_PATH . 'actions.php';
		
		// if ( Init::get_setting('status_woocommerce') === 'yes' ) {
		// 	include_once AIRESET_DEFAULT_INC_PATH . 'includes/woocommerce.php';
		// }

		// if ( Init::get_setting('aireset_default_order_pay_without_login') === 'yes' ) {
		// 	include_once AIRESET_DEFAULT_INC_PATH . 'includes/cart.php';
		// }

		// if ( Init::get_setting('aireset_default_order_pay_without_login') === 'yes' ) {
		// 	include_once AIRESET_DEFAULT_INC_PATH . 'includes/cart.php';
		// }

		include_once AIRESET_DEFAULT_INC_PATH . 'classes/class-elementor-form-input-class.php'; // Elementor Forms Input Classes
		include_once AIRESET_DEFAULT_INC_PATH . 'classes/class-elementor-form-input-custom-attributes.php'; // Elementor Forms Input Custom Attributes
		include_once AIRESET_DEFAULT_INC_PATH . 'classes/class-custom-fields.php';

		// Verifica se NÃO estamos no admin e NÃO é uma chamada AJAX
		// if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		// if ( ! is_admin() ) {
			// load shipping calculator in single product page
			include_once AIRESET_DEFAULT_INC_PATH . 'classes/shipping/management-custom-colors.php';
			if ( Init::get_setting( 'aireset_default_enable_shipping_calculator' ) === 'yes' ) {
				include_once AIRESET_DEFAULT_INC_PATH . 'classes/shipping/management-calculator.php';
				// wp_enqueue_script( 'hubgo-shipping-management-wc-front-scripts', AIRESET_DEFAULT_ASSETS . 'front/js/shipping-management-front-scripts.js', array( 'jquery' ), AIRESET_DEFAULT_VERSION );
			}
		// }

		// Maybe enable Elementor integration
		if ( in_array( 'elementor/elementor.php', get_option( 'active_plugins', array() ) ) ) {
			require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/elementor.php';
		}
	}


	/**
	 * Set default options
	 * 
	 * @since 1.0.0
	 * @version 3.8.0
	 * @return array
	 */
	public function set_default_data_options() {
		$options = [ 
			'aireset_default_fixed_viewport' => 'yes',
			'aireset_default_disable_add_to_cart_message' => 'no',
			'aireset_default_order_pay_without_login' => 'yes',
			// 'aireset_default_status_woocommerce' => 'yes',
			'aireset_default_masks' => 'yes',
			'aireset_default_intl_tel_input' => 'yes',
			'aireset_default_auto_create_or_assign_customer_to_order' => 'yes',
			'aireset_default_images' => 'no',
			'aireset_default_yith_wcas_submit_label' => 'no',
			'aireset_default_custom_orders_list_column_content' => 'yes',
			'aireset_default_disable_rest_thumbnails' => 'yes',

			'aireset_default_enable_shipping_calculator' => 'yes',
			'aireset_default_enable_auto_shipping_calculator' => 'yes',
			
			'aireset_default_primary_main_color' => '#000000',
			'aireset_default_hook_display_shipping_calculator' => 'after_cart',
			'aireset_default_text_info_before_input_shipping_calc' => 'Consultar prazo e valor da entrega',
			'aireset_default_text_button_shipping_calc' => 'Calcular',
			'aireset_default_text_header_ship' => 'Entrega',
			'aireset_default_text_header_value' => 'Valor',
			'aireset_default_note_text_bottom_shipping_calc' => '*Este resultado é apenas uma estimativa para este produto. O valor final considerado, deverá ser o total do carrinho.',
			'aireset_default_text_placeholder_input_shipping_calc' => 'Informe seu CEP',

			// 'enable_autofill_company_info' => 'no',
			// 'enable_street_number_field' => 'yes',
			// 'enable_back_to_shop_button' => 'no',
			// 'enable_skip_cart_page' => 'no',
			// 'enable_terms_is_checked_default' => 'yes',
			// 'enable_aditional_notes' => 'no',
			// 'enable_optimize_for_digital_products' => 'no',
			// 'enable_link_image_products' => 'no',
			// 'enable_fill_address' => 'yes',
			// 'enable_change_product_quantity' => 'yes',
			// 'enable_remove_product_cart' => 'yes',
			// 'enable_ddi_phone_field' => 'no',
			// 'enable_hide_coupon_code_field' => 'no',
			// 'enable_auto_apply_coupon_code' => 'no',
			// 'enable_assign_guest_orders' => 'yes',
			// 'checkout_header_type' => 'logo',
			// 'search_image_header_checkout' => '',
			// 'header_width_image_checkout' => '200',
			// 'unit_header_width_image_checkout' => 'px',
			// 'text_brand_checkout_header' => 'Checkout',
			// 'set_primary_color' => '#141D26',
			// 'set_primary_color_on_hover' => '#33404D',
			// 'set_placeholder_color' => '#33404D',
			// 'aireset_default_theme' => 'modern',
			// 'input_border_radius' => '0.375',
			// 'unit_input_border_radius' => 'rem',
			// 'h2_size' => '1.5',
			// 'h2_size_unit' => 'rem',
			// 'enable_thankyou_page_template' => 'yes',
			// 'enable_unset_wcbcf_fields_not_brazil' => 'no',
			// 'enable_manage_fields' => 'no',
			// 'get_address_api_service' => 'https://viacep.com.br/ws/{postcode}/json/',
			// 'api_auto_fill_address_param' => 'logradouro',
			// 'api_auto_fill_address_neightborhood_param' => 'bairro',
			// 'api_auto_fill_address_city_param' => 'localidade',
			// 'api_auto_fill_address_state_param' => 'uf',
			// 'logo_header_link' => get_permalink( wc_get_page_id('shop') ),
			// 'enable_field_masks' => 'yes',
			// 'enable_display_local_pickup_kangu' => 'no',
			// 'text_header_step_1' => 'Informações do cliente',
			// 'text_header_step_2' => 'Endereço de entrega',
			// 'text_header_step_3' => 'Formas de pagamento',
			// 'text_header_sidebar_right' => 'Carrinho',
			// 'text_check_step_1' => 'Contato',
			// 'text_check_step_2' => 'Entrega',
			// 'text_check_step_3' => 'Pagamento',
			// 'text_previous_step_button' => 'Voltar',
			// 'text_shipping_methods_label' => 'Formas de entrega',
			// 'set_font_family' => 'inter',
			// 'font_family' => array(
			//   'inter' => array(
			//     'font_name' => esc_html__( 'Inter', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap',
			//   ),
			//   'poppins' => array(
			//     'font_name' => esc_html__( 'Poppins', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
			//   ),
			//   'montserrat' => array(
			//     'font_name' => esc_html__( 'Montserrat', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
			//   ),
			//   'open_sans' => array(
			//     'font_name' => esc_html__( 'Open Sans', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap',
			//   ),
			//   'rubik' => array(
			//     'font_name' => esc_html__( 'Rubik', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
			//   ),
			//   'roboto' => array(
			//     'font_name' => esc_html__( 'Roboto', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
			//   ),
			//   'lato' => array(
			//     'font_name' => esc_html__( 'Lato', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
			//   ),
			//   'raleway' => array(
			//     'font_name' => esc_html__( 'Raleway', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
			//   ),
			//   'nunito' => array(
			//     'font_name' => esc_html__( 'Nunito', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
			//   ),
			//   'quicksand' => array(
			//     'font_name' => esc_html__( 'Quicksand', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
			//   ),
			//   'urbanist' => array(
			//     'font_name' => esc_html__( 'Urbanist', 'flexify-checkout-for-woocommerce' ),
			//     'font_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap',
			//   ),
			// ),
			// 'check_password_strenght' => 'yes',
			// 'email_providers_suggestion' => 'yes',
			// 'set_email_providers' => array(
			//   'gmail.com',
			//   'outlook.com',
			//   'hotmail.com',
			//   'live.com',
			//   'yahoo.com',
			//   'msn.com',
			//   'icloud.com',
			// ),
			// 'display_opened_order_review_mobile' => 'no',
			// 'text_contact_customer_review' => '{{ first_name }} {{ last_name }} <br> {{ phone }} <br> {{ email }}',
			// 'text_shipping_customer_review' => '{{ address_1 }}, {{ number }}, {{ city }} - {{ state }} (CEP: {{ postcode }})',
			// 'text_view_shop_thankyou' => 'Ver mais produtos',
			// 'enable_remove_quantity_select' => 'yes',
			// 'enable_animation_process_purchase' => 'yes',
			// 'text_animation_process_purchase_1' => 'Processando seus dados com segurança',
			// 'animation_process_purchase_file_1' => AIRESET_DEFAULT_ASSETS . 'frontend/json/document-lock.json',
			// 'text_animation_process_purchase_2' => 'Confirmando seu pagamento',
			// 'animation_process_purchase_file_2' => AIRESET_DEFAULT_ASSETS . 'frontend/json/money-safe.json',
			// 'text_animation_process_purchase_3' => 'Finalizando seu pedido',
			// 'animation_process_purchase_file_3' => AIRESET_DEFAULT_ASSETS . 'frontend/json/invoice-receipt.json',
			// 'enable_shipping_to_different_address' => 'no',
		];

		return apply_filters( 'aireset_default_set_default_options', $options );
	}


	/**
	 * Gets the items from the array and inserts them into the option if it is empty,
	 * or adds new items with default value to the option
	 * 
	 * @since 2.3.0
	 * @version 3.5.0
	 * @return void
	 */
	public function aireset_default_set_default_options() {
		$default_options = $this->set_default_data_options();
		$get_options = get_option( 'aireset_default_settings', [] );

		// if empty settings
		if ( empty( $get_options ) ) {
			update_option( 'aireset_default_settings', $default_options );
		} else {
			// iterate for each plugin settings
			foreach ( $get_options as $option => $value ) {
				// iterate for each default settings
				foreach ( $default_options as $index => $option_value ) {
					if ( ! isset( $get_options[ $index ] ) ) {
						$get_options[ $index ] = $option_value;
					}
				}
			}

			update_option( 'aireset_default_settings', $get_options );
		}
	}


	/**
	 * Checks if the option exists and returns the indicated array item
	 * 
	 * @since 1.0.0
	 * @version 2.3.0
	 * @param $key | Array key
	 * @return mixed | string or false
	 */
	public static function get_setting( $key ) {
		$default_options = get_option( 'aireset_default_settings', [] );

		// check if array key exists and return key
		if ( isset( $default_options[ $key ] ) ) {
			return $default_options[ $key ];
		}

		return false;
	}
}

new Init();

if ( ! class_exists( 'Aireset\Default\Init\Init' ) ) {
	class_alias( 'Aireset\Default\Init', 'Aireset\Default\Init\Init' );
}