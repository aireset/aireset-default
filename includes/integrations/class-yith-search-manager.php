<?php

namespace Aireset\Default\Integrations;

use Aireset\Default\Init;

class YithSearchManager {
    public function __construct() {
        if (Init::get_setting('aireset_default_yith_wcas_submit_label') === 'yes') {
            add_filter('yith_wcas_submit_as_input', '__return_false');
            add_filter('yith_wcas_submit_label', [$this, 'customize_submit_label']);
        }
    }

    public function customize_submit_label($label) {
        return '' . $label;
    }
}