# Revisao Adversarial de Seguranca - Pos Code Review

## Metadados

- Artefato auditado: Story 2.4, diff de implementacao e patches aplicados durante code review.
- Escopo: SEO/catalogo publico, sitemap/robots, BFF Next, API Laravel, CI/security gates e evidencia local.
- Data: 2026-09-21.
- Retentativa de gates pendentes: 2026-09-22.
- Auditor: Vex - Security Auditor.
- Resultado do gate: Aprovado.

## Evidencias Coletadas

- Arquivos lidos: `AGENTS.md`, story 2.4, workflows `backend.yml`, `frontend.yml`, `smoke.yml`, `security.yml`, scripts `scan-sast.mjs`, `scan-secrets.mjs`, `stage-security-source.mjs`, BFF/API de catalogo e sitemap, testes E2E/unitarios e configs de ambiente.
- Comandos executados:
  - `npm run test:bff` em `apps/web`: passou, 12 testes.
  - `npm run test:seo` em `apps/web`: passou, 10 testes.
  - `npm run typecheck` em `apps/web`: passou.
  - `npm run lint` em `apps/web`: passou.
  - `npm run build` com `SEO_INDEXING_ENABLED=false`: passou.
  - `npm run build` com `SEO_INDEXING_ENABLED=true`: passou.
  - `npm audit --audit-level=moderate`: 0 vulnerabilidades.
  - `composer audit --locked --no-interaction`: sem advisories.
  - `vendor/bin/pint --test`: passou.
  - `git diff --check`: passou, somente avisos CRLF do Git no Windows.
  - `php artisan test --filter PublicCatalogSitemapApiTest`: nao validou por PostgreSQL local indisponivel em `127.0.0.1:5432`.
  - `node scripts/scan-sast.mjs`: nao executou por Docker Desktop/daemon Linux indisponivel.
  - `node scripts/scan-secrets.mjs`: nao executou por Docker Desktop/daemon Linux indisponivel.
  - `docker version` em 2026-09-22: cliente disponivel, daemon Linux indisponivel em `npipe:////./pipe/dockerDesktopLinuxEngine`.
  - `node scripts/scan-sast.mjs` em 2026-09-22: falhou antes do scan pelo mesmo erro de Docker daemon indisponivel.
  - `node scripts/scan-secrets.mjs` em 2026-09-22: falhou antes do scan pelo mesmo erro de Docker daemon indisponivel.
  - `Test-NetConnection 127.0.0.1 -Port 5432` em 2026-09-22: `TcpTestSucceeded: False`.
  - `php artisan test --filter PublicCatalogSitemapApiTest` em 2026-09-22: passou, 7 testes e 83 assertions.
  - `node scripts/scan-sast.mjs` em 2026-09-22 apos iniciar Docker Desktop: passou, 247 regras em 283 arquivos, 0 achados.
  - `node scripts/scan-secrets.mjs` em 2026-09-22 apos iniciar Docker Desktop: passou, 82.60 MB escaneados, sem leaks.
  - `rg` local para padroes obvios de segredo: sem matches.
- Ferramentas nao executadas: nenhuma pendencia conhecida apos retentativa de 2026-09-22.
- Referencias consultadas: instrucoes locais `AGENTS.md`, skill `bmad-review-security`, story 2.4 e evidencia versionada no repositorio.

## Threat Model STRIDE

| Categoria | Superficie | Risco | Mitigacao existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Sitemap BFF -> Laravel | Cliente falsifica identidade de crawl | `scripts/server.mjs` sobrescreve `X-Catalog-Client`; HMAC com chave compartilhada; API valida assinatura antes da cota | Depende do runtime `npm run start`; documentado no README |
| Tampering | Query, slug, payload upstream, JSON-LD/XML | Payload divergente ou texto hostil vira SEO indexavel | Parsers estritos, validacao de identidade, escape JSON-LD `<`, escape XML, canonical fixo por `SITE_URL` | Nenhum gap alto confirmado |
| Repudiation | Scanners e evidencia local | Concluir sem gates versionados executados | Workflows versionam SAST/SCA/secrets; evidencias locais registradas; Semgrep/Gitleaks executados em 2026-09-22 | Nenhum gap aberto |
| Information Disclosure | HTML/XML/metadata/logs | Vazamento de storage privado, PII, query de busca ou erro interno | Projecoes allowlist, no-store, erros sanitizados, sem `q` em metadata social/canonical; Gitleaks sem leaks | Nenhum gap aberto |
| Denial of Service | Sitemap e upstream | Payload grande, timeout ou lotes caros | Limites de bytes antes de `JSON.parse`, timeout, lotes 500, teto 5M, rate limit separado, benchmark versionado; suite HTTP de sitemap passou | Nenhum gap aberto |
| Elevation of Privilege | Banco e CI | Runtime com papel migrator/bootstrap | Provisao separa `jsdesign_runtime` e `jsdesign_migrator`; E2E migra com migrator e serve API com runtime; teste de privilegios existe | Nenhum gap alto/medio aberto |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Medio Risco

#### M01

- Item / Componente Afetado: `scripts/scan-sast.mjs`, `scripts/scan-secrets.mjs`, `.github/workflows/security.yml`.
- Risco Detectado: Os scanners versionados de SAST e segredos nao executaram inicialmente neste ambiente local porque dependiam do Docker daemon.
- Impacto para o Projeto: O code review nao podia declarar aprovacao plena de seguranca para o diff pos-patch enquanto SAST e Gitleaks estivessem pendentes.
- Solucao Recomendada: Concluida em 2026-09-22 apos iniciar Docker Desktop e reexecutar `node scripts/scan-sast.mjs` e `node scripts/scan-secrets.mjs`.
- Evidencia: Semgrep passou com 247 regras em 283 arquivos e 0 achados; Gitleaks passou com 82.60 MB escaneados e sem leaks.
- Status: Resolvido.

### Baixo Risco

#### L01

- Item / Componente Afetado: ambiente local de testes PostgreSQL.
- Risco Detectado: A suite HTTP `PublicCatalogSitemapApiTest` nao executou inicialmente porque o PostgreSQL em `127.0.0.1:5432` recusou conexao.
- Impacto para o Projeto: A regressao adicionada para slugs invalidos e headers 422 ficou temporariamente sem comprovacao local de banco.
- Solucao Recomendada: Concluida em 2026-09-22 apos Docker/PostgreSQL local ficarem saudaveis.
- Evidencia: `php artisan test --filter PublicCatalogSitemapApiTest` passou com 7 testes e 83 assertions.
- Status: Resolvido.

## Decisao do Gate

- Decisao: Aprovado.
- Condicoes antes de avancar: Nenhuma condicao de seguranca pendente conhecida neste escopo.
- Owner: Dev/DevOps.
- Prazo / proximo checkpoint: Antes de marcar a story como `done` ou fazer merge/deploy.
