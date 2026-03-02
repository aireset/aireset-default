<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Change colors on front-end for the shipping calculator.
 */
class Shipping_Management_Custom_Colors {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'custom_colors' ) );
	}

	/**
	 * Print custom CSS vars and color rules.
	 *
	 * @return void
	 */
	public function custom_colors() {
		if ( Aireset\Default\Init::get_setting( 'aireset_default_enable_shipping_calculator' ) !== 'yes' ) {
			return;
		}

		$primary_color = $this->sanitize_hex_color( Aireset\Default\Init::get_setting( 'aireset_default_primary_main_color' ), '#000000' );
		$hover_color   = $this->sanitize_hex_color( Aireset\Default\Init::get_setting( 'aireset_default_secondary_main_color' ), '' );
		$text_color    = $this->sanitize_hex_color( Aireset\Default\Init::get_setting( 'aireset_default_button_text_color' ), '#ffffff' );

		if ( empty( $hover_color ) ) {
			$hover_color = $this->generate_rgba_color( $primary_color, 80 );
		}

		$css  = '.aireset-postcode:focus{border-color:' . esc_attr( $primary_color ) . ' !important;}';
		$css .= '.aireset-shipping-calc-button{background-color:' . esc_attr( $primary_color ) . ' !important;border-color:' . esc_attr( $primary_color ) . ' !important;color:' . esc_attr( $text_color ) . ' !important;}';
		$css .= '.aireset-shipping-calc-button:hover{background-color:' . esc_attr( $hover_color ) . ' !important;border-color:' . esc_attr( $hover_color ) . ' !important;color:' . esc_attr( $text_color ) . ' !important;}';
		$css .= '.aireset-postcode-search:hover{color:' . esc_attr( $primary_color ) . ';}';
		$css .= '.aireset-response table .aireset-shipping-header th{background-color:' . esc_attr( $primary_color ) . ' !important;}';
		?>
		<style type="text/css"><?php echo $css; ?></style>
		<?php
	}

	/**
	 * Sanitize hex values with fallback.
	 *
	 * @param string $value Input color.
	 * @param string $fallback Fallback color.
	 * @return string
	 */
	private function sanitize_hex_color( $value, $fallback ) {
		$color = sanitize_hex_color( (string) $value );
		if ( ! empty( $color ) ) {
			return $color;
		}

		return $fallback;
	}

	/**
	 * Generate RGBA from a hex color.
	 *
	 * @param string $color Hex color.
	 * @param int    $opacity Percent value.
	 * @return string
	 */
	public function generate_rgba_color( $color, $opacity ) {
		$color = str_replace( '#', '', (string) $color );
		if ( strlen( $color ) !== 6 ) {
			return 'rgba(0,0,0,0.8)';
		}

		$red     = hexdec( substr( $color, 0, 2 ) );
		$green   = hexdec( substr( $color, 2, 2 ) );
		$blue    = hexdec( substr( $color, 4, 2 ) );
		$opacity = max( 0, min( 100, (int) $opacity ) ) / 100;

		return "rgba($red, $green, $blue, $opacity)";
	}
}

new Shipping_Management_Custom_Colors();
