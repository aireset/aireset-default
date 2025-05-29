<?php

    use Aireset\Default\Init;

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    
    if ( ! function_exists( 'whatsapp_link_shortcode' ) ) {
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
    }

    if ( Init::get_setting('aireset_default_disable_add_to_cart_message') === 'yes' ) {
        // Desativar mensagens de "Adicionar ao Carrinho"
        add_filter( 'wc_add_to_cart_message_html', '__return_false' );
        add_filter( 'woocommerce_notice_types', '__return_empty_array' );
        add_filter( 'woocommerce_order_email_verification_required', '__return_false', 9999 );
    } 
    
    if ( Init::get_setting('aireset_default_fixed_viewport') === 'yes' ) {
        add_action('wp_head', 'aireset_disable_mobile_zoom', 0);
        function aireset_disable_mobile_zoom() {
            echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
        }
    } 
    

    if ( Init::get_setting('aireset_default_order_pay_without_login') === 'yes' ) {
        // Permitir pagamento de pedidos se o usuário estiver desconectado
        add_filter( 'user_has_cap', 'aireset_order_pay_without_login', 9999, 3 );
        if ( ! function_exists( 'aireset_order_pay_without_login' ) ) {
            function aireset_order_pay_without_login( $allcaps, $caps, $args ) {
                if ( isset( $caps[0], $_GET['key'] ) && $caps[0] === 'pay_for_order' ) {
                    $order_id = isset( $args[2] ) ? $args[2] : null;
                    $order = wc_get_order( $order_id );
                    if ( $order ) {
                        $allcaps['pay_for_order'] = true;
                    }
                }
                return $allcaps;
            }
        }

        // Permitir seleção de métodos de pagamento mesmo sem usuário vinculado
        add_filter( 'woocommerce_available_payment_gateways', 'aireset_allow_payment_gateways_without_user', 10, 1 );
        if ( ! function_exists( 'aireset_allow_payment_gateways_without_user' ) ) {
            function aireset_allow_payment_gateways_without_user( $gateways ) {
                if ( ! is_checkout_pay_page() ) {
                    return $gateways;
                }

                // Detecta o pedido pela query string /order-pay/ID/?key=...
                $order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
                if ( ! $order_id && isset( $GLOBALS['wp'] ) && ! empty( $GLOBALS['wp']->query_vars['order-pay'] ) ) {
                    $order_id = absint( $GLOBALS['wp']->query_vars['order-pay'] );
                }

                if ( ! $order_id ) {
                    return $gateways;
                }

                $order = wc_get_order( $order_id );
                if ( ! $order ) {
                    return $gateways;
                }

                // Se o pedido estiver sem usuário, forçamos os gateways a aparecer
                if ( $order->get_user_id() == 0 ) {
                    foreach ( $gateways as $gateway ) {
                        $gateway->enabled = 'yes';
                    }
                }

                return $gateways;
            }
        }

        // Permitir checkout sem cliente vinculado (impede bloqueio por ausência de user_id)
        add_filter( 'woocommerce_checkout_customer_id', 'aireset_checkout_allow_no_customer', 10, 2 );
        if ( ! function_exists( 'aireset_checkout_allow_no_customer' ) ) {
            function aireset_checkout_allow_no_customer( $customer_id, $checkout = null ) {
                // Se não houver usuário logado, retorna 0 (sem cliente)
                if ( ! is_user_logged_in() ) {
                    return 0;
                }
                return $customer_id;
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

        if ( ! function_exists( 'auto_create_or_assign_customer_to_order' ) ) {
            function auto_create_or_assign_customer_to_order($order_id) {
                // Verifica se a opção está ativada
                if (get_option('aireset_woo_auto_create_customer') != 1) {
                    return;
                }

                $order = wc_get_order($order_id);
                if ( ! $order ) {
                    return;
                }

                $billing_email = $order->get_billing_email();
                if (!$billing_email) {
                    return;
                }

                $user = get_user_by('email', $billing_email);

                if ($user) {
                    $user_id = $user->ID;
                    $order->set_customer_id($user_id);
                } else {
                    $username = sanitize_user(current(explode('@', $billing_email)));
                    $password = wp_generate_password();
                    $user_id = wc_create_new_customer($billing_email, $username, $password);

                    if (is_wp_error($user_id)) {
                        error_log('Erro ao criar cliente automaticamente: ' . $user_id->get_error_message());
                        return;
                    }
                    $order->set_customer_id($user_id);
                }

                // Atualiza SEMPRE os dados do usuário com base no pedido
                update_user_meta($user_id, 'first_name', $order->get_billing_first_name());
                update_user_meta($user_id, 'last_name', $order->get_billing_last_name());
                update_user_meta($user_id, 'billing_phone', $order->get_billing_phone());
                update_user_meta($user_id, 'billing_address_1', $order->get_billing_address_1());
                update_user_meta($user_id, 'billing_address_2', $order->get_billing_address_2());
                update_user_meta($user_id, 'billing_city', $order->get_billing_city());
                update_user_meta($user_id, 'billing_postcode', $order->get_billing_postcode());
                update_user_meta($user_id, 'billing_state', $order->get_billing_state());
                update_user_meta($user_id, 'billing_country', $order->get_billing_country());
                update_user_meta($user_id, 'billing_company', $order->get_billing_company());
                update_user_meta($user_id, 'shipping_first_name', $order->get_shipping_first_name());
                update_user_meta($user_id, 'shipping_last_name', $order->get_shipping_last_name());
                update_user_meta($user_id, 'shipping_address_1', $order->get_shipping_address_1());
                update_user_meta($user_id, 'shipping_address_2', $order->get_shipping_address_2());
                update_user_meta($user_id, 'shipping_city', $order->get_shipping_city());
                update_user_meta($user_id, 'shipping_postcode', $order->get_shipping_postcode());
                update_user_meta($user_id, 'shipping_state', $order->get_shipping_state());
                update_user_meta($user_id, 'shipping_country', $order->get_shipping_country());
                update_user_meta($user_id, 'shipping_company', $order->get_shipping_company());

                if ( is_plugin_active('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php') ) {
                    update_user_meta($user_id, 'billing_persontype', $order->get_meta('_billing_persontype'));
                    update_user_meta($user_id, 'billing_cpf', $order->get_meta('_billing_cpf'));
                    update_user_meta($user_id, 'billing_cnpj', $order->get_meta('_billing_cnpj'));
                    update_user_meta($user_id, 'billing_ie', $order->get_meta('_billing_ie'));
                    update_user_meta($user_id, 'billing_birthdate', $order->get_meta('_billing_birthdate'));
                    update_user_meta($user_id, 'billing_sex', $order->get_meta('_billing_sex'));
                    update_user_meta($user_id, 'billing_number', $order->get_meta('_billing_number'));
                    update_user_meta($user_id, 'billing_neighborhood', $order->get_meta('_billing_neighborhood'));
                    update_user_meta($user_id, 'shipping_number', $order->get_meta('_shipping_number'));
                    update_user_meta($user_id, 'shipping_neighborhood', $order->get_meta('_shipping_neighborhood'));
                }

                $order->save();
            }
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

        // var_dump($pagenow);

        if(is_admin()
            && in_array($pagenow, ['post.php', 'post-new.php'])
            && ($_GET['post_type'] ?: 'shop_order') === 'shop_order')
        {
            wp_enqueue_script('shipping-calc_js', plugins_url('admin/js/shipping-calc.js', __FILE__), array('jquery'), AIRESET_DEFAULT_VERSION);
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

    
    if ( ! function_exists( 'custom_adjust_shipping_rates' ) ) {
        add_filter( 'woocommerce_package_rates', 'custom_adjust_shipping_rates', 10, 2 );
        function custom_adjust_shipping_rates( $rates, $package ) {
            // Define a data inicial (hoje)
            $start_date = new DateTime();
            // Recupera os métodos ativos da zona de envio
            $zone = WC_Shipping_Zones::get_zone_matching_package( $package );
            $active_methods = [];
            foreach ( $zone->get_shipping_methods() as $method ) {
                if ( 'yes' === $method->enabled ) {
                    $active_methods[] = $method;
                }
            }
            $active_method_ids = wp_list_pluck( $active_methods, 'id' );

            // dump($rates);

            // Itera pelos rates para ajustar o prazo de entrega
            foreach ( $rates as $rate_id => $rate ) {
                // Se o rate não pertencer a um método ativo, ignora-o
                if ( ! in_array( $rate->method_id, $active_method_ids ) ) {
                    continue;
                }

                // Tenta recuperar a previsão de entrega a partir do meta 'delivery_time'
                $meta_data = $rate->get_meta_data();
                $delivery_time_text = '';
                foreach ( $meta_data as $key => $meta ) {
                    if ( $key === 'delivery_time' ) {
                        $delivery_time_text = $meta;
                        break;
                    }
                }

                if ( ! empty( $delivery_time_text ) ) {
                    // Exemplo: "(4 a 5 dias úteis)"
                    // Remove parênteses e as palavras "dias úteis", "dias corridos" ou "dias"
                    $clean = trim( str_replace( ['dias úteis', 'dias corridos', 'dias'], '', $delivery_time_text ), '() ' );
                    
                    // Captura os dois números (mínimo e máximo)
                    if ( preg_match('/(\d+)\s*a\s*(\d+)/', $clean, $matches) ) {
                        $base_min = intval( $matches[1] );
                        $base_max = intval( $matches[2] );
                        
                        // Função auxiliar para contar finais de semana e feriados entre hoje e uma data final
                        $count_additional_days = function( $base_days ) use ( $start_date ) {
                            $end_date = clone $start_date;
                            $end_date->modify("+{$base_days} days");
                            $additional = 0;
                            // Contagem de finais de semana
                            $interval = new DateInterval('P1D');
                            $period = new DatePeriod( $start_date, $interval, $end_date );
                            foreach ( $period as $dt ) {
                                if ( in_array( $dt->format('N'), [6,7] ) ) {
                                    $additional++;
                                }
                            }
                            // Contagem de feriados
                            $year = $start_date->format('Y');
                            $transient_key = 'custom_holidays_' . $year;
                            $holidays = get_transient( $transient_key );
                            if ( false === $holidays ) {
                                $response = wp_remote_get( "https://brasilapi.com.br/api/feriados/v1/{$year}" );
                                if ( ! is_wp_error( $response ) ) {
                                    $holidays = json_decode( wp_remote_retrieve_body( $response ), true );
                                } else {
                                    $holidays = [];
                                }
                                set_transient( $transient_key, $holidays, DAY_IN_SECONDS );
                            }
                            foreach ( $holidays as $holiday ) {
                                if ( isset( $holiday['date'] ) ) {
                                    $holiday_date = DateTime::createFromFormat('Y-m-d', $holiday['date']);
                                    if ( $holiday_date && $holiday_date >= $start_date && $holiday_date < $end_date ) {
                                        $additional++;
                                    }
                                }
                            }
                            return $additional;
                        };
                
                        $additional_min = $count_additional_days( $base_min );
                        $additional_max = $count_additional_days( $base_max );
                
                        $adjusted_min = $base_min + $additional_min;
                        $adjusted_max = $base_max + $additional_max;
                
                        // Atualiza o label removendo o texto entre parênteses original
                        $original_label = $rates[$rate_id]->get_label();
                        $original_label = preg_replace('/\s*\(.*?\)$/', '', $original_label);
                        $new_label = sprintf( __( 'Entrega em aproximadamente de %s a %s dias', 'aireset-default' ), $adjusted_min, $adjusted_max );
                        
                        $rates[$rate_id]->set_label( $original_label . ' (' . $new_label . ')' );
                        // $rates[$rate_id]->delivery_time = $new_label;
                        
                        // Atualiza a meta "delivery_time" para o novo formato
                        $rate->add_meta_data( 'delivery_time', sprintf( '%s a %s dias', $adjusted_min, $adjusted_max ) );

                
                        // Opcional: atualizar o meta "delivery_time" para o novo formato
                        // foreach ( $rate->get_meta_data() as $key => $meta_item ) {
                        //     if ( isset( $key ) && $key === 'delivery_time' ) {
                        //         // $rates[$rate_id]->update_meta_data( 'delivery_time', sprintf( '%s a %s dias', $adjusted_min, $adjusted_max ) );
                        //         dump($rates[$rate_id]->ste_meta_data['delivery_time']);
                        //         $rates[$rate_id]->add_meta_data( 'delivery_time', sprintf( '%s a %s dias', $adjusted_min, $adjusted_max ) );
                        //         // $rates[$rate_id]->meta_data['delivery_time'] = sprintf( '%s a %s dias', $adjusted_min, $adjusted_max );
                        //         dump($rates[$rate_id]->meta_data['delivery_time']);
                        //         break;
                        //     }
                        // }
                        // Atualiza a meta "delivery_time" para o novo formato
                        // $rate->update_meta_data( 'delivery_time', sprintf( '%s a %s dias', $adjusted_min, $adjusted_max ) );
                    }
                }
                // dump($rates);
                // die;
            }
            return $rates;
        }
    }
    // add_action('elementor/dynamic_tags/register_tags', function($dynamic_tags) {
    //     require_once AIRESET_DEFAULT_INC_PATH . 'elementor-dynamic-tags.php';

    //     $dynamic_tags->register_tag('Aireset\Default\Elementor\Aireset_Dynamic_Text_Tag');
    //     $dynamic_tags->register_tag('Aireset\Default\Elementor\Aireset_Dynamic_URL_Tag');
    // });

    if ( ! function_exists( 'aireset_custom_styles' ) ) {
        function aireset_custom_styles() {
            if(!is_admin()){
                // Enqueue estilo personalizado
                wp_enqueue_style( 'aireset-styles', AIRESET_DEFAULT_ASSETS . 'front/css/styles.css', array(), AIRESET_DEFAULT_VERSION);
                wp_enqueue_script( 'aireset-scripts', AIRESET_DEFAULT_ASSETS . 'front/js/scripts.js', array('jquery'), AIRESET_DEFAULT_VERSION);
            }
        }
        // Adiciona o hook para carregar o estilo na área administrativa
        add_action('wp_enqueue_scripts', 'aireset_custom_styles');
    }
    
    if ( Init::get_setting('aireset_default_intl_tel_input') === 'yes' ) {
        if ( ! function_exists( 'aireset_custom_styles' ) ) {
            function aireset_custom_styles() {
                if(!is_admin()){
                    // Enqueue estilo personalizado
                    wp_enqueue_script( 'aireset-scripts', AIRESET_DEFAULT_ASSETS . 'front/js/intl-tel-input.js', array('jquery'), AIRESET_DEFAULT_VERSION);
                }
            }
            // Adiciona o hook para carregar o estilo na área administrativa
            add_action('wp_enqueue_scripts', 'aireset_custom_styles');
        }
    }
    
    // Adicionar o botão "Criar Cliente" na página de edição do pedido no admin
    if ( ! function_exists( 'add_create_customer_button' ) ) {
        // Adicionar botão na página de edição do pedido no admin
        add_action( 'woocommerce_order_actions', 'add_create_customer_button' );

        function add_create_customer_button( $actions ) {
            global $theorder;

            // Verificar se o pedido foi feito por um visitante (sem ID de usuário)
            if ( $theorder->get_user_id() == 0 ) {
                // Adicionar a ação "Criar Cliente" se for um visitante
                $actions['create_customer'] = __( 'Cria/Atualizar Cliente e Vincular', 'woocommerce' );
            }

            return $actions;
        }
    }

    // Adicionar o processamento da ação ao clicar no botão "Criar Cliente"
    if ( ! function_exists( 'process_create_customer_from_order' ) ) {
        // Processar a ação ao clicar no botão
        add_action( 'woocommerce_order_action_create_customer', 'process_create_customer_from_order' );
        function process_create_customer_from_order( $order ) {
            $billing_email      = $order->get_billing_email();
            $billing_first_name = $order->get_billing_first_name();
            $billing_last_name  = $order->get_billing_last_name();

            if ( ! email_exists( $billing_email ) ) {
                $username = sanitize_user( current( explode( '@', $billing_email ) ), true );
                $password = wp_generate_password();
                $user_id  = wp_create_user( $username, $password, $billing_email );

                if ( ! is_wp_error( $user_id ) ) {
                    // atualiza dados e seta role
                    wp_update_user([
                        'ID'         => $user_id,
                        'first_name' => $billing_first_name,
                        'last_name'  => $billing_last_name,
                    ]);
                    ( new WP_User( $user_id ) )->set_role( 'customer' );
                } else {
                    $error = $user_id->get_error_message();
                    WC_Admin_Meta_Boxes::add_error( __( 'Erro ao criar o cliente: ', 'woocommerce' ) . $error );
                    $GLOBALS['my_create_customer_notice'] = [
                        'type' => 'error',
                        'msg'  => sprintf( __( 'Erro ao criar o cliente: %s', 'woocommerce' ), $error ),
                    ];
                    do_action( 'my_create_customer_notice' );
                }
            } else {
                // cliente já existe
                $user = get_user_by( 'email', $billing_email );
                $user_id = $user->ID;
            }

            // Atualiza todos os metadados do usuário com base no pedido
            update_user_meta($user_id, 'first_name', $order->get_billing_first_name());
            update_user_meta($user_id, 'last_name', $order->get_billing_last_name());
            update_user_meta($user_id, 'billing_phone', $order->get_billing_phone());
            update_user_meta($user_id, 'billing_address_1', $order->get_billing_address_1());
            update_user_meta($user_id, 'billing_address_2', $order->get_billing_address_2());
            update_user_meta($user_id, 'billing_city', $order->get_billing_city());
            update_user_meta($user_id, 'billing_postcode', $order->get_billing_postcode());
            update_user_meta($user_id, 'billing_state', $order->get_billing_state());
            update_user_meta($user_id, 'billing_country', $order->get_billing_country());
            update_user_meta($user_id, 'billing_company', $order->get_billing_company());
            update_user_meta($user_id, 'shipping_first_name', $order->get_shipping_first_name());
            update_user_meta($user_id, 'shipping_last_name', $order->get_shipping_last_name());
            update_user_meta($user_id, 'shipping_address_1', $order->get_shipping_address_1());
            update_user_meta($user_id, 'shipping_address_2', $order->get_shipping_address_2());
            update_user_meta($user_id, 'shipping_city', $order->get_shipping_city());
            update_user_meta($user_id, 'shipping_postcode', $order->get_shipping_postcode());
            update_user_meta($user_id, 'shipping_state', $order->get_shipping_state());
            update_user_meta($user_id, 'shipping_country', $order->get_shipping_country());
            update_user_meta($user_id, 'shipping_company', $order->get_shipping_company());

            // Campos extras do plugin brasileiro
            if ( function_exists('is_plugin_active') && is_plugin_active('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php') ) {
                update_user_meta($user_id, 'billing_persontype', $order->get_meta('_billing_persontype'));
                update_user_meta($user_id, 'billing_cpf', $order->get_meta('_billing_cpf'));
                update_user_meta($user_id, 'billing_cnpj', $order->get_meta('_billing_cnpj'));
                update_user_meta($user_id, 'billing_ie', $order->get_meta('_billing_ie'));
                update_user_meta($user_id, 'billing_birthdate', $order->get_meta('_billing_birthdate'));
                update_user_meta($user_id, 'billing_sex', $order->get_meta('_billing_sex'));
                update_user_meta($user_id, 'billing_number', $order->get_meta('_billing_number'));
                update_user_meta($user_id, 'billing_neighborhood', $order->get_meta('_billing_neighborhood'));
                update_user_meta($user_id, 'shipping_number', $order->get_meta('_shipping_number'));
                update_user_meta($user_id, 'shipping_neighborhood', $order->get_meta('_shipping_neighborhood'));
            }

            // vincula ao pedido
            $order->set_customer_id( $user_id );
            $order->save();

            // Mensagem de sucesso
            WC_Admin_Meta_Boxes::add_message( __( 'Cliente criado/atualizado e vinculado ao pedido com sucesso!', 'woocommerce' ) );
            $GLOBALS['my_create_customer_notice'] = [
                'type' => 'success',
                'msg'  => __( 'Cliente criado/atualizado e vinculado ao pedido com sucesso!', 'woocommerce' ),
            ];
            do_action( 'my_create_customer_notice' );
        }
    }

    if ( ! function_exists( 'aireset_admin_notices' ) ) {
        add_action( 'admin_notices', 'aireset_admin_notices' );
        function aireset_admin_notices() {
            if ( ! did_action( 'aireset_notice' ) ) {
                return;
            }
            // a mensagem ficará disponível em $GLOBALS['aireset_notice']
            $notice = $GLOBALS['aireset_notice'];
            printf(
                '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
                esc_attr( $notice['type'] ), 
                wp_kses_post( $notice['msg'] )
            );
        }
    }

