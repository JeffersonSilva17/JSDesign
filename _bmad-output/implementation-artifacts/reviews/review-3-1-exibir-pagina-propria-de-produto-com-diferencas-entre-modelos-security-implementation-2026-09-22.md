# Revisão Adversarial de Segurança - Implementação Story 3.1

## Metadados

- Artefato auditado: diff de implementação da story 3.1
- Escopo: contrato público de detalhe de produto, API Laravel, BFF Next.js, página pública `/produtos/[slug]`, testes e gates executáveis no ambiente atual.
- Data: 2026-09-22
- Auditor: Vex - Security Auditor
- Resultado do gate: Bloqueado

## Evidências Coletadas

- Arquivos lidos: `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php`, `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php`, `apps/api/database/migrations/2026_09_22_000001_create_catalog_product_models_table.php`, `apps/web/src/bff/catalogValidation.ts`, `apps/web/src/features/catalog-seo/detailQuery.ts`, `apps/web/src/app/(public)/produtos/[slug]/page.tsx`, `packages/contracts/catalog-public-v1.openapi.yaml`, story 3.1.
- Comandos executados: `npm run test:bff`, `npm run test:seo`, `npm run lint`, `npm run typecheck`, `npm run build` com `SITE_URL` explícito, `npm audit --audit-level=moderate`, `composer audit --locked`, `vendor/bin/pint --test`, `git diff --check`, `php -l` nos arquivos PHP alterados, tentativa filtrada de `php artisan test`, tentativa parcial de `composer test`, `docker ps`, `node scripts/scan-sast.mjs`, `node scripts/scan-secrets.mjs`.
- Ferramentas não executadas: SAST e secret scan versionados não executaram porque o Docker Desktop/Linux engine não estava disponível; testes Feature Laravel e Playwright ficaram bloqueados porque PostgreSQL `jsdesign_test` em `127.0.0.1:5432` recusou conexão.
- Referências consultadas: story 3.1, `AGENTS.md`, `project-context.md`, template da skill `bmad-review-security`.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Query `return_to`/`modelo` | Link externo ou host falso contaminando retorno/canonical | `safeReturnHref`, canonical sem query, `modelo` não entra em metadata | E2E não executado por falta de PostgreSQL |
| Tampering | Payload Laravel -> BFF | Campo extra/modelo inválido alterando HTML | `PublicCatalogProductResource` allowlist, `catalogValidation` fechado, OpenAPI `additionalProperties: false` | Testes HTTP de API não executados por falta de PostgreSQL |
| Repudiation | Mudança de contrato público | Sem rastreabilidade de campos novos | OpenAPI, testes unitários e relatório de segurança adicionados | Full regression pendente |
| Information Disclosure | API/HTML/metadata/JSON-LD | Vazamento de `storage_reference`, `variants_reference`, direitos ou evidências | Resource só expõe campos públicos, BFF rejeita campos extras, JSON-LD usa nome/descrição/url/imagem | SAST/secret scan não executados |
| Denial of Service | Galeria/modelos | Payload grande/N+1 | Galeria limitada a 8, modelos limitados a 12, query batch e timeout existente | Query budget Feature não executado por falta de PostgreSQL |
| Elevation of Privilege | CTA futuro `/carrinho` | BFF decidir preço/carrinho | CTA apenas link com slug/modelo validados; carrinho permanece placeholder | Fluxo E2E pendente |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Médio Risco

#### SEC-3-1-IMPL-001

- Item / Componente Afetado: gates versionados de segurança e regressão.
- Risco Detectado: SAST, secret scan, testes Feature Laravel e Playwright aplicáveis não foram concluídos no ambiente atual.
- Impacto para o Projeto: a implementação não possui evidência suficiente para avançar a story para `review`; riscos de vazamento/regressão podem permanecer sem detecção automatizada.
- Solução Recomendada: reexecutar `node scripts/scan-sast.mjs`, `node scripts/scan-secrets.mjs`, `composer test` e Playwright após iniciar Docker Desktop/Linux engine e PostgreSQL/Redis de teste.
- Evidência: `docker ps`, `node scripts/scan-sast.mjs` e `node scripts/scan-secrets.mjs` falharam com ausência de `dockerDesktopLinuxEngine`; `php artisan test --filter=test_detail_exposes_enriched_allowlist_gallery_and_models` falhou com `SQLSTATE[08006] connection refused` para `127.0.0.1:5432`.
- Status: Aberto

### Baixo Risco

Nenhum achado confirmado.

## Decisão do Gate

- Decisão: Bloqueado
- Condições antes de avançar: concluir os gates versionados de segurança, testes Laravel com PostgreSQL e Playwright aplicável; corrigir qualquer achado novo antes de marcar a story para review.
- Owner: time de desenvolvimento
- Prazo / próximo checkpoint: assim que Docker Desktop/Linux engine e banco de teste estiverem disponíveis.
