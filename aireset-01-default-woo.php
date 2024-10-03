<?php

/*
 * Plugin Name: Aireset - Geral
 * Plugin URI: https://github.com/aireset/aireset-01-default-woo
 * Description: Cria e Padroniza diversas configurações padrões para os E-commerces
 * Version: 1.1.1
 * Requires at least: 4.0
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.3.3
 * Author: Felipe Almeman - Aireset Agencia Web
 * Link: https://aireset.com.br
 * Text Domain: aireset-01-default-woo
 * Domain Path: /languages
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Aireset_General_Plugin {
    private static $instance = null; // Declare static instance property

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $slug = 'aireset-01-default-woo';

    public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ), 99 );
    }

	/**
	 * Checker dependencies before activate plugin
	 * 
	 * @since 1.0.0
	 * @version 3.9.0
	 * @return void
	 */
	public function init() {
		// Display notice if PHP version is bottom 7.4
		if ( version_compare( phpversion(), '7.4', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			return;
		}

        $this->load_textdomain();
        
        // Add admin menu
        add_action('admin_menu', array( $this, 'add_admin_menu' ));

        // Enqueue scripts and styles
        // add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Include additional functions
        $this->include_functions();
    }

    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Load the plugin text domain for translations.
     */
    public function load_textdomain() {
        // Load regular translation from WordPress.
        load_plugin_textdomain( 'aireset-01-default-woo', false, dirname(plugin_basename(__FILE__)) . '/languages' );

        // Try to use the plugin's own translation, only available for pt_BR.
        $locale = apply_filters( 'plugin_locale', determine_locale(), 'aireset-01-default-woo' );

        if ( 'pt_BR' === $locale ) {
            unload_textdomain( 'aireset-01-default-woo' );
            load_textdomain(
                'aireset-01-default-woo',
                plugin_dir_path( __FILE__ ) . '/languages/aireset-01-default-woo-' . $locale . '.mo'
            );
        }
    }

    /**
     * Add admin menu item.
     */
    public function add_admin_menu() {
        add_menu_page(
            'Aireset', // Título da página
            'Aireset', // Título do menu
            'manage_options',        // Capacidade necessária
            'aireset-default-woo', // Slug da página
            array( $this, 'admin_page_content' ) // Função que gera o conteúdo
        );
    }

    /**
     * Admin page content (can be customized later).
     */
    public function admin_page_content() {
        echo '<h1>' . esc_html__( 'Aireset Settings', 'aireset-01-default-woo' ) . '</h1>';
        // Add additional content here as needed.
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_assets() {
        // wp_enqueue_style( 'aireset-styles', $this->get_assets_url() . 'css/style.css' );
        // wp_enqueue_script( 'aireset-scripts', $this->get_assets_url() . 'js/script.js', array('jquery'), null, true );
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
     * Include additional functions from other files.
     */
    private function include_functions() {
        include_once plugin_dir_path(__FILE__) . 'includes/status.php';
        include_once plugin_dir_path(__FILE__) . 'includes/cart.php';
        include_once plugin_dir_path(__FILE__) . 'includes/imagens.php';
        include_once plugin_dir_path(__FILE__) . 'includes/yith.php';
        include_once plugin_dir_path(__FILE__) . 'includes/order-custom-column.php';
    }

    /**
     * Log messages.
     *
     * @param string $message
     */
    public function registrar_log( $mensagem ) {
        $log_handler = new WC_Log_Handler_File( 'aireset_verificar_pedidos' );
        $logger = wc_get_logger();
        $logger->info( $mensagem, array( 'source' => 'aireset_verificar_pedidos' ) );
    }

    /**
     * Highlight array for debugging.
     *
     * @param array $array
     * @param string $name
     */
    public function highlight_array( $array, $name = 'var' ) {
        highlight_string( "<?php\n\$$name =\n" . var_export( $array, true ) . ";\n?>" );
    }
}

// Initialize the plugin
Aireset_General_Plugin::get_instance();
