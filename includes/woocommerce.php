<?php

add_action('woocommerce_process_shop_order_meta', 'auto_create_or_assign_customer_to_order', 10, 1);

function auto_create_or_assign_customer_to_order($order_id) {
    // Verifica se a opção está ativada
    if (get_option('aireset_woo_auto_create_customer') != 1) {
        return; // Se a criação automática de clientes estiver desativada, não faz nada.
    }

    // Obtém o pedido
    $order = wc_get_order($order_id);

    // Verifica se o pedido já tem um cliente associado
    if ($order->get_customer_id() > 0) {
        return; // Cliente já está vinculado, nada a fazer.
    }

    // Obtém o e-mail de faturamento do pedido
    $billing_email = $order->get_billing_email();
    
    if (!$billing_email) {
        return; // Se não houver e-mail de faturamento, interrompe.
    }

    // Verifica se o cliente com este e-mail já existe
    $user = get_user_by('email', $billing_email);

    if ($user) {
        // Se o cliente existe, associa o cliente ao pedido
        $order->set_customer_id($user->ID);
    } else {
        // Se o cliente não existe, cria um novo cliente
        $username = sanitize_user(current(explode('@', $billing_email)));
        $password = wp_generate_password();
        $user_id = wc_create_new_customer($billing_email, $username, $password);

        // Verifica se a criação do cliente foi bem-sucedida
        if (!is_wp_error($user_id)) {
            // Associa o novo cliente ao pedido
            $order->set_customer_id($user_id);
        } else {
            error_log('Erro ao criar cliente automaticamente: ' . $user_id->get_error_message());
            return;
        }
    }

    // Salva as alterações do pedido
    $order->save();
}
