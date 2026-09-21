# Revisão Adversarial de Segurança — Story 2.4

## Revalidação após remediação — 2026-09-17

**Resultado vigente: aprovado com ressalvas para ready-for-dev.** Esta seção substitui a decisão bloqueada da análise inicial preservada abaixo. Autoriza iniciar a implementação; não aprova SEO ainda não implementado nem produção.

- H01 corrigido: Next e eslint-config-next 16.3.0 → 16.3.5, com lock atualizado.
- H02 corrigido: sharp 0.35.3 → 0.35.4 e binários/libvips correspondentes atualizados pelo gerenciador.
- M01 corrigido: Predis 3.0.0 → 3.6.0 no composer.lock, dentro da faixa ^3.0 existente.
- M02 corrigido: js-yaml 4.3.1 → 4.3.2, sem migrar para major 5.
- Não há achado alto ou médio aberto desta revisão. Nenhum aceite de vulnerabilidade foi usado.

Evidências executadas no workspace:

| Verificação | Resultado |
| --- | --- |
| npm audit --audit-level=moderate | Zero vulnerabilidades conhecidas, exit 0 |
| composer audit --locked | Nenhum advisory, exit 0 |
| npm ls next eslint-config-next sharp js-yaml --all | Versões corrigidas efetivamente instaladas |
| composer update predis/predis --with-dependencies --minimal-changes --no-interaction | Concluído, somente Predis alterado no lock PHP |
| npm run test:bff | 11 passaram |
| composer test | 128 passaram, 551 assertions; PostgreSQL/Redis reais |
| php vendor/bin/pint --test | Passou |
| npm run lint / npm run typecheck | Passaram |
| npm run build | Passou com Next 16.3.5, saída isolada .next-security |
| Playwright, suíte completa | 55 passaram em 53,9 s, build de produção na porta 3100 e 2 workers |
| git diff --check | Passou |

A suíte Playwright usou configuração temporária derivada da existente, alterando apenas porta, diretório de build e workers; removida após uso. O servidor do usuário na porta 3000 foi preservado. Arquivos gerados de configuração foram restaurados ao estado anterior; nenhuma alteração funcional da story foi implementada. O npm avisou sobre um binário SWC antigo bloqueado pelo servidor existente; a instalação nova, o build e os testes concluíram. Reiniciar o servidor de desenvolvimento existente é necessário para carregar a nova versão naquele processo.

Revisão de diff: mudanças limitadas aos manifests/locks e artefatos de acompanhamento. Mantidas fronteiras STRIDE da especificação; as correções documentais agora exigem validação de identidade, limites de corpo antes do parse, isolamento de crawl, noindex de homologação e matriz de erros. Evidência desses comportamentos será produzida na implementação.

Baixos L01/L02/L03 continuam abertos, com owners e checkpoints originais: scanners SAST/segredos dedicados indisponíveis, políticas/grants sem evidência completa e padrões adicionais de ignore. Ausência de scanner não foi registrada como aprovação. Essas ressalvas não impedem preparar a story; permanecem exigências de revisão de implementação/pré-produção conforme o relatório.

Fontes dos advisories foram reconsultadas; os audits atuais são a evidência de resolução no lock. Não se afirma ausência absoluta de vulnerabilidades nem exploração dos avisos anteriores. Próximo checkpoint: implementar a story e executar seus testes/gates próprios.

## Análise inicial — histórico anterior à remediação

## Metadados

- Artefato auditado: `_bmad-output/implementation-artifacts/2-4-preparar-paginas-publicas-de-catalogo-para-seo-e-compartilhamento.md`.
- Escopo: especificação, contratos SEO/sitemap, superfícies públicas e baseline de dependências existente. Não é aprovação da implementação; nenhum código funcional foi alterado.
- Data: 2026-09-17.
- Auditor: Vex - Security Auditor, revisão independente.
- Resultado do gate: **Bloqueado** por dependência Next existente com advisory crítico e condições relevantes ao ambiente Windows atual. A especificação nova não introduziu essa dependência.

## Evidências Coletadas

- Leitura integral: skill `bmad-review-security`, templates de relatório/gate e story 2.4. Contexto adicional: revisão 2.3, `.gitignore`, manifests, versões dos locks, `catalogTransport.ts`, `catalogValidation.ts`, rotas Laravel, `AppServiceProvider.php`, `config/database.php`, `next.config.ts`, configuração ESLint e gate da skill create-story.
- `npm audit --audit-level=moderate --json`, em `apps/web`: exit 1; três pacotes afetados, um crítico e dois altos. `npm ls next sharp js-yaml --all`: Next 16.3.0, sharp 0.35.3, js-yaml 4.3.1 via ESLint 9.39.5 → @eslint/eslintrc 3.3.6.
- `composer audit --locked --format=json`, em `apps/api`: exit 1; Predis crítico. Lock confirma `predis/predis` v3.0.0.
- Detecção de `gitleaks`, `detect-secrets`, `trufflehog` e `semgrep`: indisponíveis. Não instalados; SAST e scanner dedicado de segredos **não executados**, com risco residual registrado.
- Busca limitada em arquivos rastreados de apps/packages/.github por assinaturas de chaves privadas, token GitHub clássico e AWS access key: nenhum arquivo correspondente. Isso não equivale a secret scan completo. Não foram lidos nem impressos valores de `.env` real.
- Inspeção de políticas acessíveis: create-story exige esta revisão antes de ready-for-dev; não localizada política versionada de revisão de artefatos, autoexecução terminal ou allowlist browser. ESLint usa regras Next/TypeScript; não configura SAST de segurança dedicado.
- Pint, testes Laravel, lint, build e E2E não executados: mudança documental, sem implementação a verificar. Permanecem gates obrigatórios da futura implementação; não são declarados aprovados.
- Fontes primárias dos mantenedores verificadas em 2026-09-17: [Next/Windows](https://github.com/vercel/next.js/security/advisories/GHSA-p293-qw3h-jr36), [Next/AVIF](https://github.com/vercel/next.js/security/advisories/GHSA-2xp9-vwfh-vxw4), [sharp](https://github.com/lovell/sharp/security/advisories/GHSA-rgj7-g3m4-5g8c), [js-yaml](https://github.com/nodeca/js-yaml/security/advisories/GHSA-2883-xcg3-v3hh), [Predis](https://github.com/predis/predis/security/advisories/GHSA-w6f5-v2h6-g786).

## Threat Model STRIDE

Entradas: slug, query, headers HTTP, SITE_URL, projeção upstream e mídia pública. Saídas: HTML, OG/Twitter, JSON-LD, XML, robots e logs. Dados sensíveis a excluir: termos com PII, return_to, direitos/alias/personagem internos, storage privado e credenciais. Não há nova autenticação, upload ou pagamento.

| Categoria | Superfície | Risco | Mitigação existente/contratada | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Canonical e imagem | Host poisoning | SITE_URL validada, sem confiar em headers | Teste de implementação obrigatório |
| Tampering | Slug/query/JSON-LD/XML | XSS e alteração de consulta | Parsers estritos, escape contextual, JSON-LD escapa `<`, query parametrizada | Provar fixtures adversas |
| Repudiation | Logs/crawl | Falta de correlação ou registro de PII | Correlação técnica minimizada, sem q/return_to/payload | Verificar logs no gate de implementação |
| Information Disclosure | Metadata/sitemap | Enumeração de drafts e dados privados | Published-only, allowlists, no-store e falhas sanitizadas | Provar retirada entre requisições |
| Denial of Service | XML/API/DB | Trabalho sem limite | 500 itens/lote, 10000 lotes, teto editorial, timeout e throttle | Medir query budget; SCA atual falha |
| Elevation of Privilege | Runtime/API | Bypass de admin ou execução no host | Porta pública read-only, middleware admin preservado | Next crítico no baseline; não mitigado pelo contrato SEO |

## Achados

### Alto Risco

#### SEC-2.4-H01 — Next vulnerável no baseline

- Item / Componente Afetado: `apps/web/package.json`, package-lock e `next.config.ts`; Next 16.3.0.
- Risco Detectado: GHSA-p293-qw3h-jr36 (CVE-2026-75604), RCE em host Windows com Pages/App Router sem Cache Components. A faixa 16.0.0..<16.3.3 contém a versão instalada. Este workspace é Windows; a configuração não habilita Cache Components. O advisory não oferece workaround conhecido para hosts Windows afetados.
- Impacto para o Projeto: execução remota no servidor se um atacante alcançar o runtime nas condições do advisory. Scripts atuais vinculam a loopback, limitando exposição; isso não comprova mitigação em todos os ambientes e nenhuma exploração foi executada.
- Solução Recomendada: correção de dependências em mudança própria, com Next em versão corrigida compatível (advisory indica 16.3.3 como primeira correção dessa linha), alinhamento de eslint-config-next e lock, regressões e nova SCA. A versão efetiva deve considerar todos os advisories atuais. Alternativamente, aceite humano explícito e documentado de risco residual; inexistente nesta revisão.
- Evidência: npm audit exit 1, npm ls, configuração local e advisory primário acima. O segundo advisory crítico de Next/AVIF é registrado em H02.
- Status: **Aberto**. Owner: Dev/DevOps; responsável por eventual aceite: Sharom. Checkpoint: antes de ready-for-dev.

#### SEC-2.4-H02 — Cadeia Next/sharp de processamento AVIF

- Item / Componente Afetado: Next 16.3.0 e sharp 0.35.3 no lock web.
- Risco Detectado: GHSA-2xp9-vwfh-vxw4 (Next, crítico) e GHSA-rgj7-g3m4-5g8c (sharp, alto), relacionados ao processamento AVIF/libheif. São duas notificações da mesma cadeia, não duas explorações demonstradas.
- Impacto para o Projeto: comprometimento do processo ao otimizar entrada AVIF adversa nas condições dos advisories. A story exige PNG/JPEG e não permite imagem remota arbitrária; não foi demonstrado caminho de upload/AVIF controlável por atacante neste escopo.
- Solução Recomendada: atualizar a cadeia para versões corrigidas (Next a partir de 16.3.3 e sharp a partir de 0.35.4 para estes avisos), verificar resolução real do lock e executar SCA/regressões. Não atribuir correção à simples escolha do fallback PNG.
- Evidência: npm audit e npm ls; fontes primárias Next/AVIF e sharp. `next.config.ts` não contém mitigação explícita de otimização.
- Status: **Aberto como risco de supply chain**, explorabilidade do fluxo SEO não demonstrada. Owner Dev/DevOps; reavaliação antes de liberar gate.

### Medio Risco

#### SEC-2.4-M01 — Predis crítico em configuração condicional

- Item / Componente Afetado: `apps/api/composer.lock`, Predis v3.0.0; `config/database.php`.
- Risco Detectado: SCA classifica GHSA-w6f5-v2h6-g786 / CVE-2026-84372 como crítico, relacionado a CRLF em pipelines de conexões agregadas. A configuração versionada usa phpredis como default e não comprova uso desse caminho vulnerável; ambiente efetivo não foi lido. Classificação contextual média não altera a severidade do advisory.
- Impacto para o Projeto: potencial injeção de comandos Redis/DoS se a aplicação passar a usar Predis com o caminho afetado.
- Solução Recomendada: Dev/DevOps deve atualizar Predis para versão corrigida (3.3.0 ou posterior compatível), ou provar não aplicabilidade nas configurações implantadas; rerodar Composer audit e testes. Justificativa da classificação: dependência instalada vulnerável, sem evidência de alcance público do caminho específico.
- Evidência: composer audit exit 1 e lock v3.0.0; default phpredis em código; advisory primário.
- Status: **Aberto**, mitigação planejada por Dev/DevOps antes da liberação de produção/revisão de implementação. Não configura aceite humano.

#### SEC-2.4-M02 — js-yaml alto restrito à cadeia de desenvolvimento observada

- Item / Componente Afetado: js-yaml 4.3.1 via ESLint.
- Risco Detectado: GHSA-2883-xcg3-v3hh / CVE-2026-84375, consumo excessivo de CPU por fontes vazias de merge YAML; severidade do advisory alta, classificação contextual média.
- Impacto para o Projeto: indisponibilidade do processo que analisar YAML adverso; não identificado parser YAML de entrada pública no fluxo SEO.
- Solução Recomendada: Dev/DevOps atualizar a resolução para js-yaml 4.3.2 ou posterior compatível e executar lint/audit. Justificativa: cadeia de desenvolvimento, sem caminho HTTP identificado.
- Evidência: npm audit exit 1, npm ls identifica ESLint como consumidor; advisory primário.
- Status: **Aberto**, owner Dev/DevOps; checkpoint antes de fechar implementação. Não autoriza processamento de YAML não confiável enquanto pendente.

### Baixo Risco

#### SEC-2.4-L01 — SAST e secret scan dedicados ausentes

- Item / Componente Afetado: ferramentas locais e pipeline de segurança.
- Risco Detectado: ferramentas dedicadas indisponíveis; busca manual não cobre variantes/histórico.
- Impacto para o Projeto: padrões inseguros/segredos podem escapar da revisão.
- Solução Recomendada: Dev/DevOps anexar execução de ferramenta aprovada ou evidência equivalente antes de done; não instalar como efeito colateral desta criação.
- Evidência: Get-Command sem os quatro scanners; configuração ESLint sem plugin de segurança dedicado.
- Status: **Aberto**, owner Dev/DevOps; checkpoint revisão de implementação.

#### SEC-2.4-L02 — Políticas de agente e privilégio mínimo sem comprovação completa

- Item / Componente Afetado: governança IDE/terminal/browser e configuração efetiva de banco.
- Risco Detectado: não localizadas políticas versionadas específicas; não verificados grants do usuário efetivo de aplicação. O gate BMAD existe e foi executado.
- Impacto para o Projeto: automações futuras podem operar com limites inconsistentes; ausência de evidência não prova exploração ou privilégio excessivo em produção.
- Solução Recomendada: Sharom/DevOps registrar política aprovada e evidência sanitizada de usuário runtime com privilégio mínimo; separar usuário de migração. Não alterar IDE/sandbox nesta story.
- Evidência: buscas de policy/allowlist e config de banco baseada em env; não consultados segredos ou grants reais.
- Status: **Aberto**, owner Sharom/DevOps; checkpoint antes de produção.

#### SEC-2.4-L03 — Ignore não cobre tipos sensíveis comuns

- Item / Componente Afetado: `.gitignore`.
- Risco Detectado: `.env` coberto; padrões de chaves, certificados privados, dumps e backups não explícitos.
- Impacto para o Projeto: maior chance de commit acidental futuro.
- Solução Recomendada: Dev/DevOps acrescentar padrões com exceções legítimas documentadas, junto ao secret scan.
- Evidência: leitura do `.gitignore`; nenhuma chave privada confirmada na busca limitada.
- Status: **Aberto**, owner Dev/DevOps; checkpoint antes de produção.

## Decisão do Gate

- Decisão: **Bloqueado**. Existem achados de alto risco abertos no baseline; não houve aceite humano. A intenção segura do contrato não corrige dependências vulneráveis.
- Condições antes de avançar: resolver/reavaliar H01/H02 e rerodar os audits; documentar aplicação ou mitigação dos médios; apenas então reexecutar gate. Não marcar ready-for-dev ou done enquanto o bloqueio persistir.
- Owner: Dev/DevOps para dependências; Sharom para eventual aceite explícito, nunca presumido.
- Prazo / próximo checkpoint: antes da transição ready-for-dev. A story pode permanecer criada em draft/backlog.
- Evidência futura: XSS/escape, Host poisoning, published-only/retirada, no-store, isolamento admin, path/URL allowlist, limites/paginação, erros sanitizados e ausência de PII/segredos no HTML/XML/logs. Os ACs cobrem esses contratos; testes ainda precisam ser implementados.
- Nenhuma dependência, lockfile, código ou política global foi modificada nesta revisão.
