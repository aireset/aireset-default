<?php

namespace Aireset\Default;

defined( 'ABSPATH' ) || exit;

/**
 * Carregador central do plugin.
 *
 * Substitui as auto-instanciacoes no rodape dos arquivos, o antigo
 * includes/actions.php e a orquestracao que vivia no construtor de class-init.php.
 * Carrega e inicializa os modulos numa unica ordem explicita e controlada,
 * preservando exatamente a sequencia e as condicoes originais.
 *
 * @since 1.4.0
 */
final class Plugin {

	/**
	 * Garante boot unico.
	 *
	 * @var bool
	 */
	private static $booted = false;

	/**
	 * Inicializa todos os modulos do plugin.
	 *
	 * @return void
	 */
	public static function boot() {
		if ( self::$booted ) {
			return;
		}

		self::$booted = true;

		$inc = AIRESET_DEFAULT_INC_PATH;

		// 1. Funcoes auxiliares globais.
		require_once $inc . 'functions.php';

		// 2. Configuracoes base (opcoes padrao + getter de settings).
		require_once $inc . 'class-init.php';
		new Init();

		// 3. Gerenciador de CEP (AJAX).
		require_once $inc . 'classes/class-cep-manager.php';
		new CEP_Manager();

		// 4. Modulos de dominio (antigo includes/actions.php).
		self::load_domain_modules( $inc );

		// 5. Integracoes do Elementor Forms.
		require_once $inc . 'classes/class-elementor-form-input-class.php';
		new \Elementor_Forms_Input_Classes();

		require_once $inc . 'classes/class-elementor-form-input-custom-attributes.php';
		new \Elementor_Forms_Custom_Attributes();

		// 6. Campos customizados.
		require_once $inc . 'classes/class-custom-fields.php';

		// 7. Cores + calculadora de frete (gerenciamento).
		require_once $inc . 'classes/shipping/management-custom-colors.php';
		new \Shipping_Management_Custom_Colors();

		if ( 'yes' === Init::get_setting( 'aireset_default_enable_shipping_calculator' ) ) {
			require_once $inc . 'classes/shipping/management-calculator.php';
			new \Shipping_Management_Shipping_Calculator();
		}

		// 8. Integracao com o Elementor (dynamic tags) quando o Elementor esta ativo.
		if ( in_array( 'elementor/elementor.php', (array) get_option( 'active_plugins', array() ), true ) ) {
			require_once $inc . 'elementor-dynamic-tags/elementor.php';
			new Elementor\Aireset_Default_Elementor_Integration();
		}

		// 9. Infraestrutura: helpers, admin, assets, ajax e campos.
		require_once $inc . 'classes/class-helpers.php';

		require_once $inc . 'classes/class-admin-options.php';
		new Admin_Options();

		require_once $inc . 'classes/class-assets.php';
		new Assets();

		require_once $inc . 'classes/class-ajax.php';
		new Ajax();

		require_once $inc . 'classes/class-admin-fields.php';
		new Admin_Fields();
	}

	/**
	 * Carrega e instancia os modulos de dominio que viviam em includes/actions.php.
	 *
	 * @param string $inc Caminho da pasta includes/.
	 * @return void
	 */
	private static function load_domain_modules( $inc ) {
		require_once $inc . 'shipping/class-shipping-calculator.php';
		require_once $inc . 'orders/class-order-customer.php';
		require_once $inc . 'images/class-image-sizes.php';
		require_once $inc . 'shortcodes/class-whatsapp-shortcode.php';
		require_once $inc . 'frontend/class-viewport-manager.php';
		require_once $inc . 'checkout/class-guest-payment.php';
		require_once $inc . 'checkout/class-meta-checkout-handler.php';
		require_once $inc . 'cart/class-cart-message-manager.php';
		require_once $inc . 'integrations/class-yith-search-manager.php';
		require_once $inc . 'permalinks/class-permalink-manager.php';
		require_once $inc . 'orders/class-order-columns.php';
		require_once $inc . 'admin/class-admin-notices.php';

		new Shipping\ShippingCalculator();
		new Orders\OrderCustomer();
		new Orders\OrderColumns();
		new Images\ImageSizes();
		new Shortcodes\WhatsappShortcode();
		new Frontend\ViewportManager();
		new Checkout\GuestPayment();
		new Checkout\MetaCheckoutHandler();
		new Cart\CartMessageManager();
		new Integrations\YithSearchManager();
		new Permalinks\PermalinkManager();

		add_action(
			'elementor/dynamic_tags/register_tags',
			static function ( $dynamic_tags ) use ( $inc ) {
				require_once $inc . 'elementor-dynamic-tags.php';
				$dynamic_tags->register_tag( 'Aireset\Default\Elementor\Aireset_Dynamic_Text_Tag' );
				$dynamic_tags->register_tag( 'Aireset\Default\Elementor\Aireset_Dynamic_URL_Tag' );
			}
		);
	}
}
