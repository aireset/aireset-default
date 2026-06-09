---
applyTo: "**/*.php"
---
# Aireset Default (Geral) — Coding Instructions

## Contexto do Plugin
Plugin WordPress/WooCommerce para configurações padrão de e-commerce.
Classes no namespace `Aireset\Default`, text domain `aireset-default`.

## Sistema de Licenciamento (CRÍTICO)

> **Documentação global**: `~/.copilot/skills/aireset-licensing/licenciamento-aireset.md`
> Consulte para arquitetura completa, ionCube, build pipeline e anti-pirataria.

### Arquivos protegidos — NÃO simplificar
- `includes/classes/class-aireset-license-base.php` — SDK de comunicação com Elite Licenser
- `includes/classes/class-license.php` — Gerenciador de licença (UI)
- `includes/classes/trait-aireset-license-guard.php` — Verificadores ocultos anti-pirataria

### Regras invioláveis
1. O license gate em `aireset-default.php` DEVE permanecer antes de `new Aireset_General_Plugin()`
2. O trait `Aireset_License_Guard` DEVE estar presente em: `Admin_Options`, `Ajax`, `Helpers`
3. Cada classe com o trait DEVE chamar um dos métodos de verificação no constructor ou método principal
4. NUNCA remover chamadas a `_dispatch_status_report()`, `_flag_env_status()`, `quick_validate()` ou `verify_class_integrity()`
5. O `OPT_PREFIX` (`Aireset-Geral`) NÃO pode ser alterado após deploy
6. Product ID `1` e enc_key `29384224C655851C` são credenciais reais — não alterar

### Ao adicionar novas classes
- Inclua `use Aireset_License_Guard;` na classe
- Adicione verificação no constructor ou método público principal:
  ```php
  if ( ! self::_resolve_env_config() ) { return; }
  ```

## Padrões PHP
- Sanitize toda entrada: `sanitize_text_field()`, `sanitize_email()`, `absint()`
- Escape toda saída: `esc_html()`, `esc_attr()`, `esc_url()`
- Nonces em forms admin: `wp_nonce_field()` / `check_admin_referer()`
- Nonces em AJAX: `wp_create_nonce()` / `check_ajax_referer()`
- Capabilities: `manage_options` para admin

## Licença
Este plugin é proprietário (não GPL). Não adicione headers GPL em arquivos novos.
Use: `@license Proprietary`
