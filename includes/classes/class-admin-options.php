<?php

namespace Aireset\Default;

use Aireset\Default\Init;
use Aireset\Default\Helpers;
// use Aireset\Default\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class to handle plugin admin panel objects and functions
 * 
 * @since 1.0.0
 * @version 3.8.5
 */
class Admin_Options extends Init {

  /**
   * Admin constructor
   *
   * @since 1.0.0
   * @version 3.8.0
   */
  public function __construct() {
    parent::__construct();

    // Adiciona o menu na raiz do WordPress
    add_action('admin_menu', array($this, 'add_admin_menu'));

    // handle for billing country admin notice
    add_action('woocommerce_checkout_init', array(__CLASS__, 'check_billing_country_field'));
    add_action('admin_notices', array(__CLASS__, 'show_billing_country_warning'));
    add_action('admin_footer', array(__CLASS__, 'dismiss_billing_country_warning_script'));

    // display notice when not has [woocommerce_checkout] shortcode
    add_action('admin_notices', array(__CLASS__, 'check_for_checkout_shortcode'));

    // display notice when not has PHP gd extension
    add_action('admin_notices', array(__CLASS__, 'missing_gd_extension_notice'));
  }

  /**
   * Function for creating admin menu in WordPress root
   * 
   * @since 1.0.0
   * @version 3.8.0
   * @return void
   */
  public function add_admin_menu() {
    add_menu_page(
      'Aireset - Geral', // Título da página
      'Aireset - Geral', // Título do menu
      'manage_options', // Capacidade necessária
      'aireset-default', // Slug da página
      array($this, 'render_settings_page'), // Função que exibe o conteúdo da página
      'dashicons-admin-generic', // Ícone do menu
      2 // Posição do menu
    );
  }

  /**
   * Plugin general setting page and save options
   * 
   * @since 1.0.0
   * @return void
   */
  public function render_settings_page() {
    include_once AIRESET_DEFAULT_PATH . 'includes/admin/settings.php';
  }

  /**
   * Check if billing country is disabled on checkout
   * 
   * @since 3.7.3
   * @return void
   */
  public static function check_billing_country_field() {
    $checkout_fields = WC()->checkout()->get_checkout_fields();
    $is_disabled = empty($checkout_fields['billing']['billing_country']) || $checkout_fields['billing']['billing_country']['required'] === false;

    update_option('billing_country_field_disabled', $is_disabled);
  }

  /**
   * Display admin notice when billing country field is disabled
   * 
   * @since 3.7.3
   * @return void
   */
  public static function show_billing_country_warning() {
    $is_disabled = get_option('billing_country_field_disabled');
    $hide_notice = get_user_meta(get_current_user_id(), 'hide_billing_country_notice', true);

    if ($is_disabled && !$hide_notice) {
      $class = 'notice notice-error is-dismissible';
      $message = esc_html__('O campo País na finalização de compras está desativado, verifique se seu gateway de pagamentos depende deste campo para não receber o erro "Informe um endereço para continuar com sua compra."', 'aireset-default');

      printf('<div id="billing-country-warning" class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }
  }

  /**
   * Send action on dismiss notice for not display
   * 
   * @since 3.7.3
   * @return void
   */
  public static function dismiss_billing_country_warning_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).on('click', '#billing-country-warning .notice-dismiss', function() {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dismiss_billing_country_warning',
                }
            });
        });
    </script>
    <?php
  }

  /**
   * Display error message on WooCommerce checkout page if shortcode is missing
   * 
   * @since 4.5.0
   * @return void
   */
  public static function check_for_checkout_shortcode() {
    if (!Helpers::has_shortcode_checkout()) {
      $class = 'notice notice-error is-dismissible';
      $message = __('O Aireset - Geral depende do shortcode [woocommerce_checkout] na página de finalização de compras para funcionar corretamente.', 'aireset-default');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }
  }

  /**
   * Display error message when PHP extension GD is missing
   * 
   * @since 4.5.0
   * @return void
   */
  public static function missing_gd_extension_notice() {
    if (!extension_loaded('gd') && Init::get_setting('enable_inter_bank_pix_api') === 'yes') {
      $class = 'notice notice-error is-dismissible';
      $message = __('A extensão GD está desativada, e é necessária para gerar o QR Code do Pix. Ative-a em sua hospedagem para habilitar esse recurso.', 'aireset-default');

      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }
  }
}

new Admin_Options();

if ( ! class_exists('Aireset\Default\Admin_Options\Admin_Options') ) {
  class_alias( 'Aireset\Default\Admin_Options', 'Aireset\Default\Admin_Options\Admin_Options' );
}