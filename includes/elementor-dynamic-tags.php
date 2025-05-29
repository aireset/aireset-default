<?php
namespace Aireset\Default\Elementor;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Classe para tags de texto dinâmicas do Aireset no Elementor
 */
class Aireset_Dynamic_Text_Tag extends Data_Tag {

    public function get_name() {
        return 'aireset_dynamic_text_tag';
    }

    public function get_title() {
        return __('Aireset - Texto Dinâmico', 'aireset-default');
    }

    public function get_group() {
        return 'site';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function register_controls() {
        // Campo de Seleção da Opção
        $this->add_control(
            'option_key',
            [
                'label' => __('Selecionar Campo', 'aireset-default'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'email_contato' => __('Email de Contato', 'aireset-default'),
                    'cep' => __('Endereço (CEP)', 'aireset-default'),
                    'endereco' => __('Endereço Completo', 'aireset-default'),
                    'telefone' => __('Telefone', 'aireset-default'),
                    'whatsapp' => __('WhatsApp', 'aireset-default'),
                    'facebook' => __('Facebook', 'aireset-default'),
                    'instagram' => __('Instagram', 'aireset-default'),
                    'linkedin' => __('LinkedIn', 'aireset-default'),
                    'tiktok' => __('TikTok', 'aireset-default'),
                    'horario_aberto' => __('Horário Aberto', 'aireset-default'),
                    'cnpj' => __('CNPJ', 'aireset-default'),
                ],
            ]
        );

        // Campo Before (Antes)
        $this->add_control(
            'before_text',
            [
                'label' => __('Texto Antes', 'aireset-default'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Exemplo: Contato: ', 'aireset-default'),
            ]
        );

        // Campo After (Depois)
        $this->add_control(
            'after_text',
            [
                'label' => __('Texto Depois', 'aireset-default'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Exemplo: (Disponível 24h)', 'aireset-default'),
            ]
        );
    }

    public function get_value(array $options = []) {
        $option_key = $this->get_settings_for_display('option_key');
        $before = $this->get_settings_for_display('before_text');
        $after = $this->get_settings_for_display('after_text');

        if (!empty($option_key)) {
            $value = get_option($option_key, '');
            return $before . $value . $after;
        }

        return '';
    }
}

/**
 * Classe para tags de URL dinâmicas do Aireset no Elementor
 */
class Aireset_Dynamic_URL_Tag extends Tag {

    public function get_name() {
        return 'aireset_dynamic_url_tag';
    }

    public function get_title() {
        return __('Aireset - URL Dinâmica', 'aireset-default');
    }

    public function get_group() {
        return 'site';
    }

    public function get_categories() {
        return [Module::URL_CATEGORY];
    }

    protected function register_controls() {
        $this->add_control(
            'option_key',
            [
                'label' => __('Selecionar Link', 'aireset-default'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'whatsapp_link' => __('WhatsApp Link', 'aireset-default'),
                    'facebook_link' => __('Facebook Link', 'aireset-default'),
                    'instagram_link' => __('Instagram Link', 'aireset-default'),
                    'linkedin_link' => __('LinkedIn Link', 'aireset-default'),
                    'tiktok_link' => __('TikTok Link', 'aireset-default'),
                ],
            ]
        );
    }

    public function get_value(array $options = []) {
        $option_key = $this->get_settings_for_display('option_key');

        if (!empty($option_key)) {
            return get_option($option_key, '');
        }

        return '';
    }
}
