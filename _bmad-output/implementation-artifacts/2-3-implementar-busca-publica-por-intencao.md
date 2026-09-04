---
baseline_commit: ed2ae840ea4aef70168b1539b711f98882de08f7
---

# Story 2.3: Implementar busca pública por intenção

Status: done

<!-- Security review is mandatory before ready-for-dev. -->

## Story

Como cliente da JS Designs,
quero buscar produtos por nome, categoria, tipo, tema, personagem, ocasião, sinônimo ou grafia aproximada,
para encontrar rapidamente produtos específicos sem precisar entrar em uma conta.

## Acceptance Criteria

1. **Busca pública e fontes pesquisáveis**
   - **Given** uma visitante anônima na rota `/buscar`
   - **When** ela envia um termo válido
   - **Then** o Next.js consulta a Laravel API exclusivamente pelo BFF server-side
   - **And** o Laravel pesquisa apenas produtos `published` por nome, categoria, modalidade/tipo, tema, ocasião, `search_alias` e personagem com associação de direitos `verified`
   - **And** descrição, compatibilidade, notas, evidências de direitos, referências de storage e outros campos administrativos não participam da busca.

2. **Normalização, grafia aproximada e relevância determinística**
   - **Given** diferenças de caixa, acentuação, espaços ou erro ortográfico pequeno
   - **When** a busca é executada
   - **Then** a normalização Unicode/pt-BR e a comparação aproximada encontram correspondências dentro de limiar configurado
   - **And** cada candidato recebe uma única classe pelo seu melhor match: `exact` para igualdade normalizada do valor completo; `prefix` para início de palavra/termo; `fuzzy` para similaridade acima do limiar
   - **And** o ranking segue, nesta ordem: nome `exact`; categoria/tema/ocasião/modalidade/personagem/alias `exact`; `prefix`; `fuzzy`
   - **And** dentro da classe usa score decrescente, `published_at DESC` e `id DESC`, sem alegar popularidade ou recomendação não medida
   - **And** classe/score e a fonte interna do match não aparecem no card ou em metadados públicos.

3. **Exatos, agrupamento e semelhantes**
   - **Given** que existem correspondências exatas e aproximadas
   - **When** a resposta é exibida
   - **Then** resultados exatos aparecem primeiro, agrupados por categoria pública
   - **And** resultados aproximados aparecem em seção separada e identificada como semelhantes
   - **And** cada produto aparece no máximo uma vez e reutiliza o card público da Story 2.2
   - **And** paginação numerada opera sobre a sequência determinística de todos os exatos antes de todos os semelhantes, inclusive quando a página contém o fim dos exatos e o início dos semelhantes
   - **And** grupos exatos ordenam pelo melhor item do grupo e depois por label/slug; itens dentro do grupo mantêm ranking global
   - **And** página acima de `last_page` mantém o estado “página fora do intervalo” da 2.2, sem ser confundida com busca sem resultado
   - **And** o termo é preservado na URL, nos links de página e no retorno do detalhe.

4. **Contrato público dedicado e allowlist**
   - **Given** uma chamada `GET /api/v1/catalog/search?q=...&page=...&per_page=...`
   - **When** o Laravel responde
   - **Then** o envelope fechado contém grupos exatos da página, semelhantes da página, sugestões públicas seguras, intenção aplicável e metadados de consulta/paginação
   - **And** os itens reutilizam a projeção pública da Story 2.2
   - **And** `character`, `search_alias`, status/evidência de direitos, status/versionamento administrativo e referências de storage nunca aparecem no JSON, HTML, logs ou URLs públicas
   - **And** IDs não se repetem entre grupos/semelhantes; soma e limites de itens são coerentes com `total`, `total_exact`, `total_similar`, `current_page`, `per_page` e `last_page`
   - **And** sugestões são no máximo 6, únicas, deterministicamente ordenadas, derivadas somente de categoria/tema/ocasião editorial pública e apontam para href same-origin allowlisted
   - **And** os endpoints existentes `/catalog/products`, `/catalog/products/{slug}` e `/catalog/facets` mantêm contratos e comportamento inalterados.

5. **Busca inicial, resultado vazio e degradação**
   - **Given** `/buscar` sem `q`
   - **When** a página carrega
   - **Then** exibe formulário de busca, categorias principais e sugestões editoriais públicas, sem tratar a ausência de termo como erro
   - **And** não executa consulta aproximada vazia.
   - **Given** um termo válido sem correspondência exata nem semelhante
   - **When** a resposta vazia é exibida
   - **Then** mantém o termo escapado como texto, mostra mensagem clara e oferece alternativas acionáveis
   - **And** oferece suporte/Projeto Exclusivo apenas quando formato, recurso ou escopo estiver fora das opções configuradas
   - **And** não implementa nem simula o formulário, envio, preço, prazo ou aceite de Projeto Exclusivo desta story.
   - **Given** timeout, falha de banco ou payload upstream inválido
   - **When** a busca falha
   - **Then** a página diferencia “temporariamente indisponível” de “sem resultado”, usa erro sanitizado e mantém um caminho de retomada
   - **And** falha de sugestões/facetas complementares não derruba resultados principais válidos.

6. **Intenção de convite**
   - **Given** uma busca por tema de convite sem exemplar publicado
   - **When** o Laravel identifica intenção de convite por vocabulário/taxonomia configurada e coberta por fixtures, nunca por heurística no React/BFF
   - **Then** a experiência informa que o tema não é uma lista fechada e conduz à descoberta de convites digitais personalizados
   - **And** preserva o termo validado no contrato e em um href same-origin de handoff, sem alegar que o futuro pré-formulário já persiste ou consome esse contexto
   - **And** não força Projeto Exclusivo apenas pela ausência do tema
   - **And** não antecipa comparação/configuração do Epic 3.

7. **Validação, abuso e erros**
   - **Given** `q`, `page` ou `per_page` em formato inválido
   - **When** a fronteira HTTP recebe termo vazio/whitespace-only, menor que 2 ou maior que 120 caracteres Unicode, maior que 512 bytes UTF-8, controles/invisíveis proibidos, encoding inválido, chaves duplicadas/array-style ou paginação não canônica
   - **Then** rejeita cedo com `422` e mensagem pt-BR sanitizada
   - **And** Next e Laravel validam após normalização/collapse de espaços e concordam sobre o valor canônico e os limites
   - **And** metacaracteres SQL/LIKE são dados parametrizados, nunca SQL concatenado
   - **And** a rota aplica limiter de busca com default inicial de 60 requisições/minuto por chave não reversível, statement timeout, teto de candidatos fuzzy e `Cache-Control: no-store, private`
   - **And** abuso recebe `429` e falha de dependência recebe `503`, sem SQL, stack, path, URL interna ou termo bruto em logs.
   - **And** a página não envia o termo a analytics/terceiros e aplica política de referrer que não revele a query fora da origem.

8. **UX, acessibilidade e responsividade**
   - **Given** uso por teclado, toque, zoom ou tecnologia assistiva
   - **When** a visitante usa a busca
   - **Then** há `<form role="search">`, label persistente/nome acessível, foco visível, submit específico, erros associados e ordem de foco lógica
   - **And** chips/links funcionam sem hover ou cor exclusiva, com alvo mínimo de 44×44 px
   - **And** a tarefa essencial funciona por GET/SSR sem JavaScript do cliente
   - **And** a página mantém reflow, conteúdo íntegro e cards legíveis em 320, 420, 760 e 1100 px, respeitando `prefers-reduced-motion` e WCAG 2.2 AA
   - **And** todo texto visível fica centralizado na estrutura i18n, em pt-BR/UTF-8 nesta etapa, sem mojibake.

9. **Desempenho percebido e fronteira SEO**
   - **Given** a busca pública renderizada por SSR
   - **When** os gates E2E são executados
   - **Then** mídia, fontes e scripts complementares não bloqueiam formulário/resultados e há evidência local de LCP, CLS e interação nas faixas 320/420/760/1100 px
   - **And** a rota parametrizada usa `robots: noindex, follow` até a Story 2.4 definir canonical, metadados compartilháveis e política final de indexação
   - **And** nenhuma entrega de canonical, Open Graph, sitemap ou dados estruturados é reivindicada nesta story.

## Tasks / Subtasks

- [x] 1. Fechar contratos e testes vermelhos da busca (AC: 1–7)
  - [x] Definir DTOs imutáveis de critérios, grupos/resultados, sugestões, intenção e paginação no módulo `Catalog/Application/Queries`.
  - [x] Documentar no OpenAPI o GET dedicado, parâmetros `q`, `page`, `per_page`, envelope fechado e respostas `200/422/429/503`.
  - [x] Escrever Unit tests para limites, normalização, classificação exato/semelhante, ranking e desempate antes da implementação.
  - [x] Escrever Feature tests PostgreSQL para todas as fontes pesquisáveis, direitos verificados, publicação, allowlist, erros, query budget e retirada de publicação.

- [x] 2. Implementar busca e ranking no Laravel/PostgreSQL (AC: 1–7)
  - [x] Adicionar caso de uso/porta de leitura e implementar a consulta no adapter PostgreSQL, reutilizando hidratação em lote e `PublicCatalogProductResource`.
  - [x] Criar migration incremental para extensões/índices usados pela expressão real de busca; não reescrever migrations compartilhadas.
  - [x] Se usar `pg_trgm`/`unaccent`, validar disponibilidade/permissão no PostgreSQL 18, manter criação reversível e fazer a migration falhar claramente se o requisito não puder ser atendido; não entregar fallback silencioso sem aproximação.
  - [x] No rollback, remover somente índices/objetos pertencentes a esta migration; não executar `DROP EXTENSION` compartilhado sem prova de propriedade exclusiva.
  - [x] Não presumir que uma chamada direta a `unaccent` pode receber expression index: usar coluna normalizada/generation strategy ou função imutável auditada e provar com `EXPLAIN`/fixture representativa que a query usa o índice esperado.
  - [x] Aplicar somente bindings parametrizados; escapar `%` e `_` quando forem literais; limitar candidatos aproximados antes do ranking final.
  - [x] Manter `published` e direitos verificados como restrições anteriores à formação de candidatos, score, contagem, paginação e sugestões; personagem/alias só alimentam score interno.
  - [x] Preservar statement timeout, consulta sem N+1, resolução de imagem em lote e ordenação total.
  - [x] Extrair uma colaboração interna reutilizável para hidratação/projeção se necessário; não copiar o hydrate privado em uma segunda implementação divergente.

- [x] 3. Expor endpoint público seguro (AC: 4, 7)
  - [x] Criar Form Request que inspeciona a query string bruta antes do colapso do Laravel e valida o contrato exato.
  - [x] Criar controller fino e Resource/envelope allowlist; registrar rota em `/api/v1` com `PublicCatalogNoStore` e limiter dedicado ou endurecido.
  - [x] Configurar `CATALOG_SEARCH_SIMILARITY_THRESHOLD`, `CATALOG_SEARCH_CANDIDATE_LIMIT`, `CATALOG_SEARCH_RATE_LIMIT_PER_MINUTE` e `CATALOG_SEARCH_STATEMENT_TIMEOUT_MS` em `config/catalog.php`/`.env.example`, com parsing estrito, ranges seguros e fail-fast/fallback conservador para configuração inválida; nenhum valor é segredo.
  - [x] Cobrir payload exato e ausência de campos admin/protegidos em testes HTTP.

- [x] 4. Implementar cliente BFF e validação runtime (AC: 1, 4, 5, 7)
  - [x] Estender o cliente `server-only` com tipos fechados, parser de `searchParams`, construtor de href e fetch da busca.
  - [x] Tratar respostas externas como `unknown`; rejeitar campos extras, enums/UUIDs inválidos, grupos duplicados, imagens inseguras e metadados inconsistentes.
  - [x] Rejeitar IDs duplicados entre grupos/semelhantes, sugestões acima do limite/href externo e qualquer inconsistência matemática dos totais/paginação.
  - [x] Reutilizar transporte, timeout de 5 s, `cache: 'no-store'`, `CatalogApiError` e URLs construídas com `URL`/`URLSearchParams`.
  - [x] Não criar Route Handler Next nem chamada browser→Laravel.

- [x] 5. Substituir `/buscar` placeholder por experiência SSR (AC: 3, 5, 6, 8, 9)
  - [x] Implementar a página como Server Component com `searchParams` assíncrono do Next.js 16 e formulário GET acessível.
  - [x] Criar componentes específicos para painel, grupos exatos, semelhantes, sugestões e estados; reutilizar `CatalogCard` e padrões visuais da 2.2.
  - [x] Preservar `q` na paginação, CTA do detalhe e retorno; renderizar termo somente como texto React.
  - [x] Implementar estados inicial, com resultados, somente semelhantes, sem resultado, inválido, página fora do intervalo e indisponível.
  - [x] Para Projeto Exclusivo/suporte, fornecer apenas link honesto com contexto minimizado para rota existente/futura; não coletar PII nem simular submissão.
  - [x] Não repassar `q` a analytics, mídia ou terceiros; confirmar `Referrer-Policy` efetiva (`strict-origin-when-cross-origin` ou mais restritiva) e testar que a query não sai da origem.
  - [x] Atualizar conteúdo tipado/i18n, home/navegação que ainda dizem “em preparação”, CSS mobile-first e exports públicos.
  - [x] Definir metadata `noindex,follow` para buscas parametrizadas sem antecipar o pacote SEO da 2.4.

- [x] 6. Validar regressões, acessibilidade e operação (AC: 1–9)
  - [x] Atualizar seeder E2E com fixtures determinísticas de nome, categoria, tema, ocasião, alias, personagem verificado/não verificado, convite e typo.
  - [x] Testar BFF com envelopes válidos/inválidos, campos extras/admin, URLs, timeout e falhas.
  - [x] Criar Playwright para busca inicial, exatos/semelhantes, zero-result, convite, inválida, upstream indisponível, cruzamento exato→semelhante, página fora do intervalo, paginação/retorno, teclado/foco e 320/420/760/1100 px.
  - [x] Provar que personagem não verificado não altera resultado, total, sugestão ou classificação observável; executar fixture representativa/query plan e evidência local de CWV.
  - [x] Preservar os testes da 2.2 e remover `/buscar` somente da matriz de placeholders da foundation.
  - [x] Executar `composer test`, `vendor/bin/pint --test`, `composer audit --locked`, `npm run test:bff`, `npm run lint`, `npm run typecheck`, `npm run build`, `npm run test:e2e`, `npm audit --audit-level=moderate` e `git diff --check`.
  - [x] Executar SAST e secret scan se disponíveis; registrar explicitamente ferramentas ausentes, sem convertê-las em aprovação.

### Review Findings

- [x] [Review][Decision] Alteracao global do workflow `bmad-dev-story` esta fora do escopo da Story 2.3 — Resolvido por decisao de Sharom: manter a alteracao de governanca, mas exigir que todos os achados High, Medium e Low sejam tratados antes da conclusao.
- [x] [Review][Patch] `candidate_limit` trunca resultados, totais e paginacao antes da ordenacao final [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:230]
- [x] [Review][Patch] BFF aceita payload de busca semanticamente inconsistente com `criteria` [apps/web/src/bff/catalogApi.ts:152]
- [x] [Review][Patch] Normalizacao SQL nao e equivalente a normalizacao NFKC usada por API/BFF [apps/api/database/migrations/2026_08_29_000001_add_public_catalog_search_indexes.php:33]
- [x] [Review][Patch] Sugestoes podem gerar hrefs que o proprio BFF rejeita como busca invalida [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:330]
- [x] [Review][Patch] Clamp compartilhado de `statement_timeout` reduz endpoints existentes de 30s para 5s [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:499]
- [x] [Review][Patch] Campo de busca no HTML limita por UTF-16, mas backend/BFF validam por code points e bytes UTF-8 [apps/web/src/features/catalog-search/SearchForm.tsx:15]
- [x] [Review][Patch] Testes unitarios exercitam `PublicCatalogSearchRanker`, mas a producao ranqueia via SQL [apps/api/tests/Unit/Modules/Catalog/PublicCatalogSearchTest.php:53]
- [x] [Review][Patch] Teste de plano força `enable_seqscan = off`, entao nao prova o plano real do endpoint [apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php:314]

## Dev Notes

### Developer Context

- A Story 2.1 criou catálogo, taxonomia `theme|occasion|character|search_alias` e governança de direitos; ranking/aproximação ficaram explicitamente para esta story.
- A Story 2.2 já entrega leitura pública, Resource allowlist, BFF server-only, cards, imagens fail-closed, filtros, paginação e estados. Estenda esses padrões; não crie uma segunda stack de catálogo.
- `epics-next-only-2026-07-27.md` possui numeração antiga (busca era 1.3 e 2.3 era convite). Use apenas detalhes corroborados pelo PRD; `epics.md#Story 2.3` é a fonte autoritativa da chave atual.
- O mockup `key-busca-exclusivo.html` ilustra composição/densidade, mas inclui o formulário da Story 7.3. Nesta story só existe handoff, sem envio/anexo/PII.

### Architecture Compliance

- Stack efetiva: Next.js 16.3.0, React 19.2.0, TypeScript 5.9.x strict/ES2022, Node 24.x, PHP 8.5.x, Laravel 13.x, PostgreSQL 18, Redis 8.x, PHPUnit 12.5.x e Playwright 1.60.0. Manifests/locks prevalecem; não atualizar dependências ou lockfiles.
- Fluxo obrigatório: navegador → Next.js App Router/BFF same-origin → Laravel `/api/v1` → PostgreSQL. Relevância/intenção ficam no Laravel; Redis não é fonte nem engine primária de busca.
- Backend mantém `Domain`, `Application`, `Infrastructure`, `Interfaces/Http`; Query Builder/Eloquent somente em Infrastructure; controllers delegam a caso de uso/porta.
- O endpoint dedicado evita quebrar o envelope e a ordenação de `GET /catalog/products` consolidados na 2.2. Reutilize a mesma projeção/hidratação segura.
- Mudanças REST atualizam `packages/contracts/catalog-public-v1.openapi.yaml` e testes HTTP.

### API Contract Target

- `GET /api/v1/catalog/search?q={2..120 chars, <=512 UTF-8 bytes}&page={1..1000}&per_page={1..48}`; defaults `page=1`, `per_page=12`.
- Envelope fechado esperado:
  - `data.exact_groups[]`: categoria pública `{slug,label}` + `items[]` no shape de card da 2.2;
  - `data.similar[]`: itens no mesmo shape, sem duplicar exatos;
  - `data.suggestions[]`: somente rótulos/links derivados de categorias, temas e ocasiões editoriais públicas; nunca personagem, alias ou direitos;
  - `data.intent`: enum fechado (`generic|invitation`) e `preserved_term`; nenhuma decisão de preço/prazo;
  - `meta`: `query`, `current_page`, `per_page`, `last_page`, `total`, `total_exact`, `total_similar`.
- A paginação opera sobre a sequência total determinística “todos os exatos antes de todos os semelhantes”; `exact_groups` contém somente os exatos presentes na página atual.
- Resposta vazia válida é `200`; input inválido `422`; throttle `429`; dependência indisponível `503`. Todas carregam `no-store` e mensagens sanitizadas.

### Search Semantics and Performance

- Implementação preferida: PostgreSQL-native, sem Elasticsearch/Algolia/Scout ou nova dependência. `pg_trgm` fornece similaridade e índices GiST/GIN; `unaccent` remove diacríticos. Vincule índices às expressões reais e prove comportamento com PostgreSQL real.
- Não use threshold global de sessão sem isolamento. Preferir score/limiar explícito por query/config e binding seguro.
- Fuzzy deve ter limiar e conjunto candidato máximo configuráveis; termos de 1 caractere e consultas vazias são rejeitados antes do banco.
- Defaults/ranges iniciais, sujeitos a evidência de testes sem relaxamento silencioso: similaridade `0.30` (`0.20..0.80`), candidatos fuzzy `500` (`50..2000`), limiter `60/min` (`10..120`) e statement timeout `3000 ms` (`100..5000`). Valor fora do range deve falhar cedo ou cair para o default conservador com evidência testada, nunca ampliar o limite.
- A busca pode verificar personagem internamente somente com direitos `verified`. O resultado é o produto editorial publicado; personagem não vira conteúdo, categoria, filtro, sugestão ou metadado público.
- Não registrar `q` bruto. Métricas completas/analytics de busca pertencem ao Epic 8; qualquer observabilidade desta story deve ser agregada/minimizada e sem PII.
- Como `q` fica em URL/histórico, a UI deve orientar a não inserir dados pessoais, não enviar a query a terceiros e manter política de referrer restritiva. Handoff para suporte/Projeto Exclusivo carrega somente contexto validado e minimizado.

### Current Files to Update

- Backend: `PublicCatalogQuery.php`, `PostgresPublicCatalogQuery.php`, `routes/api.php`, `config/catalog.php`, possivelmente `AppServiceProvider.php`, `CatalogE2eSeeder.php`, testes públicos, `Catalog/README.md`, contratos e READMEs.
- Frontend: `app/(public)/buscar/page.tsx`, `bff/catalogApi.ts`, `bff/catalogValidation.ts`, possivelmente `catalogErrors.ts`, `i18n/publicContent.ts`, `i18n/publicContent.types.ts`, `features/public-store/publicLayoutContent.ts`, `app/globals.css`, testes unit/E2E/foundation.
- Novos arquivos prováveis: critérios/resultados/caso de uso/Request/Controller/Resource e migration de busca no Laravel; componentes de busca e `catalog-search.spec.ts` no Next.js.
- Preserve integralmente: listagem/facetas/detalhe 2.2, filtros `{category,occasion,modality,page,per_page}`, cards, imagens same-origin/fail-closed, middleware admin, e placeholders não relacionados.

| Arquivo existente | Estado atual | Mudança desta story | Deve ser preservado |
| --- | --- | --- | --- |
| `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogQuery.php` | porta `list/facets/findPublishedBySlug` | adicionar operação de busca ou porta dedicada coerente | assinaturas/semântica 2.2 |
| `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php` | publicados, filtros, paginação, hydrate em lote, timeout | matching/ranking e reúso do hydrate | filtros/listagem/detalhe, allowlist, imagens e query budget |
| `apps/api/routes/api.php` | três GETs públicos e grupo admin separado | registrar GET de busca com middleware/limiter | rotas e isolamento admin atuais |
| `apps/api/config/catalog.php` | limiter e statement timeout públicos | limites/threshold/candidate cap de busca | defaults existentes e ausência de segredos |
| `apps/api/app/Providers/AppServiceProvider.php` | bindings, imagem fail-closed e limiter com hash | binding/limiter apenas se necessário | resolvers e limiter atuais |
| `apps/api/database/seeders/CatalogE2eSeeder.php` | fixtures de listagem/paginação | fixtures determinísticas de busca/direitos | seed somente em `testing` e cenários 2.2 |
| `apps/api/tests/Feature/Catalog/PublicCatalogApiTest.php` | contrato, allowlist, paginação, erros, throttle | cobertura HTTP/PostgreSQL da busca | todas as asserções 2.2 |
| `apps/api/tests/Unit/Modules/Catalog/PublicCatalogReadTest.php` | filtros/excerpt/imagem | critérios/normalização/ranking ou nova suíte | vocabulário fechado da listagem |
| `apps/api/app/Modules/Catalog/README.md` | busca delegada à 2.3 | documentar contrato, ranking, índices e privacidade | fronteiras, allowlist e imagem fail-closed |
| `packages/contracts/catalog-public-v1.openapi.yaml` | listagem/detalhe/facetas fechados | adicionar busca e schemas fechados | endpoints/schemas atuais e `additionalProperties: false` |
| `apps/web/src/app/(public)/buscar/page.tsx` | `PlaceholderPage` estático | Server Component real com GET/SSR e estados | metadata pt-BR e boundary server-first |
| `apps/web/src/bff/catalogApi.ts` | parser/fetch/hrefs do catálogo, `server-only` | tipos/parser/fetch/hrefs da busca | APIs da 2.2 e `unknown` validado |
| `apps/web/src/bff/catalogValidation.ts` | guards exatos e imagem same-origin | guard fechado do envelope de busca | rejeição de extras/admin e guards atuais |
| `apps/web/src/bff/catalogErrors.ts` | erros tipados do catálogo | generalizar/adicionar consulta inválida se preciso | distinção invalid/upstream/payload |
| `apps/web/src/i18n/publicContent.ts` e `.types.ts` | copy catálogo e busca “em preparação” | shape/copy real de busca | pt-BR UTF-8, `Readonly` e placeholders restantes |
| `apps/web/src/features/public-store/publicLayoutContent.ts` | exports de conteúdo público | exportar conteúdo de busca | exports atuais |
| `apps/web/src/app/globals.css` | tokens e catálogo mobile-first | painel, grupos, semelhantes, chips/estados | foco, 44 px, reflow, reduced motion e cards |
| `apps/web/tests/e2e/foundation.spec.ts` | `/buscar` na matriz de placeholders | remover somente essa expectativa e testar navegação real | demais placeholders, UTF-8, header e mobile |
| `apps/web/tests/unit/catalog-validation.test.mjs` | contrato/transporte/imagem | envelope válido/inválido de busca | testes existentes |

### Testing Requirements

- Unit: validação/normalização, score/classificação, limiar, teto candidato e desempate.
- Feature/PostgreSQL: cada fonte de match; acento/case/espaço/typo; direitos; apenas publicados; exatos antes de semelhantes; agrupamento; paginação; allowlist; SQL/LIKE hostil como dado; `422/429/503`; no-store; query budget; despublicação imediata.
- BFF: parser fechado, `unknown` guards, campos extras/admin, grupos/metas inconsistentes, timeout/no-store e href same-origin.
- Playwright: GET/SSR sem JS, labels/roles/foco, resultados e estados, termo/retorno preservados, chips, convite e viewports 320/420/760/1100; verificar ausência de mojibake.
- Não inventar threshold numérico de cobertura. Cobrir todos os ACs e regressões próximas.

### Scope Boundaries

- **Inclui:** busca anônima, ranking exato/aproximado, grupos, semelhantes, sugestões públicas, paginação, BFF/UI SSR e handoff contextual.
- **Não inclui:** CRUD admin, formulário de Projeto Exclusivo, suporte/chat real, comparação/configuração de convite, autocomplete/typeahead, recomendações avançadas, SEO completo/canonical/OG/sitemap, analytics completo, carrinho/checkout ou novo provedor externo.

### Threat Model - STRIDE

Contexto: browser público → Next.js BFF → Laravel GET público → PostgreSQL; saída por JSON/HTML e logs minimizados. Não há autenticação intencional, upload, pagamento ou PII necessária nesta story.

| Categoria | Ameaça | Mitigação exigida |
| --- | --- | --- |
| Spoofing | cliente tenta alcançar capacidade admin pelo endpoint público | GET separado, porta read-only, nenhuma identidade/header confiado e middleware admin preservado |
| Tampering | query duplicada, array, encoding, wildcard ou termo altera a consulta/href | inspeção bruta, schema/limites estritos, `URLSearchParams`, bindings parametrizados e escape de LIKE |
| Repudiation | abuso anônimo sem correlação ou log contendo termo potencialmente pessoal | throttle com chave não reversível, correlação técnica sem IP/termo bruto e retenção mínima |
| Information Disclosure | personagem/direitos/admin/storage/SQL/stack vazam por score, sugestão, JSON, HTML, erro ou log | allowlists fechadas em Laravel+BFF, sugestões editoriais públicas, React text escaping e erros/no-store sanitizados |
| Denial of Service | fuzzy, paginação ou termos hostis provocam scan/custo excessivo | `q` 2..120, throttle, timeout, página/per-page limitadas, teto candidato, índices e query budget |
| Elevation of Privilege | BFF/controller acessa repository ou dados administrativos | caso de uso/porta pública em Application, adapter dedicado/reutilizado em Infrastructure e testes de isolamento |

### Previous Story Intelligence

- A 2.2 foi concluída após patches para query bruta, campos extras, erros não mascarados, throttle sem IP bruto, asset same-origin real, retorno filtrado, metadata e OpenAPI allowlist. Não reintroduza esses defeitos.
- Falha de facetas não derruba listagem; aplique o mesmo princípio a sugestões complementares.
- Testes integrados dependem de PostgreSQL 18/Docker saudável; não alegar conclusão se Feature/E2E não rodarem.
- SAST/secret scan ainda não estão configurados localmente. Mantenha como evidência pendente, não como check aprovado.
- A configuração verificável do repositório não contém políticas de IDE/sandbox/browser allowlist; não altere políticas globais nesta story e registre o gap para governança humana.

### Git Intelligence

- Commits recentes: `6046a58` implementou a 2.2; `e1ef326` alinhou CI/BFF/E2E; `e39bc43` criou domínio/schema/admin e `search_alias`.
- Branch observada: `story_2_3`; árvore estava limpa durante a contextualização. Mensagens recentes são curtas e orientadas ao resultado.

### Latest Technical Information

- PostgreSQL 18 `pg_trgm`: `similarity`/`word_similarity` e índices GiST/GIN suportam busca aproximada; o limiar deve ser explícito e testado. [PostgreSQL 18 pg_trgm](https://www.postgresql.org/docs/18/pgtrgm.html)
- PostgreSQL 18 `unaccent`: dicionário para remoção de diacríticos; valide sua aplicação na expressão indexada. [PostgreSQL 18 unaccent](https://www.postgresql.org/docs/18/unaccent.html)
- Next.js 16 removeu acesso síncrono a `searchParams`; páginas devem aguardar a Promise. [Next.js 16 upgrade guide](https://nextjs.org/docs/app/guides/upgrading/version-16)
- Laravel 13 oferece Form Requests/validação e retorna `422` JSON para falhas de validação; preserve o tratamento bruto adicional já existente. [Laravel 13 validation](https://laravel.com/framework/docs/13.x/validation)
- Playwright recomenda locators por role/label e assertions web-first. [Playwright locators](https://playwright.dev/docs/locators)

### References

- [Source: _bmad-output/planning-artifacts/epics.md#Epic 2]
- [Source: _bmad-output/planning-artifacts/epics.md#Story 2.3]
- [Source: _bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md#FR-6 — Busca por intenção]
- [Source: _bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md#FR-10 — Projeto Exclusivo]
- [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md#Information Architecture]
- [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md#State Patterns]
- [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md#UJ-2]
- [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md#Components]
- [Source: _bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md]
- [Source: _bmad-output/implementation-artifacts/2-1-cadastrar-produtos-com-estrutura-de-catalogo-comercial.md]
- [Source: _bmad-output/implementation-artifacts/2-2-exibir-listagens-publicas-por-categoria-ocasiao-e-tipo.md]
- [Source: apps/api/app/Modules/Catalog/README.md]
- [Source: _bmad-output/project-context.md]

## Security Gate - bmad-review-security

- Revisão: executada em 2026-08-26
- Relatório: `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-security.md`
- Resultado: aprovado com ressalvas
- Alto risco aberto: não
- Médio risco aberto: não; SEC-2.3-01/02/03 foram corrigidos no contrato e exigem evidência na implementação
- Baixos residuais: SAST/secret scan ausentes; políticas IDE/sandbox/browser não versionadas; `.gitignore` sem padrões amplos de chaves/dumps
- Owners: Dev (controles da story), Dev/DevOps (scanners/`.gitignore`) e Sharom/DevOps (governança)
- Condição: manter os scanners como gate antes de `done`; não tratar ausência de ferramenta como aprovação.

## Story Review Gate - bmad-review-adversarial-general

- Revisão: executada em 2026-08-26
- Relatório: `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-adversarial.md`
- Resultado: aprovado após correções
- Achados: 16 corrigidos; nenhum requer decisão adicional
- Correções principais: classes/ranking, paginação combinada, intenção de convite, handoff futuro, sugestões, Unicode/bytes, limiter, índices `unaccent`/`pg_trgm`, direitos antes do score, hidratação reutilizável, fronteira SEO, CWV, configuração e consistência do envelope.

## Implementation Adversarial Gate - bmad-review-adversarial-general

- Revisão: executada em 2026-08-29 após a implementação
- Relatório: `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-implementation-adversarial.md`
- Resultado: 12 achados tratados; nenhum crítico/alto aberto
- Correções principais: coerência semântica integral do envelope BFF, hierarquia de headings, query string canônica, fallback de configuração testável, falha complementar de sugestões e `EXPLAIN` sobre o SQL real com uso do índice GIN.

## Adversarial Rerun Gate - bmad-review-adversarial-general

- Revisão: executada em 2026-09-02 sobre a story em `review`
- Relatório: `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-adversarial-rerun-2026-09-02.md`
- Resultado: 14 achados resolvidos
- Severidades resolvidas: 3 High, 8 Medium, 3 Low
- Correções principais: intenção de convite condicionada a zero resultado, página fora do intervalo preservada, candidatos `non_fuzzy` limitados por produto, normalizador SQL versionado, sugestões por relevância, chips editoriais iniciais, desempate de grupos por label/slug, OpenAPI fechado e E2E de referrer sem origem fixa.

## Dev Agent Record

### Agent Model Used

GPT-5 Codex

### Implementation Plan

- Fechar DTOs, OpenAPI e testes vermelhos para normalização, ranking, contrato e segurança.
- Implementar a consulta PostgreSQL com publicação/direitos antes do score, paginação combinada, índice GIN e hidratação pública reutilizada.
- Expor Request/controller/Resource com validação bruta, limiter dedicado, no-store e falhas sanitizadas.
- Validar o envelope no BFF e substituir `/buscar` por experiência SSR acessível, responsiva e sem JavaScript obrigatório.
- Executar regressões completas, plano de query, audits e evidência local de CWV antes da revisão adversarial.

### Debug Log References

- 2026-08-26: contexto carregado de epics, PRD, UX, arquitetura Laravel/BFF, Stories 2.1/2.2, código atual, Git e documentação oficial.
- 2026-08-29: ciclo red-green-refactor executado para critérios/ranker, endpoint PostgreSQL, validação BFF e jornada SSR/E2E.
- 2026-08-29: Docker Desktop/PostgreSQL restaurados; migration `pg_trgm`/`unaccent` validada e índice GIN comprovado por `EXPLAIN` com 5.000 fixtures.
- 2026-08-29: wildcard `%`/`_` detectado pelo teste hostil e corrigido com comparação literal sem `LIKE`.
- 2026-08-29: gates CWV executados no build de produção com um worker para evitar contenção artificial entre cenários de performance.
- 2026-08-29: revisão adversarial pós-implementação encontrou 12 lacunas; todas foram corrigidas ou cobertas por evidência automatizada.
- 2026-09-02: rerun adversarial encontrou 14 achados; correções aplicadas em backend, BFF/UI, OpenAPI, testes e documentação. Docker/PostgreSQL local foi restaurado e a integração PostgreSQL passou na suíte completa.

### Completion Notes List

- Context engine analysis completed; mandatory security gate passed with documented low-risk conditions.
- Adversarial story review completed; 16 findings corrected with no open product/technical judgment.
- Story security gate passed - ready for development.
- Implementada busca pública dedicada por nome, categoria, modalidade, tema, ocasião, alias e personagem com direitos verificados, sem vazamento das fontes internas.
- Ranking determinístico, agrupamento exato, semelhantes, paginação combinada, sugestões editoriais e intenção de convite permanecem autoridade do Laravel.
- BFF server-only valida envelope fechado, totais, UUIDs, duplicações, imagens e hrefs; `/buscar` funciona por GET/SSR com estados completos, retorno ao detalhe, `noindex,follow` parametrizado e referrer restritivo.
- Validação concluída: 128 testes PHP (548 asserções), 10 testes BFF, 12 E2E específicos da busca e 55 E2E completos em modo produção aprovados. Pint, ESLint, TypeScript, build, composer audit, npm audit e `git diff --check` aprovados.
- O plano do SQL real do endpoint, com 5.000 fixtures, comprova o índice GIN após separar os candidatos fuzzy e tornar o CTE de parâmetros não materializado.
- Rerun adversarial de 2026-09-02 resolvido: convite agora só aparece sem resultados, página fora do intervalo não vira vazio, `non_fuzzy` é limitado por produto candidato, normalizador SQL versionado `catalog_public_search_normalize_v1` documenta dependência de `unaccent`/`REINDEX`, sugestões ordenam por relevância, `/buscar` inicial mostra ocasiões editoriais e contratos de erro/href foram fechados.
- SAST/secret scan: `semgrep`, `gitleaks` e `trivy` não estão disponíveis no ambiente; ausência registrada como gap, não como aprovação.

### File List

- `_bmad-output/implementation-artifacts/2-3-implementar-busca-publica-por-intencao.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-security.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-adversarial.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-implementation-adversarial.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-3-implementar-busca-publica-por-intencao-adversarial-rerun-2026-09-02.md`
- `apps/api/.env.example`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchCriteria.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchGroup.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchIntent.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchMatch.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchPage.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchQuery.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchRanker.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSearchSuggestion.php`
- `apps/api/app/Modules/Catalog/Application/Queries/SearchPublicCatalog.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Config/CatalogSearchConfiguration.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Controllers/SearchPublicCatalogController.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/SearchPublicCatalogRequest.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogSearchResource.php`
- `apps/api/app/Modules/Catalog/README.md`
- `apps/api/app/Providers/AppServiceProvider.php`
- `apps/api/config/catalog.php`
- `apps/api/database/migrations/2026_08_29_000001_add_public_catalog_search_indexes.php`
- `apps/api/database/seeders/CatalogE2eSeeder.php`
- `apps/api/routes/api.php`
- `apps/api/tests/Feature/Catalog/PublicCatalogSearchApiTest.php`
- `apps/api/tests/Unit/Modules/Catalog/CatalogSearchConfigurationTest.php`
- `apps/api/tests/Unit/Modules/Catalog/PublicCatalogSearchTest.php`
- `apps/web/src/app/(public)/buscar/page.tsx`
- `apps/web/src/app/(public)/produtos/[slug]/page.tsx`
- `apps/web/src/app/globals.css`
- `apps/web/src/app/layout.tsx`
- `apps/web/src/bff/catalogApi.ts`
- `apps/web/src/bff/catalogSearchParams.ts`
- `apps/web/src/bff/catalogValidation.ts`
- `apps/web/src/features/catalog-search/SearchForm.tsx`
- `apps/web/src/features/catalog-search/SearchResults.tsx`
- `apps/web/src/features/catalog-search/SearchState.tsx`
- `apps/web/src/features/catalog-search/SearchSuggestions.tsx`
- `apps/web/src/features/catalog/CatalogCard.tsx`
- `apps/web/src/features/public-store/publicLayoutContent.ts`
- `apps/web/src/i18n/publicContent.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/tests/e2e/catalog-search.spec.ts`
- `apps/web/tests/e2e/catalog-listing.spec.ts`
- `apps/web/tests/e2e/foundation.spec.ts`
- `apps/web/tests/unit/catalog-validation.test.mjs`
- `packages/contracts/README.md`
- `packages/contracts/catalog-public-v1.openapi.yaml`

## Change Log

- 2026-08-29: Implementada busca pública por intenção ponta a ponta no Laravel/PostgreSQL, BFF e Next.js SSR, com contratos, segurança, acessibilidade e testes completos.
- 2026-08-29: Revisão adversarial pós-implementação concluída; 12 achados corrigidos, incluindo validação semântica BFF e uso comprovado do índice pela consulta real.
- 2026-09-02: Resolvidos 14 achados do rerun adversarial da story em review; validações completas passaram com Docker/PostgreSQL restaurado, build de produção e E2E completo em modo produção.
