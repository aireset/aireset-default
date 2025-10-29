<?php

namespace Aireset\Default\Permalinks;

use Aireset\Default\Init;

class PermalinkManager {
    public function __construct() {
        if (Init::get_setting('aireset_default_custom_permalinks') === 'yes') {
            add_filter('custom_permalinks_redundant_hyphens', [$this, 'redundant_hyphens']);
            add_filter('custom_permalink_before_saving', [$this, 'permalink_before_saving'], 10, 2);
        }
    }

    public function redundant_hyphens() {
        return true;
    }

    public function permalink_before_saving($permalink, $post_id) {
        // Checa se o permalink tem barra no final
        if (substr($permalink, -1) !== '/') {
            // Se não tiver barra no final, adiciona
            $permalink .= '/';
        }
        return $permalink;
    }
}