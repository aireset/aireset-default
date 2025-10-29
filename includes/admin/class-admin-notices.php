<?php

namespace Aireset\Default\Admin;

class AdminNotices {
    private static $instance = null;
    private $notices = [];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_notices', [$this, 'display_notices']);
    }

    public function add_notice($message, $type = 'info', $expiration = 45) {
        set_transient('aireset_admin_notice', [
            'type' => $type,
            'message' => $message
        ], $expiration);
    }

    public function display_notices() {
        $notice = get_transient('aireset_admin_notice');
        if ($notice) {
            printf(
                '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
                esc_attr($notice['type']),
                esc_html($notice['message'])
            );
            delete_transient('aireset_admin_notice');
        }
    }
}