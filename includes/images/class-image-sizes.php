<?php

namespace Aireset\Default\Images;

use Aireset\Default\Init;

class ImageSizes {
    public function __construct() {
        if (Init::get_setting('aireset_default_images') === 'yes') {
            add_action('init', [$this, 'remove_default_image_sizes']);
            add_action('init', [$this, 'add_custom_image_sizes']);
        }
    }

    public function remove_default_image_sizes() {
        remove_image_size('thumbnail');
        remove_image_size('medium');
        remove_image_size('medium_large');
        remove_image_size('large');
        remove_image_size('1536x1536');
        remove_image_size('2048x2048');
        remove_image_size('2560×2560');
    }

    public function add_custom_image_sizes() {
        add_image_size('banner size', 1920, 600);
        add_image_size('banner mobile size', 1080, 1080);
        add_image_size('produto size', 500, 500);
        add_image_size('produto size table', 600, 600);
        add_image_size('produto size 2', 1080, 1080);
        add_image_size('micro banners size', 430, 260);
    }
}