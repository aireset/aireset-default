# Agents Configuration - Aireset Default Plugin

Este arquivo define agentes especializados para trabalhar com diferentes áreas do plugin.

---

## PHP Code Agent

**Especialidade:** Código PHP do WordPress/WooCommerce

### Instruções
- Seguir padrões de nomenclatura do WordPress
- Usar namespaces `Aireset\Default\*`
- Sempre sanitizar inputs com `sanitize_*` functions
- Escapar outputs com `esc_*` functions
- Usar hooks do WordPress para extensibilidade
- Verificar nonces em requisições AJAX

### Arquivos Principais
- `includes/classes/*.php`
- `includes/class-init.php`
- `includes/actions.php`
- `includes/functions.php`

---

## Admin UI Agent

**Especialidade:** Interface administrativa do plugin

### Instruções
- Usar Bootstrap 5 para estilos
- Seguir padrões de UI do WordPress admin
- Implementar salvamento via AJAX
- Usar toasts para feedback de ações
- Campos devem ter labels descritivas

### Arquivos Principais
- `includes/admin/tabs_aireset/*.php`
- `includes/classes/class-admin-options.php`
- `assets/admin/js/aireset-default-admin-scripts.js`
- `assets/admin/css/aireset-default-admin-styles.css`

---

## Elementor Integration Agent

**Especialidade:** Integração com Elementor

### Instruções
- Estender classes do Elementor corretamente
- Registrar widgets na action `elementor/widgets/register`
- Registrar dynamic tags na action `elementor/dynamic_tags/register`
- Usar categorias personalizadas `aireset-default`
- Implementar `register_controls()` para configurações

### Arquivos Principais
- `includes/elementor-dynamic-tags/elementor.php`
- `includes/elementor-dynamic-tags/*.php`
- `includes/elementor-widgets/*.php`

---

## WooCommerce Agent

**Especialidade:** Integrações com WooCommerce

### Instruções
- Verificar se WooCommerce está ativo antes de usar classes WC
- Usar HPOS-compatible code
- Hooks: `woocommerce_*` para extensões
- Usar `wc_get_order()`, `WC()->cart`, etc.

### Arquivos Principais
- `includes/cart/*.php`
- `includes/checkout/*.php`
- `includes/orders/*.php`
- `includes/shipping/*.php`

---

## Frontend Agent

**Especialidade:** Código frontend (CSS/JS)

### Instruções
- jQuery como dependência principal
- Localizar scripts com `wp_localize_script()`
- CSS em arquivos separados por módulo
- Usar classes prefixadas `aireset-*`

### Arquivos Principais
- `assets/front/js/*.js`
- `assets/front/css/*.css`

---

## Testing Agent

**Especialidade:** Testes e validação

### Instruções
- Verificar sintaxe PHP: `php -l arquivo.php`
- Testar em ambiente WordPress/WooCommerce
- Verificar compatibilidade cross-browser
- Testar responsividade mobile

### Comandos
```bash
# Verificar sintaxe de todos os arquivos PHP
find . -name "*.php" -exec php -l {} \;

# Verificar arquivos específicos
php -l includes/classes/class-ajax.php
```

---

## Documentation Agent

**Especialidade:** Documentação do código

### Instruções
- Usar PHPDoc para classes e métodos
- Documentar hooks e filters
- Manter README.md atualizado
- Registrar changelogs

### Formato PHPDoc
```php
/**
 * Descrição breve da função.
 *
 * Descrição mais detalhada se necessário.
 *
 * @since 1.0.0
 * @version 1.3.8
 * @param string $param Descrição do parâmetro.
 * @return bool Descrição do retorno.
 */
```

---

## Configurações por Tarefa

### Corrigir Bug
1. Identificar arquivo afetado
2. Criar backup mental do estado atual
3. Fazer correção mínima necessária
4. Verificar sintaxe
5. Testar funcionalidade

### Adicionar Feature
1. Identificar melhor local para código
2. Seguir padrões existentes
3. Adicionar configuração se necessário
4. Documentar mudanças
5. Atualizar changelog

### Refatorar Código
1. Entender funcionalidade atual
2. Identificar code smells
3. Aplicar melhorias incrementais
4. Manter compatibilidade retroativa
5. Atualizar documentação
