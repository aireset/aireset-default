# Plano de Modernização — Aireset Default (Geral)

> **Status:** proposta aguardando aprovação · **Plugin:** `aireset-default` v1.3.10
> **Referência de padrão:** [`docs/REFERENCIA-EXPRESSO-ORDER.md`](./REFERENCIA-EXPRESSO-ORDER.md)
> **Objetivo:** modernizar o plugin seguindo o blueprint do Expresso Order
> (bootstrap fino, PSR-4, loader central, REST namespace, admin SPA, settings
> declarativas, docs canônica) **sem alterar comportamento** e **sem tocar no
> licenciamento**.

---

## 1. Princípios inegociáveis

1. **Comportamento preservado.** Cada fase é refatoração: mesmas options, mesmos
   hooks, mesmos slugs, mesmo resultado visível. Nada de feature nova "de
   carona" numa fase de refactor.
2. **Licenciamento intocável** (conforme `AGENT.md`):
   - Não simplificar/remover `class-aireset-license-base.php`, `class-license.php`, `trait-aireset-license-guard.php`.
   - Manter o **license gate** antes de instanciar o plugin.
   - Manter o trait `Aireset_License_Guard` em `Admin_Options`, `Ajax`, `Helpers`.
   - **`OPT_PREFIX` = `Aireset-Geral` não muda.** Product ID `1`, Encryption Key `29384224C655851C`.
3. **Reversível.** Tudo sob controle de versão (o plugin **não tem git hoje** — Fase 0 resolve).
4. **Validação a cada fase.** `php -l` em tudo + checklist funcional no admin antes de seguir.

---

## 2. Diagnóstico do estado atual (resumo)

### Carregamento (encadeado e frágil)
```
aireset-default.php
  ├─ require vendor/autoload.php        (Composer — PSR-4 existe mas NÃO é usado)
  ├─ require class-admin-page.php
  ├─ LICENSE GATE  ──────────────────►  bloqueia sem licença válida
  └─ new Aireset_General_Plugin()
       └─ init() → include_functions()  (8 arquivos via filtro aireset_default_setup_includes)
            └─ class-init.php → new Init() (rodapé)
                 └─ actions.php → require + new de ~12 classes de domínio
```

### Problemas catalogados
| # | Problema | Onde |
|---|----------|------|
| P1 | ~200 linhas de código morto comentado (Freemius, Vue enqueue, menu, reset) | `aireset-default.php` |
| P2 | `error_log()` de debug em produção no update checker | `aireset-default.php` `setup_update_checker()` |
| P3 | Classe principal mistura updater + changelog + listagem GitHub + log + debug helpers | `aireset-default.php` |
| P4 | PSR-4 configurado (`Aireset\Default\` → `includes/`) mas **inutilizado** (arquivos `class-x.php`, não `X.php`) | `composer.json` + todos os includes |
| P5 | Instanciação no rodapé dos arquivos (`new Init()`, `new Ajax()`, `new Admin_Options()`, `new Autoloader()`) | vários |
| P6 | `class_alias` hacks (`Aireset\Default\Init\Init` etc.) | rodapé de init/ajax/admin-options |
| P7 | Duas convenções: `includes/classes/*` (plano) vs `includes/<domínio>/*` (novo) | toda a árvore |
| P8 | ~200 linhas de config de fontes morta (copiada do flexify-checkout) | `class-init.php` |
| P9 | Text-domains errados: `hubgo-shipping-management-wc`, `aireset-default-for-woocommerce` | vários |
| P10 | `is_plugin_active('woocommerce/woocommerce.php')` em vez de `class_exists('WooCommerce')` | bootstrap, admin-options |
| P11 | Arquivo órfão `class-compat-autoloader.php` (busca pasta inexistente, nunca carregado) | includes/classes |
| P12 | Sem build front-end; admin é PHP nav-tab puro | — |

### Inventário de módulos de domínio (o que o plugin faz)
- **Frete:** `shipping/class-shipping-calculator.php`, `classes/shipping/management-*.php`, `classes/class-cep-manager.php`
- **Pedidos:** `orders/class-order-customer.php`, `orders/class-order-columns.php`, `orders/class-order-status.php` (desativado)
- **Checkout:** `checkout/class-guest-payment.php`, `checkout/class-meta-checkout-handler.php`
- **Carrinho:** `cart/class-cart-message-manager.php`
- **Frontend:** `frontend/class-viewport-manager.php`, `images/class-image-sizes.php`
- **Integrações:** `integrations/class-yith-search-manager.php`, Elementor Forms (`class-elementor-form-input-*`)
- **Elementor Dynamic Tags:** `elementor-dynamic-tags/*` (text, url, color, file, gallery, image, number)
- **Permalinks:** `permalinks/class-permalink-manager.php`
- **Shortcodes:** `shortcodes/class-whatsapp-shortcode.php`
- **Campos:** `classes/class-custom-fields.php`, `classes/class-conditions.php`, `classes/class-admin-fields.php`
- **Admin/Infra:** `classes/class-admin-options.php`, `class-admin-page.php`, `class-assets.php`, `class-ajax.php`, `class-helpers.php`, `class-logger.php`, `admin/settings.php`, `admin/tabs_aireset/*`

### Modelo de configuração atual
- Option única **`aireset_default_settings`** (array) — maioria das settings.
- Options **avulsas** para "Dados da Empresa" (`cep`, `endereco`, `endereco_linha_2`, `cidade`, `estado`, `pais`, …) que **sincronizam** com `woocommerce_store_*`.
- Salvamento via AJAX `wp_ajax_aireset_default_admin_ajax_save_options` (nonce `aireset_default_save_options`).
- Abas ativas: **business, general (options), frete, docs, about, license**. Abas comentadas: texts, fields, conditions, integrations, design.

---

## 3. Arquitetura alvo (mapeada do blueprint Expresso Order)

```
aireset-default/
├── aireset-default.php          # Bootstrap FINO: constantes, gate de licença, dependency guard, Plugin::boot()
├── composer.json                # PSR-4 corrigido + dump-autoload
├── package.json / vite.config.ts/ tsconfig*.json   # build do admin SPA
├── src/                         # PSR-4 Aireset\Default\  (classes PascalCase)
│   ├── Plugin.php               # Loader central: regista e inicializa módulos (substitui new no rodapé)
│   ├── Support/                 # Helpers, Logger, Settings (getter/setter da option)
│   ├── Admin/                   # AdminPage, AdminSpa (REST + bootstrap), Notices, Assets, Ajax (fallback)
│   ├── Settings/                # Definição declarativa de seções (store, general, frete, …)
│   ├── Rest/                    # Controller REST  aireset-default/v1/admin
│   ├── Shipping/ Orders/ Checkout/ Cart/ Frontend/ Integrations/ Elementor/ Permalinks/ Shortcodes/ Fields/
│   └── License/                 # (mantém os 3 arquivos de licença — apenas movidos/renomeados com cuidado)
├── assets/
│   ├── scss/ → css/             # estilos admin/front (sass)
│   └── admin-spa/{src,dist}/    # React + TS + Vite (feature flag + fallback legado)
├── templates/                   # renderers PHP (admin legado continua como fallback)
├── docs/                        # ARCHITECTURE, ROADMAP, OPERATIONS, LEGAL_AND_AI_POLICY, este plano
└── languages/
```

**Contratos REST alvo** (`aireset-default/v1/admin`):
- `GET /bootstrap` — usuário, licença, flags, branding, views, seções.
- `GET|POST /settings/{section}` — seções: `store`, `general`, `frete`, `docs`… (`exact`/`prefix` como no Expresso).
- Fallback: AJAX atual permanece até a SPA cobrir cada seção.

> **Nota sobre PSR-4:** mover de `class-x.php` para `X.php` é o ideal, mas é
> invasivo. A Fase 2 oferece **duas estratégias** (ver lá) para fazer isso sem
> big-bang e sem quebrar os `require_once` existentes durante a transição.

---

## 4. Fases

Cada fase é independente, validável e mergeável sozinha. Ordem recomendada, mas
podemos parar em qualquer ponto com o plugin 100% funcional.

### Fase 0 — Rede de segurança (pré-requisito, risco nulo)
**Objetivo:** poder reverter qualquer passo.
- `git init` no plugin + `.gitignore` (vendor/, node_modules/, dist/) + commit inicial "baseline 1.3.10".
- Backup zip do estado atual fora da pasta.
- Baseline funcional: lista de verificação manual (admin abre, salva settings, frete calcula, pedidos listam, checkout convidado, tags Elementor).
- `find . -name "*.php" -exec php -l {} \;` → garantir 0 erros antes de começar.

**Aceite:** repo versionado, lint limpo, checklist registrado.

---

### Fase 1 — Bootstrap limpo (risco baixo, alto valor imediato)
**Objetivo:** `aireset-default.php` enxuto e legível; zero mudança de comportamento.
- Remover **todo** código morto comentado (P1): bloco Freemius, `enqueue_assets_admin` comentado, menu admin comentado, reset handlers comentados, `add_admin_menu`/`admin_page_content` comentados.
- Remover `error_log()` de debug do update checker (P2); manter o try/catch e o updater funcionando.
- Extrair responsabilidades da classe principal (P3) para classes próprias:
  - `Support/UpdateChecker.php` (configuração do Plugin Update Checker).
  - `Admin/Notices.php` (os 3 métodos de notice: php version, wc version, wc deactivate).
  - Remover `listar_versoes_github()`, `highlight_array()`, `registrar_log()` da classe principal (mover p/ `Support/` ou remover se não usados — **verificar uso antes**).
- Trocar `is_plugin_active('woocommerce/woocommerce.php')` por `class_exists('WooCommerce')` no bootstrap (P10).
- Corrigir text-domains errados visíveis nos notices (P9) → `aireset-default`.
- **Preservar** o license gate exatamente onde está.

**Arquivos:** `aireset-default.php`, novos `src/Support/UpdateChecker.php`, `src/Admin/Notices.php`.
**Aceite:** plugin ativa, atualiza, mostra notices certos; `php -l` limpo; diff revisado.

---

### Fase 2 — Autoload PSR-4 real + loader central (risco médio)
**Objetivo:** acabar com `require_once` manual, `new` no rodapé e `class_alias`.

**Estratégia escolhida: "ponte" incremental (sem big-bang)**
1. Criar `src/Plugin.php` com método `boot()` que **regista** e instancia os módulos numa ordem explícita (substitui `actions.php` + os `new` espalhados).
2. Adicionar um **segundo path PSR-4** no `composer.json` apontando para `src/` (`Aireset\\Default\\` → [`src/`, `includes/`]) e rodar `composer dump-autoload`.
3. Mover módulos para `src/` **um domínio por vez**, renomeando para PascalCase PSR-4 (`Init.php`, `Helpers.php`, `Shipping/ShippingCalculator.php`…), removendo o `new` do rodapé e o `class_alias` correspondente — o `Plugin::boot()` passa a instanciar.
4. Cada classe movida: ajustar namespace se preciso, manter API pública/hooks idênticos.
5. Ao fim, remover `actions.php`, `class-compat-autoloader.php` (P11) e os `class_alias` (P6).

**Por que assim:** os arquivos antigos continuam carregando via `require_once` enquanto não migrados; o autoloader cobre os já migrados. Sem janela quebrada.

**Arquivos:** `composer.json`, `src/Plugin.php`, migração progressiva de `includes/**`.
**Aceite:** todos os módulos sobem via `Plugin::boot()`; nenhum `new` no rodapé nos arquivos migrados; lint limpo; checklist funcional ok.

---

### Fase 3 — Estrutura por domínio unificada (risco médio)
**Objetivo:** uma convenção só, por domínio (P7).
- Consolidar `includes/classes/*` espalhados nos domínios corretos sob `src/` (Shipping, Orders, Checkout, Cart, Frontend, Integrations, Elementor, Permalinks, Shortcodes, Fields, Support, Admin).
- Mover **com cuidado** os 3 arquivos de licença para `src/License/` **somente se** mantivermos os nomes de classe globais e o gate funcionando (validar `is_valid()` e o Guard após cada move). Se houver qualquer risco para a licença, **deixar onde está** — o blueprint permite.
- Remover `class-init.php` legado (a parte de default options vira `Settings/Defaults.php`; o getter `get_setting()` vira `Settings::get()`), mantendo retrocompatibilidade via wrapper se algum lugar usar `Init::get_setting`.
- Limpar a config de fontes morta (P8).

**Aceite:** árvore consistente por domínio; sem duplicação de convenção; tudo funcional.

---

### Fase 4 — Settings declarativas + REST namespace (risco médio)
**Objetivo:** modelar as settings como seções declarativas e expor REST, mantendo o AJAX como fallback.
- `Settings/Sections.php`: definição das seções com `source` (`settings` | `company` | `wc`) e `exact`/`prefix` (espelha `EOP_Admin_SPA::get_section_definitions()`).
  - Mapa inicial: `store` (Dados da Empresa → options avulsas + sync WC), `general` (chaves de `aireset_default_settings`), `frete` (prefixo `aireset_default_*` de frete/CEP).
- `Rest/AdminController.php`: registra `aireset-default/v1/admin` com `GET /bootstrap`, `GET|POST /settings/{section}` — `permission_callback` `manage_options`, nonce `wp_rest`, sanitização, `WP_Error`.
- O AJAX atual (`ajax_save_options_callback`) **permanece** intacto como fallback até a SPA assumir.

**Aceite:** REST responde bootstrap/settings com os mesmos dados do form atual; salvar via REST produz o mesmo efeito do AJAX (inclusive sync de endereço WC).

---

### Fase 5 — Admin SPA (React + TS + Vite) (risco médio, faseável)
**Objetivo:** UI moderna com feature flag e fallback legado, exatamente como o Expresso.
- Scaffold `assets/admin-spa/{src,dist}` + `package.json` + `vite.config.ts` + `tsconfig`.
- `Admin/AdminSpa.php`: lê manifest do Vite, enfileira bundle como módulo só na página do plugin, injeta `window.airesetDefaultSpaConfig` + bootstrap inline, feature flag `is_enabled()` (capability + bundle compilado + `?aireset_legacy=1` + option/filter).
- SPA consome `aireset-default/v1/admin`. Migrar abas na ordem: **business (store) → general → frete → docs/about (read-only)**.
- Admin legado (`admin/settings.php` + tabs) continua como fallback sob `?aireset_legacy=1`.

**Aceite:** com bundle, SPA é a tela padrão e salva via REST; sem bundle, cai limpo no legado; equivalência funcional comprovada aba a aba.

---

### Fase 6 — Build, docs e operação (risco baixo)
**Objetivo:** fechar o padrão do blueprint.
- `npm run build:css` (sass) + `npm run build:admin-spa` (vite) documentados.
- `docs/ARCHITECTURE.md`, `docs/OPERATIONS.md`, `docs/ROADMAP.md` (atual vs alvo, build/release, smoke).
- Atualizar `AGENT.md` (nova estrutura, mantendo a seção de licenciamento).
- Smoke mínimo (lint PHP + abertura do admin + salvar settings) e checklist de release.

**Aceite:** build reproduzível; docs canônica coerente; AGENT atualizado.

---

## 5. Mapa de migração de settings → seções REST

| Seção REST | Source | Chaves (amostra) | Sync |
|------------|--------|------------------|------|
| `store` | options avulsas | `cep`, `endereco`, `endereco_linha_2`, `cidade`, `estado`, `pais`, … | → `woocommerce_store_*` |
| `general` | `aireset_default_settings` | `aireset_default_fixed_viewport`, `aireset_default_masks`, `aireset_default_intl_tel_input`, `aireset_default_images`, … | — |
| `frete` | `aireset_default_settings` (prefixo) | `aireset_default_enable_shipping_calculator`, `aireset_default_hook_display_shipping_calculator`, `aireset_default_text_*_shipping_calc`, `aireset_default_shipping_calc_*` | — |

---

## 6. Riscos e mitigação

| Risco | Mitigação |
|-------|-----------|
| Quebrar licença ao mover arquivos | Não mover os 3 arquivos de licença na dúvida; testar `License::is_valid()` + Guard após cada passo |
| Ordem de carga muda comportamento | `Plugin::boot()` com ordem explícita espelhando a atual; migração um módulo por vez |
| `class_alias` removido cedo demais | Só remover o alias do módulo já migrado e com usos verificados via grep |
| Settings divergindo entre AJAX e REST | Manter AJAX como fallback; testes A/B salvando pelos dois caminhos |
| Sem git hoje | Fase 0 cria git + backup antes de qualquer edição |

---

## 7. Critérios de aceite globais

- [ ] `php -l` limpo em 100% dos arquivos após cada fase.
- [ ] Admin abre, salva settings (general/frete/empresa) com mesmo efeito de hoje.
- [ ] Sync de endereço da empresa → WooCommerce continua funcionando.
- [ ] Calculadora de frete, colunas/cliente de pedidos, checkout convidado, tags Elementor: sem regressão.
- [ ] License gate + Guard intactos; `OPT_PREFIX` inalterado.
- [ ] Nenhum `new` no rodapé / `class_alias` nos módulos migrados.
- [ ] Diff revisado por você antes do merge de cada fase.

---

## 8. Recomendação de execução

Sugiro começar por **Fase 0 + Fase 1** numa primeira rodada (rede de segurança +
bootstrap limpo) — risco baixo, ganho imediato e visível — e revisar o diff antes
de avançar para a Fase 2 (autoload/loader), que é onde o refactor fica
estrutural. As fases 4–5 (REST + SPA) são as maiores e podem ser quebradas em
sub-rodadas por seção.

> Aprovado o plano, eu inicio pela Fase 0 e te mostro o resultado antes de seguir.
