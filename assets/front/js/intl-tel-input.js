/** 
 * Máscaras de Telefone com Bandeira
*/
// Cria um elemento <script>
const script_intlput = document.createElement('script');
// Define os atributos necessários
script_intlput.src = "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js";
script_intlput.crossOrigin = "anonymous";
script_intlput.referrerPolicy = "no-referrer";

// Adiciona o script ao <head> ou ao final do <body>
document.head.appendChild(script_intlput);

// Cria um elemento <script>
const script_intlpututils = document.createElement('script');
// Define os atributos necessários
script_intlpututils.src = "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js";
script_intlpututils.crossOrigin = "anonymous";
script_intlpututils.referrerPolicy = "no-referrer";

// Adiciona o script ao <head> ou ao final do <body>
document.head.appendChild(script_intlpututils);

// Cria um elemento <style>
const style_intlpututils = document.createElement('style');
// Define os atributos necessários
style_intlpututils.src = "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css";
style_intlpututils.crossOrigin = "anonymous";
style_intlpututils.referrerPolicy = "no-referrer";

// Adiciona o script ao <head> ou ao final do <body>
document.head.appendChild(scriptstyle_intlpututils_intlpututils);
jQuery(document).ready(function($) {
    // 1. Seleciona apenas formulários que possuem ao menos um campo de telefone
    $('form').filter(function() {
        // Ajuste o seletor conforme sua necessidade:
        // '[name="telefone"]', '[name="whatsapp"]' ou '[class^="mascara_telefone_com_bandeira"]'
        return $(this).find('[class*="mascara_telefone_com_bandeira"]').length > 0;
    })
        .each(function() {
        // 2. Dentro de cada formulário filtrado, selecionamos os campos de telefone
        let $form = $(this);
        let $phoneFields = $form.find('[class*="mascara_telefone_com_bandeira"]');
        
// 			console.log($phoneFields)

        // Armazena as instâncias do intlTelInput para cada campo
        let itiInstances = [];

        // 3. Inicia o intlTelInput para cada input do formulário
        $phoneFields.each(function(index, input) {
            itiInstances[index] = window.intlTelInput(input, {
                separateDialCode: true,
                preferredCountries: ["br"],
                nationalMode: true, 
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
            });

            // Ajuste dinâmico da largura do dropdown ao mudar o país
            $(input).on('countrychange', function() {
                let larguraInput = input.getBoundingClientRect().width;
                $(input).closest(".iti").find(".iti__country-list").css("width", larguraInput + "px");
            });

            // Formata o valor no modo nacional a cada digitação
            $(input).on("input", function() {
                let numero = itiInstances[index].getNumber(intlTelInputUtils.numberFormat.NATIONAL);
                $(input).val(numero);
            });
        });

        // 4. Captura o clique em qualquer botão ou input [type="submit"] do formulário
        $form.on('click', '[type="submit"]', function(e) {
            let invalidNumber = false;

            // Verificamos cada campo para garantir que tenha o código do país
            $phoneFields.each(function(index, input) {
                let fullNumber = itiInstances[index].getNumber(intlTelInputUtils.numberFormat.E164); 

                // Verifica se inclui o código do país (+)
                if (!fullNumber.includes('+') && $(input).is(':visible')) {
                    invalidNumber = true;
                    input.value = ''; // Limpa o campo
                } else {
                    input.value = fullNumber; // Atribui o número formatado completo
                }
            });

            // Se algum telefone estiver inválido, exibimos o alerta e impedimos o submit
            if (invalidNumber) {
                alert("Por favor, preencha o(s) número(s) de telefone com o código do país.");
                e.preventDefault();
                return false;
            }
        });
    });

});