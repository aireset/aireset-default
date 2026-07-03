<?php

namespace Aireset\Default;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Carrega a classe base do SDK (sem namespace).
require_once __DIR__ . '/class-aireset-license-base.php';

/**
 * Gerenciador de licença do Aireset Default via Elite Licenser.
 *
 * @since 1.4.0
 */
class License
{

    /** @var string */
    public $plugin_file;

    /** @var object|null */
    public $response_obj;

    /** @var string */
    public $license_message = '';

    /** @var bool */
    public $show_message = false;

    /** @var string */
    public $slug = 'aireset';

    /** @var string */
    public $license_page_slug = 'aireset_default_license';

    /** @var string */
    public $plugin_page_slug = 'aireset-default';

    /** @var string */
    public $plugin_version = '';

    /** @var string */
    public $text_domain = 'aireset-default';

    /** @var bool */
    private $is_valid = false;

    /**
     * @param string $plugin_base_file Caminho absoluto do arquivo principal do plugin.
     */
    public function __construct($plugin_base_file = '')
    {
        if (empty($plugin_base_file)) {
            $plugin_base_file = defined('AIRESET_DEFAULT_FILE') ? AIRESET_DEFAULT_FILE : '';
        }

        $this->plugin_file = $plugin_base_file;

        add_action('admin_print_styles', [$this, 'set_admin_style']);
        $this->set_plugin_data();

        $main_lic_key = 'Aireset-Geral_lic_Key';
        $lic_key_name = \Aireset_Default_License_Base::get_lic_key_param($main_lic_key);
        $license_key  = get_option($lic_key_name, '');

        // Migração de chave legada.
        if (empty($license_key)) {
            $license_key = get_option($main_lic_key, '');
            if (! empty($license_key)) {
                update_option($lic_key_name, $license_key);
                update_option($main_lic_key, '');
            }
        }

        $lic_email = get_option('Aireset-Geral_lic_email', '');

        \Aireset_Default_License_Base::add_on_delete(function () {
            update_option('Aireset-Geral_lic_Key', '');
        });

        if (\Aireset_Default_License_Base::check_wp_plugin($license_key, $lic_email, $this->license_message, $this->response_obj, $this->plugin_file)) {
            $this->is_valid = true;
            add_action('admin_menu', [$this, 'active_admin_menu'], 99999);
            add_action('admin_post_Aireset-Geral_el_deactivate_license', [$this, 'action_deactivate_license']);
        } else {
            if (! empty($license_key) && ! empty($this->license_message)) {
                $this->show_message = true;
            }
            update_option($lic_key_name, '');
            add_action('admin_post_Aireset-Geral_el_activate_license', [$this, 'action_activate_license']);
            add_action('admin_menu', [$this, 'inactive_menu'], 99999);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_inactive_flyout_config']);
        }
    }

    /**
     * Retorna se a licença está válida e ativa.
     *
     * @return bool
     */
    public static function is_valid()
    {
        $instance = self::get_instance();
        if ($instance) {
            return $instance->is_valid;
        }
        return false;
    }

    /** @var self|null */
    private static $self_obj;

    /**
     * @param string $plugin_base_file
     * @return self|null
     */
    public static function &get_instance($plugin_base_file = null)
    {
        if (empty(self::$self_obj)) {
            if (! empty($plugin_base_file)) {
                self::$self_obj = new self($plugin_base_file);
            }
        }
        return self::$self_obj;
    }

    public function set_plugin_data()
    {
        if (! function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $data = get_plugin_data($this->plugin_file, false, false);
        if (isset($data['Version'])) {
            $this->plugin_version = $data['Version'];
        }
        if (isset($data['TextDomain'])) {
            $this->text_domain = $data['TextDomain'];
        }
    }

    public function set_admin_style()
    {
        wp_register_style(
            'aireset-default-lic',
            plugins_url('includes/classes/aireset-license.css', $this->plugin_file),
            [],
            $this->plugin_version
        );
        wp_enqueue_style('aireset-default-lic');
    }

    public function active_admin_menu()
    {
        \ensure_aireset_parent_menu();
        remove_submenu_page($this->slug, $this->license_page_slug);

        add_submenu_page(
            $this->slug,
            __('Licença', $this->text_domain),
            __('Licença', $this->text_domain),
            'manage_options',
            $this->license_page_slug,
            [$this, 'render_active_page']
        );
    }

    public function inactive_menu()
    {
        \ensure_aireset_parent_menu();

        remove_submenu_page($this->slug, $this->plugin_page_slug);
        remove_submenu_page($this->slug, $this->license_page_slug);

        add_submenu_page(
            $this->slug,
            __('Geral', $this->text_domain),
            __('Geral', $this->text_domain),
            'manage_options',
            $this->plugin_page_slug,
            [$this, 'license_form']
        );

        add_submenu_page(
            $this->slug,
            __('Licença', $this->text_domain),
            __('Licença', $this->text_domain),
            'manage_options',
            $this->license_page_slug,
            [$this, 'license_form']
        );
    }

    public function enqueue_inactive_flyout_config()
    {
        if (! is_admin()) {
            return;
        }

        wp_enqueue_style(
            'aireset-admin-flyout',
            plugins_url('assets/css/admin-menu-flyout.css', $this->plugin_file),
            [],
            $this->plugin_version
        );

        wp_enqueue_script(
            'aireset-admin-flyout',
            plugins_url('assets/js/admin-menu-flyout.js', $this->plugin_file),
            [],
            $this->plugin_version,
            true
        );

        $current_page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

        $config = [
            'currentPage' => $current_page,
            'anchorPage'  => $this->plugin_page_slug,
            'menuRoot'    => 'toplevel_page_aireset',
            'title'       => __('Geral', $this->text_domain),
            'items'       => [
                [
                    'key'   => $this->license_page_slug,
                    'label' => __('Licença', $this->text_domain),
                    'icon'  => 'dashicons-admin-network',
                    'url'   => admin_url('admin.php?page=' . $this->license_page_slug),
                ],
            ],
        ];

        wp_add_inline_script(
            'aireset-admin-flyout',
            'window.airesetAdminFlyouts=window.airesetAdminFlyouts||[];'
            . 'window.airesetAdminFlyouts.push(' . wp_json_encode($config) . ');',
            'before'
        );

        wp_add_inline_style(
            'aireset-admin-flyout',
            '#adminmenu .wp-submenu li:has(> a[href*="page=' . esc_attr($this->license_page_slug) . '"]) { display: none !important; }'
        );
    }

    public function action_activate_license()
    {
        check_admin_referer('el-license');

        $license_key   = ! empty($_POST['el_license_key']) ? sanitize_text_field(wp_unslash($_POST['el_license_key'])) : '';
        $license_email = ! empty($_POST['el_license_email']) ? sanitize_email(wp_unslash($_POST['el_license_email'])) : '';

        $main_lic_key = 'Aireset-Geral_lic_Key';
        $lic_key_name = \Aireset_Default_License_Base::get_lic_key_param($main_lic_key);

        update_option($lic_key_name, $license_key);
        update_option('Aireset-Geral_lic_email', $license_email);
        update_option('_site_transient_update_plugins', '');

        $redirect_url = admin_url('admin.php?page=' . $this->plugin_page_slug . '&view=license');

        if (class_exists(__NAMESPACE__ . '\\Admin_Page')) {
            $redirect_url = Admin_Page::get_view_url('license');
        }

        wp_safe_redirect($redirect_url);
        exit;
    }

    public function action_deactivate_license()
    {
        check_admin_referer('el-license');

        $message      = '';
        $main_lic_key = 'Aireset-Geral_lic_Key';
        $lic_key_name = \Aireset_Default_License_Base::get_lic_key_param($main_lic_key);

        if (\Aireset_Default_License_Base::remove_license_key($this->plugin_file, $message)) {
            update_option($lic_key_name, '');
            update_option('_site_transient_update_plugins', '');
        }

        wp_safe_redirect(admin_url('admin.php?page=' . $this->plugin_page_slug));
        exit;
    }

    public function render_active_page()
    {
        if (class_exists(__NAMESPACE__ . '\\Admin_Page')) {
            wp_safe_redirect(Admin_Page::get_view_url('license'));
            exit;
        }

        $this->activated();
    }

    /**
     * Tela quando a licença está ativa.
     */
    public function activated()
    {
?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="Aireset-Geral_el_deactivate_license" />
            <div class="el-license-container">
                <h3 class="el-license-title">
                    <i class="dashicons-before dashicons-star-filled"></i>
                    <?php esc_html_e('Aireset Geral — Informações da Licença', 'aireset-default'); ?>
                </h3>
                <hr>
                <ul class="el-license-info">
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php esc_html_e('Status', 'aireset-default'); ?></span>
                            <?php if ($this->response_obj->is_valid) : ?>
                                <span class="el-license-valid"><?php esc_html_e('Válida', 'aireset-default'); ?></span>
                            <?php else : ?>
                                <span class="el-license-invalid"><?php esc_html_e('Inválida', 'aireset-default'); ?></span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php esc_html_e('Tipo de Licença', 'aireset-default'); ?></span>
                            <?php echo esc_html($this->response_obj->license_title); ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php esc_html_e('Expira em', 'aireset-default'); ?></span>
                            <?php
                            echo esc_html($this->response_obj->expire_date);
                            if (! empty($this->response_obj->expire_renew_link)) {
                            ?>
                                <a target="_blank" class="el-blue-btn" href="<?php echo esc_url($this->response_obj->expire_renew_link); ?>">
                                    <?php esc_html_e('Renovar', 'aireset-default'); ?>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php esc_html_e('Suporte até', 'aireset-default'); ?></span>
                            <?php
                            echo esc_html($this->response_obj->support_end);
                            if (! empty($this->response_obj->support_renew_link)) {
                            ?>
                                <a target="_blank" class="el-blue-btn" href="<?php echo esc_url($this->response_obj->support_renew_link); ?>">
                                    <?php esc_html_e('Renovar', 'aireset-default'); ?>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php esc_html_e('Chave de Licença', 'aireset-default'); ?></span>
                            <span class="el-license-key">
                                <?php echo esc_html(substr($this->response_obj->license_key, 0, 9) . 'XXXXXXXX-XXXXXXXX' . substr($this->response_obj->license_key, -9)); ?>
                            </span>
                        </div>
                    </li>
                </ul>
                <div class="el-license-active-btn">
                    <?php wp_nonce_field('el-license'); ?>
                    <?php submit_button(__('Desativar Licença', 'aireset-default')); ?>
                </div>
            </div>
        </form>
    <?php
    }

    /**
     * Formulário de ativação de licença.
     */
    public function license_form()
    {
    ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="Aireset-Geral_el_activate_license" />
            <div class="el-license-container">
                <h3 class="el-license-title">
                    <i class="dashicons-before dashicons-star-filled"></i>
                    <?php esc_html_e('Aireset Geral — Ativação de Licença', 'aireset-default'); ?>
                </h3>
                <hr>
                <?php if (! empty($this->show_message) && ! empty($this->license_message)) : ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo esc_html($this->license_message); ?></p>
                    </div>
                <?php endif; ?>
                <p><?php esc_html_e('Insira sua chave de licença e e-mail para ativar o plugin.', 'aireset-default'); ?></p>
                <div class="el-license-field">
                    <label for="el_license_key"><?php esc_html_e('Chave de Licença', 'aireset-default'); ?></label>
                    <input type="text" class="regular-text" name="el_license_key" id="el_license_key"
                        placeholder="<?php esc_attr_e('xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx', 'aireset-default'); ?>">
                </div>
                <div class="el-license-field">
                    <label for="el_license_email"><?php esc_html_e('E-mail', 'aireset-default'); ?></label>
                    <input type="email" class="regular-text" name="el_license_email" id="el_license_email"
                        placeholder="<?php esc_attr_e('seu@email.com', 'aireset-default'); ?>">
                </div>
                <div class="el-license-active-btn">
                    <?php wp_nonce_field('el-license'); ?>
                    <?php submit_button(__('Ativar Licença', 'aireset-default')); ?>
                </div>
            </div>
        </form>
<?php
    }

    /**
     * Compatibilidade: get_domain estático para código legado.
     *
     * @return string
     */
    public static function get_domain()
    {
        if (function_exists('site_url')) {
            return site_url();
        }
        if (defined('WPINC') && function_exists('get_bloginfo')) {
            return get_bloginfo('url');
        }
        return '';
    }
}

// Inicializa apenas se o arquivo principal do plugin já definiu a constante.
if (defined('AIRESET_DEFAULT_FILE')) {
    License::get_instance(AIRESET_DEFAULT_FILE);
}

if (! class_exists('Aireset\Default\License\License')) {
    class_alias('Aireset\Default\License', 'Aireset\Default\License\License');
}
