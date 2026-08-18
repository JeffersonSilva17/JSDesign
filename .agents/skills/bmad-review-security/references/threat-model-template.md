# Template de Threat Modeling STRIDE

Use este bloco em PRDs, specs, blueprints e stories que introduzam ou alterem superficies de seguranca.

## Threat Modeling - STRIDE

### Contexto de Seguranca

- Atores: `{usuarios, admins, agentes, servicos externos}`
- Ativos protegidos: `{PII, tokens, arquivos, dados financeiros, propriedade intelectual}`
- Pontos de entrada: `{APIs, CLI, webhooks, uploads, filas, jobs, browser, LLM/tool calls}`
- Pontos de saida: `{respostas HTTP, logs, eventos, arquivos, emails, APIs externas}`
- Fronteiras de confianca: `{cliente-servidor, servico-terceiro, agente-ferramenta, app-banco}`

### STRIDE

| Categoria | Pergunta obrigatoria | Mitigacao exigida |
| --- | --- | --- |
| Spoofing | Quem pode fingir ser usuario, admin, servico ou agente? | Autenticacao forte, assinatura, validacao de identidade, expiracao de token |
| Tampering | Onde payloads, arquivos ou estados podem ser alterados? | Validacao de schema, integridade, controle de versao, transacoes |
| Repudiation | Como provar quem fez a acao sem logar PII sensivel? | Auditoria com ator, acao, tempo UTC, correlacao e retencao controlada |
| Information Disclosure | Que dados podem vazar por resposta, log, erro, cache ou contrato? | Minimizacao, mascaramento, respostas genericas, segregacao admin/public |
| Denial of Service | Que input pode causar custo excessivo ou indisponibilidade? | Rate limit, limites de payload, timeout, paginacao, rejeicao precoce |
| Elevation of Privilege | Como alguem pode ganhar acesso acima do permitido? | RBAC/ABAC, checks no servidor, isolamento de tenant, testes de bypass |

### Evidencia Minima para Ready-for-Dev

- Acceptance criteria cobrem auth, autorizacao, validacao, erro sanitizado e limites de payload quando aplicaveis.
- Tasks incluem testes negativos para acesso indevido, input invalido e vazamento de dados.
- Arquivos/configs esperados estao nomeados com paths reais.
- Riscos abertos tem owner e decisao explicita antes de desenvolvimento.
