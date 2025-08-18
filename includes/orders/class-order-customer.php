<?php

namespace Aireset\Default\Orders;

use Aireset\Default\Admin\AdminNotices;

class OrderCustomer {
    
    public function __construct() {
        if (\Aireset\Default\Init::get_setting('aireset_default_auto_create_or_assign_customer_to_order') === 'yes') {
            add_action('woocommerce_order_actions', [$this, 'add_create_customer_button']);
            add_action('woocommerce_order_action_create_customer', [$this, 'process_create_customer_from_order']);
            add_action('woocommerce_process_shop_order_meta', [$this, 'auto_create_or_assign_customer_to_order'], 10, 1);
        }
    }

    public function add_create_customer_button($actions) {
        $actions['create_customer'] = __('Cria/Atualizar Cliente e Vincular', 'woocommerce');
        return $actions;
    }

    public function process_create_customer_from_order($order) {
        $billing_email = $order->get_billing_email();
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();

        if (!email_exists($billing_email)) {
            $username = sanitize_user(current(explode('@', $billing_email)), true);
            $password = wp_generate_password();
            $user_id = wp_create_user($username, $password, $billing_email);

            if (!is_wp_error($user_id)) {
                wp_update_user([
                    'ID' => $user_id,
                    'first_name' => $billing_first_name,
                    'last_name' => $billing_last_name,
                ]);
                (new \WP_User($user_id))->set_role('customer');
            } else {
                $error = $user_id->get_error_message();
                AdminNotices::get_instance()->add_notice(
                    sprintf(__('Erro ao criar o cliente: %s', 'woocommerce'), $error),
                    'error'
                );

                wp_redirect(admin_url('post.php?post=' . $order->get_id() . '&action=edit'));
                exit;
            }
        } else {
            $user = get_user_by('email', $billing_email);
            $user_id = $user->ID;
        }

        // Atualiza todos os metadados do usuário com base no pedido
        $this->update_user_meta_from_order($user_id, $order);

        $order->set_customer_id($user_id);
        $order->save();

        AdminNotices::get_instance()->add_notice(
            __('Cliente criado/atualizado e vinculado ao pedido com sucesso!', 'woocommerce'),
            'success'
        );

        wp_redirect(admin_url('post.php?post=' . $order->get_id() . '&action=edit'));
        exit;
    }

    public function auto_create_or_assign_customer_to_order($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $billing_email = $order->get_billing_email();
        if (!$billing_email) {
            return;
        }

        $user = get_user_by('email', $billing_email);

        if ($user) {
            $user_id = $user->ID;
            $order->set_customer_id($user_id);
        } else {
            $username = sanitize_user(current(explode('@', $billing_email)));
            $password = wp_generate_password();
            $user_id = wc_create_new_customer($billing_email, $username, $password);

            if (is_wp_error($user_id)) {
                error_log('Erro ao criar cliente automaticamente: ' . $user_id->get_error_message());
                return;
            }
            $order->set_customer_id($user_id);
                
            // Aciona o e-mail de boas-vindas com a senha gerada
            $mailer = WC()->mailer();
            $new_account_email = $mailer->get_emails()['WC_Email_Customer_New_Account'];
            
            if ($new_account_email) {
                $new_account_email->trigger($user_id, $password, true);
            }
        }

        // Atualiza SEMPRE os dados do usuário com base no pedido
        $this->update_user_meta_from_order($user_id, $order);

        $order->save();
    }

    private function update_user_meta_from_order($user_id, $order) {
        $meta_fields = [
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'billing_phone' => $order->get_billing_phone(),
            'billing_address_1' => $order->get_billing_address_1(),
            'billing_address_2' => $order->get_billing_address_2(),
            'billing_city' => $order->get_billing_city(),
            'billing_postcode' => $order->get_billing_postcode(),
            'billing_state' => $order->get_billing_state(),
            'billing_country' => $order->get_billing_country(),
            'billing_company' => $order->get_billing_company(),
            'shipping_first_name' => $order->get_shipping_first_name(),
            'shipping_last_name' => $order->get_shipping_last_name(),
            'shipping_address_1' => $order->get_shipping_address_1(),
            'shipping_address_2' => $order->get_shipping_address_2(),
            'shipping_city' => $order->get_shipping_city(),
            'shipping_postcode' => $order->get_shipping_postcode(),
            'shipping_state' => $order->get_shipping_state(),
            'shipping_country' => $order->get_shipping_country(),
            'shipping_company' => $order->get_shipping_company(),
        ];

        foreach ($meta_fields as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }

        // Campos extras do plugin brasileiro
        if (function_exists('is_plugin_active') && is_plugin_active('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php')) {
            $extra_fields = [
                'billing_persontype' => '_billing_persontype',
                'billing_cpf' => '_billing_cpf',
                'billing_cnpj' => '_billing_cnpj',
                'billing_ie' => '_billing_ie',
                'billing_birthdate' => '_billing_birthdate',
                'billing_sex' => '_billing_sex',
                'billing_number' => '_billing_number',
                'billing_neighborhood' => '_billing_neighborhood',
                'shipping_number' => '_shipping_number',
                'shipping_neighborhood' => '_shipping_neighborhood'
            ];

            foreach ($extra_fields as $meta_key => $order_meta_key) {
                update_user_meta($user_id, $meta_key, $order->get_meta($order_meta_key));
            }
        }
    }
}