<?php

function add_custom_order_status() {
    // register_post_status( 'wc-payed', array(
    //     'label'                     => 'Pedido Pago',
    //     'public'                    => true,
    //     'show_in_admin_status_list' => true,
    //     'show_in_admin_all_list'    => true,
    //     'exclude_from_search'       => false,
    //     'label_count'               => _n_noop( 'Pedidos Pagos <span class="count">(%s)</span>', 'Pedidos Pago <span class="count">(%s)</span>' )
    // ) );
    register_post_status( 'wc-partial-payed', array(
        'label'                     => 'Pedido Pago Parcialmente',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Pedidos Pagos Parcialmente <span class="count">(%s)</span>', 'Pedidos Pago Parcialmente <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-in-production', array(
        'label'                     => 'Em Produção',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Em Produção <span class="count">(%s)</span>', 'Em Produção <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-in-separation', array(
        'label'                     => 'Em Separação',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Em Separação <span class="count">(%s)</span>', 'Em Separação <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-order-sended', array(
        'label'                     => 'Pedido Enviado',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop( 'Pedidos Enviados <span class="count">(%s)</span>', 'Pedidos Enviados <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'add_custom_order_status' );

function edit_default_order_status_titles( $order_statuses ) {
    // Editando os status padrões
    if ( isset( $order_statuses['wc-on-hold'] ) ) {
        $order_statuses['wc-on-hold'] = 'Aguardando Confirmação'; // Novo título para 'on-hold'
    }
    if ( isset( $order_statuses['wc-pending'] ) ) {
        $order_statuses['wc-pending'] = 'Pagamento Pendente'; // Novo título para 'pending'
    }
    if ( isset( $order_statuses['wc-processing'] ) ) {
        // $order_statuses['wc-processing'] = 'Processando'; // Novo título para 'pending'
        $order_statuses['wc-processing'] = 'Pedido Pago'; // Novo título para 'pending'
    }
    if ( isset( $order_statuses['wc-cancelled'] ) ) {
        $order_statuses['wc-cancelled'] = 'Cancelado'; // Novo título para 'pending'
    }
    if ( isset( $order_statuses['wc-order-completed'] ) ) {
        $order_statuses['wc-order-completed'] = 'Concluído'; // Novo título para 'pending'
    }
    if ( isset( $order_statuses['wc-order-refunded'] ) ) {
        $order_statuses['wc-order-refunded'] = 'Reembolsado'; // Novo título para 'pending'
    }
    if ( isset( $order_statuses['wc-order-failed'] ) ) {
        $order_statuses['wc-order-failed'] = 'Malsucedido'; // Novo título para 'pending'
    }
	
    $new_order_statuses = array();
    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        if ( 'wc-processing' === $key ) {
            // $new_order_statuses['wc-payed'] = 'Pedido Pago';
            $new_order_statuses['wc-partial-payed'] = 'Pedido Pago Parcialmente';
            $new_order_statuses['wc-in-production'] = 'Em Produção';
            $new_order_statuses['wc-in-separation'] = 'Em Separação';
            $new_order_statuses['wc-order-sended'] = 'Pedido Enviado';
        }
    }
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'edit_default_order_status_titles' );

function custom_order_status_color() {
    ?>
    <style>
        .order-status.status-wc-on-hold { background: #f7a400; }
        .order-status.status-wc-pending { background: #3d3d3d; }
        .order-status.status-wc-processing { background: #cccc00; }
        .order-status.status-wc-cancelled { background: #e50000; }
        /* .order-status.status-wc-payed { background: #44ce00; } */
        .order-status.status-wc-partial-payed { background: #44ce00; }
        .order-status.status-wc-in-production { background: #214dd1; }
        .order-status.status-wc-in-separation { background: #214dd1; }
        .order-status.status-wc-order-sended { background: #00c130; }
        .order-status.status-wc-order-completed { background: #007fe0; }
        .order-status.status-wc-order-refunded { background: #898989; }
        .order-status.status-wc-order-failed { background: #ea4f4f; }		
    </style>
    <?php
}
add_action('admin_head', 'custom_order_status_color');

function add_custom_order_status_bulk_actions($bulk_actions) {
    $bulk_actions['mark_on-hold'] = 'Marcar como aguardando';
    $bulk_actions['mark_pending'] = 'Marcar como pendente';
    $bulk_actions['mark_processing'] = 'Marcar como processando';
    $bulk_actions['mark_cancelled'] = 'Marcar como cancelado';	
    // $bulk_actions['mark_payed'] = 'Marcar como pago';
    $bulk_actions['mark_partial_payed'] = 'Marcar como pago parcialmente';
    $bulk_actions['mark_in-production'] = 'Marcar como em producao';
    $bulk_actions['mark_in-separation'] = 'Marcar como em separação';
    $bulk_actions['mark_order-sended'] = 'Marcar como pedido enviado';
    $bulk_actions['mark_order-completed'] = 'Marcar como concluído';
	$bulk_actions['mark_order-refunded'] = 'Marcar como reembolsado';
	$bulk_actions['mark_order-failed'] = 'Marcar como falho';
    return $bulk_actions;
}
add_filter('bulk_actions-edit-shop_order', 'add_custom_order_status_bulk_actions');
function include_custom_order_status_in_reports( $order_statuses ) {
    // $order_statuses[] = 'wc-payed'; // Adiciona o status personalizado aos relatórios
    $order_statuses[] = 'wc-partial-payed'; // Adiciona o status personalizado aos relatórios
    $order_statuses[] = 'wc-in-production';
    $order_statuses[] = 'wc-in-separation';
    $order_statuses[] = 'wc-order-sended'; // Adiciona o status personalizado aos relatórios
    return $order_statuses;
}
add_filter( 'woocommerce_reports_order_statuses', 'include_custom_order_status_in_reports' );

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


// Function to disable WooCommerce email notifications
function disable_all_woocommerce_emails( $email_enabled, $email ) {
    return false;
}
add_filter( 'woocommerce_email_enabled', 'disable_all_woocommerce_emails', 10, 2 );

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

// Change the text for the 'processing' status to 'Pedido Pago'
function custom_rename_order_statuses( $order_statuses ) {
    if ( isset( $order_statuses['wc-processing'] ) ) {
        $order_statuses['wc-processing'] = _x( 'Pedido Pago', 'Order status', 'woocommerce' );
    }
    return $order_statuses;
}
add_filter( 'wc_order_statuses', 'custom_rename_order_statuses' );

// Add custom styles for the 'payed' status
function custom_order_status_styles() {
    echo '<style>
        .order-status.status-processing {
            background: #39b54a;
            color: white;
        }
    </style>';
}
add_action( 'admin_head', 'custom_order_status_styles' );
