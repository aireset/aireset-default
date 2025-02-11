<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// function yasglobal_redundant_hyphens() {
//   return true;
// }
// add_filter( 'custom_permalinks_redundant_hyphens', 'yasglobal_redundant_hyphens' );

// function yasglobal_permalink_before_saving( $permalink, $post_id ) {
//   // Check trialing slash in the permalink.
//   if ( substr( $permalink, -1 ) !== '/' ) {
//     // If permalink doesn't contain trialing slash then add one.
//     $permalink .= '/';
//   }

//   return $permalink;
// }
// add_filter( 'custom_permalink_before_saving', 'yasglobal_permalink_before_saving', 10, 2 );


if ( ! function_exists( 'aireset_custom_orders_list_column_content' ) ) {
    add_action( 'manage_shop_order_posts_custom_column', 'aireset_custom_orders_list_column_content', 50, 2 );

    function aireset_custom_orders_list_column_content( $column, $post_id ) {
        if ( $column === 'order_number' ) {
            global $the_order;

            if ( $phone = $the_order->get_billing_phone() ) {
                // Escape phone number and other data
                $escaped_phone = esc_html( $phone );
                echo '<br>' . esc_html__( 'Celular:', 'aireset-default' ) . ' <a href="tel:' . esc_attr( $escaped_phone ) . '" target="_blank">' . esc_html( $escaped_phone ) . '</a>';

                $status = wc_get_order_status_name( $the_order->get_status() );
                // Added translator comments and reordered placeholders
                $text = urlencode( sprintf( 
                    /* translators: %1$s: Customer's first name, %2$d: Order ID, %3$s: Order status */
                    esc_html__( "Olá %1\$s, tudo bem?\nVimos que você fez o pedido %2\$d que está como %3\$s. Conte para nós como podemos te ajudar?", 'aireset-default' ), 
                    $the_order->get_billing_first_name(), 
                    $the_order->get_id(), 
                    $status 
                ));

                echo '<br>' . esc_html__( 'Whatsapp:', 'aireset-default' ) . ' <a href="https://wa.me/55' . esc_attr( preg_replace( "/[^0-9]/", "", $escaped_phone ) ) . '?text=' . esc_attr( $text ) . '" target="_blank">' . esc_html( $escaped_phone ) . '</a>';
            }

            if ( $email = $the_order->get_billing_email() ) {
                // Escape email address
                $escaped_email = esc_html( $email );
                echo '<br>' . esc_html__( 'Email:', 'aireset-default' ) . ' <a href="mailto:' . esc_attr( $escaped_email ) . '" target="_blank">' . esc_html( $escaped_email ) . '</a>';
            }
        }
    }
}
