<?php

namespace Aireset\Default\Shortcodes;

class WhatsappShortcode {
    
    public function __construct() {
        add_shortcode('whatsapp_link', [$this, 'whatsapp_link_shortcode']);
    }

    public function whatsapp_link_shortcode($atts) {
        // Atributos padrão
        $atts = shortcode_atts(
            array(
                'number' => '',  // Número de telefone formatado
                'message' => ''  // Mensagem personalizada
            ),
            $atts,
            'whatsapp_link'
        );
    
        // Remove espaços, parênteses, traços e outros caracteres não numéricos
        $phone_number = preg_replace('/\D+/', '', $atts['number']);
    
        // Obter o título da página ou do produto
        $page_title = get_the_title();
        
        // Verificar se é um produto e obter o título do produto
        $produto_title = function_exists('wc_get_product') ? get_the_title() : '';
    
        // Mensagem padrão
        if (empty($atts['message'])) {
            $message = "Olá, estou na página {title} e gostaria de mais informações.";
        } else {
            $message = $atts['message'];
        }
    
        // Substituir os marcadores {title} e {produto_title} pela informação correspondente
        $message = str_replace('{title}', $page_title, $message);
        $message = str_replace('{produto_title}', $produto_title, $message);
        $whatsapp_message = urlencode($message);
    
        $whatsapp_link = "https://wa.me/{$phone_number}?text={$whatsapp_message}";
    
        return $whatsapp_link;
    }
}