# Revisão de Segurança — Story 3.2, pós-correções

## Metadados

- Artefato auditado: correções de revisão da Story 3.2 e lockfile do frontend.
- Escopo: configurador de quantidade, i18n, regras ESLint locais, Link interno e dependências transitivas do Next.js.
- Data: 2026-10-06
- Auditor: Vex - Security Auditor
- Resultado do gate: Aprovado.

## Evidências Coletadas

- Arquivos lidos: `PricingConfigurator.tsx`, `pricingConfiguratorContent.ts`, `eslint.config.mjs`, `package-lock.json`, story, `AGENTS.md`, `apps/web/AGENTS.md`, `.gitignore` e `.gitleaks.toml`.
- Comandos executados: `npm run lint`, `npm run typecheck`, `npm audit --audit-level=moderate`, `composer audit --locked --no-interaction`, `git diff --check`, `node scripts/scan-sast.mjs` e `node scripts/scan-secrets.mjs`.
- Ferramentas não executadas: nenhuma exigida pelo gate versionado.
- Referências consultadas: documentação local do Next.js 16.3.8 para ESLint; sem fonte externa necessária.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | `POST /api/pricing/quotes` via configurador | Cliente forjar identidade ou cotação | Entrada mínima, BFF same-origin e resposta validada | Nenhum novo gap confirmado |
| Tampering | Quantidade, limite e preços | Alterar limites ou preço no navegador | Limite retornado pelo Laravel; UI não calcula preço; guardas estritos | Nenhum novo gap confirmado |
| Repudiation | Logs de cotação | Correlação sem dados pessoais | Correlation ID e resultado sanitizado existentes | Fora do escopo desta correção |
| Information Disclosure | UI e conteúdo i18n | Expor valores administrativos ou dados pessoais | Conteúdo estático; contrato público allowlist | Nenhum novo gap confirmado |
| Denial of Service | Entrada de quantidade e dependências | Quantidade acima do teto e bibliotecas vulneráveis | Máximo preservado na UI; `sharp` 0.35.5 e `source-map-js` 1.2.2 | Nenhum novo gap confirmado |
| Elevation of Privilege | Link para fluxo futuro | Navegação interna conceder privilégio | `next/link` não altera autorização; backend continua sendo a fronteira | Nenhum novo gap confirmado |

## Achados

### Alto Risco

Nenhum achado confirmado.

### Médio Risco

Nenhum achado confirmado.

### Baixo Risco

Nenhum achado confirmado.

## Decisão do Gate

- Decisão: Aprovado.
- Condições antes de avançar: nenhuma para o gate de segurança desta correção.
- Owner: responsável pela aplicação.
- Prazo / próximo checkpoint: próxima alteração no fluxo de cotação.

## Evidência Final dos Gates

- SAST: Semgrep executou 247 regras sobre 305 arquivos, com 0 achados.
- Segredos: Gitleaks examinou aproximadamente 82,84 MB, sem vazamentos.
- SCA: `npm audit --audit-level=moderate` e `composer audit --locked --no-interaction` passaram sem advisories.
- Qualidade estática: lint, typecheck e `git diff --check` passaram.
