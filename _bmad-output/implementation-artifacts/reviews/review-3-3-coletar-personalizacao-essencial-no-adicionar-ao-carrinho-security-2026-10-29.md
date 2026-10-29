# Revisão de Segurança — Story 3.3

## Metadados

- Artefato auditado: implementação local da Story 3.3, com correções posteriores de sessão, CSRF, idempotência e carrinho.
- Escopo: rotas HTTP Next.js/Laravel, sessão convidada, persistência PostgreSQL, validação, dependências, segredos e gates versionados.
- Data: 2026-10-29
- Auditor: Vex — Security Auditor
- Resultado do gate: Bloqueado

## Evidências Coletadas

- Arquivos lidos: story, AD-10, contrato OpenAPI, rotas BFF/Laravel, middleware, `GuestCartSession`, `AddConfiguredCartItem`, migração, configuração de rate limit, `.gitignore` e scripts de gate.
- Comandos executados: `npm audit --audit-level=moderate`, `npm run lint`, `npm run typecheck`, `composer audit --locked --no-interaction`, `vendor/bin/pint --test`, `php -l`, `phpunit --filter CartConfigurationTest`, `node --test tests/unit/cart-validation.test.mjs`, `git diff --check`, busca estática de padrões de segredo e os scripts versionados de SAST/segredos.
- Resultados: npm audit sem vulnerabilidades; lint e typecheck passaram; validação BFF passou (2/2); PHPUnit de domínio passou (2/2, 6 asserções); sintaxe PHP e `git diff --check` passaram. `composer audit` encontrou um advisory médio em `symfony/mime` v8.1.7. A busca estática encontrou somente nomes de variáveis e placeholders de configuração, sem segredo exposto.
- Ferramentas não executadas: Semgrep e Gitleaks versionados não iniciaram porque Docker Desktop não expõe `//./pipe/dockerDesktopLinuxEngine`. A suíte de integração de carrinho não iniciou porque PostgreSQL recusou conexão em `127.0.0.1:5432` para `jsdesign_test`.
- Referências consultadas: `AGENTS.md`, story 3.3, AD-10 e os scripts versionados de SAST/segredos.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Cookie `__Host-jsd_cart_session`, BFF e Laravel | Uso de sessão alheia | Referência opaca HttpOnly, bearer interno e owner hash server-side | Nenhum bypass confirmado |
| Tampering | Payload, cotação e retry | Alterar campos ou repetir payload distinto | Schema fechado, cálculo no Laravel e hash canônico | Nenhum bypass confirmado |
| Repudiation | Inclusões e erros | Correlacionar sem registrar PII | IDs de correlação e respostas sanitizadas | Evidência de logs de produção fora do escopo |
| Information Disclosure | Configuração com dados de evento | Leitura cruzada ou cache | Isolamento por sessão, `no-store` e erros allowlistados | Nenhum acesso cruzado confirmado |
| Denial of Service | GET público do carrinho | Referências forjadas acionam consulta antes do throttle | Limite de leitura definido | Limite é avaliado depois da consulta de autorização e usa chave controlada pelo cliente |
| Elevation of Privilege | BFF → Laravel | Acesso direto à API interna | Token de serviço obrigatório | Nenhum bypass confirmado |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Médio Risco

#### SEC-3.3-20261029-M01 — Dependência PHP com advisory de segurança aberto

- Item / Componente Afetado: `apps/api/composer.lock`, `symfony/mime` v8.1.7.
- Risco Detectado: `composer audit --locked --no-interaction` reportou `CVE-2026-45070` / `PKSA-wtxr-p26d-nn42`, injeção de cabeçalho de e-mail através de nomes de parâmetros MIME.
- Impacto para o Projeto: qualquer fluxo de e-mail que aceite dados não confiáveis em parâmetros MIME pode gerar cabeçalhos manipulados.
- Solução Recomendada: atualizar a dependência para versão não afetada, regenerar o lockfile e executar `composer audit` novamente; revisar os chamadores de e-mail que recebem entrada externa.
- Evidência: audit de 2026-10-29 identifica v8.1.7 como afetada e indica correção a partir de v8.1.10.
- Status: Aberto.

#### SEC-3.3-20261029-M02 — Rate limit de leitura não protege a consulta de sessão

- Item / Componente Afetado: `apps/api/routes/api.php:41-42`, `apps/api/app/Modules/Cart/Interfaces/Http/Middleware/CartGuestSessionAuthorization.php:18-20`, `apps/api/app/Providers/AppServiceProvider.php:67-68`.
- Risco Detectado: `CartGuestSessionAuthorization` consulta `guest_cart_sessions` antes de `throttle:cart-read`; o limiter usa `X-Guest-Session`, valor que o navegador pode variar livremente em cookies forjados.
- Impacto para o Projeto: um atacante pode gerar muitas consultas de sessão com referências sintaticamente válidas e distintas, evitando a chave do limiter antes da rejeição de autorização.
- Solução Recomendada: aplicar limite no BFF antes da chamada interna, por IP confiável e referência, ou aplicar middleware de limite por IP antes de `CartGuestSessionAuthorization`; retornar 429 no envelope público previsto.
- Evidência: a ordem de middleware declarada nas rotas e o método `authorize()` demonstram a consulta anterior ao throttle.
- Status: Aberto.

### Baixo Risco

#### SEC-3.3-20261029-L01 — Evidência de SAST, segredos e integração indisponível neste ambiente

- Item / Componente Afetado: gates `scripts/scan-sast.mjs`, `scripts/scan-secrets.mjs` e `CartItemRequestTest`.
- Risco Detectado: não há execução atual dos scans versionados nem dos testes de integração após as mudanças de sessão.
- Impacto para o Projeto: regressões de segurança ou exposição de segredo podem permanecer sem detecção automática.
- Solução Recomendada: iniciar Docker Desktop e PostgreSQL de teste, executar os dois scans e a suíte de carrinho, e anexar saídas sanitizadas ao relatório.
- Evidência: Docker API e PostgreSQL recusaram conexão durante esta revisão.
- Status: Aberto.

## Decisão do Gate

- Decisão: Bloqueado.
- Condições antes de avançar: corrigir ou aceitar explicitamente M01 e M02 com owner e prazo; executar SAST, segredos e testes de integração quando a infraestrutura estiver disponível.
- Owner: responsável pela Story 3.3 e manutenção de dependências.
- Prazo / próximo checkpoint: após atualização de `symfony/mime`, correção do limiter e reexecução dos gates.

## Remediação — 2026-10-29

- `SEC-3.3-20261029-M01` corrigido: `symfony/mime` foi atualizado de v8.1.7 para v8.1.10, juntamente com os polyfills compatíveis; `composer audit --locked --no-interaction` passou sem advisories.
- `SEC-3.3-20261029-M02` corrigido: as rotas de leitura e escrita aplicam limite por IP antes de `CartGuestSessionAuthorization` e preservam limite por referência depois da autorização. O BFF reencaminha `X-Forwarded-For` somente quando `TRUSTED_EDGE_FORWARDING_HEADERS=true`.
- O fixture HTTP foi isolado de cabeçalhos e contadores compartilhados; `CartItemRequestTest` passou com 6 testes e 50 asserções.
- SAST versionado passou: Semgrep executou 247 regras em 328 arquivos, com 0 achados bloqueantes.
- `SEC-3.3-20261029-L01` corrigido: Gitleaks versionado executou sobre a árvore de trabalho sanitizada e concluiu com código 0; a suíte de integração voltou a executar com PostgreSQL local ativo.

## Decisão Final do Gate

- Decisão: Aprovado.
- Evidência final: SAST 0 achados; scan de segredos com código 0; `composer audit` e `npm audit` sem advisories/vulnerabilidades; lint, typecheck, Pint, testes unitários e `CartItemRequestTest` (6/6, 50 asserções) passaram.
- Risco residual: o reencaminhamento de IP depende de `TRUSTED_EDGE_FORWARDING_HEADERS=true` somente atrás de uma borda que sanitize `X-Forwarded-For`; quando desabilitado, o limite pré-autorização usa o IP do BFF e continua protegido de referências forjadas.
