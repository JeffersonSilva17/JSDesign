# Revisão de Segurança — Story 3.3

## Metadados

- Artefato auditado: `_bmad-output/implementation-artifacts/3-3-coletar-personalizacao-essencial-no-adicionar-ao-carrinho.md`
- Escopo: spec de configuração/inclusão de produto, sessão convidada, idempotência, PII e governança dos gates.
- Data: 2026-10-27
- Auditor: Vex - Security Auditor
- Resultado do gate: aprovado para `ready-for-dev`; requisitos documentais, decisão arquitetural, contrato OpenAPI e gates foram tratados.
- Alto risco aberto: não.
- Médio risco aberto com owner: não.
- Responsável por aceite de risco: n/a; a pendência restante é evidência operacional obrigatória.

## Evidências Coletadas

- Arquivos lidos: story 3.3; `AGENTS.md`; `_bmad-output/project-context.md`; Epic 3/Story 3.3/Story 4.1; Story 3.1; Story 3.2; revisão de segurança pós-correções da Story 3.2; architecture spine Laravel/BFF; PRD; UX Experience e Design; configurador de pricing e página de produto existentes; inventário dos módulos Laravel e contratos.
- Comandos executados em 2026-10-27: `node scripts/scan-sast.mjs`; `node scripts/scan-secrets.mjs`; `npm audit --audit-level=moderate`; `composer audit --locked --no-interaction`.
- Semgrep/SAST passou com 0 achados em 305 arquivos; Gitleaks/segredos passou sem vazamentos; `npm audit` encontrou 0 vulnerabilidades; `composer audit` não encontrou advisories. Lint/testes de implementação não foram executados porque a story ainda não tem código implementado.
- Referências consultadas: artefatos e código local listados acima; nenhuma consulta externa foi necessária.

## Threat Model STRIDE

| Categoria | Superfície | Risco | Mitigação especificada | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Sessão convidada e inclusão | Um cliente forja identidade/carrinho de outra pessoa | Sessão opaca no BFF, cookie protegido, autenticação server-to-server e owner derivado no Laravel | Validar na implementação |
| Tampering | Configuração, modelo, preço, retry | Cliente altera campos, envia preço próprio ou repete mutação | Schema fechado, validação Laravel, recálculo e idempotência; chave opaca vinculada à sessão e ao hash do payload | Validar na implementação e no contrato OpenAPI |
| Repudiation | Criação de configuração | Dificuldade de rastrear falha sem registrar PII | Timestamp UTC e correlation ID técnico | Evidência depende da implementação |
| Information Disclosure | Erros, logs, URL, analytics e item | PII de evento/criança vaza para observabilidade ou outro cliente | Proibição explícita de PII em URL/logs/analytics; erros sanitizados; retenção de 30 dias | Validar eliminação e autorização na implementação |
| Denial of Service | Endpoint público e texto livre | Corpo grande, abuso de retry ou flood | Limites de corpo/campos, rate limit, timeout e idempotência | Valores concretos devem constar no contrato/versionamento |
| Elevation of Privilege | Acesso a item/carrinho | IDOR permite ler/alterar item de outro cliente | Autorização Laravel em cada leitura/escrita, owner derivado e testes negativos de IDOR | Validar na implementação do módulo Cart |

## Achados

### Alto Risco

Nenhum achado confirmado na especificação. A ausência de implementação/gates não constitui aprovação técnica.

### Médio Risco

#### SEC-3.3-01 — Identidade de carrinho convidado sem decisão arquitetural

- Item / Componente Afetado: inclusão server-side e futura autorização de item/carrinho.
- Risco tratado na arquitetura: antes da decisão, um endpoint mutável poderia aceitar IDOR, perder associação de configuração ou recorrer a identificadores reutilizáveis expostos ao cliente.
- Impacto para o Projeto: configuração e PII de evento podem ser acessadas ou alteradas por outra pessoa, ou itens podem ser associados a carrinho incorreto.
- Solução aplicada na especificação e em AD-10: sessão convidada opaca no BFF em cookie `HttpOnly`, `Secure`, `SameSite=Lax`; credencial server-to-server BFF→Laravel; CSRF same-origin validado no BFF; owner/carrinho derivados no Laravel; nenhum identificador de cliente/carrinho no corpo ou URL; idempotência vinculada à sessão e testes negativos de IDOR.
- Evidência: decisão AD-10 atualizada em 2026-10-14; inventário ainda não contém módulo Cart, que deverá nascer já sob esse contrato.
- Status: Tratado; validar na implementação.

#### SEC-3.3-02 — PII do pré-formulário sem política de retenção vinculada

- Item / Componente Afetado: nome, data, horário, local e observações da configuração; persistência até carrinho/pedido.
- Risco original tratado: a especificação agora fixa retenção, eliminação e controle de acesso dos dados enquanto o carrinho está abandonado.
- Impacto para o Projeto: dados pessoais, possivelmente relacionados a crianças/eventos, podem permanecer acessíveis além do necessário ou sobreviver à limpeza do carrinho.
- Solução aplicada na especificação: carrinhos e pré-formulários abandonados seguem o prazo de 30 dias do NFR-7; somente os campos necessários são persistidos sob autorização server-side, sem PII em URL, logs ou analytics, com eliminação verificável e teste de remoção.
- Evidência: PRD NFR-7/NFR-8 fixa 30 dias para carrinhos/pré-formulários abandonados; a retenção de até 90 dias da Story 4.1 é para o carrinho por conta e não substitui a regra de abandono definida nesta story.
- Status: Tratado documentalmente; validar na implementação.

### Baixo Risco

#### SEC-3.3-03 — Gates automatizados indisponíveis neste ambiente

- Item / Componente Afetado: gates versionados Semgrep e Gitleaks.
- Risco Detectado: scans não executaram porque o Docker Engine não estava disponível.
- Impacto para o Projeto: não há evidência nova de SAST ou varredura de segredos para esta revisão.
- Solução Recomendada: owner do ambiente de desenvolvimento; disponibilizar Docker e repetir os gates sobre a implementação antes de concluir a story. Aceitação: registrar saída sanitizada de ambos os scans.
- Evidência: ambos comandos retornaram `failed to connect to the docker API ... dockerDesktopLinuxEngine`.
- Status: Corrigido; gate automatizado executado com sucesso.

## Decisão do Gate

- Decisão: requisitos de segurança dos dois achados médios, o mecanismo concreto de sessão/CSRF, o contrato OpenAPI e os gates automatizados foram incorporados; story aprovada para `ready-for-dev`.
- Condições antes de avançar: manter os controles aprovados durante a implementação e executar testes de IDOR/CSRF/eliminação no código da story.
- Owner: Sharom/Privacidade para retenção; responsável pelo ambiente para Docker/gates; implementação do módulo Cart para aplicar AD-10.
- Próximo checkpoint: revisão de segurança da implementação antes de `done`.

## Evidência dos gates SCA

- `npm audit --audit-level=moderate` em `apps/web`: passou, `found 0 vulnerabilities`, após atualizar Next.js para 16.4.3 e Zod transitivo para 4.6.7.
- `composer audit --locked --no-interaction` em `apps/api`: passou, `No security vulnerability advisories found.`
- SAST e segredo: não executados com sucesso; requerem Docker Desktop Linux Engine.
