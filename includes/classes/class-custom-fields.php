<?php

namespace Aireset\Default;

if (!defined('ABSPATH')) {
    exit;
}

class Custom_Fields {

    public static function get_fields() {
        return [
            'dados_empresa' => [
                'title' => esc_html__('Dados da Empresa', 'aireset-default'),
                'fields' => [
                    'email_contato' => [
                        'title' => esc_html__('Email de Contato', 'aireset-default'),
                        'description' => esc_html__('Email para contato com a empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'cep' => [
                        'title' => esc_html__('Endereço (CEP)', 'aireset-default'),
                        'description' => esc_html__('CEP do endereço da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'endereco' => [
                        'title' => esc_html__('Endereço Completo', 'aireset-default'),
                        'description' => esc_html__('Rua, Número, Complemento, Cidade - Estado.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'telefone' => [
                        'title' => esc_html__('Telefone', 'aireset-default'),
                        'description' => esc_html__('Telefone da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'whatsapp' => [
                        'title' => esc_html__('WhatsApp', 'aireset-default'),
                        'description' => esc_html__('Número do WhatsApp da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'whatsapp_link' => [
                        'title' => esc_html__('WhatsApp Link', 'aireset-default'),
                        'description' => esc_html__('Link do WhatsApp da empresa.', 'aireset-default'),
                        'type' => 'url'
                    ],
                    'facebook' => [
                        'title' => esc_html__('Facebook', 'aireset-default'),
                        'description' => esc_html__('Nome do Facebook da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'facebook_link' => [
                        'title' => esc_html__('Facebook Link', 'aireset-default'),
                        'description' => esc_html__('Link do Facebook da empresa.', 'aireset-default'),
                        'type' => 'url'
                    ],
                    'instagram' => [
                        'title' => esc_html__('Instagram', 'aireset-default'),
                        'description' => esc_html__('Nome do Instagram da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'instagram_link' => [
                        'title' => esc_html__('Instagram Link', 'aireset-default'),
                        'description' => esc_html__('Link do Instagram da empresa.', 'aireset-default'),
                        'type' => 'url'
                    ],
                    'linkedin' => [
                        'title' => esc_html__('LinkedIn', 'aireset-default'),
                        'description' => esc_html__('Nome do LinkedIn da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'linkedin_link' => [
                        'title' => esc_html__('LinkedIn Link', 'aireset-default'),
                        'description' => esc_html__('Link do LinkedIn da empresa.', 'aireset-default'),
                        'type' => 'url'
                    ],
                    'tiktok' => [
                        'title' => esc_html__('TikTok', 'aireset-default'),
                        'description' => esc_html__('Nome do TikTok da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'tiktok_link' => [
                        'title' => esc_html__('TikTok Link', 'aireset-default'),
                        'description' => esc_html__('Link do TikTok da empresa.', 'aireset-default'),
                        'type' => 'url'
                    ],
                    'horario_aberto' => [
                        'title' => esc_html__('Horário de Abertura', 'aireset-default'),
                        'description' => esc_html__('Horário de funcionamento.', 'aireset-default'),
                        'type' => 'text'
                    ],
                    'cnpj' => [
                        'title' => esc_html__('CNPJ', 'aireset-default'),
                        'description' => esc_html__('CNPJ da empresa.', 'aireset-default'),
                        'type' => 'text'
                    ],
                ]
            ],
            
        ];
    }

    /**
     * Filtra e retorna os campos com base no tipo especificado.
     *
     * @param string $type O tipo de campo a ser filtrado (ex: 'text', 'url').
     * @return array Lista de campos que correspondem ao tipo informado.
     */
    public static function get_fields_by_type($type) {
        $all_fields = self::get_fields();
        $filtered_fields = [];

        foreach ($all_fields as $group_key => $group) {
            if (!isset($group['fields']) || !is_array($group['fields'])) {
                continue;
            }

            foreach ($group['fields'] as $field_key => $field) {
                if (isset($field['type']) && $field['type'] === $type) {
                    $filtered_fields[$field_key] = $field;
                }
            }
        }

        return $filtered_fields;
    }


}
