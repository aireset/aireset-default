<?php

namespace Aireset\Default\Cart;

use Aireset\Default\Init;

class CartMessageManager {
    
    public function __construct() {
        if (Init::get_setting('aireset_default_disable_add_to_cart_message') === 'yes') {
            // Desativar mensagens de "Adicionar ao Carrinho"
            add_filter('wc_add_to_cart_message_html', '__return_false');
            add_filter('woocommerce_notice_types', '__return_empty_array');
            add_filter('woocommerce_order_email_verification_required', '__return_false', 9999);
        }
    }
}