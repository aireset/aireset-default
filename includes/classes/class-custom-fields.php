<?php

namespace Aireset\Default;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Fields {

	public static function get_fields() {
		return array(
			'dados_empresa' => array(
				'title'  => esc_html__( 'Dados da Empresa', 'aireset-default' ),
				'fields' => array(
					'email_contato' => array(
						'title'       => esc_html__( 'Email de Contato', 'aireset-default' ),
						'description' => esc_html__( 'Email para contato com a empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'cep' => array(
						'title'       => esc_html__( 'Endereco (CEP)', 'aireset-default' ),
						'description' => esc_html__( 'CEP da empresa (sincronizado com WooCommerce > Endereco da loja).', 'aireset-default' ),
						'type'        => 'text',
					),
					'endereco' => array(
						'title'       => esc_html__( 'Endereco linha 1', 'aireset-default' ),
						'description' => esc_html__( 'Rua e numero principal da empresa (sincronizado com WooCommerce).', 'aireset-default' ),
						'type'        => 'text',
					),
					'endereco_linha_2' => array(
						'title'       => esc_html__( 'Endereco linha 2', 'aireset-default' ),
						'description' => esc_html__( 'Complemento de endereco (sincronizado com WooCommerce).', 'aireset-default' ),
						'type'        => 'text',
					),
					'cidade' => array(
						'title'       => esc_html__( 'Cidade', 'aireset-default' ),
						'description' => esc_html__( 'Cidade da loja (sincronizado com WooCommerce).', 'aireset-default' ),
						'type'        => 'text',
					),
					'estado' => array(
						'title'       => esc_html__( 'Estado (UF)', 'aireset-default' ),
						'description' => esc_html__( 'UF da loja (ex: SP, PR, SC) sincronizada com WooCommerce.', 'aireset-default' ),
						'type'        => 'text',
					),
					'pais' => array(
						'title'       => esc_html__( 'Pais (ISO2)', 'aireset-default' ),
						'description' => esc_html__( 'Pais da loja em codigo ISO2 (ex: BR, US), sincronizado com WooCommerce.', 'aireset-default' ),
						'type'        => 'text',
					),
					'telefone' => array(
						'title'       => esc_html__( 'Telefone', 'aireset-default' ),
						'description' => esc_html__( 'Telefone da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'whatsapp' => array(
						'title'       => esc_html__( 'WhatsApp', 'aireset-default' ),
						'description' => esc_html__( 'Numero do WhatsApp da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'whatsapp_link' => array(
						'title'       => esc_html__( 'WhatsApp Link', 'aireset-default' ),
						'description' => esc_html__( 'Link do WhatsApp da empresa.', 'aireset-default' ),
						'type'        => 'url',
					),
					'facebook' => array(
						'title'       => esc_html__( 'Facebook', 'aireset-default' ),
						'description' => esc_html__( 'Nome do Facebook da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'facebook_link' => array(
						'title'       => esc_html__( 'Facebook Link', 'aireset-default' ),
						'description' => esc_html__( 'Link do Facebook da empresa.', 'aireset-default' ),
						'type'        => 'url',
					),
					'instagram' => array(
						'title'       => esc_html__( 'Instagram', 'aireset-default' ),
						'description' => esc_html__( 'Nome do Instagram da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'instagram_link' => array(
						'title'       => esc_html__( 'Instagram Link', 'aireset-default' ),
						'description' => esc_html__( 'Link do Instagram da empresa.', 'aireset-default' ),
						'type'        => 'url',
					),
					'linkedin' => array(
						'title'       => esc_html__( 'LinkedIn', 'aireset-default' ),
						'description' => esc_html__( 'Nome do LinkedIn da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'linkedin_link' => array(
						'title'       => esc_html__( 'LinkedIn Link', 'aireset-default' ),
						'description' => esc_html__( 'Link do LinkedIn da empresa.', 'aireset-default' ),
						'type'        => 'url',
					),
					'tiktok' => array(
						'title'       => esc_html__( 'TikTok', 'aireset-default' ),
						'description' => esc_html__( 'Nome do TikTok da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
					'tiktok_link' => array(
						'title'       => esc_html__( 'TikTok Link', 'aireset-default' ),
						'description' => esc_html__( 'Link do TikTok da empresa.', 'aireset-default' ),
						'type'        => 'url',
					),
					'horario_aberto' => array(
						'title'       => esc_html__( 'Horario de Abertura', 'aireset-default' ),
						'description' => esc_html__( 'Horario de funcionamento.', 'aireset-default' ),
						'type'        => 'text',
					),
					'cnpj' => array(
						'title'       => esc_html__( 'CNPJ', 'aireset-default' ),
						'description' => esc_html__( 'CNPJ da empresa.', 'aireset-default' ),
						'type'        => 'text',
					),
				),
			),
		);
	}

	/**
	 * Filter and return fields by type.
	 *
	 * @param string $type Field type.
	 * @return array
	 */
	public static function get_fields_by_type( $type ) {
		$all_fields      = self::get_fields();
		$filtered_fields = array();

		foreach ( $all_fields as $group ) {
			if ( ! isset( $group['fields'] ) || ! is_array( $group['fields'] ) ) {
				continue;
			}

			foreach ( $group['fields'] as $field_key => $field ) {
				if ( isset( $field['type'] ) && $field['type'] === $type ) {
					$filtered_fields[ $field_key ] = $field;
				}
			}
		}

		return $filtered_fields;
	}
}
