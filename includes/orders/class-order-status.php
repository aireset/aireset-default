<?php

namespace Aireset\Default\Orders;

use Aireset\Default\Init;

class OrderStatus {
    
    public function __construct() {
        if (Init::get_setting('aireset_default_status_woocommerce') === 'yes') {
            // Códigos comentados originais para status personalizados
            /*
            add_action('init', [$this, 'add_custom_order_status']);
            add_filter('wc_order_statuses', [$this, 'edit_default_order_status_titles']);
            */
        }
    }

    /*
    public function add_custom_order_status() {
        // register_post_status( 'wc-payed', [
        //     'label'                     => 'Pedido Pago',
        //     'public'                    => true,
        //     'show_in_admin_status_list' => true,
        //     'show_in_admin_all_list'    => true,
        //     'exclude_from_search'       => false,
        //     'label_count'               => _n_noop( 'Pedidos Pagos <span class="count">(%s)</span>', 'Pedidos Pago <span class="count">(%s)</span>' )
        // ] );

        // Pedido Pago Parcialmente
        register_post_status( 'wc-partial-payed', [
            'label'                     => _x( 'Pedido Pago Parcialmente', 'Order status', 'aireset-default' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            // translators: %s: Number of partial paid orders
            'label_count'               => _n_noop(
                'Pedidos Pagos Parcialmente <span class="count">(%s)</span>', 
                'Pedidos Pago Parcialmente <span class="count">(%s)</span>', 
                'aireset-default'
            )
        ]);

        // Em Produção
        register_post_status( 'wc-in-production', [
            'label'                     => _x( 'Em Produção', 'Order status', 'aireset-default' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            // translators: %s: Number of orders in production
            'label_count'               => _n_noop( 
                'Em Produção <span class="count">(%s)</span>', 
                'Em Produção <span class="count">(%s)</span>', 
                'aireset-default'
            )
        ]);

        // Em Separação
        register_post_status( 'wc-in-separation', [
            'label'                     => _x( 'Em Separação', 'Order status', 'aireset-default' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            // translators: %s: Number of orders in separation
            'label_count'               => _n_noop( 
                'Em Separação <span class="count">(%s)</span>', 
                'Em Separação <span class="count">(%s)</span>', 
                'aireset-default'
            )
        ]);

        // Pedido Enviado
        register_post_status( 'wc-order-sended', [
            'label'                     => _x( 'Pedido Enviado', 'Order status', 'aireset-default' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            // translators: %s: Number of sent orders
            'label_count'               => _n_noop( 
                'Pedidos Enviados <span class="count">(%s)</span>', 
                'Pedidos Enviados <span class="count">(%s)</span>', 
                'aireset-default'
            )
        ]);
    }

    public function edit_default_order_status_titles($order_statuses) {
        if (isset($order_statuses['wc-on-hold'])) {
            $order_statuses['wc-on-hold'] = _x('Aguardando Confirmação', 'Order status', 'aireset-default');
        }
        if (isset($order_statuses['wc-pending'])) {
            $order_statuses['wc-pending'] = _x('Pagamento Pendente', 'Order status', 'aireset-default');
        }
        if (isset($order_statuses['wc-processing'])) {
            $order_statuses['wc-processing'] = _x('Pedido Pago', 'Order status', 'aireset-default');
        }
        if (isset($order_statuses['wc-cancelled'])) {
            $order_statuses['wc-cancelled'] = _x('Cancelado', 'Order status', 'aireset-default');
        }
        if (isset($order_statuses['wc-order-completed'])) {
            $order_statuses['wc-order-completed'] = _x('Concluído', 'Order status', 'aireset-default');
        }
        if (isset($order_statuses['wc-order-refunded'])) {
            $order_statuses['wc-order-refunded'] = _x('Reembolsado', 'Order status', 'aireset-default');
        }
        if (isset($order_statuses['wc-order-failed'])) {
            $order_statuses['wc-order-failed'] = _x('Malsucedido', 'Order status', 'aireset-default');
        }

        $new_order_statuses = array();
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[$key] = $status;
            if ('wc-processing' === $key) {
                $new_order_statuses['wc-partial-payed'] = _x('Pedido Pago Parcialmente', 'Order status', 'aireset-default');
                $new_order_statuses['wc-in-production'] = _x('Em Produção', 'Order status', 'aireset-default');
                $new_order_statuses['wc-in-separation'] = _x('Em Separação', 'Order status', 'aireset-default');
                $new_order_statuses['wc-order-sended'] = _x('Pedido Enviado', 'Order status', 'aireset-default');
            }
        }
        return $new_order_statuses;
    }
    */
}