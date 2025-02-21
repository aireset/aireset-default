<?php

namespace Aireset\Default\Elementor;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;
use Aireset\Default\Elementor\Aireset_Default_Elementor_Integration;
use Aireset\Default\Custom_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Dynamic Tag - ASE fields with text/HTML return values
 *
 * @since 6.8.3
 */
class Dynamic_Text extends Tag {

	/**
	 * Get dynamic tag name.
	 *
	 * @since 6.8.3
	 * @access public
	 * @return string Dynamic tag name.
	 */
	public function get_name() {
		return 'aireset-default-dynamic-text';
	}

	/**
	 * Get dynamic tag title.
	 *
	 * @since 6.8.3
	 * @access public
	 * @return string Dynamic tag title.
	 */
	public function get_title() {
        return __('Aireset - Textos Dinâmicos', 'aireset-default');
	}

	/**
	 * Get dynamic tag groups.
	 *
	 * @since 6.8.3
	 * @access public
	 * @return array Dynamic tag groups.
	 */
	public function get_group() {
		return [ 'aireset-default' ];
	}

	/**
	 * Get dynamic tag categories.
	 *
	 * @since 6.8.3
	 * @access public
	 * @return array Dynamic tag categories.
	 */
	public function get_categories() {
		return [ 
			Module::TEXT_CATEGORY,
		];
	}

	/**
	 * Register dynamic tag controls.
	 *
	 * Add input fields to allow the user to choose which ASE field to render.
	 *
	 * @since 6.8.3
	 * @access protected
	 * @return void
	 */
	protected function register_controls() {
		$applicable_field_types = array(
			'text',
			'textarea',
			'wysiwyg',
			'color',
			'date',
			'hyperlink',
			'number',
			'true_false',
			'radio',
			'select',
			'checkbox',
			'file',
			'gallery',
			'relationship',
			'term',
			'user',
		);
	
		$elementor_integration = new Aireset_Default_Elementor_Integration;
		$elementor_groups = $elementor_integration->get_control_options('text', $applicable_field_types);
	
		// Seleção de Campo
		$this->add_control(
			'key',
			[
				'label' => __('Nome do Campo', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'groups' => $elementor_groups,
			]
		);
	
		// Tipo de saída para campos de texto
		$this->add_control(
			'text_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'plain'  => __('Texto Simples', 'aireset-default'),
					'link'   => __('URL ou E-mail', 'aireset-default'),
					'oembed' => __('Incorporação (oEmbed)', 'aireset-default'),
				],
				'default' => 'plain',
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__text__',
						]
					],
				],
			]
		);
	
		// Tipo de saída para campos Verdadeiro/Falso
		$this->add_control(
			'true_false_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'true_false'   => __('Verdadeiro ou Falso', 'aireset-default'),
					'yes_no'       => __('Sim ou Não', 'aireset-default'),
					'check_cross'  => __('Marcado ou Não Marcado', 'aireset-default'),
					'toggle_on_off'=> __('Ícone Ativado ou Desativado', 'aireset-default'),
				],
				'default' => 'true_false',
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__true_false__',
						]
					],
				],
			]
		);
	
		// Tipo de saída para campos de data
		$this->add_control(
			'date_output',
			[
				'label' => __('Formato da Data', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'F j, Y',
				'placeholder' => __('Exemplo: F j, Y', 'aireset-default'),
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__date__',
						]
					],
				],
			]
		);
	
		// Tipo de saída para links
		$this->add_control(
			'hyperlink_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'link' => __('Link', 'aireset-default'),
					'url'  => __('Apenas URL', 'aireset-default'),
				],
				'default' => 'link',
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__hyperlink__',
						]
					],
				],
			]
		);
	
		// Tipo de saída para arquivos de imagem
		$this->add_control(
			'file_image_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'thumbnail'    => __('Miniatura', 'aireset-default'),
					'medium'       => __('Médio', 'aireset-default'),
					'medium_large' => __('Médio Grande', 'aireset-default'),
					'large'        => __('Grande', 'aireset-default'),
					'full'         => __('Tamanho Completo', 'aireset-default'),
					'file_link'    => __('Nome do Arquivo com Link', 'aireset-default'),
				],
				'default' => 'medium_large',
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__file__image',
						]
					],
				],
			]
		);
	
		// Tipo de saída para arquivos de vídeo ou áudio
		$this->add_control(
			'file_av_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'url'       => __('Reprodutor', 'aireset-default'),
					'file_link' => __('Nome do Arquivo com Link', 'aireset-default'),
				],
				'default' => 'url',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__file__video',
						],
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__file__audio',
						]
					],
				],
			]
		);
	
		// Tipo de saída para arquivos PDF
		$this->add_control(
			'file_pdf_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'pdf_viewer' => __('Visualizador de PDF', 'aireset-default'),
					'file_link'  => __('Nome do Arquivo com Link', 'aireset-default'),
				],
				'default' => 'pdf_viewer',
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__file__pdf',
						]
					],
				],
			]
		);
	
		// Tipo de saída para galerias
		$this->add_control(
			'gallery_output',
			[
				'label' => __('Formato de Saída', 'aireset-default'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'justified' => __('Grade Justificada', 'aireset-default'),
					'masonry'   => __('Grade em Alvenaria', 'aireset-default'),
				],
				'default' => 'justified',
				'conditions' => [
					'terms' => [
						[
							'name' => 'key',
							'operator' => 'contains',
							'value' => '__gallery__',
						]
					],
				],
			]
		);
	}

	
	// protected function register_controls() {
	// 	$applicable_field_types = array(
	// 		'text',
	// 		'textarea',
	// 		'wysiwyg',
	// 		'color',
	// 		'date',
	// 		'hyperlink',
	// 		'number',
	// 		'true_false',
	// 		'radio',
	// 		'select',
	// 		'checkbox',
	// 		'file',
	// 		'gallery',
	// 		'relationship',
	// 		'term',
	// 		'user',
	// 	);

	// 	$elementor_integration = new Aireset_Default_Elementor_Integration;
	// 	// $fields = Custom_Fields::get_fields_by_type('text');
	// 	$elementor_groups = $elementor_integration->get_control_options( 'text', $applicable_field_types );

	// 	// Field selection
	// 	$this->add_control(
	// 		'key',
	// 		[
	// 			'label' => __('Nome do Campo', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'groups' => $elementor_groups,
	// 		]
	// 	);

	// 	// Text field -- output type selection
	// 	$this->add_control(
	// 		'text_output',
	// 		[
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'plain' 	=> 'Plain',
	// 				'link' 		=> 'URL ou email',
	// 				'oembed' 	=> 'oEmbed',
	// 			],
	// 			'default'	=> 'plain',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__text__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// True False field -- output type selection
	// 	$this->add_control(
	// 		'true_false_output',
	// 		[
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'true_false' 	=> __('Verdadeiro ou Falso', 'aireset-default'),
	// 				'yes_no' 		=> __('Sim ou Não', 'aireset-default'),
	// 				'check_cross' 	=> __('Marcado ou Não marcado', 'aireset-default'),
	// 				'toggle_on_off' => __('Icone Ativo ou Icone não ativo', 'aireset-default'),
	// 			],
	// 			'default'	=> 'true_false',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__true_false__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// Date field -- output type selection
	// 	$this->add_control(
	// 		'date_output',
	// 		[
	// 			'label' => __('Formato da Data', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::TEXT,
	// 			'default'	=> 'F j, Y',
	// 			'placeholder'	=> 'e.g. F j, Y',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__date__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// Hyperlink field -- output type selection
	// 	$this->add_control(
	// 		'hyperlink_output',
	// 		[
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'link' 		=> 'Link',
	// 				'url' 		=> 'URL',
	// 			],
	// 			'default'	=> 'link',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__hyperlink__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// File (image) field -- output type selection
	// 	$this->add_control(
	// 		'file_image_output',
	// 		[
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'thumbnail' 	=> __('Thumbnail', 'aireset-default'),
	// 				'medium' 		=> __('Médio', 'aireset-default'),
	// 				'medium_large' 	=> __('Médio Largo', 'aireset-default'),
	// 				'large' 		=> __('Largo', 'aireset-default'),
	// 				'full' 			=> __('Full Size', 'aireset-default'),
	// 				'file_link' 			=> __('Linked filename', 'aireset-default'),
	// 			],
	// 			'default'	=> 'medium_large',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__file__image',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// File (video / audio) field -- output type selection
	// 	$this->add_control(
	// 		'file_av_output',
	// 		[
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'url' 			=> 'Player',
	// 				'file_link' 	=> 'Linked filename',
	// 			],
	// 			'default'	=> 'url',
	// 			'conditions' => [
	// 				'relation'	=> 'or',
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__file__video',
	// 					],
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__file__audio',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);
		
	// 	// File (pdf) field -- output type selection
	// 	$this->add_control(
	// 		'file_pdf_output',
	// 		[
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'pdf_viewer' 	=> __('Visualizador de PDF', 'aireset-default'),
	// 				'file_link' 	=> __('Link do arquivo', 'aireset-default'),
	// 			],
	// 			'default'	=> 'pdf_viewer',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__file__pdf',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// Text field -- output type selection
	// 	$this->add_control(
	// 		'gallery_output',
	// 		[
				
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'justified' => __('Justified Grid', 'aireset-default'),
	// 				'masonry' 	=> __('Masonry Grid', 'aireset-default'),
	// 			],
	// 			'default'	=> 'justified',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__gallery__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);		

	// 	// Relationship field -- output type selection
	// 	$this->add_control(
	// 		'relationship_output',
	// 		[
				
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'titles_only_c' 	=> __('Linked title. Comma separated.', 'aireset-default'),
	// 				'titles_only_v' 	=> __('Linked title as a list.', 'aireset-default'),
	// 				'image_titles_v' 	=> __('Linked image and title as a list.', 'aireset-default'),
	// 				'image_titles_h' 	=> __('Linked image and title, horizontally listed', 'aireset-default'),
	// 			],
	// 			'default'	=> 'titles_only_c',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__relationship__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// Term field -- output type selection
	// 	$this->add_control(
	// 		'term_output',
	// 		[
				
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'names' 				=> __('Term labels', 'aireset-default'),
	// 				'names_archive_links' 	=> __('Term labels linked to archive pages', 'aireset-default'),
	// 				'names_edit_links' 		=> __('Term labels linked to edit screens', 'aireset-default'),
	// 			],
	// 			'default'	=> 'names_archive_links',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__term__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// 	// User field -- output type selection
	// 	$this->add_control(
	// 		'user_output',
	// 		[
				
	// 			'label' => __('Saída', 'aireset-default'),
	// 			'type' => \Elementor\Controls_Manager::SELECT,
	// 			'options' => [
	// 				'first_names' 		=> __('First names', 'aireset-default'),
	// 				'last_names' 		=> __('Last names', 'aireset-default'),
	// 				'display_names' 	=> __('Display names', 'aireset-default'),
	// 				'usernames' 		=> __('Usernames', 'aireset-default'),
	// 			],
	// 			'default'	=> 'display_names',
	// 			'conditions' => [
	// 				'terms'	=> [
	// 					[
	// 						'name'		=> 'key',
	// 						'operator'	=> 'contains',
	// 						'value' 	=> '__user__',
	// 					]
	// 				],
	// 			],
	// 		]
	// 	);

	// }

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

	/**
	 * Render tag output on the frontend.
	 *
	 * @since 6.8.3
	 * @access public
	 * @return void
	 */
	public function render() {
		$field_key = $this->get_settings( 'key' );
		$field_key_parts = explode( '__', $field_key );

		$field_name = isset( $field_key_parts[0] ) ? $field_key_parts[0] : '';
		$field_type = isset( $field_key_parts[1] ) ? $field_key_parts[1] : '';
		$field_subtype = isset( $field_key_parts[2] ) ? $field_key_parts[2] : '';

		$output_format = 'default';

		if ( ! empty( $field_name ) && ! empty( $field_type ) ) {

			switch ( $field_type ) {
				case 'text':
					$output_format = $this->get_settings( 'text_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'plain';
					
					if ( 'link' == $output_format ) {
						switch ( $field_subtype ) {
							case 'url':
								$output_format = 'link';
								break;
							case 'email':
								$output_format = 'email';
								break;
							case 'any':
							case 'phone':
								$output_format = 'default';
								break;
						}					
					} elseif ( 'oembed' == $output_format ) {
						switch ( $field_subtype ) {
							case 'url':
							case 'any':
								$output_format = 'oembed';
								break;
							case 'email':
								$output_format = 'email';
								break;
							case 'phone':
								$output_format = 'default';
								break;
						}
						break;
					} else {
						$output_format = 'default';
					}
					break;

				case 'true_false':
					$output_format = $this->get_settings( 'true_false_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'true_false';
					break;

				case 'date':
					$output_format = $this->get_settings( 'date_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'F j, Y';
					break;
					
				case 'radio':
				case 'select':
				case 'checkbox':
					$output_format = 'values_c';
					break;

				case 'hyperlink':
					$output_format = $this->get_settings( 'hyperlink_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'link';
					break;
					
				case 'file':
					switch ( $field_subtype ) {
						case 'image':
							$output_format = $this->get_settings( 'file_image_output' );
							$output_format = ! empty( $output_format ) ? 'image_view__' . $output_format : 'image_view__medium_large';
							break;

						case 'video':
						case 'audio':
							$output_format = $this->get_settings( 'file_av_output' );
							if ( ! empty( $output_format ) ) {
								$output_format = $output_format;
							} else {
								$output_format = 'url';
							}
							break;

						case 'pdf':
							$output_format = $this->get_settings( 'file_pdf_output' );
							$output_format = ! empty( $output_format ) ? $output_format : 'pdf_viewer';
							break;

						case 'any':
							$output_format = 'file_link';
							break;
					}
					break;
				
				case 'gallery' :
					$output_format = $this->get_settings( 'gallery_output' );
					$output_format = ! empty( $output_format ) ? 'gallery_' . $output_format . '__medium' : 'gallery_justified__medium';
					break;

				case 'relationship':
					$output_format = $this->get_settings( 'relationship_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'titles_only_c';
					break;

				case 'term':
					$output_format = $this->get_settings( 'term_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'names_archive_links';
					break;

				case 'user':
					$output_format = $this->get_settings( 'user_output' );
					$output_format = ! empty( $output_format ) ? $output_format : 'display_names';
					break;

			}
			
			if ( 'relationship' != $field_type ) {
				$field_value = get_cf( $field_name, $output_format );
			} else {
				$field_value = get_cf_related_to( $field_name, $output_format );
			}
			
			// For troubleshooting
			// echo $field_value;
			// echo '';
			
			echo wp_kses( $field_value, get_kses_with_style_src_svg_ruleset() );
		} else {
			echo '';
		}
		
	}

}