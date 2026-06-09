<?php

namespace {
    defined('ABSPATH') || exit;

    if (! function_exists('ensure_aireset_parent_menu')) {
        function ensure_aireset_parent_menu()
        {
            static $cleanup_hooked = false;
            global $admin_page_hooks, $menu, $submenu;

            $parent_exists = isset($admin_page_hooks['aireset']);

            if (! $parent_exists && is_array($menu)) {
                foreach ($menu as $menu_item) {
                    if (isset($menu_item[2]) && 'aireset' === $menu_item[2]) {
                        $parent_exists = true;
                        break;
                    }
                }
            }

            if ( ! $parent_exists ) {

                add_menu_page(
                    __( 'Aireset' ),
                    __( 'Aireset' ),
                    apply_filters( 'aireset_parent_menu_capability', 'read' ),
                    'aireset',
                    'render_aireset_parent_page',
                    'https://aireset.com.br/wp-content/logo_para_clientes/icone-preto.png',
                    3
                );
                $submenu['aireset'] = isset( $submenu['aireset'] ) ? $submenu['aireset'] : array();

                add_action( 'admin_head', 'aireset_parent_menu_icon_css' );
                function aireset_parent_menu_icon_css() {
                    echo '<style>#toplevel_page_aireset .wp-menu-image img{width:24px!important;height:auto!important;padding:6px 0 0!important;}</style>';
                }
                unset( $menu[4] );
            }

            if (! $cleanup_hooked) {
                $cleanup_hooked = true;
                add_action(
                    'admin_menu',
                    function () {
                        remove_submenu_page('aireset', 'aireset');
                    },
                    99999
                );
            }

            return $parent_exists;
        }
    }

    if (! function_exists('render_aireset_parent_page')) {
        function render_aireset_parent_page()
        {
            global $submenu;

            if (empty($submenu['aireset']) || ! is_array($submenu['aireset'])) {
                wp_die(esc_html__('Acesso negado.'));
            }

            foreach ($submenu['aireset'] as $item) {
                $page_slug  = isset($item[2]) ? (string) $item[2] : '';
                $capability = isset($item[1]) ? (string) $item[1] : 'read';

                if ('' === $page_slug || 'aireset' === $page_slug || ! current_user_can($capability)) {
                    continue;
                }

                wp_safe_redirect(admin_url('admin.php?page=' . $page_slug));
                exit;
            }

            wp_die(esc_html__('Acesso negado.'));
        }
    }
}

namespace Aireset\Default {
    defined('ABSPATH') || exit;

    class Admin_Page
    {

        const PAGE_SLUG = 'aireset-default';

        public static function get_default_view()
        {
            if (! class_exists(__NAMESPACE__ . '\\License') || ! License::is_valid()) {
                return 'license';
            }

            return 'general';
        }

        public static function get_views()
        {
            return array(
                'general' => array(
                    'label'     => __('Geral', 'aireset-default'),
                    'page_slug' => self::PAGE_SLUG,
                ),
                'business' => array(
                    'label'     => __('Dados da Empresa', 'aireset-default'),
                    'page_slug' => 'aireset-default-business',
                    'icon'      => 'dashicons-building',
                ),
                'frete' => array(
                    'label'     => __('Gerenciamento de Frete', 'aireset-default'),
                    'page_slug' => 'aireset-default-frete',
                    'icon'      => 'dashicons-location-alt',
                ),
                'docs' => array(
                    'label'     => __('Documentacao', 'aireset-default'),
                    'page_slug' => 'aireset-default-docs',
                    'icon'      => 'dashicons-media-document',
                ),
                'about' => array(
                    'label'     => __('Sobre', 'aireset-default'),
                    'page_slug' => 'aireset-default-about',
                    'icon'      => 'dashicons-info-outline',
                ),
                'license' => array(
                    'label'     => __('Licenca', 'aireset-default'),
                    'page_slug' => 'aireset_default_license',
                    'icon'      => 'dashicons-admin-network',
                ),
            );
        }

        public static function get_available_views()
        {
            if (! current_user_can('manage_options')) {
                return array();
            }

            if (! class_exists(__NAMESPACE__ . '\\License') || ! License::is_valid()) {
                return array('license');
            }

            return array('general', 'business', 'frete', 'docs', 'about', 'license');
        }

        public static function normalize_view($view)
        {
            $view            = sanitize_key((string) $view);
            $available_views = self::get_available_views();

            if (in_array($view, $available_views, true)) {
                return $view;
            }

            return self::get_default_view();
        }

        public static function get_current_view()
        {
            $view = isset($_GET['view']) ? wp_unslash($_GET['view']) : '';

            return self::normalize_view($view);
        }

        public static function is_current_view($view)
        {
            return self::get_current_view() === self::normalize_view($view);
        }

        public static function get_panel_classes($view)
        {
            $classes = 'nav-content';

            if (self::is_current_view($view)) {
                $classes .= ' active';
            }

            return $classes;
        }

        public static function get_view_url($view = '', $args = array())
        {
            $query = array('page' => self::PAGE_SLUG);

            if ('' !== $view) {
                $query['view'] = self::normalize_view($view);
            }

            if (! empty($args)) {
                $query = array_merge($query, $args);
            }

            return add_query_arg($query, admin_url('admin.php'));
        }

        public static function get_menu_highlight_slug()
        {
            $current_view = self::get_current_view();
            $views        = self::get_views();

            if (isset($views[$current_view]['page_slug'])) {
                return $views[$current_view]['page_slug'];
            }

            return self::PAGE_SLUG;
        }

        public static function register_page()
        {
            \ensure_aireset_parent_menu();

            add_submenu_page(
                'aireset',
                __('Aireset - Geral', 'aireset-default'),
                __('Geral', 'aireset-default'),
                'manage_options',
                self::PAGE_SLUG,
                array(__CLASS__, 'render_page')
            );

            foreach (array('business', 'frete', 'docs', 'about') as $view) {
                $definition = self::get_views()[$view];

                add_submenu_page(
                    'aireset',
                    $definition['label'],
                    $definition['label'],
                    'manage_options',
                    $definition['page_slug'],
                    static function () use ($view) {
                        self::redirect_to_view($view);
                    }
                );
            }
        }

        public static function enqueue_menu_flyout_assets()
        {
            if (! is_admin()) {
                return;
            }

            $plugin_url     = defined('AIRESET_DEFAULT_URL') ? (string) constant('AIRESET_DEFAULT_URL') : '';
            $plugin_path    = defined('AIRESET_DEFAULT_PATH') ? (string) constant('AIRESET_DEFAULT_PATH') : '';
            $plugin_version = defined('AIRESET_DEFAULT_VERSION') ? constant('AIRESET_DEFAULT_VERSION') : false;

            if ('' === $plugin_url) {
                return;
            }

            wp_enqueue_style(
                'aireset-admin-flyout',
                $plugin_url . 'assets/css/admin-menu-flyout.css',
                array(),
                file_exists($plugin_path . 'assets/css/admin-menu-flyout.css') ? (string) filemtime($plugin_path . 'assets/css/admin-menu-flyout.css') : $plugin_version
            );

            wp_enqueue_script(
                'aireset-admin-flyout',
                $plugin_url . 'assets/js/admin-menu-flyout.js',
                array(),
                file_exists($plugin_path . 'assets/js/admin-menu-flyout.js') ? (string) filemtime($plugin_path . 'assets/js/admin-menu-flyout.js') : $plugin_version,
                true
            );

            $views = self::get_views();
            $items = array(
                array(
                    'key'   => $views['business']['page_slug'],
                    'label' => $views['business']['label'],
                    'icon'  => $views['business']['icon'],
                    'url'   => self::get_view_url('business'),
                ),
                array(
                    'key'   => $views['frete']['page_slug'],
                    'label' => $views['frete']['label'],
                    'icon'  => $views['frete']['icon'],
                    'url'   => self::get_view_url('frete'),
                ),
                array(
                    'key'   => $views['docs']['page_slug'],
                    'label' => $views['docs']['label'],
                    'icon'  => $views['docs']['icon'],
                    'url'   => self::get_view_url('docs'),
                ),
                array(
                    'key'   => $views['about']['page_slug'],
                    'label' => $views['about']['label'],
                    'icon'  => $views['about']['icon'],
                    'url'   => self::get_view_url('about'),
                ),
                array(
                    'key'   => $views['license']['page_slug'],
                    'label' => $views['license']['label'],
                    'icon'  => $views['license']['icon'],
                    'url'   => self::get_view_url('license'),
                ),
            );

            $config = array(
                'currentPage' => self::get_menu_highlight_slug(),
                'anchorPage'  => self::PAGE_SLUG,
                'menuRoot'    => 'toplevel_page_aireset',
                'title'       => __('Geral', 'aireset-default'),
                'items'       => $items,
            );

            wp_add_inline_script(
                'aireset-admin-flyout',
                'window.airesetAdminFlyouts=window.airesetAdminFlyouts||[];'
                    . 'window.airesetAdminFlyouts.push(' . wp_json_encode($config) . ');',
                'before'
            );
        }

        public static function redirect_to_view($view)
        {
            if (! current_user_can('manage_options')) {
                wp_die(esc_html__('Acesso negado.', 'aireset-default'));
            }

            wp_safe_redirect(self::get_view_url($view));
            exit;
        }

        public static function render_page()
        {
            if (! current_user_can('manage_options')) {
                wp_die(esc_html__('Acesso negado.', 'aireset-default'));
            }

            $plugin_path = defined('AIRESET_DEFAULT_PATH') ? (string) constant('AIRESET_DEFAULT_PATH') : '';

            if ('' === $plugin_path) {
                wp_die(esc_html__('Arquivos do plugin indisponiveis.', 'aireset-default'));
            }

            include_once $plugin_path . 'includes/admin/settings.php';
        }
    }
}
