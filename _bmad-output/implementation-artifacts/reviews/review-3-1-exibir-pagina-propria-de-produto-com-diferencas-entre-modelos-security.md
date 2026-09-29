# Revisao de Seguranca - Story 3.1

## Metadados

- Artefato auditado: `_bmad-output/implementation-artifacts/3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md`
- Escopo: story pronta para desenvolvimento, contrato publico de catalogo, pagina publica de produto, BFF Next.js e Laravel API.
- Data: 2026-09-22
- Auditor: Vex - Security Auditor
- Resultado do gate: Aprovado

## Evidencias Coletadas

- Arquivos lidos: story 3.1; sprint-status; project-context; epics; PRD/adendo; architecture spine; UX DESIGN/EXPERIENCE; `apps/web/src/app/(public)/produtos/[slug]/page.tsx`; `apps/web/src/bff/catalogApi.ts`; `apps/web/src/bff/catalogValidation.ts`; `apps/web/src/features/catalog-seo/detailQuery.ts`; `apps/web/src/features/catalog-seo/catalogMetadata.ts`; `apps/web/src/features/catalog-seo/catalogReads.ts`; `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php`; `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php`; migration de catalogo; seeder E2E; testes de catalogo/SEO; OpenAPI publico.
- Comandos executados:
  - `node scripts/scan-sast.mjs`: Semgrep executou 247 regras em 283 arquivos, 0 achados.
  - `node scripts/scan-secrets.mjs`: 82.63 MB escaneados, no leaks found.
  - `npm audit --audit-level=moderate` em `apps/web`: 0 vulnerabilidades.
  - `composer audit --locked` em `apps/api`: nenhum advisory.
  - `git diff --check`: sem problemas.
- Ferramentas nao executadas: testes completos de implementacao nao executados porque a story ainda e especificacao, sem codigo de produto implementado nesta etapa.
- Referencias consultadas: OWASP/STRIDE via skill; docs locais Next em `apps/web/node_modules/next/dist/docs/`; Next official `generateMetadata` e `Image`; Laravel docs oficiais de JSON Resources; artefatos BMad do projeto.

## Threat Model STRIDE

| Categoria | Superficie | Risco | Mitigacao existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | `return_to`, host/canonical, crawlers | Origem ou retorno malicioso contaminar metadata/link de volta. | Story exige `detailQuery`, `SITE_URL`/metadata existente e ausencia de `return_to` em canonical/OG/JSON-LD. | Nenhum gap bloqueante. |
| Tampering | Slug, payload Laravel, modelos, galeria | Payload malformado ou texto hostil alterar HTML/JSON-LD. | Schema BFF estrito, `additionalProperties: false`, limites de tamanho, slug validado, serializador existente. | Nenhum gap bloqueante. |
| Repudiation | Mudancas de contrato/API | Campo publico novo sem rastreabilidade ou teste. | Story exige OpenAPI, testes HTTP exatos, changelog e gates. | Nenhum gap bloqueante. |
| Information Disclosure | Resource publico, HTML, metadata, logs | Vazar campos admin, direitos, storage privado ou personagem protegido. | Resource allowlist, filtros de taxonomy, testes negativos e proibicao explicita de campos privados. | Nenhum gap bloqueante. |
| Denial of Service | Galeria/modelos/query de detalhe | N+1, payload excessivo ou imagem arbitraria. | Limite de 8 imagens/12 modelos, query budget, statement timeout e validacao de path. | Nenhum gap bloqueante. |
| Elevation of Privilege | BFF/UI de produto | BFF passar a decidir preco, carrinho, miniatura ou regra de configuracao. | Story delimita dominio Laravel e proibe calculo/checkout client-owned. | Nenhum gap bloqueante. |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Medio Risco

Nenhum achado confirmado.

### Baixo Risco

Nenhum achado confirmado.

## Decisao do Gate

- Decisao: Aprovado.
- Condicoes antes de avancar: manter os controles escritos na story durante a implementacao; reexecutar bmad-review-security sobre o diff/codigo antes de concluir a implementacao.
- Owner: Dev da story 3.1.
- Prazo / proximo checkpoint: antes de mover a implementacao para `done` ou aceitar code review.

## Revalidacao Documental Pos-Rerun Adversarial - 2026-09-22

- Escopo: alteracoes documentais aplicadas apos `review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-adversarial-rerun-2026-09-22.md`.
- Arquivos alterados: story 3.1 e relatorio adversarial de rerun; sem codigo de aplicacao, lockfile, contrato ou migration implementados nesta rodada.
- Resultado: Aprovado com ressalva operacional.
- Risco de seguranca confirmado: nenhum alto ou medio risco novo. As correcoes reduziram ambiguidade sobre fonte de dados, CTA, query `modelo`, allowlist, galeria e testes.
- Evidencia executada: `git diff --check` sem erro de whitespace; apenas aviso Git de normalizacao LF/CRLF no `sprint-status.yaml`.
- Ferramenta nao executada com sucesso: `node scripts/scan-secrets.mjs` falhou antes do scan porque o Docker Desktop/Linux engine nao estava disponivel (`failed to connect to the docker API at npipe:////./pipe/dockerDesktopLinuxEngine`). O secret scan completo tinha passado antes das correcoes do rerun, mas esta tentativa posterior nao conta como aprovacao de scanner.
- Condicao: na implementacao, reexecutar `node scripts/scan-secrets.mjs`, `node scripts/scan-sast.mjs` e o gate de seguranca sobre o diff de codigo em ambiente com Docker disponivel.
