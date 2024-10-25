<?php

use Aireset\Default\Init;
use Aireset\Default\Helpers;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="texts" class="nav-content">
    <table class="form-table">
        <?php
        /**
         * Hook for display custom text option
         * 
         * @since 3.6.0
         */
        do_action('aaireset_default_before_texts_options'); ?>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo dos campos da etapa de contato', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_header_step_1" name="text_header_step_1" value="<?php echo Init::get_setting('text_header_step_1') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Título das formas de entrega', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_shipping_methods_label" name="text_shipping_methods_label" value="<?php echo Init::get_setting('text_shipping_methods_label') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo dos campos da etapa de entrega', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_header_step_2" name="text_header_step_2" value="<?php echo Init::get_setting('text_header_step_2') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo dos campos da etapa de pagamento', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_header_step_3" name="text_header_step_3" value="<?php echo Init::get_setting('text_header_step_3') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo dos itens do carrinho', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_header_sidebar_right" name="text_header_sidebar_right" value="<?php echo Init::get_setting('text_header_sidebar_right') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo do verificador da etapa de contato', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_check_step_1" name="text_check_step_1" value="<?php echo Init::get_setting('text_check_step_1') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo do verificador da etapa de entrega', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_check_step_2" name="text_check_step_2" value="<?php echo Init::get_setting('text_check_step_2') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto informativo do verificador da etapa de pagamento', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_check_step_3" name="text_check_step_3" value="<?php echo Init::get_setting('text_check_step_3') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto do botão de voltar etapas', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_previous_step_button" name="text_previous_step_button" value="<?php echo Init::get_setting('text_previous_step_button') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto do botão para revisitar a loja da página de agradecimento', 'aireset-default' ) ?>
                <span class="aireset-default-description"><?php echo esc_html__( 'Deixe em branco para não exibir.', 'aireset-default' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_view_shop_thankyou" name="text_view_shop_thankyou" value="<?php echo Init::get_setting('text_view_shop_thankyou') ?>"/>
            </td>
        </tr>
        
        <tr class="container-separator"></tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto do resumo de informações de contato', 'aireset-default' ) ?>
                <span class="aireset-default-description mb-3"><?php echo esc_html__( 'Utilize as variáveis abaixo para recuperar as informações de campos, use <br> para quebrar uma linha. Ou deixe em branco para não exibir.', 'aireset-default' ) ?></span>

                <?php foreach ( Helpers::get_placeholder_input_values() as $field_id => $value ) : ?>
                    <div class="d-flex mb-1">
                        <span class="aireset-default-description"><code><?php echo esc_html( $value['placeholder_html'] ) ?></code>
                        </span><span class="aireset-default-description ms-2"><?php echo esc_html( $value['description'] ) ?></span>
                    </div>
                <?php endforeach; ?>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_contact_customer_review" name="text_contact_customer_review" value="<?php echo Init::get_setting('text_contact_customer_review') ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo esc_html__( 'Texto do resumo de informações de entrega', 'aireset-default' ) ?>
                <span class="aireset-default-description mb-3"><?php echo esc_html__( 'Utilize as variáveis abaixo para recuperar as informações de campos, use <br> para quebrar uma linha. Ou deixe em branco para não exibir.', 'aireset-default' ) ?></span>

                <?php foreach ( Helpers::get_placeholder_input_values() as $field_id => $value ) : ?>
                    <div class="d-flex mb-1">
                        <span class="aireset-default-description"><code><?php echo esc_html( $value['placeholder_html'] ) ?></code>
                        </span><span class="aireset-default-description ms-2"><?php echo esc_html( $value['description'] ) ?></span>
                    </div>
                <?php endforeach; ?>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_shipping_customer_review" name="text_shipping_customer_review" value="<?php echo Init::get_setting('text_shipping_customer_review') ?>"/>
            </td>
        </tr>
        
        <?php
        /**
         * Hook for display custom text option
         * 
         * @since 3.6.0
         */
        do_action('aaireset_default_after_texts_options'); ?>

    </table>
</div>