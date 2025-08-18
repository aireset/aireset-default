<?php

namespace Aireset\Default\Frontend;

use Aireset\Default\Init;

class ViewportManager {
    
    public function __construct() {
        if (Init::get_setting('aireset_default_fixed_viewport') === 'yes') {
            add_action('wp_head', [$this, 'disable_mobile_zoom'], 0);
        }
    }

    public function disable_mobile_zoom() {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
    }
}