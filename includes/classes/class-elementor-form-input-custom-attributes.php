<?php

/**
 * Plugin Name: Elementor Form Custom Attributes
 * Plugin URI: https://github.com/your-repo/elementor-form-custom-attributes
 * Description: Adds a custom attributes field to Elementor form fields.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://github.com/your-repo
 * License: GPLv3 or later
 */

class Elementor_Forms_Custom_Attributes {

    public $allowed_fields = [
        'select',
        'text',
        'tel',
        'date',
        'datetime',
        'email',
        'url',
        'password',
    ];

    public function __construct() {
        // Add custom attributes to form field render
        add_filter( 'elementor_pro/forms/render/item', [ $this, 'maybe_add_custom_attributes' ], 10, 3 );

        // Add custom attributes field control in Elementor form fields
        add_action( 'elementor/element/form/section_form_fields/before_section_end', [ $this, 'add_custom_attributes_field_control' ], 100, 2 );
    }

    /**
     * Adiciona o campo de atributos customizados nos controles do Elementor
     * @param $element
     * @param $args
     */
    public function add_custom_attributes_field_control( $element, $args ) {
        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'form_fields' );

        if ( is_wp_error( $control_data ) ) {
            return;
        }

        // Cria um novo controle de atributos customizados
        $tmp = new Elementor\Repeater();
        $tmp->add_control(
            'field_custom_attributes',
            [
                'label' => 'Atributos Customizados',
                'inner_tab' => 'form_fields_advanced_tab',
                'tab' => 'content',
                'tabs_wrapper' => 'form_fields_tabs',
                'type' => 'textarea',
                'description' => 'Adicione atributos HTML personalizados no campo, um por linha (ex.: data-cep="true").',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => 'in',
                            'value' => $this->allowed_fields,
                        ],
                    ],
                ],
            ]
        );

        $pattern_field = $tmp->get_controls();
        $pattern_field = $pattern_field['field_custom_attributes'];

        // Insere o novo campo antes do controle de ID Personalizado
        $new_order = [];
        foreach ( $control_data['fields'] as $field_key => $field ) {
            if ( 'custom_id' === $field['name'] ) {
                $new_order['field_custom_attributes'] = $pattern_field;
            }
            $new_order[ $field_key ] = $field;
        }
        $control_data['fields'] = $new_order;

        $element->update_control( 'form_fields', $control_data );
    }

    /**
     * Adiciona os atributos customizados ao campo no frontend
     * @param $field
     * @param $field_index
     * @param $form_widget
     * @return array
     */
    public function maybe_add_custom_attributes( $field, $field_index, $form_widget ) {
        if ( ! empty( $field['field_custom_attributes'] ) && in_array( $field['field_type'], $this->allowed_fields ) ) {
            // Processa os atributos linha por linha
            $attributes = explode( "\n", $field['field_custom_attributes'] );
            foreach ( $attributes as $attribute ) {
                $attribute = trim( $attribute );
                if ( strpos( $attribute, '=' ) !== false ) {
                    list( $attr_name, $attr_value ) = explode( '=', $attribute, 2 );
                    $attr_name = trim( $attr_name );
                    $attr_value = trim( $attr_value, '"\''); // Remove aspas ao redor do valor
                    // Adiciona o atributo ao render correto
                    if ( $field['field_type'] === 'select' ) {
                        $form_widget->add_render_attribute( 'select' . $field_index, $attr_name, $attr_value );
                    } else {
                        $form_widget->add_render_attribute( 'input' . $field_index, $attr_name, $attr_value );
                    }
                }
            }
        }
        return $field;
    }
}

// Inicializa a classe
new Elementor_Forms_Custom_Attributes();
