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
		// // get AJAX call on check inline errors
		// add_action( 'wp_ajax_flexify_check_for_inline_error', array( __CLASS__, 'check_for_inline_error' ) );
	
		// // get AJAX call on check inline errors for not logged users
		// add_action( 'wp_ajax_nopriv_flexify_check_for_inline_error', array( __CLASS__, 'check_for_inline_error' ) );

		// // get AJAX call on check error on proceed step
		// add_action( 'wp_ajax_flexify_check_for_inline_errors', array( __CLASS__, 'check_for_inline_errors' ) );

		// // get AJAX call on check error on proceed step for not logged users
		// add_action( 'wp_ajax_nopriv_flexify_check_for_inline_errors', array( __CLASS__, 'check_for_inline_errors' ) );

		// // get AJAX call on login event
		// add_action( 'wp_ajax_flexify_login', array( __CLASS__, 'login' ) );

		// // get AJAX call on login event for not logged users
		// add_action( 'wp_ajax_nopriv_flexify_login', array( __CLASS__, 'login' ) );

		// get AJAX calls on change options
		add_action( 'wp_ajax_aireset_default_admin_ajax_save_options', array( $this, 'ajax_save_options_callback' ) );
	
		// // get AJAX call from add new font
		// add_action( 'wp_ajax_add_new_font_action', array( $this, 'add_new_font_action_callback' ) );
	
		// // get AJAX call for query products search
		// add_action( 'wp_ajax_get_woo_products_ajax', array( $this, 'get_woo_products_callback' ) );
	
		// // get AJAX call for query products categories
		// add_action( 'wp_ajax_get_woo_categories_ajax', array( $this, 'get_woo_categories_callback' ) );
		
		// // get AJAX call for query products categories
		// add_action( 'wp_ajax_get_woo_attributes_ajax', array( $this, 'get_woo_attributes_callback' ) );
	
		// // get AJAX call for query WP users
		// add_action( 'wp_ajax_search_users_ajax', array( $this, 'search_users_ajax_callback' ) );
	
		// // get AJAX call from add new condition
		// add_action( 'wp_ajax_add_new_checkout_condition', array( $this, 'add_new_checkout_condition_callback' ) );
	
		// // get AJAX call from exclude condition item
		// add_action( 'wp_ajax_exclude_condition_item', array( $this, 'exclude_condition_item_callback' ) );
	
		// // get AJAX call from add new email provider
		// add_action( 'wp_ajax_add_new_email_provider', array( $this, 'add_new_email_provider_callback' ) );
	
		// // get AJAX call from remove email provider item
		// add_action( 'wp_ajax_remove_email_provider', array( $this, 'remove_email_provider_callback' ) );

		// // dismiss billing country notice
		// add_action( 'wp_ajax_dismiss_billing_country_warning', array( __CLASS__, 'dismiss_billing_country_warning' ) );

		// // on deactive license process
		// add_action( 'wp_ajax_deactive_license_action', array( $this, 'deactive_license_callback' ) );

		// // clear activation cache
		// add_action( 'wp_ajax_clear_activation_cache_action', array( $this, 'clear_activation_cache_callback' ) );

		// // reset settings to default
		// add_action( 'wp_ajax_aireset_default_reset_plugin_action', array( $this, 'reset_plugin_callback' ) );

		// // check field available on create new field
		// add_action( 'wp_ajax_check_field_availability', array( $this, 'check_field_availability_callback' ) );

		// // remove option from select
		// add_action( 'wp_ajax_remove_select_option', array( $this, 'remove_select_option_callback' ) );

		// // add new select option live
		// add_action( 'wp_ajax_add_new_option_select_live', array( $this, 'add_new_option_select_live_callback' ) );

		// // set customer data on checkout session
		// add_action( 'wp_ajax_get_checkout_session_data', array( $this, 'get_checkout_session_data_callback' ) );
		// add_action( 'wp_ajax_nopriv_get_checkout_session_data', array( $this, 'get_checkout_session_data_callback' ) );

		// // set cart items data on checkout session
		// add_action( 'wp_ajax_get_product_cart_session_data', array( $this, 'get_product_cart_session_data_callback' ) );
		// add_action( 'wp_ajax_nopriv_get_product_cart_session_data', array( $this, 'get_product_cart_session_data_callback' ) );

		// // set entry time on checkout session
		// add_action( 'wp_ajax_set_checkout_entry_time', array( $this, 'set_checkout_entry_time_callback' ) );
		// add_action( 'wp_ajax_nopriv_set_checkout_entry_time', array( $this, 'set_checkout_entry_time_callback' ) );

		// // enable AJAX request for autofill company field on digit CNPJ
		// if ( Init::get_setting('enable_autofill_company_info') === 'yes' && License::is_valid() ) {
		// 	add_action( 'wp_ajax_cnpj_autofill_query', array( __CLASS__, 'cnpj_autofill_query_callback' ) );
		// 	add_action( 'wp_ajax_nopriv_cnpj_autofill_query', array( __CLASS__, 'cnpj_autofill_query_callback' ) );
		// }
	}	


	/**
	 * Check for inline errors
	 * 
	 * @since 1.0.0
	 * @version 3.9.8
	 * @return void
	 */
	public static function check_for_inline_errors() {
		// filter and sanitize array fields from frontend
		$fields = filter_input( INPUT_POST, 'fields', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );
		$messages = array();

		foreach ( $fields as $field ) {
			$field_id = isset( $field['id'] ) ? $field['id'] : '';
			$field_key = isset( $field['key'] ) ? $field['key'] : '';
			$field_args = isset( $field['args'] ) ? $field['args'] : array();
			$field_value = isset( $field['value'] ) ? $field['value'] : '';
			$field_country = isset( $field['country'] ) ? $field['country'] : '';

			$messages[$field_key] = Fields::render_inline_errors( $field_id, $field_key, $field_args, $field_value, $field_country );
		}

		$session_key = WC()->session->get('aireset_default_ship_different_address') === 'yes' ? 'shipping' : 'billing';

		$messages['fragments'] = array(
			'.aireset-default-review-customer' => Steps::render_customer_review(),
			'.aireset-default-review-customer-contact' => Steps::replace_placeholders( Init::get_setting('text_contact_customer_review'), Steps::get_review_customer_fragment() ),
			'.aireset-default-review-shipping-address' => Steps::replace_placeholders( Init::get_setting('text_shipping_customer_review'), Steps::get_review_customer_fragment(), $session_key ),
			'.aireset-default-review-shipping-method' => Helpers::get_shipping_method(),
		);

		wp_send_json_success( $messages );
		
		exit;
	}


	/**
	 * Check for inline error for the given field
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function check_for_inline_error() {
		Fields::render_inline_errors();
	}


	/**
	 * Login
	 *
	 * @since 1.0.0
	 * @throws Exception On login error.
	 */
	public static function login() {
		check_admin_referer('woocommerce-login');

		try {
			$username = filter_input( INPUT_POST, 'username' );
			$password = filter_input( INPUT_POST, 'password' );
			$rememberme = filter_input( INPUT_POST, 'rememberme' );

			$creds = array(
				'user_login' => trim( $username ),
				'user_password' => $password,
				'remember' => ! empty( $rememberme ),
			);

			$validation_error = new \WP_Error();

			/**
			 * Process login Validation Error.
			 *
			 * @since 1.0.0
			 */
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

			if ( $validation_error->get_error_code() ) {
				throw new \Exception( '<strong>' . __( 'Erro:', 'woocommerce' ) . '</strong> ' . $validation_error->get_error_message() );
			}

			if ( empty( $creds['user_login'] ) ) {
				throw new \Exception( '<strong>' . __( 'Erro:', 'woocommerce' ) . '</strong> ' . __( 'Usuário é obrigatório.', 'woocommerce' ) );
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login.

			/**
			 * Login credentials.
			 *
			 * @since 1.0.0
			 */
			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				throw new \Exception( $user->get_error_message() );
			} else {
				wp_send_json_success();
			}
		} catch ( \Exception $e ) {
			wp_send_json_error(
				array(
					'error' => $e->getMessage(),
				)
			);
		}
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
	

	/**
	 * Add new font to library on AJAX callback
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function add_new_font_action_callback() {
		if ( isset( $_POST['new_font_id'] ) ) {
			$font_id = strtolower( $_POST['new_font_id'] );

			$new_font = array(
				$font_id => array(
					'font_name' => $_POST['new_font_name'],
					'font_url' => $_POST['new_font_url'],
				),
			);

			// get settings array
			$options = get_option('aireset_default_settings', array());

			// add new theme to array settings
			if ( ! isset( $options['font_family'][$font_id] ) ) {
				$options['font_family'][$font_id] = $new_font[$font_id];
			}

			// save the updated options
			$new_font_added = update_option( 'aireset_default_settings', $options );

			// check if new theme added with successful
			if ( $new_font_added ) {
				$response = array(
					'status' => 'success',
					'reload' => true,
				);
			} else {
				$response = array(
					'status' => 'error',
					'font_exists' => true,
					'reload' => false,
				);
			}

			// send response to frontend
			wp_send_json( $response );
		}
	}


	/**
	 * Get WooCommerce products in AJAX
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function get_woo_products_callback() {
		if ( isset( $_POST['search_query'] ) ) {
			$search_query = sanitize_text_field( $_POST['search_query'] );
			
			$args = array(
				'post_type' => 'product',
				'status' => 'publish',
				'posts_per_page' => -1, // Return all results
				's' => $search_query,
			);
			
			$products = new \WP_Query( $args );
			
			if ( $products->have_posts() ) {
				while ( $products->have_posts() ) {
					$products->the_post();

					echo '<li class="list-group-item" data-product-id="'. get_the_ID() .'">' . get_the_title() . '</li>';
				}
			} else {
				echo esc_html__( 'Nenhuma produto encontrado.', 'aireset-default-for-woocommerce' );
			}
			
			wp_die(); // end ajax call
		}
	}


	/**
	 * Get WooCommerce product categories in AJAX
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function get_woo_categories_callback() {
		if ( isset( $_POST['search_query'] ) ) {
			$search_query = sanitize_text_field( $_POST['search_query'] );
			
			$args = array(
				'taxonomy' => 'product_cat',
				'hide_empty' => false,
				'name__like' => $search_query,
			);
			
			$categories = get_terms( $args );
			
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					echo '<li class="list-group-item" data-category-id="'. $category->term_id .'">'. $category->name .'</li>';
				}
			} else {
				echo esc_html__( 'Nenhuma categoria encontrada.', 'aireset-default-for-woocommerce' );
			}
			
			wp_die(); // end ajax call
		}
	}

	/**
	 * Get product attributes in AJAX
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function get_woo_attributes_callback() {
		if ( isset( $_POST['search_query'] ) ) {
			$search_query = sanitize_text_field( $_POST['search_query'] );

			// get all registered attribute taxonomies
			$attribute_taxonomies = wc_get_attribute_taxonomies();

			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
					// Use the taxonomy name instead of the 'attribute_name'
					$taxonomy_name = 'pa_' . $attribute_taxonomy->attribute_name;

					// Verify that the taxonomy name contains the search term
					if ( strpos( $taxonomy_name, $search_query ) !== false ) {
						$args = array(
							'taxonomy' => $taxonomy_name,
							'hide_empty' => false,
						);

						$attributes = get_terms( $args );

						if ( ! empty( $attributes ) ) {
							foreach ( $attributes as $attribute ) {
								echo '<li class="list-group-item" data-attribute-id="' . $attribute->term_id . '">' . $attribute->name . '</li>';
							}
						} else {
							echo esc_html__( 'Nenhum atributo encontrado.', 'aireset-default-for-woocommerce' );
						}
					}
				}
			}

			wp_die(); // end ajax call
		}
	}

	/**
	 * Search WP users in AJAX
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function search_users_ajax_callback() {
		if ( isset( $_POST['search_query'] ) ) {
			$search_query = sanitize_text_field( $_POST['search_query'] );

			// Run a query to search for users based on the search term
			$args = array(
				'search' => '*' . $search_query . '*',
				'search_columns' => array(
					'user_login',
					'user_email',
					'user_nicename',
					'display_name',
				),
				'number' => -1, // Return all results
			);

			$users = get_users( $args );

			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					echo '<li class="list-group-item" data-user-id="' . $user->ID . '">' . $user->display_name . '</li>';
				}
			} else {
				echo esc_html__( 'Nenhum usuário encontrado.', 'aireset-default-for-woocommerce' );
			}

			wp_die(); // end ajax call
		}
	}

	/**
	 * Add new condition AJAX callback
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function add_new_checkout_condition_callback() {
		if ( isset( $_POST['type_rule'] ) && $_POST['type_rule'] !== 'none' ) {
			$form_condition = array(
				'type_rule' => isset( $_POST['type_rule'] ) ? sanitize_text_field( $_POST['type_rule'] ) : null,
				'component' => isset( $_POST['component'] ) ? sanitize_text_field( $_POST['component'] ) : null,
				'component_field' => isset( $_POST['component_field'] ) ? sanitize_text_field( $_POST['component_field'] ) : null,
				'verification_condition' => isset( $_POST['verification_condition'] ) ? sanitize_text_field( $_POST['verification_condition'] ) : null,
				'verification_condition_field' => isset( $_POST['verification_condition_field'] ) ? sanitize_text_field( $_POST['verification_condition_field'] ) : null,
				'condition' => isset( $_POST['condition'] ) ? sanitize_text_field( $_POST['condition'] ) : null,
				'condition_value' => isset( $_POST['condition_value'] ) ? sanitize_text_field( $_POST['condition_value'] ) : null,
				'payment_method' => isset( $_POST['payment_method'] ) ? sanitize_text_field( $_POST['payment_method'] ) : null,
				'shipping_method' => isset( $_POST['shipping_method'] ) ? sanitize_text_field( $_POST['shipping_method'] ) : null,
				'filter_user' => isset( $_POST['filter_user'] ) ? sanitize_text_field( $_POST['filter_user'] ) : null,
				'specific_user' => isset( $_POST['filter_user'] ) ? $_POST['filter_user'] : null,
				'specific_role' => isset( $_POST['specific_role'] ) ? sanitize_text_field( $_POST['specific_role'] ) : null,
				'specific_products' => isset( $_POST['specific_products'] ) ? $_POST['specific_products'] : null,
				'specific_categories' => isset( $_POST['specific_categories'] ) ? $_POST['specific_categories'] : null,
				'specific_attributes' => isset( $_POST['specific_attributes'] ) ? $_POST['specific_attributes'] : null,
				'product_filter' => isset( $_POST['product_filter'] ) ? sanitize_text_field( $_POST['product_filter'] ) : null,
			);

			// remove null values
			$form_condition = array_filter( $form_condition, function( $value ) {
				return ! is_null( $value );
			});

			// get current conditions
			$current_conditions = get_option('aireset_default_conditions', array());

			$empty_conditions = false;

			// check if conditions is empty
			if ( empty( $current_conditions ) ) {
				$empty_conditions = true;
			}

			// merge new condition with existing
			$current_conditions[] = $form_condition;

			// Update conditions
			$update_conditions = update_option( 'aireset_default_conditions', $current_conditions );

			// check if successfully updated
			if ( $update_conditions ) {
				$get_fields = Helpers::get_checkout_fields_on_admin();
				$condition_type = array(
					'show' => esc_html__( 'Mostrar', 'aireset-default-for-woocommerce' ),
					'hide' => esc_html__( 'Ocultar', 'aireset-default-for-woocommerce' ),
				);

				$component_type_label = '';

				if ( $form_condition['component'] === 'field' ) {
					$field_id = $form_condition['component_field'];
					$component_type_label = sprintf( esc_html__( 'Campo %s', 'aireset-default-for-woocommerce' ), $get_fields['billing'][$field_id]['label'] );
				} elseif ( $form_condition['component'] === 'shipping' ) {
					$shipping_id = $form_condition['shipping_method'];
					$component_type_label = sprintf( esc_html__( 'Forma de entrega %s', 'aireset-default-for-woocommerce' ), WC()->shipping->get_shipping_methods()[$shipping_id]->method_title );
				} elseif ( $form_condition['component'] === 'payment' ) {
					$payment_id = $form_condition['payment_method'];
					$component_type_label = sprintf( esc_html__( 'Forma de pagamento %s', 'aireset-default-for-woocommerce' ), WC()->payment_gateways->payment_gateways()[$payment_id]->method_title );
				}

				$component_verification_label = '';

				if ( $form_condition['verification_condition'] === 'field' ) {
					$field_id = $form_condition['verification_condition_field'];
					$component_verification_label = sprintf( esc_html__( 'Campo %s', 'aireset-default-for-woocommerce' ), $get_fields['billing'][$field_id]['label'] );
				} elseif ( $form_condition['verification_condition'] === 'qtd_cart_total' ) {
					$component_verification_label = esc_html__( 'Quantidade total do carrinho', 'aireset-default-for-woocommerce' );
				} elseif ( $form_condition['verification_condition'] === 'cart_total_value' ) {
					$component_verification_label = esc_html__( 'Valor total do carrinho', 'aireset-default-for-woocommerce' );
				}

				$condition = array(
					'is' => esc_html__( 'É', 'aireset-default-for-woocommerce' ),
					'is_not' => esc_html__( 'Não é', 'aireset-default-for-woocommerce' ),
					'empty' => esc_html__( 'Vazio', 'aireset-default-for-woocommerce' ),
					'not_empty' => esc_html__( 'Não está vazio', 'aireset-default-for-woocommerce' ),
					'contains' => esc_html__( 'Contém', 'aireset-default-for-woocommerce' ),
					'not_contain' => esc_html__( 'Não contém', 'aireset-default-for-woocommerce' ),
					'start_with' => esc_html__( 'Começa com', 'aireset-default-for-woocommerce' ),
					'finish_with' => esc_html__( 'Termina com', 'aireset-default-for-woocommerce' ),
					'bigger_then' => esc_html__( 'Maior que', 'aireset-default-for-woocommerce' ),
					'less_than' => esc_html__( 'Menor que', 'aireset-default-for-woocommerce' ),
				);
				
				$condition_value = isset( $form_condition['condition_value'] ) ? $form_condition['condition_value'] : '';

				$response = array(
					'status' => 'success',
					'toast_header_title' => esc_html( 'Nova condição adicionada', 'aireset-default-for-woocommerce' ),
					'toast_body_title' => esc_html( 'Condição criada com sucesso!', 'aireset-default-for-woocommerce' ),
					'condition_line_1' => sprintf( esc_html__( 'Condição: %s %s', 'aireset-default-for-woocommerce' ), $condition_type[$form_condition['type_rule']], $component_type_label ),
					'condition_line_2' => sprintf( esc_html__( 'Se: %s %s %s', 'aireset-default-for-woocommerce' ), $component_verification_label, mb_strtolower( $condition[$form_condition['condition']] ), $condition_value ),
				);

				if ( $empty_conditions ) {
					$response[] = array(
						'empty_conditions' => 'yes',
					);
				}
			} else {
				$response = array(
					'status' => 'error',
					'error_message' => esc_html__( 'Ops! Não foi possível criar uma nova condição.', 'aireset-default-for-woocommerce' ),
				);
			}

			// send response
			wp_send_json( $response );
		}
	}

	/**
	 * Exclude condition item AJAX callback
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function exclude_condition_item_callback() {
		if ( isset( $_POST['condition_index'] ) ) {
			$exclude_item = sanitize_text_field( $_POST['condition_index'] );
			$get_conditions = get_option('aireset_default_conditions', array());

			if ( isset( $get_conditions[$exclude_item] ) ) {
				unset( $get_conditions[$exclude_item] );

				$update_conditions = update_option('aireset_default_conditions', $get_conditions);

				if ( $update_conditions ) {
					$response = array(
						'status' => 'success',
						'toast_header_title' => esc_html( 'Excluído com sucesso', 'aireset-default-for-woocommerce' ),
						'toast_body_title' => esc_html( 'Condição excluída com sucesso!', 'aireset-default-for-woocommerce' ),
					);
			
					if ( empty( $get_conditions ) ) {
						$response[] = array(
							'empty_conditions' => 'yes',
							'empty_conditions_message' => esc_html( 'Ainda não existem condições.', 'aireset-default-for-woocommerce' ),
						);
					}
				} else {
					$response = array(
						'status' => 'error',
						'toast_header_title' => esc_html( 'Erro ao excluir', 'aireset-default-for-woocommerce' ),
						'toast_body_title' => esc_html( 'Ops! Não foi possível excluir a condição.', 'aireset-default-for-woocommerce' ),
					);
				}
		
				// send response
				wp_send_json( $response );
			}
		}
	}

	/**
	 * Add new email provider for suggestion on checkout
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function add_new_email_provider_callback() {
		if ( isset( $_POST['new_provider'] ) ) {
			$new_provider = sanitize_text_field( $_POST['new_provider'] );
			$get_options = get_option('aireset_default_settings', array());
			$providers = $get_options['set_email_providers'];
			$providers[] = $new_provider;
			$get_options['set_email_providers'] = $providers;
			$update_providers = update_option( 'aireset_default_settings', $get_options );

			if ( $update_providers ) {
				$response = array(
					'status' => 'success',
					'new_provider' => $new_provider,
					'toast_header_title' => esc_html( 'Provedor de e-mail adicionado', 'aireset-default-for-woocommerce' ),
					'toast_body_title' => esc_html( 'Novo provedor de e-mail adicionado com sucesso!', 'aireset-default-for-woocommerce' ),
				);
			} else {
				$response = array(
					'status' => 'error',
					'toast_header_title' => esc_html( 'Erro ao adicionar', 'aireset-default-for-woocommerce' ),
					'toast_body_title' => esc_html( 'Ops! Não foi possível adicionar o novo provedor.', 'aireset-default-for-woocommerce' ),
				);
			}

			wp_send_json( $response );
		}
	}


	/**
	 * Exclude email provider item
	 * 
	 * @since 3.5.0
	 * @version 3.8.0
	 * @return void
	 */
	public function remove_email_provider_callback() {
		if ( isset( $_POST['exclude_provider'] ) ) {
			$exclude_provider = sanitize_text_field( $_POST['exclude_provider'] );
			$get_options = get_option('aireset_default_settings', array());
			$providers = $get_options['set_email_providers'];
			$search_provider = array_search( $exclude_provider, $providers );

			if ( $search_provider !== false ) {
				unset( $providers[$search_provider] );

				$get_options['set_email_providers'] = $providers;
				$update_providers = update_option( 'aireset_default_settings', $providers );

				if ( $update_providers ) {
					$response = array(
						'status' => 'success',
						'toast_header_title' => esc_html( 'Provedor de e-mail removido', 'aireset-default-for-woocommerce' ),
						'toast_body_title' => esc_html( 'Provedor de e-mail removido com sucesso!', 'aireset-default-for-woocommerce' ),
					);
				} else {
					$response = array(
						'status' => 'error',
						'toast_header_title' => esc_html( 'Erro ao remover', 'aireset-default-for-woocommerce' ),
						'toast_body_title' => esc_html( 'Ops! Não foi possível remover o provedor de e-mail.', 'aireset-default-for-woocommerce' ),
					);
				}

				wp_send_json( $response );
			}
		}
	}

	/**
	 * Update option on get AJAX call for hide notice
	 * 
	 * @since 3.7.3
	 * @version 3.8.0
	 * @return void
	 */
	public static function dismiss_billing_country_warning() {
		update_user_meta( get_current_user_id(), 'hide_billing_country_notice', true );
		wp_die();
	}


	/**
     * Deactive license on AJAX callback
     * 
     * @since 3.8.0
     * @return void
     */
    public function deactive_license_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'deactive_license_action' ) {
            $message = '';
            $deactivation = License::deactive_license( aireset_default_FILE, $message );

            if ( $deactivation ) {
                delete_option('aireset_default_license_key');
                delete_option('aireset_default_license_response_object');
                delete_option('aireset_default_temp_license_key');
				delete_option('aireset_default_alternative_license');
                delete_option('aireset_default_alternative_license_activation');
				delete_option('aireset_default_alternative_license_decrypted');
				delete_transient('aireset_default_license_status_cached');
				delete_transient('aireset_default_api_request_cache');
                delete_transient('aireset_default_api_response_cache');

                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'A licença foi desativada', 'aireset-default-for-woocommerce' ),
                    'toast_body_title' => esc_html__( 'Todos os recursos da versão Pro agora estão desativados!', 'aireset-default-for-woocommerce' ),
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'toast_header_title' => esc_html__( 'Ops! Ocorreu um erro.', 'aireset-default-for-woocommerce' ),
                    'toast_body_title' => esc_html__( 'Ocorreu um erro ao desativar sua licença.', 'aireset-default-for-woocommerce' ),
                );
            }

            wp_send_json( $response );
        }
    }


	/**
     * Clear activation cache on AJAX callback
     * 
     * @since 3.8.0
     * @return void
     */
    public function clear_activation_cache_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'clear_activation_cache_action' ) {
            delete_transient('aireset_default_api_request_cache');
            delete_transient('aireset_default_api_response_cache');
            delete_transient('aireset_default_license_status_cached');
            delete_option('aireset_default_alternative_license');
            delete_option('aireset_default_alternative_license_activation');

            $response = array(
                'status' => 'success',
                'toast_header_title' => esc_html__( 'Cache de ativação limpo', 'aireset-default-for-woocommerce' ),
                'toast_body_title' => esc_html__( 'O cache de ativação foi limpo com sucesso!', 'aireset-default-for-woocommerce' ),
            );

            wp_send_json( $response );
        }
    }


	/**
     * Reset plugin options to default on AJAX callback
     * 
     * @since 3.8.0
     * @return void
     */
    public function reset_plugin_callback() {
        if ( isset( $_POST['action'] ) && $_POST['action'] === 'aireset_default_reset_plugin_action' ) {
            $delete_option = delete_option('aireset_default_settings');

            if ( $delete_option ) {
				delete_option('aireset_default_step_fields');
				delete_option('aireset_default_conditions');
				delete_option('aireset_default_alternative_license_activation');
				delete_transient('aireset_default_api_request_cache');
				delete_transient('aireset_default_api_response_cache');
				delete_transient('aireset_default_license_status_cached');

                $response = array(
                    'status' => 'success',
                    'toast_header_title' => esc_html__( 'As opções foram redefinidas', 'aireset-default-for-woocommerce' ),
                    'toast_body_title' => esc_html__( 'As opções foram redefinidas com sucesso!', 'aireset-default-for-woocommerce' ),
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'toast_header_title' => esc_html__( 'Ops! Ocorreu um erro.', 'aireset-default-for-woocommerce' ),
                    'toast_body_title' => esc_html__( 'Ocorreu um erro ao redefinir as configurações.', 'aireset-default-for-woocommerce' ),
                );
            }

            wp_send_json( $response );
        }
    }


	/**
	 * Check availability from new checkout field
	 * 
	 * @since 3.8.0
	 * @return void
	 */
	public function check_field_availability_callback() {
		if ( isset( $_POST['field_name'] ) ) {
			$field_name = sanitize_text_field( $_POST['field_name'] );
			$current_fields = Helpers::get_array_index_checkout_fields();
	
			if ( in_array( $field_name, $current_fields ) ) {
				$response = array(
					'status' => 'success',
					'available' => false,
				);
			} else {
				$response = array(
					'status' => 'success',
					'available' => true,
				);
			}

			wp_send_json( $response );
		} else {
			$response = array(
				'status' => 'success',
				'available' => false,
			);

			wp_send_json( $response );
		}
	}


	/**
	 * Remove select option item on AJAX callback
	 * 
	 * @since 3.8.0
	 * @return void
	 */
	public function remove_select_option_callback() {
		if ( isset( $_POST['field_id'] ) && isset( $_POST['exclude_option'] ) ) {
			$field_id = sanitize_text_field( $_POST['field_id'] );
			$exclude_option = sanitize_text_field( $_POST['exclude_option'] );
			$get_fields = maybe_unserialize( get_option('aireset_default_step_fields', array()) );

			if ( isset( $get_fields[$field_id] ) && $get_fields[$field_id]['type'] === 'select' ) {
				$options = $get_fields[$field_id]['options'];
				
				foreach ( $options as $index => $option ) {
					if ( $option['value'] === $exclude_option ) {
						unset( $options[$index] );
						break;
					}
				}

				$get_fields[$field_id]['options'] = array_values( $options );
				$field_updated = update_option( 'aireset_default_step_fields', maybe_serialize( $get_fields ) );

				if ( $field_updated ) {
					$response = array(
						'status' => 'success',
						'toast_header_title' => esc_html__('Opção removida', 'aireset-default-for-woocommerce'),
						'toast_body_title' => esc_html__('A opção foi removida com sucesso!', 'aireset-default-for-woocommerce'),
					);
				} else {
					$response = array(
						'status' => 'error',
						'toast_header_title' => esc_html__('Erro ao remover', 'aireset-default-for-woocommerce'),
						'toast_body_title' => esc_html__('Ops! Não foi possível remover a opção.', 'aireset-default-for-woocommerce'),
					);
				}

				wp_send_json( $response );
			} else {
				$response = array(
					'status' => 'error',
					'toast_header_title' => esc_html__('Erro ao remover', 'aireset-default-for-woocommerce'),
					'toast_body_title' => esc_html__('Ops! O campo não existe ou não é do tipo select.', 'aireset-default-for-woocommerce'),
				);

				wp_send_json( $response );
			}
		}
	}


	/**
	 * Add new option to select field
	 * 
	 * @since 3.8.0
	 * @return void
	 */
	public function add_new_option_select_live_callback() {
		if ( isset( $_POST['option_value'] ) && isset( $_POST['option_title'] ) ) {
			$field_id = sanitize_text_field( $_POST['field_id'] );
			$option_value = sanitize_text_field( $_POST['option_value'] );
			$option_title = sanitize_text_field( $_POST['option_title'] );
			$get_fields = maybe_unserialize( get_option('aireset_default_step_fields', array()) );

			if ( isset( $get_fields[$field_id] ) && $get_fields[$field_id]['type'] === 'select' ) {
				$options = $get_fields[$field_id]['options'];

				$options[] = array(
					'value' => $option_value,
					'text' => $option_title,
				);

				$get_fields[$field_id]['options'] = $options;
				$field_updated = update_option('aireset_default_step_fields', maybe_serialize( $get_fields ));

				if ( $field_updated ) {
					$response = array(
						'status' => 'success',
						'toast_header_title' => esc_html__('Nova opção adicionada', 'aireset-default-for-woocommerce'),
						'toast_body_title' => esc_html__('A nova opção foi adicionada com sucesso!', 'aireset-default-for-woocommerce'),
					);
				} else {
					$response = array(
						'status' => 'error',
						'toast_header_title' => esc_html__('Erro ao adicionar', 'aireset-default-for-woocommerce'),
						'toast_body_title' => esc_html__('Ops! Não foi possível adicionar a nova opção.', 'aireset-default-for-woocommerce'),
					);
				}

				wp_send_json( $response );
			} else {
				$response = array(
					'status' => 'error',
					'toast_header_title' => esc_html__('Erro ao adicionar', 'aireset-default-for-woocommerce'),
					'toast_body_title' => esc_html__('Ops! O campo não existe ou não é do tipo select.', 'aireset-default-for-woocommerce'),
				);

				wp_send_json( $response );
			}
		}
	}


	/**
	 * Save billing fields data in custom session
	 * 
	 * @since 1.8.5
	 * @version 3.9.8
	 * @return void
	 */
	public function get_checkout_session_data_callback() {
		// Receive data from POST fields
		$fields_data = isset( $_POST['fields_data'] ) ? json_decode( stripslashes( $_POST['fields_data'] ), true ) : [];
		$ship_to_different_address = isset( $_POST['ship_to_different_address'] ) ? sanitize_text_field( $_POST['ship_to_different_address'] ) : '';
		$session_data = array();
	
		foreach ( $fields_data as $field ) {
			// Add field and value to array if they exist and are not empty
			if ( isset( $field['field_id'] ) && isset( $field['value'] ) ) {
				$field_id = $field['field_id'];
				$field_value = sanitize_text_field( $field['value'] );
				$session_data[$field_id] = $field_value;
			}
		}

		WC()->session->set( 'aireset_default_customer_fields', $session_data );
		WC()->session->set( 'aireset_default_ship_different_address', $ship_to_different_address );
	}


	/**
	 * Get product cart data from checkout session in AJAX callback
	 * 
	 * @since 3.5.0
	 * @version 3.9.8
	 * @return void
	 */
	public function get_product_cart_session_data_callback() {
		$cart_items = WC()->cart->get_cart();
		$items = array();
	
		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$product_id = $product->get_id();
			$quantity = $cart_item['quantity'];
			
			if ( $product->is_type('variable') ) {
				$variation_id = $cart_item['variation_id'];
				$variation_data = wc_get_product( $variation_id )->get_variation_attributes();
				$items[] = array(
					'product_name' => $product->get_title(),
					'product_id' => $product_id,
					'variation_id' => $variation_id,
					'quantity' => $quantity,
					'variation_data' => $variation_data,
				);
			} else {
				$items[] = array(
					'product_name' => $product->get_title(),
					'product_id' => $product_id,
					'quantity' => $quantity,
				);
			}
		}
	
		// Get selected payment method
		$payment_method = WC()->session->get('chosen_payment_method');
		$payment_method_label = __( 'Nenhuma forma de pagamento selecionada', 'aireset-default-for-woocommerce' );

		if ( $payment_method ) {
			$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

			if ( isset( $available_gateways[$payment_method] ) ) {
				$payment_method_label = $available_gateways[$payment_method]->get_title();
			}
		}
	
		$session_data = array(
			'items' => $items,
			'shipping_method' => array(
				'id' => WC()->session->get('chosen_shipping_methods'),
				'label' => Helpers::get_selected_shipping_method_name(),
			),
			'payment_method' => array(
				'id' => $payment_method,
				'label' => $payment_method_label,
			),
			'checkout_entry_time' => WC()->session->get('checkout_entry_time'),
		);
	
		WC()->session->set( 'aireset_default_items_cart', $session_data );
	
		$response = array(
			'status' => 'success',
			'data' => $session_data,
		);
	
		wp_send_json( $response );
	}
	

	/**
	 * Set entry time on checkout session in AJAX callback
	 * 
	 * @since 3.5.0
	 * @version 3.9.8
	 * @return void
	 */
	public function set_checkout_entry_time_callback() {
		if ( isset( $_POST['entry_time'] ) && $_POST['entry_time'] === 'yes' ) {
			$current_time = current_time('mysql');
			$entry_time_formatted = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $current_time ) );

			WC()->session->set('checkout_entry_time', $entry_time_formatted);

			$response = array(
				'status' => 'success',
				'data' => $entry_time_formatted,
			);
		} else {
			$response = array(
				'status' => 'error',
			);
		}

		wp_send_json( $response );
	}


	/**
	 * Send JSON from frontend for autofill fields
	 * 
	 * @since 1.4.5
	 * @version 3.9.8
	 * @return array
	 */
	public static function query_cnpj_data( $cnpj ) {
		$url = 'https://www.receitaws.com.br/v1/cnpj/' . $cnpj;
		$response = wp_safe_remote_get( $url );
	
		if ( is_wp_error( $response ) ) {
			return false;
		}
	
		$body = wp_remote_retrieve_body( $response );

		return json_decode( $body );
	}


	/**
	 * AJAX callback function for get CNPJ data
	 * 
	 * @since 1.4.5
	 * @version 3.9.8
	 * @return void
	 */
	public static function cnpj_autofill_query_callback() {
		$cnpj = sanitize_text_field( $_POST['cnpj'] );
		$data = self::query_cnpj_data( $cnpj );
	
		if ( $data ) {
			wp_send_json_success( $data );
		}
	}
}

new Ajax();

if ( ! class_exists('Aireset\Default\Ajax\Ajax') ) {
    class_alias( 'Aireset\Default\Ajax', 'Aireset\Default\Ajax\Ajax' );
}