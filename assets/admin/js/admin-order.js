jQuery(function($) {
    function initMasks() {
        // Máscara para CEP
        $('input[name="_billing_postcode"], input[name="_shipping_postcode"]').mask('00000-000', {
            clearIfNotMatch: true,
            placeholder: '_____-___'
        });

        // Máscara para cpf                    
        $('.cpf').mask('000.000.000-00', {reverse: true});

        // Máscara para cnpj
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});

        var maskBehavior = function (val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            },
            options = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(maskBehavior.apply({}, arguments), options);
                },
                clearIfNotMatch: true,
                placeholder: '(__) ____-____'
        };
        $('input[name="_billing_phone"]').mask(maskBehavior, options);
        $('input[name="_billing_cellphone"]').mask(maskBehavior, options);
    }

    // Inicializa as máscaras
    initMasks();

    // Observa mudanças na DOM para campos dinâmicos
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initMasks();
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });    


    // Função para formatar o CEP
    function formatCEP(cep) {
        return cep.replace(/\D/g, '').replace(/^(\d{5})(\d{3})+?$/, '$1-$2');
    }

    // Função para preencher os campos de endereço
    function fillAddressFields(prefix, data) {
        $('#_' + prefix + '_address_1').val(data.logradouro);
        $('#_' + prefix + '_neighborhood').val(data.bairro);
        $('#_' + prefix + '_city').val(data.cidade);
        $('#_' + prefix + '_state').val(data.uf);
        $("#_" + prefix + "_number").focus();
    }

    // Função para buscar o CEP
    function searchCEP(cep, prefix) {
        cep = cep.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            return;
        }

        const $row = $('#_' + prefix + '_postcode').closest('.address_row');
        if ($row.length) {
            $row.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        }

        $.ajax({
            url: aireset_cep_params.ajax_url,
            method: 'POST',
            data: {
                action: 'aireset_buscar_cep',
                nonce: aireset_cep_params.nonce,
                cep: cep
            },
            success: function(response) {
                if (response.success && response.data) {
                    fillAddressFields(prefix, response.data);
                } else {
                    alert('CEP não encontrado ou inválido.');
                }
            },
            error: function() {
                alert('Erro ao buscar o CEP. Tente novamente.');
            },
            complete: function() {
                if ($row.length) {
                    $row.unblock();
                }
            }
        });
    }

    // Função para adicionar máscara e evento de busca aos campos de CEP
    function initCEPField($field, prefix) {
        // Adiciona máscara ao digitar
        $field.on('input', function() {
            var cep = $(this).val();
            $(this).val(formatCEP(cep));
        });

        // Busca endereço ao completar o CEP
        let typingTimer;
        $field.on('input', function() {
            clearTimeout(typingTimer);
            const cep = $(this).val().replace(/\D/g, '');
            
            if (cep.length === 8) {
                typingTimer = setTimeout(function() {
                    searchCEP(cep, prefix);
                }, 500);
            }
        });

        // Busca também ao perder o foco
        $field.on('blur', function() {
            const cep = $(this).val();
            if (cep.replace(/\D/g, '').length === 8) {
                searchCEP(cep, prefix);
            }
        });
    }

    // Inicializa os campos de CEP
    function initCEPAutoFill() {
        ['billing', 'shipping'].forEach(function(prefix) {
            const $postcodeField = $('#_' + prefix + '_postcode');
            if ($postcodeField.length) {
                initCEPField($postcodeField, prefix);
            }
        });

        // Observa mudanças na DOM para campos dinâmicos
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Elemento
                        ['billing', 'shipping'].forEach(function(prefix) {
                            const $newField = $(node).find('#_' + prefix + '_postcode');
                            if ($newField.length && !$newField.data('cep-initialized')) {
                                initCEPField($newField, prefix);
                                $newField.data('cep-initialized', true);
                            }
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Inicializa quando a página carregar
    initCEPAutoFill();
});
