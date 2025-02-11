<?php

namespace Aireset\Default;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for init plugin
 * 
 * @since 1.0.0
 * @version 3.8.0
 * @package MeuMouse.com
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

    // set default checkout fields options
    add_action( 'admin_init', array( $this, 'set_checkout_fields_steps_options' ) );
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
      'get_address_api_service' => 'https://viacep.com.br/ws/{postcode}/json/',
      'api_auto_fill_address_param' => 'logradouro',
      'api_auto_fill_address_neightborhood_param' => 'bairro',
      'api_auto_fill_address_city_param' => 'localidade',
      'api_auto_fill_address_state_param' => 'uf',

      'enable_status' => 'yes',
      'enable_cart' => 'no',
      'enable_imagens' => 'yes',
      'enable_yith' => 'no',
      'enable_order-custom-column' => 'yes',
      'enable_woocommerce' => 'yes',
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
    $get_options = get_option('aireset_default_settings', array());

    // if empty settings
    if ( empty( $get_options ) ) {
        update_option( 'aireset_default_settings', $default_options );
    } else {
        // iterate for each plugin settings
        foreach ( $get_options as $option => $value ) {
          // iterate for each default settings
          foreach ( $default_options as $index => $option_value ) {
            if ( ! isset( $get_options[$index] ) ) {
              $get_options[$index] = $option_value;
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
    $default_options = get_option('aireset_default_settings', array());

    // check if array key exists and return key
    if ( isset( $default_options[$key] ) ) {
        return $default_options[$key];
    }

    return false;
  }


  /**
   * Set default options checkout fields
   * 
   * @since 3.0.0
   * @version 3.1.0
   * @return void
   */
  public function set_checkout_fields_steps_options() {
    $get_fields = self::get_wc_native_checkout_fields();
    $get_field_options = get_option('aireset_default_step_fields', array());
    $get_field_options = maybe_unserialize( $get_field_options );

    // create options if array is empty
    if ( empty( $get_field_options ) ) {
        $fields = array();

        foreach ( $get_fields as $key => $value ) {
          $fields[$key] = $value;
        }

        update_option('aireset_default_step_fields', maybe_serialize( $fields ) );
    } else {
      foreach ( $get_fields as $key => $value ) {
        if ( ! isset( $get_field_options[$key] ) ) {
            $get_field_options[$key] = $value;
        }
      }

      update_option( 'aireset_default_step_fields', maybe_serialize( $get_field_options ) );
    }

    /**
     * Add integration with Brazilian Market on WooCommerce plugin
     * 
     * @since 1.0.0
     */
    if ( class_exists('Extra_Checkout_Fields_For_Brazil') && ! isset( $get_field_options['billing_cpf'] ) ) {
      $wcbcf_fields = self::get_wcbcf_fields();
      $get_field_options = maybe_unserialize( $get_field_options );

      // Add Brazilian Market on WooCommerce fields to existing options
      $get_field_options = array_merge( $get_field_options, $wcbcf_fields );
      update_option('aireset_default_step_fields', maybe_serialize( $get_field_options ));
    }
  }

  
  /**
   * Get checkout step fields
   * 
   * @since 3.0.0
   * @return array
   */
  public static function get_wc_native_checkout_fields() {
    $checkout_fields = array(
      'billing_email' => array(
        'id' => 'billing_email',
        'type' => 'email',
        'label' => esc_html__( 'Endereço de e-mail', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '1',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '1',
      ),
      'billing_first_name' => array(
          'id' => 'billing_first_name',
          'type' => 'text',
          'label' => esc_html__( 'Nome', 'aireset-default' ),
          'position' => 'left',
          'classes' => '',
          'label_classes' => '',
          'required' => 'yes',
          'priority' => '2',
          'source' => 'native',
          'enabled' => 'yes',
          'step' => '1',
      ),
      'billing_last_name' => array(
        'id' => 'billing_last_name',
        'type' => 'text',
        'label' => esc_html__( 'Sobrenome', 'aireset-default' ),
        'position' => 'right',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '3',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '1',
      ),
      'billing_phone' => array(
        'id' => 'billing_phone',
        'type' => 'tel',
        'label' => esc_html__( 'Telefone', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '(00) 00000-0000',
        'required' => 'yes',
        'priority' => '4',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '1',
      ),
      'billing_company' => array(
        'id' => 'billing_company',
        'type' => 'text',
        'label' => esc_html__( 'Empresa', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '5',
        'source' => 'native',
        'enabled' => 'no',
        'step' => '1',
      ),
      'billing_country' => array(
        'id' => 'billing_country',
        'type' => 'select',
        'label' => esc_html__( 'País', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '14',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '2',
      ),
      'billing_postcode' => array(
        'id' => 'billing_postcode',
        'type' => 'tel',
        'label' => esc_html__( 'CEP', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '00000-000',
        'required' => 'yes',
        'priority' => '15',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '2',
      ),
      'billing_address_1' => array(
        'id' => 'billing_address_1',
        'type' => 'text',
        'label' => esc_html__( 'Endereço', 'aireset-default' ),
        'position' => 'left',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '16',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '2',
      ),
      'billing_address_2' => array(
        'id' => 'billing_address_2',
        'type' => 'text',
        'label' => esc_html__( 'Apartamento, suíte, unidade, etc.', 'aireset-default' ),
        'position' => 'right',
        'classes' => '',
        'label_classes' => '',
        'required' => 'no',
        'priority' => '19',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '2',
      ),
      'billing_city' => array(
        'id' => 'billing_city',
        'type' => 'text',
        'label' => esc_html__( 'Cidade', 'aireset-default' ),
        'position' => 'left',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '20',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '2',
      ),
      'billing_state' => array(
        'id' => 'billing_state',
        'type' => 'select',
        'label' => esc_html__( 'Estado', 'aireset-default' ),
        'position' => 'right',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '21',
        'source' => 'native',
        'enabled' => 'yes',
        'step' => '2',
      ),
    );

    return apply_filters( 'aireset_default_get_fields', $checkout_fields );
  }


  /**
   * Get fields from Brazilian Market on WooCommerce plugin
   * 
   * @since 3.0.0
   * @version 3.8.0
   * @return array
   */
  public static function get_wcbcf_fields() {
    return array(
      'billing_persontype' => array(
        'id' => 'billing_persontype',
        'type' => 'select',
        'label' => esc_html__( 'Tipo de Pessoa', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '6',
        'source' => 'plugin',
        'enabled' => 'yes',
        'step' => '1',
        'options' => array(
          '1' => esc_html__( 'Pessoa Física (CPF)', 'aireset-default' ),
          '2' => esc_html__( 'Pessoa Jurífica (CNPJ)', 'aireset-default' ),
        ),
      ),
      'billing_cpf' => array(
        'id' => 'billing_cpf',
        'type' => 'tel',
        'label' => esc_html__( 'CPF', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '000.000.000-00',
        'required' => 'yes',
        'priority' => '7',
        'source' => 'plugin',
        'enabled' => 'yes',
        'step' => '1',
      ),
      'billing_cnpj' => array(
        'id' => 'billing_cnpj',
        'type' => 'tel',
        'label' => esc_html__( 'CNPJ', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '00.000.000/0000-00',
        'required' => 'yes',
        'priority' => '8',
        'source' => 'plugin',
        'enabled' => 'yes',
        'step' => '1',
      ),
      'billing_ie' => array(
        'id' => 'billing_ie',
        'type' => 'tel',
        'label' => esc_html__( 'Inscrição Estadual', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '',
        'required' => 'no',
        'priority' => '9',
        'source' => 'plugin',
        'enabled' => 'no',
        'step' => '1',
      ),
      'billing_cellphone' => array(
        'id' => 'billing_cellphone',
        'type' => 'tel',
        'label' => esc_html__( 'Celular', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '(00) 00000-0000',
        'required' => 'no',
        'priority' => '10',
        'source' => 'plugin',
        'enabled' => 'no',
        'step' => '1',
      ),
      'billing_rg' => array(
        'id' => 'billing_rg',
        'type' => 'text',
        'label' => esc_html__( 'RG', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '',
        'required' => 'no',
        'priority' => '11',
        'source' => 'plugin',
        'enabled' => 'no',
        'step' => '1',
      ),
      'billing_birthdate' => array(
        'id' => 'billing_birthdate',
        'type' => 'tel',
        'label' => esc_html__( 'Data de nascimento', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'input_mask' => '',
        'required' => 'no',
        'priority' => '12',
        'source' => 'plugin',
        'enabled' => 'no',
        'step' => '1',
      ),
      'billing_gender' => array(
        'id' => 'billing_gender',
        'type' => 'select',
        'label' => esc_html__( 'Gênero', 'aireset-default' ),
        'position' => 'full',
        'classes' => '',
        'label_classes' => '',
        'required' => 'no',
        'priority' => '13',
        'source' => 'plugin',
        'enabled' => 'no',
        'step' => '1',
      ),
      'billing_number' => array(
        'id' => 'billing_number',
        'type' => 'text',
        'label' => esc_html__( 'Número da residência', 'aireset-default' ),
        'position' => 'right',
        'classes' => '',
        'label_classes' => '',
        'required' => 'yes',
        'priority' => '17',
        'source' => 'plugin',
        'enabled' => 'yes',
        'step' => '2',
      ),
      'billing_neighborhood' => array(
        'id' => 'billing_neighborhood',
        'type' => 'text',
        'label' => esc_html__( 'Bairro', 'aireset-default' ),
        'position' => 'left',
        'classes' => '',
        'label_classes' => '',
        'required' => 'no',
        'priority' => '18',
        'source' => 'plugin',
        'enabled' => 'yes',
        'step' => '2',
      ),
    );
  }
}

new Init();

if ( ! class_exists('Aireset\Default\Init\Init') ) {
  class_alias( 'Aireset\Default\Init', 'Aireset\Default\Init\Init' );
}