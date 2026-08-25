# Revisão Adversarial de Segurança — Story 2.2

## Metadados

- Artefato auditado: `_bmad-output/implementation-artifacts/2-2-exibir-listagens-publicas-por-categoria-ocasiao-e-tipo.md`
- Escopo: contrato de leitura pública do catálogo, filtros/paginação, BFF, projeção de cards, entrega de imagens, detalhe mínimo e testes
- Data: 2026-08-18
- Auditor: Vex - Security Auditor
- Resultado do gate: Aprovado com ressalvas

## Evidências Coletadas

- Arquivos lidos: story 2.2; `project-context.md`; Epic 2; PRD; UX `EXPERIENCE.md` e `DESIGN.md`; architecture spine; story 2.1; módulo Catalog, rotas, provider, Resources, migrations, BFF e configuração/testes web relevantes.
- Comandos executados: `composer audit --locked` (nenhum advisory); `npm audit --audit-level=moderate` (0 vulnerabilidades); busca por `dangerouslySetInnerHTML`, SQL raw e `storage_reference`; inspeção de ferramentas de segurança disponíveis.
- Ferramentas não executadas: `gitleaks`, `detect-secrets`, `trufflehog` e `semgrep` não estão instalados neste ambiente. A story mantém SAST/secret scan como evidência obrigatória de implementação/CI.
- Referências consultadas: OWASP ASVS/Top 10 e STRIDE conforme a skill; documentação oficial vigente de Laravel 13, Next.js 16, PostgreSQL 18 e Playwright.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente/especificada | Gap residual |
| --- | --- | --- | --- | --- |
| Spoofing | GETs públicos | uso de entrada pública para alcançar capacidade admin | endpoints read-only separados, allowlist e preservação do middleware admin | nenhum alto risco |
| Tampering | query, slug, href e imagem | duplicação/coerção, path traversal ou URL hostil | inspeção bruta, limites, parametrização, slug fechado, path relativo same-origin validado em Laravel+BFF | implementar e provar por testes |
| Repudiation | tráfego anônimo | burst sem telemetria ou log de conteúdo hostil | throttle/métricas sem query bruta, payload ou storage reference | limites operacionais ainda serão configurados |
| Information Disclosure | Resource, erro, HTML, cache e logs | vazamento de admin/direitos/storage/SQL/stack/upstream | projeção allowlist, erros negativos, `no-store`, React textual, lista explícita de campos proibidos | implementar e provar por contrato |
| Denial of Service | listagem, facets, count, hidratação | N+1, paginação abusiva, burst | máximo 48, tamanhos fechados, throttle, timeout, índices e orçamento constante de queries | valores finais dependem do ambiente |
| Elevation of Privilege | BFF → Laravel → banco/arquivo | query pública reutiliza command repository/admin adapter | porta read-only dedicada e resolver fail-closed; controllers sem acesso a tabelas | nenhum alto risco |

## Achados

### Alto Risco

Nenhum achado de alto risco permanece aberto no artefato revisado.

### Médio Risco

#### SEC-2.2-01 — Controle de abuso depende de configuração operacional

- Item / Componente Afetado: Acceptance Criteria 12; Tasks 3 e 6; Threat Model STRIDE
- Risco Detectado: os três GETs anônimos executam paginação/count/facetas e podem sofrer burst; um throttle ingênuo apenas por IP no Laravel também pode agrupar todo tráfego do BFF.
- Impacto para o Projeto: exaustão de workers/conexões ou bloqueio coletivo de clientes legítimos.
- Solução Recomendada: throttle nomeado e configurável com identidade operacional compatível com proxy confiável, limites de página/payload, timeout/statement limit, `429` sanitizado, métricas e teste de burst. Documentar valores e topologia antes de produção.
- Evidência: o draft revisado incorporou requisitos verificáveis, mas os valores pertencem à implementação/deployment.
- Status: Aceito pelo Responsável — owner Sharom / Dev; condição obrigatória da implementação.

#### SEC-2.2-02 — Entrega real de imagens ainda não possui adapter aprovado

- Item / Componente Afetado: Acceptance Criteria 6; Fronteira de imagens; Tasks 1, 2 e 4
- Risco Detectado: transformar referência opaca em URL sem uma fronteira fechada permitiria disclosure, host bypass, redirect ou path traversal.
- Impacto para o Projeto: vazamento de storage interno, conteúdo remoto não confiável ou requisições indevidas.
- Solução Recomendada: manter default fail-closed; aceitar somente path relativo same-origin estruturalmente validado no adapter e BFF; proibir URL absoluta, `//`, credenciais, fragmento, traversal, controle, barra invertida e redirect. Ativar fotos reais somente após adapter aprovado.
- Evidência: o contrato foi corrigido para remover a alternativa ambígua de allowlist externa e exige testes negativos.
- Status: Aceito pelo Responsável — owner Sharom / Dev; fallback `null` é a postura segura até aprovação.

### Baixo Risco

#### SEC-2.2-03 — Evidência local incompleta de SAST e secret scan

- Item / Componente Afetado: Definition of Done / Tasks 7 e 8
- Risco Detectado: o ambiente possui SCA funcional, mas não disponibiliza os scanners SAST/segredos consultados.
- Impacto para o Projeto: falhas que não aparecem em SCA podem ficar sem evidência automatizada local.
- Solução Recomendada: executar ferramentas já aprovadas no CI ou registrar evidência equivalente; nunca declarar o controle executado quando a ferramenta estiver ausente.
- Evidência: `composer audit` e `npm audit` passaram; comandos de descoberta não localizaram `gitleaks`, `detect-secrets`, `trufflehog` ou `semgrep`.
- Status: Aceito pelo Responsável — owner Sharom / DevOps; evidência exigida antes de encerrar implementação.

## Decisão do Gate

- Decisão: Aprovado com ressalvas
- Condições antes de avançar: nenhuma pendência de alto risco; desenvolver exatamente os controles descritos, manter imagens fail-closed e não liberar produção sem configuração/testes de throttle e evidência de scanners disponíveis.
- Owner: Sharom / Dev; Sharom / DevOps para evidência CI
- Prazo / próximo checkpoint: durante `bmad-dev-story`, antes de marcar a implementação como concluída

