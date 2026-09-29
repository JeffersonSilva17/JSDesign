---
baseline_commit: 062e26b
---

# Story 3.1: Exibir página própria de produto com diferenças entre modelos

Status: done

## Story

Como cliente da JS Designs,
quero abrir uma página própria do produto e comparar os modelos disponíveis,
para escolher corretamente a versão desejada antes de comprar.

Requisitos cobertos: FR-7, FR-8, FR-9; UX-DR11, UX-DR12, UX-DR13, UX-DR15; AD-1, AD-2, AD-9, AD-11, AD-13. Dependências: Épico 2 concluído; página pública de detalhe, leitura BFF e SEO/canonical já existem. Esta especificação não implementa carrinho, checkout, cálculo final de preço, preço por modelo, personalização completa, miniatura paga ou sugestões complementares.

## Acceptance Criteria

1. **Página própria enriquecida para produto publicado**
   - **Given** um produto publicado, **When** a cliente acessar `/produtos/<slug>`, **Then** a página deve exibir galeria pública, nome, descrição, categoria, modalidade, disponibilidade, preço base em EUR, informações de produção/entrega e CTA compatível com a modalidade.
   - **And** se o produto tiver mais de um modelo, o preço deve ser apresentado como "A partir de <preço base>" e nunca como subtotal/modelo; se houver um modelo único, usar "Preço base <preço>".
   - **And** produto não publicado, inexistente ou slug inválido continua em `notFound()`/noindex, sem reaproveitar dados de outro produto.
   - **And** timeout, payload inválido ou API indisponível continuam exibindo estado sanitizado de catálogo indisponível, sem stack trace, SQL, path interno, campo administrativo ou metadata comercial falsa.

2. **Diferença clara entre modalidades**
   - **Given** o produto é `physical_personalized`, **When** a página renderizar, **Then** deve ficar claro que é físico/personalizado, produzido sob encomenda quando aplicável, com prazo, materiais, acabamento e composição/conteúdo quando disponíveis e CTA "Personalizar e comprar".
   - **Given** o produto é `digital_personalized`, **When** a página renderizar, **Then** deve informar que não há entrega imediata e que haverá edição/criação, prévia e aprovação antes da entrega final; CTA "Escolher modelo".
   - **Given** o produto é `digital_ready`, **When** a página renderizar, **Then** deve informar "Produto digital", "Download imediato" quando `is_immediate_delivery=true`, compatibilidade com Silhouette Studio quando disponível, ausência de personalização/briefing/aprovação, condições de uso resumidas e CTA "Comprar agora".
   - **And** o texto de digital pronto deve incluir, quando `usage_terms` existir, um resumo visível "Uso permitido em peças físicas; redistribuição digital do arquivo não autorizada" ou texto equivalente vindo do contrato, além de link para `/termos`; não precisa coletar consentimento de download nesta story.

3. **Comparação de modelos antes do carrinho**
   - **Given** um produto com modelos/versões públicos, **When** a cliente visualizar a página, **Then** os modelos aparecem em lista comparável com nome, diferença escrita, indicador visual acessível e estado selecionado.
   - **And** a escolha do modelo deve acontecer antes de qualquer navegação futura para carrinho/configuração e deve ser preservada em URL same-origin como `?modelo=<model_key>` no CTA.
   - **And** se houver apenas um modelo, a página deve apresentar esse modelo como selecionado/único sem bloco vazio ou promessa de comparação inexistente.
   - **And** o contrato deve limitar modelos a no máximo 12 itens por produto, com `key` público estável, `label` até 120 caracteres, `difference` até 360 caracteres, `is_default`, `image` opcional validada, `sort_order` determinístico e exatamente um modelo padrão por produto.
   - **And** sem JavaScript, todos os modelos continuam visíveis como links para a própria página com `?modelo=<model_key>`, o modelo selecionado é derivado da query válida, e query ausente/inválida seleciona o modelo padrão sem noindexar a página.
   - **And** JavaScript só pode melhorar a seleção local usando radio group/fieldset ou controle semanticamente equivalente; a página deve funcionar com links e HTML renderizado pelo servidor.
   - **And** a implementação deve criar uma migration incremental `catalog_product_models` para dados reais de modelos públicos; `variants_reference` permanece legado/admin e não é fonte pública nesta story.

4. **Contrato público allowlist e dados de catálogo**
   - **Given** a Laravel API responder o detalhe público, **When** o payload for serializado, **Then** somente campos públicos allowlistados podem sair: galeria pública resolvida, materiais, composição/conteúdo, descrição de arquivo, termos de uso, quantidade mínima, prazo, compatibilidade e modelos públicos.
   - **And** `ProductDetail` deve manter o shape de `ProductCard` intacto para listagem/busca e acrescentar somente no detalhe: `gallery`, `materials`, `composition`, `file_description`, `usage_terms`, `minimum_quantity` e `models`.
   - **And** `models` deve ser array obrigatório com 1 a 12 itens; cada item tem exatamente `key`, `label`, `difference`, `is_default`, `image`; `image` usa o mesmo schema `Image` ou `null`. Não há preço, disponibilidade ou compatibilidade por modelo nesta story.
   - **And** `gallery` deve ser array obrigatório com 0 a 8 imagens, cada item no schema `Image`; a primeira imagem deve ser a primária segura quando ela existir, seguida das demais seguras por `sort_order` e `id`.
   - **And** OpenAPI e validadores runtime devem usar `additionalProperties: false`, limites de tamanho explícitos e enums existentes para cada objeto novo.
   - **And** personagens, aliases internos, `verification_status`, evidências de direitos, notas administrativas, `storage_reference`, caminhos privados e dados de cliente permanecem fora de API, BFF, HTML, metadata, JSON-LD e logs.
   - **And** qualquer extensão de payload deve atualizar `packages/contracts/catalog-public-v1.openapi.yaml`, validação runtime do BFF e testes HTTP exatos.

5. **Galeria pública segura e performática**
   - **Given** o produto possui uma ou mais imagens públicas aprovadas, **When** a página renderizar, **Then** a galeria deve usar somente paths públicos validados pelo resolver existente, com `alt_text`, ordem determinística e fallback editorial/estado sem imagem quando nenhuma imagem for segura.
   - **And** imagem insegura ou não resolvida deve ser omitida individualmente; a página mostra as imagens restantes e só usa o estado "imagem indisponível" se nenhuma imagem pública segura sobrar.
   - **And** a galeria pública deve ser limitada a no máximo 8 imagens por produto; excedentes não entram no payload público até existir paginação/estratégia aprovada.
   - **And** `primary_image` continua sendo o campo usado para metadata/social image; `gallery[0]` deve ser consistente com `primary_image` quando a imagem primária for segura.
   - **And** `next/image` deve preservar dimensões estáveis, `sizes` adequado e pai posicionado quando usar `fill`, evitando CLS e downloads desnecessários.
   - **And** não introduzir imagem remota arbitrária, `dangerouslyAllowSVG`, fetch de storage privado pelo navegador, base64 enviado pela API ou bypass do `FailClosedPublicCatalogImageResolver`.

6. **SEO e retorno preservados**
   - **Given** a página de produto atual já possui metadata, canonical limpo, JSON-LD e `return_to` seguro, **When** a tela for enriquecida, **Then** esses comportamentos continuam validados.
   - **And** `generateMetadata` e o corpo devem usar a mesma leitura/classificação memoizada do produto enriquecido; divergência de slug, payload inválido ou campos novos inválidos tornam a página indisponível/noindex, não metadata de produto parcial.
   - **And** `return_to` continua permitido apenas para `/produtos` e `/buscar` conforme parser existente; nunca entra em canonical, OG, JSON-LD ou sitemap.
   - **And** `modelo` é a única query nova permitida no detalhe; valor válido pode selecionar o modelo no corpo, mas canonical/OG/JSON-LD/sitemap continuam usando `/produtos/<slug>` sem query.
   - **And** dados estruturados não devem inventar `Offer`, estoque, SKU, avaliação, promoção ou prazo comercial que não esteja no contrato público implementado.

7. **UX mobile-first e acessível**
   - **Given** viewport de 320, 420, 760 e 1100 px, **When** a página renderizar, **Then** não deve haver overflow horizontal, sobreposição de texto/CTA/galeria ou alvo de toque menor que 44 x 44 px.
   - **And** os modelos devem ser selecionáveis por teclado e leitor de tela por links SSR e, se houver melhoria client-side, por `fieldset`/radio group ou componente com semântica equivalente; o estado selecionado deve usar `aria-current` ou `aria-checked` mais texto visível, não apenas cor.
   - **And** mudanças client-side de modelo que alterem resumo textual devem atualizar uma região `aria-live="polite"` curta; sem JavaScript, a mudança ocorre por navegação para a mesma página com `modelo`.
   - **And** textos visíveis devem vir do i18n/conteúdo público tipado, em português do Brasil, sem mojibake e sem textos hardcoded dispersos nos componentes.

8. **Regressões e limites de escopo**
   - **Given** as stories 2.1-2.4 estão concluídas, **When** 3.1 for implementada, **Then** listagem, busca, categorias, sitemap/robots, metadata SSR, retorno do detalhe, noindex de estados inválidos e imagem social continuam verdes.
   - **And** o Next.js/BFF não deve calcular preço final, desconto, frete, disponibilidade real de carrinho, cobrança de miniatura ou regra de modelos; ele apenas exibe dados públicos e solicita ações futuras.
   - **And** links/CTAs desta story apontam para `/carrinho?produto=<slug>&modelo=<model_key>` apenas como handoff futuro; enquanto `/carrinho` for placeholder, a página de produto deve mostrar texto próximo ao CTA avisando "A compra/configuração será ativada em etapa própria" e o placeholder do carrinho não deve criar item, subtotal ou checkout.

## Tasks / Subtasks

- [x] 1. Estender contrato público de produto somente com campos necessários à página própria (AC: 1, 3, 4)
  - [x] Definir shape de `ProductDetail` em OpenAPI para `gallery`, `materials`, `composition`, `file_description`, `usage_terms`, `minimum_quantity` e `models`, sem alterar o shape de `ProductCard`.
  - [x] Criar migration incremental `catalog_product_models` com `id`, `product_id`, `public_key`, `label`, `difference`, `image_reference`, `sort_order`, `is_default`, timestamps, unique `(product_id, public_key)`, unique parcial de default por produto e índice `(product_id, sort_order, id)`.
  - [x] Atualizar `PublicCatalogQuery::findPublishedBySlug`, `PostgresPublicCatalogQuery`, `PublicCatalogProductResource` e testes HTTP para published-only, allowlist exata e ausência de campos privados.
  - [x] Garantir que produtos publicados sem linhas em `catalog_product_models` exponham um modelo padrão derivado do produto (`key: "padrao"`, label editorial do i18n/contrato, difference curta), para manter `models` obrigatório e não quebrar catálogo existente.

- [x] 2. Atualizar BFF e validação runtime do detalhe (AC: 1, 4, 6)
  - [x] Ampliar tipos em `apps/web/src/bff/catalogApi.ts` e validação em `apps/web/src/bff/catalogValidation.ts`.
  - [x] Manter slug canonical, erro `not-found` versus `invalid-payload`, memoização por `readProduct`, proteção de `return_to` em `detailQuery` e parser estrito para `modelo`.
  - [x] Atualizar `catalogStructuredData`/metadata apenas com campos públicos verdadeiros; não adicionar `Offer` nesta story.

- [x] 3. Evoluir a página `/produtos/[slug]` como Server Component (AC: 1, 2, 3, 5, 6)
  - [x] Reestruturar `apps/web/src/app/(public)/produtos/[slug]/page.tsx` para galeria, summary-panel, diferenciação de modalidades e comparação/seleção de modelos.
  - [x] Criar componentes em `apps/web/src/features/catalog/` quando a página ficar grande o bastante; começar com links SSR para modelos e usar `'use client'` somente no menor componente necessário se a melhoria client-side for realmente implementada.
  - [x] Preservar o link "Voltar aos produtos" e o comportamento sem JavaScript já testado.
  - [x] Montar CTA com `/carrinho?produto=<slug>&modelo=<model_key>` usando somente slug/modelo validados e mensagem visível de handoff futuro enquanto o carrinho for placeholder.

- [x] 4. Atualizar conteúdo público/i18n e estilos (AC: 2, 3, 7, 8)
  - [x] Adicionar textos em `apps/web/src/i18n/publicContent.ts` e tipos em `publicContent.types.ts`.
  - [x] Ajustar `apps/web/src/app/globals.css` seguindo tokens existentes, sem card dentro de card, sem botões/textos quebrando em 320 px e sem WhatsApp flutuante.
  - [x] Diferenciar visualmente modalidades e modelos por texto, badges e hierarquia; não depender só de cor.

- [x] 5. Atualizar fixtures e testes (AC: 1-8)
  - [x] Atualizar `CatalogE2eSeeder` e testes de API para produtos físico personalizado, convite digital personalizado, digital pronto, galeria múltipla e produto sem modelos.
  - [x] Atualizar testes unitários BFF/SEO para o novo payload, rejeição de campos extras e ausência de dados privados.
  - [x] Testar payloads inválidos de modelo: `key` duplicado, mais de 12 modelos, ausência de default, múltiplos defaults, label vazio, difference acima de 360, imagem unsafe, campo extra e query `modelo` desconhecida.
  - [x] Atualizar Playwright em `catalog-listing.spec.ts`/`catalog-seo.spec.ts` ou criar spec dedicada para detalhe do produto, cobrindo 320/420/760/1100 px, teclado, no-JS com links de modelo, CTA com query preservada e estados degradados.

- [x] 6. Executar gates e registrar evidências (AC: 1-8)
  - [x] Backend: `composer test`, `vendor/bin/pint --test`, `composer audit --locked`.
  - [x] Frontend: `npm run test:bff`, `npm run test:seo`, `npm run lint`, `npm run typecheck`, `npm run build`, Playwright aplicável e `npm audit --audit-level=moderate`.
  - [x] Raiz/segurança: `git diff --check`, `node scripts/scan-sast.mjs` e `node scripts/scan-secrets.mjs` quando o ambiente permitir.

### Review Findings

- [x] [Review][Patch] Galeria parcial com `alt_text` vazio quebra o detalhe inteiro [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:469]
- [x] [Review][Patch] Modelos públicos inválidos são silenciados e substituídos por `padrao` [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:486]
- [x] [Review][Patch] Listagem e busca passaram a resolver imagens secundárias sem uso público [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:450]
- [x] [Review][Patch] `usage_terms` aceito pelo admin pode exceder o contrato público/BFF [apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogRules.php:35]
- [x] [Review][Patch] `image_reference` de modelo não fica restrito às imagens públicas do próprio produto [apps/api/database/migrations/2026_09_22_000001_create_catalog_product_models_table.php:18]
- [x] [Review][Patch] Dados E2E novos exibem mojibake em campos públicos [apps/api/database/seeders/CatalogE2eSeeder.php:52]
- [x] [Review][Patch] Cobertura obrigatória de bordas da página de detalhe e modelos inválidos está incompleta [apps/web/tests/e2e/catalog-listing.spec.ts:77]
- [x] [Review][Security] Campos privados aninhados em galeria/modelos poderiam vazar por adapter alternativo [apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php]
- [x] [Review][NFR] Prefetch de links visíveis do catálogo competia com navegação e deixava o gate de interação instável [apps/web/src/features/catalog/CatalogCard.tsx] [apps/web/src/features/catalog/CatalogFilters.tsx]
- [x] [Review][Dependency] Advisories baixos publicados em 2026-09-29 para Laravel/Flysystem corrigidos via Composer update [apps/api/composer.lock]

## Dev Notes

### Contexto de fonte

- O Épico 3 pede que a cliente entenda e configure lembrancinhas, convites e digitais antes do carrinho; Laravel controla precificação/configuração e Next.js exibe/coleta. [Source: _bmad-output/planning-artifacts/epics.md#Epic 3]
- Story 3.1 cobre página própria de produto, diferenças entre modelos, tipo de produto, produção/entrega e convite digital personalizado sem entrega imediata. [Source: _bmad-output/planning-artifacts/epics.md#Story 3.1]
- PRD exige página de produto físico com fotos reais, preço, quantidades, material, acabamento, conteúdo, personalização, prazo e entrega; convite deve explicar funcionamento/diferenciais/prazo/dados necessários; digital pronto deve explicar ausência de personalização, arquivos, Silhouette Studio, termos e entrega imediata. [Source: _bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md#FR-7]
- UX define `summary-panel`, `configurator-invite`, cards por modalidade, Produto Digital Pronto com selo forte e mobile-first desde 320 px. [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md#Components] [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md#Product-Specific Rules]
- Arquitetura fixa Browser -> Next.js BFF -> Laravel API -> PostgreSQL; BFF não contém regra de negócio principal; precificação/configuração são server-owned no Laravel. [Source: _bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md#AD-1] [Source: _bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md#AD-13]

### Estado atual do código

- `apps/web/src/app/(public)/produtos/[slug]/page.tsx` já busca `readProduct`, trata `CatalogApiError` `not-found`, preserva `return_to`, renderiza uma imagem primária, descrição, modalidade, disponibilidade, compatibilidade, preço e link de volta.
- `apps/web/src/features/catalog-seo/detailQuery.ts` é a proteção de retorno same-origin; não duplicar nem relaxar esse parser.
- `apps/web/src/features/catalog-seo/catalogMetadata.ts` e `catalogStructuredData.ts` controlam metadata/JSON-LD seguros. Manter a política de noindex/canonical da 2.4.
- `apps/web/src/bff/catalogValidation.ts` valida payload público estritamente e rejeita campos extras; qualquer campo novo precisa entrar ali e no OpenAPI.
- `apps/api/database/migrations/2026_08_13_000001_create_catalog_tables.php` já possui colunas de detalhe (`minimum_quantity`, `variants_reference`, `materials`, `composition`, `file_description`, `usage_terms`) e tabela de imagens. Não reescrever essa migration; criar migration incremental para `catalog_product_models`.
- `variants_reference` permanece legado/admin nesta story; não usar como fonte pública, parser transitório ou fallback para modelos.
- `PostgresPublicCatalogQuery::hydrate` hoje busca somente imagem primária e taxonomy pública; galeria/modelos exigem query adicional controlada e sem N+1.

### Regras de implementação

- Manter App Router; páginas/layouts são Server Components por padrão. Adicionar `'use client'` apenas se a seleção de modelo exigir estado local.
- O navegador nunca chama Laravel diretamente. Todas as leituras passam por `apps/web/src/bff/*` e `API_INTERNAL_URL` permanece server-side.
- Valores monetários continuam em `price_minor` + `currency`; não usar float e não calcular desconto/subtotal final no BFF.
- Personagens/ativos protegidos não são categoria pública. Não expor taxonomias `character`, `search_alias`, direitos, evidências ou notas de verificação.
- Galeria pública deve usar o resolver/validador de imagem atual; se múltiplas imagens forem expostas, cada URL precisa continuar validada por `PublicImagePath` e pelo validador BFF.
- Modelos/versões devem vir de `catalog_product_models` ou do modelo padrão derivado quando não houver linhas nessa tabela. Cada modelo público tem `key`, `label`, `difference`, `is_default` e `image`; não há preço, disponibilidade nem compatibilidade por modelo nesta story.
- Exigir exatamente um modelo padrão. Query `modelo` ausente ou inválida seleciona esse padrão no corpo; canonical/OG/JSON-LD continuam sem query.
- Se o produto não tiver modelos cadastrados, expor um modelo único derivado (`key: "padrao"`) no contrato e mostrar estado "modelo único" na UI; não exibir comparação vazia.
- CTAs desta story sempre apontam para `/carrinho?produto=<slug>&modelo=<model_key>` como handoff futuro. O carrinho permanece placeholder e não cria item/subtotal/checkout.
- Textos longos de descrição, materiais, composição, termos e diferenças de modelos precisam ter limites no contrato e truncamento/apresentação previsível na UI; metadata deve continuar usando descrição curta sanitizada, sem copiar termos extensos ou query strings.

### Arquivos prováveis

- Backend/API: `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogQuery.php`, `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php`, `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php`, `apps/api/tests/Feature/Catalog/PublicCatalogApiTest.php`, `apps/api/database/seeders/CatalogE2eSeeder.php`, nova migration incremental sob `apps/api/database/migrations/` para `catalog_product_models`, e `packages/contracts/catalog-public-v1.openapi.yaml`.
- Frontend/BFF: `apps/web/src/bff/catalogApi.ts`, `apps/web/src/bff/catalogValidation.ts`, `apps/web/src/features/catalog-seo/catalogStructuredData.ts`, `apps/web/src/app/(public)/produtos/[slug]/page.tsx`, possíveis novos componentes em `apps/web/src/features/catalog/`, `apps/web/src/i18n/publicContent.ts`, `apps/web/src/i18n/publicContent.types.ts`, `apps/web/src/app/globals.css`, testes em `apps/web/tests/unit/*.mjs` e `apps/web/tests/e2e/*.spec.ts`.

### Threat Modeling - STRIDE

#### Contexto de Segurança

- Atores: visitante anônima, crawler, Next.js BFF, Laravel API, PostgreSQL, agente de desenvolvimento.
- Ativos protegidos: dados administrativos do catálogo, evidências de direitos, paths de storage, API interna, futuras informações de carrinho/pedido, propriedade intelectual e imagens privadas.
- Pontos de entrada: slug público, queries `return_to` e `modelo`, payload Laravel, imagens públicas resolvidas, HTML/metadata/JSON-LD.
- Pontos de saída: resposta HTML, metadata social, JSON-LD, logs técnicos, resposta JSON pública do Laravel.
- Fronteiras de confiança: navegador/Next, Next/Laravel, Laravel/PostgreSQL, storage público/privado.

| Categoria | Risco | Mitigação exigida |
| --- | --- | --- |
| Spoofing | Host/retorno falsificado tenta contaminar canonical ou link de volta. | Usar `SITE_URL`/metadata existente e `detailQuery`; ignorar host não confiável para metadata. |
| Tampering | Slug/payload/modelo malformado altera página ou injeta HTML/JSON-LD. | Validar slug e query `modelo`, schema estrito no BFF, escape React/serializador JSON-LD existente e testes com texto hostil. |
| Repudiation | Mudanças de contrato sem rastreabilidade dificultam provar origem dos dados. | Atualizar OpenAPI, testes HTTP exatos e changelog da story; logs sem PII. |
| Information Disclosure | Campos admin, direitos, storage privado ou personagem protegido vazam no HTML/API. | Resource allowlist, taxonomy pública filtrada, testes negativos no JSON/HTML/metadata/JSON-LD. |
| Denial of Service | Galeria/modelos geram N+1, payload grande ou imagem arbitrária. | Limitar campos/itens, query batch, statement timeout existente, validação de path e testes de query budget quando houver nova tabela. |
| Elevation of Privilege | BFF ou UI passa a decidir regra de preço/configuração/carrinho. | Regra de domínio no Laravel; Next exibe apenas projeção pública e CTAs; testes/arquitetura impedem cálculo client-owned. |

### Testes esperados

- PHP Feature: detalhe com produto físico, convite personalizado, digital pronto, produto sem modelos, múltiplas imagens, draft/unpublished, imagem insegura, taxonomy protegida, modelos inválidos e adapter alternativo tentando vazar campos extras.
- TypeScript unit: `isCatalogProductEnvelope` aceita shape novo e rejeita campo extra, modelo duplicado, default ausente/múltiplo, imagem unsafe, slug divergente e textos fora de limite.
- Playwright: detalhe renderiza por modalidade, seleção de modelo por teclado, CTA correto com `produto` e `modelo`, link de volta, no-JS por links SSR, reflow 320/420/760/1100, ausência de overflow e estados indisponíveis sanitizados.
- SEO: canonical/OG/JSON-LD não incluem `return_to`, query de busca, campos privados ou `Offer` inventado; produto inválido continua 404/noindex.
- Performance/query budget: detalhe com 8 imagens e 12 modelos não pode introduzir N+1; teste HTTP ou dedicado deve provar no máximo 8 queries SQL para a leitura do detalhe, incluindo timeout/configuração transacional se ela aparecer no query log.

### Informação técnica atualizada

- Next.js 16.3.5 está instalado; `generateMetadata` é server-only, aceita `params`/`searchParams` como promises no App Router e pode usar `React.cache` quando `fetch` direto não é a chave de memoização. [Source: apps/web/node_modules/next/dist/docs/01-app/03-api-reference/04-functions/generate-metadata.md] [Source: https://nextjs.org/docs/app/api-reference/functions/generate-metadata]
- `next/image` em Next 16 exige `alt`, usa `preload` em vez de `priority`, requer pai posicionado para `fill` e recomenda `sizes` para layouts responsivos. [Source: apps/web/node_modules/next/dist/docs/01-app/03-api-reference/02-components/image.md] [Source: https://nextjs.org/docs/app/api-reference/components/image]
- Laravel JSON Resources centralizam transformação `toArray` para payloads de API; preservar `PublicCatalogProductResource` como allowlist explícita em vez de serializar model/query cru. [Source: https://github.com/laravel/docs/blob/13.x/eloquent-resources.md]
- Antes de escrever código em `apps/web`, reler docs relevantes sob `apps/web/node_modules/next/dist/docs/`, conforme `apps/web/AGENTS.md`.

### Aprendizados recentes e git

- O merge mais recente (`062e26b`) integra `story_2_4`; a implementação de 2.4 mexeu em página de detalhe, metadata/SEO, OpenAPI, validação BFF, testes e gates de segurança.
- A story 2.4 deixou como guardrail: não inventar `Offer`, SKU, estoque, promoção ou compra/configuração completa em dados estruturados antes dos épicos de produto/carrinho.
- Stories 2.3/2.4 reforçaram validação semântica entre consulta e payload; não confiar só em shape JSON.
- Padrão de teste atual cobre HTTP Laravel, validação BFF por Node test e Playwright SSR/no-JS/reflow. Reutilizar esses estilos em vez de criar harness paralelo.

### Condições para Desenvolvimento

- Pronta para implementação. Gate de segurança e revisão adversarial documental passaram sem alto/médio risco aberto.
- Se uma decisão de modelo/variant exigir alteração arquitetural ampla ou `implementation_plan.md`, apresentar o artefato concreto para revisão humana antes de implementar.
- Funcionalidade transacional permanece fora desta story: carrinho real, cálculo de subtotal por quantidade, miniatura paga e complementares pertencem às próximas stories do Épico 3.

## Security Gate - bmad-review-security

- Status: aprovado em 2026-09-22.
- Relatório: `reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-security.md`.
- Resultado: Sem alto, médio ou baixo risco confirmado na revisão documental. SAST, secret scan, npm audit, composer audit e `git diff --check` executados com sucesso.
- Revalidação pós-rerun em 2026-09-22: aprovada com ressalva operacional; `git diff --check` passou, mas `node scripts/scan-secrets.mjs` não executou porque o Docker Desktop/Linux engine estava indisponível. Não há alto/médio risco novo nas correções documentais.
- Condição para a implementação: reexecutar `bmad-review-security` sobre o diff/código antes de concluir a story.
- Revalidação pós-correção em 2026-09-29: aprovada. Relatório: `reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-security-postpatch-2026-09-29.md`.
- Resultado: Sem alto/médio risco aberto. SAST, secret scan, npm audit, composer audit, testes backend, E2E e SEO concluídos com sucesso; advisories baixos de dependência PHP corrigidos por atualização de lock.

## Story Review Gate - bmad-review-adversarial-general

- Status: aprovado em 2026-09-22.
- Relatório: `reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-adversarial.md`.
- Resultado: 12 achados documentais aplicados diretamente na story; nenhuma pendência de julgamento humano.
- Rerun: `reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-adversarial-rerun-2026-09-22.md`.
- Resultado do rerun: 15 achados adicionais corrigidos na story em 2026-09-22, fechando decisões de modelagem, CTA, query `modelo`, galeria, contrato e testes.

## Dev Agent Record

### Agent Model Used

Codex (criação de contexto).

### Debug Log References

- 2026-09-22: implementação iniciada via `bmad-dev-story`; sprint/story marcadas como `in-progress`, preservando `baseline_commit`.
- 2026-09-22: adicionados migration incremental `catalog_product_models`, hidratação pública de galeria/modelos, campos de detalhe allowlistados, OpenAPI, validação runtime BFF, parser `modelo`, página SSR de detalhe, i18n, CSS, fixtures e testes.
- 2026-09-22: validações concluídas com sucesso: `npm run test:bff`, `npm run test:seo`, `npm run lint`, `npm run typecheck`, `npm run build` com `SITE_URL`/SEO/API explícitos, `npm audit --audit-level=moderate`, `composer audit --locked`, `vendor/bin/pint --test`, `git diff --check`, `php -l` nos arquivos PHP alterados.
- 2026-09-22: validações bloqueadas pelo ambiente: Docker Desktop/Linux engine indisponível; SAST/secret scan versionados falharam por Docker ausente; PostgreSQL `jsdesign_test` recusou conexão em `127.0.0.1:5432`, bloqueando testes Feature Laravel, `composer test` completo e Playwright.
- 2026-09-22: `bmad-review-security` executado sobre o diff de implementação; relatório `reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-security-implementation-2026-09-22.md`; gate bloqueado por scans/testes obrigatórios não executados no ambiente.
- 2026-09-28: `bmad-code-review` executado sobre a implementação; 7 achados de patch aplicados para galeria/modelos/imagens/limites/fixtures/testes. Validações concluídas com sucesso: `php -l` nos arquivos PHP alterados, `npm run test:bff`, `npm run test:seo`, `npm run lint`, `npm run typecheck`, `npm run build`, `vendor/bin/pint --test`, `composer audit --locked`, `npm audit --audit-level=moderate`, `git diff --check`.
- 2026-09-28: testes Feature Laravel/Playwright e `composer test` completo seguem bloqueados porque `127.0.0.1:5432` não aceita conexão (`TcpTestSucceeded: False`); tentativa focada de PHPUnit foi interrompida após timeouts/erros de banco.
- 2026-09-29: Docker Desktop/PostgreSQL/Redis disponíveis; gates bloqueados reexecutados. Correções pós-review adicionadas para allowlist aninhada de imagens/modelos, cobertura de query budget/galeria/modelos, ajuste mobile da galeria e desativação de prefetch em links de filtro/card para estabilizar o gate de interação.
- 2026-09-29: `composer audit` encontrou advisories baixos recém-publicados em `laravel/framework` e `league/flysystem`; atualizado lock/vendor para Laravel `13.34.0` e Flysystem `3.36.0`; audit e `composer test` passaram após o update.
- 2026-09-29: validações finais concluídas: `composer test` (145 passed, 1 skipped), `vendor/bin/pint --test`, `composer audit --locked --no-interaction`, `npm run test:bff`, `npm run test:seo`, `npm run lint`, `npm run typecheck`, `npm run build` com SEO off/on, Playwright completo (70 passed), Playwright SEO com SEO on (6 passed), `npm audit --audit-level=moderate`, `node scripts/scan-sast.mjs`, `node scripts/scan-secrets.mjs`, `git diff --check`.

- 2026-09-22: `python` indisponível para `resolve_customization.py`; fallback manual aplicado com `customize.toml`. Sem activation prepend/append/on_complete e sem overrides locais de `bmad-create-story`.
- 2026-09-22: carregados sprint status, contexto persistente, PRD/adendo, épicos, arquitetura, UX, código atual de catálogo, testes, OpenAPI, migration, seeders, docs locais do Next e fontes oficiais.
- 2026-09-22: security gate executado com Semgrep/SAST, secret scan, npm audit, composer audit e diff check. Revisão adversarial geral executada e correções documentais aplicadas.
- 2026-09-22: rerun adversarial solicitado pelo usuário; achados aplicados diretamente. Decisões fechadas: `catalog_product_models`, CTA `/carrinho?produto=<slug>&modelo=<key>`, query `modelo`, `ProductDetail` enriquecido sem alterar `ProductCard`.

### Completion Notes List

- Implementação funcional concluída e story promovida para `done` após os gates obrigatórios de Docker/PostgreSQL, Playwright e segurança passarem em 2026-09-29.
- Achados de code review de 2026-09-28 e achados pós-review de 2026-09-29 foram tratados; não há alto/médio risco aberto no gate `bmad-review-security`.
- `ProductDetail` foi enriquecido sem alterar `ProductCard`; `models` permanece obrigatório com fallback público `padrao`; galeria e imagens de modelo passam pelo resolver/validador público.
- A página `/produtos/[slug]` renderiza galeria, diferenciação de modalidade, modelos por links SSR, query `modelo` e CTA futuro `/carrinho?produto=<slug>&modelo=<key>`, sem implementar carrinho real.
- Links de filtros e cards do catálogo usam `prefetch={false}` para evitar prefetch massivo de rotas server-side em páginas densas e preservar o gate de interação.

- Context engine analysis concluída; story security gate passed - ready for development.

### File List

- `_bmad-output/implementation-artifacts/reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-security-implementation-2026-09-22.md`
- `_bmad-output/implementation-artifacts/reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-security-postpatch-2026-09-29.md`
- `apps/api/composer.lock`
- `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php`
- `apps/api/database/migrations/2026_09_22_000001_create_catalog_product_models_table.php`
- `apps/api/database/seeders/CatalogE2eSeeder.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Files/TestingPublicCatalogImageResolver.php`
- `apps/api/tests/Feature/Catalog/CatalogAdminApiTest.php`
- `apps/api/tests/Feature/Catalog/PublicCatalogApiTest.php`
- `apps/web/src/app/(public)/produtos/[slug]/page.tsx`
- `apps/web/src/app/globals.css`
- `apps/web/src/bff/catalogApi.ts`
- `apps/web/src/bff/catalogValidation.ts`
- `apps/web/src/features/catalog-seo/detailQuery.ts`
- `apps/web/src/features/catalog/CatalogCard.tsx`
- `apps/web/src/features/catalog/CatalogFilters.tsx`
- `apps/web/src/i18n/publicContent.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/tests/e2e/catalog-listing.spec.ts`
- `apps/web/tests/e2e/catalog-seo.spec.ts`
- `apps/web/tests/unit/catalog-seo.test.mjs`
- `apps/web/tests/unit/catalog-validation.test.mjs`
- `packages/contracts/catalog-public-v1.openapi.yaml`
- `packages/contracts/catalog-admin-v1.openapi.yaml`

- `_bmad-output/implementation-artifacts/3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md`
- `_bmad-output/implementation-artifacts/reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-security.md`
- `_bmad-output/implementation-artifacts/reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-adversarial.md`
- `_bmad-output/implementation-artifacts/reviews/review-3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos-adversarial-rerun-2026-09-22.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`

## Change Log

- 2026-09-22: implementação de contrato/API/BFF/UI/testes adicionada; story permanece in-progress por bloqueio de ambiente nos gates obrigatórios.
- 2026-09-29: gates obrigatórios e revisão de segurança pós-correção concluídos; story marcada como done.

- 2026-09-22: story criada em draft com escopo, ACs, tasks, dev notes, STRIDE e gates pendentes.
- 2026-09-22: revisão adversarial e security gate executados; story promovida para ready-for-dev.
- 2026-09-22: aplicadas todas as correções do rerun adversarial solicitado pelo usuário; story permanece ready-for-dev com comportamento de modelos, CTA, contrato e testes determinados.
