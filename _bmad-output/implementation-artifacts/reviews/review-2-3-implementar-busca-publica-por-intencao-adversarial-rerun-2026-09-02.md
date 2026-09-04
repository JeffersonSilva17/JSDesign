# Revisao adversarial - Story 2.3 em review

Content type: story implementation review, uncommitted workspace diff, API/BFF/UI/tests/contracts.

## Resolution Log

- **Status:** resolved by implementation updates on 2026-09-02.
- **Closure verification:** `composer test` passed with 128 tests / 548 assertions; `vendor\bin\pint --test`, `composer audit --locked`, `npm run test:bff`, `npm run typecheck`, `npm run build`, `npm run lint`, `npm audit --audit-level=moderate`, `CI=1 npm run test:e2e -- --workers=1`, and `git diff --check` passed. `semgrep`, `gitleaks`, and `trivy` were not installed locally and remain recorded as tooling gaps, not approvals.
- **ADV-2.3-RR-001:** `invitation` is now emitted only when `total === 0`; a feature test proves a published matching invitation product returns `generic`.
- **ADV-2.3-RR-002:** search now preserves the requested page even for zero-result responses, and the UI treats any `current_page > last_page` as out of range.
- **ADV-2.3-RR-003:** `non_fuzzy` candidates are deduplicated per product and limited by the configured candidate limit before final ranking; the endpoint SQL assertion now checks this branch.
- **ADV-2.3-RR-004:** the PostgreSQL normalizer now trims and collapses whitespace, including NBSP, under the versioned function used by the search indexes; a feature test covers catalog values with irregular spacing.
- **ADV-2.3-RR-005:** suggestions are now ordered by relevance before label and covered by a competing-suggestion feature test.
- **ADV-2.3-RR-006:** `/buscar` initial state now renders editorial occasion suggestions in addition to category chips; E2E covers the `Aniversário` chip.
- **ADV-2.3-RR-007:** exact groups now sort by best item rank/score, then category label and slug; a feature test covers label/slug tie-break.
- **ADV-2.3-RR-008:** OpenAPI now documents the narrower allowlist for suggestion and handoff hrefs; BFF unit tests assert the contract patterns.
- **ADV-2.3-RR-009:** public error responses now reference closed schemas with concrete properties and `additionalProperties: false`.
- **ADV-2.3-RR-010:** the search normalizer was renamed to `catalog_public_search_normalize_v1` and no longer uses `CREATE OR REPLACE FUNCTION`.
- **ADV-2.3-RR-011:** the migration comments the `unaccent` operational dependency and requires `REINDEX` after dictionary changes; the feature test checks the comment.
- **ADV-2.3-RR-012:** fuzzy behavior remains covered against the real PostgreSQL endpoint; the PHP ranker remains a unit-only deterministic helper and is not used as the production oracle.
- **ADV-2.3-RR-013:** invalid `/buscar` requests no longer echo the rejected raw query into the search input.
- **ADV-2.3-RR-014:** the E2E referrer test now derives the first-party origin dynamically from the running page/request.

## Findings

- **ADV-2.3-RR-001**
  - **Severity:** High
  - **Finding:** A intencao `invitation` e ativada para qualquer busca que contenha vocabulario de convite, mesmo quando existem resultados publicados. Isso viola a condicao da AC6, que limita a experiencia de convite ao caso "tema de convite sem exemplar publicado", e pode misturar CTA de handoff com resultados validos.
  - **Evidence:** `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:314-328`; `apps/web/src/features/catalog-search/SearchResults.tsx:16-25`; `apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php:132-139` cobre somente total zero.
  - **Recommendation:** Condicionar `invitation` ao resultado sem exemplar publicado, ou explicitar no contrato/testes quando ele pode coexistir com resultados.

- **ADV-2.3-RR-002**
  - **Severity:** High
  - **Finding:** Pagina fora do intervalo e confundida com busca vazia quando `total = 0`. O backend força `current_page = 1` para total zero e o frontend so mostra estado fora do intervalo quando `total > 0`, entao `/buscar?q=semresultado&page=99` cai no estado vazio, apesar da AC3 exigir que pagina acima de `last_page` nao seja confundida com sem resultado.
  - **Evidence:** `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:137-145`; `apps/web/src/features/catalog-search/SearchResults.tsx:12-19`; teste em `apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php:104-106` cobre pagina fora do intervalo apenas com `total > 0`.
  - **Recommendation:** Preservar a pagina solicitada tambem em resultado zero ou definir explicitamente que total zero sempre tem pagina 1; alinhar API, UI e testes com a decisao.

- **ADV-2.3-RR-003**
  - **Severity:** High
  - **Finding:** O caminho `non_fuzzy` nao tem limite de candidatos e calcula `catalog_search_normalize` sobre todos os produtos publicados, categorias, modalidades e taxonomias antes de filtrar exato/prefixo. O teto configuravel so limita fuzzy, deixando o endpoint publico vulneravel a custo linear grande para buscas comuns ou prefixos amplos.
  - **Evidence:** `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:182-214`; limite fuzzy somente em `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:249-251`; teste de budget em `apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php:180-193` conta queries, nao trabalho de linhas/planos do `non_fuzzy`.
  - **Recommendation:** Criar estrategia indexada/limitada para exato e prefixo, medir plano real nesses ramos e adicionar teste com cardinalidade representativa para nome, categoria e taxonomia.

- **ADV-2.3-RR-004**
  - **Severity:** Medium
  - **Finding:** A normalizacao do termo e a normalizacao indexada do banco nao sao equivalentes. PHP aplica NFKC, trim e colapso de espacos; a funcao SQL so aplica `lower(public.unaccent(...))`. Valores editoriais com espacos duplicados, NBSP ou caracteres de compatibilidade podem falhar como `exact`/`prefix` ou ranquear diferente do contrato.
  - **Evidence:** `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchCriteria.php:32-58`; `apps/api/database/migrations/2026_08_29_000001_add_public_catalog_search_indexes.php:13-19`.
  - **Recommendation:** Tornar a funcao SQL semanticamente equivalente ao normalizador de entrada, ou reduzir o contrato para a normalizacao realmente implementada; cobrir fixtures com espacos em dados de catalogo, nao so na query.

- **ADV-2.3-RR-005**
  - **Severity:** Medium
  - **Finding:** As sugestoes nao sao ranqueadas por relevancia para a query. A query ordena primeiro por label normalizado e so depois por similaridade dentro do mesmo label; em seguida o PHP reordena alfabeticamente. Na pratica, as seis primeiras sugestoes alfabeticas podem vencer sugestoes mais proximas do termo.
  - **Evidence:** `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:297-309`; teste em `apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php:109-129` valida origem/limite, mas nao relevancia.
  - **Recommendation:** Ordenar por score de similaridade/prefixo antes de label, aplicar limiar minimo se necessario e testar com pelo menos tres sugestoes competindo.

- **ADV-2.3-RR-006**
  - **Severity:** Medium
  - **Finding:** A pagina inicial de `/buscar` entrega formulario e categorias, mas nao entrega sugestoes editoriais publicas de tema/ocasiao como a AC5 pede. A UI inicial consome `fetchCatalogFacets`, mas renderiza apenas `facets.categories`.
  - **Evidence:** `apps/web/src/app/(public)/buscar/page.tsx:29-39`; `apps/web/tests/e2e/catalog-search.spec.ts:9-17`.
  - **Recommendation:** Renderizar tambem chips editoriais derivados de ocasioes/temas ou ajustar a AC para dizer que categorias sao suficientes.

- **ADV-2.3-RR-007**
  - **Severity:** Medium
  - **Finding:** A ordenacao secundaria de grupos exatos por `label/slug` nao esta implementada. Os grupos sao montados por primeira aparicao na pagina, e a ordenacao SQL usa `id DESC` como desempate global, nao categoria `label/slug`.
  - **Evidence:** `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:117-135`; `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:270-278`; AC3 no story exige melhor item e depois `label/slug`.
  - **Recommendation:** Materializar a ordenacao de grupos explicitamente e adicionar teste com dois grupos empatados no melhor item.

- **ADV-2.3-RR-008**
  - **Severity:** Medium
  - **Finding:** O OpenAPI documenta `href` de sugestao e handoff apenas como caminho relativo `^/(?!/)`, mas nao expressa a allowlist real de `/buscar?q=...` e `/produtos?modality=digital_personalized#busca=...`. O BFF rejeita varias formas inseguras, mas consumidores gerados pelo contrato receberiam permissao mais ampla do que a fronteira aceita.
  - **Evidence:** `packages/contracts/catalog-public-v1.openapi.yaml:145-156`; BFF mais estrito em `apps/web/src/bff/catalogValidation.ts:147-164`.
  - **Recommendation:** Fechar o schema com padroes/path permitidos ou descricoes normativas testadas por contrato.

- **ADV-2.3-RR-009**
  - **Severity:** Medium
  - **Finding:** Schemas de erro no OpenAPI nao sao envelopes fechados. Eles declaram apenas campos obrigatorios, sem `properties` completas e sem `additionalProperties: false`, apesar da story exigir contratos publicos fechados e erros sanitizados.
  - **Evidence:** `packages/contracts/catalog-public-v1.openapi.yaml:203-207`.
  - **Recommendation:** Definir `message`, `errors` e `retry_after` com tipos concretos e `additionalProperties: false` para cada resposta publica.

- **ADV-2.3-RR-010**
  - **Severity:** Medium
  - **Finding:** A migration usa `CREATE OR REPLACE FUNCTION` e o rollback executa `DROP FUNCTION IF EXISTS`, o que pode sobrescrever/remover uma funcao preexistente com o mesmo nome sem prova de propriedade exclusiva. Isso contraria o cuidado exigido de remover somente objetos pertencentes a esta migration.
  - **Evidence:** `apps/api/database/migrations/2026_08_29_000001_add_public_catalog_search_indexes.php:13-19`; `apps/api/database/migrations/2026_08_29_000001_add_public_catalog_search_indexes.php:26-31`.
  - **Recommendation:** Usar nome versionado/namespaceado, falhar se a funcao ja existir com definicao inesperada, ou registrar ownership verificavel antes de substituir/remover.

- **ADV-2.3-RR-011**
  - **Severity:** Medium
  - **Finding:** A funcao `catalog_search_normalize` e marcada `IMMUTABLE` embora dependa do dicionario `unaccent`. O teste prova apenas que `provolatile = i`, nao que a imutabilidade seja semanticamente segura se regras de `unaccent` mudarem; isso pode deixar indices inconsistentes.
  - **Evidence:** `apps/api/database/migrations/2026_08_29_000001_add_public_catalog_search_indexes.php:13-19`; `apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php:215-249`.
  - **Recommendation:** Documentar a restricao operacional de nao alterar o dicionario sem `REINDEX`, ou usar estrategia de coluna normalizada/generation que torne a dependencia explicita.

- **ADV-2.3-RR-012**
  - **Severity:** Low
  - **Finding:** O ranker unitario em PHP implementa uma similaridade de trigramas propria, enquanto a producao usa `pg_trgm.similarity`. Isso cria um oraculo de teste que pode divergir da semantica real de ranking, especialmente em padding, Unicode e limites.
  - **Evidence:** `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchRanker.php:60-82`; SQL real em `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:243-251`.
  - **Recommendation:** Testar ranking fuzzy principal contra PostgreSQL real ou limitar o ranker PHP a regras independentes que nao tentem emular `pg_trgm`.

- **ADV-2.3-RR-013**
  - **Severity:** Low
  - **Finding:** A pagina invalida reflete `rawParams.q` no `defaultValue` do input, inclusive para termos que falharam por controles/invisiveis ou tamanho. React escapa HTML, mas controles e invisiveis continuam sendo ecoados no DOM, o que enfraquece a promessa de erro sanitizado.
  - **Evidence:** `apps/web/src/app/(public)/buscar/page.tsx:21-27`; `apps/web/src/features/catalog-search/SearchForm.tsx:8-18`.
  - **Recommendation:** Para estado invalido, exibir campo vazio ou uma versao canonicalizada/limitada que ja passou por filtro seguro.

- **ADV-2.3-RR-014**
  - **Severity:** Low
  - **Finding:** O teste de referrer/terceiros assume `http://127.0.0.1:3000` como unica origem propria. Se a suite rodar em outro host/porta, ela pode classificar request first-party como terceiro ou deixar de provar a politica pretendida em ambiente equivalente.
  - **Evidence:** `apps/web/tests/e2e/catalog-search.spec.ts:79-88`.
  - **Recommendation:** Derivar a origem de `page.url()` ou da configuracao Playwright em vez de fixar host/porta.
