<?php

namespace Aireset\Default;

/*
 * Plugin Name: Aireset - Geral
 * Plugin URI: https://github.com/aireset/aireset-default
 * Description: Cria e Padroniza diversas configurações padrões para os E-commerces e Sites Institucionais
 * Version: 1.3.3
 * Requires at least: 4.0
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.3.3
 * Author: Felipe Almeman - Aireset Agencia Web
 * Link: https://aireset.com.br
 * Text Domain: aireset-default
 * Domain Path: /languages
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Carrega o autoloader do Composer se existir.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Verifica se o plugin Brazilian Market está ativo antes de atualizar os campos extras
// if ( ! function_exists( 'is_plugin_active' ) ) {
// 	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// }

// $updater_path = plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
// require_once $updater_path;
// use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Aireset_General_Plugin {

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $version = '1.3.2';
	
    private static $instance = null; // Declare static instance property

	/**
	 * Plugin initiated
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
		$this->setup_update_checker(); // Adicione esta linha
    }

	/**
     * Configura o update checker para o plugin.
     * Esta função contém o código corrigido.
     */
    public function setup_update_checker() {
        // // Verifica se a classe PucFactory existe antes de usá-la.
        // if ( ! class_exists('\YahnisElsts\PluginUpdateChecker\v5\PucFactory') ) {
        //     return;
        // }

        // $repositoryUrl = 'https://github.com/aireset/aireset-default/';
        
        // // **CORREÇÃO 2: Usar a PucFactory correta para construir o objeto.**
        // $updateChecker = PucFactory::buildUpdateChecker(
        //     $repositoryUrl,
        //     __FILE__,
        //     'aireset-default'
        // );

        // // Opcional: Definir a branch
        // $updateChecker->setBranch('master');
		
		// $updateChecker->addFilter('remove_from_default_update_checks', '__return_false');

        // **CORREÇÃO 3: Usar o método correto para habilitar release assets.**
        // A linha abaixo é a forma correta e substitui a chamada getVcsApi() que causava o erro.
        // $updateChecker->enableReleaseAssets();

        // Opcional: Adicionar token de autenticação para evitar limites da API do GitHub
        // $updateChecker->setAuthentication('SEU_PERSONAL_ACCESS_TOKEN');
    }
	
	/**
	 * Define constants
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
		$this->define( 'AIRESET_DEFAULT_ADMIN_EMAIL', get_option('admin_email') );
		$this->define( 'AIRESET_DEFAULT_DOCS_LINK', 'https://ajuda.aireset.com.br' );
		$this->define( 'AIRESET_DEFAULT_PLUGIN_NAME', esc_html__( 'Aireset - Geral', 'aireset-default' ) );
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
			add_action( 'admin_notices', array( $this, 'aireset_default_wc_php_version_notice' ) );
			return;
		}

		// display notice if WooCommerce version is bottom 6.0
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && version_compare( WC_VERSION, '6.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'aireset_default_wc_version_notice' ) );
			return;
		}
		
		$this->define_constants();

        $this->load_textdomain();
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
        
        // Add admin menu
        // add_action('admin_menu', array( $this, 'add_admin_menu' ));

        // Enqueue scripts and styles
        // add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets_admin' ) );// preciso fazer o mesmo para frontend

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets_frontend' ) );

		// check if WooCommerce is active
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'before_woocommerce_init', array( $this, 'setup_hpos_compatibility' ) );
			// add_action( 'plugins_loaded', array( $this, 'setup_includes' ), 20 );
			// add_filter( 'plugin_action_links_' . AIRESET_DEFAULT_BASENAME, array( $this, 'hubgo_shipping_management_wc_plugin_links' ), 10, 4 );
		}

		// Hooks para a área administrativa – somente se estivermos no admin.
        // if ( is_admin() ) {
        //     // Executa a verificação do reset e adiciona o link oculto somente na página do plugin.
        //     add_action( 'admin_init', array( $this, 'handle_reset_options' ) );
        //     // add_action( 'admin_footer', array( $this, 'hidden_reset_link' ) );
        // }

        // Include additional functions
        $this->include_functions();
    }

	/**
	 * Setup WooCommerce High-Performance Order Storage (HPOS) compatibility
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
	 * Run on activation
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		self::clear_wc_template_cache();
	}


	/**
	 * Deactivate plugin
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		self::clear_wc_template_cache();
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
	 * Clear WooCommerce template cache
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function clear_wc_template_cache() {
		if ( function_exists('wc_clear_template_cache') ) {
			wc_clear_template_cache();
		}
	}


	/**
	 * Define constant if not already set
	 *
	 * @since 1.0.0
	 * @param string $name | Constant name
	 * @param string|bool $value | Constant value
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
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
        load_plugin_textdomain( 'aireset-default', false, dirname(plugin_basename(__FILE__)) . '/languages' );

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
     * Add admin menu item.
     */
    // public function add_admin_menu() {
    //     add_menu_page(
    //         'Aireset', // Título da página
    //         'Aireset', // Título do menu
    //         'manage_options',        // Capacidade necessária
    //         'aireset-default', // Slug da página
    //         array( $this, 'admin_page_content' ) // Função que gera o conteúdo
    //     );
    // }

    /**
     * Admin page content (can be customized later).
     */
    // public function admin_page_content() {
    //     echo '<h1>' . esc_html__( 'Aireset - Geral', 'aireset-default' ) . '</h1>';
    //     // Add additional content here as needed.
    // }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_assets_admin() {
        // wp_enqueue_style( 'aireset-styles', $this->get_assets_url() . 'css/style.css' );
        // wp_enqueue_script( 'aireset-scripts', $this->get_assets_url() . 'js/script.js', array('jquery'), null, true );

        // Carrega o estilo CSS do plugin
        // wp_enqueue_style(
        //     'aireset-style',
        //     $this->get_assets_url() . 'css/style.css',
        //     array(),
        //     '1.0.0'
        // );

        // // Carrega Vue.js via CDN (ou localmente se preferir)
        // wp_enqueue_script(
        //     'vue-js',
        //     'https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js',
        //     array(),
        //     '2.6.14',
        //     true
        // );

        // // Carrega o script principal do Vue
        // wp_enqueue_script(
        //     'aireset-app',
        //     $this->get_assets_url() . 'js/app.js',
        //     array('vue-js'),
        //     '1.0.0',
        //     true
        // );
    }
    public function enqueue_assets_frontend() {
		
		if ( is_plugin_active( 'checkout-mestres-wp/checkout-woocommerce-mestres-wp.php' ) ) {
			wp_enqueue_style( 'aireset-checkout-mestres-styles', AIRESET_DEFAULT_ASSETS . 'front/css/checkout-mestres-wp.css' );
		}
        // wp_enqueue_style( 'aireset-styles', $this->get_assets_url() . 'css/style.css' );
        // wp_enqueue_script( 'aireset-scripts', $this->get_assets_url() . 'js/script.js', array('jquery'), null, true );

        // Carrega o estilo CSS do plugin
        // wp_enqueue_style(
        //     'aireset-style',
        //     $this->get_assets_url() . 'css/style.css',
        //     array(),
        //     '1.0.0'
        // );

        // // Carrega Vue.js via CDN (ou localmente se preferir)
        // wp_enqueue_script(
        //     'vue-js',
        //     'https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js',
        //     array(),
        //     '2.6.14',
        //     true
        // );

        // // Carrega o script principal do Vue
        // wp_enqueue_script(
        //     'aireset-app',
        //     $this->get_assets_url() . 'js/app.js',
        //     array('vue-js'),
        //     '1.0.0',
        //     true
        // );
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
        // include_once plugin_dir_path(__FILE__) . 'includes/status.php';
        // include_once plugin_dir_path(__FILE__) . 'includes/cart.php';
        // include_once plugin_dir_path(__FILE__) . 'includes/imagens.php';
        // include_once plugin_dir_path(__FILE__) . 'includes/yith.php';
        // include_once plugin_dir_path(__FILE__) . 'includes/order-custom-column.php';
        
		$includes = apply_filters( 'aireset_default_setup_includes', array(
			'functions.php',
			'classes/class-license.php',
			'class-init.php',
			'classes/class-helpers.php',
			'classes/class-admin-options.php',
			'classes/class-assets.php',
			'classes/class-ajax.php',
			// // 'classes/class-compat-autoloader.php',
			// // 'classes/class-sidebar.php',
			// // 'classes/class-steps.php',
			// // 'classes/class-logger.php',
			// // 'classes/class-modules.php',
			// // 'classes/class-updater.php',
		));

		foreach ( $includes as $file ) {
			$file_path = AIRESET_DEFAULT_INC_PATH . $file;

			if ( file_exists( $file_path ) ) {
				include_once $file_path;
			}
		}
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

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'aireset-default-for-woocommerce' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Trapaceando?', 'aireset-default-for-woocommerce' ), '1.0.0' );
	}

	/**
	 * Notice if WooCommerce is deactivate
	 */
	public function aireset_default_wc_deactivate_notice() {
		if ( !current_user_can( 'install_plugins' ) ) { return; }

		echo '<div class="notice is-dismissible error">
				<p>' . __( 'Requer que <strong>WooCommerce</strong> esteja instalado e ativado.', 'hubgo-shipping-management-wc' ) . '</p>
			</div>';
	}

	/**
	 * WooCommerce version notice.
	 */
	public function aireset_default_wc_version_notice() {
		echo '<div class="notice is-dismissible error">
				<p>' . __( 'Requer a versão do WooCommerce 5.0 ou maior. Faça a atualização do plugin WooCommerce.', 'hubgo-shipping-management-wc' ) . '</p>
			</div>';
	}

	/**
	 * PHP version notice
	 * 
	 * @since 1.0.0
	 */
	public function aireset_default_wc_php_version_notice() {
		echo '<div class="notice is-dismissible error">
				<p>' . __( 'Requer a versão do PHP 7.4 ou maior. Contate o suporte da sua hospedagem para realizar a atualização.', 'hubgo-shipping-management-wc' ) . '</p>
			</div>';
	}

	// Função utilitária para listar releases do GitHub
	public static function listar_versoes_github() {
		$repo = 'aireset/aireset-default';
		$url = "https://api.github.com/repos/$repo/releases";
		$args = array(
			'headers' => array(
				'Accept'        => 'application/vnd.github.v3+json',
				'User-Agent'    => 'WordPress/' . get_bloginfo('version'),
			),
			'timeout' => 15,
		);

		$response = wp_remote_get($url, $args);

		if (is_wp_error($response)) {
			return 'Erro ao consultar o GitHub: ' . $response->get_error_message();
		}

		$body = wp_remote_retrieve_body($response);
		$releases = json_decode($body);

		if (empty($releases)) {
			return 'Nenhuma versão encontrada.';
		}

		$output = '<ul>';
		foreach ($releases as $release) {
			$output .= '<li>' . esc_html($release->tag_name) . ' - ' . esc_html($release->name) . '</li>';
		}
		$output .= '</ul>';

		return $output;
	}
}

// Initialize the plugin
// Aireset_General_Plugin::get_instance();

$aireset_default = new Aireset_General_Plugin();

if ( $aireset_default->initiated ) {
	register_activation_hook( __FILE__, array( $aireset_default, 'activate' ) );
	register_deactivation_hook( __FILE__, array( $aireset_default, 'deactivate' ) );
}

// if ( ! function_exists( 'adp_fs' ) ) {
//     // Create a helper function for easy SDK access.
//     function adp_fs() {
//         global $adp_fs;

//         if ( ! isset( $adp_fs ) ) {
//             // Include Freemius SDK.
//             require_once dirname(__FILE__) . '/freemius/start.php';

//             $adp_fs = fs_dynamic_init( array(
//                 'id'                  => '16791',
//                 'slug'                => 'aireset-default-pro',
//                 'type'                => 'plugin',
//                 'public_key'          => 'pk_5d033d0ef9241016259794d49fad3',
//                 'is_premium'          => true,
//                 'premium_suffix'      => 'Pro',
//                 // If your plugin is a serviceware, set this option to false.
//                 'has_premium_version' => true,
//                 'has_addons'          => false,
//                 'has_paid_plans'      => true,
//                 'trial'               => array(
//                     'days'               => 7,
//                     'is_require_payment' => false,
//                 ),
//                 'menu'                => array(
//                     'support'        => false,
//                 ),
//             ) );
//         }

//         return $adp_fs;
//     }

//     // Init Freemius.
//     adp_fs();
//     // Signal that SDK was initiated.
//     do_action( 'adp_fs_loaded' );
// }