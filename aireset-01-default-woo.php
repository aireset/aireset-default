<?php
/*
Plugin Name: Aireset - 01 - Configurações Padrões Woocomerce - Aireset
Description: Cria e Padroniza diversas configurações padrões para os E-commerces
Version: 1.0
Author: Felipe Almeman - Aireset Agencia Web
Link: https://aireset.com.br
*/

defined('ABSPATH') or die('No script kiddies please!');

function aireset_registrar_log($mensagem) {
    $log_handler = new WC_Log_Handler_File('aireset_verificar_pedidos');
    $logger = wc_get_logger();
    $logger->info($mensagem, array('source' => 'aireset_verificar_pedidos'));
}
function highlight_array($array, $name = 'var') {
    highlight_string("<?php\n\$$name =\n" . var_export($array, true) . ";\n?>");
}

// Adiciona um item de menu no admin do WordPress
add_action('admin_menu', 'aireset_default_wooo_add_admin_menu');

function aireset_default_wooo_add_admin_menu() {
    add_menu_page(
        'Aireset', // Título da página
        'Aireset', // Título do menu
        'manage_options',        // Capacidade necessária
        'aireset-default-woocommerce', // Slug da página
        '' // Função que gera o conteúdo
    );
}

// Inclui funções adicionais
include_once plugin_dir_path(__FILE__) . 'includes/status.php';
include_once plugin_dir_path(__FILE__) . 'includes/cart.php';