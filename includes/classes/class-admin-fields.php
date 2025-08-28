<?php

namespace Aireset\Default;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Classe para gerenciar campos do admin
 */
class Admin_Fields {
    /**
     * Constructor
     */
    public function __construct() {
        // Reordena os campos no admin
        add_filter('woocommerce_admin_billing_fields', array($this, 'reorder_admin_billing_fields'), 10, 1);
        add_filter('woocommerce_admin_shipping_fields', array($this, 'reorder_admin_shipping_fields'), 10, 1);
    }

    /**
     * Reordena os campos de cobrança
     */
    public function reorder_admin_billing_fields($fields) {
        if (isset($fields['postcode']) && isset($fields['address_1'])) {
            // Guarda o campo CEP
            $postcode = $fields['postcode'];
            
            // Remove o CEP da posição atual
            unset($fields['postcode']);
            
            // Se existirem campos de estado e país
            if (isset($fields['country']) && isset($fields['state'])) {
                // Guarda os campos
                $country = $fields['country'];
                $state = $fields['state'];
                
                // Define Brasil como padrão se não houver país definido
                if (empty($fields['country']['value'])) {
                    $country['value'] = 'BR';
                }
                
                // Remove da posição atual
                unset($fields['country'], $fields['state']);
            }
            
            // Reordena os campos
            $new_fields = array();
            foreach ($fields as $key => $field) {
                if ($key === 'address_1') {
                    $new_fields['postcode'] = $postcode;
                }
                $new_fields[$key] = $field;
            }
            
            // Adiciona estado e país no final, estado primeiro
            if (isset($state)) {
                $new_fields['state'] = $state;
                $new_fields['country'] = $country;
            }
            
            return $new_fields;
        }
        
        return $fields;
    }

    /**
     * Reordena os campos de entrega
     */
    public function reorder_admin_shipping_fields($fields) {
        if (isset($fields['postcode']) && isset($fields['address_1'])) {
            // Guarda o campo CEP
            $postcode = $fields['postcode'];
            
            // Remove o CEP da posição atual
            unset($fields['postcode']);
            
            // Se existirem campos de estado e país
            if (isset($fields['country']) && isset($fields['state'])) {
                // Guarda os campos
                $country = $fields['country'];
                $state = $fields['state'];
                
                // Define Brasil como padrão se não houver país definido
                if (empty($fields['country']['value'])) {
                    $country['value'] = 'BR';
                }
                
                // Remove da posição atual
                unset($fields['country'], $fields['state']);
            }
            
            // Reordena os campos
            $new_fields = array();
            foreach ($fields as $key => $field) {
                if ($key === 'address_1') {
                    $new_fields['postcode'] = $postcode;
                }
                $new_fields[$key] = $field;
            }
            
            // Adiciona estado e país no final, estado primeiro
            if (isset($state)) {
                $new_fields['state'] = $state;
                $new_fields['country'] = $country;
            }
            
            return $new_fields;
        }
        
        return $fields;
    }
}

new Admin_Fields();
