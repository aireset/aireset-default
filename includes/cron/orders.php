<?php



// function custom_payment_complete($order_id) {
//     if (!$order_id) {
//         return;
//     }

//     // Obtém o pedido
//     $order = wc_get_order($order_id);

//     // Define o status personalizado
//     $order->update_status('payed');
// }

// add_action('woocommerce_payment_complete', 'custom_payment_complete');


// Agende o evento ao ativar o plugin/tema
add_action('wp', 'agendar_verificacao_pedidos');

function agendar_verificacao_pedidos() {
    if (!wp_next_scheduled('verificar_pedidos')) {
        wp_schedule_event(time(), 'daily', 'verificar_pedidos');
        aireset_registrar_log('Evento verificar_pedidos agendado.');
    }
}

// Remova o evento ao desativar o plugin/tema
register_deactivation_hook(__FILE__, 'desativar_verificacao_pedidos');

function desativar_verificacao_pedidos() {
    wp_clear_scheduled_hook('verificar_pedidos');
    aireset_registrar_log('Evento verificar_pedidos removido.');
}

// Verificar e atualizar pedidos de "Pedido Pago" para "Em Produção" após três dias
add_action('verificar_pedidos', 'verificar_e_atualizar_pedidos');

function verificar_e_atualizar_pedidos() {
    aireset_registrar_log('Iniciando verificação de pedidos.');
    $args = array(
        'status' => 'payed',
        'date_paid' => '<=' . (time() - 3 * DAY_IN_SECONDS),
        'date_query' => array(
            array(
                'after' => '2024-05-01',
                'inclusive' => true,
            ),
        ),
    );

    $pedidos = wc_get_orders($args);
    aireset_registrar_log('Pedidos encontrados: ' . count($pedidos));

    foreach ($pedidos as $pedido) {
        $pedido->update_status('em-producao'); // Altere para o status desejado
        aireset_registrar_log('Pedido ID ' . $pedido->get_id() . ' atualizado para Em Produção.');
    }
}
