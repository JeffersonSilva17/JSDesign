# Revisão de Segurança — Story 3.3 (follow-up)

## Metadados

- Artefato auditado: implementação local da Story 3.3, grupo principal do carrinho
- Escopo: sessões, CSRF, validação Laravel/BFF, persistência, idempotência, privacidade, retenção, erros e gates de segurança
- Data: 2026-10-28
- Auditor: Vex — Security Auditor
- Resultado do gate: Bloqueado, aguardando owner e tratamento dos riscos médios

## Evidências Coletadas

- Arquivos lidos: rotas BFF e Laravel do carrinho, transporte e validação BFF, casos de uso, domínio, migração, config, contrato OpenAPI, story e project-context.
- Comandos executados: `node scripts/scan-sast.mjs`; `node scripts/scan-secrets.mjs`; Gitleaks sobre histórico; `npm audit --audit-level=moderate`; `composer audit --locked --no-interaction`; `git diff --check main`.
- Resultados: Semgrep 247 regras em 324 arquivos, 0 achados; Gitleaks em arquivos atuais (82,99 MB) e 22 commits, sem vazamentos; npm 0 vulnerabilidades; Composer sem advisories; `git diff --check` sem erros.
- Ferramentas não executadas: testes automatizados, pois esta solicitação é de revisão e não pediu execução de testes.
- Referências consultadas: arquivos versionados do repositório e a política local `AGENTS.md`.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Cookie de sessão, BFF e Laravel | Forjar sessão ou chamar a API interna | Cookie opaco `HttpOnly`/`Secure`; bearer BFF; Laravel deriva `owner_hash` | Nenhum bypass confirmado |
| Tampering | Payload de configuração e cotação | Campos inválidos ou cotação adulterada | BFF valida schema; Laravel recalcula cotação e rejeita campos extras | Laravel aceita `null` em textos obrigatórios quando a API interna recebe o payload |
| Repudiation | Inclusões e erros | Dificuldade de correlacionar falhas sem registrar PII | UUID de correlação nas respostas; timestamp no item; sem logging do conteúdo observado | Sem achado confirmado neste escopo |
| Information Disclosure | Leitura do carrinho e respostas | Expor configuração de outra sessão ou PII em erros | Isolamento por hash de sessão; respostas `no-store`; erros allowlistados; JSX escapa texto | Nenhum acesso cruzado confirmado |
| Denial of Service | Leitura pública do carrinho | Flood de GETs gera consultas internas sem limite | Sessão e resposta têm formato validado; escrita possui rate limit | Rota GET não aplica rate limit por IP/sessão |
| Elevation of Privilege | BFF → Laravel | Bypass do BFF ou acesso a carrinho alheio | Middleware exige token interno; owner não vem do corpo/URL | Nenhum bypass confirmado |

## Achados

### Alto Risco

#### SEC-3.3-FU-H01 — Sessão convidada e idempotência não são mantidas server-side como exige AD-10

- Item / Componente Afetado: `apps/web/src/app/api/cart/session/route.ts:7-14`; `apps/web/src/app/api/cart/items/route.ts:29-45`; `apps/api/app/Modules/Cart/Interfaces/Http/Middleware/CartServiceAuthorization.php:16-21`
- Risco Detectado: o BFF coloca o segredo aleatório da sessão diretamente no cookie e o reencaminha como `X-Guest-Session`; o Laravel usa o hash desse valor como owner. Não existe registro server-side que associe uma referência de sessão à identidade interna, CSRF, chave de idempotência ou hash canônico do payload.
- Impacto para o Projeto: a implementação diverge da fronteira de autorização aprovada e não cumpre o vínculo server-side exigido para retries. O cookie passa a funcionar como credencial material de leitura/escrita, contrariando AD-10 e o guardrail da story.
- Solução Recomendada: decidir a persistência server-side para sessão convidada e idempotência antes de corrigir: cookie deve conter somente referência opaca; o BFF resolve a sessão, valida CSRF e emite/guarda a associação da chave ao hash canônico; Laravel recebe apenas contexto interno autenticado.
- Evidência: a rota de sessão cria `randomBytes(32)` e usa esse mesmo valor como cookie; a rota de itens lê e repassa o cookie; o middleware Laravel calcula `hash('sha256', $session)` diretamente.
- Status: Aberto; requer decisão humana por envolver a arquitetura AD-10.

### Médio Risco

#### SEC-3.3-FU-M01 — Validação Laravel aceita `null` em campos textuais obrigatórios

- Item / Componente Afetado: `apps/api/app/Modules/Cart/Domain/CartConfiguration.php:24-35`
- Risco Detectado: `isset` não valida valores `null`; a API Laravel pode aceitar e persistir configuração incompleta para `theme`, `personalization_text`, `colors`, `event_name` ou `event_location`.
- Impacto para o Projeto: a API pode armazenar dados que não satisfazem os critérios de personalização; a resposta BFF rejeita a configuração resultante e a inclusão pode parecer indisponível após persistir.
- Solução Recomendada: validar presença, tipo, whitespace e tamanho com `array_key_exists`/validação explícita para cada campo obrigatório antes de persistir.
- Evidência: o laço de limites só executa a checagem quando `isset($value[$field])` é verdadeiro; `null` contorna o bloco.
- Status: Aberto; registrado também como patch no arquivo da story.

#### SEC-3.3-FU-M02 — GET público do carrinho não tem rate limit

- Item / Componente Afetado: `apps/web/src/app/api/cart/items/route.ts:10-19`; `apps/api/routes/api.php:37-39`
- Risco Detectado: cada GET com cookie de sessão sintaticamente válido chama a API Laravel; a rota Laravel GET não usa throttle e executa leitura do carrinho e dos itens.
- Impacto para o Projeto: um cliente externo pode gerar consultas internas sem limite nessa rota pública e aumentar a carga no banco/BFF.
- Solução Recomendada: limitar GET no endpoint público com uma chave server-derived por IP e sessão, mantendo resposta `429` sanitizada e `Retry-After`.
- Evidência: o handler BFF chama `readCart` sem rate limit; o grupo de rotas Laravel aplica autorização e `no-store`, enquanto somente POST usa `throttle:cart-add-item`.
- Status: Aberto; registrado também como patch no arquivo da story; owner ainda não atribuído.

### Baixo Risco

Nenhum achado confirmado.

## Follow-up de Remediação — 2026-10-28

- `SEC-3.3-FU-H01` corrigido: `guest_cart_sessions` mantém no servidor a referência opaca, owner hash, hash CSRF, hash da chave de idempotência e hash canônico do payload. O BFF só persiste a referência `HttpOnly` e encaminha o contexto autenticado ao Laravel.
- `SEC-3.3-FU-M01` corrigido: campos textuais obrigatórios são validados para presença, tipo, tamanho e conteúdo não branco no Laravel.
- `SEC-3.3-FU-M02` corrigido: leitura de itens usa `throttle:cart-read`, com chave derivada da referência de sessão.
- Complementos: idempotência independente de ordem de propriedades; expiração não revive carrinho; tamanho de resposta e número de itens são limitados; contrato OpenAPI e retorno 413 foram alinhados.

### Evidências após a correção

- `php -l` passou para todos os arquivos Cart alterados, rota, provider e migração.
- `npm run typecheck` passou; `node --test tests/unit/cart-validation.test.mjs` passou (2/2).
- `node scripts/scan-sast.mjs` e `node scripts/scan-secrets.mjs` não concluíram porque o Docker Desktop não está em execução (`npipe://./pipe/dockerDesktopLinuxEngine`). Os gates continuam pendentes de execução neste ambiente.
- O teste de integração `CartItemRequestTest` não iniciou porque PostgreSQL em `127.0.0.1:5432` recusou conexão para `jsdesign_test`; o teste unitário de configuração passou (2 testes, 6 asserções).

### Conclusão do follow-up

Os achados alto e médios desta revisão estão corrigidos no código. A aprovação formal do gate de segurança permanece pendente apenas da execução dos scans versionados com Docker e da suíte de integração com PostgreSQL de teste disponível.

## Decisão do Gate

- Decisão: Bloqueado
- Condições antes de avançar: decisão humana e tratamento do alto risco de sessão server-side; atribuir owner e tratar ou aceitar explicitamente os riscos médios; resolver os demais achados funcionais do code review.
- Owner: não atribuído
- Prazo / próximo checkpoint: próxima revisão desta story após decisão do usuário
