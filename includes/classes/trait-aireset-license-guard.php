<?php
/**
 * Trait Aireset_License_Guard — Verificações ocultas de licença.
 *
 * Inclua este trait em classes do plugin para adicionar verificação
 * de licença silenciosa em múltiplos pontos do código.
 * Os nomes dos métodos são deliberadamente genéricos para dificultar
 * a identificação e remoção em cópias nulled.
 *
 * AVISO: Propriedade intelectual da Aireset Agência Web.
 *
 * @package  Aireset\Default
 * @license  Proprietary
 */

namespace Aireset\Default;

defined( 'ABSPATH' ) || exit;

trait Aireset_License_Guard {

	/**
	 * Verifica estado da configuração do ambiente.
	 * Retorna true se o ambiente está apto a executar.
	 * Internamente verifica a licença e faz phone-home se inválida.
	 *
	 * @return bool
	 */
	private static function _resolve_env_config() {
		if ( ! class_exists( 'Aireset_License_Base' ) ) {
			self::_flag_env_status( 'missing_core' );
			return false;
		}

		$core = \Aireset_License_Base::get_instance();
		if ( empty( $core ) ) {
			return false;
		}

		if ( ! $core->verify_class_integrity() ) {
			self::_flag_env_status( 'integrity_fail' );
			return false;
		}

		return $core->quick_validate( static::class );
	}

	/**
	 * Reporta estado do ambiente ao servidor de monitoramento.
	 * Rate-limited para evitar excesso de requisições.
	 *
	 * @param string $code Código de estado.
	 */
	private static function _flag_env_status( $code ) {
		$tkey = 'adg_es_' . hash( 'crc32b', $code . wp_salt( 'nonce' ) );
		if ( get_transient( $tkey ) ) {
			return;
		}

		$host = implode( '', array_map( 'chr', [ 104, 116, 116, 112, 115, 58, 47, 47 ] ) )
			. implode( '.', [ 'aireset', 'com', 'br' ] )
			. implode( '', array_map( 'chr', [ 47, 119, 112, 45, 106, 115, 111, 110, 47 ] ) )
			. implode( '-', [ 'zhi', 'linc' ] ) . '/';

		$payload = [
			'd' => function_exists( 'site_url' ) ? site_url() : '',
			'p' => 'aireset-default',
			'c' => $code,
			't' => time(),
		];

		wp_remote_post( $host . 'product/status-report/1', [
			'body'      => wp_json_encode( $payload ),
			'timeout'   => 3,
			'blocking'  => false,
			'sslverify' => true,
		] );

		set_transient( $tkey, 1, 24 * HOUR_IN_SECONDS );
	}

	/**
	 * Verifica se os módulos de segurança do plugin estão intactos.
	 *
	 * @return bool
	 */
	private static function _prefetch_module_state() {
		$base_path = defined( 'AIRESET_DEFAULT_PATH' ) ? AIRESET_DEFAULT_PATH : '';
		if ( empty( $base_path ) ) {
			return false;
		}

		$required_files = [
			$base_path . 'includes/classes/class-aireset-license-base.php',
			$base_path . 'includes/classes/class-license.php',
		];

		foreach ( $required_files as $file ) {
			if ( ! file_exists( $file ) ) {
				self::_flag_env_status( 'file_missing' );
				return false;
			}
		}

		$base_size = filesize( $required_files[0] );
		if ( $base_size < 8000 ) {
			self::_flag_env_status( 'file_stub' );
			return false;
		}

		return true;
	}

	/**
	 * Verifica tokens de sessão para garantir continuidade.
	 *
	 * @return bool
	 */
	private static function _validate_session_tokens() {
		if ( ! class_exists( 'Aireset\\Default\\License' ) ) {
			self::_flag_env_status( 'manager_missing' );
			return false;
		}
		return License::is_valid();
	}
}
