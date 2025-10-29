<?php

namespace Aireset\Default\Checkout;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Meta Checkout Handler Class
 * 
 * Handles checkout functionality for Meta (Facebook/Instagram) traffic.
 * Processes products and coupons from URL parameters and redirects to checkout.
 * 
 * @since 1.3.7
 */
class MetaCheckoutHandler {

    /**
     * Constructor
     * 
     * @since 1.3.7
     */
    public function __construct() {
        /**
         * Manipulador de Checkout da Meta (Facebook/Instagram)
         *
         * 1. O código é "engatado" no 'template_redirect' (roda em todas as páginas).
         * 2. A função 'meta_checkout_handler_specific_page' é chamada.
         * 3. A PRIMEIRA COISA que a função faz é verificar as condições (Validação de URL e Parâmetro).
         * 4. SÓ SE as condições forem atendidas, o carrinho é limpo.
         */
        add_action( 'template_redirect', array( $this, 'meta_checkout_handler_specific_page' ) );
    }

    /**
     * Meta Checkout Handler for Specific Page
     * 
     * Processes Meta (Facebook/Instagram) checkout parameters and redirects to checkout.
     * Only runs on checkout page with products parameter.
     * 
     * @since 1.3.7
     * @return void
     */
    public function meta_checkout_handler_specific_page() {

        // -----------------------------------------------------------------
        // INÍCIO DA FUNÇÃO E DAS VALIDAÇÕES
        // -----------------------------------------------------------------

        // Validação 1: O WooCommerce está ativo?
        // Se não estiver, o código para imediatamente.
        if ( ! function_exists('WC') ) {
            return; // Para aqui. Nenhuma ação é tomada.
        }

        /**
         * Validação 2 (A MAIS IMPORTANTE - A QUE VOCÊ PEDIU):
         *
         * Aqui verificamos DUAS coisas antes de fazer QUALQUER COISA:
         *
         * a) ! is_checkout() : Verifica se NÃO estamos na página de checkout.
         * A função 'is_checkout()' do WooCommerce é a forma correta e oficial de
         * verificar se a URL é /checkout OU /finalizar-compra (ou qualquer
         * outra que você tenha configurado).
         *
         * b) ! isset( $_GET['products'] ) : Verifica se o parâmetro ?products NÃO existe.
         *
         * Se QUALQUER uma dessas validações falhar (ou seja, se não estivermos
         * no checkout OU se não houver o parâmetro 'products'), a função
         * executa 'return;' e TERMINA IMEDIATAMENTE.
         */
        if ( ! is_checkout() || ! isset( $_GET['products'] ) || empty( $_GET['products'] ) ) {
            
            // Cenário 1: Usuário na Homepage -> !is_checkout() é VERDADEIRO -> O IF é verdadeiro -> return;
            // Cenário 2: Usuário no /finalizar-compra/ SEM ?products= -> !isset() é VERDADEIRO -> O IF é verdadeiro -> return;
            
            // O CARRINHO NÃO É LIMPO. NADA ACONTECE.
            return; 
        }

        // -----------------------------------------------------------------
        // FIM DAS VALIDAÇÕES
        // -----------------------------------------------------------------
        
        //
        // O CÓDIGO SÓ CHEGA AQUI SE ESTIVERMOS EM /finalizar-compra/ (ou /checkout/)
        // E A URL TIVER O PARÂMETRO ?products=
        //
        
        // 3. Limpa o Carrinho
        //    Esta linha SÓ é executada se as validações acima passarem.
        //    Não há risco de limpar o carrinho na Homepage.
        WC()->cart->empty_cart();

        // 4. Processa a Lista de Produtos
        $products_string = sanitize_text_field( wp_unslash( $_GET['products'] ) );
        $products_list = explode( ',', $products_string );

        if ( ! empty( $products_list ) ) {
            foreach ( $products_list as $product_item ) {
                $item_parts = explode( ':', $product_item );

                // Garante que temos um ID e uma Quantidade
                if ( count( $item_parts ) === 2 ) {
                    $product_id = absint( $item_parts[0] ); // ID do Produto
                    $quantity   = absint( $item_parts[1] ); // Quantidade

                    // Adiciona ao carrinho (WooCommerce valida se o produto existe e tem estoque)
                    if ( $product_id > 0 && $quantity > 0 ) {
                        WC()->cart->add_to_cart( $product_id, $quantity );
                    }
                }
            }
        }

        // 5. Aplica o Cupom (Opcional)
        if ( isset( $_GET['coupon'] ) && ! empty( $_GET['coupon'] ) ) {
            $coupon_code = sanitize_text_field( wp_unslash( $_GET['coupon'] ) );
            WC()->cart->apply_coupon( $coupon_code );
        }

        // 6. Prepara o Redirecionamento Limpo
        //    (Remove ?products= da URL para evitar loop se o usuário der F5)
        $checkout_url = wc_get_checkout_url();

        // 7. Preserva os Parâmetros de Rastreamento (UTMs, fbclid, etc.)
        $params_to_preserve = array(
            'fbclid',
            'cart_origin',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content'
        );
        
        $query_args_to_pass = array();
        
        foreach ( $params_to_preserve as $param ) {
            if ( isset( $_GET[ $param ] ) && ! empty( $_GET[ $param ] ) ) {
                $query_args_to_pass[ $param ] = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );
            }
        }

        // Adiciona os parâmetros de rastreamento (se existirem) à URL de checkout base
        if ( ! empty( $query_args_to_pass ) ) {
            $redirect_url = add_query_arg( $query_args_to_pass, $checkout_url );
        } else {
            $redirect_url = $checkout_url;
        }
        
        // 8. Redireciona e Encerra o Script
        //    Envia o usuário para a URL de checkout limpa (com carrinho cheio e UTMs).
        wp_safe_redirect( $redirect_url );
        exit;
    }
}

new MetaCheckoutHandler();
