<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aireset_yith_wcas_submit_label' ) ) {
    function aireset_yith_wcas_submit_label( $label ) { 
        return '' . $label; 
    }

    add_filter( 'yith_wcas_submit_as_input', '__return_false' );
    add_filter( 'yith_wcas_submit_label', 'aireset_yith_wcas_submit_label' );
}