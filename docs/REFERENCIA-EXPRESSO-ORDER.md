# Referência de Arquitetura — Aireset Expresso Order

> **Propósito deste documento**
> O `aireset-expresso-order` é o **plugin-base de referência** da Aireset para
> desenvolvimento: define o padrão de **layout, framework PHP, frontend (SPA),
> documentos/PDF, licenciamento e governança para IAs**.
> Este arquivo documenta *como ele está construído* para servir de **blueprint**
> ao evoluir o `aireset-default` e os demais plugins `aireset-*`.
>
> Ele descreve a arquitetura do Expresso Order; **não** autoriza alterar aquele
> plugin. O Expresso Order é proprietário (ver seção 11). Aqui ele é **modelo**.
>
> - Plugin documentado: `wp-content/plugins/aireset-expresso-order`
> - Versão de referência: `1.2.24`
> - Última atualização desta referência: 2026-06-06

---

## 1. Visão geral

O Expresso Order é um plugin WordPress/WooCommerce **proprietário** que combina
três camadas modernas dentro do padrão de plugin clássico:

1. **Backend PHP modular** — bootstrap enxuto + classes de responsabilidade única em `includes/`, todas sob o prefixo `EOP_`/`eop_`.
2. **Admin SPA React + TypeScript + Vite** — interface administrativa principal, consumindo uma API REST dedicada, com **fallback** para o admin legado em PHP via feature flag.
3. **Sistema de licença + integridade + telemetria** (Elite Licenser) que protege o boot do plugin.

A documentação canônica do próprio plugin vive em `docs/` (ARCHITECTURE, ROADMAP,
OPERATIONS, LEGAL_AND_AI_POLICY) + `AGENT.md` + `.github/copilot-instructions.md`.
**Esse conjunto de documentos é, em si, parte do padrão a ser replicado.**

---

## 2. Estrutura de pastas (layout padrão)

```
aireset-expresso-order/
├── aireset-expresso-order.php      # Bootstrap: constantes, ativação, gate de licença, init
├── README.md                       # Porta de entrada + índice da doc canônica
├── AGENT.md                        # Instruções curtas para agentes de IA
├── CHANGELOG.md                    # Histórico de versões
├── LICENSE                         # Texto integral da licença proprietária
├── readme.txt                      # Cabeçalho padrão WordPress
├── .github/
│   ├── copilot-instructions.md     # Política/regra técnica para Copilot
│   ├── instructions/               # Instruções específicas por contexto
│   └── workflows/release-zip.yml   # CI de empacotamento
├── docs/
│   ├── ARCHITECTURE.md             # Arquitetura atual + alvo + contratos REST
│   ├── ROADMAP.md                  # Backlog e matriz funcional por superfície
│   ├── OPERATIONS.md               # Build, release, smoke tests, performance
│   ├── LEGAL_AND_AI_POLICY.md      # Titularidade, uso vedado, política de IA
│   └── archive/2026-05/            # Documentos históricos (NÃO canônicos)
├── includes/                       # Classes PHP (uma responsabilidade cada)
│   ├── class-admin-page.php        # Shell admin legado / roteamento de views
│   ├── class-admin-spa.php         # Boot do SPA + REST namespace + contratos
│   ├── class-ajax-handlers.php     # Handlers AJAX legados (fallback)
│   ├── class-settings.php          # Configurações gerais (option única)
│   ├── class-settings-portability.php  # Exportar/importar settings
│   ├── class-pdf-settings.php / class-pdf-admin-page.php / class-document-manager.php
│   ├── class-wc-pdf-integration.php
│   ├── class-order-creator.php / class-orders-page.php
│   ├── class-shipping-calculator.php
│   ├── class-shortcode.php / class-public-proposal.php
│   ├── class-post-confirmation-flow.php
│   ├── class-role.php / class-page-installer.php / class-performance-audit.php
│   ├── class-eop-license-manager.php / class-eop-license-base.php
│   ├── class-eop-integrity.php / class-eop-telemetry.php
│   ├── trait-eop-license-guard.php
│   └── trait-eop-attachment-documents.php
├── templates/                      # Renderers PHP (admin legado + superfícies públicas)
│   ├── admin-app.php               # Container que monta o SPA
│   ├── admin-page.php / orders-*.php / pdf-admin-page.php
│   ├── documentation-page.php / shortcode-page.php
│   └── settings/embedded/*.php      # Telas de settings por domínio
├── assets/
│   ├── scss/  → css/                # Estilos legados (compilados por sass)
│   ├── js/                          # JS legado (admin, orders, fontselect, coloris…)
│   ├── images/
│   └── admin-spa/
│       ├── src/                     # React + TS (main.tsx, App.tsx, app/*)
│       └── dist/                    # Bundle compilado + .vite/manifest.json
├── scripts/                        # build-release-package.php, smoke/perf .mjs
├── package.json / vite.config.ts / tsconfig*.json
└── dist/                           # Pacote de distribuição gerado
```

**Princípios de layout a replicar:**
- Bootstrap fino na raiz; lógica em `includes/`.
- Uma classe por arquivo, nome `class-<dominio>.php`, classe `EOP_<Dominio>`.
- Renderers (HTML) ficam em `templates/`, nunca embutidos em strings PHP grandes.
- Assets com fonte (`scss/`, `admin-spa/src/`) separados do compilado (`css/`, `dist/`).
- Documentação canônica em `docs/` + `AGENT.md` + instruções de IA em `.github/`.

---

## 3. Framework PHP e convenções

### 3.1 Constantes e prefixos

Definidas no topo do bootstrap (`aireset-expresso-order.php`):

```php
define( 'EOP_VERSION', '1.2.24' );
define( 'EOP_PLUGIN_FILE', __FILE__ );
define( 'EOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EOP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EOP_TEXT_DOMAIN', 'aireset-expresso-order' );
```

- Prefixo de constantes/funções: `EOP_` / `eop_`.
- Prefixo de classes: `EOP_` (ex.: `EOP_Admin_SPA`, `EOP_Settings`).
- Text domain único e constante (`EOP_TEXT_DOMAIN`).
- `EOP_REQUEST_START` marca o início para a auditoria de performance.

### 3.2 Ciclo de vida

| Fase | Função | Conteúdo |
|------|--------|----------|
| Ativação | `eop_activate()` (`register_activation_hook`) | Cria role `vendedor_expresso`, instala páginas via `EOP_Page_Installer::activate()` |
| Desativação | `eop_deactivate()` | Remove a role |
| i18n | `eop_load_textdomain()` no hook `init` | `load_plugin_textdomain` a partir de `/languages` |
| Boot | `eop_init()` no hook `plugins_loaded` | Verifica WooCommerce e chama `::init()` de cada módulo |

### 3.3 Padrão de módulo

Cada classe expõe um `public static function init()` que registra seus próprios
hooks. O bootstrap apenas **carrega os arquivos** (`require_once`) e depois
**inicializa** cada módulo em `eop_init()`:

```php
function eop_init() {
    if ( ! class_exists( 'WooCommerce' ) ) { /* admin_notice e return */ }

    EOP_Page_Installer::init();
    EOP_Admin_SPA::init();
    EOP_Admin_Page::init();
    EOP_Ajax_Handlers::init();
    EOP_Document_Manager::init();
    // … demais módulos …
    EOP_Role::restrict_menus();
}
add_action( 'plugins_loaded', 'eop_init' );
```

**Regra:** dependência de WooCommerce é sempre verificada antes de inicializar.
Sem WooCommerce → `admin_notice` e aborta, nunca fatal.

---

## 4. Sistema de licença (gate de boot)

O Expresso Order é protegido por **Elite Licenser** com três núcleos:
`EOP_License_Manager`, `EOP_Integrity_Core` e `EOP_Telemetry_Core`.

### 4.1 Gate no bootstrap

Logo após carregar o manager, o boot **interrompe** se a licença for inválida:

```php
require_once EOP_PLUGIN_DIR . 'includes/class-eop-license-manager.php';
EOP_License_Manager::get_instance( __FILE__ );

if ( ! EOP_License_Manager::is_valid() ) {
    add_action( 'admin_notices', /* aviso de ativação */ );
    return; // nenhum módulo funcional é carregado
}
```

- A licença é **a primeira coisa** validada; módulos de negócio só são incluídos depois do gate.
- `EOP_License_Manager` é singleton (`get_instance`/`is_valid`).
- Chaves guardadas em options com prefixo `Aireset-ExpressoOrder_lic_*` (chave ofuscada via `EOP_License_Core::get_lic_key_param`).
- Ativação/desativação via `admin-post.php` com nonce `el-license`.

### 4.2 Integridade e telemetria

`trait-eop-license-guard.php` (trait `EOP_License_Guard`) oferece a módulos
protegidos:
- `_resolve_env_config()` — confere `verify_distribution()` (arquivos essenciais presentes) e `verify_class_integrity()`.
- `_flag_env_status()` — reporta eventos ao `EOP_Telemetry_Core` com rate-limit.
- `_push_integrity_notice()` — avisos no admin em caso de tamper.

**Regra de ouro (vale para todo plugin licenciado da Aireset):** o gate de
licença e os mecanismos de integridade **nunca** são removidos, contornados ou
desofuscados sem autorização humana registrada do titular (ver seção 11).

---

## 5. Admin SPA (React + TypeScript + Vite)

A superfície administrativa principal é uma SPA. O PHP entrega um **bootstrap
mínimo**; o React assume a UI.

### 5.1 Stack

- `react` + `react-dom` 19
- `typescript` 5
- `vite` 7 + `@vitejs/plugin-react`
- `playwright` para smoke browser

`vite.config.ts`:
```ts
build: {
  outDir: 'assets/admin-spa/dist',
  emptyOutDir: true,
  manifest: true,                                  // gera .vite/manifest.json
  rollupOptions: { input: 'assets/admin-spa/src/main.tsx' },
}
```

### 5.2 Integração PHP ↔ SPA (`EOP_Admin_SPA`)

- **Lê o manifest do Vite** (`assets/admin-spa/dist/.vite/manifest.json`) para descobrir o JS/CSS com hash.
- **Enfileira o bundle** como `type="module"` (filtro `script_loader_tag`) apenas na página `aireset_page_eop-pedido-expresso`.
- **Versão por `filemtime`** do arquivo compilado (cache-busting confiável).
- Injeta **configuração inline** antes do script:
  ```php
  window.eopAdminSpaConfig = { rest_root, rest_nonce, legacy_admin_url, initial_view, documentation_url };
  ```
- O bootstrap (payload pesado) também pode ir **inline** (`window.eopAdminSpaBootstrap`) para eliminar o round-trip REST no primeiro carregamento — o cliente cai para `GET /bootstrap` se ausente.
- `wp_enqueue_media()` é chamado para o seletor de logo funcionar dentro do SPA.

### 5.3 Feature flag e fallback legado

Decisão centralizada em `EOP_Admin_SPA::is_enabled()`:

1. Exige capability `edit_shop_orders`.
2. Exige **bundle compilado** (`has_built_assets()`); sem build → cai limpo no admin legado.
3. `?eop_admin_legacy=1` (para quem tem `manage_options`) força o legado.
4. Option `eop_admin_experimental.enabled` (default `yes`) + filtro `eop_enable_admin_spa`.

> Padrão a replicar: **toda migração de UI é por feature flag, com fallback
> funcional preservado e equivalência comprovada antes do cutover.**

### 5.4 Estrutura do cliente (`assets/admin-spa/src/`)

- `main.tsx` — entrypoint que monta o `<App/>`.
- `App.tsx` — shell único: sidebar de navegação + área de conteúdo; gerencia estado por domínio (bootstrap, settings, orders, previews) com `useState`/`useRef` e **cache em memória** por seção/superfície.
- `app/api.ts` — cliente REST tipado (`adminApi`), injeta `X-WP-Nonce`, `credentials: same-origin`.
- `app/types.ts` — contratos TypeScript dos payloads.
- `app/view-config.ts` — mapa declarativo de navegação: `primaryNavItems`, `navGroups`, `utilityNavItems`, `labels`, e os mapas `settingsSectionByView` / `previewSurfaceByView`.

O `App.tsx` consome **CSS variables de branding** (`--eop-primary`, `--eop-surface`,
`--eop-border`, `--eop-radius`, `--eop-font-family`) vindas do bootstrap, de modo
que o tema do painel é dirigido pelas configurações salvas.

---

## 6. Contrato REST administrativo

Namespace dedicado: **`aireset-expresso-order/v1/admin`** (constante
`EOP_Admin_SPA::REST_NAMESPACE`). Registrado em `rest_api_init`.

| Método | Rota | Permissão | Função |
|--------|------|-----------|--------|
| GET | `/bootstrap` | `edit_shop_orders` | Usuário, licença, flags, branding, rotas, views, seções, docs |
| GET/POST | `/settings/{section}` | `manage_options` | Ler / salvar uma seção de configuração |
| GET | `/previews/{surface}` | `manage_options` | Preview server-side da superfície pública |
| GET | `/views/{view_name}` | `edit_shop_orders` | Lazy-load de view do admin |
| GET | `/pdf-tabs/{pdf_tab}` | `edit_shop_orders` | Aba do módulo PDF |
| GET | `/customers/search` | `edit_shop_orders` | Busca de cliente por documento |
| GET | `/products` · `/product-categories` | `edit_shop_orders` | Busca de produtos/categorias |
| POST | `/shipping/rates` | `edit_shop_orders` | Cálculo de frete por payload |
| GET/POST | `/orders` | `edit_shop_orders` | Listar / criar pedido |
| GET/PUT | `/orders/{id}` | `edit_shop_orders` | Detalhe / atualizar pedido |
| PUT | `/orders/{id}/post-confirmation/stage` | `edit_shop_orders` | Avançar etapa do fluxo complementar |

**Padrões de segurança (obrigatórios em todo endpoint novo):**
- `permission_callback` sempre presente (`can_access_admin_app` = `edit_shop_orders`; `can_manage_settings` = `manage_options`).
- Nonce REST `wp_rest` via header `X-WP-Nonce`.
- Sanitização de entrada (`sanitize_key`, `sanitize_text_field`, `absint`).
- Erros como `WP_Error` com `status` HTTP adequado.
- Acesso a pedido valida `EOP_Orders_Page::current_user_can_access_order()`.
- Pedidos sempre via **CRUD API do WooCommerce** (`wc_get_order`, `$order->...`) — compatível com HPOS.

> **Migração AJAX → REST:** o JS legado tenta primeiro o namespace REST e cai
> para `admin-ajax.php`. A remoção de handlers `wp_ajax_*` é feita **por domínio**,
> nunca em lote, sempre após validação no navegador.

---

## 7. Arquitetura de configurações (settings)

Modelo de **option única + seções declarativas**, centralizado em
`EOP_Admin_SPA::get_section_definitions()`:

- Dois "sources": `settings` (`EOP_Settings::OPTION_KEY`) e `pdf` (`EOP_PDF_Settings::OPTION_KEY`).
- Cada seção declara as chaves que possui por:
  - `exact` → lista de chaves exatas, ou
  - `prefix` → todas as chaves que começam com o prefixo (ex.: `proposal_`, `post_confirmation_contract_`).
- `filter_allowed_settings_keys()` garante que o POST de uma seção **só toca as chaves daquela seção** (merge seguro com o existente + sanitização do módulo dono).
- O backend devolve, além de `values`:
  - `fields` → descrição declarativa para o React renderizar (tipos `text`, `textarea`, `select`, `toggle`, `media`),
  - `meta` → título, descrição, intro e label do botão (reproduz o visual do admin legado por domínio).

Seções de referência: `store`, `general`, `proposal`, `new-order`, `orders-list`,
`confirmation-general`, `confirmation-contract`, `confirmation-upload-products`, `pdf`.

> A seção `store` compartilha base com o WooCommerce e com o **Aireset Default** —
> alterar ali reflete nos dois. Esse é um ponto de integração direto entre os plugins.

---

## 8. Previews como fonte de verdade

Regra arquitetural central: **o preview do admin nunca é um mock**. Ele consome
o **renderer público real** (ou um renderer servidor equivalente):

| Superfície | Fonte do preview |
|------------|------------------|
| `new-order` | iframe da página/shortcode público real |
| `proposal` | `EOP_Public_Proposal::render_admin_preview_card()` |
| `confirmation-contract` | `EOP_Post_Confirmation_Flow::render_admin_contract_preview_markup()` |
| `confirmation-upload-products` | `EOP_Post_Confirmation_Flow::render_admin_upload_products_preview_markup()` |

> Divergência entre preview e renderer público é tratada como **regressão funcional**.

---

## 9. Documentos e PDF

- `EOP_PDF_Settings` — option e sanitização das configs de loja/documento.
- `EOP_PDF_Admin_Page` — abas de configuração do PDF (lazy via REST `/pdf-tabs/{tab}`).
- `EOP_Document_Manager` + `trait-eop-attachment-documents.php` — geração/anexo de documentos.
- `EOP_WC_PDF_Integration` — integração com o pipeline do WooCommerce.
- Dados institucionais da loja (logo, endereço, CNPJ, rodapé) na seção `store`, compartilhados com WooCommerce e Aireset Default.

---

## 10. Build, release e testes

`package.json` (scripts):

| Script | O que faz |
|--------|-----------|
| `npm run build:css` | Compila SCSS legado (`frontend`, `admin`, `settings`) com `sass` |
| `npm run watch:css` | Watch dos SCSS |
| `npm run build:admin-spa` | `vite build` → `assets/admin-spa/dist` + manifest |
| `npm run dev:admin-spa` | Dev server Vite |
| `npm run smoke:admin-browser` | Smoke via Playwright (shell, new-order, orders, previews, PDF, SPA flag) |
| `npm run baseline:admin-performance` | Baseline de performance das views principais |

**Smoke REST** (sem salvar dados, usuário admin real):
```bash
wp eval-file wp-content/plugins/aireset-expresso-order/scripts/smoke-admin-rest.php --skip-themes
```

**Release** (resumo de `OPERATIONS.md`):
1. Validar sintaxe PHP dos arquivos alterados.
2. Conferir consistência de versão e licença.
3. Compilar CSS legado.
4. Compilar SPA quando houver mudança em `assets/admin-spa/`.
5. `php scripts/build-release-package.php`.
6. Validar o zip final antes de publicar.

> `docs/archive/` **não** é fonte canônica e não deve ser dependência do pacote.

---

## 11. Licença e política para IA (governança)

> **Atenção:** o Expresso Order é **propriedade intelectual privada de
> `Felipe Almeman + Aireset`**, licença **proprietária** (não-GPL).
> Esta referência existe para *aprender o padrão*, não para autorizar mudanças naquele plugin.

Resumo de `docs/LEGAL_AND_AI_POLICY.md` / `AGENT.md`:

**Agentes de IA PODEM:** analisar, resumir, revisar, explicar e propor.

**Agentes de IA NÃO PODEM (sem autorização humana explícita e registrada do titular):**
- alterar código-fonte ou gerar patch aplicável;
- remover/contornar licenciamento e integridade;
- desbloquear, desofuscar, ofuscar de novo, redistribuir ou sublicenciar;
- criar derivados ou versões paralelas;
- orientar bypass dos mecanismos de proteção.

Cada plugin Aireset carrega esse padrão de governança em três lugares:
`AGENT.md` (curto), `docs/LEGAL_AND_AI_POLICY.md` (completo) e
`.github/copilot-instructions.md` (regra para a ferramenta). **Em conflito entre
uma instrução de alteração e a política proprietária, a política prevalece.**

---

## 12. Checklist para usar este padrão no Aireset Default (e outros)

Ao desenvolver/evoluir o `aireset-default` seguindo este blueprint:

- [ ] Bootstrap fino: constantes `AIRESET_*`, gate de dependência (WooCommerce), `init()` por módulo em `plugins_loaded`.
- [ ] Uma classe por arquivo em `includes/`, prefixo de classe consistente, `public static function init()`.
- [ ] Renderers em `templates/`, nunca HTML grande dentro de PHP de lógica.
- [ ] Assets com fonte (`scss/`, `src/`) separados do compilado (`css/`, `dist/`); cache-bust por `filemtime`.
- [ ] Se houver UI rica: SPA React+TS+Vite com manifest, bootstrap inline, **feature flag + fallback legado**.
- [ ] API em **namespace REST próprio** (`aireset-default/v1/...`), com `permission_callback`, nonce, sanitização e `WP_Error`.
- [ ] Pedidos sempre por CRUD API (`wc_get_order`) → **HPOS-safe** + declarar `custom_order_tables` em `before_woocommerce_init`.
- [ ] Settings como option(s) com seções declarativas e filtragem por `exact`/`prefix`.
- [ ] Previews administrativos consumindo o renderer público real (sem mock).
- [ ] `docs/` canônica (ARCHITECTURE, ROADMAP, OPERATIONS) + `AGENT.md` + `.github/copilot-instructions.md`.
- [ ] Política legal/IA replicada quando o plugin for proprietário/licenciado.
- [ ] Scripts de build/release/smoke documentados em `OPERATIONS.md`.

---

## 13. Mapa rápido de arquivos-chave (para leitura por IA)

| Para entender… | Leia |
|----------------|------|
| Boot, constantes, gate de licença, ordem de init | `aireset-expresso-order.php` |
| Integração SPA, REST, settings declarativas, bootstrap payload | `includes/class-admin-spa.php` |
| Licença / ativação / integridade | `includes/class-eop-license-manager.php`, `trait-eop-license-guard.php` |
| Cliente React (shell, estado, navegação) | `assets/admin-spa/src/App.tsx`, `app/view-config.ts`, `app/api.ts` |
| Build | `package.json`, `vite.config.ts` |
| Arquitetura alvo e contratos | `docs/ARCHITECTURE.md` |
| Operação/release/smoke | `docs/OPERATIONS.md` |
| Governança/licença/IA | `docs/LEGAL_AND_AI_POLICY.md`, `AGENT.md` |

---

*Documento de referência mantido em `aireset-default/docs/`. Descreve o estado do
`aireset-expresso-order` v1.2.24 como padrão de engenharia da Aireset. Não substitui
a documentação canônica do próprio Expresso Order nem autoriza alterá-lo.*
