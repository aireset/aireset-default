<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main class init
 * 
 * @version 1.0.0
 * @version 1.3.0
 */
class Shipping_Management_Init extends Init{

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );

		// load shipping calculator in single product page
		if ( Init::get_setting( 'enable_shipping_calculator' ) === 'yes' ) {
			include_once AIRESET_DEFAULT_INC_PATH . 'classes/shipping/management-calculator.php';
		}
	}


	/**
	 * Load scripts in front-end
	 * 
	 * @since 1.0.0
	 * @version 1.3.0
	 * @return void
	 */
	public function enqueue_front_scripts() {
		wp_enqueue_script( 'hubgo-shipping-management-wc-front-scripts', AIRESET_DEFAULT_ASSETS . 'front/js/shipping-management-front-scripts.js', array( 'jquery' ), HUBGO_SHIPPING_MANAGEMENT_VERSION );
	}

}

new Shipping_Management_Init();