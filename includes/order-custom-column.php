<?php

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

/*
if(!function_exists('custom_orders_list_column_content')){
    add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 50, 2 );
    
    function custom_orders_list_column_content( $column, $post_id ) {
        if ( $column == 'order_number' )
        {
            global $the_order;
    
            if( $phone = $the_order->get_billing_phone() ){
                $phone_wp_dashicon = '<span class="dashicons dashicons-phone"></span> ';
                echo '<br>Celular: '.'<a href="tel:'.$phone.'" target="_blank">' . $phone.'</a></strong>';
                $status = wc_get_order_status_name($the_order->get_status());
                $text = urlencode("Olá ". $the_order->get_billing_first_name() .", tudo bem? ". PHP_EOL ."Vimos que você fez o pedido ". $the_order->get_id() ." que está como ". $status . ", conte para nós como podemos te ajudar?");
                echo '<br>Whatsapp: '.'<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "",$phone).'?text='. $text .'" target="_blank">' . $phone.'</a></strong>';
            }
    
            if( $email = $the_order->get_billing_email() ){
                echo '<br>Email: '.'<a href="mailto:'.$email.'" target="_blank">' . $email . '</a>';
            }
        }
    }
}


*/