# Copilot Instructions - Aireset Default Plugin

## Visão Geral do Projeto

Este é um plugin WordPress/WooCommerce chamado **Aireset - Geral** que fornece diversas funcionalidades padrão para e-commerces e sites institucionais. O plugin é desenvolvido em PHP e segue a arquitetura de plugins do WordPress.

## Estrutura do Projeto

```
aireset-default/
├── aireset-default.php          # Arquivo principal do plugin
├── includes/
│   ├── class-init.php           # Classe de inicialização principal
│   ├── actions.php              # Carrega e inicializa as classes de funcionalidades
│   ├── functions.php            # Funções auxiliares globais
│   ├── classes/                 # Classes principais do plugin
│   │   ├── class-ajax.php       # Manipulador de requisições AJAX
│   │   ├── class-admin-options.php  # Opções do painel admin
│   │   ├── class-assets.php     # Gerenciamento de scripts e estilos
│   │   ├── class-custom-fields.php  # Campos personalizados para Elementor
│   │   ├── class-helpers.php    # Funções auxiliares
│   │   ├── class-license.php    # Gerenciamento de licença
│   │   └── shipping/            # Calculadora de frete
│   ├── admin/                   # Arquivos do painel administrativo
│   │   ├── tabs_aireset/        # Abas de configuração
│   │   └── settings.php         # Configurações gerais
│   ├── elementor-dynamic-tags/  # Tags dinâmicas do Elementor
│   ├── elementor-widgets/       # Widgets do Elementor
│   ├── cart/                    # Funcionalidades do carrinho
│   ├── checkout/                # Funcionalidades do checkout
│   ├── orders/                  # Funcionalidades de pedidos
│   ├── frontend/                # Funcionalidades do frontend
│   ├── images/                  # Gerenciamento de imagens
│   ├── integrations/            # Integrações com outros plugins
│   ├── permalinks/              # Gerenciamento de permalinks
│   ├── shipping/                # Calculadora de frete
│   └── shortcodes/              # Shortcodes do plugin
├── assets/                      # Assets (CSS, JS, imagens)
├── templates/                   # Templates do plugin
├── languages/                   # Arquivos de tradução
└── vendor/                      # Dependências (Composer)
```

## Convenções de Código

### Namespaces
- Usar namespace `Aireset\Default` como base
- Subnamespaces por funcionalidade: `Aireset\Default\Orders`, `Aireset\Default\Cart`, etc.

### Classes
- Uma classe por arquivo
- Nomear arquivos como `class-nome-da-classe.php`
- Usar PascalCase para nomes de classes
- Implementar construtor para hooks e actions

### Hooks WordPress
- Sempre usar prefixo `aireset_default_` para hooks personalizados
- Registrar actions e filters no construtor da classe

### Opções do Plugin
- Armazenar configurações em `aireset_default_settings` (array)
- Campos da empresa também são salvos como opções individuais
- Usar `Init::get_setting('nome_da_opcao')` para obter valores

### JavaScript
- Usar jQuery como dependência
- Localizar scripts com `wp_localize_script()`
- Usar AJAX para salvar configurações

### Segurança
- Sempre usar `sanitize_text_field()` para inputs
- Verificar nonces em requisições AJAX
- Usar `esc_html()`, `esc_attr()` para output

## Funcionalidades Principais

1. **Calculadora de Frete** - Calculadora de CEP na página do produto
2. **Gerenciamento de Pedidos** - Colunas personalizadas, vinculação de clientes
3. **Integração Elementor** - Tags dinâmicas e widgets
4. **Dados da Empresa** - Campos configuráveis para contato/redes sociais
5. **Configurações de Checkout** - Guest payment, meta fields
6. **Shortcodes** - WhatsApp link generator

## Desenvolvimento

### Adicionar Nova Funcionalidade
1. Criar classe em `includes/classes/` ou subdiretório apropriado
2. Adicionar require no `actions.php`
3. Instanciar a classe no mesmo arquivo
4. Adicionar opções relacionadas em `class-init.php`

### Adicionar Aba Admin
1. Criar arquivo em `includes/admin/tabs_aireset/`
2. Registrar em `class-admin-options.php`
3. Campos são salvos automaticamente via AJAX

### Adicionar Widget Elementor
1. Criar classe em `includes/elementor-widgets/`
2. Registrar em `elementor-dynamic-tags/elementor.php`

## Testes

- Verificar sintaxe PHP: `php -l arquivo.php`
- Testar em ambiente WordPress com WooCommerce ativo
- Verificar compatibilidade com HPOS (High-Performance Order Storage)

## Debugging

- Usar `error_log()` para logs
- Trait `Logger` disponível em `class-logger.php`
- WooCommerce logger disponível via `wc_get_logger()`

## Notas Importantes

- Plugin requer PHP 7.4+
- Plugin requer WooCommerce 6.0+
- Compatível com HPOS do WooCommerce
- Sistema de atualizações via GitHub (plugin-update-checker)
