<?php

namespace Aireset\Default;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Carrega as classes
require_once AIRESET_DEFAULT_INC_PATH . 'shipping/class-shipping-calculator.php';
require_once AIRESET_DEFAULT_INC_PATH . 'orders/class-order-status.php';
require_once AIRESET_DEFAULT_INC_PATH . 'orders/class-order-customer.php';
require_once AIRESET_DEFAULT_INC_PATH . 'images/class-image-sizes.php';
require_once AIRESET_DEFAULT_INC_PATH . 'shortcodes/class-whatsapp-shortcode.php';
require_once AIRESET_DEFAULT_INC_PATH . 'frontend/class-viewport-manager.php';
require_once AIRESET_DEFAULT_INC_PATH . 'checkout/class-guest-payment.php';
require_once AIRESET_DEFAULT_INC_PATH . 'cart/class-cart-message-manager.php';
require_once AIRESET_DEFAULT_INC_PATH . 'integrations/class-yith-search-manager.php';
require_once AIRESET_DEFAULT_INC_PATH . 'permalinks/class-permalink-manager.php';
require_once AIRESET_DEFAULT_INC_PATH . 'orders/class-order-columns.php';
require_once AIRESET_DEFAULT_INC_PATH . 'admin/class-admin-notices.php';

// Inicializa as classes
new \Aireset\Default\Shipping\ShippingCalculator();
new \Aireset\Default\Orders\OrderStatus();
new \Aireset\Default\Orders\OrderCustomer();
new \Aireset\Default\Orders\OrderColumns();
new \Aireset\Default\Images\ImageSizes();
new \Aireset\Default\Shortcodes\WhatsappShortcode();
new \Aireset\Default\Frontend\ViewportManager();
new \Aireset\Default\Checkout\GuestPayment();
new \Aireset\Default\Cart\CartMessageManager();
new \Aireset\Default\Integrations\YithSearchManager();
new \Aireset\Default\Permalinks\PermalinkManager();

add_action('elementor/dynamic_tags/register_tags', function($dynamic_tags) {
    require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags.php';
    $dynamic_tags->register_tag('Aireset\Default\Elementor\Aireset_Dynamic_Text_Tag');
    $dynamic_tags->register_tag('Aireset\Default\Elementor\Aireset_Dynamic_URL_Tag');
});


