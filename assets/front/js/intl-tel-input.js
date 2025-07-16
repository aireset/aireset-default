/** * Máscaras de Telefone com Bandeira (Versão Corrigida e Melhorada)
 * * Este script carrega as dependências da biblioteca intl-tel-input e a inicializa
 * nos campos de formulário que contêm a classe 'mascara_telefone_com_bandeira'.
 */
(function($) {

    // --- 1. Carregamento Seguro das Dependências ---

    // Função para carregar um script e retornar uma Promise
    function loadScript(src) {
        return new Promise(function(resolve, reject) {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Função para carregar um arquivo CSS
    function loadStyle(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }

    // Carrega o CSS primeiro
    loadStyle("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css");

    // Carrega os scripts em sequência e só depois executa o código principal
    loadScript("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js")
        .then(function() {
            // O script principal foi carregado, agora podemos executar nossa lógica
            initializePhoneFields();
        })
        .catch(function() {
            console.error("Erro ao carregar o script intl-tel-input.");
        });


    // --- 2. Lógica de Inicialização do Plugin ---

    function initializePhoneFields() {
        // Seleciona todos os campos de telefone com a classe específica
        const phoneFields = $('[class*="mascara_telefone_com_bandeira"]');
        
        // Se não encontrar nenhum campo, não faz nada
        if (!phoneFields.length) {
            return;
        }

        // Itera sobre cada campo encontrado para inicializar a biblioteca
        phoneFields.each(function() {
            const input = this;
            const $form = $(input).closest('form');

            // Previne inicialização dupla caso o script rode mais de uma vez
            if ($(input).data('iti-initialized')) {
                return;
            }

            const iti = window.intlTelInput(input, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                separateDialCode: true,
                preferredCountries: ["br"],
                nationalMode: true,
            });
            
            // Guarda a instância no próprio elemento para referência futura
            $(input).data('iti-instance', iti);
            $(input).data('iti-initialized', true);

            // Validação no evento de SUBMIT do formulário (mais robusto que o 'click')
            if ($form.length && !$form.data('submit-handler-attached')) {
                $form.on('submit', function(e) {
                    let isFormValid = true;
                    
                    // Valida todos os campos de telefone dentro deste formulário específico
                    $form.find('[class*="mascara_telefone_com_bandeira"]').each(function() {
                        const fieldInput = this;
                        const fieldIti = $(fieldInput).data('iti-instance');

                        if (fieldIti) {
                            // Usa o validador da própria biblioteca (muito mais seguro)
                            if (fieldInput.value.trim() && !fieldIti.isValidNumber()) {
                                alert("Por favor, insira um número de telefone válido para o país selecionado.");
                                $(fieldInput).css('border-color', 'red'); // Destaque visual
                                isFormValid = false;
                                e.preventDefault(); // Impede o envio do formulário
                                return false; // Para o loop .each
                            } else {
                                // Se válido, formata para o padrão internacional E.164
                                fieldInput.value = fieldIti.getNumber();
                                $(fieldInput).css('border-color', ''); // Remove o destaque
                            }
                        }
                    });

                    if (!isFormValid) {
                        return false; // Impede o envio final
                    }
                });
                
                $form.data('submit-handler-attached', true); // Garante que o handler seja adicionado apenas uma vez
            }
        });
    }

})(jQuery);
