<?php

use Aireset\Default\Custom_Fields;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Business fields and legacy fallback settings.
$fields = Custom_Fields::get_fields()['dados_empresa']['fields'];
$settings = get_option('aireset_default_settings', array());
$woo_default_country = (string) get_option('woocommerce_default_country', 'BR');
$woo_country_parts = explode(':', $woo_default_country);
$woo_country = isset($woo_country_parts[0]) ? strtoupper((string) $woo_country_parts[0]) : 'BR';
$woo_state = isset($woo_country_parts[1]) ? strtoupper((string) $woo_country_parts[1]) : '';
$woo_address_map = array(
  'endereco' => (string) get_option('woocommerce_store_address', ''),
  'endereco_linha_2' => (string) get_option('woocommerce_store_address_2', ''),
  'cidade' => (string) get_option('woocommerce_store_city', ''),
  'cep' => (string) get_option('woocommerce_store_postcode', ''),
  'pais' => $woo_country,
  'estado' => $woo_state,
);

?>

<div id="business" class="nav-content">
   <table class="form-table">
      <?php
      /**
      * Hook for display custom generals option
      *
      * @since 3.6.0
      */
      do_action('aireset_default_before_business_options'); ?>
         <?php foreach ($fields as $field_key => $field) : ?>
            <tr>
               <th>
                  <?php echo $field['title']; ?>
                  <span class="aireset-default-description">
                     <?php echo $field['description']; ?>
                  </span>
               </th>
               <td style="width: 100%;">
                  <?php
                    $field_value = '';

                    if (isset($woo_address_map[$field_key]) && $woo_address_map[$field_key] !== '') {
                      // Keep the company address fields always aligned with Woo settings.
                      $field_value = $woo_address_map[$field_key];
                    } else {
                      $field_value = (string) get_option($field_key, '');
                    }

                    if ($field_value === '' && isset($settings[$field_key])) {
                      // Backward compatibility for values previously stored in the settings array.
                      $field_value = (string) $settings[$field_key];
                    }
                  ?>
                  <input style="width: 100%;" type="<?php echo esc_attr($field['type']); ?>" class="regular-text" name="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr($field_value); ?>">
               </td>
            </tr>
         <?php endforeach; ?>

      <?php
      /**
      * Hook for display custom general options
      *
      * @since 3.6.0
      */
      do_action('aireset_default_after_business_options'); ?>

   </table>
</div>
