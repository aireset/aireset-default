# Documentação Completa - Aireset Default Plugin

## Visão Geral

**Nome do Plugin:** Aireset - Geral  
**Versão:** 1.3.8  
**Autor:** Felipe Almeman - Aireset Agencia Web  
**Requisitos:** PHP 7.4+, WordPress 4.0+, WooCommerce 5.0+  

O plugin Aireset Default fornece funcionalidades essenciais e padronizações para e-commerces e sites institucionais desenvolvidos com WordPress e WooCommerce.

---

## Estrutura de Arquivos

### Arquivo Principal
- **`aireset-default.php`** - Ponto de entrada do plugin. Define constantes, carrega autoloader do Composer, configura sistema de atualizações via GitHub, e inicializa o plugin.

### Diretório `/includes`

#### Classes Principais (`/includes/classes/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-init.php` | ✅ ATIVO | Classe de inicialização. Define opções padrão e carrega funcionalidades baseadas em configurações. |
| `class-ajax.php` | ✅ ATIVO | Manipula requisições AJAX para salvar configurações do admin. |
| `class-admin-options.php` | ✅ ATIVO | Renderiza o painel de configurações administrativo. |
| `class-admin-fields.php` | ✅ ATIVO | Gerencia campos do formulário admin. |
| `class-assets.php` | ✅ ATIVO | Enqueue de scripts e estilos (admin e frontend). |
| `class-helpers.php` | ✅ ATIVO | Funções auxiliares. |
| `class-license.php` | ✅ ATIVO | Gerenciamento de licença (se aplicável). |
| `class-custom-fields.php` | ✅ ATIVO | Define campos personalizados para Elementor (dados da empresa, gerenciamento de frete). |
| `class-cep-manager.php` | ✅ ATIVO | Gerenciamento de CEP/endereços. |
| `class-conditions.php` | ⚠️ NÃO INCLUÍDO | Condições para checkout. Arquivo existe mas **não é carregado em lugar algum**. |
| `class-compat-autoloader.php` | ⚠️ NÃO INCLUÍDO | Autoloader de compatibilidade. Referenciado mas comentado no `aireset-default.php`. |
| `class-logger.php` | ⚠️ NÃO INCLUÍDO | Trait Logger. Arquivo existe mas **não é usado por nenhuma classe**. |
| `class-elementor-form-input-class.php` | ✅ ATIVO | Adiciona suporte para classes nos campos de formulário Elementor. |
| `class-elementor-form-input-custom-attributes.php` | ✅ ATIVO | Adiciona atributos customizados nos campos de formulário Elementor. |

#### Subdiretório Shipping (`/includes/classes/shipping/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `management-calculator.php` | ✅ ATIVO | Calculadora de frete na página do produto. Carregado condicionalmente. |
| `management-custom-colors.php` | ✅ ATIVO | Cores personalizadas para calculadora de frete. |
| `management-init.php` | ⚠️ NÃO INCLUÍDO | Classe init para shipping. **Não é carregada em lugar algum** e tem erro (referencia `Init` sem namespace e `HUBGO_SHIPPING_MANAGEMENT_VERSION` não definido). |

#### Admin (`/includes/admin/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `settings.php` | ✅ ATIVO | Configurações gerais. |
| `class-admin-notices.php` | ✅ ATIVO | Avisos administrativos. |
| `tabs_aireset/` | ✅ ATIVO | Diretório com abas de configuração (about, business, conditions, design, docs, fields, frete, integrations, options, texts). |

#### Actions (`/includes/actions.php`)

**Status:** ✅ ATIVO

Carrega e inicializa as seguintes classes:
- `ShippingCalculator`
- `OrderCustomer`
- `OrderColumns`
- `ImageSizes`
- `WhatsappShortcode`
- `ViewportManager`
- `GuestPayment`
- `MetaCheckoutHandler`
- `CartMessageManager`
- `YithSearchManager`
- `PermalinkManager`
- Dynamic Tags do Elementor

#### Cart (`/includes/cart/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-cart-message-manager.php` | ✅ ATIVO | Gerencia mensagens do carrinho (desabilita mensagem de "adicionado ao carrinho"). |

#### Checkout (`/includes/checkout/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-guest-payment.php` | ✅ ATIVO | Permite pagamento de pedidos sem login. |
| `class-meta-checkout-handler.php` | ✅ ATIVO | Manipula meta dados do checkout. |

#### Cron (`/includes/cron/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `orders.php` | ⚠️ NÃO INCLUÍDO | Cron para verificar pedidos. **Não é carregado em lugar algum**. Define funções para atualizar pedidos "Pago" para "Em Produção" após 3 dias. |

#### Elementor Dynamic Tags (`/includes/elementor-dynamic-tags/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `elementor.php` | ✅ ATIVO | Integração principal com Elementor. Registra grupo de tags, tags dinâmicas e widgets. |
| `text.php` | ✅ ATIVO | Tag dinâmica de texto. |
| `color.php` | ⚠️ NÃO INCLUÍDO | Tag dinâmica de cor. Comentado no `elementor.php`. |
| `file.php` | ⚠️ NÃO INCLUÍDO | Tag dinâmica de arquivo. Comentado no `elementor.php`. |
| `gallery.php` | ⚠️ NÃO INCLUÍDO | Tag dinâmica de galeria. Comentado no `elementor.php`. |
| `image.php` | ⚠️ NÃO INCLUÍDO | Tag dinâmica de imagem. Comentado no `elementor.php`. |
| `number.php` | ⚠️ NÃO INCLUÍDO | Tag dinâmica de número. Comentado no `elementor.php`. |
| `url.php` | ⚠️ NÃO INCLUÍDO | Tag dinâmica de URL. Comentado no `elementor.php`. |

#### Elementor Widgets (`/includes/elementor-widgets/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-shipping-calculator-widget.php` | ✅ ATIVO | Widget Elementor para calculadora de frete. |

#### Elementor Dynamic Tags (Raiz)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `elementor-dynamic-tags.php` | ⚠️ PARCIALMENTE ATIVO | Define tags `Aireset_Dynamic_Text_Tag` e `Aireset_Dynamic_URL_Tag`. É carregado via hook `elementor/dynamic_tags/register_tags` em `actions.php`. |

#### Frontend (`/includes/frontend/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-viewport-manager.php` | ✅ ATIVO | Adiciona meta tag para desabilitar zoom no mobile. |

#### Images (`/includes/images/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-image-sizes.php` | ✅ ATIVO | Gerenciamento de tamanhos de imagem. |

#### Integrations (`/includes/integrations/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-yith-search-manager.php` | ✅ ATIVO | Integração com YITH WooCommerce Ajax Search. |

#### Orders (`/includes/orders/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-order-columns.php` | ✅ ATIVO | Colunas personalizadas na lista de pedidos (WhatsApp, CPF/CNPJ, etc). |
| `class-order-customer.php` | ✅ ATIVO | Vinculação de cliente ao pedido no admin. |
| `class-order-status.php` | ⚠️ NÃO INCLUÍDO | Status personalizados de pedidos. Comentado em `actions.php`. Código interno está todo comentado no construtor. |

#### Permalinks (`/includes/permalinks/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-permalink-manager.php` | ✅ ATIVO | Gerenciamento de permalinks. |

#### Shipping (`/includes/shipping/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-shipping-calculator.php` | ✅ ATIVO | Calculadora de frete adicional. |

#### Shortcodes (`/includes/shortcodes/`)

| Arquivo | Status | Descrição |
|---------|--------|-----------|
| `class-whatsapp-shortcode.php` | ✅ ATIVO | Shortcode `[whatsapp_link]` para gerar links do WhatsApp. |

#### Funções Globais (`/includes/functions.php`)

| Função | Status | Descrição |
|--------|--------|-----------|
| `ignorar_miniaturas_na_api_tiny()` | ✅ ATIVO | Ignora criação de miniaturas em requisições REST API. |
| `is_aireset_default_admin_settings()` | ✅ ATIVO | Verifica se está na página de configurações do plugin. |
| `is_aireset_default()` | ✅ ATIVO | Verifica se está na página de checkout. |
| `is_aireset_template()` | ⚠️ PROBLEMA | Usa `Core::is_thankyou_page()` mas classe `Core` **não existe** no plugin. |
| `aireset_default_only_virtual()` | ✅ ATIVO | Verifica se carrinho contém apenas produtos virtuais. |
| `remove_filters_with_method_name()` | ❌ COMENTADO | Função para remover filtros por nome de método. |
| `order_has_shipping_method()` | ❌ COMENTADO | Verifica se pedido tem método de entrega. |

---

## Problemas Identificados

### 1. Arquivos Não Carregados

Os seguintes arquivos existem mas **não são incluídos/carregados em nenhum lugar**:

1. **`class-conditions.php`** - Implementa condições para campos, pagamentos e métodos de entrega no checkout. Funcionalidade completa mas não ativada.

2. **`class-compat-autoloader.php`** - Autoloader para classes de compatibilidade. Comentado no arquivo principal.

3. **`class-logger.php`** - Trait Logger para logging. Não é usado por nenhuma classe.

4. **`management-init.php`** (shipping) - Classe de inicialização do módulo de shipping. Tem erros de código e não é carregada.

5. **`cron/orders.php`** - Cron job para atualização automática de status de pedidos. Não é incluído em nenhum lugar.

6. **`class-order-status.php`** - Status personalizados de pedidos. Carregado no `actions.php` mas comentado.

7. **Tags dinâmicas Elementor** (color.php, file.php, gallery.php, image.php, number.php, url.php) - Arquivos existem mas estão comentados no `elementor.php`.

### 2. Erros de Código

1. **`functions.php`** linha 126 - `is_aireset_template()` usa `Core::is_thankyou_page()` mas a classe `Core` não existe no plugin.

2. **`management-init.php`** - Usa `HUBGO_SHIPPING_MANAGEMENT_VERSION` que não está definido. Referencia `Init` sem namespace correto.

3. **`class-order-status.php`** linha 297 - Cria classes de email dentro de condição que verifica setting que pode não existir no contexto global.

### 3. Código Comentado

Muitas funcionalidades estão comentadas no código, indicando features planejadas ou desabilitadas:

- Freemius integration (`aireset-default.php`)
- Vue.js frontend (`aireset-default.php`)
- Várias opções de configuração em `class-init.php`
- Admin menu próprio (`aireset-default.php`)
- Máscaras de campo no frontend (`class-assets.php`)

---

## Funcionalidades Ativas

### 1. Calculadora de Frete
- **Arquivos:** `classes/shipping/management-calculator.php`, `shipping/class-shipping-calculator.php`
- **Configuração:** `aireset_default_enable_shipping_calculator`
- **Descrição:** Exibe calculadora de CEP na página do produto. Suporta frete grátis, Correios, e múltiplos métodos de entrega.

### 2. Gerenciamento de Pedidos
- **Arquivos:** `orders/class-order-columns.php`, `orders/class-order-customer.php`
- **Descrição:** Adiciona colunas personalizadas (WhatsApp, CPF/CNPJ) na lista de pedidos e permite criar/vincular clientes diretamente do pedido.

### 3. Integração Elementor
- **Arquivos:** `elementor-dynamic-tags/`, `elementor-widgets/`, `elementor-dynamic-tags.php`
- **Descrição:** Tags dinâmicas para exibir dados da empresa e widget de calculadora de frete.

### 4. Dados da Empresa
- **Arquivos:** `admin/tabs_aireset/business.php`, `classes/class-custom-fields.php`
- **Descrição:** Painel para configurar informações de contato, redes sociais, endereço, CNPJ, etc.

### 5. Guest Payment
- **Arquivo:** `checkout/class-guest-payment.php`
- **Configuração:** `aireset_default_order_pay_without_login`
- **Descrição:** Permite que clientes paguem pedidos sem necessidade de login.

### 6. Shortcode WhatsApp
- **Arquivo:** `shortcodes/class-whatsapp-shortcode.php`
- **Uso:** `[whatsapp_link number="5511999999999" message="Olá"]`
- **Descrição:** Gera link do WhatsApp com número e mensagem personalizados.

### 7. Viewport Manager
- **Arquivo:** `frontend/class-viewport-manager.php`
- **Configuração:** `aireset_default_fixed_viewport`
- **Descrição:** Adiciona meta tag para desabilitar zoom no mobile.

### 8. Cart Message Manager
- **Arquivo:** `cart/class-cart-message-manager.php`
- **Configuração:** `aireset_default_disable_add_to_cart_message`
- **Descrição:** Desabilita mensagem "produto adicionado ao carrinho".

### 9. YITH Search Integration
- **Arquivo:** `integrations/class-yith-search-manager.php`
- **Descrição:** Personalização do YITH WooCommerce Ajax Search.

---

## Configurações Disponíveis

Todas as configurações são armazenadas em `aireset_default_settings` (array) no banco de dados.

| Opção | Padrão | Descrição |
|-------|--------|-----------|
| `aireset_default_fixed_viewport` | yes | Meta tag para viewport fixo |
| `aireset_default_disable_add_to_cart_message` | no | Desabilitar mensagem de carrinho |
| `aireset_default_order_pay_without_login` | yes | Pagamento sem login |
| `aireset_default_masks` | yes | Máscaras de input |
| `aireset_default_intl_tel_input` | yes | Input de telefone internacional |
| `aireset_default_auto_create_or_assign_customer_to_order` | yes | Criar/vincular cliente automaticamente |
| `aireset_default_images` | no | Gerenciamento de imagens |
| `aireset_default_yith_wcas_submit_label` | no | Label de submit YITH |
| `aireset_default_custom_orders_list_column_content` | yes | Colunas personalizadas em pedidos |
| `aireset_default_enable_shipping_calculator` | yes | Habilitar calculadora de frete |
| `aireset_default_enable_auto_shipping_calculator` | yes | Cálculo automático de frete |
| `aireset_default_primary_main_color` | #000000 | Cor principal |
| `aireset_default_hook_display_shipping_calculator` | after_cart | Posição da calculadora |
| `aireset_default_text_info_before_input_shipping_calc` | Consultar prazo... | Texto informativo |
| `aireset_default_text_button_shipping_calc` | Calcular | Texto do botão |
| `aireset_default_text_header_ship` | Entrega | Cabeçalho entrega |
| `aireset_default_text_header_value` | Valor | Cabeçalho valor |
| `aireset_default_note_text_bottom_shipping_calc` | *Este resultado... | Nota inferior |
| `aireset_default_text_placeholder_input_shipping_calc` | Informe seu CEP | Placeholder CEP |

---

## Hooks e Filters

### Actions
- `aireset_default_setup_includes` - Modificar lista de includes do plugin
- `aireset_default_set_default_options` - Modificar opções padrão

### Filters
- `aireset_default_set_default_options` - Filtrar opções padrão
- `aireset_is_aireset_template` - Verificar se é template Aireset

---

## Compatibilidade

- **WordPress:** 4.0+
- **WooCommerce:** 5.0+ (testado até 9.3.3)
- **PHP:** 7.4+
- **HPOS:** Compatível com High-Performance Order Storage

---

## Sistema de Atualizações

O plugin utiliza [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) para atualizações via GitHub.

- **Repositório:** `https://github.com/aireset/aireset-default/`
- **Branch:** `master`
- **Release Assets:** Habilitado

---

## Recomendações

1. **Ativar `class-conditions.php`** - Funcionalidade completa que pode adicionar valor ao plugin.

2. **Corrigir `is_aireset_template()`** - Remover ou criar classe `Core` com método `is_thankyou_page()`.

3. **Ativar tags dinâmicas Elementor** - Descomentar registros de url, image, file, etc.

4. **Ativar cron de pedidos** - Incluir `cron/orders.php` se funcionalidade for desejada.

5. **Corrigir `management-init.php`** - Ou remover arquivo se não for necessário.

6. **Utilizar Trait Logger** - Implementar logging nas classes principais.

7. **Limpar código comentado** - Remover ou ativar funcionalidades comentadas.
