<?php
/**
 * Classe base de licenciamento Elite Licenser para plugins Aireset.
 *
 * NÃO usa namespace — precisa ser acessível globalmente pelo SDK do Elite Licenser.
 *
 * @package Aireset\License
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aireset_License_Base' ) ) {

	class Aireset_License_Base {

		/**
		 * Chave encriptada do produto (enc_key do Elite Licenser).
		 *
		 * @var string
		 */
		public $key = '29384224C655851C';

		/**
		 * ID do produto no Elite Licenser.
		 *
		 * @var string
		 */
		private $product_id = '1';

		/**
		 * Slug/base do produto.
		 *
		 * @var string
		 */
		private $product_base = 'aireset-default';

		/**
		 * URL do servidor de licenças (com barra final).
		 *
		 * @var string
		 */
		private $server_host = 'https://aireset.com.br/wp-json/zhi-linc/';

		/**
		 * Se deve verificar atualizações via Elite Licenser.
		 *
		 * @var bool
		 */
		private $has_check_update = true;

		private $plugin_file;
		private $theme_dir_name = '';
		private static $selfobj = null;
		private $version = '';
		private $is_theme = false;
		private $email_address = '';
		private static $_on_delete_license = [];

		/* ───────── Fingerprint de integridade ───────── */
		private static $_fp_salt = 'adg_ar_2026';

		public function __construct( $plugin_base_file = '' ) {
			if ( empty( $plugin_base_file ) ) {
				$dir = str_replace( '\\', '/', dirname( __FILE__ ) );
			} else {
				$dir = str_replace( '\\', '/', dirname( $plugin_base_file ) );
			}

			if ( strpos( $dir, 'wp-content/themes' ) !== false ) {
				$this->is_theme       = true;
				$this->theme_dir_name = self::get_this_theme_path_name();
				$theme_data           = wp_get_theme( $this->theme_dir_name );
				$version              = $theme_data->get( 'Version' );
				if ( ! empty( $version ) ) {
					$this->version = $version;
				}
			}

			$this->plugin_file = $plugin_base_file;
			if ( empty( $this->plugin_file ) && $this->is_theme ) {
				$this->plugin_file = self::get_this_theme_path();
			}

			if ( empty( $this->version ) ) {
				$this->version = $this->get_current_version();
			}

			if ( $this->has_check_update ) {
				if ( function_exists( 'add_action' ) ) {
					add_action( 'admin_post_' . $this->product_base . '_fupc', function () {
						update_option( '_site_transient_update_plugins', '' );
						update_option( '_site_transient_update_themes', '' );
						set_site_transient( 'update_themes', null );
						delete_transient( $this->product_base . '_up' );
						wp_redirect( admin_url( 'plugins.php' ) );
						exit;
					} );
					add_action( 'init', [ $this, 'init_action_handler' ] );
				}

				if ( function_exists( 'add_filter' ) ) {
					add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'plugin_update' ] );
					add_filter( 'plugins_api', [ $this, 'check_update_info' ], 10, 3 );
					add_filter( 'plugin_row_meta', function ( $links, $plugin_file ) {
						if ( plugin_basename( $this->plugin_file ) === $plugin_file ) {
							$links[] = '<a class="edit" href="' . esc_url( admin_url( 'admin-post.php' ) . '?action=' . $this->product_base . '_fupc' ) . '">' . esc_html__( 'Update Check', $this->product_base ) . '</a>';
						}
						return $links;
					}, 10, 2 );

					add_action( 'in_plugin_update_message-' . plugin_basename( $this->plugin_file ), [ $this, 'update_message_cb' ], 20, 2 );

					add_action( 'upgrader_process_complete', function () {
						update_option( '_site_transient_update_plugins', '' );
						update_option( '_site_transient_update_themes', '' );
						set_site_transient( 'update_themes', null );
					}, 10, 2 );
				}
			}
		}

		public function set_email_address( $email_address ) {
			$this->email_address = sanitize_email( $email_address );
		}

		public function init_action_handler() {
			$handler = hash( 'crc32b', $this->product_id . $this->key . $this->get_domain() ) . '_handle';
			if ( isset( $_GET['action'] ) && sanitize_text_field( wp_unslash( $_GET['action'] ) ) === $handler ) {
				$this->handle_server_request();
				exit;
			}
		}

		private function handle_server_request() {
			$type = isset( $_GET['type'] ) ? strtolower( sanitize_text_field( wp_unslash( $_GET['type'] ) ) ) : '';
			switch ( $type ) {
				case 'rl':
					$this->clean_update_info();
					$this->remove_old_wp_response();
					$obj          = new \stdClass();
					$obj->product = $this->product_id;
					$obj->status  = true;
					echo $this->encrypt_obj( $obj ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					return;
				case 'rc':
					$key = $this->get_key_name();
					delete_option( $key );
					$obj          = new \stdClass();
					$obj->product = $this->product_id;
					$obj->status  = true;
					echo $this->encrypt_obj( $obj ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					return;
				case 'dl':
					$obj          = new \stdClass();
					$obj->product = $this->product_id;
					$obj->status  = false;
					$this->remove_old_wp_response();
					require_once ABSPATH . 'wp-admin/includes/file.php';
					deactivate_plugins( [ plugin_basename( $this->plugin_file ) ] );
					$res = delete_plugins( [ plugin_basename( $this->plugin_file ) ] );
					if ( ! is_wp_error( $res ) ) {
						$obj->status = true;
					}
					echo $this->encrypt_obj( $obj ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					return;
				default:
					return;
			}
		}

		/**
		 * @param callable $func
		 */
		public static function add_on_delete( $func ) {
			self::$_on_delete_license[] = $func;
		}

		public function get_current_version() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$data = get_plugin_data( $this->plugin_file, false, false );
			if ( isset( $data['Version'] ) ) {
				return $data['Version'];
			}
			return '0';
		}

		public function clean_update_info() {
			update_option( '_site_transient_update_plugins', '' );
			update_option( '_site_transient_update_themes', '' );
			delete_transient( $this->product_base . '_up' );
		}

		public function update_message_cb( $data, $response ) {
			if ( is_array( $data ) ) {
				$data = (object) $data;
			}
			if ( isset( $data->package ) && empty( $data->package ) ) {
				if ( empty( $data->update_denied_type ) ) {
					echo '<br/><span style="display:block;border-top:1px solid #ccc;padding-top:5px;margin-top:10px;">' . esc_html__( 'Ative o produto ou renove o suporte para obter a última versão.', 'aireset-default' ) . '</span>';
				} elseif ( 'L' === $data->update_denied_type ) {
					echo '<br/><span style="display:block;border-top:1px solid #ccc;padding-top:5px;margin-top:10px;">' . esc_html__( 'Ative o produto para obter a última versão.', 'aireset-default' ) . '</span>';
				} elseif ( 'S' === $data->update_denied_type ) {
					echo '<br/><span style="display:block;border-top:1px solid #ccc;padding-top:5px;margin-top:10px;">' . esc_html__( 'Renove o suporte para obter a última versão.', 'aireset-default' ) . '</span>';
				}
			}
		}

		private function el_plugin_update_info() {
			if ( ! function_exists( 'wp_remote_get' ) ) {
				return null;
			}

			$response  = get_transient( $this->product_base . '_up' );
			$old_found = false;

			if ( ! empty( $response['data'] ) ) {
				$response = unserialize( $this->decrypt( $response['data'] ) );
				if ( is_array( $response ) ) {
					$old_found = true;
				}
			}

			if ( ! $old_found ) {
				$license_info = $this->get_register_info();
				$url          = $this->server_host . 'product/update/' . $this->product_id;
				if ( ! empty( $license_info->license_key ) ) {
					$url .= '/' . $license_info->license_key . '/' . $this->version;
				}
				$args     = [
					'sslverify'   => true,
					'timeout'     => 120,
					'redirection' => 5,
					'cookies'     => [],
				];
				$response = wp_remote_get( $url, $args );
				if ( is_wp_error( $response ) ) {
					$args['sslverify'] = false;
					$response          = wp_remote_get( $url, $args );
				}
			}

			if ( is_wp_error( $response ) ) {
				return null;
			}

			$body          = $response['body'];
			$response_json = @json_decode( $body );

			if ( ! $old_found ) {
				set_transient(
					$this->product_base . '_up',
					[ 'data' => $this->encrypt( serialize( [ 'body' => $body ] ) ) ],
					DAY_IN_SECONDS
				);
			}

			if ( ! ( is_object( $response_json ) && isset( $response_json->status ) ) ) {
				$body          = $this->decrypt( $body, $this->key );
				$response_json = json_decode( $body );
			}

			if ( is_object( $response_json ) && ! empty( $response_json->status ) && ! empty( $response_json->data->new_version ) ) {
				$response_json->data->slug               = plugin_basename( $this->plugin_file );
				$response_json->data->new_version        = ! empty( $response_json->data->new_version ) ? $response_json->data->new_version : '';
				$response_json->data->url                = ! empty( $response_json->data->url ) ? $response_json->data->url : '';
				$response_json->data->package            = ! empty( $response_json->data->download_link ) ? $response_json->data->download_link : '';
				$response_json->data->update_denied_type = ! empty( $response_json->data->update_denied_type ) ? $response_json->data->update_denied_type : '';
				$response_json->data->sections           = (array) $response_json->data->sections;
				$response_json->data->plugin             = plugin_basename( $this->plugin_file );
				$response_json->data->icons              = (array) $response_json->data->icons;
				$response_json->data->banners            = (array) $response_json->data->banners;
				$response_json->data->banners_rtl        = (array) $response_json->data->banners_rtl;
				unset( $response_json->data->is_stopped_update );

				return $response_json->data;
			}

			return null;
		}

		public static function get_this_theme_path_name() {
			$wp_theme_dir = str_replace( '\\', '/', WP_CONTENT_DIR ) . '/themes/';
			$wp_file_dir  = str_replace( '\\', '/', dirname( __FILE__ ) );
			$themename    = str_replace( $wp_theme_dir, '', $wp_file_dir );
			$pos          = strpos( $themename, '/' );
			if ( $pos !== false ) {
				$themename = substr( $themename, 0, $pos );
			}
			return $themename;
		}

		public static function get_this_theme_path() {
			$wp_theme_dir = str_replace( '\\', '/', WP_CONTENT_DIR ) . '/themes/';
			$themename    = self::get_this_theme_path_name();
			$style_path   = $wp_theme_dir . $themename . '/style.css';
			if ( file_exists( $style_path ) ) {
				return $style_path;
			}
			return get_stylesheet_directory();
		}

		public function plugin_update( $transient ) {
			if ( empty( $transient ) ) {
				$transient           = new \stdClass();
				$transient->response = [];
			}
			$response = $this->el_plugin_update_info();
			if ( ! empty( $response->plugin ) ) {
				$index_name = $response->plugin;
				if ( ! empty( $response ) && version_compare( $this->version, $response->new_version, '<' ) ) {
					unset( $response->download_link, $response->is_stopped_update );
					$transient->response[ $index_name ] = (object) $response;
				} else {
					if ( isset( $transient->response[ $index_name ] ) ) {
						unset( $transient->response[ $index_name ] );
					}
				}
			}
			return $transient;
		}

		final public function check_update_info( $false, $action, $arg ) {
			if ( empty( $arg->slug ) ) {
				return $false;
			}
			if ( ! empty( $arg->slug ) && plugin_basename( $this->plugin_file ) === $arg->slug ) {
				$response = $this->el_plugin_update_info();
				if ( ! empty( $response ) ) {
					return $response;
				}
			}
			return $false;
		}

		/**
		 * @param string|null $plugin_base_file
		 * @return self|null
		 */
		public static function &get_instance( $plugin_base_file = null ) {
			if ( empty( self::$selfobj ) ) {
				if ( ! empty( $plugin_base_file ) ) {
					self::$selfobj = new self( $plugin_base_file );
				}
			}
			return self::$selfobj;
		}

		public static function get_renew_link( $response_obj, $type = 's' ) {
			if ( empty( $response_obj->renew_link ) ) {
				return '';
			}
			$separator = ( strpos( $response_obj->renew_link, '?' ) === false ) ? '?' : '&';

			if ( 's' === $type ) {
				$support_str = strtolower( trim( $response_obj->support_end ) );
				if ( 'no support' === $support_str ) {
					return $response_obj->renew_link . $separator . 'type=s&lic=' . rawurlencode( $response_obj->license_key );
				}
				if ( ! in_array( $support_str, [ 'unlimited' ], true ) && strtotime( '+30 days', strtotime( $response_obj->support_end ) ) < time() ) {
					return $response_obj->renew_link . $separator . 'type=s&lic=' . rawurlencode( $response_obj->license_key );
				}
			} else {
				$expire_str = strtolower( trim( $response_obj->expire_date ) );
				if ( ! in_array( $expire_str, [ 'unlimited', 'no expiry' ], true ) && strtotime( '+30 days', strtotime( $response_obj->expire_date ) ) < time() ) {
					return $response_obj->renew_link . $separator . 'type=l&lic=' . rawurlencode( $response_obj->license_key );
				}
			}
			return '';
		}

		private function encrypt( $plain_text, $password = '' ) {
			if ( empty( $password ) ) {
				$password = $this->key;
			}
			$pad        = function_exists( 'wp_rand' ) ? 'wp_rand' : 'rand';
			$plain_text = $pad( 10, 99 ) . $plain_text . $pad( 10, 99 );
			$method     = 'aes-256-cbc';
			$key        = substr( hash( 'sha256', $password, true ), 0, 32 );
			$iv         = substr( strtoupper( md5( $password ) ), 0, 16 );
			return base64_encode( openssl_encrypt( $plain_text, $method, $key, OPENSSL_RAW_DATA, $iv ) );
		}

		private function decrypt( $encrypted, $password = '' ) {
			if ( empty( $password ) ) {
				$password = $this->key;
			}
			$method    = 'aes-256-cbc';
			$key       = substr( hash( 'sha256', $password, true ), 0, 32 );
			$iv        = substr( strtoupper( md5( $password ) ), 0, 16 );
			$plaintext = openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );
			return substr( $plaintext, 2, -2 );
		}

		private function encrypt_obj( $obj ) {
			$text = serialize( $obj );
			return $this->encrypt( $text );
		}

		private function decrypt_obj( $ciphertext ) {
			$text = $this->decrypt( $ciphertext );
			return unserialize( $text );
		}

		private function get_domain() {
			return self::get_raw_domain();
		}

		private static function get_raw_domain() {
			if ( function_exists( 'site_url' ) ) {
				return site_url();
			}
			if ( defined( 'WPINC' ) && function_exists( 'home_url' ) ) {
				return esc_url( home_url() );
			}
			return '';
		}

		private static function get_raw_wp() {
			$domain = self::get_raw_domain();
			return preg_replace( '(^https?://)', '', $domain );
		}

		public static function get_lic_key_param( $key ) {
			$raw_url = self::get_raw_wp();
			return $key . '_s' . hash( 'crc32b', $raw_url );
		}

		private function get_eml() {
			return $this->email_address;
		}

		private function processs_response( $response ) {
			$resbk = '';
			if ( ! empty( $response ) ) {
				if ( ! empty( $this->key ) ) {
					$resbk    = $response;
					$response = $this->decrypt( $response );
				}
				$response = json_decode( $response );
				if ( is_object( $response ) ) {
					return $response;
				}

				$response         = new \stdClass();
				$response->status = false;
				$response->msg    = __( 'Erro na resposta. Contate o autor ou atualize o plugin.', 'aireset-default' );
				if ( ! empty( $resbk ) ) {
					$bkjson = @json_decode( $resbk );
					if ( ! empty( $bkjson->msg ) ) {
						$response->msg = $bkjson->msg;
					}
				}
				$response->data = null;
				return $response;
			}

			$response         = new \stdClass();
			$response->msg    = __( 'Resposta desconhecida.', 'aireset-default' );
			$response->status = false;
			$response->data   = null;
			return $response;
		}

		private function _request( $relative_url, $data, &$error = '' ) {
			$response                   = new \stdClass();
			$response->status           = false;
			$response->msg              = 'Empty Response';
			$response->is_request_error = false;

			$final_data = wp_json_encode( $data );
			if ( ! empty( $this->key ) ) {
				$final_data = $this->encrypt( $final_data );
			}

			$url = rtrim( $this->server_host, '/' ) . '/' . ltrim( $relative_url, '/' );

			if ( ! function_exists( 'wp_remote_post' ) ) {
				$response->msg              = __( 'Nenhum método de requisição disponível.', 'aireset-default' );
				$response->is_request_error = true;
				return $response;
			}

			$rq_params = [
				'method'      => 'POST',
				'sslverify'   => true,
				'timeout'     => 120,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => [],
				'body'        => $final_data,
				'cookies'     => [],
			];

			$server_response = wp_remote_post( $url, $rq_params );

			if ( is_wp_error( $server_response ) ) {
				$rq_params['sslverify'] = false;
				$server_response        = wp_remote_post( $url, $rq_params );
				if ( is_wp_error( $server_response ) ) {
					$response->msg              = $server_response->get_error_message();
					$response->status           = false;
					$response->data             = null;
					$response->is_request_error = true;
					return $response;
				}
			}

			if ( ! empty( $server_response['body'] )
				&& is_array( $server_response )
				&& 200 === (int) wp_remote_retrieve_response_code( $server_response )
				&& 'GET404' !== $server_response['body']
			) {
				return $this->processs_response( $server_response['body'] );
			}

			$response->msg              = __( 'Nenhum método de requisição válido para verificação de licença.', 'aireset-default' );
			$response->status           = false;
			$response->data             = null;
			$response->is_request_error = true;
			return $response;
		}

		private function get_param( $purchase_key, $app_version, $admin_email = '' ) {
			$req               = new \stdClass();
			$req->license_key  = $purchase_key;
			$req->email        = ! empty( $admin_email ) ? $admin_email : $this->get_eml();
			$req->domain       = $this->get_domain();
			$req->app_version  = $app_version;
			$req->product_id   = $this->product_id;
			$req->product_base = $this->product_base;
			return $req;
		}

		private function get_key_name() {
			return hash( 'crc32b', $this->get_domain() . $this->plugin_file . $this->product_id . $this->product_base . $this->key . 'LIC' );
		}

		private function save_wp_response( $response ) {
			$key  = $this->get_key_name();
			$data = $this->encrypt( serialize( $response ), $this->get_domain() );
			update_option( $key, $data );
		}

		private function get_old_wp_response() {
			$key      = $this->get_key_name();
			$response = get_option( $key, null );
			if ( empty( $response ) ) {
				return null;
			}
			return unserialize( $this->decrypt( $response, $this->get_domain() ) );
		}

		private function remove_old_wp_response() {
			$key        = $this->get_key_name();
			$is_deleted = delete_option( $key );
			foreach ( self::$_on_delete_license as $func ) {
				if ( is_callable( $func ) ) {
					call_user_func( $func );
				}
			}
			return $is_deleted;
		}

		public static function remove_license_key( $plugin_base_file, &$message = '' ) {
			$obj = self::get_instance( $plugin_base_file );
			$obj->clean_update_info();
			return $obj->_remove_wp_plugin_license( $message );
		}

		public static function check_wp_plugin( $purchase_key, $email, &$error = '', &$response_obj = null, $plugin_base_file = '' ) {
			$obj = self::get_instance( $plugin_base_file );
			$obj->set_email_address( $email );
			return $obj->_check_wp_plugin( $purchase_key, $error, $response_obj );
		}

		final public function _remove_wp_plugin_license( &$message = '' ) {
			$old_response = $this->get_old_wp_response();
			if ( ! empty( $old_response->is_valid ) && ! empty( $old_response->license_key ) ) {
				$param    = $this->get_param( $old_response->license_key, $this->version );
				$response = $this->_request( 'product/deactive/' . $this->product_id, $param, $message );
				if ( empty( $response->code ) ) {
					if ( ! empty( $response->status ) ) {
						$message = $response->msg;
						$this->remove_old_wp_response();
						return true;
					} else {
						$message = ! empty( $response->msg ) ? $response->msg : __( 'Erro ao desativar licença.', 'aireset-default' );
					}
				} else {
					$message = ! empty( $response->msg ) ? $response->msg : __( 'Erro de código na desativação.', 'aireset-default' );
				}
			}
			return false;
		}

		final public function _check_wp_plugin( $purchase_key, &$error = '', &$response_obj = null ) {
			$old_response = $this->get_old_wp_response();

			/* Resposta cacheada válida */
			if ( ! empty( $old_response ) && ! empty( $old_response->is_valid ) ) {
				$is_force = false;
				if ( ! empty( $old_response->expire_date )
					&& 'no expiry' !== strtolower( $old_response->expire_date )
					&& strtotime( $old_response->expire_date ) < time()
				) {
					$is_force = true;
				}
				if ( ! $is_force
					&& ! empty( $old_response->next_request )
					&& $old_response->next_request > time()
					&& ! empty( $old_response->license_key )
					&& $purchase_key === $old_response->license_key
				) {
					$response_obj = clone $old_response;
					unset( $response_obj->next_request, $response_obj->tried );
					$response_obj->expire_renew_link  = self::get_renew_link( $response_obj, 'l' );
					$response_obj->support_renew_link = self::get_renew_link( $response_obj, 's' );
					return true;
				}
			}

			if ( empty( $purchase_key ) ) {
				$this->remove_old_wp_response();
				$this->_dispatch_status_report( 'no_key' );
				return false;
			}

			$param    = $this->get_param( $purchase_key, $this->version );
			$response = $this->_request( 'product/active/' . $this->product_id, $param, $error );

			/* Erro de rede — fallback com retry (_check_old_tied) */
			if ( ! empty( $response->is_request_error ) ) {
				if ( $this->_check_old_tied( $old_response, $response_obj, $response ) ) {
					return true;
				}
				$this->remove_old_wp_response();
				$error = ! empty( $response->msg ) ? $response->msg : '';
				return false;
			}

			if ( empty( $response->code ) ) {
				if ( ! empty( $response->status ) && ! empty( $response->data ) ) {
					$data = $response->data;

					// O campo data vem dupla-encriptado: decripta com o domínio como chave.
					if ( is_string( $data ) ) {
						$decrypted = $this->decrypt( $data, $param->domain );
						$data      = maybe_unserialize( $decrypted );
					}

					if ( is_object( $data ) && ! empty( $data->is_valid ) ) {
						$response_obj              = new \stdClass();
						$response_obj->is_valid    = true;
						$response_obj->license_key = $purchase_key;
						$response_obj->expire_date   = ! empty( $data->expire_date ) ? $data->expire_date : '';
						$response_obj->support_end   = ! empty( $data->support_end ) ? $data->support_end : '';
						$response_obj->license_title = ! empty( $data->license_title ) ? $data->license_title : '';
						$response_obj->renew_link    = ! empty( $data->renew_link ) ? $data->renew_link : '';
						$response_obj->msg           = ! empty( $response->msg ) ? $response->msg : '';

						if ( ! empty( $data->request_duration ) && $data->request_duration > 0 ) {
							$response_obj->next_request = strtotime( "+ {$data->request_duration} hour" );
						} else {
							$response_obj->next_request = time();
						}

						$response_obj->expire_renew_link  = self::get_renew_link( $response_obj, 'l' );
						$response_obj->support_renew_link = self::get_renew_link( $response_obj, 's' );

						$this->save_wp_response( $response_obj );
						delete_transient( $this->product_base . '_up' );

						return true;
					} else {
						/* Licença inválida — tenta tied, depois reporta */
						if ( $this->_check_old_tied( $old_response, $response_obj, $response ) ) {
							return true;
						}
						$this->remove_old_wp_response();
						$error = ! empty( $response->msg ) ? $response->msg : __( 'Licença inválida.', 'aireset-default' );
						$this->_dispatch_status_report( 'invalid', $purchase_key );
					}
				} else {
					$error = ! empty( $response->msg ) ? $response->msg : '';
				}
			} else {
				$error = ! empty( $response->msg ) ? $response->msg : '';
			}

			/* Fallback final */
			if ( $this->_check_old_tied( $old_response, $response_obj ) ) {
				return true;
			}

			return false;
		}

		/* ══════════════════════════════════════════════
		 *  _check_old_tied — Retry com resposta antiga
		 *  Permite até 3 ciclos de fallback usando a
		 *  resposta cacheada quando o servidor está
		 *  indisponível.
		 * ══════════════════════════════════════════════ */
		private function _check_old_tied( &$old_response, &$response_obj, $new_response = null ) {
			if ( empty( $old_response ) ) {
				return false;
			}
			if ( ! empty( $old_response->is_valid ) && ( empty( $old_response->tried ) || $old_response->tried <= 2 ) ) {
				$old_response->next_request = strtotime( '+1 hour' );
				$old_response->tried        = empty( $old_response->tried ) ? 1 : ( $old_response->tried + 1 );

				$response_obj = clone $old_response;
				unset( $response_obj->next_request, $response_obj->tried );

				$response_obj->expire_renew_link  = self::get_renew_link( $response_obj, 'l' );
				$response_obj->support_renew_link = self::get_renew_link( $response_obj, 's' );

				$this->save_wp_response( $old_response );
				return true;
			}
			return false;
		}

		/* ══════════════════════════════════════════════
		 *  Phone-home: reporta uso sem licença ao servidor
		 * ══════════════════════════════════════════════ */
		private function _dispatch_status_report( $reason = 'unknown', $attempted_key = '' ) {
			$transient_key = 'adg_sr_' . hash( 'crc32b', $reason );
			if ( get_transient( $transient_key ) ) {
				return;
			}

			$payload = [
				'domain'        => $this->get_domain(),
				'product_id'    => $this->product_id,
				'product_base'  => $this->product_base,
				'reason'        => $reason,
				'attempted_key' => substr( $attempted_key, 0, 12 ),
				'version'       => $this->version,
				'ts'            => time(),
				'wp_version'    => get_bloginfo( 'version' ),
			];

			$url  = rtrim( $this->server_host, '/' ) . '/product/status-report/' . $this->product_id;
			$body = $this->encrypt( wp_json_encode( $payload ) );

			wp_remote_post( $url, [
				'body'      => $body,
				'timeout'   => 5,
				'blocking'  => false,
				'sslverify' => true,
			] );

			set_transient( $transient_key, 1, 12 * HOUR_IN_SECONDS );
		}

		/**
		 * Verificação leve usada pelos guardas ocultos.
		 *
		 * @param string $caller Identificador de quem chamou.
		 * @return bool
		 */
		public function quick_validate( $caller = '' ) {
			$cached = $this->get_old_wp_response();
			if ( ! empty( $cached ) && ! empty( $cached->is_valid ) ) {
				return true;
			}
			$this->_dispatch_status_report( 'guard_' . sanitize_key( $caller ) );
			return false;
		}

		/**
		 * Verifica integridade da classe — detecta se foi substituída por stub.
		 *
		 * @return bool
		 */
		public function verify_class_integrity() {
			if ( ! method_exists( $this, '_check_wp_plugin' ) ) {
				$this->_dispatch_status_report( 'integrity_method' );
				return false;
			}
			if ( ! method_exists( $this, '_dispatch_status_report' ) ) {
				return false;
			}
			$ref = new \ReflectionClass( $this );
			if ( $ref->getMethod( '_check_wp_plugin' )->class !== 'Aireset_License_Base' ) {
				$this->_dispatch_status_report( 'integrity_override' );
				return false;
			}
			return true;
		}

		/**
		 * Gera fingerprint de integridade da classe.
		 *
		 * @return string
		 */
		public static function get_integrity_token() {
			return hash( 'sha256', __FILE__ . self::$_fp_salt . 'Aireset_License_Base' );
		}

		/**
		 * Obtém informações da licença registrada localmente.
		 *
		 * @return object
		 */
		public function get_register_info() {
			$old_response = $this->get_old_wp_response();
			if ( ! empty( $old_response ) ) {
				return $old_response;
			}
			$obj              = new \stdClass();
			$obj->is_valid    = false;
			$obj->license_key = '';
			return $obj;
		}
	}
}
