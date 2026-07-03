<?php

namespace Aireset\Default;

/*
 * Plugin Name: Aireset — Geral
 * Plugin URI: https://aireset.com.br
 * Author: Felipe Almeman, Aireset Agencia Web
 * Author URI: https://aireset.com.br
 * Description: Cria e Padroniza diversas configurações padrões para os E-commerces e Sites Institucionais
 * Version: 1.3.11
 * Requires at least: 4.0
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.3.3
 * Text Domain: aireset-default
 * Domain Path: /languages
 * License: Proprietary
 * License URI: https://aireset.com.br/termos-de-uso
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Telemetria de sistema independente (roda antes do gate de licenca).
require_once __DIR__ . '/includes/aireset-sys.php';

// Carrega o autoloader do Composer se existir.
require_once __DIR__ . '/vendor/autoload.php';

// Configura o sistema de atualizações.
$updater_path = __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
if ( file_exists( $updater_path ) ) {
    require_once $updater_path;
}

require_once __DIR__ . '/includes/classes/class-admin-page.php';

class Aireset_General_Plugin {

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $version = '1.3.11';

	/**
	 * Single instance of the plugin.
	 *
	 * @var Aireset_General_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Plugin initiated.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $initiated = false;

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $slug = 'aireset-default';

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ), 99 );
		$this->setup_update_checker();
	}

	/**
	 * Configure the GitHub update checker for the plugin.
	 *
	 * @return void
	 */
	public function setup_update_checker() {
		// Fonte unica de atualizacao: Elite Licenser (aireset.com.br). GitHub PUC desativado.
		return;

		if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		try {
			$update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
				'https://github.com/aireset/aireset-default/',
				__FILE__,
				'aireset-default'
			);

			if ( $update_checker ) {
				$update_checker->setBranch( 'master' );
				$update_checker->getVcsApi()->enableReleaseAssets();
			}
		} catch ( \Exception $e ) {
			// Falha na configuração do atualizador não deve quebrar o carregamento do plugin.
			return;
		}
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 * @version 3.9.0
	 * @return void
	 */
	private function define_constants() {
		$this->define( 'AIRESET_DEFAULT_FILE', __FILE__ );
		$this->define( 'AIRESET_DEFAULT_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'AIRESET_DEFAULT_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'AIRESET_DEFAULT_ASSETS', AIRESET_DEFAULT_URL . 'assets/' );
		$this->define( 'AIRESET_DEFAULT_INC_PATH', AIRESET_DEFAULT_PATH . 'includes/' );
		$this->define( 'AIRESET_DEFAULT_TPL_PATH', AIRESET_DEFAULT_PATH . 'templates/' );
		$this->define( 'AIRESET_DEFAULT_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'AIRESET_DEFAULT_VERSION', self::$version );
		$this->define( 'AIRESET_DEFAULT_SLUG', self::$slug );
		$this->define( 'AIRESET_DEFAULT_ADMIN_EMAIL', get_option( 'admin_email' ) );
		$this->define( 'AIRESET_DEFAULT_DOCS_LINK', 'https://ajuda.aireset.com.br' );
		$this->define( 'AIRESET_DEFAULT_PLUGIN_NAME', 'Aireset - Geral' );
	}

	/**
	 * Check dependencies and bootstrap the plugin.
	 *
	 * @since 1.0.0
	 * @version 3.9.0
	 * @return void
	 */
	public function init() {
		// Display notice if PHP version is below 7.4.
		if ( version_compare( phpversion(), '7.4', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'aireset_default_wc_php_version_notice' ) );
			return;
		}

		// Display notice if WooCommerce version is below 6.0.
		if ( class_exists( 'WooCommerce' ) && defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '6.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'aireset_default_wc_version_notice' ) );
			return;
		}

		$this->define_constants();

		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets_frontend' ) );

		// Declare HPOS compatibility when WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );
		}

		// Include additional functions and modules.
		$this->include_functions();
	}

	/**
	 * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility.
	 *
	 * @since 1.2.5
	 * @return void
	 */
	public function setup_hpos_compatibility() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.1', '<' ) ) {
			return;
		}

		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				AIRESET_DEFAULT_FILE,
				true
			);
		}
	}

	/**
	 * Run on activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		self::clear_wc_template_cache();
	}

	/**
	 * Run on deactivation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		self::clear_wc_template_cache();
		wp_clear_scheduled_hook( 'aireset_default_verify_unlinked_orders_customer' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', AIRESET_DEFAULT_FILE ) );
	}

	/**
	 * Clear WooCommerce template cache.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function clear_wc_template_cache() {
		if ( function_exists( 'wc_clear_template_cache' ) ) {
			wc_clear_template_cache();
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Return a single instance of this class.
	 *
	 * @return Aireset_General_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'aireset-default', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Try to use the plugin's own translation, only available for pt_BR.
		$locale = apply_filters( 'plugin_locale', determine_locale(), 'aireset-default' );

		if ( 'pt_BR' === $locale ) {
			unload_textdomain( 'aireset-default' );
			load_textdomain(
				'aireset-default',
				plugin_dir_path( __FILE__ ) . '/languages/aireset-default-' . $locale . '.mo'
			);
		}
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets_frontend() {
		if ( is_plugin_active( 'checkout-mestres-wp/checkout-woocommerce-mestres-wp.php' ) ) {
			wp_enqueue_style( 'aireset-checkout-mestres-styles', AIRESET_DEFAULT_ASSETS . 'front/css/checkout-mestres-wp.css' );
		}
	}

	/**
	 * Get assets URL.
	 *
	 * @return string
	 */
	public static function get_assets_url() {
		return plugins_url( 'assets/', __FILE__ );
	}

	/**
	 * Boot the plugin modules through the central loader.
	 *
	 * @return void
	 */
	private function include_functions() {
		Plugin::boot();
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'aireset-default' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'aireset-default' ), '1.0.0' );
	}

	/**
	 * WooCommerce version notice.
	 *
	 * @return void
	 */
	public function aireset_default_wc_version_notice() {
		echo '<div class="notice is-dismissible error">
				<p>' . esc_html__( 'Requer a versão do WooCommerce 5.0 ou maior. Faça a atualização do plugin WooCommerce.', 'aireset-default' ) . '</p>
			</div>';
	}

	/**
	 * PHP version notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function aireset_default_wc_php_version_notice() {
		echo '<div class="notice is-dismissible error">
				<p>' . esc_html__( 'Requer a versão do PHP 7.4 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'aireset-default' ) . '</p>
			</div>';
	}
}

/* ── License gate ── */
if ( ! defined( 'AIRESET_DEFAULT_FILE' ) ) {
	define( 'AIRESET_DEFAULT_FILE', __FILE__ );
}
require_once __DIR__ . '/includes/classes/class-license.php';

if ( ! \Aireset\Default\License::is_valid() ) {
	add_action( 'admin_notices', function () {
		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			esc_html__( 'Aireset Geral: ative sua licença para usar o plugin.', 'aireset-default' )
		);
	} );
	return;
}
/* ── /License gate ── */

require_once __DIR__ . '/includes/classes/trait-aireset-license-guard.php';

// Initialize the plugin.
$aireset_default = new Aireset_General_Plugin();

register_activation_hook( __FILE__, array( '\Aireset\Default\Aireset_General_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\Aireset\Default\Aireset_General_Plugin', 'deactivate' ) );
