<?php

namespace Aireset\Default\Orders;

use Aireset\Default\Admin\AdminNotices;

class OrderCustomer {
    const VERIFY_UNLINKED_ORDERS_HOOK = 'aireset_default_verify_unlinked_orders_customer';

    public function __construct() {
        if (\Aireset\Default\Init::get_setting('aireset_default_auto_create_or_assign_customer_to_order') === 'yes') {
            add_action('woocommerce_order_actions', [$this, 'add_create_customer_button']);
            add_action('woocommerce_order_action_create_customer', [$this, 'process_create_customer_from_order']);
            add_action('woocommerce_process_shop_order_meta', [$this, 'auto_create_or_assign_customer_to_order'], 10, 1);
            add_action('woocommerce_checkout_order_created', [$this, 'auto_create_or_assign_customer_to_checkout_order'], 10, 1);
            add_action('init', [$this, 'schedule_unlinked_orders_verification']);
            add_action(self::VERIFY_UNLINKED_ORDERS_HOOK, [$this, 'verify_unlinked_orders']);
            add_action('woocommerce_process_shop_order_meta', [$this, 'maybe_clear_unlinked_skip_flag'], 5, 1);
        }
    }

    /**
     * Remove a flag _aireset_unlinked_skip quando o e-mail de cobrança é preenchido/atualizado no admin.
     */
    public function maybe_clear_unlinked_skip_flag($order_id) {
        $order = wc_get_order($order_id);

        if (!$order instanceof \WC_Order) {
            return;
        }

        if (!$order->meta_exists('_aireset_unlinked_skip')) {
            return;
        }

        $billing_email = sanitize_email($order->get_billing_email());

        if (!empty($billing_email) && is_email($billing_email)) {
            $order->delete_meta_data('_aireset_unlinked_skip');
            $order->save();
        }
    }

    public function add_create_customer_button($actions) {
        $actions['create_customer'] = __('Cria/Atualizar Cliente e Vincular', 'woocommerce');
        return $actions;
    }

    public function process_create_customer_from_order($order) {
        $result = $this->get_or_create_customer_from_order($order, false);

        if (is_wp_error($result)) {
            AdminNotices::get_instance()->add_notice(
                sprintf(__('Erro ao criar o cliente: %s', 'woocommerce'), $result->get_error_message()),
                'error'
            );

            wp_redirect(admin_url('post.php?post=' . $order->get_id() . '&action=edit'));
            exit;
        }

        $this->assign_customer_to_order($order, $result['user_id']);

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

        $result = $this->get_or_create_customer_from_order($order, true);

        if (is_wp_error($result)) {
            AdminNotices::get_instance()->add_notice(
                sprintf(__('Erro ao vincular o cliente ao pedido: %s', 'woocommerce'), $result->get_error_message()),
                'error'
            );

            error_log('Erro ao criar/vincular cliente automaticamente: ' . $result->get_error_message());
            return;
        }

        $this->assign_customer_to_order($order, $result['user_id']);
    }

    public function auto_create_or_assign_customer_to_checkout_order($order) {
        if (!$order instanceof \WC_Order) {
            return;
        }

        $result = $this->get_or_create_customer_from_order($order, true);

        if (is_wp_error($result)) {
            error_log('Erro ao criar/vincular cliente no checkout: ' . $result->get_error_message());
            return;
        }

        $this->assign_customer_to_order($order, $result['user_id']);
    }

    public function schedule_unlinked_orders_verification() {
        if (wp_next_scheduled(self::VERIFY_UNLINKED_ORDERS_HOOK)) {
            return;
        }

        wp_schedule_event(time() + MINUTE_IN_SECONDS * 5, 'hourly', self::VERIFY_UNLINKED_ORDERS_HOOK);
    }

    public function verify_unlinked_orders() {
        $batch_size = 50;
        $max_batches = 5;

        for ($batch = 0; $batch < $max_batches; $batch++) {
            $order_ids = wc_get_orders([
                'type' => 'shop_order',
                'customer_id' => 0,
                'limit' => $batch_size,
                'return' => 'ids',
                'orderby' => 'date',
                'order' => 'DESC',
                'meta_query' => [
                    [
                        'key' => '_aireset_unlinked_skip',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            ]);

            if (empty($order_ids)) {
                break;
            }

            foreach ($order_ids as $order_id) {
                $order = wc_get_order($order_id);

                if (!$order instanceof \WC_Order) {
                    continue;
                }

                $result = $this->get_or_create_customer_from_order($order, true);

                if (is_wp_error($result)) {
                    if ('aireset-invalid-email' === $result->get_error_code()) {
                        $order->update_meta_data('_aireset_unlinked_skip', 'no_email');
                        $order->save();
                        continue;
                    }

                    error_log('Erro ao verificar pedido sem cliente no cron: ' . $result->get_error_message());
                    continue;
                }

                $this->assign_customer_to_order($order, $result['user_id']);
            }
        }
    }

    private function get_or_create_customer_from_order($order, $send_new_account_email = false) {
        $billing_email = sanitize_email($order->get_billing_email());

        if (empty($billing_email) || !is_email($billing_email)) {
            return new \WP_Error('aireset-invalid-email', __('O pedido não possui um e-mail de cobrança válido.', 'woocommerce'));
        }

        $user = get_user_by('email', $billing_email);

        if ($user instanceof \WP_User) {
            return [
                'user_id' => (int) $user->ID,
                'created' => false,
            ];
        }

        $user = $this->find_existing_customer_by_document($order);

        if ($user instanceof \WP_User) {
            return [
                'user_id' => (int) $user->ID,
                'created' => false,
            ];
        }

        $password = wp_generate_password();
        $user_id = wc_create_new_customer(
            $billing_email,
            '',
            $password,
            [
                'first_name' => $order->get_billing_first_name(),
                'last_name'  => $order->get_billing_last_name(),
            ]
        );

        if (is_wp_error($user_id)) {
            return $user_id;
        }

        if ($send_new_account_email) {
            $this->trigger_new_account_email($user_id, $password);
        }

        return [
            'user_id' => (int) $user_id,
            'created' => true,
        ];
    }

    private function find_existing_customer_by_document($order) {
        $cpf = $this->normalize_document($order->get_meta('_billing_cpf'));
        $cnpj = $this->normalize_document($order->get_meta('_billing_cnpj'));

        if ($cpf) {
            $user = $this->find_user_by_normalized_meta('billing_cpf', $cpf);

            if ($user instanceof \WP_User) {
                return $user;
            }
        }

        if ($cnpj) {
            $user = $this->find_user_by_normalized_meta('billing_cnpj', $cnpj);

            if ($user instanceof \WP_User) {
                return $user;
            }
        }

        return null;
    }

    private function find_user_by_normalized_meta($meta_key, $document) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = %s
            AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(meta_value, '.', ''), '-', ''), '/', ''), ' ', ''), ',', '') = %s
            ORDER BY umeta_id ASC
            LIMIT 1",
            $meta_key,
            $document
        );

        $user_id = $wpdb->get_var($sql);

        if (!$user_id) {
            return null;
        }

        $user = get_user_by('id', (int) $user_id);

        return $user instanceof \WP_User ? $user : null;
    }

    private function normalize_document($value) {
        if (!is_scalar($value)) {
            return '';
        }

        return preg_replace('/\D+/', '', (string) $value);
    }

    private function assign_customer_to_order($order, $user_id) {
        $order->set_customer_id($user_id);
        $this->update_user_meta_from_order($user_id, $order);
        $order->save();
    }

    private function trigger_new_account_email($user_id, $password) {
        $mailer = WC()->mailer();
        $emails = $mailer ? $mailer->get_emails() : [];
        $new_account_email = isset($emails['WC_Email_Customer_New_Account']) ? $emails['WC_Email_Customer_New_Account'] : null;

        if ($new_account_email) {
            $new_account_email->trigger($user_id, $password, true);
        }
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
            $this->maybe_update_user_meta($user_id, $key, $value);
        }

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
                'shipping_neighborhood' => '_shipping_neighborhood',
            ];

            foreach ($extra_fields as $meta_key => $order_meta_key) {
                $this->maybe_update_user_meta($user_id, $meta_key, $order->get_meta($order_meta_key));
            }
        }
    }

    private function maybe_update_user_meta($user_id, $meta_key, $value) {
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === '' || $value === null) {
            return;
        }

        update_user_meta($user_id, $meta_key, $value);
    }
}
