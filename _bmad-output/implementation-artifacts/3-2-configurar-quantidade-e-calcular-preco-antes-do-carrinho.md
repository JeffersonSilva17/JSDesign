# Story 3.2: Configurar quantidade e calcular preço antes do carrinho

---
baseline_commit: 830baa8195d02651159c83abb8a3eb7f165279d6
---

Status: done

## Story

Como cliente comprando lembrancinhas ou itens personalizados,
quero escolher a quantidade e ver o preço atualizado,
para entender o valor antes de adicionar o produto ao carrinho.

## Acceptance Criteria

1. **Given** um produto público que aceita quantidade, **when** a cliente informar uma quantidade válida na página do produto, **then** a interface deve enviar ao BFF somente `product_slug`, `model_key` quando o produto tiver modelos, `quantity` e `currency`, e exibir os valores retornados pelo Laravel: preço unitário aplicável, desconto, subtotal anterior ao desconto, total e moeda.
2. **Given** uma quantidade elegível para uma faixa, **when** a cotação for calculada, **then** o Laravel deve selecionar exatamente uma faixa cujos limites inclusivos contenham a quantidade; a faixa define o preço unitário final em EUR, o subtotal é preço base vezes quantidade, o desconto é subtotal menos preço unitário aplicado vezes quantidade, e o total é subtotal menos desconto. Sem faixa elegível, aplica-se o preço base sem desconto.
3. **Given** um produto `digital_ready`, **when** for cotado, **then** a única quantidade aceita é 1. Para produtos que aceitam quantidade, zero, fração, valor não inteiro, quantidade abaixo do mínimo, acima do máximo efetivo, representação numérica fora do formato inteiro ou corpo inválido devem receber erro sanitizado; a UI bloqueia a cotação e informa os limites públicos.
4. **Given** produto inexistente, não publicado, despublicado ou indisponível, ou modelo inválido, **when** uma cotação for solicitada, **then** o Laravel deve negar a operação sem revelar dados administrativos. Inexistente, não publicado, despublicado e indisponível compartilham `404 quote_unavailable`; modelo inválido retorna `422 invalid_model`. Moeda diferente de EUR retorna `422 unsupported_currency`. O BFF apresenta estado recuperável sem total inventado.
5. **Given** uma solicitação válida repetida ou concorrente, **when** produto, modelo, quantidade, moeda e `pricing_rule_version` forem os mesmos, **then** o resultado deve ser idêntico. A cotação é somente leitura, calculada a partir de um snapshot consistente, não reserva disponibilidade nem cria item, carrinho, pedido, desconto persistido ou cobrança. O carrinho deve recalcular preço e disponibilidade no fluxo futuro; não deve confiar nos valores desta cotação.
6. **Given** a cotação retornada, **when** a cliente visualizar a página em 320 px, teclado ou leitor de tela, **then** quantidade, moeda, subtotal, desconto e mensagens de validação devem ter nomes acessíveis, foco visível, região dinâmica anunciável e não causar overflow; o CTA deve dizer “Continuar para configuração” e deixar claro que ainda não adicionou item ao carrinho.
7. **Given** a arquitetura do produto, **when** o preço for exibido, **then** Next.js/BFF deve apenas encaminhar entrada e apresentar o contrato validado; nenhuma regra final de preço, desconto, mínimo ou moeda pode existir hardcoded no frontend.

## Tasks / Subtasks

- [x] Modelar o domínio de cotação no módulo Laravel `Pricing` (AC: 1, 2, 3, 4, 5, 7)
  - [x] Criar value objects/DTOs para quantidade inteira positiva, moeda EUR, dinheiro em unidade mínima e resultado de cotação.
  - [x] Reutilizar `CatalogProduct`, `Money`, `minimum_quantity`, modalidade, disponibilidade e publicação existentes; não duplicar regra no BFF.
  - [x] Manter `pricing_rule_version` monotônica e incrementar quando preço base, mínimo, máximo, faixa, publicação ou disponibilidade mudar. Não aceitar essa versão como entrada da cotação; o carrinho futuro recalcula com a versão atual.
  - [x] Antes de qualquer multiplicação, validar `quantity <= intdiv(PHP_INT_MAX, unit_price_minor)`; se exceder, responder erro sanitizado sem calcular ou truncar.
- [x] Persistir/configurar faixas progressivas de preço de forma incremental (AC: 1, 2, 5)
  - [x] Criar migrations incrementais para `pricing_product_rules` (FK única para `catalog_products`, `maximum_quantity` nullable e `version` positiva) e `pricing_quantity_tiers` (FK da regra, `minimum_quantity`, `maximum_quantity` nullable e `unit_price_minor`); PostgreSQL é a fonte de verdade.
  - [x] Interpretar limites de faixa como inclusivos; `maximum_quantity=null` significa sem teto da faixa. Rejeitar sobreposição, intervalos invertidos, preço negativo, preço de faixa acima do preço base ou preço de uma faixa maior que o da faixa anterior. Ordenar por mínimo crescente; quantidades em lacunas usam preço base sem desconto.
  - [x] Usar máximo efetivo `min(maximum_quantity configurado, 10000)`; sem máximo configurado, usar 10000. Mínimo efetivo é `minimum_quantity` público ou 1 quando nulo; `digital_ready` usa mínimo e máximo iguais a 1. Rejeitar configuração cujo máximo efetivo seja inferior ao mínimo.
  - [x] As faixas desta story são por produto e valem igualmente para todos os modelos públicos. Validar `model_key` contra o catálogo, mas não aceitar preço específico por modelo; preço por modelo exige outra story e contrato.
  - [x] Não reescrever migrations compartilhadas. Administração completa de regras/faixas permanece na Story 8.2; nesta story, usar fixtures/seed controlado e leitura pública.
- [x] Expor contrato REST versionado para cotação (AC: 1–5, 7)
  - [x] Definir `POST /api/v1/pricing/quotes` em `packages/contracts/pricing-v1.openapi.yaml`: JSON estrito `product_slug` (string), `model_key` opcional (string), `quantity` (integer) e `currency` (const `EUR`); `additionalProperties: false`; payload máximo 4 KiB.
  - [x] Documentar resposta estrita com `product_slug`, `model_key` nullable, `quantity`, `minimum_quantity`, `maximum_quantity`, `base_unit_price_minor`, `unit_price_minor`, `subtotal_minor`, `discount_minor`, `total_minor`, `currency`, `pricing_rule_version` positiva e `applied_tier` nullable contendo somente limites públicos e preço unitário aplicado. Todos os valores monetários são inteiros não negativos em centavos EUR.
  - [x] Documentar erros: `400 invalid_request`, `422 invalid_quantity|invalid_model|unsupported_currency`, `404 quote_unavailable`, `429 rate_limited`, `503 quote_unavailable_temporarily`; corpo allowlist `{ error: { code, message, correlation_id } }`, sem detalhes internos. O BFF mapeia indisponibilidade/timeout do Laravel para 503, preserva somente códigos públicos permitidos e traduz cada código explicitamente para os idiomas suportados.
  - [x] Rejeitar campos extras; não aceitar preço, desconto, versão ou limite informado pelo cliente. Não existe conflito de versão neste endpoint: a versão é gerada e devolvida pelo servidor, e o carrinho futuro sempre recalcula.
  - [x] Ler produto, publicação, disponibilidade e configuração de preço em uma transação PostgreSQL com snapshot consistente; a revisão retornada cobre todos os dados comerciais usados no cálculo.
  - [x] Usar caso de uso/porta de aplicação e Resources HTTP; controller não consulta Eloquent diretamente.
- [x] Integrar Next.js BFF à página de produto existente (AC: 1, 3, 4, 6, 7)
  - [x] Adicionar rota same-origin `POST /api/pricing/quotes` e função server-side em `apps/web/src/bff/`; chamar Laravel com timeout total de 3 s, validar `unknown` com type guard estrito e nunca expor `API_INTERNAL_URL`.
  - [x] Criar componente mínimo client-side somente para quantidade/estado da cotação; manter página e metadata como Server Components e não calcular valores no navegador. Debounce de 250 ms; cancelar requisição anterior e ainda ignorar respostas cujo identificador ou quantidade não correspondam à edição atual.
  - [x] Renderizar `summary-panel`/`configurator-physical` conforme tokens e conteúdo i18n existentes; preservar CTA/modelo e handoff futuro da Story 3.1.
  - [x] Não adicionar item ao carrinho nem implementar personalização, miniatura, cupom, frete ou checkout; esses comportamentos pertencem às Stories 3.3, 3.4 e Epic 4.
- [x] Atualizar contratos, conteúdo e observabilidade segura (AC: 1–7)
  - [x] Atualizar OpenAPI em `packages/contracts`, tipos/validação BFF e textos pt-BR; manter tradução explícita para cada idioma suportado, sem fallback silencioso em outro idioma.
  - [x] Registrar apenas identificador técnico/correlation id, resultado e motivo sanitizado; não registrar e-mail, endereço, payload completo ou dados de pagamento.
- [x] Cobrir critérios e regressões com testes (AC: 1–7)
  - [x] Unitários PHP para cada limite inclusivo de faixa, lacuna que retorna preço base, validação de configuração, mínimo/máximo, `digital_ready=1`, moeda, modelo, overflow e determinismo; validar fórmula inteira sem regra de arredondamento implícita.
  - [x] Feature HTTP Laravel para status/publicação e datas-limite, indisponibilidade, modelo e moeda inválidos, campos extras, corpo acima de 4 KiB, payloads/erros exatos, rate limit (60 req/min/IP) e ausência de campos administrativos.
  - [x] Testes BFF/TypeScript para payload válido, cada código de erro, limite de campos, timeout de 3 s, `Cache-Control: no-store`, tradução e ausência de cálculo local.
  - [x] Playwright deve atrasar/resolver respostas fora de ordem e provar que somente a cotação da quantidade atual é renderizada; cobrir teclado, anúncio acessível, limites, reflow a 320 px, timeout/erro recuperável e ausência de mutação do carrinho. Manter smoke atravessando Next.js BFF e Laravel reais.

## Dev Notes

### Contexto e dependências

- O Epic 3 mantém configuração e preço antes do carrinho; Laravel controla precificação/configuração e Next.js exibe/coleta. [Source: `_bmad-output/planning-artifacts/epics.md#Epic 3`]
- Os requisitos desta story são FR-11/FR-14, AR-16 e UX-DR13/UX-DR14/UX-DR31. [Source: `_bmad-output/planning-artifacts/epics.md#Story 3.2`]
- Story 3.1 já entrega a página `apps/web/src/app/(public)/produtos/[slug]/page.tsx`, escolha de modelo e CTA futuro com `produto`/`modelo`; estender o fluxo sem alterar o contrato de seleção de modelo. [Source: `_bmad-output/implementation-artifacts/3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md#Estado atual do código`]
- O schema atual de catálogo possui `price_minor`, `currency` (EUR), `minimum_quantity`, modalidade e disponibilidade. A migration existente é compartilhada: qualquer faixa nova deve ser incremental. [Source: `apps/api/database/migrations/2026_08_13_000001_create_catalog_tables.php`]
- `pricing_product_rules.product_id` é único e referencia `catalog_products`; `pricing_quantity_tiers` referencia a regra e tem constraint de não negatividade/intervalo. Faixas são do produto, não do modelo; revisão de preço muda em qualquer alteração de preço, elegibilidade ou publicação.
- A administração de preço/faixas pertence à Story 8.2. Não criar painel admin nesta story; usar seed/fixture explícito para provar a leitura do domínio.

### Guardrails de arquitetura

- Fluxo obrigatório: navegador → Next.js/BFF → Laravel `/api/v1` → PostgreSQL. O navegador nunca chama a API interna diretamente.
- Preço, elegibilidade, moeda, disponibilidade, arredondamento e descontos são server-owned. BFF/UI não podem reproduzir regra comercial nem usar `number`/float para dinheiro; contratos usam inteiros em unidade mínima + moeda.
- O contrato OpenAPI tem schemas de request, sucesso e erro fechados. Sucesso inclui preço base/aplicável, subtotal antes do desconto, desconto, total, faixa pública, limites e revisão; erros contêm código estável, mensagem genérica e correlation id. O BFF localiza a mensagem.
- `maximum_quantity` é configuração explícita na regra de preço, limitada ao máximo absoluto 10000, e é devolvido na cotação; não deixar multiplicações de inteiros grandes alcançarem o cálculo.
- Produtos cotáveis são exatamente os que têm `status=published`, `published_at <= now UTC`, `unpublished_at` nulo ou futuro e disponibilidade diferente de `unavailable`. Cotação não reserva estoque; `made_to_order` é cotável se respeitar mínimo/máximo.
- Preço por modelo não faz parte desta story. Todos os modelos públicos de um produto compartilham preço base e faixas; `model_key` é validado e retornado, sem alterar o cálculo.
- Organizar Laravel em `Modules/Pricing/{Domain,Application,Infrastructure,Interfaces/Http}` e manter Eloquent/Query Builder restritos à Infrastructure.
- A cotação é leitura calculada, sem persistir carrinho/pedido. O endpoint deve ser seguro para retry e não liberar pagamento, briefing, produção ou entrega.
- Para produtos sem quantidade configurável (digital pronto, por exemplo), o domínio deve rejeitar quantidade diferente da regra pública definida; não aplicar desconto progressivo por inferência do frontend.
- A rota Laravel e a rota BFF devem responder `Cache-Control: no-store`. O rate limiter nomeado `public-pricing-quote` limita a 60 req/min por IP usando o mecanismo Laravel existente; o BFF expira após 3 s. A UI usa debounce de 250 ms, aborta chamadas antigas e confere sequência e quantidade antes de renderizar resposta.
- Preservar allowlists públicas do catálogo. Não devolver direitos, taxonomias protegidas, custo interno, margens, regras administrativas, estoque interno ou identificadores de banco.
- Usar UTC nos timestamps/versionamento, correlation id sem PII e erros públicos estáveis. Timeout e falha do Laravel devem resultar em estado recuperável, nunca em preço estimado.

### Arquivos e padrões prováveis

- Backend: novo módulo `apps/api/app/Modules/Pricing/`, migration em `apps/api/database/migrations/`, bindings em `apps/api/app/Providers/`, rotas sob `apps/api/routes/`, testes em `apps/api/tests/Unit/Modules/Pricing` e `apps/api/tests/Feature/Pricing`.
- Frontend/BFF: `apps/web/src/bff/`, `apps/web/src/app/(public)/produtos/[slug]/page.tsx`, novo componente em `apps/web/src/features/pricing/` ou `catalog/`, i18n em `apps/web/src/i18n/`, estilos em `apps/web/src/app/globals.css`.
- Contrato: `packages/contracts/catalog-public-v1.openapi.yaml` ou novo `pricing-v1.openapi.yaml`, mantendo convenção e testes de contrato.
- Antes de alterar frontend, reler documentação local do Next em `apps/web/node_modules/next/dist/docs/`; preservar `detailQuery`, metadata, noindex/canonical e prefetch controlado de 3.1.

### STRIDE e controles obrigatórios

| Ameaça | Controle exigido |
| --- | --- |
| Spoofing | Endpoint público não confia em identidade enviada; autenticação não é necessária para cotação, mas acesso a qualquer dado não público deve falhar fechado. |
| Tampering | Form Request + schema BFF estritos; validar slug/modelo/moeda/quantidade e versão; não aceitar preço enviado pelo cliente. |
| Repudiation | Correlation id, versão da regra e logs sanitizados; não registrar PII nem payload financeiro completo. |
| Information Disclosure | Resource allowlist; mensagens indistinguíveis para produto inexistente/não publicado quando necessário; sem SQL, stack trace, margem ou dados administrativos. |
| Denial of Service | Limite absoluto 10000, corpo até 4 KiB, 60 req/min/IP, timeout BFF 3 s, consulta indexada e sem N+1; validar overflow antes da multiplicação. |
| Elevation of Privilege | Somente catálogo publicado e disponível; nenhuma rota admin reaproveitada sem RBAC; BFF não concede autorização nem expõe segredo server-to-server. |

### Validação e gates

- Executar `composer test`, `vendor/bin/pint --test`, `npm run lint`, `npm run typecheck`, `npm run build` e Playwright aplicável.
- Executar `composer audit --locked`, `npm audit --audit-level=moderate`, `node scripts/scan-sast.mjs`, `node scripts/scan-secrets.mjs` e `git diff --check`; registrar explicitamente qualquer ferramenta indisponível.
- Não adicionar dependência sem necessidade e não alterar lockfiles fora do escopo.
- A rota pública deve usar o mecanismo de rate limit existente e timeout server-side; se a implementação não puder provar esses controles, registrar a lacuna como risco residual antes do merge.

## Security Gate - bmad-review-security

- Status: aprovado para desenvolvimento em 2026-10-06; repetir a revisão de segurança sobre a implementação antes de concluir a story.
- Escopo: story, contratos de dependência, `apps/web/package-lock.json`, SAST, secret scan, composer audit, npm audit e governança `AGENTS.md`.
- Resultado: Next está em `16.3.8`; a cadeia de lint com `braces@3.0.3` foi removida do lockfile, substituída por ESLint plano com regras TypeScript/React/hooks. `npm audit` e `npm audit --omit=dev` passaram sem vulnerabilidades; Composer audit passou sem advisories. SAST examinou 284 arquivos e reportou 0 achados; o scan de segredos examinou ~82,75 MB e não encontrou segredos. `npm run lint` e `git diff --check` passaram. A especificação está liberada para desenvolvimento. A revisão de segurança da implementação será feita antes de concluir a story.

## Story Review Gate - bmad-review-adversarial-general

- Revisão adversarial documental executada novamente em 2026-10-05; os achados de contrato, limites, moeda, preço de modelo, versões, erros, disponibilidade, timeout e concorrência foram resolvidos.
- Decisão: pronto para desenvolvimento. A implementação futura ainda precisa de revisão de segurança antes da conclusão.
- Relatório: `reviews/review-3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho-adversarial.md`.

## Project Context Reference

- Regras persistentes: `_bmad-output/project-context.md`.
- Arquitetura principal: `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md`.
- UX: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md` e `EXPERIENCE.md`.

## Dev Agent Record

### Agent Model Used

Codex (story context engine)

### Debug Log References

- A primeira execução da suíte encontrou erro de sintaxe PHP na função do gatilho de versão; a declaração SQL foi convertida para nowdoc e a suíte passou.
- O E2E de concorrência encontrou divergência no mock de model_key; o mock passou a refletir os campos enviados e os 3 cenários passaram.

### Completion Notes List

- Implementados cálculo inteiro de preço, faixas progressivas persistidas, limites e versão monotônica com snapshot PostgreSQL consistente.
- Implementados endpoint Laravel, contrato OpenAPI, BFF same-origin com limites/timeout, interface acessível e integração na página do produto.
- A revisão adversarial levantou 10 pontos de integridade/limites e todos foram corrigidos; relatório final no review adversarial de implementação.
- Revisão de segurança sem achados altos ou médios abertos; risco baixo residual de configuração de proxy/cliente descrito no relatório de implementação.
- Validação: composer test 162 passaram, 1 benchmark ignorado, 810 assertions; Pint passou; lint, typecheck, BFF 23/23, SEO 10/10, build Next.js e Playwright 3/3 passaram. npm audits e Composer audit sem advisories; SAST 0 achados; Gitleaks sem segredos; git diff --check passou.

### File List

- _bmad-output/implementation-artifacts/3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho.md
- _bmad-output/implementation-artifacts/sprint-status.yaml
- _bmad-output/project-context.md
- _bmad-output/implementation-artifacts/reviews/review-3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho-security.md
- _bmad-output/implementation-artifacts/reviews/review-3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho-adversarial.md
- _bmad-output/implementation-artifacts/reviews/review-3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho-security-implementation.md
- _bmad-output/implementation-artifacts/reviews/review-3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho-adversarial-implementation.md
- apps/api/app/Modules/Pricing/Application/CreatePricingQuote.php
- apps/api/app/Modules/Pricing/Application/QuoteProduct.php
- apps/api/app/Modules/Pricing/Domain/PricingQuote.php
- apps/api/app/Modules/Pricing/Domain/QuoteCalculator.php
- apps/api/app/Modules/Pricing/Domain/QuoteCurrency.php
- apps/api/app/Modules/Pricing/Domain/QuoteFailure.php
- apps/api/app/Modules/Pricing/Domain/QuoteQuantity.php
- apps/api/app/Modules/Pricing/Infrastructure/Persistence/PostgresQuoteProduct.php
- apps/api/app/Modules/Pricing/Interfaces/Http/Controllers/CreatePricingQuoteController.php
- apps/api/app/Providers/AppServiceProvider.php
- apps/api/database/migrations/2026_10_06_000001_create_pricing_tables.php
- apps/api/database/seeders/CatalogE2eSeeder.php
- apps/api/routes/api.php
- apps/api/tests/Feature/Pricing/PricingQuoteRequestTest.php
- apps/api/tests/Unit/Modules/Pricing/QuoteCalculatorTest.php
- apps/web/eslint.config.mjs
- apps/web/package.json
- apps/web/package-lock.json
- apps/web/src/app/(public)/produtos/[slug]/page.tsx
- apps/web/src/app/api/pricing/quotes/route.ts
- apps/web/src/app/globals.css
- apps/web/src/bff/pricingApi.ts
- apps/web/src/bff/pricingTransport.ts
- apps/web/src/bff/pricingValidation.ts
- apps/web/src/features/pricing/PricingConfigurator.tsx
- apps/web/tests/e2e/pricing-quote.spec.ts
- apps/web/tests/unit/pricing-transport.test.mjs
- apps/web/tests/unit/pricing-validation.test.mjs
- packages/contracts/pricing-v1.openapi.yaml

## Change Log

- 2026-10-05: story criada a partir do backlog com contexto de Epic 3, arquitetura, UX, PRD e Story 3.1.
- 2026-10-05: revisões documentais corrigidas; contrato, faixas, preços, versões, erros, limites, concorrência e disponibilidade especificados.
- 2026-10-06: pricing Laravel/PostgreSQL, API versionada, Next.js BFF/UI, testes, gates SAST/SCA/segredos e revisões adversarial/de segurança concluídos; story movida para review.
### Review Findings

- [x] [Review][Patch] Preserve and display the known product maximum after quantity edits so an out-of-range quote is blocked locally (AC 3) [apps/web/src/features/pricing/PricingConfigurator.tsx:24]
- [x] [Review][Patch] Reset configurator state when slug/model changes so a quote from the previous product cannot remain visible (AC 1, 4, 5) [apps/web/src/features/pricing/PricingConfigurator.tsx:27]
- [x] [Review][Patch] Move configurator copy into the existing centralized i18n content structure [apps/web/src/i18n/pricingConfiguratorContent.ts:1]
- [x] [Review][Patch] Restore safe Core Web Vitals coverage without the vulnerable official plugin [apps/web/eslint.config.mjs:4] — local rules cover HTML images, internal anchors, synchronous scripts, manual CSS links, `<head>`, async Client Components and assignment to `module`.
- [x] [Review][Patch] Update transitive dependencies to `sharp@0.35.5` and `source-map-js@1.2.2`, clearing the npm dependency audit [apps/web/package-lock.json:1907]
### Security Gate Evidence (2026-10-06)

- `composer audit --locked --no-interaction`: passed; no advisories.
- `npm audit --audit-level=moderate`: passed; `sharp@0.35.5` and `source-map-js@1.2.2` resolve the former high advisories.
- `npm run lint` and `npm run typecheck`: passed with the local Core Web Vitals rules enabled.
- `node scripts/scan-sast.mjs`: Semgrep executou 247 regras sobre 305 arquivos, com 0 achados.
- `node scripts/scan-secrets.mjs`: Gitleaks examinou aproximadamente 82,84 MB, sem vazamentos.
- Revisão pós-correções: `reviews/review-3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho-security-postpatch-2026-10-06.md`; gate aprovado.

- 2026-10-06: all code-review corrections and the dependency-audit remediation applied; local Core Web Vitals coverage avoids the vulnerable official plugin.
- 2026-10-06: gates SAST, segredos, dependências e revisão de segurança pós-correções aprovados; story concluída.
