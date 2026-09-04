# Revisão Adversarial de Segurança — Story 2.3

## Metadados

- Artefato auditado: `_bmad-output/implementation-artifacts/2-3-implementar-busca-publica-por-intencao.md`
- Escopo: contrato e plano da busca pública, API/BFF/PostgreSQL, privacidade, disponibilidade, governança e gates de supply chain
- Data: 2026-08-26
- Auditor: Vex - Security Auditor
- Resultado do gate: Aprovado com ressalvas

## Evidências Coletadas

- Arquivos lidos: story 2.3; project-context; PRD/UX/arquitetura/épicos; Stories 2.1/2.2 e reviews; rotas, requests, Resources, queries, config, migrations, BFF, UI, testes, manifests, `.gitignore` e `next.config.ts` atuais.
- Comandos executados: inspeção por `rg`/Git; `composer audit --locked`; `npm audit --audit-level=moderate`; detecção de ferramentas de SAST/segredos; busca somente por nomes de arquivos sensíveis e assinaturas comuns em arquivos rastreados.
- Resultado SCA: Composer sem advisories; npm sem vulnerabilidades.
- Ferramentas não executadas: `gitleaks`, `detect-secrets`, `trufflehog` e `semgrep` não estão instalados/configurados; ausência não foi tratada como aprovação.
- Varredura manual limitada: nenhum header de chave privada ou token comum foi encontrado; `.env.example` e o SQL de inicialização esperado são os únicos arquivos rastreados que coincidiram com extensões/padrões sensíveis verificados.
- Referências: OWASP/API threat modeling incorporado via STRIDE; documentação oficial PostgreSQL 18, Laravel 13, Next.js 16 e Playwright citada na story.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente/planejada | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | GET público Laravel | cliente tenta alcançar capacidade admin | endpoint read-only separado, sem identidade confiada, middleware admin preservado | nenhum alto risco aberto |
| Tampering | query string e hrefs | duplicatas, arrays, encoding e wildcards alteram consulta | inspeção bruta, contrato fechado, limites, bindings e `URLSearchParams` | provar com testes negativos |
| Repudiation | abuso anônimo | correlação insuficiente ou log de termo/IP | limiter com chave não reversível, sem `q`/IP bruto, correlação técnica mínima | scanner/log policy depende de implementação |
| Information Disclosure | JSON, HTML, URL, erro e logs | direitos, personagem, alias, storage, SQL ou termo pessoal vazam | allowlists Laravel+BFF, texto React, no-store, erros sanitizados, referrer restritivo e sem analytics de `q` | validar headers e ausência em payload/log |
| Denial of Service | fuzzy/PostgreSQL | scan caro, paginação abusiva e candidate explosion | `q` 2..120, throttle, timeout, per-page/page, teto candidato, índices e query budget | valores operacionais devem ser comprovados |
| Elevation of Privilege | BFF/controller/query | acesso a repository/admin | porta pública em Application, query em Infrastructure e isolamento de rotas | provar testes de bypass |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Médio Risco

#### SEC-2.3-01

- Item / Componente Afetado: busca aproximada PostgreSQL e migration planejada
- Risco Detectado: fuzzy sem limiar/teto/índice ou rollback com `DROP EXTENSION` compartilhado poderia causar DoS ou indisponibilidade de outras capacidades.
- Impacto para o Projeto: consultas públicas custosas, timeout do banco ou remoção acidental de extensão usada por outro módulo.
- Solução Recomendada: limites configuráveis, candidate cap, statement timeout, índice aderente à expressão, teste de query budget e rollback que remove apenas objetos próprios.
- Evidência: AC 7, Tasks 2/6 e `Search Semantics and Performance` foram corrigidos para exigir esses controles.
- Status: Corrigido no contrato da story; evidência de implementação permanece obrigatória.

#### SEC-2.3-02

- Item / Componente Afetado: matching por personagem/alias e sugestões públicas
- Risco Detectado: a busca poderia virar canal lateral para associações protegidas ou expor direitos, verificadores, evidências e termos internos.
- Impacto para o Projeto: divulgação indevida de propriedade intelectual e dados administrativos.
- Solução Recomendada: personagem somente quando a associação protegida estiver `verified` e o produto `published`; nunca expor personagem/alias/direitos; sugestões somente de taxonomia editorial pública.
- Evidência: ACs 1/4, contrato, testes e STRIDE exigem filtro anterior ao match e allowlists fechadas em Laravel e BFF.
- Status: Corrigido no contrato da story; evidência de implementação permanece obrigatória.

#### SEC-2.3-03

- Item / Componente Afetado: `q` em URL, handoff e navegação externa
- Risco Detectado: termos podem conter PII e vazar por logs, analytics, referrer ou encaminhamento integral a suporte/terceiros.
- Impacto para o Projeto: tratamento desnecessário de dados pessoais e exposição em histórico/telemetria.
- Solução Recomendada: não registrar/enviar `q` bruto; orientar a não inserir PII; contexto minimizado; `Referrer-Policy: strict-origin-when-cross-origin` ou mais restritiva; teste de header/terceiros.
- Evidência: AC 7, Tasks 5, Dev Notes e STRIDE foram atualizados com controles explícitos.
- Status: Corrigido no contrato da story; evidência de implementação permanece obrigatória.

### Baixo Risco

#### SEC-2.3-L01

- Item / Componente Afetado: pipeline de segurança
- Risco Detectado: não há SAST nem secret scanner local verificável.
- Impacto para o Projeto: padrões vulneráveis ou segredos podem passar pelos checks locais.
- Solução Recomendada: Dev/DevOps deve executar ferramenta aprovada no CI ou anexar evidência equivalente antes de fechar a implementação.
- Evidência: ferramentas não encontradas; SCA passou e a varredura manual foi limitada.
- Status: Aberto como condição de implementação; owner Dev/DevOps; checkpoint antes de `done`.

#### SEC-2.3-L02

- Item / Componente Afetado: governança de IDE/sandbox/browser
- Risco Detectado: não foram encontrados Artifact Review Policy, Terminal Auto Execution Policy ou browser allowlist verificáveis no repositório.
- Impacto para o Projeto: automações futuras podem operar sem guardrails versionados consistentes.
- Solução Recomendada: Sharom/DevOps deve definir políticas no mecanismo de governança aprovado; não alterar configuração global como efeito colateral desta story.
- Evidência: busca por arquivos de policy/sandbox/allowlist e configurações de IDE não retornou política aplicável.
- Status: Aberto como recomendação de governança; owner Sharom/DevOps; não bloqueia esta story.

#### SEC-2.3-L03

- Item / Componente Afetado: `.gitignore`
- Risco Detectado: cobre `.env`, mas não explicita chaves/certificados/dumps/backups comuns (`*.pem`, `*.key`, `*.p12`, `*.pfx`, `*.dump`, `*.bak`).
- Impacto para o Projeto: maior chance de versionamento acidental de artefatos sensíveis futuros.
- Solução Recomendada: ampliar padrões com exceções documentadas para certificados públicos/fixtures legítimos e manter secret scan no CI.
- Evidência: `.gitignore` atual; nenhum segredo comum confirmado nos arquivos rastreados.
- Status: Aberto como recomendação de baixo risco; owner Dev/DevOps; checkpoint antes de produção.

## Decisão do Gate

- Decisão: Aprovado com ressalvas
- Condições antes de avançar: implementar e testar todos os controles marcados nos ACs; não aceitar evidência apenas declaratória; manter L01 como gate antes de `done` e registrar L02/L03 no backlog/governança apropriado.
- Owner: Dev para controles da story; Dev/DevOps para scanners e `.gitignore`; Sharom/DevOps para políticas de governança.
- Prazo / próximo checkpoint: revisão adversarial da implementação e gates completos antes de mover de `review` para `done`.

