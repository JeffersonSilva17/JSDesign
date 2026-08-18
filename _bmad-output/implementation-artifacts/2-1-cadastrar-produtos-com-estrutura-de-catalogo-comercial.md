---
story_key: 2-1-cadastrar-produtos-com-estrutura-de-catalogo-comercial
story_id: "2.1"
epic: 2
status: done
created: 2026-08-13
baseline_commit: 54bfea20cd2b5e6ea244efd2c11424b96c512594
---

# Story 2.1: Cadastrar produtos com estrutura de catálogo comercial

Status: done

## Story

Como administradora da JS Designs,
quero cadastrar produtos com dados comerciais e taxonomia estruturados,
para que a loja consiga publicar, organizar e futuramente pesquisar itens físicos personalizados, digitais prontos e digitais personalizados sem ambiguidades.

## Escopo e resultado esperado

Esta story cria a primeira fatia vertical do domínio `Catalog` no Laravel: modelo de domínio, persistência PostgreSQL, validação de publicação e contrato administrativo REST. Ela prepara dados que serão consumidos pelas Stories 2.2–2.4 e pelo Epic 3.

Inclui:

- produto, categoria, taxonomia de descoberta e imagens como metadados referenciados;
- três modalidades comerciais: `physical_personalized`, `digital_ready` e `digital_personalized`;
- rascunho, publicação e retirada de publicação com validação por modalidade;
- dados comerciais mínimos exigidos pelo FR-5;
- bloqueio de publicação quando houver personagem/ativo protegido sem verificação de direitos de uso comercial;
- API administrativa versionada, protegida por autenticação e autorização fail-closed;
- testes unitários, de integração PostgreSQL e HTTP.

Não inclui:

- UI administrativa completa (Epic 8 / Story 8.2);
- listagens públicas, cards ou BFF público (Story 2.2);
- ranking, busca aproximada ou índice de busca público (Story 2.3);
- SEO público (Story 2.4);
- configuradores, cálculo por quantidade, descontos, carrinho ou checkout (Epics 3 e 4);
- upload binário ou escolha definitiva do provedor de storage; esta story registra referências de imagens já aceitas pelo adapter de arquivos;
- RBAC completo, gestão de usuários ou auditoria administrativa ampla (Story 8.5).

## Acceptance Criteria

1. **Criar rascunho com modalidade explícita**
   - **Dado** uma administradora autenticada e autorizada para manter o catálogo
   - **Quando** enviar um produto válido para `POST /api/v1/admin/catalog/products`
   - **Então** o Laravel deve criar um rascunho com ID opaco, `slug` único, nome, descrição, modalidade, categoria, estado de publicação e timestamps UTC
   - **E** a modalidade deve aceitar somente `physical_personalized`, `digital_ready` ou `digital_personalized`
   - **E** o endpoint deve responder `201` com um recurso administrativo estável, sem expor detalhes internos do banco.

2. **Registrar a estrutura comercial completa do FR-5**
   - **Dado** o cadastro ou a edição de um produto
   - **Quando** os dados comerciais forem persistidos
   - **Então** o produto deve suportar preço-base em unidades menores inteiras e moeda ISO, quantidade mínima ou referência de variante, personalização, prazo, materiais, composição, disponibilidade e tipo de entrega
   - **E** valores monetários não podem usar ponto flutuante
   - **E** regras de precificação por quantidade/variante podem ser referenciadas ou estruturadas para evolução, mas cálculo de preço e descontos não pertence a esta story.

3. **Associar categoria, tema, ocasião, personagem e termos alternativos**
   - **Dado** um produto com metadados de descoberta
   - **Quando** a administradora salvar o cadastro
   - **Então** deve poder associar uma categoria principal, zero ou mais temas, ocasiões, personagens/ativos protegidos e termos alternativos de busca
   - **E** valores de taxonomia devem ser normalizados para evitar duplicatas por caixa, espaços ou acentuação equivalente, preservando o rótulo editorial exibível
   - **E** personagens devem ser metadados de descoberta, nunca uma categoria pública ou promessa de disponibilidade por si só.

4. **Registrar imagens sem acoplar o domínio ao storage**
   - **Dado** imagens de produto já aceitas pelo mecanismo de arquivos aprovado
   - **Quando** forem associadas ao produto
   - **Então** o catálogo deve registrar referência opaca de storage, texto alternativo, ordem e indicação de imagem principal
   - **E** deve existir exatamente uma imagem principal quando o produto for publicado
   - **E** o domínio e a API não devem aceitar caminho de filesystem, URL interna arbitrária, binário base64 ou SVG não sanitizado como substituto da referência de storage.

5. **Salvar rascunho incompleto e publicar apenas produto comercialmente válido**
   - **Dado** um produto em rascunho
   - **Quando** faltarem campos obrigatórios para sua modalidade
   - **Então** alterações parciais válidas podem ser salvas sem tornar o produto público
   - **E** uma tentativa de publicação deve retornar `422` com erros tipados por campo e sem alterar o estado anterior
   - **E** a publicação deve exigir, no mínimo, nome, descrição, modalidade, categoria, preço/moeda, disponibilidade, tipo de entrega, prazo aplicável e uma imagem principal com texto alternativo
   - **E** as exigências condicionais abaixo devem ser aplicadas pelo domínio Laravel, não apenas pelo Form Request.

6. **Aplicar invariantes por modalidade**
   - **Dado** uma tentativa de publicação
   - **Quando** a modalidade for `physical_personalized`
   - **Então** devem existir quantidade mínima ou variantes, materiais/composição, indicação de personalização, prazo de criação/produção e entrega física
   - **Quando** a modalidade for `digital_personalized`
   - **Então** devem existir indicação de personalização, prazo de criação, entrega digital e nenhuma promessa de download imediato
   - **Quando** a modalidade for `digital_ready`
   - **Então** o produto deve ser marcado como não personalizado, com entrega digital imediata, descrição dos arquivos/compatibilidade e condições de uso; não pode exigir briefing ou aprovação.

7. **Bloquear publicação de personagem/ativo protegido sem direitos verificados**
   - **Dado** um produto associado a personagem ou outro ativo protegido
   - **Quando** a administradora salvar um rascunho
   - **Então** deve poder registrar estado de verificação (`pending`, `verified` ou `rejected`), observações, referência de evidência e, quando verificado, autora e data/hora
   - **E** observações e referência de evidência devem permanecer administrativas e nunca aparecer no contrato público
   - **Quando** tentar publicar sem todas as associações protegidas em `verified`
   - **Então** o sistema deve rejeitar a publicação com erro acionável e preservar o rascunho
   - **E** `rejected` deve impedir publicação até que uma nova verificação válida seja registrada.

8. **Editar e retirar de publicação sem apagar identidade**
   - **Dado** um produto existente
   - **Quando** a administradora atualizar dados permitidos ou retirar o produto de publicação
   - **Então** o mesmo ID deve ser preservado e o `slug` não deve mudar silenciosamente
   - **E** a retirada deve remover o produto de consultas públicas futuras sem excluir o histórico comercial
   - **E** exclusão física de produto publicado não pertence a esta story.

9. **Proteger todas as escritas administrativas**
   - **Dado** uma requisição sem identidade administrativa válida, com identidade expirada ou sem permissão de catálogo
   - **Quando** tentar criar, editar, publicar ou retirar um produto
   - **Então** a API deve negar com `401` ou `403`, não persistir alterações e não revelar existência ou dados administrativos além do necessário
   - **E** é proibido implementar autenticação por header confiado do navegador, token fixo em `.env`, query string ou bypass condicionado a produção
   - **E** enquanto a estratégia BFF↔Laravel ainda não estiver configurada, o contrato deve falhar fechado; testes podem substituir o resolvedor de identidade por fake explícito no container.

10. **Manter autoridade e camadas no Laravel**
    - **Dado** qualquer comando de cadastro ou publicação
    - **Quando** a requisição atravessar a API
    - **Então** o controller deve delegar a um caso de uso em `Application`
    - **E** validação e invariantes comerciais devem residir em `Domain`/`Application`
    - **E** Eloquent ou Query Builder, migrations e detalhes PostgreSQL devem permanecer em `Infrastructure`
    - **E** entidades de domínio não podem depender de HTTP, Eloquent, storage ou autenticação do framework.

11. **Garantir contrato, concorrência e erros previsíveis**
    - **Dado** payload inválido, `slug` duplicado ou atualização concorrente
    - **Quando** a API processar a escrita
    - **Então** deve responder com contrato JSON estável e códigos coerentes (`422` para validação, `409` para conflito de unicidade/versão)
    - **E** constraints PostgreSQL devem garantir unicidade e integridade referencial, não apenas verificações em memória
    - **E** uma atualização deve usar versão explícita ou comparação equivalente para impedir sobrescrita silenciosa.

12. **Cobrir o comportamento com testes executáveis**
    - **Dado** a implementação concluída
    - **Quando** a suíte backend for executada contra PostgreSQL
    - **Então** testes unitários devem cobrir normalização, invariantes por modalidade e direitos de uso
    - **E** testes Feature/HTTP devem cobrir sucesso, `401`, `403`, `422`, `409`, rascunho incompleto, publicação válida, bloqueio de licença, imagem principal, retirada e isolamento de campos administrativos
    - **E** migrations devem subir e descer de forma limpa no banco de teste
    - **E** `composer test` e `vendor/bin/pint --test` devem passar sem regressão dos endpoints existentes.

## Tasks / Subtasks

- [x] 1. Modelar o domínio mínimo de catálogo (AC: 1–8, 10–11)
  - [x] Criar `apps/api/app/Modules/Catalog/Domain/` com IDs opacos, enums/objetos de valor para modalidade, status, disponibilidade, entrega, dinheiro e verificação de direitos.
  - [x] Definir o agregado `Product` e seus comandos de criação, edição, publicação e retirada; rascunho aceita incompletude controlada, publicação executa validação completa.
  - [x] Definir taxonomia normalizada e associações de produto sem transformar personagens em navegação pública.
  - [x] Definir `CatalogProductRepository` e portas necessárias sem dependência de Eloquent/HTTP/storage.

- [x] 2. Criar schema PostgreSQL e repository em Infrastructure (AC: 1–8, 11)
  - [x] Criar migrations apenas para entidades exigidas nesta story: categorias, produtos, termos/taxonomia, associações, imagens e verificação de direitos (a modelagem pode consolidar tabelas quando mantiver constraints e consultas futuras claras).
  - [x] Usar UUID/ULID opaco; timestamps UTC; preço em inteiro + moeda ISO; FKs, índices e uniques explícitos.
  - [x] Garantir `slug` único, taxonomia canônica única por tipo e no máximo uma imagem principal por produto por constraint PostgreSQL quando praticável.
  - [x] Persistir atributos estruturados somente quando a semântica for estável; não transformar todo o produto em um blob JSONB sem constraints.
  - [x] Implementar repository com Query Builder/Eloquent exclusivamente em `Infrastructure` e transações nas escritas compostas.

- [x] 3. Implementar casos de uso e publicação (AC: 1–8, 10–11)
  - [x] Criar casos de uso de criar rascunho, atualizar, publicar e retirar de publicação.
  - [x] Centralizar validação condicional por modalidade no domínio e retornar erros tipados (`code`, `message_key`, `field_path`, `recoverable`, `next_action?`).
  - [x] Implementar normalização Unicode/case/whitespace para chaves de taxonomia e termos alternativos, preservando texto editorial.
  - [x] Impedir publicação de ativo protegido sem verificação completa e impedir mutação retroativa silenciosa da evidência verificada.
  - [x] Aplicar controle de concorrência otimista por `version`/ETag ou mecanismo equivalente documentado.

- [x] 4. Expor a API administrativa versionada e fail-closed (AC: 1, 5–11)
  - [x] Criar Form Requests, Resources e controllers finos em `Interfaces/Http`.
  - [x] Adicionar rotas sob `/api/v1/admin/catalog/products` em `apps/api/routes/api.php` para criar, atualizar, publicar e retirar de publicação.
  - [x] Proteger o grupo por middleware/policy de identidade administrativa; separar autenticação (`401`) de autorização de catálogo (`403`).
  - [x] Criar uma porta/resolvedor substituível se a estratégia definitiva de auth ainda estiver ausente, com adapter de produção fail-closed e fake apenas em testes; não adicionar credencial improvisada.
  - [x] Registrar bindings em `apps/api/app/Providers/AppServiceProvider.php` ou provider dedicado ao módulo.
  - [x] Não criar rota pública de escrita, server action com regra comercial nem chamada browser→Laravel direta.

- [x] 5. Registrar imagens por referência segura (AC: 4–6)
  - [x] Definir contrato para referências opacas produzidas por mecanismo de arquivos aprovado; validar formato e pertença/estado sem aceitar path/URL arbitrária.
  - [x] Persistir `storage_reference`, `alt_text`, `sort_order` e `is_primary`.
  - [x] Deixar upload binário, processamento e CDN atrás de porta/adaptador e fora do contrato de domínio desta story.

- [x] 6. Criar testes de domínio, persistência e HTTP (AC: 1–12)
  - [x] Adicionar testes unitários em `apps/api/tests/Unit/Modules/Catalog/` para modalidade, publicação, taxonomia, dinheiro e direitos.
  - [x] Adicionar testes Feature em `apps/api/tests/Feature/Catalog/` usando PostgreSQL real e `RefreshDatabase`/isolamento equivalente.
  - [x] Provar constraints e corrida: slug/taxonomia duplicados, imagem principal única e versão de atualização conflitante.
  - [x] Provar que campos administrativos de licença/evidência nunca entram em payload público ou Resource reutilizável por Story 2.2.
  - [x] Rodar `composer test`, `vendor/bin/pint --test` e smoke de rotas existentes.

- [x] 7. Documentar contrato e handoff para as próximas stories (AC: 2–4, 8, 11)
  - [x] Atualizar `packages/contracts/` com OpenAPI/JSON Schema ou documentação equivalente do contrato administrativo, sem duplicar regra de domínio.
  - [x] Documentar quais campos são seguros para projeção pública e quais são estritamente administrativos.
  - [x] Registrar no README do módulo como Stories 2.2/2.3 devem consumir queries de leitura sem acessar Models Eloquent fora de `Infrastructure`.

### Review Findings

- [x] [Review][Decision] SCA encontrou vulnerabilidades altas fora do escopo direto da story — resolvido em 2026-08-17 com atualizacao de `league/commonmark` para 2.10.0 e `npm audit fix` para `nanoid`; `composer audit` e `npm audit --audit-level=moderate` limpos.
- [x] [Review][Patch] Rotas de produto aceitam `{product}` nao-UUID antes de consultar PostgreSQL [apps/api/routes/api.php:24]
- [x] [Review][Patch] `protected_assets.*.status=verified` sem `evidence_reference` passa pela validacao HTTP e pode cair em check constraint/500 [apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogRules.php:46]
- [x] [Review][Patch] Check constraints alem de `23505` nao sao convertidas para contrato JSON tipado [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresCatalogProductRepository.php:174]
- [x] [Review][Patch] Duas imagens primarias no payload viram conflito generico de banco em vez de `422` por campo [apps/api/database/migrations/2026_08_13_000001_create_catalog_tables.php:93]
- [x] [Review][Patch] Publicacao aceita campos textuais obrigatorios contendo apenas espacos [apps/api/app/Modules/Catalog/Domain/Product.php:62]
- [x] [Review][Patch] Publicacao valida referencia de arquivo apenas da imagem primaria; imagens secundarias rejeitadas podem seguir para a projecao [apps/api/app/Modules/Catalog/Domain/Product.php:68]
- [x] [Review][Patch] `PublishCatalogProduct` valida publicacao antes de checar versao esperada, podendo retornar `422` em requisicao stale que deveria ser `409` [apps/api/app/Modules/Catalog/Application/PublishCatalogProduct.php:18]
- [x] [Review][Patch] `unpublish` permite mover rascunho para `unpublished` e `publish` permite republicar produto ja publicado, alterando versao/timestamp sem transicao real [apps/api/app/Modules/Catalog/Domain/Product.php:118]
- [x] [Review][Patch] Contrato OpenAPI apresenta `slug` como campo comum de PATCH, mas a aplicacao rejeita mudanca de slug [packages/contracts/catalog-admin-v1.openapi.yaml:91]
- [x] [Review][Patch] `Money` e `Product::draft` coercem `price_minor` para inteiro, permitindo valores malformados em callers nao-HTTP [apps/api/app/Modules/Catalog/Domain/Product.php:16]

## Dev Notes

### Decisões de modelagem obrigatórias

- O agregado é `Product`; categoria, taxonomia, imagens e verificação de direitos são partes/associações controladas pelo módulo `Catalog`.
- Modalidade é conceito de domínio, não string livre. Use backed enum PHP ou objeto de valor puro.
- Status mínimo recomendado: `draft`, `published`, `unpublished`. Não introduzir workflow editorial complexo sem requisito.
- Dinheiro: `price_minor` inteiro não negativo + `currency` ISO 4217; EUR é a moeda-base. Conversão e regras multi-moeda ficam para stories comerciais posteriores.
- `slug` é estável e único. Se alteração manual for permitida, trate como operação explícita com conflito detectável; não regenere em toda edição de nome.
- Taxonomia deve diferenciar `theme`, `occasion`, `character` e `search_alias`. Sinônimos/aliases alimentam a Story 2.3; nesta story apenas são mantidos e normalizados.
- Um tema de convite publicado é inspiração/descoberta, não lista fechada dos temas aceitos.
- Personagem/ativo protegido pode existir em rascunho, mas publicação exige direitos `verified`. Não exibir evidência, notas, autora ou timestamps em projeções públicas.
- Não use JSONB como substituto indiscriminado de tabelas/constraints. JSONB é aceitável para composição ou metadados realmente variáveis, com shape validado na aplicação.
- Referências de imagem são opacas. Não persistir binário no PostgreSQL nem aceitar URLs internas fornecidas pelo cliente.

### Fronteira de autenticação

O repositório ainda não possui `User`, tabela de usuários, Sanctum ou estratégia BFF↔Laravel final. A arquitetura deixa essa escolha explicitamente deferida. Portanto:

- não selecionar silenciosamente Sanctum, JWT, Basic Auth ou token fixo dentro desta story;
- implementar a autorização do catálogo por uma fronteira substituível e fail-closed;
- permitir fake explícito apenas em testes;
- tratar desenvolvimento e operação como estados diferentes: a story pode ser implementada e testada com a fronteira fail-closed, mas não pode ser considerada operacional em ambiente real até existir uma estratégia de autenticação administrativa aprovada que produza uma identidade e permissão `catalog.manage` (ou nome equivalente documentado);
- RBAC completo/MFA e gestão de papéis continuam na Story 8.5, mas nenhuma escrita pode ficar pública até lá.

### Fronteira de arquivos

Storage privado definitivo também está deferido pela arquitetura, então esta story não deve criar upload binário completo nem escolher provedor. Para que o cadastro seja implementável sem abrir uma brecha:

- definir uma porta de validação de arquivo do catálogo que aceite apenas identificadores opacos já emitidos por mecanismo aprovado ou fixture explícita de teste;
- em produção, o adapter deve falhar fechado enquanto não houver mecanismo de arquivos aprovado;
- testes podem substituir essa porta por fake explícito que comprove referência válida, referência inexistente, referência rejeitada e tentativa de URL/path/base64;
- a publicação deve exigir imagem principal validada pela porta, não apenas payload bem formado.

### Contrato HTTP recomendado

- `POST /api/v1/admin/catalog/products` — cria rascunho.
- `PATCH /api/v1/admin/catalog/products/{product}` — atualiza usando `version`/ETag.
- `POST /api/v1/admin/catalog/products/{product}/publish` — publica após validação completa.
- `POST /api/v1/admin/catalog/products/{product}/unpublish` — retira de publicação.
- Use Laravel Resources para separar shape administrativo de qualquer projeção pública futura.
- Respostas de validação devem usar pt-BR via `message_key`/mapeamento existente ou estabelecido no módulo, preservando UTF-8.

### Arquivos UPDATE — estado atual e preservação

- `apps/api/routes/api.php`: hoje registra health e Promotions dentro de `v1`. Adicionar grupo administrativo sem alterar nomes, paths ou comportamento das rotas existentes.
- `apps/api/app/Providers/AppServiceProvider.php`: hoje vincula repository e e-mail de Promotions. Acrescentar bindings de Catalog sem remover ou substituir os atuais; provider dedicado é aceitável se registrado corretamente.
- `packages/contracts/README.md`: hoje reserva o diretório para OpenAPI/JSON Schema e proíbe regra de negócio. Manter essa fronteira.
- `apps/api/app/Modules/README.md`: já define as quatro camadas e proíbe domínio antecipado. Atualizar apenas se necessário para registrar o módulo real, preservando as regras arquiteturais.
- `README.md`: hoje declara que catálogo real ainda não existe. Ao concluir a implementação, remover/ajustar somente essa limitação e preservar setup, portas e comandos.

### Arquivos NEW esperados

- `apps/api/app/Modules/Catalog/Domain/**`
- `apps/api/app/Modules/Catalog/Application/**`
- `apps/api/app/Modules/Catalog/Infrastructure/Persistence/**`
- `apps/api/app/Modules/Catalog/Interfaces/Http/{Controllers,Requests,Resources}/**`
- `apps/api/database/migrations/*_create_catalog_*.php`
- `apps/api/tests/Unit/Modules/Catalog/**`
- `apps/api/tests/Feature/Catalog/**`
- `packages/contracts/catalog-admin-v1.*` ou localização equivalente coerente com o diretório.

Não há arquivo frontend obrigatório nesta story. Se uma UI administrativa for criada, ela será scope creep em relação ao planejamento atual.

### Testing Requirements

- Testes de domínio sem framework para regras de modalidade, publicação, normalização e direitos.
- Testes HTTP com identity resolver fake explícito para autorizado, não autenticado e proibido.
- Testes PostgreSQL para FKs, uniques, transações e concorrência; não usar SQLite como prova final de tipos/constraints PostgreSQL.
- Testar que falha de publicação é atômica e não muda status/dados previamente persistidos.
- Testar UTF-8/acentos em nome, descrição, tema, ocasião e aliases.
- Testar limites e tamanho de arrays/strings para impedir payloads abusivos; os limites devem ser configuráveis ou documentados.
- Preservar todos os testes de Promotions, health e foundation.

### Inteligência do código e Git

- A Story 1.5 estabeleceu o padrão de módulo vertical Laravel, repository por interface, bindings no provider, controllers finos, Form Requests, migrations PostgreSQL e testes Feature/Unit. Reutilizar o padrão estrutural, não copiar a complexidade específica de Promotions.
- O commit `c58318c` adicionou o módulo `Promotions` e modificou somente pontos de composição compartilhados; Catalog deve repetir essa disciplina de mudança localizada.
- O worktree estava limpo durante a criação desta story.
- Não existe story anterior no Epic 2; a continuidade vem da fundação concluída do Epic 1 e do código vigente.

### Informação técnica atual

- O lockfile atual controla Laravel `v13.23.0`, PHPUnit `12.5.33`, Pint `1.30.3` e Predis `v3.0.0`; o runtime local é PHP `8.5.8`. Use os lockfiles como fonte de verdade e não atualize dependências nesta story sem necessidade demonstrada.
- Laravel 13 suporta UUID/ULID, `jsonb`, FKs e índices via migrations. Use constraints PostgreSQL para integridade e mantenha migrations reversíveis.
- Laravel 13 oferece Form Requests/validation, Resources e HTTP tests; regras condicionais de domínio ainda devem ser repetidas/garantidas no domínio, não apenas na borda HTTP.
- PostgreSQL 18 oferece `jsonb`, `tsvector` e índices GIN. A Story 2.1 deve preparar dados normalizados; o desenho do índice/ranking de busca pertence à Story 2.3.

Fontes oficiais consultadas em 2026-08-13:

- https://laravel.com/docs/13.x/migrations
- https://laravel.com/docs/13.x/validation
- https://laravel.com/docs/13.x/testing
- https://www.postgresql.org/docs/18/datatype.html
- https://www.postgresql.org/docs/18/textsearch-indexes.html

### Project Structure Notes

- Backend: `apps/api/app/Modules/Catalog/{Domain,Application,Infrastructure,Interfaces/Http}`.
- API: REST JSON versionada sob `/api/v1`; browser não fala diretamente com Laravel.
- Persistência: PostgreSQL é a fonte transacional; Redis não guarda estado durável do catálogo.
- Eloquent/Query Builder: somente `Infrastructure`.
- Nenhuma regra de catálogo em controller, middleware, Resource, BFF ou componente React.
- Nomes de classes em inglês técnico consistente com o código; mensagens e documentação em português do Brasil.

### References

- [Source: `_bmad-output/planning-artifacts/epics.md` — Epic 2 e Story 2.1, linhas 560–592]
- [Source: `_bmad-output/planning-artifacts/epics.md` — FR-5, AR-4, AR-6 e AR-7, linhas 23–210]
- [Source: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md` — FR-5, linhas 283–296]
- [Source: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md` — FR-46 e FR-48, linhas 788–824]
- [Source: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md` — NFR-5, NFR-7, NFR-10 e guardrails legais, linhas 888–1005]
- [Source: `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md` — AD-1 a AD-5, AD-9, AD-10, Stack e Estrutura Inicial]
- [Source: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md` — product-card, taxonomia de busca e regras por modalidade]
- [Source: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md` — product-card, badges, imagem e diferenciação visual]
- [Source: `_bmad-output/planning-artifacts/implementation-readiness-report-2026-07-31.md` — documentos canônicos, dependências e timing de criação do catálogo]
- [Source: `apps/api/app/Modules/README.md`, `apps/api/routes/api.php`, `apps/api/app/Providers/AppServiceProvider.php` — padrões implementados]
- [Source: `apps/api/composer.json`, `apps/api/composer.lock`, `README.md` — stack e versões reais]
- [Source: `_bmad-output/implementation-artifacts/1-5-exibir-captura-de-e-mail-com-cupom-de-primeira-compra.md` e commit `c58318c` — padrões e aprendizados recentes]

## Change Log

- 2026-08-13: Implementada a fatia vertical Catalog (domínio, PostgreSQL, casos de uso, API administrativa fail-closed, imagens opacas, testes e contrato OpenAPI); status movido para `review`.
- 2026-08-13: Revisão adversarial pós-implementação executada; 14 achados tratados e nenhum crítico/alto permaneceu aberto.
- 2026-08-17: Code review e security gate executados; 1 decisão SCA e 10 patches corrigidos, audits limpos e status movido para `done`.

## Dev Agent Record

### Agent Model Used

Codex GPT-5

### Debug Log References

- 2026-08-13: Tarefa 1 implementada em TDD; teste vermelho por classes ausentes, seguido por 6 testes/19 asserções verdes do domínio.
- 2026-08-13: Tarefas 2–5 validadas contra PostgreSQL 18; regressão completa com 39 testes e 240 asserções.
- 2026-08-13: Tarefas 6–7 concluídas; migrations sobem/descem, 40 testes/241 asserções, Pint e smoke das 7 rotas passaram.
- 2026-08-13: Revisão adversarial pós-implementação corrigiu isolamento de camadas, invariantes em edição publicada, imutabilidade de evidência, estados de arquivo, constraints, contratos HTTP/OpenAPI e UTF-8.
- 2026-08-13: Gate terminal aprovado: migrations `up/down/up`, 46 testes/256 asserções, Pint e `git diff --check` sem erros.
- 2026-08-17: Code review + security gate corrigiram UUID de rota, validação cruzada de imagens/direitos, erros de integridade, transições de publicação, validação de whitespace/price, contrato OpenAPI e SCA (`league/commonmark`, `nanoid`).
- 2026-08-17: Validação final: `composer test` 54 testes/277 asserções, `vendor/bin/pint --test`, `composer audit`, `npm audit --audit-level=moderate`, `npm run lint`, `npm run typecheck`, `npm run build` e `git diff --check` passaram.

### Completion Notes List

- Domínio Catalog criado com agregado Product, enums fechados, Money, taxonomia normalizada, erros tipados e porta de repository sem dependências de framework.
- Persistência estruturada, casos de uso, concorrência otimista, API administrativa fail-closed e validação de referências opacas implementadas sem novas dependências.
- Contrato OpenAPI e handoff de leitura documentados; projeção pública usa allowlist e exclui integralmente metadados administrativos de direitos.
- Revisão adversarial obrigatória registrada com 14 achados resolvidos; nenhum achado crítico/alto ficou pendente.
- Definition of Done satisfeita: todas as tarefas marcadas, File List reconciliada com os 57 caminhos alterados e story/sprint em `done`.
- Code review e security gate resolvidos; todos os findings foram marcados, os audits SCA ficaram limpos e a story foi movida para `done`.

- Ultimate context engine analysis completed - comprehensive developer guide created.
- História criada a partir dos artefatos canônicos; versões antigas de arquitetura e épicos foram excluídas da base de decisão.
- A fronteira de autenticação foi mantida fail-closed para não inventar credencial insegura nem antecipar o RBAC completo do Epic 8.
- Revisão adversarial executada; ajustes aplicados para explicitar pré-condições operacionais de autenticação administrativa e validação de referências de arquivo.

### File List

- `_bmad-output/implementation-artifacts/2-1-cadastrar-produtos-com-estrutura-de-catalogo-comercial.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `_bmad-output/implementation-artifacts/reviews/review-2-1-cadastrar-produtos-com-estrutura-de-catalogo-comercial-adversarial.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-1-implementation-adversarial-2026-08-13.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-1-security-2026-08-13.md`
- `_bmad-output/implementation-artifacts/deferred-work.md`
- `apps/api/app/Modules/Catalog/Application/CreateCatalogProduct.php`
- `apps/api/app/Modules/Catalog/Application/FileReferenceValidator.php`
- `apps/api/app/Modules/Catalog/Application/FileReferenceStatus.php`
- `apps/api/app/Modules/Catalog/Application/IdGenerator.php`
- `apps/api/app/Modules/Catalog/Application/PublishCatalogProduct.php`
- `apps/api/app/Modules/Catalog/Application/Security/AdminIdentity.php`
- `apps/api/app/Modules/Catalog/Application/Security/AdminIdentityResolver.php`
- `apps/api/app/Modules/Catalog/Application/UnpublishCatalogProduct.php`
- `apps/api/app/Modules/Catalog/Application/UpdateCatalogProduct.php`
- `apps/api/app/Modules/Catalog/Domain/Availability.php`
- `apps/api/app/Modules/Catalog/Domain/CatalogConflict.php`
- `apps/api/app/Modules/Catalog/Domain/CatalogProductRepository.php`
- `apps/api/app/Modules/Catalog/Domain/CatalogValidationFailed.php`
- `apps/api/app/Modules/Catalog/Domain/DeliveryType.php`
- `apps/api/app/Modules/Catalog/Domain/Money.php`
- `apps/api/app/Modules/Catalog/Domain/Product.php`
- `apps/api/app/Modules/Catalog/Domain/ProductModality.php`
- `apps/api/app/Modules/Catalog/Domain/PublicationStatus.php`
- `apps/api/app/Modules/Catalog/Domain/RightsVerificationStatus.php`
- `apps/api/app/Modules/Catalog/Domain/TaxonomyTerm.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Files/FailClosedFileReferenceValidator.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Identifiers/LaravelUuidGenerator.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresCatalogProductRepository.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Security/FailClosedAdminIdentityResolver.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Controllers/CreateCatalogProductController.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Controllers/PublishCatalogProductController.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Controllers/UnpublishCatalogProductController.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Controllers/UpdateCatalogProductController.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Middleware/CatalogAdminAuthorization.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogRequest.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogRules.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/ProductVersionRequest.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/StoreCatalogProductRequest.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/UpdateCatalogProductRequest.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/AdminCatalogProductResource.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogProductResource.php`
- `apps/api/app/Modules/Catalog/README.md`
- `apps/api/app/Providers/AppServiceProvider.php`
- `apps/api/bootstrap/app.php`
- `apps/api/composer.lock`
- `apps/api/routes/api.php`
- `apps/api/database/migrations/2026_08_13_000001_create_catalog_tables.php`
- `apps/api/tests/Feature/Catalog/CatalogAdminApiTest.php`
- `apps/api/tests/Feature/Catalog/CatalogPersistenceTest.php`
- `apps/api/tests/Unit/Modules/Catalog/CatalogDomainTest.php`
- `apps/api/tests/Unit/Modules/Catalog/CatalogApplicationTest.php`
- `apps/web/package-lock.json`
- `apps/api/app/Modules/README.md`
- `packages/contracts/catalog-admin-v1.openapi.yaml`
- `packages/contracts/README.md`
- `README.md`
