<?php

use Aireset\Default\Init;
use Aireset\Default\Admin_Options;
use Aireset\Default\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="fields" class="nav-content">
    <table class="form-table">
        <?php
        /**
         * Hook for display custom fields options
         * 
         * @since 3.6.0
         */
        do_action('aireset_default_before_fields_options'); ?>

        <tr>
            <th>
                <?php echo esc_html__( 'Mostrar campo de observações adicionais', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para mostrar o campo de observações adicionais no pedido.', 'aireset-default' ) ?></span>
            </th>
            <td>
                <div class="form-check form-switch">
                    <input type="checkbox" class="toggle-switch" id="enable_aditional_notes" name="enable_aditional_notes" value="yes" <?php checked( Init::get_setting('enable_aditional_notes') === 'yes' ); ?> />
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Ocultar campo de cupom de desconto', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Quando ativado, o campo do cupom não será exibido aos usuários e eles não poderão inserir um código de cupom. No entanto, você pode aplicar um cupom automaticamente.', 'aireset-default' ) ?></span>
            </th>
            <td>
                <div class="form-check form-switch">
                    <input type="checkbox" class="toggle-switch" id="enable_hide_coupon_code_field" name="enable_hide_coupon_code_field" value="yes" <?php checked( Init::get_setting('enable_hide_coupon_code_field') === 'yes' ); ?> />
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Adicionar máscaras para campos', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para adicionar máscaras de preenchimento em campos da finalização de compras.', 'aireset-default' ) ?></span>
            </th>
            <td>
                <div class="form-check form-switch">
                    <input type="checkbox" class="toggle-switch" id="enable_field_masks" name="enable_field_masks" value="yes" <?php checked( Init::get_setting('enable_field_masks') === 'yes' ); ?> />
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo esc_html__( 'Otimizar para produtos digitais', 'aireset-default' );
                
                if ( ! License::is_valid() ) : ?>
                    <span class="badge pro bg-primary rounded-pill ms-2">
                        <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                        <?php echo esc_html__( 'Pro', 'aireset-default' ) ?>
                    </span>
                <?php endif; ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para remover a etapa de entrega em produtos digitais.', 'aireset-default' ) ?></span>
            </th>
            <td>
                <div class="form-check form-switch <?php echo ( ! License::is_valid() ) ? 'require-pro' : ''; ?>">
                    <input type="checkbox" class="toggle-switch <?php echo ( ! License::is_valid() ) ? 'pro-version' : ''; ?>" id="enable_optimize_for_digital_products" name="enable_optimize_for_digital_products" value="yes" <?php checked( Init::get_setting('enable_optimize_for_digital_products') === 'yes' && License::is_valid() ); ?> />
                </div>
            </td>
        </tr>

        <?php if ( class_exists('Extra_Checkout_Fields_For_Brazil') ) : ?>
            <tr>
                <th>
                    <?php echo esc_html__( 'Ocultar campos do mercado brasileiro se país não for Brasil', 'aireset-default' );
                    
                    if ( ! License::is_valid() ) : ?>
                        <span class="badge pro bg-primary rounded-pill ms-2">
                            <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                            <?php echo esc_html__( 'Pro', 'aireset-default' ) ?>
                        </span>
                    <?php endif; ?>

                    <span class="aireset-default-description"><?php echo esc_html__( 'Ative esta opção para ocultar os campos inseridos pelo plugin Brazilian Market on WooCommerce quando o país selecionado não for Brasil.', 'aireset-default' ) ?></span>
                </th>
                <td>
                    <div class="form-check form-switch <?php echo ( ! License::is_valid() ) ? 'require-pro' : ''; ?>">
                        <input type="checkbox" class="toggle-switch <?php echo ( ! License::is_valid() ) ? 'pro-version' : ''; ?>" id="enable_unset_wcbcf_fields_not_brazil" name="enable_unset_wcbcf_fields_not_brazil" value="yes" <?php checked( Init::get_setting('enable_unset_wcbcf_fields_not_brazil') === 'yes' && License::is_valid() ); ?> />
                    </div>
                </td>
            </tr>
        <?php endif; ?>
       
        <tr class="container-separator"></tr>

        <tr class="w-100">
            <th>
                <?php echo esc_html__( 'Gerenciar os campos e etapas da finalização de compras', 'aireset-default' ); ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Arraste e solte o campo para reordenar ou mudar o campo de etapa.', 'aireset-default' ) ?></span>
            </th>
            <td>
                <div class="form-check form-switch">
                    <input type="checkbox" class="toggle-switch" id="enable_manage_fields" name="enable_manage_fields" value="yes" <?php checked( Init::get_setting('enable_manage_fields') === 'yes' && License::is_valid() ); ?> />
                </div>
            </td>
        </tr>

        <tr class="mt-4 step-checkout-fields-container">
            <td>
                <button id="add_new_checkout_fields_trigger" class="btn btn-primary d-flex align-items-center">
                    <svg class="icon icon-white me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                    <?php echo esc_html__('Adicionar novos campos', 'aireset-default' ) ?>
                </button>

                <?php include_once AIRESET_DEFAULT_INC_PATH . 'admin/tabs/parts/new-fields.php'; ?>
            </td>
        </tr>

        <?php
        /**
         * Hook for display custom fields options
         * 
         * @since 3.6.0
         */
        do_action('aireset_default_after_fields_options'); ?>

    </table>
</div>