<?php

function auto_create_or_assign_customer_to_order($order_id) {
    // Verifica se a opção está ativada
    /*if (get_option('aireset_woo_auto_create_customer') != 1) {
        return; // Se a criação automática de clientes estiver desativada, não faz nada.
    }*/
    
    // Obtém o pedido
    $order = wc_get_order($order_id);

    if (!$order) {
        error_log('Pedido não encontrado: ' . $order_id);
        return; // Se o pedido não for encontrado, interrompe.
    }

    // Verifica se o pedido já tem um cliente associado
    if ($order->get_customer_id() > 0) {
        return; // Cliente já está vinculado, nada a fazer.
    }

    // Obtém o e-mail de faturamento do pedido
    $billing_email = $order->get_billing_email();
    $billing_first_name = $order->get_billing_first_name();
    $billing_last_name = $order->get_billing_last_name();

    if (!$billing_email) {
        wc_add_notice('Pedido sem e-mail de faturamento: ' . $order_id, 'success');
        // error_log('Pedido sem e-mail de faturamento: ' . $order_id);
        return; // Se não houver e-mail de faturamento, interrompe.
    }

    // Verifica se o cliente com este e-mail já existe
    $user = get_user_by('email', $billing_email);
    
    highlight_array($user, 'user');
    highlight_array($user->ID, 'user');
    highlight_array($order->get_user_id(), 'get_user_id');

    if ($user) {
        // Se o cliente existe, associa o cliente ao pedido
        $order->set_customer_id($user->ID);

        // Força o salvamento do pedido de forma alternativa
        $result = $order->save();

        // Verifica se o salvamento ocorreu com sucesso
        if (is_wp_error($result)) {
            error_log('Erro ao salvar o pedido: ' . $result->get_error_message());
        } else {
            error_log('Cliente vinculado ao pedido: ' . $user->ID);
        }
        
        highlight_array($order->get_user_id(), 'get_user_id');
        highlight_array($result, 'resultado da sql');
        
        // wc_add_notice(__('Pedido vinculado ao cliente existente.', 'woocommerce'), 'success');
    } else {
        // Se o cliente não existe, cria um novo cliente
        $username = sanitize_user(current(explode('@', $billing_email)), true);
        $password = wp_generate_password();
        $user_id = wp_create_user($username, $password, $billing_email);

        if (!is_wp_error($user_id)) {
            // Atualizar as informações do usuário com os detalhes do pedido
            wp_update_user(array(
                'ID' => $user_id,
                'first_name' => $billing_first_name,
                'last_name' => $billing_last_name
            ));

            // Atribuir o papel de "customer"
            $user = new WP_User($user_id);
            $user->set_role('customer');

            // Associa o novo cliente ao pedido
            $order->set_customer_id($user_id);

            // Salva o pedido e verifica se ocorreu algum erro
            $result = $order->save();
            if (is_wp_error($result)) {
                error_log('Erro ao salvar o pedido: ' . $result->get_error_message());
            } else {
                error_log('Novo cliente criado e vinculado ao pedido: ' . $user_id);
            }
        } else {
            // Registra o erro no log e exibe uma mensagem de erro
            error_log('Erro ao criar cliente automaticamente: ' . $user_id->get_error_message());
            return;
        }
    }
    die('teste8');
}

// Usando hooks adequados para criação e atualização do pedido
// add_action('woocommerce_checkout_order_created', 'auto_create_or_assign_customer_to_order', 10, 1);
add_action('woocommerce_checkout_order_processed', 'auto_create_or_assign_customer_to_order', 10, 1);
// add_action('save_post_shop_order', 'auto_create_or_assign_customer_to_order', 10, 1);

function add_force_create_customer_order_action($actions, $order) {
    // Verifica se o pedido já tem um cliente associado
    if ($order->get_customer_id() == 0) {
        $actions['force_create_customer'] = __('Forçar Criação/Associação de Cliente', 'aireset-default');
    }
    return $actions;
}
add_filter('woocommerce_order_actions', 'add_force_create_customer_order_action', 10, 2);

function handle_force_create_customer() {
    // Verifica o nonce de segurança
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'force_create_customer')) {
        wp_die(__('Ação não permitida', 'aireset-default'));
    }

    if (!isset($_GET['post'])) {
        wp_die(__('ID do pedido não fornecido', 'aireset-default'));
    }

    $order_id = intval($_GET['post']);

    // Chama a função para criar ou associar o cliente
    auto_create_or_assign_customer_to_order($order_id);

    // Redireciona de volta para a página do pedido após executar a ação
    wp_redirect(wp_get_referer());
    exit;
}
add_action('admin_action_force_create_customer', 'handle_force_create_customer');



// // Adicionar botão na página de edição do pedido no admin
// add_action( 'woocommerce_order_actions', 'add_create_customer_button' );

// function add_create_customer_button( $actions ) {
//     global $theorder;

//     // Verificar se o pedido foi feito por um visitante (sem ID de usuário)
//     if ( $theorder->get_user_id() == 0 ) {
//         // Adicionar a ação "Criar Cliente" se for um visitante
//         $actions['create_customer'] = __( 'Criar Cliente e Vincular', 'woocommerce' );
//     }

//     return $actions;
// }

// Processar a ação ao clicar no botão
// add_action( 'woocommerce_order_action_create_customer', 'process_create_customer_from_order' );

// function process_create_customer_from_order( $order ) {
//     $billing_email = $order->get_billing_email();
//     $billing_first_name = $order->get_billing_first_name();
//     $billing_last_name = $order->get_billing_last_name();

//     // Verificar se já existe um usuário com o mesmo e-mail
//     if ( !email_exists( $billing_email ) ) {
//         // Gerar um nome de usuário baseado no e-mail
//         $username = sanitize_user( current( explode( '@', $billing_email ) ), true );

//         // Gerar uma senha aleatória
//         $password = wp_generate_password();

//         // Criar o novo usuário
//         $user_id = wp_create_user( $username, $password, $billing_email );

//         if ( !is_wp_error( $user_id ) ) {
//             // Atualizar as informações do usuário com os detalhes do pedido
//             wp_update_user( array(
//                 'ID' => $user_id,
//                 'first_name' => $billing_first_name,
//                 'last_name' => $billing_last_name
//             ));

//             // Atribuir o papel de "customer" (cliente)
//             $user = new WP_User( $user_id );
//             $user->set_role( 'customer' );

//             // Atualizar o pedido para vincular ao novo usuário
//             $order->set_customer_id( $user_id );
//             $order->save();

//             // Exibir uma mensagem de sucesso no admin
//             wc_add_notice( __( 'Cliente criado e vinculado ao pedido com sucesso!', 'woocommerce' ), 'success' );
//         } else {
//             // Exibir uma mensagem de erro no admin
//             wc_add_notice( __( 'Erro ao criar o cliente: ', 'woocommerce' ) . $user_id->get_error_message(), 'error' );
//         }
//     } else {
//         // Se o cliente já existir, vincule o pedido a ele
//         $user = get_user_by( 'email', $billing_email );
//         $order->set_customer_id( $user->ID );
//         $order->save();

//         // Exibir uma mensagem de sucesso no admin
//         wc_add_notice( __( 'Pedido vinculado ao cliente existente.', 'woocommerce' ), 'success' );
//     }
// }

