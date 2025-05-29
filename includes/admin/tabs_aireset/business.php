<?php

use Aireset\Default\Custom_Fields;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Captura os campos
$fields = Custom_Fields::get_fields()['dados_empresa']['fields'];

// Salvar os campos ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('save_aireset_fields')) {
    foreach ($fields as $key => $field) {
        if (isset($_POST[$key])) {
            update_option($key, sanitize_text_field($_POST[$key]));
        }
    }
}

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
                  <input style="width: 100%;" type="<?php echo $field['type']; ?>" class="regular-text" name="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr(get_option($field_key, '')); ?>">
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