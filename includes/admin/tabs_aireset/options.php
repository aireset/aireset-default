<?php

use Aireset\Default\Init;
use Aireset\Default\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="general" class="nav-content">
   <table class="form-table">
      <?php
      /**
      * Hook for display custom generals option
      * 
      * @since 3.6.0
      */
      do_action('aireset_default_before_general_options'); ?>

      <tr>
         <th>
            <?php echo esc_html__( 'Força evitar zoom de navegador?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que as pessoas não possam mexer no zoom do site.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_fixed_viewport" name="aireset_default_fixed_viewport" value="yes" <?php checked( Init::get_setting( 'aireset_default_fixed_viewport') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Desativar mensagem padrão de "Produto Adicionado ao Carrinho"?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que as pessoas possam pagar sem ter que Logar no Woocomerce.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_disable_add_to_cart_message" name="aireset_default_disable_add_to_cart_message" value="yes" <?php checked( Init::get_setting( 'aireset_default_disable_add_to_cart_message') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Pagar sem Login no Woocomerce?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que as pessoas possam pagar sem ter que Logar no Woocomerce.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_order_pay_without_login" name="aireset_default_order_pay_without_login" value="yes" <?php checked( Init::get_setting( 'aireset_default_order_pay_without_login') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar mascaras customizadas?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que você possa usar mascarás customizadas no no wordpress.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_masks" name="aireset_default_masks" value="yes" <?php checked( Init::get_setting( 'aireset_default_masks') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Número de Telefone com Bandeira?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que possa usar Número de Telefone com Bandeira.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_intl_tel_input" name="aireset_default_intl_tel_input" value="yes" <?php checked( Init::get_setting( 'aireset_default_intl_tel_input') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Status Customizados do Woocomerce?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que Status Customizados do Woocomerce sejam ativados.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_status_woocommerce" name="aireset_default_status_woocommerce" value="yes" <?php checked( Init::get_setting( 'aireset_default_status_woocommerce') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Criação Automatica de Clientes pelo Admin Woocomerce?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para que ao adicionar um novo pedido crie automaticamente ou encontre o cliente e vincule ao pedido.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_auto_create_or_assign_customer_to_order" name="aireset_default_auto_create_or_assign_customer_to_order" value="yes" <?php checked( Init::get_setting( 'aireset_default_auto_create_or_assign_customer_to_order') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Imagens Padrões da Aireset?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para remover os seguintes tamanhos (thumbnail, medium, medium_large, large, 1536x1536, 2048x2048, 2560×2560) e adicionar os seguintes (banner size, banner mobile size, produto size, produto size table, produto size 2, micro banners size).', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_images" name="aireset_default_images" value="yes" <?php checked( Init::get_setting( 'aireset_default_images') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Ajuste de Label para o Botão do Yith?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção ajustar a Label para o Botão do Yith.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_yith_wcas_submit_label" name="aireset_default_yith_wcas_submit_label" value="yes" <?php checked( Init::get_setting( 'aireset_default_yith_wcas_submit_label') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Ativar Coluna Customizada para chamar no Whatsapp na tela de Pedidos?', 'aireset-default' ) ?>
            <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para exibir uma Coluna Customizada para chamar no Whatsapp na tela de Pedidos.', 'aireset-default' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="aireset_default_custom_orders_list_column_content" name="aireset_default_custom_orders_list_column_content" value="yes" <?php checked( Init::get_setting( 'aireset_default_custom_orders_list_column_content') === 'yes' ); ?> />
            </div>
         </td>
      </tr>

      <?php
      /**
      * Hook for display custom general options
      * 
      * @since 3.6.0
      */
      do_action('aireset_default_after_general_options'); ?>

   </table>
</div>