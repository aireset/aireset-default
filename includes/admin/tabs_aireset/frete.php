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
           <span class="aireset-default-description"><?php echo esc_html__('Ative esta opcao para adicionar uma calculadora de frete na pagina de produto individual.', 'aireset-default' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="aireset_default_enable_shipping_calculator" name="aireset_default_enable_shipping_calculator" value="yes" <?php checked( self::get_setting( 'aireset_default_enable_shipping_calculator' ) == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Ativar calculo automatico de frete', 'aireset-default' ) ?>
           <span class="aireset-default-description"><?php echo esc_html__('Ative esta opcao para que o frete seja calculado de forma automatica.', 'aireset-default' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="aireset_default_enable_auto_shipping_calculator" name="aireset_default_enable_auto_shipping_calculator" value="yes" <?php checked( self::get_setting( 'aireset_default_enable_auto_shipping_calculator' ) == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Posicao da calculadora de frete', 'aireset-default' ) ?>
           <span class="aireset-default-description"><?php echo esc_html__( 'Selecione o local de exibicao na pagina de produto. Shortcode disponivel: [aireset_shipping_calculator]', 'aireset-default' ) ?></span>
       </th>
        <td>
           <select name="aireset_default_hook_display_shipping_calculator" class="form-select">
              <option value="after_cart" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'after_cart' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Depois do carrinho (Padrao)', 'aireset-default' ) ?></option>
              <option value="before_cart" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'before_cart' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Antes do carrinho', 'aireset-default' ) ?></option>
              <option value="meta_end" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'meta_end' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Final das informacoes adicionais', 'aireset-default' ) ?></option>
              <option value="shortcode" <?php echo ( self::get_setting( 'aireset_default_hook_display_shipping_calculator' ) == 'shortcode' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Shortcode', 'aireset-default' ) ?></option>
           </select>
        </td>
     </tr>
     <tr>
         <th>
            <?php echo esc_html__( 'Cor principal', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Selecione a cor principal para botoes e outros estilos.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="color" name="aireset_default_primary_main_color" class="form-control-color" value="<?php echo self::get_setting( 'aireset_default_primary_main_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Cor hover do botao', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Cor aplicada ao hover do botao da calculadora.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="color" name="aireset_default_secondary_main_color" class="form-control-color" value="<?php echo self::get_setting( 'aireset_default_secondary_main_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Cor do texto do botao', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Cor do texto no botao da calculadora.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="color" name="aireset_default_button_text_color" class="form-control-color" value="<?php echo self::get_setting( 'aireset_default_button_text_color' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto informativo antes do campo de CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para nao exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_info_before_input_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_text_info_before_input_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do botao da calculadora de frete', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para nao exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_button_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_text_button_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do cabecalho das formas de entrega', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para nao exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_header_ship" value="<?php echo self::get_setting( 'aireset_default_text_header_ship' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do cabecalho do valor das formas de entrega', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para nao exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_header_value" value="<?php echo self::get_setting( 'aireset_default_text_header_value' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do espaco reservado do campo de CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para nao exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_text_placeholder_input_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_text_placeholder_input_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto de observacao inferior das opcoes de frete', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para nao exibir.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_note_text_bottom_shipping_calc" value="<?php echo self::get_setting( 'aireset_default_note_text_bottom_shipping_calc' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Exibir link "Nao sei meu CEP"', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative para exibir um link de ajuda para consulta de CEP.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_show_postcode_helper_link" name="aireset_default_show_postcode_helper_link" value="yes" <?php checked( self::get_setting( 'aireset_default_show_postcode_helper_link' ) == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Texto do link de ajuda de CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Rotulo do link exibido abaixo do campo de CEP.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_postcode_helper_link_text" value="<?php echo self::get_setting( 'aireset_default_postcode_helper_link_text' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'URL do link de ajuda de CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Endereco do site para consulta de CEP.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="url" class="form-control input-control-wd-20" name="aireset_default_postcode_helper_link_url" value="<?php echo self::get_setting( 'aireset_default_postcode_helper_link_url' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ordem dos resultados de frete', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Define a ordem de exibicao das opcoes de frete.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <select name="aireset_default_shipping_calc_results_order" class="form-select">
               <option value="default" <?php echo ( self::get_setting( 'aireset_default_shipping_calc_results_order' ) == 'default' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Padrao da transportadora', 'aireset-default' ) ?></option>
               <option value="lowest" <?php echo ( self::get_setting( 'aireset_default_shipping_calc_results_order' ) == 'lowest' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Menor valor primeiro', 'aireset-default' ) ?></option>
               <option value="highest" <?php echo ( self::get_setting( 'aireset_default_shipping_calc_results_order' ) == 'highest' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Maior valor primeiro', 'aireset-default' ) ?></option>
            </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Dias para lembrar CEP', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Quantidade de dias para manter o CEP salvo no navegador.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="number" min="1" max="365" class="form-control input-control-wd-20" name="aireset_default_shipping_calc_cookie_days" value="<?php echo self::get_setting( 'aireset_default_shipping_calc_cookie_days' ) ?>"/>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Mensagem sem opcoes de frete', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Texto exibido quando nao houver formas de entrega para o CEP informado.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <input type="text" class="form-control input-control-wd-20" name="aireset_default_empty_shipping_message" value="<?php echo self::get_setting( 'aireset_default_empty_shipping_message' ) ?>"/>
         </td>
      </tr>
  </table>
</div>
