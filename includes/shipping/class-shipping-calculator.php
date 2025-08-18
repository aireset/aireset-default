<?php

namespace Aireset\Default\Shipping;

class ShippingCalculator {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_admin_shipping_calculate', [$this, 'calculate_shipping']);
        add_filter('woocommerce_package_rates', [$this, 'adjust_shipping_rates'], 10, 2);
    }

    public function enqueue_admin_scripts() {
        global $pagenow;

        if(is_admin() 
            && in_array($pagenow, ['post.php', 'post-new.php'])
            && ($_GET['post_type'] ?: 'shop_order') === 'shop_order') 
        {
            wp_enqueue_script('shipping-calc_js', plugins_url('admin/js/shipping-calc.js', AIRESET_DEFAULT_FILE), array('jquery'), AIRESET_DEFAULT_VERSION);
            wp_localize_script('shipping-calc_js', 'shipping_calc', array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('admin_shipping_calculate')
            ));
        }
    }

    public function calculate_shipping() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'admin_shipping_calculate')) {
            wp_send_json_error();
        }

        $products = isset($_POST['products']) ? (array) $_POST['products'] : array();
        $orderItems = array_map(function($productId) {
            return new \WC_Order_Item_Product((int) $productId);
        }, $products);

        $cart = \WC()->cart;
        $cart->set_cart_contents(array());
        foreach($orderItems as $orderItem) {
            $cart->add_to_cart($orderItem->get_product_id(), $orderItem->get_quantity());
        }

        $package = [
            'destination' => [
                'country' => isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '',
                'state' => isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '',
                'postcode' => isset($_POST['postcode']) ? sanitize_text_field($_POST['postcode']) : ''
            ],
            'contents' => array_map(function($orderItem) {
                return [
                    'quantity' => (int) $orderItem->get_quantity(),
                    'data' => $orderItem->get_product(),
                    'line_total' => $orderItem->get_total(),
                    'line_tax' => $orderItem->get_total_tax(),
                    'line_subtotal' => $orderItem->get_subtotal(),
                    'line_subtotal_tax' => $orderItem->get_subtotal_tax()
                ];
            }, $orderItems),
            'contents_cost' => array_sum(array_map(function ($orderItem) {
                return $orderItem->get_total();
            }, $orderItems))
        ];

        $shippingZone = \WC_Shipping_Zones::get_zone_matching_package($package);
        $shippingMethods = $shippingZone->get_shipping_methods(true);

        $prices = array();
        foreach($shippingMethods as $shippingMethod) {
            $rates = $shippingMethod->get_rates_for_package($package);
            foreach($rates as $rate) {
                $prices[] = [
                    'id' => wp_kses($rate->get_id(), array()),
                    'method' => wp_kses($rate->get_method_id(), array()),
                    'total' => (float) $rate->get_cost(),
                    'tax' => (float) (is_array($rate->get_shipping_tax()) ? array_sum($rate->get_shipping_tax())
                        : $rate->get_shipping_tax())
                ];
            }
        }

        wp_send_json_success([
            'shipping' => $prices
        ]);
    }

    public function adjust_shipping_rates($rates, $package) {
        // Define a data inicial (hoje)
        $start_date = new \DateTime();
        // Recupera os métodos ativos da zona de envio
        $zone = \WC_Shipping_Zones::get_zone_matching_package($package);
        $active_methods = [];
        foreach ($zone->get_shipping_methods() as $method) {
            if ('yes' === $method->enabled) {
                $active_methods[] = $method;
            }
        }
        $active_method_ids = wp_list_pluck($active_methods, 'id');

        // Itera pelos rates para ajustar o prazo de entrega
        foreach ($rates as $rate_id => $rate) {
            if (!in_array($rate->method_id, $active_method_ids)) {
                continue;
            }

            $meta_data = $rate->get_meta_data();
            $delivery_time_text = '';
            foreach ($meta_data as $key => $meta) {
                if ($key === 'delivery_time') {
                    $delivery_time_text = $meta;
                    break;
                }
            }

            if (!empty($delivery_time_text)) {
                $clean = trim(str_replace(['dias úteis', 'dias corridos', 'dias'], '', $delivery_time_text), '() ');
                
                if (preg_match('/(\d+)\s*a\s*(\d+)/', $clean, $matches)) {
                    $base_min = intval($matches[1]);
                    $base_max = intval($matches[2]);
                    
                    $count_additional_days = function($base_days) use ($start_date) {
                        $end_date = clone $start_date;
                        $end_date->modify("+{$base_days} days");
                        $additional = 0;
                        
                        $interval = new \DateInterval('P1D');
                        $period = new \DatePeriod($start_date, $interval, $end_date);
                        foreach ($period as $dt) {
                            if (in_array($dt->format('N'), [6,7])) {
                                $additional++;
                            }
                        }
                        
                        $year = $start_date->format('Y');
                        $transient_key = 'custom_holidays_' . $year;
                        $holidays = get_transient($transient_key);
                        if (false === $holidays) {
                            $response = wp_remote_get("https://brasilapi.com.br/api/feriados/v1/{$year}");
                            if (!is_wp_error($response)) {
                                $holidays = json_decode(wp_remote_retrieve_body($response), true);
                            } else {
                                $holidays = [];
                            }
                            set_transient($transient_key, $holidays, DAY_IN_SECONDS);
                        }
                        foreach ($holidays as $holiday) {
                            if (isset($holiday['date'])) {
                                $holiday_date = \DateTime::createFromFormat('Y-m-d', $holiday['date']);
                                if ($holiday_date && $holiday_date >= $start_date && $holiday_date < $end_date) {
                                    $additional++;
                                }
                            }
                        }
                        return $additional;
                    };
            
                    $additional_min = $count_additional_days($base_min);
                    $additional_max = $count_additional_days($base_max);
            
                    $adjusted_min = $base_min + $additional_min;
                    $adjusted_max = $base_max + $additional_max;
            
                    $original_label = $rates[$rate_id]->get_label();
                    $original_label = preg_replace('/\s*\(.*?\)$/', '', $original_label);
                    $new_label = sprintf(__('Entrega em aproximadamente de %s a %s dias', 'aireset-default'), $adjusted_min, $adjusted_max);
                    
                    $rates[$rate_id]->set_label($original_label . ' (' . $new_label . ')');
                    $rate->add_meta_data('delivery_time', sprintf('%s a %s dias', $adjusted_min, $adjusted_max));
                }
            }
        }
        return $rates;
    }
}