---
baseline_commit: 9080598
---

# Story 1.3: Implementar idioma, textos base e estrutura pública em português do Brasil

Status: done

## Story

Como cliente da JS Designs,  
quero navegar pela loja com textos claros em português do Brasil,  
para entender os produtos, etapas de compra e próximos passos sem confusão.

## Requisitos cobertos

- FR-4
- NFR-10
- UX-DR3, UX-DR30, UX-DR33

## Acceptance Criteria

1. **Português do Brasil e UTF-8**
   - **Given** a loja pública base  
     **When** a cliente acessar qualquer página pública existente  
     **Then** todos os textos visíveis devem estar em português do Brasil  
     **And** caracteres acentuados devem renderizar corretamente  
     **And** arquivos criados/alterados devem permanecer em UTF-8.

2. **Conteúdo público centralizado**
   - **Given** a estrutura de conteúdo da loja  
     **When** o time precisar alterar textos de cabeçalho, rodapé, home, placeholders, CTAs e metadata pública  
     **Then** esses textos devem estar organizados em módulos de conteúdo/dicionário tipados no frontend  
     **And** não devem ficar espalhados em componentes de layout ou páginas.

3. **Estrutura preparada para internacionalização futura**
   - **Given** que o lançamento deve suportar português do Brasil, inglês e espanhol  
     **When** a estrutura de conteúdo for implementada  
     **Then** deve existir definição tipada de locale padrão `pt-BR` e locales futuros `en` e `es`  
     **And** a implementação desta story deve manter somente `pt-BR` como conteúdo ativo  
     **And** não deve implementar troca real de idioma, moeda, redirecionamento por localização, cookies de preferência ou rotas localizadas ainda.

4. **Diferenciação clara de modalidades**
   - **Given** a cliente em fluxo público  
     **When** encontrar textos sobre produtos físicos, convites digitais personalizados ou Produto Digital Pronto  
     **Then** os textos devem diferenciar claramente:
       - produto físico personalizado;
       - convite digital personalizado;
       - produto digital pronto;
     **And** convites digitais personalizados devem informar que não são entregues imediatamente porque exigem edição/criação, prévia e aprovação  
     **And** Produto Digital Pronto deve usar linguagem sem personalização e mencionar download imediato somente como conceito futuro/placeholder, sem simular compra real nesta story.

5. **Textos mobile-first**
   - **Given** a experiência mobile-first  
     **When** os textos forem exibidos em 320 px  
     **Then** labels, CTAs e mensagens devem ser curtos, legíveis e sem truncamento crítico  
     **And** mensagens mais longas devem aparecer apenas em áreas informativas, não em controles compactos do header.

6. **Validação técnica de conteúdo**
   - **Given** o frontend em Next.js  
     **When** os testes forem executados  
     **Then** deve existir cobertura mínima para:
       - textos principais em português do Brasil;
       - acentos em pelo menos home, header, footer e placeholders;
       - ausência de mojibake evidente, como `Ã`, `�` ou texto corrompido;
       - `html lang="pt-BR"`;
       - preservação dos testes existentes de navegação, metadata `noindex`, `/health` e `/api/health`.

## Tasks / Subtasks

- [x] Preparar estrutura de idioma/conteúdo sem mudar rotas públicas (AC: 1, 2, 3)
  - [x] Antes de editar código Next.js, ler `apps/web/AGENTS.md` e os guias locais relevantes em `apps/web/node_modules/next/dist/docs/01-app/`, principalmente App Router, metadata e internationalization.
  - [x] Criar estrutura em `apps/web/src/i18n/` ou `apps/web/src/features/public-store/content/` com tipos explícitos para `SupportedLocale`, `defaultLocale`, `supportedLocales` e conteúdo público `pt-BR`.
  - [x] Manter `pt-BR` como único conteúdo ativo nesta story.
  - [x] Não criar rotas `/pt-BR`, `/en`, `/es`, `proxy.ts`, cookies de idioma, detecção por `Accept-Language` ou seletor funcional de idioma nesta story.
  - [x] Não adicionar biblioteca de i18n (`next-intl`, `next-international`, etc.) sem justificativa explícita; o esperado é estrutura tipada leve com TypeScript.

- [x] Centralizar textos visíveis já existentes (AC: 1, 2, 4, 5)
  - [x] Mover labels e descrições de `apps/web/src/features/public-store/publicLayoutContent.ts` para o novo módulo de conteúdo ou refatorar esse arquivo para virar façade do conteúdo `pt-BR`.
  - [x] Centralizar textos da home pública atualmente em `apps/web/src/app/(public)/page.tsx`: eyebrow, `h1`, lead, CTAs, cards conceituais e seção “Próximos caminhos”.
  - [x] Centralizar textos institucionais do rodapé atualmente em `SiteFooter.tsx`: título, descrição, contato/suporte, confiança e copyright.
  - [x] Centralizar textos genéricos de `PlaceholderPage.tsx`: CTAs “Voltar para a home” e “Voltar para produtos”.
  - [x] Centralizar metadata pública de home e placeholders ou criar helpers tipados para reduzir duplicação.
  - [x] Preservar labels e destinos atuais do header e footer; não quebrar testes da Story 1.2.

- [x] Refinar microcopy pública para modalidades e próximos passos (AC: 4, 5)
  - [x] Garantir que home e placeholders usem termos consistentes: `lembrancinhas físicas personalizadas`, `convites digitais personalizados`, `Produto Digital Pronto`, `Silhouette Studio` quando fizer sentido.
  - [x] Explicitar que convites digitais personalizados não têm entrega imediata, pois exigem edição/criação, prévia e aprovação.
  - [x] Explicitar que Produto Digital Pronto é digital, sem personalização, e que download imediato só ocorrerá após pagamento confirmado em story futura.
  - [x] Evitar promessas transacionais ainda não implementadas: preço, disponibilidade, carrinho real, checkout, cupom, prazo fechado, pagamento, entrega ou suporte funcional.
  - [x] Manter textos compactos no header/CTAs; textos explicativos podem ficar em home/placeholder.

- [x] Preservar arquitetura Frontend/BFF e escopo da story (AC: 3, 6)
  - [x] Não alterar backend Laravel.
  - [x] Não criar módulo Laravel `I18n` nesta story; a arquitetura prevê isso futuramente, mas esta story é frontend público/base textual.
  - [x] Não mover regra de preço, moeda, carrinho, pedido, checkout ou pagamento para Next.js.
  - [x] Não implementar conversão de moeda, escolha de moeda, preservação real de carrinho ao trocar idioma ou contexto de pedido; esses comportamentos dependem de stories futuras com domínio Laravel.
  - [x] Manter `/health` fora do `PublicShell` e preservar `/api/health`.

- [x] Atualizar testes de frontend/smoke para conteúdo e UTF-8 (AC: 1, 5, 6)
  - [x] Atualizar `apps/web/tests/e2e/foundation.spec.ts` ou criar spec dedicada para validar textos principais em português do Brasil.
  - [x] Validar `html[lang="pt-BR"]`.
  - [x] Validar renderização de acentos: `Área da Cliente`, `Políticas`, `Privacidade`, `Trocas e reembolso`, `Detalhes personalizados`, `lembrancinhas físicas personalizadas`, `convites digitais personalizados`.
  - [x] Validar que a home diferencia as três modalidades em texto visível.
  - [x] Validar que placeholder de Produto Digital Pronto/Produtos Digitais, se criado como texto conceitual nesta story, não simula compra real.
  - [x] Validar ausência de mojibake evidente no conteúdo renderizado (`Ã`, `�`).
  - [x] Preservar todos os testes existentes da Story 1.2: links/hrefs, mobile 320 px, teclado, noindex de `/carrinho` e `/entrar`, ausência de WhatsApp flutuante, `/health` e BFF `/api/health`.

- [x] Rodar validações proporcionais ao escopo (AC: 6)
  - [x] Em `apps/web`: `npm run lint`.
  - [x] Em `apps/web`: `npm run typecheck`.
  - [x] Em `apps/web`: `npm run build`.
  - [x] Em `apps/web`: `npm run test:e2e`, com Laravel API ativa para preservar o smoke BFF existente.
  - [x] Rodar backend somente se algum arquivo backend for alterado por engano; o esperado é não alterar `apps/api`.

### Review Findings

- [x] [Review][Patch] Centralizar o texto do skip link no dicionário público [apps/web/src/components/layout/PublicShell.tsx:14]
- [x] [Review][Patch] Usar o locale tipado como fonte do `<html lang>` [apps/web/src/app/layout.tsx:18]
- [x] [Review][Patch] Fortalecer a guarda de mojibake para cobrir `Ã`, `Â` e aspas corrompidas além de `Ãƒ` [apps/web/tests/e2e/foundation.spec.ts:45]
- [x] [Review][Patch] Consumir `qualityCopy` nos testes em vez de duplicar literais de validação [apps/web/src/i18n/publicContent.ts:263]

## Dev Notes

### Escopo exato desta story

Esta story cria a base textual e a estrutura inicial de conteúdo em português do Brasil para as páginas públicas existentes. Ela prepara o terreno para internacionalização futura, mas não entrega a troca real de idioma/moeda.

Resultado esperado:

- Conteúdo público base organizado em um dicionário/módulo tipado.
- `pt-BR` definido como locale padrão ativo.
- `en` e `es` reconhecidos apenas como locales futuros/suportados em contrato de tipos, sem rotas ou traduções ativas.
- Textos públicos atuais deixam de ficar embutidos diretamente em componentes/páginas sempre que forem conteúdo editorial reutilizável.
- A interface continua com as mesmas rotas públicas criadas na Story 1.2.
- Os testes comprovam acentuação, `lang="pt-BR"`, ausência de mojibake e preservação da fundação.

Fora do escopo:

- Tradução completa para inglês/espanhol.
- Seletor funcional de idioma.
- Seletor de moeda.
- Conversão cambial.
- Preservação real de Carrinho/Pedido entre idiomas.
- Rotas localizadas `/pt-BR`, `/en`, `/es`.
- Detecção regional por localização/IP.
- `proxy.ts`/redirect por `Accept-Language`.
- Backend Laravel `I18n`.
- Catálogo, busca real, carrinho, checkout, suporte funcional, desconto por e-mail ou admin.

### Fundamento do produto

O PRD exige que a cliente possa alternar entre português do Brasil, inglês e espanhol e escolher moedas suportadas sem perder página, carrinho ou contexto de pedido. Também exige que português do Brasil, inglês e espanhol cubram interface, conteúdo comercial, mensagens transacionais, e-mails, formulários, políticas, suporte e SEO, com datas, endereços, moedas e números sem ambiguidade. [Fonte: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md:264-281`, `:957-959`]

O adendo reforça que idioma deve ser propriedade explícita de conteúdo e comunicação, com fallback editorial controlado, sem mistura silenciosa de idiomas. Também define que localização regional pode sugerir idioma/moeda, mas a preferência manual vence; essa parte fica fora desta story. [Fonte: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/addendum.md:231-239`]

O UX define que os idiomas do lançamento são português do Brasil, inglês e espanhol, e que carrinho, pedido e Área da Cliente devem preservar contexto ao trocar idioma. Nesta story, isso vira preparação estrutural; a preservação real depende de Carrinho/Pedido futuros. [Fonte: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md:23`]

### Arquitetura obrigatória

- Browser → Next.js Frontend/BFF → Laravel API.
- Next.js é dono de renderização pública, SEO, navegação e composição de conteúdo estático.
- Laravel continua sendo autoridade de domínio, estado e regras comerciais.
- BFF não pode conter regra de preço, carrinho, pedido, pagamento, briefing, aprovação, frete ou moeda transacional.
- Se futuramente idioma/moeda afetarem carrinho/pedido/preço, a fonte de verdade deve ser Laravel/PostgreSQL.

Fontes:

- `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md:50-101`
- `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md:191-241`

### Stack e versões que devem ser preservadas

- Frontend/BFF: Next.js `16.3.0`, React `19.2.0`, TypeScript.
- Node local/CI: `24.x`.
- E2E: Playwright `1.60.0`.
- Backend/API preservado: PHP `8.5.x`, Laravel `13.x`.
- Banco/cache preservados: PostgreSQL `18.x`, Redis.

Não voltar para Next.js 16.2.x; a Story 1.1 atualizou para 16.3.0 por decisão de segurança.

Fonte: `apps/web/package.json`.

### Informação técnica atual do Next.js

Usar App Router. Pages e layouts são Server Components por padrão. Metadata estática deve continuar com `Metadata`; metadata dinâmica só se houver necessidade real. [Fonte: `apps/web/node_modules/next/dist/docs/01-app/03-api-reference/04-functions/generate-metadata.md`]

A documentação local do Next.js 16 orienta i18n por rotas internacionalizadas e conteúdo localizado. O padrão recomendado para App Router usa `[lang]`, dicionários e, quando necessário, `proxy.ts` para redirecionamento por locale. **Não implementar isso nesta story**, mas estruturar conteúdo de forma que não bloqueie essa migração futura. [Fonte: `apps/web/node_modules/next/dist/docs/01-app/02-guides/internationalization.md`]

No Next.js 16, `middleware` foi renomeado/deprecado em favor de `proxy` para essa camada de roteamento. Se uma story futura implementar redirecionamento por locale, deve considerar `proxy.ts`, não `middleware.ts`. [Fonte: `apps/web/node_modules/next/dist/docs/01-app/02-guides/upgrading/version-16.md`; fonte oficial verificada: https://nextjs.org/docs/app/guides/internationalization]

### Estado atual dos arquivos que serão alterados

- `apps/web/src/app/layout.tsx`
  - Estado atual: root layout com `metadata`, `<html lang="pt-BR">`, `<body>{children}</body>` e import de `globals.css`.
  - Mudança esperada: idealmente nenhuma ou mínima; preservar `lang="pt-BR"`.
  - Preservar: root layout obrigatório, import global e metadata geral.

- `apps/web/src/app/(public)/page.tsx`
  - Estado atual: home pública com textos hardcoded para eyebrow, `h1`, lead, CTAs, cards conceituais e seção de próximos caminhos.
  - Mudança esperada: consumir conteúdo centralizado `pt-BR`.
  - Preservar: estrutura semântica, `h1` único, CTAs e ausência de catálogo/preço real.

- `apps/web/src/features/public-store/publicLayoutContent.ts`
  - Estado atual: centraliza parte de nav/footer/placeholders.
  - Mudança esperada: virar façade para conteúdo `pt-BR` ou ser substituído por módulo de conteúdo mais abrangente e tipado.
  - Preservar: `mainNavItems`, `footerSections`, `publicCta`, `placeholderPages` ou exports equivalentes para não quebrar componentes.

- `apps/web/src/components/layout/SiteHeader.tsx`
  - Estado atual: consome `mainNavItems` e `publicCta`, mas mantém textos de marca/subtítulo e `aria-label` hardcoded.
  - Mudança esperada: consumir conteúdo centralizado para brand label, subtítulo, nav label e CTA.
  - Preservar: `next/link`, header sem client component, links/hrefs e alvos de toque.

- `apps/web/src/components/layout/SiteFooter.tsx`
  - Estado atual: textos institucionais e confiança hardcoded, footer sections vindas de `publicLayoutContent.ts`.
  - Mudança esperada: consumir conteúdo centralizado para textos de marca, contato/suporte, confiança e copyright.
  - Preservar: seção de links, sem WhatsApp funcional e mensagem aprovada “Pagamentos protegidos e processados por parceiros certificados.”

- `apps/web/src/components/layout/PlaceholderPage.tsx`
  - Estado atual: consome `placeholderPages` mas mantém CTAs de fallback hardcoded.
  - Mudança esperada: CTAs de fallback vindos de conteúdo centralizado.
  - Preservar: placeholders honestos e não transacionais.

- `apps/web/tests/e2e/foundation.spec.ts`
  - Estado atual: valida layout público, links/hrefs, mobile 320 px, teclado, noindex, placeholders, `/health` e `/api/health`.
  - Mudança esperada: acrescentar cobertura de conteúdo PT-BR/acentos/UTF-8 sem reduzir os testes existentes.
  - Preservar: teste BFF `/api/health`; ele exige Laravel API ativa.

### Estrutura recomendada

```text
apps/web/src/
  i18n/
    locales.ts                 # defaultLocale, supportedLocales, SupportedLocale
    publicContent.ts           # conteúdo público pt-BR tipado
    publicContent.types.ts     # tipos do contrato editorial, se útil
  features/
    public-store/
      publicLayoutContent.ts   # façade/exports compatíveis, se mantido
```

Estrutura alternativa aceitável:

```text
apps/web/src/features/public-store/content/
  locales.ts
  pt-BR.ts
  types.ts
```

Escolha uma estrutura e mantenha a coesão. Não duplicar a mesma cópia em `i18n/` e `features/public-store/`.

### Contrato mínimo de conteúdo

O contrato deve cobrir, no mínimo:

- `brand`: nome, aria-label da home, subtítulo curto.
- `navigation`: labels, hrefs, descrições e `ariaLabel`.
- `cta`: CTA principal `Ver produtos` e CTA secundário `Buscar por tema`.
- `home`: eyebrow, título, lead, cards conceituais e seção de próximos caminhos.
- `footer`: título, textos institucionais, contato/suporte, confiança, copyright e seções.
- `placeholders`: title, eyebrow, description e CTA fallback.
- `metadata`: title/description de home e placeholders.
- `qualityCopy`: strings usadas por testes para validar acentuação e diferenciação de modalidades.

Usar `as const`/tipos TypeScript para tornar quebras visíveis em `npm run typecheck`.

### Conteúdo obrigatório a preservar/refinar

Termos aprovados:

- `JS Designs`
- `Área da Cliente`
- `lembrancinhas físicas personalizadas`
- `convites digitais personalizados`
- `Produto Digital Pronto`
- `Silhouette Studio`
- `Pagamentos protegidos e processados por parceiros certificados.`
- `Ver produtos`
- `Buscar por tema`

Mensagens obrigatórias:

- Convites digitais personalizados não são entrega imediata: precisam de edição/criação, prévia e aprovação.
- Produto Digital Pronto não inclui personalização e só terá download imediato após pagamento confirmado, em story futura.
- Produtos físicos personalizados exigirão configuração/personalização antes do carrinho, em stories futuras.
- Placeholders não simulam preço, disponibilidade, carrinho, login, checkout, suporte ou compra.

### Regras de UX/microcopy

- Textos diretos, humanos e confiáveis.
- CTAs devem ser específicos; evitar “Finalizar agora” para algo que ainda exige configuração.
- Não usar “100% seguro”; usar a frase aprovada de pagamentos protegidos.
- Não usar textos longos em controles do header.
- Textos de botão devem caber em 320 px sem truncamento.
- Não misturar português de Portugal e português do Brasil sem motivo; preferir “frete” e “faturamento” quando for genérico, mantendo “Fatura” como documento fiscal português quando aplicável.

Fontes:

- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md:81-99`
- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md:277-287`
- `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/review-editorial-prose.md`

### Inteligência da Story 1.2

- Story 1.2 está `done` e foi mergeada em `main` no commit `9080598`.
- A base pública atual usa route group `apps/web/src/app/(public)/`.
- A página técnica `/health` fica fora do `PublicShell` e deve continuar assim.
- Componentes públicos são Server Components; não houve necessidade de `use client`.
- A code review da 1.2 corrigiu:
  - navegação mobile sem overflow horizontal;
  - contraste AA no card accent;
  - tipografia sem escala por `vw`;
  - hrefs de header/footer em testes;
  - metadata de placeholders;
  - ausência estrutural de WhatsApp;
  - landmarks e `h1` único.
- Reaproveitar esses testes e não afrouxar asserts existentes.

Fonte: `_bmad-output/implementation-artifacts/1-2-criar-o-layout-publico-base-da-loja.md`.

### Anti-patterns proibidos

- Não usar Python em scripts, build, testes, tooling ou documentação do produto.
- Não alterar backend Laravel para organizar texto público.
- Não adicionar biblioteca de i18n antes de necessidade real.
- Não implementar troca de idioma/moeda de forma falsa ou parcial.
- Não criar dropdown de idioma que não funciona.
- Não criar rotas localizadas que quebrem URLs atuais.
- Não usar `middleware.ts` para i18n no Next 16; se uma story futura precisar de roteamento por locale, pesquisar e usar `proxy.ts`.
- Não codificar tradução em componentes React quando a cópia for editorial/reutilizável.
- Não misturar idiomas na mesma página sem fallback explícito.
- Não expor API interna ou segredo no navegador.
- Não implementar catálogo, busca real, carrinho, checkout, suporte, cupom ou pagamento.
- Não adicionar WhatsApp flutuante permanente.

### Testing Requirements

Mínimo obrigatório:

- `npm run lint`
- `npm run typecheck`
- `npm run build`
- `npm run test:e2e` com Laravel API ativa

Cobertura mínima de teste:

- `html` mantém `lang="pt-BR"`.
- Home renderiza textos com acentos corretamente.
- Header e footer renderizam labels PT-BR com hrefs preservados.
- Placeholders renderizam títulos/descrições PT-BR e continuam honestos/não transacionais.
- Textos diferenciam produto físico personalizado, convite digital personalizado e Produto Digital Pronto.
- Convite digital personalizado informa que não é entrega imediata.
- Ausência de mojibake evidente (`Ã`, `�`) no texto visível ou HTML renderizado.
- Testes da Story 1.2 continuam passando.

### Definition of Done

- Story implementada somente no escopo frontend público/textual.
- Conteúdo público base centralizado e tipado.
- Locale padrão `pt-BR` definido e documentado no código.
- Locales futuros `en` e `es` aparecem apenas como contrato/preparação, sem conteúdo ativo incompleto.
- Home, header, footer, placeholders e metadata pública consomem a fonte centralizada.
- Todas as páginas públicas existentes continuam acessíveis nas mesmas URLs.
- `/health` técnico e `/api/health` BFF preservados.
- Textos visíveis em português do Brasil, UTF-8 e sem mojibake.
- Nenhuma regressão nos testes de acessibilidade/mobile básicos da Story 1.2.
- Checks locais relevantes passam.
- Nenhum Python adicionado.

## Dev Agent Record

### Agent Model Used

Codex GPT-5.

### Debug Log References

- 2026-08-05 — Início da implementação; `baseline_commit` preservado como `9080598`; customização `bmad-dev-story` resolvida manualmente sem Python.
- 2026-08-05 — RED: novos testes E2E de idioma/conteúdo falharam antes da implementação; smoke BFF também falhou por Laravel API inativa.
- 2026-08-05 — GREEN/REFACTOR: conteúdo público centralizado em `apps/web/src/i18n/`, componentes ligados à façade e microcopy refinada.
- 2026-08-05 — Validações frontend aprovadas: `npm run lint`, `npm run typecheck`, `npm run build` e `npx playwright test --grep-invert "BFF consulta"`.
- 2026-08-05 — `npm run test:e2e` completo permanece bloqueado por ambiente: sem serviço em `127.0.0.1:8000`, sem portas `5432` e `6379` ativas para PostgreSQL/Redis.
- 2026-08-05 — Revisão adversarial registrada em `_bmad-output/implementation-artifacts/reviews/review-1-3-implementar-idioma-textos-base-e-estrutura-publica-em-portugues-do-brasil-adversarial.md`.
- 2026-08-06 — Code review BMAD executado; 4 achados de patch aplicados e revalidados.
- 2026-08-06 — Validações após code review: `npm run lint`, `npm run typecheck`, `npm run build` e `npx playwright test --grep-invert "BFF consulta"` passaram.
- 2026-08-06 — Ambiente BFF/API reativado com Docker Desktop, Postgres em `5432`, Redis em `6379` e Laravel API em `127.0.0.1:8000`.
- 2026-08-06 — `npm run test:e2e` completo passou com 10/10 testes.

### Completion Notes List

- Story criada via `bmad-create-story` em 2026-08-05.
- Customização BMAD resolvida manualmente por leitura de TOML, sem Python, preservando a regra do projeto.
- Análise considerou PRD, addendum, UX, arquitetura Laravel/BFF, Story 1.2 e documentação local/oficial do Next.js sobre App Router, metadata e internationalization.
- Implementada estrutura tipada de locale/conteúdo em `apps/web/src/i18n/` com `pt-BR` ativo e `en`/`es` apenas como contrato futuro.
- Refatorados home, header, footer, placeholders e metadata pública para consumir conteúdo centralizado sem criar rotas localizadas, seletor de idioma, cookies, `proxy.ts` ou biblioteca i18n.
- Microcopy diferencia lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto, sem simular preço, carrinho, checkout, pagamento ou compra real.
- Testes E2E cobrem `html lang="pt-BR"`, acentos, ausência de mojibake renderizado, modalidades, metadata placeholder e preservação dos testes da Story 1.2.
- Code review BMAD corrigiu centralização do skip link, uso do locale tipado no `<html lang>`, regex de mojibake e consumo de `qualityCopy` pelos testes.
- Story concluída após Laravel API + PostgreSQL + Redis ficarem ativos e `npm run test:e2e` passar completo.

### File List

- `_bmad-output/implementation-artifacts/1-3-implementar-idioma-textos-base-e-estrutura-publica-em-portugues-do-brasil.md`
- `_bmad-output/implementation-artifacts/reviews/review-1-3-implementar-idioma-textos-base-e-estrutura-publica-em-portugues-do-brasil-adversarial.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `apps/web/src/i18n/locales.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/src/i18n/publicContent.ts`
- `apps/web/src/features/public-store/publicLayoutContent.ts`
- `apps/web/src/app/layout.tsx`
- `apps/web/src/app/(public)/page.tsx`
- `apps/web/src/app/(public)/buscar/page.tsx`
- `apps/web/src/app/(public)/carrinho/page.tsx`
- `apps/web/src/app/(public)/categorias/page.tsx`
- `apps/web/src/app/(public)/entrar/page.tsx`
- `apps/web/src/app/(public)/entrega/page.tsx`
- `apps/web/src/app/(public)/politicas/page.tsx`
- `apps/web/src/app/(public)/privacidade/page.tsx`
- `apps/web/src/app/(public)/produtos/page.tsx`
- `apps/web/src/app/(public)/suporte/page.tsx`
- `apps/web/src/app/(public)/termos/page.tsx`
- `apps/web/src/app/(public)/trocas-e-reembolso/page.tsx`
- `apps/web/src/components/layout/PlaceholderPage.tsx`
- `apps/web/src/components/layout/PublicShell.tsx`
- `apps/web/src/components/layout/SiteFooter.tsx`
- `apps/web/src/components/layout/SiteHeader.tsx`
- `apps/web/src/app/globals.css`
- `apps/web/tests/e2e/foundation.spec.ts`

### Change Log

- 2026-08-05 — Criada story 1.3 com contexto de implementação para conteúdo PT-BR, estrutura de locale futura e guardrails de escopo.
- 2026-08-05 — Implementada base de conteúdo público PT-BR tipada no frontend, com testes E2E de idioma/UTF-8 e revisão adversarial registrada; conclusão bloqueada pelo ambiente BFF/API.
- 2026-08-06 — Aplicados os 4 patches do code review BMAD; ambiente BFF/API reativado e E2E completo aprovado com 10/10 testes.
