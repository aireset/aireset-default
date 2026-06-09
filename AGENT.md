# Aireset Default (Geral) — Agent Guide

> Contexto para agentes de IA que trabalham neste plugin.

## O que é

Plugin WordPress/WooCommerce **Aireset Geral** — cria e padroniza configurações para e-commerces (campos customizados, opções administrativas, calculadora de frete, integrações com Elementor Forms).

## Stack

- PHP 7.4+ (namespace `Aireset\Default`, classes herdam de `Init`)
- WooCommerce como dependência opcional (muitas features exigem WC)
- Composer autoloader + Plugin Update Checker (GitHub)
- jQuery no frontend admin

## Estrutura de Arquivos

```
aireset-default.php                          ← Entry point, defines, license gate, requires
includes/
  class-init.php                             ← Base class com configuração de includes
  functions.php                              ← Funções auxiliares
  actions.php                                ← Hooks e filtros
  classes/
    class-admin-options.php                  ← Menu admin, render de settings (usa Guard)
    class-ajax.php                           ← Handlers AJAX (usa Guard)
    class-helpers.php                        ← Funções utilitárias (usa Guard)
    class-admin-fields.php                   ← Campos administrativos
    class-assets.php                         ← Enqueue de assets
    class-cep-manager.php                    ← Gerenciamento de CEP
    class-conditions.php                     ← Lógica condicional
    class-custom-fields.php                  ← Campos customizados WC
    class-elementor-form-input-class.php     ← Elementor Forms: Input Classes
    class-elementor-form-input-custom-attributes.php ← Elementor Forms: Custom Attributes
    class-logger.php                         ← Logger
    class-aireset-license-base.php           ← SDK licenciamento (NÃO EDITAR sem necessidade)
    class-license.php                        ← Gerenciador de licença (UI + ativação)
    trait-aireset-license-guard.php          ← Validadores ocultos anti-pirataria
    aireset-license.css                      ← Estilo do formulário de licença
    shipping/                                ← Classes de frete
```

## Sistema de Licenciamento

> **Documentação completa global**: `~/.copilot/skills/aireset-licensing/licenciamento-aireset.md`
> Contém arquitetura, fluxo de ativação, camadas anti-pirataria, ionCube, build pipeline e troubleshooting.

### Arquivos envolvidos
1. `class-aireset-license-base.php` — SDK que fala com o servidor Elite Licenser
2. `class-license.php` — UI admin de ativação/desativação (namespace `Aireset\Default`)
3. `trait-aireset-license-guard.php` — Verificações ocultas em classes do plugin

### Credenciais
- **Product ID**: `1`
- **Encryption Key**: `29384224C655851C`
- **OPT_PREFIX**: `Aireset-Geral`

### License Gate
O plugin **bloqueia completamente** sem licença válida. O gate está em `aireset-default.php` após a classe principal, antes do `new Aireset_General_Plugin()`.

### Validadores Ocultos
3 classes usam `use Aireset_License_Guard`:
- `Admin_Options` → `_resolve_env_config()` no constructor
- `Ajax` → `_prefetch_module_state()` no constructor
- `Helpers` → `_validate_session_tokens()` no `get_details_fields()`

### Options no banco
| Option | Uso |
|--------|-----|
| `Aireset-Geral_lic_Key_s{hash}` | Chave de licença (hash do domínio) |
| `Aireset-Geral_lic_email` | Email de ativação |

## Convenções

- **Namespace**: `Aireset\Default`
- **Classe base**: `Aireset_License_Base` (sem namespace, global)
- **Text domain**: `aireset-default`
- **Menu pai**: `aireset` (registrado pelo checkout-aireset)
- **Capabilities**: `manage_options` para admin

## Regras ao Editar

1. **NUNCA simplifique ou remova** código dos arquivos de licenciamento
2. Mantenha o trait `Aireset_License_Guard` nas 3 classes indicadas
3. O license gate deve permanecer **antes** de `new Aireset_General_Plugin()`
4. Ao adicionar novas classes principais, considere incluir o trait de guard
5. O `OPT_PREFIX` (`Aireset-Geral`) **não pode mudar** após primeira ativação
6. Para distribuição, compile com ionCube os 3 arquivos de licenciamento (veja `~/.copilot/skills/aireset-licensing/licenciamento-aireset.md`)

## Testes

```bash
# Validação de sintaxe PHP em todos os arquivos
find . -name "*.php" -exec php -l {} \;
```
