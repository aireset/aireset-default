<?php

    use Aireset\Default\Init;

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    function whatsapp_link_shortcode($atts) {
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
    add_shortcode('whatsapp_link', 'whatsapp_link_shortcode');

    if ( Init::get_setting('aireset_default_disable_add_to_cart_message') === 'yes' ) {
        // Desativar mensagens de "Adicionar ao Carrinho"
        add_filter( 'wc_add_to_cart_message_html', '__return_false' );
        add_filter( 'woocommerce_notice_types', '__return_empty_array' );
        add_filter( 'woocommerce_order_email_verification_required', '__return_false', 9999 );
    } 

    if ( Init::get_setting('aireset_default_order_pay_without_login') === 'yes' ) {

        /**
         * Permitir pagamento de pedidos se o usuário estiver desconectado - WooCommerce
         */
        add_filter( 'user_has_cap', 'aireset_order_pay_without_login', 9999, 3 );

        if ( ! function_exists( 'aireset_order_pay_without_login' ) ) {
            function aireset_order_pay_without_login( $allcaps, $caps, $args ) {
                if ( isset( $caps[0], $_GET['key'] ) ) {
                    if ( $caps[0] === 'pay_for_order' ) {
                        $order_id = isset( $args[2] ) ? $args[2] : null;
                        $order = wc_get_order( $order_id );
                        if ( $order ) {
                            $allcaps['pay_for_order'] = true;
                        }
                    }
                }
                return $allcaps;
            }
        } 
    }

    if ( Init::get_setting('aireset_default_custom_orders_list_column_content') === 'yes' ) {
        // function yasglobal_redundant_hyphens() {
        //   return true;
        // }
        // add_filter( 'custom_permalinks_redundant_hyphens', 'yasglobal_redundant_hyphens' );

        // function yasglobal_permalink_before_saving( $permalink, $post_id ) {
        //   // Check trialing slash in the permalink.
        //   if ( substr( $permalink, -1 ) !== '/' ) {
        //     // If permalink doesn't contain trialing slash then add one.
        //     $permalink .= '/';
        //   }

        //   return $permalink;
        // }
        // add_filter( 'custom_permalink_before_saving', 'yasglobal_permalink_before_saving', 10, 2 );


        if ( ! function_exists( 'aireset_custom_orders_list_column_content' ) ) {
            add_action( 'manage_shop_order_posts_custom_column', 'aireset_custom_orders_list_column_content', 50, 2 );

            function aireset_custom_orders_list_column_content( $column, $post_id ) {
                if ( $column === 'order_number' ) {
                    global $the_order;

                    if ( $phone = $the_order->get_billing_phone() ) {
                        // Escape phone number and other data
                        $escaped_phone = esc_html( $phone );
                        echo '<br>' . esc_html__( 'Celular:', 'aireset-default' ) . ' <a href="tel:' . esc_attr( $escaped_phone ) . '" target="_blank">' . esc_html( $escaped_phone ) . '</a>';

                        $status = wc_get_order_status_name( $the_order->get_status() );
                        // Added translator comments and reordered placeholders
                        $text = urlencode( sprintf( 
                            /* translators: %1$s: Customer's first name, %2$d: Order ID, %3$s: Order status */
                            esc_html__( "Olá %1\$s, tudo bem?\nVimos que você fez o pedido %2\$d que está como %3\$s. Conte para nós como podemos te ajudar?", 'aireset-default' ), 
                            $the_order->get_billing_first_name(), 
                            $the_order->get_id(), 
                            $status 
                        ));

                        echo '<br>' . esc_html__( 'Whatsapp:', 'aireset-default' ) . ' <a href="https://wa.me/55' . esc_attr( preg_replace( "/[^0-9]/", "", $escaped_phone ) ) . '?text=' . esc_attr( $text ) . '" target="_blank">' . esc_html( $escaped_phone ) . '</a>';
                    }

                    if ( $email = $the_order->get_billing_email() ) {
                        // Escape email address
                        $escaped_email = esc_html( $email );
                        echo '<br>' . esc_html__( 'Email:', 'aireset-default' ) . ' <a href="mailto:' . esc_attr( $escaped_email ) . '" target="_blank">' . esc_html( $escaped_email ) . '</a>';
                    }
                }
            }
        }
    }
    
    if ( Init::get_setting('aireset_default_yith_wcas_submit_label') === 'yes' ) {
        if ( ! function_exists( 'aireset_yith_wcas_submit_label' ) ) {
            function aireset_yith_wcas_submit_label( $label ) { 
                return '' . $label; 
            }
        
            add_filter( 'yith_wcas_submit_as_input', '__return_false' );
            add_filter( 'yith_wcas_submit_label', 'aireset_yith_wcas_submit_label' );
        }
    }

    if ( Init::get_setting('aireset_default_images') === 'yes' ) {
        remove_image_size( 'thumbnail' );
        remove_image_size( 'medium' );
        remove_image_size( 'medium_large' );
        remove_image_size( 'large' );
        remove_image_size( '1536x1536' );
        remove_image_size( '2048x2048' );
        remove_image_size( '2560×2560' );

        add_image_size( 'banner size', 1920, 600 );
        add_image_size( 'banner mobile size', 1080, 1080 );
        add_image_size( 'produto size', 500, 500 );
        add_image_size( 'produto size table', 600, 600 );
        add_image_size( 'produto size 2', 1080, 1080 );
        add_image_size( 'micro banners size', 430, 260 );
    }

    if ( Init::get_setting('aireset_default_auto_create_or_assign_customer_to_order') === 'yes' ) {
        add_action('woocommerce_process_shop_order_meta', 'auto_create_or_assign_customer_to_order', 10, 1);

        function auto_create_or_assign_customer_to_order($order_id) {
            // Verifica se a opção está ativada
            if (get_option('aireset_woo_auto_create_customer') != 1) {
                return; // Se a criação automática de clientes estiver desativada, não faz nada.
            }

            // Obtém o pedido
            $order = wc_get_order($order_id);

            // Verifica se o pedido já tem um cliente associado
            if ($order->get_customer_id() > 0) {
                return; // Cliente já está vinculado, nada a fazer.
            }

            // Obtém o e-mail de faturamento do pedido
            $billing_email = $order->get_billing_email();
            
            if (!$billing_email) {
                return; // Se não houver e-mail de faturamento, interrompe.
            }

            // Verifica se o cliente com este e-mail já existe
            $user = get_user_by('email', $billing_email);

            if ($user) {
                // Se o cliente existe, associa o cliente ao pedido
                $order->set_customer_id($user->ID);
            } else {
                // Se o cliente não existe, cria um novo cliente
                $username = sanitize_user(current(explode('@', $billing_email)));
                $password = wp_generate_password();
                $user_id = wc_create_new_customer($billing_email, $username, $password);

                // Verifica se a criação do cliente foi bem-sucedida
                if (!is_wp_error($user_id)) {
                    // Associa o novo cliente ao pedido
                    $order->set_customer_id($user_id);
                } else {
                    error_log('Erro ao criar cliente automaticamente: ' . $user_id->get_error_message());
                    return;
                }
            }

            // Salva as alterações do pedido
            $order->save();
        }
    }

    if ( Init::get_setting('aireset_default_status_woocommerce') === 'yes' ) {
        if ( ! function_exists( 'aireset_add_custom_order_status' ) ) {
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
                    'label'                     => _x( 'Pedido Pago Parcialmente', 'Order status', 'aireset-default' ),
                    'public'                    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_admin_all_list'    => true,
                    'exclude_from_search'       => false,
                    /* translators: %s: Number of partial paid orders */
                    'label_count'               => _n_noop(
                        'Pedidos Pagos Parcialmente <span class="count">(%s)</span>', 
                        'Pedidos Pago Parcialmente <span class="count">(%s)</span>', 
                        'aireset-default'
                    )
                ]);
    
                // Em Produção
                register_post_status( 'wc-in-production', [
                    'label'                     => _x( 'Em Produção', 'Order status', 'aireset-default' ),
                    'public'                    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_admin_all_list'    => true,
                    'exclude_from_search'       => false,
                    /* translators: %s: Number of orders in production */
                    'label_count'               => _n_noop( 
                        'Em Produção <span class="count">(%s)</span>', 
                        'Em Produção <span class="count">(%s)</span>', 
                        'aireset-default'
                    )
                ]);
    
                // Em Separação
                register_post_status( 'wc-in-separation', [
                    'label'                     => _x( 'Em Separação', 'Order status', 'aireset-default' ),
                    'public'                    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_admin_all_list'    => true,
                    'exclude_from_search'       => false,
                    /* translators: %s: Number of orders in separation */
                    'label_count'               => _n_noop( 
                        'Em Separação <span class="count">(%s)</span>', 
                        'Em Separação <span class="count">(%s)</span>', 
                        'aireset-default'
                    )
                ]);
    
                // Pedido Enviado
                register_post_status( 'wc-order-sended', [
                    'label'                     => _x( 'Pedido Enviado', 'Order status', 'aireset-default' ),
                    'public'                    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_admin_all_list'    => true,
                    'exclude_from_search'       => false,
                    /* translators: %s: Number of sent orders */
                    'label_count'               => _n_noop( 
                        'Pedidos Enviados <span class="count">(%s)</span>', 
                        'Pedidos Enviados <span class="count">(%s)</span>', 
                        'aireset-default'
                    )
                ]);
            }
            add_action( 'init', 'aireset_add_custom_order_status' );
        }
    
        if ( ! function_exists( 'aireset_edit_default_order_status_titles' ) ) {
            /**
             * Edita títulos dos status padrão de pedidos
             */
            function aireset_edit_default_order_status_titles( $order_statuses ) {
                if ( isset( $order_statuses['wc-on-hold'] ) ) {
                    $order_statuses['wc-on-hold'] = _x( 'Aguardando Confirmação', 'Order status', 'aireset-default' );
                }
                if ( isset( $order_statuses['wc-pending'] ) ) {
                    $order_statuses['wc-pending'] = _x( 'Pagamento Pendente', 'Order status', 'aireset-default' );
                }
                if ( isset( $order_statuses['wc-processing'] ) ) {
                    $order_statuses['wc-processing'] = _x( 'Pedido Pago', 'Order status', 'aireset-default' );
                }
                if ( isset( $order_statuses['wc-cancelled'] ) ) {
                    $order_statuses['wc-cancelled'] = _x( 'Cancelado', 'Order status', 'aireset-default' );
                }
                if ( isset( $order_statuses['wc-order-completed'] ) ) {
                    $order_statuses['wc-order-completed'] = _x( 'Concluído', 'Order status', 'aireset-default' );
                }
                if ( isset( $order_statuses['wc-order-refunded'] ) ) {
                    $order_statuses['wc-order-refunded'] = _x( 'Reembolsado', 'Order status', 'aireset-default' );
                }
                if ( isset( $order_statuses['wc-order-failed'] ) ) {
                    $order_statuses['wc-order-failed'] = _x( 'Malsucedido', 'Order status', 'aireset-default' );
                }
    
                $new_order_statuses = array();
                foreach ( $order_statuses as $key => $status ) {
                    $new_order_statuses[ $key ] = $status;
                    if ( 'wc-processing' === $key ) {
                        $new_order_statuses['wc-partial-payed'] = _x( 'Pedido Pago Parcialmente', 'Order status', 'aireset-default' );
                        $new_order_statuses['wc-in-production'] = _x( 'Em Produção', 'Order status', 'aireset-default' );
                        $new_order_statuses['wc-in-separation'] = _x( 'Em Separação', 'Order status', 'aireset-default' );
                        $new_order_statuses['wc-order-sended'] = _x( 'Pedido Enviado', 'Order status', 'aireset-default' );
                    }
                }
                return $new_order_statuses;
            }
            add_filter( 'wc_order_statuses', 'aireset_edit_default_order_status_titles' );
        }
    
        if ( ! function_exists( 'aireset_custom_styles' ) ) {
            function aireset_custom_styles() {
                // Enqueue estilo personalizado
                wp_enqueue_style( 'aireset-styles', AIRESET_DEFAULT_ASSETS . 'front/css/styles.css' );
            }
            // Adiciona o hook para carregar o estilo na área administrativa
            add_action('wp_enqueue_scripts', 'aireset_custom_styles');
        }
    
        if ( ! function_exists( 'aireset_custom_order_status_color' ) ) {
            /**
             * Adiciona cores personalizadas para os status de pedidos
             */
            function aireset_custom_order_status_color() {
                // Enqueue estilo personalizado
                wp_enqueue_style( 'aireset-order-status-styles', AIRESET_DEFAULT_ASSETS . 'front/css/order-status-styles.css' );
            }
            // Adiciona o hook para carregar o estilo na área administrativa
            add_action('admin_enqueue_scripts', 'aireset_custom_order_status_color');
        }
    
    
        if ( ! function_exists( 'aireset_add_custom_order_status_bulk_actions' ) ) {
            /**
             * Adiciona ações em massa personalizadas para os pedidos
             */
            function aireset_add_custom_order_status_bulk_actions($bulk_actions) {
                $bulk_actions['mark_on-hold'] = _x( 'Marcar como aguardando', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_pending'] = _x( 'Marcar como pendente', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_processing'] = _x( 'Marcar como processando', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_cancelled'] = _x( 'Marcar como cancelado', 'Bulk action', 'aireset-default' );	
                $bulk_actions['mark_partial_payed'] = _x( 'Marcar como pago parcialmente', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_in-production'] = _x( 'Marcar como em produção', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_in-separation'] = _x( 'Marcar como em separação', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_order-sended'] = _x( 'Marcar como pedido enviado', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_order-completed'] = _x( 'Marcar como concluído', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_order-refunded'] = _x( 'Marcar como reembolsado', 'Bulk action', 'aireset-default' );
                $bulk_actions['mark_order-failed'] = _x( 'Marcar como falho', 'Bulk action', 'aireset-default' );
                
                return $bulk_actions;
            }
            add_filter('bulk_actions-edit-shop_order', 'aireset_add_custom_order_status_bulk_actions');
        }
    
        if ( ! function_exists( 'aireset_include_custom_order_status_in_reports' ) ) {
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
        }
    
        if ( ! function_exists( 'aireset_include_custom_order_status_in_reports' ) ) {
            /**
             * Desabilita notificações de email do WooCommerce
             */
            function aireset_disable_all_woocommerce_emails( $email_enabled, $email ) {
                return false;
            }
            add_filter( 'woocommerce_email_enabled', 'aireset_disable_all_woocommerce_emails', 10, 2 );
        }
    
        if ( ! function_exists( 'aireset_custom_rename_order_statuses' ) ) {
            /**
             * Renomeia o status 'processing' para 'Pedido Pago'
             */
            function aireset_custom_rename_order_statuses( $order_statuses ) {
                if ( isset( $order_statuses['wc-processing'] ) ) {
                    $order_statuses['wc-processing'] = _x( 'Pedido Pago', 'Order status', 'aireset-default' );
                }
                return $order_statuses;
            }
            add_filter( 'wc_order_statuses', 'aireset_custom_rename_order_statuses' );
        }
    
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
    }

        

    /**
     * Load JavaScript for our administration
     */
    add_action('admin_enqueue_scripts', function() {
        global $pagenow;

        var_dump($pagenow);

        if(is_admin()
            && in_array($pagenow, ['post.php', 'post-new.php'])
            && ($_GET['post_type'] ?: 'shop_order') === 'shop_order')
        {
            wp_enqueue_script('shipping-calc_js', plugins_url('admin/js/shipping-calc.js', __FILE__));
            wp_localize_script( 'shipping-calc_js', 'shipping_calc', array(
                    'url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('admin_shipping_calculate')
                )
            );
        }
    });

    /**
     * REST API
     */
    add_action('wp_ajax_admin_shipping_calculate', function () {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'admin_shipping_calculate')) {
            wp_send_json_error();
        }

        $products = isset($_POST['products']) ? (array) $_POST['products'] : array();
        $orderItems = array_map(function($productId) {
            return new WC_Order_Item_Product((int) $productId);
        }, $products);

        $cart = WC()->cart;
        $cart->set_cart_contents(array());
        foreach($orderItems as $orderItem)
        {
            $cart->add_to_cart($orderItem->get_product_id(), $orderItem->get_quantity());
        }

        $package = [
            'destination' => [
                'country' => isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '',
                'state' => isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '',
                'postcode' => isset($_POST['postcode']) ? sanitize_text_field($_POST['postcode']) : ''
            ],
            'contents' => array_map(function($orderItem) {
                return [
                    'quantity' => (int) $orderItem->get_quantity(),
                    'data' => $orderItem->get_product(),
                    'line_total' => $orderItem->get_total(),
                    'line_tax' => $orderItem->get_total_tax(),
                    'line_subtotal' => $orderItem->get_subtotal(),
                    'line_subtotal_tax' => $orderItem->get_subtotal_tax()
                ];
            }, $orderItems),
            'contents_cost' => array_sum(array_map(function (WC_Order_Item_Product $orderItem) {
                return $orderItem->get_total();
            }, $orderItems))
        ];

        $shippingZone = WC_Shipping_Zones::get_zone_matching_package($package);
        /** @var WC_Shipping_Method[] $shippingMethods */
        $shippingMethods = $shippingZone->get_shipping_methods(true);

        $prices = array();
        foreach($shippingMethods as $shippingMethod)
        {
            /** @var WC_Shipping_Rate[] $rates */
            $rates = $shippingMethod->get_rates_for_package($package);
            foreach($rates as $rate)
            {
                $prices[] = [
                    'id' => wp_kses($rate->get_id(), array()),
                    'method' => wp_kses($rate->get_method_id(), array()),
                    'total' => (float) $rate->get_cost(),
                    'tax' => (float) (is_array($rate->get_shipping_tax()) ? array_sum($rate->get_shipping_tax())
                        : $rate->get_shipping_tax())
                ];
            }
        }

        wp_send_json_success([
            'shipping' => $prices
        ]);
    });