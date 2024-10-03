<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    function aireset_add_custom_order_status() {
        // register_post_status( 'wc-payed', [
        //     'label'                     => 'Pedido Pago',
        //     'public'                    => true,
        //     'show_in_admin_status_list' => true,
        //     'show_in_admin_all_list'    => true,
        //     'exclude_from_search'       => false,
        //     'label_count'               => _n_noop( 'Pedidos Pagos <span class="count">(%s)</span>', 'Pedidos Pago <span class="count">(%s)</span>' )
        // ] );
        // Pedido Pago Parcialmente
        register_post_status( 'wc-partial-payed', [
            'label'                     => _x( 'Pedido Pago Parcialmente', 'Order status', 'aireset-01-default-woo' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            /* translators: %s: Number of partial paid orders */
            'label_count'               => _n_noop(
                'Pedidos Pagos Parcialmente <span class="count">(%s)</span>', 
                'Pedidos Pago Parcialmente <span class="count">(%s)</span>', 
                'aireset-01-default-woo'
            )
        ]);

        // Em Produção
        register_post_status( 'wc-in-production', [
            'label'                     => _x( 'Em Produção', 'Order status', 'aireset-01-default-woo' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            /* translators: %s: Number of orders in production */
            'label_count'               => _n_noop( 
                'Em Produção <span class="count">(%s)</span>', 
                'Em Produção <span class="count">(%s)</span>', 
                'aireset-01-default-woo'
            )
        ]);

        // Em Separação
        register_post_status( 'wc-in-separation', [
            'label'                     => _x( 'Em Separação', 'Order status', 'aireset-01-default-woo' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            /* translators: %s: Number of orders in separation */
            'label_count'               => _n_noop( 
                'Em Separação <span class="count">(%s)</span>', 
                'Em Separação <span class="count">(%s)</span>', 
                'aireset-01-default-woo'
            )
        ]);

        // Pedido Enviado
        register_post_status( 'wc-order-sended', [
            'label'                     => _x( 'Pedido Enviado', 'Order status', 'aireset-01-default-woo' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            /* translators: %s: Number of sent orders */
            'label_count'               => _n_noop( 
                'Pedidos Enviados <span class="count">(%s)</span>', 
                'Pedidos Enviados <span class="count">(%s)</span>', 
                'aireset-01-default-woo'
            )
        ]);
    }
    add_action( 'init', 'aireset_add_custom_order_status' );

    /**
     * Edita títulos dos status padrão de pedidos
     */
    function aireset_edit_default_order_status_titles( $order_statuses ) {
        if ( isset( $order_statuses['wc-on-hold'] ) ) {
            $order_statuses['wc-on-hold'] = _x( 'Aguardando Confirmação', 'Order status', 'aireset-01-default-woo' );
        }
        if ( isset( $order_statuses['wc-pending'] ) ) {
            $order_statuses['wc-pending'] = _x( 'Pagamento Pendente', 'Order status', 'aireset-01-default-woo' );
        }
        if ( isset( $order_statuses['wc-processing'] ) ) {
            $order_statuses['wc-processing'] = _x( 'Pedido Pago', 'Order status', 'aireset-01-default-woo' );
        }
        if ( isset( $order_statuses['wc-cancelled'] ) ) {
            $order_statuses['wc-cancelled'] = _x( 'Cancelado', 'Order status', 'aireset-01-default-woo' );
        }
        if ( isset( $order_statuses['wc-order-completed'] ) ) {
            $order_statuses['wc-order-completed'] = _x( 'Concluído', 'Order status', 'aireset-01-default-woo' );
        }
        if ( isset( $order_statuses['wc-order-refunded'] ) ) {
            $order_statuses['wc-order-refunded'] = _x( 'Reembolsado', 'Order status', 'aireset-01-default-woo' );
        }
        if ( isset( $order_statuses['wc-order-failed'] ) ) {
            $order_statuses['wc-order-failed'] = _x( 'Malsucedido', 'Order status', 'aireset-01-default-woo' );
        }

        $new_order_statuses = array();
        foreach ( $order_statuses as $key => $status ) {
            $new_order_statuses[ $key ] = $status;
            if ( 'wc-processing' === $key ) {
                $new_order_statuses['wc-partial-payed'] = _x( 'Pedido Pago Parcialmente', 'Order status', 'aireset-01-default-woo' );
                $new_order_statuses['wc-in-production'] = _x( 'Em Produção', 'Order status', 'aireset-01-default-woo' );
                $new_order_statuses['wc-in-separation'] = _x( 'Em Separação', 'Order status', 'aireset-01-default-woo' );
                $new_order_statuses['wc-order-sended'] = _x( 'Pedido Enviado', 'Order status', 'aireset-01-default-woo' );
            }
        }
        return $new_order_statuses;
    }
    add_filter( 'wc_order_statuses', 'aireset_edit_default_order_status_titles' );

    /**
     * Adiciona cores personalizadas para os status de pedidos
     */
    function aireset_custom_order_status_color() {
        // Enqueue estilo personalizado
        wp_enqueue_style( 'aireset-order-status-styles', Aireset_General_Plugin::get_assets_url() . 'css/order-status-styles.css' );
    }

    // Adiciona o hook para carregar o estilo na área administrativa
    add_action('admin_enqueue_scripts', 'aireset_custom_order_status_color');

    /**
     * Adiciona ações em massa personalizadas para os pedidos
     */
    function aireset_add_custom_order_status_bulk_actions($bulk_actions) {
        $bulk_actions['mark_on-hold'] = _x( 'Marcar como aguardando', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_pending'] = _x( 'Marcar como pendente', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_processing'] = _x( 'Marcar como processando', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_cancelled'] = _x( 'Marcar como cancelado', 'Bulk action', 'aireset-01-default-woo' );	
        $bulk_actions['mark_partial_payed'] = _x( 'Marcar como pago parcialmente', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_in-production'] = _x( 'Marcar como em produção', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_in-separation'] = _x( 'Marcar como em separação', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_order-sended'] = _x( 'Marcar como pedido enviado', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_order-completed'] = _x( 'Marcar como concluído', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_order-refunded'] = _x( 'Marcar como reembolsado', 'Bulk action', 'aireset-01-default-woo' );
        $bulk_actions['mark_order-failed'] = _x( 'Marcar como falho', 'Bulk action', 'aireset-01-default-woo' );
        
        return $bulk_actions;
    }
    add_filter('bulk_actions-edit-shop_order', 'aireset_add_custom_order_status_bulk_actions');

    /**
     * Inclui os status personalizados nos relatórios
     */
    function aireset_include_custom_order_status_in_reports( $order_statuses ) {
        $order_statuses[] = 'wc-partial-payed'; // Adiciona o status personalizado aos relatórios
        $order_statuses[] = 'wc-in-production';
        $order_statuses[] = 'wc-in-separation';
        $order_statuses[] = 'wc-order-sended'; // Adiciona o status personalizado aos relatórios
        return $order_statuses;
    }
    add_filter( 'woocommerce_reports_order_statuses', 'aireset_include_custom_order_status_in_reports' );

    /**
     * Desabilita notificações de email do WooCommerce
     */
    function aireset_disable_all_woocommerce_emails( $email_enabled, $email ) {
        return false;
    }
    add_filter( 'woocommerce_email_enabled', 'aireset_disable_all_woocommerce_emails', 10, 2 );

    /**
     * Renomeia o status 'processing' para 'Pedido Pago'
     */
    function aireset_custom_rename_order_statuses( $order_statuses ) {
        if ( isset( $order_statuses['wc-processing'] ) ) {
            $order_statuses['wc-processing'] = _x( 'Pedido Pago', 'Order status', 'aireset-01-default-woo' );
        }
        return $order_statuses;
    }
    add_filter( 'wc_order_statuses', 'aireset_custom_rename_order_statuses' );

/*

// Register custom email notifications
function register_custom_order_status_emails( $email_classes ) {
    // Include the custom email class file
    require_once( 'includes/class-wc-email-order-status.php' );

    // Add custom email classes
    $email_classes['WC_Email_Order_Status_Payed'] = new WC_Email_Order_Status_Payed();
    $email_classes['WC_Email_Order_Status_Partial_Payed'] = new WC_Email_Order_Status_Partial_Payed();
    $email_classes['WC_Email_Order_Status_In_Production'] = new WC_Email_Order_Status_In_Production();
    $email_classes['WC_Email_Order_Status_In_Separation'] = new WC_Email_Order_Status_In_Separation();
    $email_classes['WC_Email_Order_Status_Order_Sended'] = new WC_Email_Order_Status_Order_Sended();
    
    return $email_classes;
}
add_filter( 'woocommerce_email_classes', 'register_custom_order_status_emails' );
*/
/*
// Add custom email settings
function custom_order_status_email_settings( $settings ) {
    $settings[] = array(
        'title'    => 'Pedido Pago',
        'type'     => 'checkbox',
        'desc'     => 'Enable this email notification',
        'id'       => 'woocommerce_order_status_payed_settings',
    );

    $settings[] = array(
        'title'    => 'Pedido Pago Parcialmente',
        'type'     => 'checkbox',
        'desc'     => 'Enable this email notification',
        'id'       => 'woocommerce_order_status_partial_payed_settings',
    );
    
    $settings[] = array(
        'title'    => 'Pedido Em Produção',
        'type'     => 'checkbox',
        'desc'     => 'Enable this email notification',
        'id'       => 'woocommerce_order_status_in_production_settings',
    );
    
    $settings[] = array(
        'title'    => 'Pedido Em Separação',
        'type'     => 'checkbox',
        'desc'     => 'Enable this email notification',
        'id'       => 'woocommerce_order_status_in_separation_settings',
    );
    
    $settings[] = array(
        'title'    => 'Pedido Enviado',
        'type'     => 'checkbox',
        'desc'     => 'Enable this email notification',
        'id'       => 'woocommerce_order_status_order_Sended_settings',
    );
    
    $settings[] = array(
        'type'     => 'sectionend',
        'id'       => 'woocommerce_order_status_payed_settings',
    );
    
    $settings[] = array(
        'type'     => 'sectionend',
        'id'       => 'woocommerce_order_status_partial_payed_settings',
    );
    
    $settings[] = array(
        'type'     => 'sectionend',
        'id'       => 'woocommerce_order_status_in_production_settings',
    );
    
    $settings[] = array(
        'type'     => 'sectionend',
        'id'       => 'woocommerce_order_status_in_separation_settings',
    );
    
    $settings[] = array(
        'type'     => 'sectionend',
        'id'       => 'woocommerce_order_status_order_Sended_settings',
    );
    
    return $settings;
}
add_filter( 'woocommerce_email_settings', 'custom_order_status_email_settings' );

if ( ! class_exists( 'WC_Email_Order_Status_Payed' ) ) {
    class WC_Email_Order_Status_Payed extends WC_Email {
        public function __construct() {
            $this->id          = 'order_status_payed';
            $this->title       = 'Pedido Pago';
            $this->description = 'Este e-mail será enviado quando um pedido for marcado como pago.';
            $this->heading     = 'Atualização de pedido!';
            $this->subject     = 'Seu pedido #{order_number} em {site_title} foi confirmado o pagamento!';

            $this->template_html  = 'emails/customer-order-status-payed.php';
            $this->template_plain = 'emails/plain/customer-order-status-payed.php';

            // Trigger email
            add_action( 'woocommerce_order_status_payed_notification', array( $this, 'trigger' ) );

            parent::__construct();

            // Template location
            $this->template_base = plugin_dir_path( __FILE__ ) . 'templates/';
        }

        public function trigger( $order_id ) {
            if ( $order_id ) {
                $this->object = wc_get_order( $order_id );
                $this->recipient = $this->object->get_billing_email();

                $this->placeholders = array(
                    '{order_date}'   => wc_format_datetime( $this->object->get_date_created() ),
                    '{order_number}' => $this->object->get_order_number(),
                );

                $previous_status = $this->object->get_meta( '_previous_status' );

                if ( in_array( $previous_status, array( 'processing', 'pending' ) ) && $this->is_enabled() && $this->get_recipient() ) {
                    $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                }
            }
        }

        public function get_content_html() {
            return wc_get_template_html( $this->template_html, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $this,
            ), '', $this->template_base );
        }

        public function get_content_plain() {
            return wc_get_template_html( $this->template_plain, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'         => $this,
            ), '', $this->template_base );
        }
    }
}*/

// // Function to conditionally disable WooCommerce email notifications
// function disable_specific_woocommerce_emails( $email_enabled, $email ) {
//     $emails_to_disable = array(
//         'new_order',
//         'cancelled_order',
//         'customer_processing_order',
//         'customer_completed_order',
//         'customer_refunded_order',
//         'customer_invoice',
//         'customer_note',
//         'customer_on_hold_order',
//         'customer_reset_password',
//         'customer_new_account',
//         'customer_partially_refunded_order',
//     );

//     if ( in_array( $email->id, $emails_to_disable ) ) {
//         return false;
//     }

//     return $email_enabled;
// }
// add_filter( 'woocommerce_email_enabled', 'disable_specific_woocommerce_emails', 10, 2 );



