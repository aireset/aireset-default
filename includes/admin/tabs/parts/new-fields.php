<div class="add-new-checkout-fields-container popup-container">
    <div class="popup-content">
        <div class="popup-header">
            <h5 class="popup-title"><?php echo esc_html__('Adicionar novo campo para finalização de compras', 'aireset-default') ?></h5>
            <button class="add-new-checkout-fields-close btn-close fs-lg" aria-label="<?php esc_html( 'Fechar', 'aireset-default' ); ?>"></button>
        </div>
        <div class="popup-body">
            <input type="hidden" id="field_source" value="added"/>

            <table id="add_new_fields_form" class="form-table">
                <tr id="set_field_id">
                    <th class="w-50">
                        <?php echo esc_html__( 'Nome e ID do campo *', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__( 'Informe o nome que será usado no campo em letras minúsculas, usando underline no lugar dos espaços e após o prefixo "billing_".', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <div class="input-group">
                            <span class="w-fit input-group-text"><?php echo esc_html__( 'billing_', 'aireset-default' ) ?></span>
                            <input type="text" class="form-control" id="checkout_field_name" name="checkout_field_name" value=""/>
                        </div>
                        <input type="hidden" id="checkout_field_name_concat" value=""/>
                        <div id="check_field_availability" class="d-none bg-translucent-danger text-danger px-3 py-2 rounded-pill mt-2 w-fit" data-avalability="true"><?php echo esc_html__( 'Este nome e ID do campo já está em uso. Use um outro nome.', 'aireset-default' ) ?></div>
                    </td>
                </tr>
                
                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Tipo do campo *', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__( 'Selecione o tipo do campo que será incluído na finalização de compras.', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <select id="checkout_field_type" class="form-select">
                            <option value="text"><?php echo esc_html__( 'Texto', 'aireset-default' ) ?></option>
                            <option value="textarea"><?php echo esc_html__( 'Área de texto', 'aireset-default' ) ?></option>
                            <option value="number"><?php echo esc_html__( 'Número', 'aireset-default' ) ?></option>
                            <option value="password"><?php echo esc_html__( 'Senha', 'aireset-default' ) ?></option>
                            <option value="phone"><?php echo esc_html__( 'Telefone', 'aireset-default' ) ?></option>
                            <option value="url"><?php echo esc_html__( 'URL', 'aireset-default' ) ?></option>
                            <option value="select"><?php echo esc_html__( 'Seletor/Lista suspensa', 'aireset-default' ) ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="container-separator require-add-new-field-select d-none"></tr>

                <tr class="require-add-new-field-select d-none">
                    <td class="w-100 d-flex flex-column align-items-start">
                        <select id="preview_select_new_field" class="form-select"></select>
                        <div id="preview_options_container" class="my-3"></div>

                        <div class="d-flex align-items-center mt-4">
                            <div class="input-group me-2">
                                <span class="input-group-text w-fit"><?php echo esc_html__( 'Valor da opção', 'aireset-default' ) ?></span>
                                <input type="text" id="add_new_field_select_option_value" class="form-control input-control-wd-12" value="" placeholder="<?php echo esc_html__( 'BR', 'aireset-default' ) ?>"/>
                            </div>
                            
                            <div class="input-group me-3">
                                <span class="input-group-text w-fit"><?php echo esc_html__( 'Título da opção', 'aireset-default' ) ?></span>
                                <input type="text" id="add_new_field_select_option_title" class="form-control input-control-wd-12" value="" placeholder="<?php echo esc_html__( 'Brasil', 'aireset-default' ) ?>"/>
                            </div>

                            <div class="w-25">
                                <button id="add_new_options_to_select" class="btn btn-icon btn-icon-lg btn-outline-secondary">
                                    <svg class="icon icon-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="container-separator require-add-new-field-select d-none"></tr>

                <tr class="container-separator require-add-new-field-multicheckbox d-none"></tr>

                <tr class="require-add-new-field-multicheckbox d-none">
                    <td class="w-100 d-flex flex-column align-items-start">
                        <div id="preview_multicheckbox_container" class="my-3"></div>

                        <div class="d-flex align-items-center mt-4">
                            <div class="input-group me-2">
                                <span class="input-group-text w-fit"><?php echo esc_html__( 'ID da opção', 'aireset-default' ) ?></span>
                                <input type="text" id="add_new_field_multicheckbox_option_id" class="form-control input-control-wd-12" value="" placeholder="<?php echo esc_html__( 'verify_gdpr', 'aireset-default' ) ?>"/>
                            </div>

                            <div class="input-group me-3">
                                <span class="input-group-text w-fit"><?php echo esc_html__( 'Título da opção', 'aireset-default' ) ?></span>
                                <input type="text" id="add_new_field_multicheckbox_option_title" class="form-control input-control-wd-12" value="" placeholder="<?php echo esc_html__( 'GDPR', 'aireset-default' ) ?>"/>
                            </div>

                            <div class="w-25">
                                <button id="add_new_options_to_multicheckbox" class="btn btn-icon btn-icon-lg btn-outline-secondary">
                                    <svg class="icon icon-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="container-separator require-add-new-field-multicheckbox d-none"></tr>
                
                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Título do campo *', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__( 'Informe o título do campo.', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <input type="text" class="form-control" id="checkout_field_title" name="checkout_field_title" value=""/>
                    </td>
                </tr>

                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Obrigatoriedade do campo', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__('Ao desativar, este campo se tornará não obrigatório.', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="toggle-switch toggle-active-field" id="required_field" value="no"/>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Posição do campo', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__('Define a posição deste campo na finalização de compras.', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <select class="form-select" id="field_position">
                            <option value="left"><?php echo esc_html__( 'Esquerda', 'aireset-default' ) ?></option>
                            <option value="right"><?php echo esc_html__( 'Direita', 'aireset-default' ) ?></option>
                            <option value="full"><?php echo esc_html__( 'Largura completa', 'aireset-default' ) ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Classe CSS personalizada do campo (Opcional)', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__('Informe a(s) classe(s) CSS personalizadas para este campo. (Opcional)', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <input type="text" class="form-control" id="field_classes" value=""/>
                    </td>
                </tr>

                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Classe CSS personalizada do título (Opcional)', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__('Informe a(s) classe(s) CSS personalizadas para o título (label) deste campo. (Opcional)', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <input type="text" class="form-control" id="field_label_classes" value=""/>
                    </td>
                </tr>

                <tr class="require-input-mask">
                    <th class="w-50">
                        <?php echo esc_html__( 'Máscara do campo (Opcional)', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__('Adicione uma máscara de preenchimento para este campo, seguindo o padrão informado pela documentação. (Opcional)', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <input type="text" class="form-control" id="field_input_mask" value=""/>
                    </td>
                </tr>

                <tr>
                    <th class="w-50">
                        <?php echo esc_html__( 'Etapa do campo', 'aireset-default' ) ?>
                        <span class="aireset-default-description"><?php echo esc_html__('Define em qual etapa da finalização de compras o campo será exibido.', 'aireset-default' ) ?></span>
                    </th>
                    <td class="w-50">
                        <select class="form-select" id="field_step">
                            <option value="1"><?php echo esc_html__( 'Etapa 1 (Contato)', 'aireset-default' ) ?></option>
                            <option value="2"><?php echo esc_html__( 'Etapa 2 (Entrega)', 'aireset-default' ) ?></option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <div class="popup-footer">
            <div class="w-100 d-flex justify-content-end">
                <button id="fcw_add_new_field" class="btn btn-primary" ><?php echo esc_html__( 'Adicionar campo', 'aireset-default' ) ?></button>
            </div>
        </div>
    </div>
</div>