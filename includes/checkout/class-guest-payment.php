<?php

namespace Aireset\Default\Checkout;

use Aireset\Default\Init;

class GuestPayment {
    
    public function __construct() {
        if (Init::get_setting('aireset_default_order_pay_without_login') === 'yes') {
            // Permitir pagamento de pedidos se o usuário estiver desconectado
            add_filter('user_has_cap', [$this, 'order_pay_without_login'], 9999, 3);
            
            // Permitir seleção de métodos de pagamento mesmo sem usuário vinculado
            add_filter('woocommerce_available_payment_gateways', [$this, 'allow_payment_gateways_without_user'], 10, 1);
            
            // Permitir checkout sem cliente vinculado
            add_filter('woocommerce_checkout_customer_id', [$this, 'checkout_allow_no_customer'], 10, 2);
        }
    }

    public function order_pay_without_login($allcaps, $caps, $args) {
        if (isset($caps[0], $_GET['key']) && $caps[0] === 'pay_for_order') {
            $order_id = isset($args[2]) ? $args[2] : null;
            $order = wc_get_order($order_id);
            if ($order) {
                $allcaps['pay_for_order'] = true;
            }
        }
        return $allcaps;
    }

    public function allow_payment_gateways_without_user($gateways) {
        if (!is_checkout_pay_page()) {
            return $gateways;
        }

        // Detecta o pedido pela query string /order-pay/ID/?key=...
        $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
        if (!$order_id && isset($GLOBALS['wp']) && !empty($GLOBALS['wp']->query_vars['order-pay'])) {
            $order_id = absint($GLOBALS['wp']->query_vars['order-pay']);
        }

        if (!$order_id) {
            return $gateways;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            return $gateways;
        }

        // Se o pedido estiver sem usuário, forçamos os gateways a aparecer
        if ($order->get_user_id() == 0) {
            foreach ($gateways as $gateway) {
                $gateway->enabled = 'yes';
            }
        }

        return $gateways;
    }

    public function checkout_allow_no_customer($customer_id, $checkout = null) {
        // Se não houver usuário logado, retorna 0 (sem cliente)
        if (!is_user_logged_in()) {
            return 0;
        }
        return $customer_id;
    }
}