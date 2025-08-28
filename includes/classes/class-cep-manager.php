<?php

namespace Aireset\Default;

use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * CEP Manager Class
 * 
 * @since 1.3.5
 */
class CEP_Manager {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_aireset_buscar_cep', array($this, 'buscar_cep'));
    }

    /**
     * Busca o CEP usando o CEP Promise
     */
    public function buscar_cep() {
        check_ajax_referer('aireset_cep_autofill', 'nonce');

        if (!isset($_POST['cep'])) {
            wp_send_json_error('CEP não fornecido');
        }

        $cep = sanitize_text_field($_POST['cep']);
        $cep = preg_replace('/[^0-9]/', '', $cep);

        try {
            $address = CepPromise::fetch($cep);
            
            wp_send_json_success(array(
                'logradouro' => $address->street,
                'bairro' => $address->district,
                'cidade' => $address->city,
                'uf' => $address->state,
                'cep' => $address->zipCode
            ));
        } catch (CepPromiseException $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}

new CEP_Manager();
