# Revisão de Segurança — Story 3.2

Data: 2026-10-06  
Artefato auditado: `_bmad-output/implementation-artifacts/3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho.md`  
Escopo: story, dependências web/API, configuração ESLint, SAST, scan de segredos e governança `AGENTS.md`. Não existe implementação Pricing/cotação para revisar.  
Auditor: Vex — Security Auditor  
Resultado do gate: **Aprovado para a especificação; revisão da implementação pendente**.

## Evidências coletadas

- Arquivos lidos: story 3.2, relatório anterior, `apps/web/package.json`, `apps/web/package-lock.json`, `apps/web/eslint.config.mjs`, `apps/api/composer.json`, scripts versionados de SAST/segredos, `.gitignore` e `AGENTS.md`.
- `npm audit --audit-level=moderate` (`apps/web`): passou, 0 vulnerabilidades.
- `npm audit --omit=dev --audit-level=moderate` (`apps/web`): passou, 0 vulnerabilidades.
- `npm run lint` (`apps/web`): passou com a nova configuração ESLint plana.
- `composer audit --locked --no-interaction` (`apps/api`): passou, sem advisories.
- `node scripts/scan-sast.mjs`: passou; 284 arquivos, 247 regras executadas, 0 achados.
- `node scripts/scan-secrets.mjs`: passou; cerca de 82,75 MB examinados, nenhum segredo encontrado.
- `git diff --check`: passou; Git emitiu somente avisos de normalização LF/CRLF.
- Docker Desktop foi iniciado sem elevação de privilégio para executar os scanners versionados.
- A cadeia de lint vulnerável foi removida do lockfile e de `node_modules`. Next permanece em `16.3.8`; ESLint agora usa `typescript-eslint`, `eslint-plugin-react` e `eslint-plugin-react-hooks`.
- Referências técnicas externas: advisory `GHSA-vfj7-8cjw-p6xm` consultado pelo `npm audit`.
- Ferramentas não executadas: testes de aplicação e revisão de código Pricing, pois a implementação não existe e esta remediação não alterou lógica da aplicação.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação existente | Gap |
|---|---|---|---|---|
| Spoofing | Cotação pública | Cliente tentar acessar dados não públicos | A story exige somente leitura de produto público e não confia em identidade enviada | Implementação ainda pendente |
| Tampering | Request Laravel/BFF | Adulteração de quantidade ou dados comerciais | Schemas estritos; campos extras e preço enviado pelo cliente rejeitados | Implementação ainda pendente |
| Repudiation | Logs | Dados pessoais ou payload sensível em logs | Correlation ID e motivos sanitizados, sem payload | Implementação ainda pendente |
| Information Disclosure | Respostas e erros | Exposição de produto privado ou configuração administrativa | Allowlist pública e erro uniforme para cotação indisponível | Implementação ainda pendente |
| Denial of Service | Endpoint público | Flood, corpo grande e quantidade extrema | Rate limit, limite de payload/quantidade, timeout e checagem de overflow especificados; SAST sem achados | Controles ainda sem evidência de runtime |
| Elevation of Privilege | Laravel/BFF | Reuso de rota administrativa ou exposição de segredo interno | Rota pública separada, BFF same-origin e segredo server-side especificados | Implementação ainda pendente |

## Achados

### Alto Risco

Nenhum achado confirmado aberto.

### Médio Risco

#### SEC-3.2-09 — Cadeia vulnerável de lint removida

- Item / Componente Afetado: `apps/web/package.json`, `apps/web/package-lock.json`, `apps/web/eslint.config.mjs`.
- Risco Detectado: a cadeia `eslint-config-next` → `@next/eslint-plugin-next` → `fast-glob` → `micromatch` incluía `braces@3.0.3`, afetado por DoS de exaustão de stack.
- Impacto para o Projeto: entrada maliciosa processada pelo lint poderia causar indisponibilidade em desenvolvimento ou CI. A dependência estava no grafo de desenvolvimento.
- Solução Recomendada: removida a cadeia vulnerável e preservadas regras TypeScript, React e hooks por configuração ESLint plana.
- Evidência: lockfile sem `eslint-config-next`, `fast-glob`, `micromatch` e `braces`; audit integral e lint passaram.
- Status: Corrigido.

### Baixo Risco

#### SEC-3.2-10 — Scanners indisponíveis no ambiente inicial

- Item / Componente Afetado: `scripts/scan-sast.mjs`, `scripts/scan-secrets.mjs`.
- Risco Detectado: a execução inicial falhou porque o daemon Docker estava parado, deixando SAST e scan de segredos sem evidência.
- Impacto para o Projeto: possíveis vulnerabilidades de código ou segredos poderiam não ser detectados.
- Solução Recomendada: Docker Desktop iniciado sem elevação e ambos os scanners versionados executados novamente.
- Evidência: Semgrep examinou 284 arquivos e reportou 0 achados; Gitleaks examinou cerca de 82,75 MB e reportou nenhum segredo.
- Status: Corrigido.

## Decisão do Gate

- Decisão: **Aprovado para a especificação e dependências auditadas**.
- Condições antes de concluir a story: implementar Pricing/BFF/contrato e revisar novamente segurança do código, incluindo controles em runtime e testes negativos. O gate atual não declara a implementação segura.
- Owner: responsável pela Story 3.2.
- Próximo checkpoint: revisão de segurança após implementação e antes de concluir a story.
