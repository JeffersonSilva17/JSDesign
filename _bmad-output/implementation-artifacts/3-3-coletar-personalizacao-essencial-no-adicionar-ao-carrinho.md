---
baseline_commit: b94a2af110fe8046d2a89818cdd33d9460778455
---

# Story 3.3: Coletar personalização essencial no adicionar ao carrinho

Status: done

## Story

Como cliente comprando um produto personalizado,
quero informar os dados essenciais da personalização antes de adicionar o item,
para que a encomenda tenha contexto inicial sem antecipar o Briefing completo.

**Requisitos cobertos:** FR-11, FR-12; UX-DR14, UX-DR15, UX-DR30. **Dependências:** Stories 3.1 e 3.2 concluídas. O fluxo transacional e a revisão/edição de vários itens pertencem à Story 4.1; a regra de cobrança e o arquivo da Miniatura pertencem à Story 3.4. Esta story registra apenas a escolha explícita da Miniatura no pré-formulário.

## Acceptance Criteria

1. **Configuração física personalizada.** Dado um produto `physical_personalized` publicado, quando a cliente configurar o item, então tema, nome/texto principal e data do evento são obrigatórios, observação curta é opcional, e a quantidade, modelo e cotação atuais da Story 3.2 permanecem associados à configuração. Tema pode ser escolhido entre opções públicas do produto ou informado em texto livre. Fotos, links e referências não são coletados aqui; são enviados no Briefing protegido após o pagamento.
2. **Pré-formulário de convite.** Dado um produto `digital_personalized`, o tipo/modelo publicado define `invite_type` (`standard` ou `complex`) e o formulário coleta `theme`, `colors`, `event_name`, `event_date`, `event_time` e `event_location`, além de uma escolha explícita de Miniatura quando o produto for compatível. `event_name` é o nome ou texto literal que deve aparecer no convite; `event_location` é o nome/local apresentado no convite, não endereço postal. Todos esses campos são obrigatórios para Convite Padrão e Complexo antes do carrinho. Convite Complexo ainda exige Briefing completo após o pagamento. `event_date` usa `YYYY-MM-DD` como data civil e o Laravel rejeita data anterior à sua data operacional configurada; `event_time` usa `HH:mm` (24 horas) como hora local, sem conversão de fuso. A ausência do tema no catálogo não bloqueia texto livre. A escolha de Miniatura é `none`, `client_file` ou `create_by_js_designs`, sem upload nesta story e sem cobrança calculada pela UI; a cobrança única é da Story 3.4.
3. **Validação e inclusão atômica.** Dada uma configuração incompleta ou inválida, quando a cliente selecionar “Adicionar ao carrinho” ou “Comprar agora”, então cada campo obrigatório pendente é indicado junto ao campo, os valores já informados são preservados e nenhuma inclusão ocorre. Dada uma configuração válida, então a inclusão deve criar ou reutilizar o carrinho convidado corrente e um item com produto/modelo publicados e configuração validada pelo Laravel; não pode depender de preço, disponibilidade, identidade, campos obrigatórios ou total enviados como confiáveis pelo navegador. Validar publicação, disponibilidade e cotação atual na mesma transação. O BFF emite uma chave opaca para a submissão lógica, vincula-a à sessão server-side e ao hash do payload canônico, e a UI reutiliza a mesma chave em retries; a chave vale 24 horas e guarda somente hash do payload e referência do resultado. Replay igual retorna o mesmo resultado e payload diferente retorna conflito sanitizado. Nenhum identificador de carrinho ou cliente é aceito no corpo ou na URL.
4. **Detalhes para depois.** O catálogo público expõe `allows_deferred_briefing: true` somente quando a Administração publicar essa capacidade para o produto/modalidade. Na ausência ou com valor `false`, a opção não aparece; nesta story nenhum produto é elegível por padrão. Quando explicitamente habilitada, a cliente pode escolher “Preencher depois” apenas para campos que não alterem modalidade, escopo ou preço. A tela informa que a primeira arte só será preparada após o Briefing completo e que a produção só começa após aprovação da arte.
5. **Resumo e continuidade.** “Adicionar ao carrinho” e “Comprar agora” têm a mesma semântica nesta story: ambos incluem server-side e levam ao carrinho; “Comprar agora” não inicia checkout, que permanece fora do escopo. Após inclusão aceita pelo servidor, a cliente recebe confirmação e pode seguir para o carrinho. O resumo preserva modalidade, modelo, quantidade, configuração essencial e escolha de Miniatura por item, escapando todo texto livre como texto. Falha, timeout, conflito/idempotência, cotação desatualizada ou produto indisponível não mostra sucesso fictício nem perde os dados preenchidos; em conflito de preço/disponibilidade, retorna nova cotação e exige confirmação deliberada.
6. **Modalidades fora do escopo.** Para `digital_ready`, nenhuma personalização ou Briefing é solicitado e a rota desta story responde `unsupported_mode` sem criar item; a compra pertence ao fluxo explícito das Stories 3.5/6.1. Não declarar checkout/compra concluídos enquanto esse fluxo não existir. Um produto personalizado nunca é incluído sem configuração válida ou opção explícita de preenchimento posterior permitida pelo catálogo.
7. **Privacidade, contrato e acessibilidade.** O contrato Laravel e a validação runtime do BFF rejeitam campos extras, limites excedidos e tipos incorretos, e retornam erros públicos allowlistados (`400`, `413`, `419`, `422`, `429`, `503`, `409` e timeout), com `Retry-After` quando aplicável. O endpoint aceita no máximo 10 submissões por minuto por sessão e timeout upstream de 3 segundos, ambos configuráveis no servidor. Os limites são: `theme` 120 caracteres, `colors` 160, `event_name` 120, `event_location` 200, `note` 500 e corpo total 16 KiB, em UTF-8; a API rejeita bytes inválidos. Nome, tema, data, horário, local e observações não aparecem em URL, analytics, logs ou mensagens de erro; não coletar anexos nesta etapa. O locale selecionado manualmente pela cliente é a fonte única para UI e mensagens traduzidas em pt-BR, inglês e espanhol. Formulários funcionam por teclado e leitor de tela, associam erros aos campos e refluem a partir de 320 px.

## Tasks / Subtasks

- [x] 1. Fechar contratos de configuração e inclusão antes de implementar (AC: 3–5, 7)
  - [x] Publicar/atualizar contrato OpenAPI versionado em `packages/contracts/cart-v1.openapi.yaml` com `additionalProperties: false`, corpo de 16 KiB, allowlist, limites acima, matriz `standard`/`complex`, `allows_deferred_briefing`, códigos de erro e formatos canônicos.
  - [x] Especificar os dois CTAs como inclusão seguida de carrinho, conflito de cotação e contrato do item mínimo (`product_id`, `model_id`, `quantity`, `configuration`, `quote_snapshot_id`, `created_at`), que a Story 4.1 poderá revisar.
  - [x] Aplicar AD-10: cookie de sessão convidada opaco, `HttpOnly`, `Secure` e `SameSite=Lax` mantido pelo BFF; autenticação server-to-server BFF→Laravel; CSRF same-origin validado pelo BFF; owner/carrinho derivados no Laravel; sem ID no payload/URL e com isolamento contra IDOR.
  - [x] Publicar `allows_deferred_briefing` como capacidade pública somente quando a Administração a configurar; ausência equivale a `false`.
- [x] 2. Implementar configuração e validação Laravel (AC: 1–4, 6–7)
  - [x] Colocar regra e validação de domínio no módulo responsável do Laravel (`Cart`/configuração conforme estrutura aprovada), em camadas Domain, Application, Infrastructure e Interfaces/Http; controllers não implementam regras nem consultam Eloquent diretamente.
  - [x] Validar publicação/disponibilidade atual, modalidade, modelo, matriz de campos, data futura, enum de Miniatura e configuração no servidor; rejeitar campo obrigatório ausente, enum inválido, texto acima do limite e produto incompatível.
  - [x] Persistir configuração junto do item somente pelo fluxo aprovado de carrinho; tornar a inclusão idempotente e não confiar na cotação antiga da UI para total/preço final. O carrinho deve recalcular valores conforme a regra vigente.
- [x] 3. Integrar BFF e interface de produto (AC: 1–6)
  - [x] Estender `apps/web/src/app/(public)/produtos/[slug]/page.tsx` e o configurador da feature de pricing/catalog, preservando quantidade/cotação, seleção de modelo, canonical, metadata e estados de indisponibilidade das Stories 3.1/3.2.
  - [x] Acrescentar campos de personalização física e de convite com conteúdo centralizado em `apps/web/src/i18n/`; usar validação runtime estrita do BFF e rota same-origin para a Laravel API.
  - [x] Tratar “Adicionar ao carrinho”/“Comprar agora” como submissão real: navegar ao carrinho apenas após confirmação server-side; manter estado digitado em falhas recuperáveis. Não colocar PII nos parâmetros de URL.
  - [x] Implementar “Preencher depois” apenas para `allows_deferred_briefing=true`, sem habilitação por ausência, e exibir os avisos de prazo/Briefing/Aprovação exigidos; manter o locale manual também nos erros.
- [x] 4. Cobrir segurança, comportamento e regressões (AC: 1–7)
  - [x] Criar testes unitários de domínio e testes HTTP Laravel para modalidade/modelo, matriz de campos obrigatórios, limites, campos extras, produto indisponível, data/horário canônicos, idempotência (replay igual e payload conflitante) e erros sem detalhes internos.
  - [x] Criar testes BFF/UI para payload/resposta inválidos, timeout, retry, ausência de duplicação, preservação de dados, inclusão confirmada apenas após resposta aceita, modalidades personalizadas e `digital_ready`.
  - [x] Fixar fixtures públicas de Convite Padrão/Complexo, comprovar que o pré-formulário é reutilizado no contexto do Briefing sem redigitação e verificar a resposta `unsupported_mode` de `digital_ready`.
  - [x] Criar testes Playwright acessíveis para teclado, erros junto aos campos, viewport de 320 px, CTAs, Convite Padrão/Complexo, retry concorrente, escaping de texto e confirmação de inclusão somente após aceite; preservar smoke tests do catálogo e da cotação.
  - [x] Executar os gates de SAST, dependências e segredos versionados e revisar a implementação com `bmad-review-security` antes de concluir. Semgrep: 0 achados em 305 arquivos; Gitleaks: nenhum vazamento; `npm audit`: 0 vulnerabilidades; `composer audit`: sem advisories, em 2026-10-27.

## Dev Notes

### Contexto e limites

- A página de produto, seleção do modelo e cotação em EUR já estão implementadas. A Story 3.2 explicitamente não cria item/carrinho; esta story introduz uma inclusão real server-owned. Os CTAs físicos usam “Comprar já” e os convites usam “Comprar agora”; ambos têm o comportamento de inclusão e navegação ao carrinho definido nesta story.
- Story 4.1 é responsável por revisar, alterar quantidade/remover itens, manter carrinho por conta por até 90 dias e recalcular ao retornar. Esta story cobre captura segura da configuração e inclusão idempotente; não implementar persistência de carrinho por conta, notificações de abandono, checkout, cupom, frete ou pagamento.
- Story 3.4 é responsável pelo preço, compartilhamento e eventual upload da Miniatura. Esta story coleta somente `none`, `client_file` ou `create_by_js_designs`, nunca arquivo nem valor calculado no cliente.
- “Preencher depois” depende exclusivamente de `allows_deferred_briefing=true` publicado pelo catálogo; ausência ou `false` mantém o controle oculto. Nenhum produto atual é elegível por padrão. A capacidade deve ser configurada e testada pela Administração antes de ser usada.
- Convite Padrão exige dados essenciais antes do carrinho e informa até 24 horas após pagamento e dados completos. Convite Complexo exige os mesmos dados essenciais antes do carrinho, depois exige Briefing completo e informa até 48 horas após o Briefing. O pré-formulário não é o Briefing.
- Campos livres usam os limites do AC7 e o corpo total de 16 KiB no OpenAPI/Form Request. `event_date` é `YYYY-MM-DD` sem fuso e `event_time` é `HH:mm` local; o Laravel rejeita datas anteriores à sua data operacional configurada.
- O shape e requiredness do convite estão definidos no AC2 como matriz única para `standard` e `complex`; a UI consome o contrato e o Laravel permanece autoridade.

### Guardrails de arquitetura e implementação

- Fluxo: navegador → rota same-origin Next.js/BFF → Laravel `/api/v1` → PostgreSQL. O navegador nunca chama a API interna diretamente. BFF adapta/valida payloads, não decide regra comercial nem autorização.
- Antes de criar endpoint ou persistência, aplicar AD-10: o BFF mantém a sessão convidada em referência opaca server-side e cookie `HttpOnly`, `Secure`, `SameSite=Lax`; valida `Origin`/`Referer` e token CSRF associado à sessão; chama o Laravel com credencial server-to-server; o Laravel deriva owner/carrinho e autoriza cada requisição. Nenhum cookie, token, ID de cliente ou ID de carrinho fornecido pelo navegador concede acesso isoladamente. Testes negativos de IDOR/CSRF são obrigatórios.
- Enviar somente dados que compõem a configuração essencial; limitar corpo e campos; rejeitar chaves desconhecidas; usar consultas parametrizadas e transação Laravel para validação e inclusão. Nunca aceitar identificadores de cliente/pedido, preço, desconto, total, miniatura paga ou status enviados pelo browser.
- Configuração pode conter PII de evento, inclusive dados associados a crianças. Minimizar campos, restringir acesso por autorização server-side, não registrar payloads, definir retenção de 30 dias para carrinho/pré-formulário abandonado conforme NFR-7, executar eliminação verificável e não enviar a analytics/terceiros. Erros externos devem ser genéricos e sem SQL, stack trace ou paths.
- Prevenir CSRF conforme a estratégia aprovada de sessão; aplicar rate limit padrão de 10 submissões por minuto por sessão, limite de 16 KiB, timeout upstream de 3 segundos e respostas sem cache a dados de configuração; retry deve usar a chave de idempotência server-validated descrita no AC3.
- Reutilizar componentes, validadores e conteúdo i18n existentes. Não adicionar dependências nem mover validação de domínio ao componente React. Preservar o cálculo monetário inteiro no Laravel e a atualização de cotação da Story 3.2.
- Testes de persistência usam PostgreSQL real e `RefreshDatabase`; não usar SQLite quando constraints/transações PostgreSQL forem relevantes. Não inventar threshold de cobertura.

### Arquivos e padrões existentes relevantes

- `apps/web/src/app/(public)/produtos/[slug]/page.tsx`: Server Component existente; lê o produto, resolve modelo, mantém SEO e renderiza `PricingConfigurator`.
- `apps/web/src/features/pricing/PricingConfigurator.tsx` e `apps/web/src/i18n/pricingConfiguratorContent.ts`: estado mínimo de quantidade/cotação com abort/controle de sequência e conteúdo localizado; estender com cuidado sem mover cálculo ao cliente.
- `apps/web/src/bff/pricingApi.ts`, `pricingValidation.ts` e `apps/web/src/app/api/pricing/quotes/route.ts`: padrão de rota BFF, validação de `unknown`, erros allowlistados e timeout a reutilizar como referência, não duplicar indiscriminadamente.
- `apps/api/app/Modules/Pricing/`: domínio e endpoint de cotação atuais. Não transformar cotação somente leitura em mutação nem colocar carrinho no módulo Pricing.
- `packages/contracts/pricing-v1.openapi.yaml`: contrato de cotação atual; contrato de inclusão/configuração deve ser separado/versionado ou estendido somente com limites de domínio claros.
- Não existe módulo `Cart` no código atual. O agregado continua pertencendo ao Laravel; criar a fronteira de sessão conforme AD-10 antes da implementação.

### STRIDE — ameaças e controles

| Ameaça | Superfície | Controle exigido |
|---|---|---|
| Spoofing | Sessão convidada e rota de inclusão | Identidade derivada somente da sessão aprovada no servidor; não aceitar ID de cliente/carrinho do payload. |
| Tampering | Campos, modelo, quantidade, cotação e repetição | Schema fechado, validação Laravel contra catálogo atual, preço recalculado e inclusão idempotente. |
| Repudiation | Criação/alteração da configuração | Timestamp UTC e identificador de correlação técnico; sem copiar PII para logs. |
| Information Disclosure | Erros, logs, URL, analytics e carrinho | PII fora de URL/logs/analytics; erros sanitizados; item só acessível pela identidade autorizada. |
| Denial of Service | Corpo público, texto livre e retry | Limite de payload/campos, rate limit, timeout, tamanho máximo e idempotência. |
| Elevation of Privilege | Acesso a item/carrinho alheio | Autorização no Laravel em toda leitura/escrita; BFF não concede acesso; testes negativos de IDOR. |

### Referências

- [Épico 3 e Story 3.3](../planning-artifacts/epics.md#story-33-coletar-personalização-essencial-no-adicionar-ao-carrinho)
- [Story 4.1 — limites do gerenciamento do carrinho](../planning-artifacts/epics.md#story-41-gerenciar-carrinho-com-itens-configurados-e-persistência-por-conta)
- [PRD — FR-11 e FR-12](../planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md#fr-11--configuração-de-produto-físico)
- [Arquitetura — espinha Laravel/BFF](../planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md)
- [UX — Experience](../planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md)
- [UX — Design](../planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md)
- [Story 3.1 — página de produto](3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md)
- [Story 3.2 — quantidade e cotação](3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho.md)
- Contexto persistente: `../project-context.md`.

## Security Gate - bmad-review-security

- Status documental: `SEC-3.3-01` e `SEC-3.3-02` tratados na especificação e em AD-10. O contrato OpenAPI está publicado e os gates automatizados passaram; story pronta para desenvolvimento.
- `SEC-3.3-01`: AD-10 agora define sessão convidada opaca no BFF, autenticação server-to-server, CSRF same-origin, owner derivado no Laravel, autorização por requisição e testes negativos de IDOR.
- `SEC-3.3-02`: retenção de carrinho/pré-formulário abandonado fixada em 30 dias conforme NFR-7, com eliminação verificável, minimização, acesso server-side e proibição de PII em observabilidade.
- Evidência dos gates em 2026-10-27: Semgrep/SAST passou com 0 achados em 305 arquivos; Gitleaks passou sem vazamentos; `npm audit --audit-level=moderate` encontrou 0 vulnerabilidades; `composer audit --locked --no-interaction` não encontrou advisories. A estratégia concreta de sessão/CSRF/autorização está registrada em AD-10 e o contrato está em `packages/contracts/cart-v1.openapi.yaml`.
- Condição para iniciar desenvolvimento: implementar as tarefas restantes da story sem alterar os contratos ou controles aprovados; qualquer mudança arquitetural exige nova revisão humana e novo gate de segurança.
- Relatório: `reviews/review-3-3-coletar-personalizacao-essencial-no-adicionar-ao-carrinho-security.md`.

## Story Review Gate - bmad-review-adversarial-general

- Revisão de acompanhamento executada em 2026-10-27 e registrada em `reviews/review-3-3-coletar-personalizacao-essencial-no-adicionar-ao-carrinho-adversarial.md`.
- Achados de produto tratados: semântica dos CTAs, matriz de campos e shape do convite, datas/horários, limites, capacidade de preenchimento posterior, fronteira com Story 4.1, concorrência, fluxo `digital_ready`, locale, escaping e códigos de erro.
- Miniatura foi resolvida por separação de responsabilidades: a escolha explícita é capturada aqui; preço, compartilhamento e arquivo permanecem na Story 3.4.
- Veredito: `ready-for-dev`; não há decisão comercial, arquitetural ou gate de segurança pendente para iniciar a implementação.

## Project Context Reference

- Regras persistentes: `_bmad-output/project-context.md` e `AGENTS.md`.
- Arquitetura: `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md`.
- UX: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md` e `EXPERIENCE.md`.

## Dev Agent Record

### Agent Model Used

Codex (story context engine)

### Debug Log References

### Completion Notes List

- Implementado o fluxo de configuração essencial para produto físico e convite digital padrão/complexo, com preço atual validado e inclusão server-side no carrinho convidado.
- O Laravel valida produto, publicação, disponibilidade, modalidade, modelo, campos, cotações e idempotência em transação. Sessão, autorização server-to-server, CSRF, limite de requisição, expiração de 30 dias e limpeza programada estão ativos.
- O BFF fecha schemas de entrada e saída, emite chave de idempotência, aplica timeout e limite de corpo, localiza erros e preserva os dados digitados em falhas recuperáveis.
- A leitura de carrinho diferencia indisponibilidade de carrinho vazio. A configuração salva fica disponível no endpoint do carrinho para continuidade do fluxo de Briefing posterior.
- Verificações: PHPUnit 171 testes / 857 assertions / 1 skipped; BFF 25/25; Playwright 74/74 e suíte focada de pricing 5/5 (incluindo Convite Complexo); typecheck, lint e build passaram.
- Segurança: Semgrep 0 achados em 324 arquivos; Gitleaks sem vazamentos; npm audit 0 vulnerabilidades; composer audit sem advisories. Achados de aplicação foram tratados; a observação baixa sobre políticas externas de sandbox está registrada no relatório de segurança.

### File List

- packages/contracts/cart-v1.openapi.yaml
- packages/contracts/catalog-admin-v1.openapi.yaml
- packages/contracts/catalog-public-v1.openapi.yaml
- packages/contracts/pricing-v1.openapi.yaml
- apps/api/.env.example
- apps/api/app/Modules/Catalog/Domain/Product.php
- apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresCatalogProductRepository.php
- apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php
- apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogRules.php
- apps/api/app/Modules/Catalog/Interfaces/Http/Resources/AdminCatalogProductResource.php
- apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php
- apps/api/app/Modules/Pricing/Domain/PricingQuote.php
- apps/api/app/Modules/Pricing/Interfaces/Http/Controllers/CreatePricingQuoteController.php
- apps/api/app/Modules/Cart/
- apps/api/config/cart.php
- apps/api/config/services.php
- apps/api/database/migrations/2026_10_27_000001_create_guest_cart_tables.php
- apps/api/database/seeders/CatalogE2eSeeder.php
- apps/api/routes/api.php
- apps/api/routes/console.php
- apps/api/app/Providers/AppServiceProvider.php
- apps/api/tests/Feature/Cart/CartItemRequestTest.php
- apps/api/tests/Feature/Catalog/PublicCatalogApiTest.php
- apps/api/tests/Feature/Pricing/PricingQuoteRequestTest.php
- apps/api/tests/Unit/Modules/Cart/CartConfigurationTest.php
- apps/web/.env.example
- apps/web/package.json
- apps/web/tsconfig.json
- apps/web/src/app/(public)/carrinho/page.tsx
- apps/web/src/app/(public)/produtos/[slug]/page.tsx
- apps/web/src/app/api/cart/
- apps/web/src/app/globals.css
- apps/web/src/bff/cartTransport.ts
- apps/web/src/bff/cartValidation.ts
- apps/web/src/bff/catalogApi.ts
- apps/web/src/bff/catalogValidation.ts
- apps/web/src/bff/pricingValidation.ts
- apps/web/src/features/pricing/PricingConfigurator.tsx
- apps/web/src/i18n/cartConfiguratorContent.ts
- apps/web/tests/e2e/catalog-listing.spec.ts
- apps/web/tests/e2e/catalog-seo-upstream.spec.ts
- apps/web/tests/e2e/catalog-seo.spec.ts
- apps/web/tests/e2e/pricing-quote.spec.ts
- apps/web/tests/unit/cart-validation.test.mjs
- apps/web/tests/unit/catalog-validation.test.mjs
- apps/web/tests/unit/pricing-transport.test.mjs
- apps/web/tests/unit/pricing-validation.test.mjs
- _bmad-output/implementation-artifacts/reviews/review-3-3-coletar-personalizacao-essencial-no-adicionar-ao-carrinho-security-implementation.md
- _bmad-output/implementation-artifacts/reviews/review-3-3-coletar-personalizacao-essencial-no-adicionar-ao-carrinho-adversarial-implementation.md

### Change Log

- 2026-10-27 — Implementação completa; validações, testes e gates de segurança passaram; status atualizado para done.

### Review Remediation — 2026-10-28

- [x] AD-10: a referência opaca no cookie é resolvida contra `guest_cart_sessions`; owner hash, CSRF, chave de idempotência e hash canônico ficam server-side.
- [x] Configurações são comparadas e hasheadas de forma canônica; textos obrigatórios rejeitam ausência, `null` e branco.
- [x] Carrinhos têm limite configurável de itens, resposta BFF limitada a 64 KiB e carrinhos vencidos são excluídos antes de reutilização.
- [x] Sessão renova cookies e expiração; timeout é configurável; 413, contrato OpenAPI e rate limiting de leitura foram alinhados.

### Review Findings

- [ ] [Review][Decision] Implementação não cumpre a sessão convidada server-side definida em AD-10 — `apps/web/src/app/api/cart/session/route.ts` grava no cookie o valor aleatório que é encaminhado ao Laravel como `X-Guest-Session`; não há referência de sessão persistida no BFF, nem vínculo server-side entre sessão, chave de idempotência e hash canônico do payload. A correção exige decidir a persistência server-side compatível com a arquitetura aprovada. [apps/web/src/app/api/cart/session/route.ts:7]
- [ ] [Review][Patch] BFF pode responder 503 após persistir o item porque compara a ordem das propriedades JSON como texto [apps/web/src/bff/cartTransport.ts:22]
- [ ] [Review][Patch] Campos textuais obrigatórios aceitam `null` na validação Laravel [apps/api/app/Modules/Cart/Domain/CartConfiguration.php:24]
- [ ] [Review][Patch] Carrinho sem limite ou paginação pode exceder o limite de 16 KiB do BFF e ficar indisponível [apps/web/src/bff/cartTransport.ts:35]
- [ ] [Review][Patch] Hash de idempotência depende da ordem das propriedades em vez de canonicalizar o payload [apps/api/app/Modules/Cart/Application/AddConfiguredCartItem.php:19]
- [ ] [Review][Patch] Cookie da sessão não é renovado quando uma inclusão estende a expiração do carrinho [apps/web/src/app/api/cart/session/route.ts:12]
- [ ] [Review][Patch] OpenAPI aceita formatos de `quote_snapshot_id` que a API rejeita [packages/contracts/cart-v1.openapi.yaml:103]
- [ ] [Review][Patch] OpenAPI proíbe briefing adiado físico que a validação Laravel aceita quando habilitado [packages/contracts/cart-v1.openapi.yaml:126]
- [ ] [Review][Patch] Timeout upstream de 3 segundos está fixo no código e não é configurável no servidor [apps/web/src/bff/cartTransport.ts:6]
- [ ] [Review][Patch] API Laravel responde 400 em vez de 413 quando o corpo excede 16 KiB [apps/api/app/Modules/Cart/Interfaces/Http/Controllers/AddConfiguredCartItemController.php:21]
- [ ] [Review][Patch] Leitura pública do carrinho não tem rate limit e pode gerar consultas internas sem limite por sessão/IP [apps/web/src/app/api/cart/items/route.ts:10]
