# Revisão de Segurança — Story 3.2 (implementação)

Data: 2026-10-06  
Artefato auditado: implementação da cotação em Laravel, PostgreSQL, Next.js BFF e componente de produto.  
Escopo: entradas HTTP, validação/serialização, consulta e versão de preço, logs, dependências, SAST, segredos e controles públicos.  
Auditor: Vex — Security Auditor  
Resultado do gate: **Aprovado com ressalva baixa de configuração de ambiente**.

## Evidências coletadas

- Arquivos revisados: migration e gatilhos de `Pricing`, caso de uso, calculadora, adaptador PostgreSQL, controller, rotas, provider, BFF, rota same-origin, componente de cotação, contrato OpenAPI, testes e `AGENTS.md`.
- `composer test`: passou, 162 testes, 810 assertions; 1 benchmark ignorado.
- `vendor/bin/pint --test`: passou.
- `npm run lint`, `npm run typecheck`, `npm run test:bff` (23 testes), `npm run test:seo` (10 testes) e `npm run build`: passaram.
- Playwright `pricing-quote.spec.ts`: 3 testes passaram, incluindo integração real BFF/API, falha recuperável, viewport de 320 px, estado acessível e resposta fora de ordem.
- `npm audit --audit-level=moderate` e `npm audit --omit=dev --audit-level=moderate`: 0 vulnerabilidades cada.
- `composer audit --locked --no-interaction`: nenhum advisory.
- `node scripts/scan-sast.mjs`: Semgrep, 304 arquivos, 247 regras executadas, 0 achados.
- `node scripts/scan-secrets.mjs`: Gitleaks, aproximadamente 82,82 MB examinados, nenhum segredo encontrado.
- `git diff --check`: passou; Git só informou normalização LF/CRLF em arquivos já alterados.
- Ferramentas não executadas: nenhuma exigida pelo gate versionado.
- Referências externas: nenhuma necessária; revisão baseada nos contratos e ferramentas presentes no repositório.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | POST público Laravel/BFF | Requisições anônimas tentarem acessar produto não público | Identidade do cliente não é aceita; consulta filtra publicação e aplicação rejeita indisponibilidade; rate limit por IP | Nenhum gap de implementação confirmado |
| Tampering | JSON, preço e resposta BFF | Campo extra ou cotação adulterada | Schemas fechados, valores comerciais calculados no servidor, resposta validada estritamente e relações monetárias verificadas | Nenhum gap de implementação confirmado |
| Repudiation | Logs da cotação | Log revelar payload ou PII | Somente correlation ID, resultado e código sanitizado são registrados | Nenhum gap confirmado |
| Information Disclosure | Respostas públicas | Exposição de dados de catálogo administrativo ou erro interno | Projeção allowlist, mensagens localizadas no BFF, erros uniformes e `no-store` | Nenhum gap confirmado |
| Denial of Service | Corpo HTTP, PostgreSQL e Laravel | Payload grande ou rajada de chamadas | Leitura limitada a 4 KiB no BFF, resposta upstream limitada a 16 KiB, quantidade até 10000, timeout 3 s e 60 req/min/IP | Limites de proxy/deployment não foram verificados neste workspace |
| Elevation of Privilege | Rota de cotação e banco | Reuso de rota administrativa ou mutação de carrinho | Rota pública somente leitura separada; role runtime tem privilégios mínimos verificados por teste existente | Nenhum gap de implementação confirmado |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Médio Risco

#### SEC-3.2-11 — O BFF materializava corpo sem limite antes da validação

- Item / Componente Afetado: `apps/web/src/app/api/pricing/quotes/route.ts`.
- Risco Detectado: `request.text()` lia o corpo inteiro antes de medir os 4 KiB, permitindo consumo de memória desnecessário em requisições grandes.
- Impacto para o Projeto: chamadas públicas grandes poderiam ampliar consumo de memória dos workers Next.js.
- Solução Recomendada: ler o stream em partes, interromper ao exceder 4 KiB, rejeitar UTF-8 inválido e limitar também o JSON retornado pelo serviço interno.
- Evidência: `readLimitedJsonBody` é usado no request BFF e no response Laravel; testes cobrem limite, cancelamento, UTF-8 inválido e resposta acima de 16 KiB.
- Status: Corrigido.

#### SEC-3.2-12 — A UI aceitava objetos de resposta e erro com validação superficial

- Item / Componente Afetado: `apps/web/src/features/pricing/PricingConfigurator.tsx`.
- Risco Detectado: os guards locais aceitavam objetos incompletos e não relacionavam todas as quantias retornadas à solicitação atual.
- Impacto para o Projeto: resposta malformada poderia ser renderizada como preço ou erro público.
- Solução Recomendada: reutilizar os guards estritos do BFF no componente e conferir produto, modelo e quantidade esperados.
- Evidência: componente usa `isQuote` e `isPublicError`; os guards validam allowlist, limites, UUID público, faixa e consistência aritmética.
- Status: Corrigido.

### Baixo Risco

#### SEC-3.2-13 — Configuração de proxy e controles do cliente não são verificáveis aqui

- Item / Componente Afetado: configuração de deployment/runtime fora dos arquivos deste workspace.
- Risco Detectado: este review não prova que o proxy de produção preserva corretamente o IP do cliente nem que os controles locais do cliente/IDE correspondem à governança descrita em `AGENTS.md`.
- Impacto para o Projeto: configuração incorreta pode reduzir a efetividade operacional do rate limit ou dos controles do agente, sem alterar os controles implementados pela aplicação.
- Solução Recomendada: confirmar esses parâmetros no deployment e no cliente utilizado antes da operação em produção.
- Evidência: `AGENTS.md` descreve a política e ressalva explicitamente que ela não comprova as configurações do cliente/IDE; esses parâmetros não existem no escopo versionado auditado.
- Status: Aberto; risco residual baixo, não bloqueia a revisão da story.

## Decisão do Gate

- Decisão: **Aprovado com ressalva baixa de ambiente**; nenhum risco alto ou médio permanece aberto.
- Condições antes de avançar: nenhuma pendência de implementação; o owner de deployment confirma proxy/runtime antes de produção.
- Owner: responsável pela aplicação/deployment.
- Próximo checkpoint: revisão da configuração do ambiente de produção.
