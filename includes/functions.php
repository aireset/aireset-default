<?php

use Aireset\Default\Core;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Remove filter/action declared by class
 * This function can remove hooks applied by classes that were declared without an accessible variable.
 * 
 * @since 3.3.0
 * @param string $hook_name | Hook name
 * @param string $method_name | Method name
 * @param int $priority | Priority
 * @link https://wordpress.stackexchange.com/a/304861
 * @return bool
 */
// function remove_filters_with_method_name( $hook_name = '', $method_name = '', $priority = 0 ) {
//     global $wp_filter;

//     // Take only filters on right hook name and priority
//     if ( ! isset( $wp_filter[$hook_name][$priority] ) || ! is_array( $wp_filter[$hook_name][$priority] ) ) {
//         return false;
//     }

//     // Loop on filters registered
//     foreach ( (array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array ) {
//         // Test if filter is an array ! (always for class/method)
//         if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
//             // Test if object is a class and method is equal to param !
//             if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
//                 // Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
//                 if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
//                     unset( $wp_filter[ $hook_name ]->callbacks[$priority][$unique_id] );
//                 } else {
//                     unset( $wp_filter[$hook_name][$priority][$unique_id] );
//                 }
//             }
//         }
//     }

//     return false;
// }


/**
 * Check if is admin links URL
 * 
 * @since 3.5.0
 * @version 3.7.0
 * @return bool
 */
function is_aireset_default_admin_settings() {
    $current_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $admin_page_url = admin_url('admin.php?page=aireset-default');
    
    if ( $current_url === $admin_page_url || strpos( $current_url, 'admin.php?page=aireset-default' ) !== false ) {
        return true;
    }

    return false;
}


/**
 * Check if is checkout must run on `wp` hook at the earliest.
 *
 * @since 1.0.0
 * @version 3.5.0
 * @param bool $force_early Force early check by getting the post ID from the URL.
 * @return bool
 */
function is_aireset_default( $force_early = false ) {
    if ( $force_early ) {
        $request_uri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $page_id = url_to_postid( home_url( $request_uri ) );

        return wc_get_page_id('checkout') === $page_id;
    }

    if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) {
        return false;
    }

    if ( is_checkout() ) {
        return true;
    }

    $wc_ajax = filter_input( INPUT_GET, 'wc-ajax', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

    if ( 'update_order_review' === $wc_ajax ) {
        return true;
    }

    $queried_object = get_queried_object();

    if ( is_wp_error( $queried_object ) || empty( $queried_object ) || ! isset( $queried_object->ID ) ) {
        return false;
    }

    $checkout_page_id = wc_get_page_id('checkout');

    return $checkout_page_id === $queried_object->ID && $queried_object->is_main_query();
}


/**
 * Check if is Flexify Checkout template
 *
 * @since 1.0.0
 * @version 3.5.0
 * @return bool
 */
function is_aireset_template() {
    return apply_filters( 'aireset_is_aireset_template', is_aireset_default() || Core::is_thankyou_page() || is_wc_endpoint_url('order-pay') );
}


/**
 * Check if the cart only contains virtual products
 *
 * @since 1.0.0
 * @version 3.5.2
 * @return bool
 */
function aireset_default_only_virtual() {
    $only_virtual = true;

    // Check if WooCommerce is initialized and the cart is available
    if ( ! function_exists('WC') || ! WC()->cart ) {
        return $only_virtual;
    }

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        // Check if there are non-virtual products.
        if ( ! $cart_item['data']->is_virtual() ) {
            $only_virtual = false;

            break;
        }
    }

    return $only_virtual;
}


/**
 * Check if there is a selected shipping method
 * 
 * @since 3.6.0
 * @param object $order | Order object
 * @return bool
 */
// function order_has_shipping_method( $order ) {
//     foreach ( $order->get_items() as $order_item ) {
//         $item = wc_get_product($order_item->get_product_id());

//         if ( ! $item->is_virtual() ) {
//             return true;
//         }
//     }

//     return false;
// }