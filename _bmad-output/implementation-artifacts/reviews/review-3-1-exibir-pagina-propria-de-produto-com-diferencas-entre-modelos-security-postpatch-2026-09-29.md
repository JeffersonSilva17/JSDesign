# Revisão Adversarial de Segurança - Pós-correção Story 3.1

## Metadados

- Artefato auditado: diff final da implementação da story 3.1 após `bmad-code-review`.
- Escopo: contrato público de detalhe de produto, API Laravel, BFF Next.js, página `/produtos/[slug]`, SEO, Playwright, dependências e gates versionados.
- Data: 2026-09-29
- Auditor: Vex - Security Auditor
- Resultado do gate: Aprovado

## Evidências Coletadas

- Arquivos revisados: `PublicCatalogProductResource.php`, `PostgresPublicCatalogQuery.php`, migration `catalog_product_models`, `CatalogE2eSeeder.php`, `catalogApi.ts`, `catalogValidation.ts`, `detailQuery.ts`, `CatalogCard.tsx`, `CatalogFilters.tsx`, página `/produtos/[slug]`, OpenAPI público/admin, testes unitários e E2E.
- Comandos aprovados: `composer test`, `vendor/bin/pint --test`, `composer audit --locked --no-interaction`, `npm run test:bff`, `npm run test:seo`, `npm run lint`, `npm run typecheck`, `npm run build` com SEO off, `npm run build` com SEO on, `npm run test:e2e -- --workers=1 --reporter=line`, `npm run test:e2e -- tests/e2e/catalog-seo.spec.ts --workers=1 --reporter=line`, `npm audit --audit-level=moderate`, `node scripts/scan-sast.mjs`, `node scripts/scan-secrets.mjs`, `git diff --check`.
- Resultados relevantes: Laravel `145 passed, 1 skipped`; Playwright `70 passed`; SEO E2E `6 passed`; SEO unit `10 passed`; SAST `0 findings`; secret scan `no leaks found`; npm audit `0 vulnerabilities`; composer audit limpo após atualização de dependências.
- Limitação registrada: `CatalogSitemapBenchmarkTest` segue opt-in por `CATALOG_SITEMAP_BENCHMARK=1`; não bloqueia a story porque os testes funcionais de sitemap/query budget passaram.

## Threat Model STRIDE

| Categoria | Superfície | Risco revisado | Mitigação/evidência |
| --- | --- | --- | --- |
| Spoofing | `return_to`, host/canonical e query `modelo` | Retorno externo ou modelo forjado contaminando link, metadata ou CTA | Parser same-origin preservado; canonical/OG/JSON-LD sem query; E2E no-JS e SEO passaram. |
| Tampering | Payload Laravel, modelos, galeria | Campo extra ou modelo inválido alterando HTML/API | API resource com allowlist aninhada; BFF rejeita campos extras/default inválido; testes HTTP/unit cobrem duplicados, defaults e campos privados. |
| Repudiation | Mudança de contrato público | Contrato e implementação divergirem sem rastreabilidade | OpenAPI público/admin atualizados; story e relatório registram evidências e comandos. |
| Information Disclosure | API, HTML, metadata, JSON-LD | Vazamento de `storage_reference`, direitos, notas admin ou caminhos privados | Resource filtra galeria/modelos com `PublicImagePath`; testes de adapter alternativo e HTML/SEO passaram; secret scan limpo. |
| Denial of Service | Galeria/modelos/prefetch | Payload grande, N+1 ou prefetch competindo com navegação | Galeria limitada a 8, modelos a 12, query budget constante; prefetch de filtros/cards desativado; Core Web Vitals passou. |
| Elevation of Privilege | CTA futuro `/carrinho` e BFF | UI decidir preço, estoque ou carrinho real | CTA é handoff validado com slug/modelo; placeholder não cria item/subtotal; testes E2E cobrem modalidade e CTA. |

## Achados

### Alto Risco

Nenhum achado aberto ou confirmado.

### Médio Risco

#### SEC-3-1-POST-001

- Item / Componente Afetado: `PublicCatalogProductResource` e validação BFF do detalhe.
- Risco Detectado: adaptadores alternativos poderiam entregar campos privados aninhados em `gallery`/`models.image` se o resource propagasse arrays sem allowlist explícita.
- Impacto para o Projeto: exposição de `storage_reference`, notas administrativas ou paths internos em API/HTML/metadata se uma fonte futura retornasse campos a mais.
- Solução Aplicada: resource passou a revalidar imagens aninhadas com `PublicImagePath` e retornar apenas `url`/`alt_text`; modelos retornam exatamente `key`, `label`, `difference`, `is_default`, `image`.
- Evidência: testes `detail resource strips nested private fields from alternative adapter`, unit BFF para campos extras e SAST final com 0 achados.
- Status: Resolvido

#### SEC-3-1-POST-002

- Item / Componente Afetado: gates de segurança/regressão bloqueados em 2026-09-22 por Docker/PostgreSQL indisponível.
- Risco Detectado: ausência de evidência automatizada para regressões de API, Playwright, SAST e segredos.
- Impacto para o Projeto: story não podia ser promovida porque riscos de vazamento/regressão não estavam medidos.
- Solução Aplicada: Docker/PostgreSQL/Redis iniciados, migrations/seeds executados e todos os gates obrigatórios concluídos.
- Evidência: `composer test` 145 passed/1 skipped, Playwright 70 passed, SAST 0 findings, secret scan sem vazamentos.
- Status: Resolvido

### Baixo Risco

#### SEC-3-1-POST-003

- Item / Componente Afetado: dependências PHP `laravel/framework` e `league/flysystem`.
- Risco Detectado: `composer audit` encontrou advisories baixos publicados em 2026-09-29.
- Impacto para o Projeto: manter versões afetadas enfraqueceria o gate de dependências, mesmo sem exploração confirmada no fluxo da story.
- Solução Aplicada: `composer update laravel/framework league/flysystem league/flysystem-local --with-all-dependencies --no-interaction`; Laravel atualizado para `13.34.0` e Flysystem para `3.36.0`.
- Evidência: `composer audit --locked --no-interaction` retornou sem advisories; `composer test` passou após o update.
- Status: Resolvido

## Decisão do Gate

- Decisão: Aprovado.
- Condições antes de avançar: nenhuma pendência alta ou média aberta.
- Observações: a governança em `AGENTS.md` foi auditada como política do repositório; este relatório não certifica enforcement do cliente/IDE fora dos comandos executados.
- Owner: time de desenvolvimento.
- Próximo checkpoint: próxima story do Épico 3 deve herdar os limites de modelo/galeria, o contrato de CTA como handoff e a regra de não calcular preço/carrinho no BFF.
