<?php
namespace Aireset\Default\Elementor;

use Aireset\Default\Custom_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Aireset_Default_Elementor_Integration {

	/**
	 * Class constructor
	 * 
	 * @since 6.8.3
	 */
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_request_variables_dynamic_tag_group' ] );
		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_default_dynamic_tags' ] );
	}

	/**
	 * Register Request Variables Dynamic Tag Group.
	 *
	 * Register new dynamic tag group for Request Variables.
	 *
	 * @since 6.8.3
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager Elementor dynamic tags manager.
	 * @return void
	 */
	public function register_request_variables_dynamic_tag_group( $dynamic_tags_manager ) {
		$dynamic_tags_manager->register_group(
			'aireset-default',
			[
				'title' => __('Aireset Geral', 'aireset-default')
			]
		);
	}

	/**
	 * Register the various field types as dynamic data source
	 *
	 * @since 6.8.3
	 */
	public function register_default_dynamic_tags( $dynamic_tags_manager ) {
		// Register dynamic data that can return text (including HTML)
		require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/text.php';
		$dynamic_tags_manager->register( new \Aireset\Default\Elementor\Dynamic_Text );

		// Register dynamic data that can return URL
		// require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/url.php';
		// $dynamic_tags_manager->register( new \Aireset\Default\Elementor\Url );

		// // Register dynamic data that can return image data
		// require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/image.php';
		// $dynamic_tags_manager->register( new \Aireset\Default\Elementor\Image );

		// // Register dynamic data that can return gallery data
		// require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/gallery.php';
		// $dynamic_tags_manager->register( new \Aireset\Default\Elementor\Gallery );

		// // Register dynamic data that can return file data
		// require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/file.php';
		// $dynamic_tags_manager->register( new \Aireset\Default\Elementor\File );

		// // Register dynamic data that can return number data
		// require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/number.php';
		// $dynamic_tags_manager->register( new \Aireset\Default\Elementor\Number );

		// // Register dynamic data that can return color data
		// require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags/color.php';
		// $dynamic_tags_manager->register( new \Aireset\Default\Elementor\Color );
	}

	/**
	 * Get key value pairs for the select element/control used so select which custom field to use as dynamic data source
	 * @param  string $return_type 	the return type of the custom fields
	 * @return array              	array of custom field name - custom field label
	 */
	// public function get_control_options( $return_type = 'text', $applicable_field_types = array() ) {
		
	// 	// Get all published custom field groups
	// 	$cf_groups = array();
	// 	$cf_group_fields = array();

	// 	// We don't use WP_Query and wp_reset_postdata() at the end to prevent messing with the main query
	// 	$args = array(
	// 	    'post_type' => 'asenha_cfgroup',
	// 	    'post_status' => 'publish',
	// 	    'numberposts' => -1,
	// 	);

	//     $cfgroups = get_posts( $args );

	// 	if ( ! empty( $cfgroups ) ) {
	// 		foreach ( $cfgroups as $cfgroup ) {
	// 			// setup_postdata( $cfgroup ); // Not currently needed to access ID and title
				
	// 	        $cf_group_id = $cfgroup->ID;
	// 	        $fields = CFG()->find_fields( array( 'group_id' => $cf_group_id ) );
	// 	        $cf_group_fields = array();

	// 	        if ( is_array( $fields ) && ! empty( $fields ) ) {
	// 		        foreach ( $fields as $field ) {
	// 		        	// We exclude repeater fields and their sub-fields
	// 		        	if ( 'repeater'	!= $field['type'] && 0 === $field['parent_id'] && 'tab' != $field['type'] ) {
	// 		        		if ( in_array( $field['type'], $applicable_field_types ) ) {
	// 		        			$sub_type = 'none';
	// 		        			switch ( $field['type'] ) {
	// 		        				case 'text':
	// 			        				$sub_type = isset( $field['options']['text_type'] ) ? $field['options']['text_type'] : 'any';
	// 		        					break;
	// 		        				case 'select':
	// 			        				$sub_type = ( 1 == $field['options']['multiple'] ) ? 'multiple' : 'single';
	// 		        					break;
	// 		        				case 'file':
	// 			        				$sub_type = ( 'file' == $field['options']['file_type'] ) ? 'any' : $field['options']['file_type'];
	// 		        					break;
	// 		        			}

	// 		        			$field_key = $field['name'] . '__' . $field['type'] . '__' . $sub_type;
			        			
	// 		        			$should_add_field_to_cf_groups = false;
			        			
	// 		        			// Only include applicable field types and sub-types for each return types
	// 		        			switch ( $return_type ) {
	// 		        				case 'text':
	// 				        			$should_add_field_to_cf_groups = true;
	// 		        					break;

	// 		        				case 'url':
	// 		        					if ( 'text' != $field['type'] ) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					} elseif ( 'text' == $field['type'] 
	// 		        						&& isset( $field['options']['text_type'] ) 
	// 		        						&& 'url' == $field['options']['text_type'] 
	// 		        					) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					} else {}
	// 		        					break;

	// 		        				case 'image':
	// 		        					if ( 'file' == $field['type'] 
	// 		        						&& isset( $field['options']['file_type'] ) 
	// 		        						&& 'image' == $field['options']['file_type'] 
	// 		        					) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					}
	// 		        					break;

	// 		        				case 'gallery':
	// 				        			$should_add_field_to_cf_groups = true;
	// 		        					break;

	// 		        				case 'file':
	// 		        					if ( 'file' == $field['type'] ) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					}
	// 		        					break;

	// 		        				case 'number':
	// 		        					if ( 'text' == $field['type'] 
	// 		        						&& isset( $field['options']['text_type'] ) 
	// 		        						&& 'number' == $field['options']['text_type'] 
	// 		        					) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					} elseif ( 'number' == $field['type'] ) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					}
	// 		        					break;

	// 		        				case 'color':
	// 		        					if ( 'color' == $field['type'] ) {
	// 					        			$should_add_field_to_cf_groups = true;
	// 		        					}
	// 		        					break;
	// 		        			}
			        			
	// 		        			if ( $should_add_field_to_cf_groups ) {
	// 					        	$cf_group_fields[$field_key] = $field['label'];
	// 		        			}
	// 		        		}
	// 			        }
	// 		        }
	// 	        }

	// 	        $post_title = $cfgroup->post_title;
	// 	        $cf_groups[] = array(
	// 	        	'label'		=> $post_title,
	// 	        	'options'	=> $cf_group_fields,
	// 	        );
	// 	    }
	// 	    // wp_reset_postdata(); // this breaks the main query, so, we comment out
	// 	}
	// 	// vi( $cf_groups );
		
	// 	return $cf_groups;
	// }

	public function get_control_options($return_type = 'text', $applicable_field_types = array()) {
		// Obtém os grupos de campos personalizados
		$cf_groups = [];
		
		$all_fields = Custom_Fields::get_fields();

		foreach ($all_fields as $group_key => $group) {
			if (!isset($group['fields']) || !is_array($group['fields'])) {
				continue;
			}

			$cf_group_fields = [];

			foreach ($group['fields'] as $field_key => $field) {
				if (!isset($field['type']) || !in_array($field['type'], $applicable_field_types)) {
					continue;
				}

				$sub_type = 'none';
				switch ($field['type']) {
					case 'text':
						$sub_type = 'any';
						break;
					case 'select':
						$sub_type = 'single';
						break;
					case 'file':
						$sub_type = 'any';
						break;
				}

				$field_key_formatted = "{$field_key}__{$field['type']}__{$sub_type}";
				$should_add_field = false;

				switch ($return_type) {
					case 'text':
						$should_add_field = true;
						break;

					case 'url':
						if ($field['type'] !== 'text') {
							$should_add_field = true;
						} elseif (isset($field['options']['text_type']) && $field['options']['text_type'] === 'url') {
							$should_add_field = true;
						}
						break;

					case 'image':
						if ($field['type'] === 'file' && isset($field['options']['file_type']) && $field['options']['file_type'] === 'image') {
							$should_add_field = true;
						}
						break;

					case 'gallery':
						$should_add_field = true;
						break;

					case 'file':
						if ($field['type'] === 'file') {
							$should_add_field = true;
						}
						break;

					case 'number':
						if ($field['type'] === 'text' && isset($field['options']['text_type']) && $field['options']['text_type'] === 'number') {
							$should_add_field = true;
						} elseif ($field['type'] === 'number') {
							$should_add_field = true;
						}
						break;

					case 'color':
						if ($field['type'] === 'color') {
							$should_add_field = true;
						}
						break;
				}

				if ($should_add_field) {
					$cf_group_fields[$field_key_formatted] = $field['title'];
				}
			}

			if (!empty($cf_group_fields)) {
				$cf_groups[] = [
					'label' => $group['title'],
					'options' => $cf_group_fields,
				];
			}
		}

		return $cf_groups;
	}
}

$aireset_default_elementor_integration = new Aireset_Default_Elementor_Integration();