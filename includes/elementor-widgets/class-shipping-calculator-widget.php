<?php

namespace Aireset\Default\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Aireset\Default\Init;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Shipping Calculator Widget
 *
 * @since 1.0.0
 */
class Shipping_Calculator_Widget extends Widget_Base {

    /**
     * Get widget name.
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'aireset_shipping_calculator';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Calculadora de Frete', 'aireset-default');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-delivery';
    }

    /**
     * Get widget categories.
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['aireset-default'];
    }

    /**
     * Get widget keywords.
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['shipping', 'calculator', 'frete', 'cep', 'entrega', 'aireset'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Conteúdo', 'aireset-default'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'info_text',
            [
                'label' => __('Texto informativo', 'aireset-default'),
                'type' => Controls_Manager::TEXT,
                'default' => Init::get_setting('aireset_default_text_info_before_input_shipping_calc') ?: __('Consultar prazo e valor da entrega', 'aireset-default'),
                'placeholder' => __('Consultar prazo e valor da entrega', 'aireset-default'),
            ]
        );

        $this->add_control(
            'placeholder_text',
            [
                'label' => __('Placeholder do campo CEP', 'aireset-default'),
                'type' => Controls_Manager::TEXT,
                'default' => Init::get_setting('aireset_default_text_placeholder_input_shipping_calc') ?: __('Informe seu CEP', 'aireset-default'),
                'placeholder' => __('Informe seu CEP', 'aireset-default'),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Texto do botão', 'aireset-default'),
                'type' => Controls_Manager::TEXT,
                'default' => Init::get_setting('aireset_default_text_button_shipping_calc') ?: __('Calcular', 'aireset-default'),
                'placeholder' => __('Calcular', 'aireset-default'),
            ]
        );

        $this->add_control(
            'show_cep_link',
            [
                'label' => __('Mostrar link "Não sei meu CEP"', 'aireset-default'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'aireset-default'),
                'label_off' => __('Não', 'aireset-default'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Estilo', 'aireset-default'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => __('Cor principal', 'aireset-default'),
                'type' => Controls_Manager::COLOR,
                'default' => Init::get_setting('aireset_default_primary_main_color') ?: '#000000',
                'selectors' => [
                    '{{WRAPPER}} .aireset-shipping-calc-button' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .aireset-postcode-search' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Cor do texto do botão', 'aireset-default'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .aireset-shipping-calc-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        
        // Only render on product pages or in Elementor editor
        if (!$is_editor && (!function_exists('is_product') || !is_product())) {
            return;
        }

        $settings = $this->get_settings_for_display();
        
        $info_text = $settings['info_text'];
        $placeholder = $settings['placeholder_text'];
        $button_text = $settings['button_text'];
        $show_link = $settings['show_cep_link'] === 'yes';

        ?>
        <div class="aireset-shipping-calc aireset-elementor-widget">
            <?php if (!empty($info_text)) : ?>
                <span class="aireset-info-shipping-calc"><?php echo esc_html($info_text); ?></span>
            <?php endif; ?>
            
            <div class="aireset-form-group">
                <input class="aireset-postcode" placeholder="<?php echo esc_attr($placeholder); ?>" name="aireset-postcode">
                <button class="aireset-shipping-calc-button">
                    <span class="aireset-shipping-calc-button-title"><?php echo esc_html($button_text); ?></span>
                </button>
            </div>
            
            <?php if ($show_link) : ?>
                <a class="aireset-postcode-search" href="https://buscacepinter.correios.com.br/app/endereco/" target="_blank"><?php echo esc_html__('Não sei meu CEP', 'aireset-default'); ?></a>
            <?php endif; ?>
            
            <div class="aireset-response"></div>
        </div>
        <?php
    }

    /**
     * Render widget output in the editor.
     */
    protected function content_template() {
        ?>
        <#
        var info_text = settings.info_text || '<?php echo esc_js(__('Consultar prazo e valor da entrega', 'aireset-default')); ?>';
        var placeholder = settings.placeholder_text || '<?php echo esc_js(__('Informe seu CEP', 'aireset-default')); ?>';
        var button_text = settings.button_text || '<?php echo esc_js(__('Calcular', 'aireset-default')); ?>';
        #>
        <div class="aireset-shipping-calc aireset-elementor-widget">
            <# if (info_text) { #>
                <span class="aireset-info-shipping-calc">{{{ info_text }}}</span>
            <# } #>
            
            <div class="aireset-form-group">
                <input class="aireset-postcode" placeholder="{{{ placeholder }}}" name="aireset-postcode">
                <button class="aireset-shipping-calc-button">
                    <span class="aireset-shipping-calc-button-title">{{{ button_text }}}</span>
                </button>
            </div>
            
            <# if (settings.show_cep_link === 'yes') { #>
                <a class="aireset-postcode-search" href="https://buscacepinter.correios.com.br/app/endereco/" target="_blank"><?php echo esc_html__('Não sei meu CEP', 'aireset-default'); ?></a>
            <# } #>
            
            <div class="aireset-response"></div>
        </div>
        <?php
    }
}
