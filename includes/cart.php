<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Desativar mensagens de "Adicionar ao Carrinho"
add_filter( 'wc_add_to_cart_message_html', '__return_false' );
add_filter( 'woocommerce_notice_types', '__return_empty_array' );
add_filter( 'woocommerce_order_email_verification_required', '__return_false', 9999 );

/**
 * Permitir pagamento de pedidos se o usuário estiver desconectado - WooCommerce
 */
add_filter( 'user_has_cap', 'aireset_order_pay_without_login', 9999, 3 );

if ( ! function_exists( 'aireset_order_pay_without_login' ) ) {
    function aireset_order_pay_without_login( $allcaps, $caps, $args ) {
        if ( isset( $caps[0], $_GET['key'] ) ) {
            if ( $caps[0] === 'pay_for_order' ) {
                $order_id = isset( $args[2] ) ? $args[2] : null;
                $order = wc_get_order( $order_id );
                if ( $order ) {
                    $allcaps['pay_for_order'] = true;
                }
            }
        }
        return $allcaps;
    }
}
