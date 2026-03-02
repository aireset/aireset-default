<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class for shipping calculator on product page.
 */
class Shipping_Management_Shipping_Calculator {

	public function __construct() {
		add_action( 'wp_ajax_aireset_ajax_postcode', array( $this, 'aireset_ajax_postcode' ) );
		add_action( 'wp_ajax_nopriv_aireset_ajax_postcode', array( $this, 'aireset_ajax_postcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$hook_display = $this->get_setting( 'aireset_default_hook_display_shipping_calculator', 'after_cart' );
		if ( 'after_cart' === $hook_display ) {
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'form_shipping_calc' ), 10 );
		} elseif ( 'before_cart' === $hook_display ) {
			add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'form_shipping_calc' ), 10 );
		} elseif ( 'meta_end' === $hook_display ) {
			add_action( 'woocommerce_product_meta_end', array( $this, 'form_shipping_calc' ), 10 );
		} else {
			add_shortcode( 'aireset_shipping_calculator', array( $this, 'create_form_shipping_calc_shortcode' ) );
		}

		$this->update_default_customer_address();
	}

	/**
	 * Enqueue front assets for calculator.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'aireset-shipping-calculator-styles', AIRESET_DEFAULT_ASSETS . 'front/css/shipping-calculator-styles.css', array(), AIRESET_DEFAULT_VERSION );
		wp_enqueue_script( 'aireset-shipping-calculator-scripts', AIRESET_DEFAULT_ASSETS . 'front/js/shipping-calculator.js', array( 'jquery' ), AIRESET_DEFAULT_VERSION, true );

		wp_localize_script(
			'aireset-shipping-calculator-scripts',
			'aireset_params',
			array(
				'ajax_url'                          => admin_url( 'admin-ajax.php' ),
				'nonce'                             => wp_create_nonce( 'aireset-shipping-calc-nonce' ),
				'without_selected_variation_message' => __( 'Por favor, escolha uma opcao antes de calcular.', 'aireset-default' ),
				'invalid_postcode_message'          => __( 'Digite um CEP valido com 8 numeros.', 'aireset-default' ),
				'auto_shipping'                     => $this->get_setting( 'aireset_default_enable_auto_shipping_calculator', 'yes' ),
				'cookie_days'                       => $this->get_cookie_days(),
			)
		);
	}

	/**
	 * Handle calculator request from front-end.
	 *
	 * @return void
	 */
	public function aireset_ajax_postcode() {
		check_ajax_referer( 'aireset-shipping-calc-nonce', 'nonce' );

		$data              = wp_unslash( $_POST );
		$shipping_response = $this->get_product_shipping_estimate( $data );

		if ( is_string( $shipping_response ) && '' !== trim( $shipping_response ) ) {
			echo '<div class="woocommerce-message woocommerce-error">' . esc_html( $shipping_response ) . '</div>';
			wp_die();
		}

		if ( ! is_array( $shipping_response ) || empty( $shipping_response ) ) {
			echo '<div class="woocommerce-message woocommerce-error">' . esc_html( $this->get_empty_message() ) . '</div>';
			wp_die();
		}

		$shipping_response = $this->sort_rates( $shipping_response );
		$shipping_response = apply_filters( 'aireset_default_shipping_calculator_rates', $shipping_response, $data );

		$header_ship  = $this->get_setting( 'aireset_default_text_header_ship', '' );
		$header_value = $this->get_setting( 'aireset_default_text_header_value', '' );
		$footer_note  = $this->get_setting( 'aireset_default_note_text_bottom_shipping_calc', '' );

		echo '<table cellspacing="0" class="aireset-table-shipping-methods"><tbody>';

		if ( '' !== trim( $header_ship ) || '' !== trim( $header_value ) ) {
			echo '<tr class="aireset-shipping-header">';
			echo '<th>' . esc_html( $header_ship ) . '</th>';
			echo '<th>' . esc_html( $header_value ) . '</th>';
			echo '</tr>';
		}

		foreach ( $shipping_response as $shipping ) {
			$label = $this->extract_rate_label( $shipping );
			$cost  = $this->extract_rate_cost( $shipping );

			echo '<tr class="aireset-shipping-method">';
			echo '<td>' . wp_kses_post( $label ) . '</td>';
			echo '<td>' . wp_kses_post( wc_price( $cost ) ) . '</td>';
			echo '</tr>';
		}

		if ( '' !== trim( $footer_note ) ) {
			echo '<tr class="aireset-shipping-bottom"><td colspan="2"><span>' . esc_html( $footer_note ) . '</span></td></tr>';
		}

		echo '</tbody></table>';
		wp_die();
	}

	/**
	 * Get shipping estimate data.
	 *
	 * @param array $request Post data.
	 * @return array|string
	 */
	public function get_product_shipping_estimate( array $request ) {
		$product_id = isset( $request['product'] ) ? absint( $request['product'] ) : 0;
		if ( empty( $product_id ) ) {
			return __( 'Produto invalido para calculo de frete.', 'aireset-default' );
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return __( 'Produto nao encontrado para calculo de frete.', 'aireset-default' );
		}

		if ( ! $product->needs_shipping() || 'no' === get_option( 'woocommerce_calc_shipping' ) ) {
			return __( 'Nao foi possivel calcular a entrega deste produto.', 'aireset-default' );
		}

		$customer = WC()->customer;
		if ( ! $customer ) {
			return __( 'Nao foi possivel carregar os dados de entrega.', 'aireset-default' );
		}

		$postcode_raw = isset( $request['postcode'] ) ? wc_clean( (string) $request['postcode'] ) : '';
		$postcode     = wc_normalize_postcode( $postcode_raw );
		$country      = $customer->get_shipping_country();

		if ( empty( $country ) ) {
			$default_location = wc_get_customer_default_location();
			$country          = isset( $default_location['country'] ) ? $default_location['country'] : WC()->countries->get_base_country();
		}

		if ( empty( $postcode ) || ! WC_Validation::is_postcode( $postcode, $country ) ) {
			return __( 'Por favor, insira um CEP valido.', 'aireset-default' );
		}

		$quantity = isset( $request['qty'] ) ? max( 1, (int) $request['qty'] ) : 1;
		$price    = (float) $product->get_price_excluding_tax();
		$tax      = (float) $product->get_price_including_tax() - $price;

		$destination = array(
			'country'   => $country,
			'state'     => $customer->get_shipping_state(),
			'postcode'  => $postcode,
			'city'      => $customer->get_shipping_city(),
			'address'   => $customer->get_shipping_address(),
			'address_2' => $customer->get_shipping_address_2(),
		);

		$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : 0;
		$product_root = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		$cart_id      = WC()->cart ? WC()->cart->generate_cart_id( $product_root, $variation_id ) : 'aireset_calc_' . $product_id;

		$package = array(
			'destination'     => $destination,
			'applied_coupons' => WC()->cart ? WC()->cart->applied_coupons : array(),
			'user'            => array( 'ID' => get_current_user_id() ),
			'contents'        => array(
				$cart_id => array(
					'product_id'         => $product_root,
					'variation_id'       => $variation_id,
					'data'               => $product,
					'quantity'           => $quantity,
					'line_total'         => $price,
					'line_tax'           => $tax,
					'line_subtotal'      => $price,
					'line_subtotal_tax'  => $tax,
					'contents_cost'      => $price * $quantity,
				),
			),
		);

		$package = apply_filters( 'aireset_default_shipping_calculator_package', $package, $request, $product );

		$zone             = WC_Shipping_Zones::get_zone_matching_package( $package );
		$active_methods   = array();
		$active_method_ids = array();

		if ( $zone ) {
			foreach ( $zone->get_shipping_methods() as $method ) {
				if ( 'yes' === $method->enabled ) {
					$active_methods[] = $method;
				}
			}
			$active_method_ids = wp_list_pluck( $active_methods, 'id' );
		}

		$rates              = array();
		$has_freeshipping   = false;
		$freeshipping_title = '';

		foreach ( $active_methods as $method ) {
			if ( 'free_shipping' !== $method->id || 'yes' !== $method->enabled ) {
				continue;
			}

			$has_coupon = false;
			if ( in_array( $method->requires, array( 'coupon', 'either', 'both' ), true ) && WC()->cart ) {
				$coupons = WC()->cart->get_coupons();
				if ( ! empty( $coupons ) ) {
					foreach ( $coupons as $coupon ) {
						if ( $coupon->is_valid() && $coupon->get_free_shipping() ) {
							$has_coupon = true;
							break;
						}
					}
				}
			}

			$has_met_min_amount = false;
			if ( in_array( $method->requires, array( 'min_amount', 'either', 'both' ), true ) ) {
				$total = (float) $price * (float) $quantity;
				if ( $total >= (float) $method->get_option( 'min_amount' ) ) {
					$has_met_min_amount = true;
				}
			}

			switch ( $method->requires ) {
				case 'min_amount':
					$has_freeshipping = $has_met_min_amount;
					break;
				case 'coupon':
					$has_freeshipping = $has_coupon;
					break;
				case 'both':
					$has_freeshipping = $has_met_min_amount && $has_coupon;
					break;
				case 'either':
					$has_freeshipping = $has_met_min_amount || $has_coupon;
					break;
				default:
					$has_freeshipping = false;
					break;
			}

			if ( $has_freeshipping ) {
				$freeshipping_title = $method->method_title;
				break;
			}
		}

		if ( $has_freeshipping ) {
			$rates[] = (object) array(
				'cost'  => 0,
				'label' => ! empty( $freeshipping_title ) ? $freeshipping_title : __( 'Frete gratis', 'aireset-default' ),
			);
		}

		$package_rates = WC_Shipping::instance()->calculate_shipping_for_package( $package );
		if ( ! empty( $package_rates['rates'] ) ) {
			foreach ( $package_rates['rates'] as $rate ) {
				$include = empty( $active_method_ids ) || in_array( $rate->method_id, $active_method_ids, true );
				if ( ! $include ) {
					continue;
				}

				$meta = $rate->get_meta_data();
				if ( isset( $meta['_delivery_forecast'] ) ) {
					$delivery_forecast = trim( (string) $meta['_delivery_forecast'] );
					if ( '' !== $delivery_forecast ) {
						$rate->set_label( $rate->get_label() . ' ' . sprintf( __( '(Entrega em %s dias)', 'aireset-default' ), $delivery_forecast ) );
					}
				}

				$rates[] = $rate;
			}
		}

		if ( ! empty( $rates ) ) {
			$customer->set_shipping_postcode( $postcode );
			$customer->set_billing_postcode( $postcode );
		}

		return $rates;
	}

	/**
	 * Ensure Woo default customer address.
	 *
	 * @return void
	 */
	public function update_default_customer_address() {
		$address = get_option( 'woocommerce_default_customer_address' );
		if ( empty( $address ) ) {
			update_option( 'woocommerce_default_customer_address', 'geolocation' );
		}
	}

	/**
	 * Render calculator HTML.
	 *
	 * @return void
	 */
	public function form_shipping_calc() {
		$info_text     = $this->get_setting( 'aireset_default_text_info_before_input_shipping_calc', '' );
		$placeholder   = $this->get_setting( 'aireset_default_text_placeholder_input_shipping_calc', 'Informe seu CEP' );
		$button_text   = $this->get_setting( 'aireset_default_text_button_shipping_calc', 'Calcular' );
		$show_help     = 'yes' === $this->get_setting( 'aireset_default_show_postcode_helper_link', 'yes' );
		$help_text     = $this->get_setting( 'aireset_default_postcode_helper_link_text', 'Nao sei meu CEP' );
		$help_url      = $this->get_setting( 'aireset_default_postcode_helper_link_url', 'https://buscacepinter.correios.com.br/app/endereco/' );

		echo '<div class="aireset-shipping-calc">';
		echo '<span class="aireset-info-shipping-calc">' . esc_html( $info_text ) . '</span>';
		echo '<div class="aireset-form-group">';
		echo '<input class="aireset-postcode" placeholder="' . esc_attr( $placeholder ) . '" name="aireset-postcode">';
		echo '<button class="aireset-shipping-calc-button" type="button"><span class="aireset-shipping-calc-button-title">' . esc_html( $button_text ) . '</span></button>';
		echo '</div>';

		if ( $show_help && ! empty( $help_url ) && ! empty( $help_text ) ) {
			echo '<a class="aireset-postcode-search" href="' . esc_url( $help_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $help_text ) . '</a>';
		}

		echo '<div class="aireset-response"></div>';
		echo '</div>';
	}

	/**
	 * Shortcode callback.
	 *
	 * @return string
	 */
	public function create_form_shipping_calc_shortcode() {
		if ( ! is_product() ) {
			return '';
		}

		ob_start();
		$this->form_shipping_calc();
		return ob_get_clean();
	}

	/**
	 * Get plugin setting with fallback.
	 *
	 * @param string $key Option key.
	 * @param string $default Fallback.
	 * @return string
	 */
	private function get_setting( $key, $default = '' ) {
		$value = Aireset\Default\Init::get_setting( $key );
		if ( false === $value || null === $value ) {
			return $default;
		}
		return (string) $value;
	}

	/**
	 * Get empty-state shipping message.
	 *
	 * @return string
	 */
	private function get_empty_message() {
		$message = $this->get_setting( 'aireset_default_empty_shipping_message', 'Nenhuma forma de entrega disponivel.' );
		return '' !== trim( $message ) ? $message : 'Nenhuma forma de entrega disponivel.';
	}

	/**
	 * Get cookie persistence days.
	 *
	 * @return int
	 */
	private function get_cookie_days() {
		$days = absint( $this->get_setting( 'aireset_default_shipping_calc_cookie_days', '30' ) );
		if ( $days < 1 ) {
			$days = 30;
		}
		return $days;
	}

	/**
	 * Sort rates based on setting.
	 *
	 * @param array $rates Shipping rates.
	 * @return array
	 */
	private function sort_rates( array $rates ) {
		$order = $this->get_setting( 'aireset_default_shipping_calc_results_order', 'default' );
		if ( count( $rates ) < 2 || ! in_array( $order, array( 'lowest', 'highest' ), true ) ) {
			return $rates;
		}

		usort(
			$rates,
			function( $a, $b ) use ( $order ) {
				$cost_a = $this->extract_rate_cost( $a );
				$cost_b = $this->extract_rate_cost( $b );

				if ( $cost_a === $cost_b ) {
					return 0;
				}

				if ( 'highest' === $order ) {
					return ( $cost_a > $cost_b ) ? -1 : 1;
				}

				return ( $cost_a < $cost_b ) ? -1 : 1;
			}
		);

		return $rates;
	}

	/**
	 * Extract cost from a shipping object/rate.
	 *
	 * @param mixed $rate Rate object.
	 * @return float
	 */
	private function extract_rate_cost( $rate ) {
		if ( is_object( $rate ) ) {
			if ( method_exists( $rate, 'get_cost' ) ) {
				return (float) $rate->get_cost();
			}

			if ( isset( $rate->cost ) ) {
				return (float) $rate->cost;
			}
		}

		return 0.0;
	}

	/**
	 * Extract label from a shipping object/rate.
	 *
	 * @param mixed $rate Rate object.
	 * @return string
	 */
	private function extract_rate_label( $rate ) {
		if ( is_object( $rate ) ) {
			if ( method_exists( $rate, 'get_label' ) ) {
				return (string) $rate->get_label();
			}

			if ( isset( $rate->label ) ) {
				return (string) $rate->label;
			}
		}

		return '';
	}
}

new Shipping_Management_Shipping_Calculator();
