<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }
    
    remove_image_size( 'thumbnail' );
    remove_image_size( 'medium' );
    remove_image_size( 'medium_large' );
    remove_image_size( 'large' );
    remove_image_size( '1536x1536' );
    remove_image_size( '2048x2048' );
    remove_image_size( '2560×2560' );

    add_image_size( 'banner size', 1920, 600 );
    add_image_size( 'banner mobile size', 1080, 1080 );
    add_image_size( 'produto size', 500, 500 );
    add_image_size( 'produto size table', 600, 600 );
    add_image_size( 'produto size 2', 1080, 1080 );
    add_image_size( 'micro banners size', 430, 260 );