jQuery(document).ready(function ($) {
    // Inclui a máscara de CEP (requer jQuery Mask Plugin)
    const billingCepField = $('#_billing_postcode');
    const shippingCepField = $('#_shipping_postcode');

    // Adiciona um placeholder para guiar o usuário no formato do CEP
    billingCepField.attr('placeholder', '00000-000');
    shippingCepField.attr('placeholder', '00000-000');

    // Aplicando a máscara para os campos de CEP
    billingCepField.mask('00000-000');
    shippingCepField.mask('00000-000');

    // URL da API de CEP - permite ajuste em diferentes ambientes
    const apiBaseUrl = 'https://viacep.com.br/ws/';

    // Função para consultar API e preencher campos de endereço
    function consultarCep(cep, tipo) {
        $.ajax({
            url: `${apiBaseUrl}${cep}/json/`,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (!data.erro) {
                    if (tipo === 'billing') {
                        $('#_billing_address_1').val(data.logradouro);
                        $('#_billing_neighborhood').val(data.bairro);
                        $('#_billing_city').val(data.localidade);
                        $('#_billing_state').val(data.uf);
                        // Foca no campo de número do endereço após preencher os dados do endereço
                        $('#_billing_number').focus();
                    } else if (tipo === 'shipping') {
                        $('#_shipping_address_1').val(data.logradouro);
                        $('#_shipping_neighborhood').val(data.bairro);
                        $('#_shipping_city').val(data.localidade);
                        $('#_shipping_state').val(data.uf);
                        // Foca no campo de número do endereço após preencher os dados do endereço
                        $('#_shipping_number').focus();
                    }
                } else {
                    alert('CEP não encontrado.');
                }
            },
            error: function () {
                alert('Erro ao consultar o CEP.');
            }
        });
    }

    // Monitorar mudanças no campo de CEP de faturamento
    billingCepField.on('blur', function () {
        const cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            consultarCep(cep, 'billing');
        }
    });

    // Monitorar mudanças no campo de CEP de entrega
    shippingCepField.on('blur', function () {
        const cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            consultarCep(cep, 'shipping');
        }
    });
});
