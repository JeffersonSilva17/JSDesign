---
baseline_commit: a62dba4b831963d3c8cb0c7700cf63fd1d94ac88
---

# Story 1.1: Inicializar a fundação técnica da plataforma

Status: done

## Story

As a equipe de desenvolvimento,  
I want uma base funcional com Next.js BFF, Laravel API, PostgreSQL, Redis e pipeline CI/CD inicial,  
so that as próximas funcionalidades da loja sejam implementadas sobre uma arquitetura consistente, testável e alinhada ao padrão definido.

## Requisitos cobertos

- AR-1, AR-2, AR-3, AR-4, AR-5, AR-6, AR-7, AR-8, AR-12, AR-28
- NFR-3, NFR-4, NFR-5

## Acceptance Criteria

1. **Given** o repositório da JS Designs  
   **When** a aplicação for iniciada em ambiente local  
   **Then** devem existir aplicações separadas para Frontend/BFF em Next.js e Backend/API em Laravel  
   **And** o Frontend/BFF deve conseguir chamar um endpoint saudável da Laravel API

2. **Given** a Laravel API  
   **When** o backend for configurado  
   **Then** deve usar PostgreSQL como banco principal  
   **And** deve usar Redis para cache/fila, sem tratar Redis como fonte durável de dados  
   **And** Eloquent deve ficar restrito à camada de Infrastructure/persistência

3. **Given** a arquitetura aprovada  
   **When** o backend for organizado  
   **Then** deve existir separação inicial por Domain, Application, Infrastructure e Interfaces  
   **And** regras principais de negócio não devem ser colocadas no BFF

4. **Given** o pipeline CI/CD  
   **When** houver push ou pull request  
   **Then** devem rodar checks mínimos de backend, frontend e testes automatizados iniciais  
   **And** falhas nesses checks devem bloquear a aprovação técnica

5. **Given** a base inicial  
   **When** um dev executar os comandos documentados  
   **Then** deve conseguir subir a stack local e validar a comunicação Frontend/BFF → Laravel API

## Tasks / Subtasks

- [x] Criar estrutura monorepo mínima aprovada (AC: 1, 3)
  - [x] Criar `apps/web/` para Next.js Frontend+BFF.
  - [x] Criar `apps/api/` para Laravel API.
  - [x] Criar `packages/contracts/` para contratos compartilhados/OpenAPI quando necessário.
  - [x] Criar `infra/docker/` para arquivos de ambiente local.
  - [x] Criar `.github/workflows/` para CI/CD.

- [x] Scaffold do backend Laravel API (AC: 1, 2, 3)
  - [x] Inicializar Laravel 13.x em `apps/api`.
  - [x] Configurar PHP 8.5.x como runtime esperado.
  - [x] Configurar PostgreSQL como conexão principal.
  - [x] Configurar Redis para cache/fila.
  - [x] Criar endpoint versionado `GET /api/v1/health`.
  - [x] Criar teste HTTP/Feature para `GET /api/v1/health`.
  - [x] Criar esqueleto de módulos Laravel sem implementar domínios futuros: `Catalog`, `Cart`, `Pricing`, `Checkout`, `Orders`, `Briefing`, `Artwork`, `Files`, `Fulfillment`, `DigitalDelivery`, `Support`, `Notifications`, `Admin`, `I18n`, `Legal`.
  - [x] Em cada módulo criado agora, manter somente estrutura vazia ou arquivos mínimos necessários; não criar tabelas de domínio antecipadamente.

- [x] Scaffold do frontend/BFF Next.js (AC: 1, 3)
- [x] Inicializar Next.js 16.3.x + React 19.2.x + TypeScript em `apps/web`.
  - [x] Criar rota/página mínima de health local do frontend.
  - [x] Criar rota BFF server-side que consulta `GET /api/v1/health` da Laravel API.
  - [x] Garantir que segredos e URL interna da API fiquem em variáveis de ambiente server-side, nunca expostos ao navegador.
  - [x] Criar teste mínimo de renderização/build para a aplicação web.

- [x] Configurar ambiente local documentado (AC: 1, 2, 5)
  - [x] Criar `.env.example` do backend com variáveis de PostgreSQL, Redis, app URL e API versioning.
  - [x] Criar `.env.example` do frontend com URL server-side da Laravel API.
  - [x] Criar configuração local de PostgreSQL 18.x e Redis em `infra/docker/`.
  - [x] Documentar comandos para subir backend, frontend, banco, Redis, filas e testes.
  - [x] Documentar que Python não faz parte da stack do produto.

- [x] Configurar CI/CD inicial com GitHub Actions (AC: 4)
  - [x] Criar workflow de backend: instalar dependências PHP/Laravel, validar estilo/análise estática quando configurada e rodar testes Laravel.
  - [x] Criar workflow de frontend: instalar dependências, rodar lint/typecheck/build.
  - [x] Criar workflow ou job e2e/smoke mínimo com Playwright conforme versão definida pela arquitetura.
  - [x] Garantir que CI falhe quando build/testes falharem.
  - [x] Não configurar deploy nesta story.

- [x] Criar testes de integração/smoke da fundação (AC: 1, 4, 5)
  - [x] Teste backend confirma resposta saudável de `/api/v1/health`.
  - [x] Teste BFF confirma que o Next.js consegue consultar o health da API.
  - [x] Teste local ou e2e mínimo confirma que a aplicação web carrega e exibe estado operacional básico.

- [x] Atualizar documentação operacional mínima (AC: 5)
  - [x] Criar ou atualizar `README.md` raiz com comandos essenciais.
  - [x] Documentar portas locais, variáveis obrigatórias e ordem de inicialização.
  - [x] Documentar como rodar testes backend, frontend e e2e.
  - [x] Documentar limites explícitos da Story 1.1: sem catálogo real, sem carrinho, sem checkout, sem autenticação completa e sem tabelas de domínio futuras.


### Review Findings

- [x] [Review][Patch] Tornar o healthcheck backend honesto sobre PostgreSQL/Redis [`apps/api/app/Http/Controllers/Api/V1/HealthController.php:11`] — O endpoint `/api/v1/health` retorna `ok` sem consultar PostgreSQL nem Redis; isso enfraquece AC1/AC2/AC5 porque BFF/CI/local podem considerar a API saudável com dependências obrigatórias fora do ar.
- [x] [Review][Patch] Remover ou alinhar o healthcheck Laravel não versionado `/up` [`apps/api/bootstrap/app.php:12`] — A aplicação expõe `/up` além de `/api/v1/health`, criando duas semânticas de saúde e risco de operação/CI validar o endpoint errado.
- [x] [Review][Patch] Transformar o backend em API-only, removendo rota web/welcome/Vite desnecessários [`apps/api/routes/web.php:6`] — O Laravel ainda renderiza HTML (`welcome`) e mantém package/Vite/scripts npm, borrando a separação Next.js Frontend+BFF → Laravel API.
- [x] [Review][Patch] Remover scripts Composer que instalam/buildam frontend Laravel [`apps/api/composer.json:40`] — `composer setup/dev` ainda roda `npm install`, `npm run build` e `npm run dev` dentro da API, criando dependência Node/Vite não exigida para o backend REST.
- [x] [Review][Patch] Corrigir fallbacks que permitem SQLite/database cache/queue [`apps/api/config/database.php:20`] — `database.php`, `cache.php` e `queue.php` ainda caem para `sqlite`/`database`, contrariando PostgreSQL principal e Redis cache/fila quando `.env` está incompleto.
- [x] [Review][Patch] Exercitar migrations e configuração PostgreSQL/Redis nos checks backend [`apps/api/phpunit.xml:25`] — Os testes atuais usam `CACHE_STORE=array` e `QUEUE_CONNECTION=sync`; CI sobe Redis/PostgreSQL mas não prova que cache/fila/migrations funcionam contra a stack exigida.
- [x] [Review][Patch] Tratar o scaffold Eloquent/User fora de Infrastructure [`apps/api/app/Models/User.php:3`] — O scaffold mantém `App\Models\User`, auth config, factory e seeder ativo fora de `Modules/*/Infrastructure`, criando precedente contra a regra de Eloquent restrito à persistência.
- [x] [Review][Patch] Não criar usuário previsível no seeder da fundação [`apps/api/database/seeders/DatabaseSeeder.php:20`] — `DatabaseSeeder` cria `Test User`/`test@example.com`, fora do escopo desta story e com risco de conta previsível se rodado no ambiente errado.
- [x] [Review][Patch] Restringir portas Docker a localhost e usar variáveis de porta [`infra/docker/compose.yaml:13`] — PostgreSQL/Redis estão publicados como `5432:5432` e `6379:6379`, expondo serviços em todas as interfaces e ignorando `POSTGRES_PORT`/`REDIS_PORT` do `.env.example`.
- [x] [Review][Patch] Remover ou conectar `API_VERSION` à configuração real [`apps/api/.env.example:6`] — `API_VERSION=v1` existe, mas rota e payload hardcodeiam `v1`; isso cria configuração enganosa.
- [x] [Review][Patch] Trocar `minimum-stability` do Composer para `stable` [`apps/api/composer.json:82`] — `minimum-stability: dev` enfraquece reprodutibilidade futura da fundação sem necessidade evidente.
- [x] [Review][Defer] Pin de digest/imagem Docker exata para PostgreSQL/Redis [`infra/docker/compose.yaml:5`] — deferred, pre-existing policy/operational hardening; a story pediu PostgreSQL 18.x e Redis, não uma política completa de pin por digest.
- [x] [Review][Defer] Pin de GitHub Actions por SHA em vez de tags versionadas [`/.github/workflows/backend.yml:41`] — deferred, policy/operational hardening; os workflows já usam versões maiores explícitas e o gate da story é CI executável.
- [x] [Review][Defer] Tornar criação de `jsdesign_test` idempotente quando volume já existe [`infra/docker/postgres/init/01-create-test-db.sql:1`] — deferred, operational hardening; só afeta ambientes com volume antigo e pode ser tratado junto da documentação Docker.
- [x] [Review][Patch] Rodar smoke/e2e do BFF contra build de produção no CI [`apps/web/playwright.config.ts:13`] — O smoke atual usa `npm run dev`; isso pode aprovar integração em modo dev sem provar que `next build` + `next start` funcionam na rota BFF.
- [x] [Review][Patch] Sanitizar erro público retornado por `/api/health` no BFF [`apps/web/src/app/api/health/route.ts:19`] — Route Handlers são públicos; a resposta atual serializa `error.message` e pode vazar detalhes internos como variável de ambiente ou comportamento da API.
- [x] [Review][Patch] Adicionar timeout explícito na chamada server-side à Laravel API [`apps/web/src/bff/apiClient.ts:23`] — Sem `signal`/timeout, `/api/health` pode ficar preso até o limite da plataforma quando a API aceita conexão mas não responde.
- [x] [Review][Patch] Validar em runtime o payload de health recebido da Laravel API [`apps/web/src/bff/apiClient.ts:34`] — O cast TypeScript não valida JSON em runtime; um HTTP 200 com shape errado poderia fazer o BFF publicar `status: ok`.
- [x] [Review][Patch] Normalizar e validar `API_INTERNAL_URL` antes de montar a chamada upstream [`apps/web/src/bff/apiClient.ts:12`] — Valor com whitespace, protocolo inválido ou URL malformada falha tarde e com erro genérico.
- [x] [Review][Patch] Ignorar/remover `tsconfig.tsbuildinfo` gerado pelo TypeScript [`apps/web/tsconfig.tsbuildinfo:1`] — O arquivo é cache local gerado por `tsc --noEmit` com incremental e não deve entrar no versionamento.
- [x] [Review][Patch] Declarar versão Node esperada no frontend e documentá-la [`apps/web/package.json:1`] — CI usa Node 24, mas o projeto não declara `engines.node`, deixando ambiente local e CI sem contrato explícito.
- [x] [Review][Patch] Documentar explicitamente `API_INTERNAL_URL` na seção de smoke/e2e local [`README.md:88`] — O README depende implicitamente de `.env.local`; quem executar só a seção de smoke pode receber 503 por configuração ausente.
- [x] [Review][Patch] Documentar comando de worker/fila no README [`README.md:47`] — A story exige documentar backend, frontend, banco, Redis, filas e testes; o comando `php artisan queue:work` ainda não aparece.
- [x] [Review][Patch] Alinhar a story com a decisão aprovada de Next.js 16.3.x [`_bmad-output/implementation-artifacts/1-1-inicializar-a-fundacao-tecnica-da-plataforma.md:66`] — A implementação e o README usam Next 16.3.0 por decisão aprovada de segurança, mas a task/dev notes ainda mencionam 16.2.x.
- [x] [Review][Defer] Criar testes negativos dedicados para BFF degradado/API indisponível [`apps/web/tests/e2e/foundation.spec.ts:10`] — deferred, exige estrutura adicional de teste/servidor com ambiente alternativo; registrar para hardening de testes sem bloquear a fundação.
- [x] [Review][Patch] Tornar comandos do README reprodutíveis a partir da raiz do repositório [`README.md:23`] — Os blocos `cd apps/api` e `cd apps/web` podiam falhar se executados sequencialmente no mesmo terminal.
- [x] [Review][Patch] Usar `npm ci` no setup frontend documentado [`README.md:37`] — O projeto possui `package-lock.json` e o CI usa instalação reprodutível.
- [x] [Review][Patch] Aguardar health dos serviços Docker no setup local [`README.md:45`] — `docker compose up -d --wait` reduz corrida entre PostgreSQL/Redis e API/BFF.
- [x] [Review][Patch] Parametrizar variáveis PostgreSQL declaradas no exemplo Docker [`infra/docker/compose.yaml:9`] — `infra/docker/.env.example` declarava DB/user/password, mas o compose não consumia esses valores.
- [x] [Review][Patch] Remover `container_name` fixo do compose local [`infra/docker/compose.yaml:6`] — Nomes fixos dificultam múltiplos worktrees/projetos compose no mesmo host.
- [x] [Review][Patch] Rodar migrations no smoke CI antes de iniciar a API [`/.github/workflows/smoke.yml:63`] — O smoke agora prova que o schema técnico migra antes de validar BFF/API.
- [x] [Review][Patch] Adicionar timeouts e log de diagnóstico aos workflows [`/.github/workflows/smoke.yml:9`] — Jobs agora têm `timeout-minutes`; o smoke publica log do servidor Laravel em falha.
- [x] [Review][Patch] Exercitar Redis como cache/fila em teste de fundação [`apps/api/tests/Feature/FoundationConfigurationTest.php:15`] — O teste agora faz roundtrip de cache e consulta a conexão de fila Redis.
- [x] [Review][Patch] Remover teste unitário placeholder do Laravel [`apps/api/tests/Unit/ExampleTest.php:1`] — O teste `assertTrue(true)` não agregava evidência para a story.
- [x] [Review][Patch] Substituir README padrão do Laravel por README da API JS Designs [`apps/api/README.md:1`] — O arquivo padrão sugeria Laravel genérico e Laravel Boost, fora do handoff da story.
- [x] [Review][Patch] Tornar metadata Composer específica da API JS Designs [`apps/api/composer.json:3`] — `laravel/laravel` e descrição de skeleton eram resíduos do scaffold.
- [x] [Review][Patch] Registrar customização BMAD intencional na story [`_bmad/custom/bmad-dev-story.toml:1`] — A metodologia nova de revisão adversarial pós-story agora consta na File List.
- [x] [Review][Patch] Consolidar Completion Notes contraditórias [`_bmad-output/implementation-artifacts/1-1-inicializar-a-fundacao-tecnica-da-plataforma.md:405`] — Notas antigas de bloqueio foram substituídas por estado final atual.
- [x] [Review][Patch] Marcar story antiga 1.1 como superseded [`_bmad-output/implementation-artifacts/1-1-acessar-loja-base-visual-navegacao-global.md:3`] — Evita que agentes/ferramentas selecionem a story 1.1 errada.
- [x] [Review][Patch] Atualizar cabeçalho do relatório adversarial [`_bmad-output/implementation-artifacts/review-adversarial-1-1-inicializar-a-fundacao-tecnica-da-plataforma.md:3`] — O relatório agora explicita revisão pré-dev e pós-implementação.
- [x] [Review][Patch] Expor itens deferidos também no sprint status [`_bmad-output/implementation-artifacts/sprint-status.yaml:108`] — Dívidas aceitas aparecem em `action_items` além do ledger dedicado.
## Dev Notes

### Escopo exato desta story

Esta story cria a base técnica executável. Ela não implementa produto, catálogo, carrinho, checkout, briefing, aprovação, entrega, suporte ou admin funcional. Esses domínios aparecem como estrutura modular para orientar a arquitetura, mas suas tabelas, regras e casos de uso só devem nascer nas stories correspondentes.

Resultado esperado da story:

- `apps/web` executa Next.js.
- `apps/api` executa Laravel.
- PostgreSQL e Redis sobem em ambiente local.
- Next.js BFF consulta a Laravel API por um endpoint de health.
- CI roda checks mínimos de backend, frontend e smoke/e2e.
- README permite outro dev subir e validar a base.

### Arquitetura obrigatória

- Browser → Next.js Frontend+BFF → Laravel API → Domain → PostgreSQL/Redis/adapters.
- O navegador deve consumir Next.js. Next.js chama Laravel por contrato server-side ou controlado.
- Laravel é autoridade de domínio, estado e regras comerciais.
- BFF pode compor tela, adaptar payload, SSR/SEO, sessão/cookies e cache de leitura; não pode calcular preço, decidir estado de pedido, validar domínio ou duplicar regra comercial.
- API Laravel deve ser REST JSON versionada inicialmente em `/api/v1`.
- `payment_state` e `order_state` devem ser conceitos separados quando existirem, mas esta story não cria pedido/pagamento.
- Jobs/filas futuros pertencem ao Laravel. Redis é cache/fila, não fonte durável.

Fonte: `architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md` — AD-1 a AD-15.

### Backend Laravel: organização obrigatória

Estrutura esperada:

```text
apps/api/
  app/
    Modules/
      Catalog/
        Domain/
        Application/
        Infrastructure/
        Interfaces/Http/
      Cart/
      Pricing/
      Checkout/
      Orders/
      Briefing/
      Artwork/
      Files/
      Fulfillment/
      DigitalDelivery/
      Support/
      Notifications/
      Admin/
      I18n/
      Legal/
  database/
    migrations/
    seeders/
  tests/
    Feature/
    Unit/
```

Regras:

- Domain não depende de HTTP, Eloquent, filas, storage ou gateways.
- Application orquestra casos de uso.
- Infrastructure contém Eloquent, repositories, queries, migrations e adapters.
- Interfaces/Http contém controllers, requests/resources e rotas HTTP.
- Eloquent está incluso, mas não deve virar entidade de domínio.
- Nesta story, criar somente migration/configuração técnica necessária para Laravel funcionar. Não criar tabelas de catálogo, pedido, pagamento, briefing ou usuário/cliente além do que o scaffold exigir.

Fonte: `ARCHITECTURE-SPINE.md` — AD-3, AD-4, Estrutura Inicial.

### Frontend/BFF Next.js: organização obrigatória

Estrutura esperada:

```text
apps/web/
  app/
  src/
    components/
    features/
    bff/
    i18n/
  tests/e2e/
```

Regras:

- Usar TypeScript.
- BFF deve rodar server-side para chamadas à API interna.
- Não expor tokens, credenciais, DSN ou URL interna sensível no bundle do navegador.
- Página inicial nesta story pode ser mínima/operacional; a identidade visual completa vem na Story 1.2.
- Preparar `src/i18n/`, mas não implementar a experiência completa de idiomas nesta story; isso é Story 1.3.

Fonte: `ARCHITECTURE-SPINE.md` — AD-1, AD-2, Estrutura Inicial.

### Ambiente local

Configurar ambiente local com:

- PostgreSQL 18.x como banco transacional.
- Redis como cache/fila.
- Laravel apontando para PostgreSQL e Redis.
- Next.js apontando server-side para a Laravel API.

Não usar Python em scripts, build, testes, tooling ou documentação do produto. Qualquer automação local deve usar ferramentas da stack: PHP/Composer/Laravel, Node/package manager, Docker/GitHub Actions.

### CI/CD mínimo

Criar GitHub Actions com cobertura inicial:

- Backend:
  - instalar PHP 8.5.x;
  - instalar dependências Composer;
  - preparar `.env` de teste;
  - rodar testes Laravel;
  - rodar análise estática/lint somente se configurado no scaffold.
- Frontend:
  - instalar Node;
  - instalar dependências;
  - rodar lint/typecheck/build.
- E2E/smoke:
  - rodar teste mínimo do frontend/BFF ou preparar job Playwright inicial conforme viável.

Não configurar deploy nem segredos de produção nesta story.

Fonte: `ARCHITECTURE-SPINE.md` — AD-15.

### Requisitos de qualidade relevantes

- NFR-3: falhas complementares não devem impedir fluxos essenciais; para esta story, healthchecks e documentação de retomada são obrigatórios.
- NFR-4: desenhar comandos e endpoints iniciais como idempotentes quando aplicável; healthcheck não pode ter efeito colateral.
- NFR-5: base deve permitir análise de dependências, revisão de código, testes e futuro gate de segurança.
- NFR-12/UX: app web deve ser preparado para responsividade, mas a UI completa vem depois.

Fonte: `prd-JSDESIGN-2026-07-25/prd.md` — NFR-3, NFR-4, NFR-5, NFR-12.

### UX relevante para a fundação

Mesmo que esta story não implemente o layout final, a fundação não pode impedir:

- mobile-first;
- WCAG 2.2 AA;
- foco visível;
- operação a partir de 320 px;
- SEO essencial;
- ausência de WhatsApp flutuante permanente;
- futuras rotas de Home, busca, Área da Cliente, carrinho e suporte.

Fonte: `ux-JSDESIGN-2026-07-26/DESIGN.md` e `EXPERIENCE.md`.

### Estado real do repositório antes desta story

Inspeção local em 2026-07-31:

- Não há `package.json`, `composer.json`, lockfiles ou apps scaffoldados no root atual.
- Existe `prototype/` com HTML/CSS/JS estático e assets visuais. Esse protótipo é referência, não base técnica do app Next.js.
- Existe `.github/agents/`, mas não há `.github/workflows/` de CI/CD.
- Existe `_bmad-output/implementation-artifacts/1-1-acessar-loja-base-visual-navegacao-global.md`, porém ele pertence à quebra antiga de stories e não corresponde à story atual. Não reutilizar como story anterior.

### Latest technical information

Verificação web em 2026-07-31:

- Laravel 13.x é a linha aprovada pela arquitetura; documentação oficial indica suporte Laravel 13 e PHP 8.3–8.5. Usar PHP 8.5.x conforme decisão da arquitetura.
- PHP 8.5 está em suporte ativo segundo php.net.
- Next.js 16.3 é a linha aprovada por decisão de segurança tomada durante a implementação; a linha 16.2 original foi substituída porque `npm audit --omit=dev` indicou correção em 16.3.0.
- React docs indicam React 19.2 como versão atual documentada.
- PostgreSQL docs atuais estão em 18.x.
- Playwright oficial já lista versão mais recente acima de 1.60; porém a arquitetura fixa Playwright 1.60.x. Não atualizar para 1.62+ nesta story sem revisar a arquitetura.

Fontes oficiais:

- Laravel releases: https://laravel.com/docs/13.x/releases
- Laravel queues: https://laravel.com/docs/13.x/queues
- PHP supported versions: https://www.php.net/supported-versions.php
- Next.js blog/releases: https://nextjs.org/blog e https://github.com/vercel/next.js/releases
- React versions: https://react.dev/versions
- PostgreSQL docs current: https://www.postgresql.org/docs/current/index.html
- Playwright release notes: https://playwright.dev/docs/release-notes
- GitHub Actions docs: https://docs.github.com/actions

### Project Structure Notes

- A estrutura aprovada é monorepo em `apps/`, `packages/`, `infra/` e `.github/workflows/`.
- Não há estrutura existente de aplicação para preservar; esta story cria os primeiros arquivos de app.
- Preservar `_bmad`, `_bmad-output`, `.agents`, `.github/agents` e `prototype/`.
- Não mover nem apagar o protótipo estático.
- Não apagar o story file antigo; ele fica como artefato histórico, mas não deve influenciar o sprint atual.

### Anti-patterns proibidos

- Não criar uma aplicação Laravel monolítica que também renderiza a loja pública.
- Não fazer o navegador chamar diretamente uma API interna sensível se a chamada deveria passar pelo BFF.
- Não colocar regra de preço, pedido, pagamento, briefing ou aprovação no Next.js.
- Não transformar Models Eloquent em entidades ricas de domínio.
- Não criar todas as tabelas do e-commerce nesta story.
- Não usar SQLite como banco principal da aplicação.
- Não usar Redis para estado durável.
- Não adicionar Python como dependência de produto, automação, build ou testes.
- Não copiar visual, paleta, claims ou composição da referência Gio.

### Suggested implementation order

1. Criar estrutura de diretórios.
2. Scaffold Laravel API.
3. Scaffold Next.js web/BFF.
4. Configurar ambiente local PostgreSQL/Redis.
5. Criar healthcheck Laravel.
6. Criar chamada BFF para healthcheck.
7. Criar testes mínimos.
8. Criar CI/CD.
9. Atualizar README.

### Testing Requirements

Mínimo obrigatório:

- Backend:
  - Feature/HTTP test para `GET /api/v1/health`.
  - Teste deve validar status HTTP 200 e payload JSON previsível.
- Frontend/BFF:
  - Teste de build/typecheck.
  - Teste da rota BFF/health validando integração com API quando ambiente estiver ativo.
- E2E/smoke:
  - Playwright deve abrir a aplicação web e validar que a página inicial/fundação responde.
- CI:
  - Backend tests e frontend build/typecheck devem rodar em GitHub Actions.
  - Falha de teste/build deve quebrar o workflow.

### Definition of Done

- Estrutura `apps/web`, `apps/api`, `packages/contracts`, `infra/docker` e `.github/workflows` criada.
- Laravel API sobe localmente e responde `GET /api/v1/health`.
- Next.js sobe localmente e consegue consultar o health da API por rota BFF server-side.
- PostgreSQL e Redis configurados para ambiente local.
- README documenta setup e comandos.
- CI/CD mínimo criado e executável.
- Testes mínimos backend/frontend/smoke criados.
- Nenhuma regra de domínio futura implementada antecipadamente.
- Nenhuma dependência Python adicionada.

## Dev Agent Record

### Agent Model Used

GPT-5 Codex.

### Debug Log References

- 2026-08-01 — Início do `bmad-dev-story`; pré-checagem local encontrou `php: NOT_FOUND`, `composer: NOT_FOUND`, `docker: NOT_FOUND`, `node: v24.14.0`, `npm: 11.9.0`, `git: 2.53.0.windows.1`.
- 2026-08-01 — Implementação interrompida por HALT técnico: não é possível scaffoldar/validar Laravel API, Composer dependencies, PostgreSQL/Redis via Docker e testes backend reais sem PHP, Composer e Docker.
- 2026-08-01 — Após autorização de instalação, PHP 8.5.8 foi instalado via `winget` em escopo de usuário e configurado com extensões necessárias (`openssl`, `curl`, `fileinfo`, `intl`, `mbstring`, `pdo_pgsql`, `pgsql`, `zip`).
- 2026-08-01 — Composer 2.10.2 foi instalado manualmente em `C:\Users\Admin\AppData\Local\Programs\Composer`, com assinatura SHA-384 validada contra `https://composer.github.io/installer.sig`.
- 2026-08-01 — Docker Desktop 4.84.0 foi instalado via `winget`; CLI disponível em `C:\Program Files\Docker\Docker\resources\bin\docker.exe`, mas o daemon não inicia porque WSL/Virtual Machine Platform ainda exige privilégios de administrador.
- 2026-08-01 — Tentativa de instalar WSL via `winget install Microsoft.WSL` falhou com `administrator privileges are required`. Retomada completa da Story 1.1 ainda depende de WSL/Docker operacional ou alternativa equivalente para PostgreSQL/Redis.

- 2026-08-02 — Retomada do `bmad-dev-story` sem Python. O scaffold Laravel 13 foi criado parcialmente/com sucesso em `apps/api` via Composer; `composer show laravel/framework` confirmou `laravel/framework v13.23.0`.
- 2026-08-02 — Verificação de ambiente confirmou novamente que o WSL não está instalado. Docker Desktop/daemon continua indisponível, então PostgreSQL 18.x e Redis locais via `infra/docker` não podem ser validados nesta sessão.
- 2026-08-02 — Implementação interrompida por HALT técnico antes de marcar tasks: a Definition of Done exige stack local com PostgreSQL/Redis e comunicação Frontend/BFF → Laravel API validada.
- 2026-08-02 — Nova tentativa solicitada por Sharom: sessão atual não está elevada (`is_admin=False`); `wsl --install --no-distribution` falhou porque WSL não está instalado/ativado e a operação exige privilégios do Windows.
- 2026-08-02 — Alternativas locais verificadas no PATH: `psql`, `postgres`, `pg_ctl`, `redis-server` e `redis-cli` não encontrados. Sem Docker/WSL ou PostgreSQL/Redis nativos, a validação local exigida pela story permanece bloqueada.
- 2026-08-04 — Sharom executou `wsl --install` como administrador; WSL 2.7.11 e `VirtualMachinePlatform` foram instalados, mas o Windows informou que as alterações só terão efeito após reinicialização.
- 2026-08-04 — Nova validação local confirmou `VirtualizationFirmwareEnabled=False` em CPU AMD Ryzen 5 1600. WSL2 ainda não inicia porque a virtualização de firmware/BIOS não está habilitada; Docker CLI/Compose existem, mas o daemon `dockerDesktopLinuxEngine` não conecta.
- 2026-08-04 — Após Sharom reiniciar e habilitar SVM no BIOS/UEFI, nova validação mostrou `VirtualizationFirmwareEnabled=True`, mas `HypervisorPresent=False`. Isso indica que a virtualização de firmware já chegou ao Windows, porém o hypervisor/recurso opcional do Windows ainda não carregou corretamente no boot.

- 2026-08-04 — Docker Desktop foi iniciado com sucesso após WSL2/virtualização ficarem operacionais; `docker version` passou a exibir Client e Server.
- 2026-08-04 — PostgreSQL 18 e Redis subiram via `infra/docker/compose.yaml`; o primeiro mount em `/var/lib/postgresql/data` falhou por mudança específica do `postgres:18`, corrigido para `/var/lib/postgresql` com novo volume `jsdesign-postgres18-data`.
- 2026-08-04 — Backend validado: `php artisan test` passou com 3 testes/4 assertions, incluindo `GET /api/v1/health`; `vendor/bin/pint --test` passou.
- 2026-08-04 — Frontend/BFF validado: `npm run lint`, `npm run typecheck`, `npm run build` passaram; Playwright smoke passou com 2 testes, incluindo a rota BFF `/api/health` consultando a Laravel API.
- 2026-08-04 — Bloqueio de conclusão: `npm audit --omit=dev` reporta 3 vulnerabilidades high em `next@16.2.0`/dependências transitivas. O fix recomendado pelo npm é `next@16.3.0`, fora da versão 16.2.x exigida pela story/arquitetura.

- 2026-08-04 — Sharom aprovou atualizar Next.js de 16.2.0 para 16.3.0 para resolver o bloqueio de segurança e reduzir risco futuro; `next` e `eslint-config-next` foram travados em `16.3.0` sem caret.
- 2026-08-04 — Após atualização para Next.js 16.3.0, `npm audit --omit=dev` passou com 0 vulnerabilidades; `npm run lint`, `npm run typecheck`, `npm run build`, `php artisan test`, `vendor/bin/pint --test` e `npm run test:e2e` passaram.
- 2026-08-04 — Revisão adversarial pós-implementação executada e registrada em `_bmad-output/implementation-artifacts/review-adversarial-1-1-inicializar-a-fundacao-tecnica-da-plataforma.md`; achados bloqueantes tratados antes de mover a story para review.

### Completion Notes List

- Implementação funcional da Story 1.1 concluída localmente com Laravel API, Next.js BFF, PostgreSQL, Redis, CI/CD, README e testes mínimos.
- Bloqueio de segurança de `next@16.2.0` resolvido por decisão explícita de Sharom com atualização para Next.js/eslint-config-next `16.3.0` sem caret.
- Nenhum Python foi usado em scripts, build, testes ou automação do produto.
- Revisão adversarial pós-implementação registrada e code review BMAD executado por grupos: Backend/API, Frontend/BFF e Docker/CI/CD/documentação/artefatos.
- Patches do code review foram aplicados e validados; hardenings fora do gate da fundação ficaram registrados em `deferred-work.md` e `sprint-status.yaml`.
- A story antiga `1-1-acessar-loja-base-visual-navegacao-global.md` foi marcada como `superseded` para evitar conflito com a nova Story 1.1 técnica.
- Story pronta para encerramento como `done`.

### File List

- `_bmad-output/implementation-artifacts/1-1-inicializar-a-fundacao-tecnica-da-plataforma.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `_bmad-output/implementation-artifacts/deferred-work.md`
- `_bmad-output/implementation-artifacts/review-adversarial-1-1-inicializar-a-fundacao-tecnica-da-plataforma.md`
- `_bmad-output/implementation-artifacts/1-1-acessar-loja-base-visual-navegacao-global.md`
- `_bmad/custom/bmad-dev-story.toml`
- `apps/api/`
- `packages/contracts/`
- `infra/docker/`
- `.github/workflows/`
- `.gitignore`
- `README.md`
- `.github/workflows/backend.yml`
- `.github/workflows/frontend.yml`
- `.github/workflows/smoke.yml`
- `apps/api/.env.example`
- `apps/api/app/Http/Controllers/Api/V1/HealthController.php`
- `apps/api/app/Modules/`
- `apps/api/bootstrap/app.php`
- `apps/api/composer.json`
- `apps/api/composer.lock`
- `apps/api/phpunit.xml`
- `apps/api/README.md`
- `apps/api/routes/api.php`
- `apps/api/tests/Feature/FoundationConfigurationTest.php`
- `apps/api/tests/Feature/HealthTest.php`
- `apps/web/.env.example`
- `apps/web/AGENTS.md`
- `apps/web/CLAUDE.md`
- `apps/web/eslint.config.mjs`
- `apps/web/next-env.d.ts`
- `apps/web/next.config.ts`
- `apps/web/package.json`
- `apps/web/package-lock.json`
- `apps/web/playwright.config.ts`
- `apps/web/src/app/api/health/route.ts`
- `apps/web/src/app/globals.css`
- `apps/web/src/app/health/page.tsx`
- `apps/web/src/app/layout.tsx`
- `apps/web/src/app/page.tsx`
- `apps/web/src/bff/apiClient.ts`
- `apps/web/src/components/.gitkeep`
- `apps/web/src/features/.gitkeep`
- `apps/web/src/i18n/.gitkeep`
- `apps/web/tests/e2e/foundation.spec.ts`
- `infra/docker/.env.example`
- `infra/docker/compose.yaml`
- `infra/docker/postgres/init/01-create-test-db.sql`
- `packages/contracts/README.md`

### Change Log

- 2026-08-04 — Implementada a fundação técnica Laravel API + Next.js BFF + PostgreSQL/Redis + CI/CD + README; a conclusão formal ficou temporariamente bloqueada até decisão de segurança/versionamento do Next.js.

- 2026-08-04 — Atualizado Next.js/eslint-config-next para 16.3.0 por decisão de Sharom; audit limpo, regressão completa aprovada e revisão adversarial pós-implementação registrada.

- 2026-08-04 — Code review Backend/API: 11 patches aplicados e validados; 2 itens de hardening operacional deferidos.
- 2026-08-04 — Code review Frontend/BFF: 10 patches aplicados e validados; 1 item de hardening de teste deferido.
- 2026-08-04 — Code review Docker/CI/CD/documentação/artefatos: 16 patches aplicados e validados; story e sprint atualizados para `done`.
