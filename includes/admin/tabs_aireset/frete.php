<?php

use Aireset\Default\Init;
use Aireset\Default\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="frete" class="nav-content">
   <table class="form-table">
      <tr>
        <th>
           <?php echo esc_html__( 'Ativar calculadora de frete', 'aireset-default' ) ?>
           <span class="aireset-default-description"><?php echo esc_html__('Ative esta opção para adicionar uma calculadora de frete na página de produto individual.', 'aireset-default' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="aireset_default_enable_shipping_calculator" name="aireset_default_enable_shipping_calculator" value="yes" <?php checked( self::get_setting( 'aireset_default_enable_shipping_calculator' ) == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Ativar cálculo automático de frete', 'aireset-default' ) ?>
           <span class="aireset-default-description"><?php echo esc_html__('Ative esta opção para que o frete seja calculado de forma automática.', 'aireset-default' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="aireset_default_enable_auto_shipping_calculator" name="aireset_default_enable_auto_shipping_calculator" value="yes" <?php checked( self::get_setting( 'aireset_default_enable_auto_shipping_calculator' ) == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Posição da calculadora de frete', 'aireset-default' ) ?>
           <span class="aireset-default-description"><?php echo esc_html__( 'Selecione onde o gancho que será exibido as formas de pagamento na página de produto individual. Shortcode disponível: [aireset_shipping_calculator]', 'aireset-default' ) ?></span>
       </th>
        <td>
           <select name="aireset_default_hook_display_shipping_calculator" class="form-select">
              <option value="after_cart" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'after_cart' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Depois do carrinho (Padrão)', 'aireset-default' ) ?></option>
              <option value="before_cart" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'before_cart' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Antes do carrinho', 'aireset-default' ) ?></option>
              <option value="meta_end" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'meta_end' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Final das informações adicionais', 'aireset-default' ) ?></option>
              <option value="shortcode" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'shortcode' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Shortcode', 'aireset-default' ) ?></option>
           </select>
        </td>
     </tr>
     <tr>
         <th>
            <?php echo esc_html__( 'Cor principal', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Selecione a cor principal para botões e outros estilos.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="color" name="aireset_default_primary_main_color" class="form-control-color" value="<?php echo self::get_setting( 'aireset_default_primary_main_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto informativo antes do campo de CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_info_before_input_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_text_info_before_input_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do botão da calculadora de frete', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_button_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_text_button_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do cabeçalho das formas de entrega', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_header_ship" value="<?php echo self::get_setting( 'aireset_default_text_header_ship' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do cabeçalho do valor das formas de entrega', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_header_value" value="<?php echo self::get_setting( 'aireset_default_text_header_value' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do espaço reservado do campo de CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_placeholder_input_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_text_placeholder_input_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto de observação inferior das opções de frete', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_note_text_bottom_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_note_text_bottom_shipping_calc' ) ?>"/>
         </td>
      </tr>
  </table>
</div>