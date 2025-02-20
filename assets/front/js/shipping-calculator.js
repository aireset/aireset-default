jQuery(document).ready(function($) {
    'use strict';
    
    console.log("Script carregado");
    
    // Para cada instância do shipping calculator (container com classe .aireset-shipping-calc)
    $('.aireset-shipping-calc').each(function() {
        var $container = $(this);
        
        // Botão com loading (caso exista dentro do container)
        $container.find('.button-loading').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            var btnWidth = $btn.width();
            var btnHeight = $btn.height();
            
            // Mantém as dimensões originais
            $btn.width(btnWidth);
            $btn.height(btnHeight);
            
            // Adiciona o spinner
            $btn.html('<span class="spinner-border spinner-border-sm"></span>');
            
            setTimeout(function() {
                // Restaura o texto original após 5 segundos
                $btn.html(originalText);
            }, 5000);
        });
        
        // Evento para o botão de cálculo (classe .aireset-shipping-calc-button)
        $container.find('.aireset-shipping-calc-button').on('click', function(e) {
            e.preventDefault();
            
            var $requestButton = $(this);
            var originalText = $requestButton.text();
            var btnWidth = $requestButton.width();
            var btnHeight = $requestButton.height();
            
            // Mantém as dimensões originais do botão
            $requestButton.width(btnWidth);
            $requestButton.height(btnHeight);
            
            // Seleciona o input de CEP dentro do container
            var $postcodeInput = $container.find('.aireset-postcode');
            
            if ($postcodeInput.val().length < 3) {
                $postcodeInput.focus();
                return;
            }
            
            // Limpa a área de resposta dentro do container
            var $response = $container.find('.aireset-response');
            $response.html('');
            
            var detected_variation = detect_product_variation();
            
            if (!detected_variation) {
                $response.fadeOut('fast', function() {
                    $(this).html('<div class="woocommerce-message woocommerce-error">' + aireset_params.without_selected_variation_message + '</div>').fadeIn('fast');
                });
            } else {
                $requestButton.html('<span class="aireset-button-loader"></span>');
                
                $.ajax({
                    type: 'post',
                    url: aireset_params.ajax_url + '?action=aireset_ajax_postcode',
                    data: {
                        product: detected_variation,
                        qty: ($('.quantity input.qty').length ? $('.quantity input.qty').val() : 1),
                        postcode: $postcodeInput.val(),
                        nonce: aireset_params.nonce
                    },
                    success: function(response) {
                        $requestButton.html(originalText);
                        $response.fadeOut('fast', function() {
                            $(this).html(response).fadeIn('fast');
                        });
                    }
                });
            }
        });
        
        // Previne o submit ao pressionar ENTER em form.cart ou no input de CEP dentro do container
        $container.find('form.cart, .aireset-postcode').on('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $container.find('.aireset-shipping-calc-button').click();
                e.preventDefault();
                return false;
            }
        });
        
        // Aplica máscara no input de CEP, gerencia cookies e, se auto_shipping estiver ativado, dispara o cálculo
        var savedCep = getCookie('savedCep');
        var $postcodeInput = $container.find('.aireset-postcode');
        var auto_calculator = aireset_params.auto_shipping;
        
        if (savedCep) {
            setFormattedCep(savedCep, $postcodeInput);
        }
        
        $postcodeInput.on('input', function() {
            var value = $(this).val().replace(/\D/g, '');
            var formattedValue = '';
            
            if (value.length > 5) {
                formattedValue = value.substring(0, 5) + '-' + value.substring(5, 8);
            } else {
                formattedValue = value;
            }
            
            $(this).val(formattedValue);
            setCookie('savedCep', value, 30);
        });
        
        $(window).on('load', function() {
            if ($postcodeInput.val() !== '' && auto_calculator === 'yes') {
                $container.find('.aireset-shipping-calc-button').click();
            }
        });
    });
    
    /**
     * Define o cookie
     */
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }
    
    /**
     * Obtém o cookie
     */
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }
    
    /**
     * Formata o CEP e atualiza o input fornecido
     */
    function setFormattedCep(postcode, $inputElement) {
        var formattedCep = postcode.replace(/^(\d{5})(\d{3})$/, '$1-$2');
        $inputElement.val(formattedCep);
    }
    
    /**
     * Detecta a variação do produto em WooCommerce
     */
    function detect_product_variation() {
        var variationId = jQuery('input[name=variation_id]').val();
        var addToCartValue = jQuery('*[name=add-to-cart]').val();
        
        if (variationId && variationId > 0) {
            return variationId;
        } else if (addToCartValue && addToCartValue > 0) {
            return addToCartValue;
        } else {
            return false;
        }
    }
});