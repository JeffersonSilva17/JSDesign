# Revalidação de segurança — implementação 2.4

## Metadados

- Artefato: implementação da story 2.4, diff local e arquivos novos.
- Escopo: SEO/HTML/XML, transporte BFF/API, identidade do limiter, imagem editorial, permissões PostgreSQL locais/CI e gates de segurança.
- Data: 2026-09-17.
- Auditor: Codex, seguindo `bmad-review-security` (Vex); revisão local, sem auditor independente.
- Autorização: solicitação do usuário para corrigir todos os achados e continuar. O relatório documental anterior permanece como histórico.
- Gate: **Aprovado para revisão da implementação**, no escopo local descrito. Todos os seis achados foram tratados; os limites externos estão explicitados ao final.

## Evidências coletadas

- Arquivos: `sitemapIdentity.ts`, `scripts/server.mjs`, `catalogSitemapTransport.ts`, `SitemapClientIdentity.php`, `AppServiceProvider.php`, `bootstrap/app.php`, `catalogValidation.ts`, imagem editorial, scripts de provisionamento/scan, testes, workflows, `.gitignore`, `.gitleaks.toml` e `AGENTS.md`.
- Unitários: 10 SEO e 11 BFF aprovados. PHP: 135 testes/619 assertions aprovados; benchmark opt-in separado, 1 teste/10 assertions.
- E2E: **65 aprovados em cada modo**, true/false. Ingresso assinado, filtros e cenários vazios passaram; teste antigo da home atualizado para exigir noindex quando o modo global está desligado.
- Builds true/false aprovados; `/catalog-social.png` aparece estático e tem corpo PNG de 37693 bytes no artefato do build.
- PostgreSQL: runtime sem superuser/createDB/createRole/replication/bypassRLS, sem CREATE/TEMP no banco nem CREATE no schema. SELECT funciona; CREATE TABLE retorna SQLSTATE 42501. E2E atende HTTP com runtime e prepara a base isolada com migrator.
- SCA: `npm audit --audit-level=moderate` e `composer audit --locked` sem advisories.
- Gitleaks: arquivos Git-visíveis atuais, incluindo novos, e histórico de 16 commits aprovados. A primeira execução sinalizou 99 falsos positivos: 45 exemplos de JWT truncado, 5 referências a design tokens, 48 checksums do manifesto BMAD e 1 identificador de story. Exceções combinam caminho e conteúdo; nenhuma pasta inteira foi excluída e nenhum segredo real confirmado foi aceito.
- Semgrep: configuração oficial `p/default`, métricas desativadas, imagens Docker fixadas por digest. Primeiro scan: 5 achados; acesso a propriedade foi tornado literal; quatro ocorrências têm supressões locais específicas, justificadas abaixo. Revalidação final incluindo testes/scripts: **247 regras em 283 arquivos, zero achados**, saída 0.
- Imagens dos scanners: digests versionados nos scripts e workflow. Montagens de código somente leitura; Gitleaks sem rede. Semgrep obtém regras públicas; não há login/upload de código configurado.
- Referências primárias: [Gitleaks](https://github.com/gitleaks/gitleaks), [Semgrep CLI](https://docs.semgrep.dev/cli-reference), documentação local da versão instalada de Next em `node_modules/next/dist/docs/01-app/02-guides/custom-server.md`.

## Threat Model STRIDE

| Categoria | Superfície / risco | Controle e evidência |
| --- | --- | --- |
| Spoofing | Headers de cliente e origem forjados | Origem fixa validada; servidor deriva IP do socket ou proxy explícito, sobrescreve header interno; assinatura HMAC expira em 30 s; testes de adulteração, expiração, isolamento de cotas e headers falsos. |
| Tampering | Payload de outra consulta, XSS, XML/JSON malformados | Envelopes fechados, identidade validada, query estrita, escape JSON-LD/XML e testes com fechamento de script. |
| Repudiation | Verificação sem evidência | Relatórios versionados, testes e gates CI; não há nova operação de escrita pública nesta story. |
| Information Disclosure | Segredos/PII/admin em resposta ou log | Projeção pública mínima, mensagens sanitizadas, identificador HMAC sem IP em claro, scans redigidos e `.env` fora do Git. |
| Denial of Service | Cota compartilhada, imagem por acesso, payload ou consulta ilimitada | Cota por identidade autenticada separada da navegação, PNG estático, lotes/bytes/deadline/statement timeout limitados; benchmark de 100 mil produtos. |
| Elevation of Privilege | Runtime com papel administrativo no banco | Runtime DML e migrator não-superuser distintos, provisionamento local executado e teste de privilégio negativo. |

## Achados

### Alto Risco

Nenhum achado de segurança confirmado. A regressão funcional IMP-01 foi corrigida na validação de listagem vazia, mantendo filtros exatos e impedindo indexação de categoria inexistente.

### Médio Risco

#### SEC-IMP-M01 — cota compartilhada por todos os visitantes do BFF

- Componente: entrada HTTP web e limiter de sitemap Laravel.
- Risco/impacto: a API via o IP do BFF; um visitante podia consumir a cota comum e impedir crawl dos demais.
- Tratamento: servidor Node identifica o socket; proxy só é aceito por allowlist de IP exato, com `X-Real-IP` sobrescrito pelo proxy. Header interno sempre sobrescrito. HMAC com timestamp validado antes do throttle; acesso direto usa `REMOTE_ADDR`, sem confiar em X-Forwarded-For.
- Evidência: testes PHP provam duas cotas independentes, expiração/adulteração rejeitadas e ausência de bypass por X-Forwarded-For; testes Node e E2E cobrem ingresso.
- Status: **Corrigido**. A implantação deve usar `npm run start`, chave privada compartilhada e proxy configurado conforme README.

#### SEC-IMP-M02 — renderização da imagem editorial em cada acesso

- Componente: `/catalog-social.png`.
- Risco/impacto: consumo de CPU repetido para conteúdo fixo; o probe original não era ensaio de saturação.
- Tratamento: `force-static`, geração no build.
- Evidência: build marca a rota estática; PNG existente em `.next/server/app/catalog-social.png.body`; HTTP valida formato e dimensões 1200×630.
- Status: **Corrigido**.

#### SEC-IMP-M03 — usuário de runtime com superuser no PostgreSQL local

- Componente: `.env` local, configuração de testes e CI.
- Risco/impacto: comprometimento da aplicação ampliava o alcance para DDL e administração do cluster.
- Tratamento: papéis runtime/migrator separados, credenciais aleatórias em arquivos ignorados, defaults de grants para objetos futuros. Conta administrativa limitada ao bootstrap; nenhuma base/volume de desenvolvimento apagado.
- Evidência: `RuntimeDatabasePrivilegesTest` com 11 assertions e E2E executando API com runtime. Migrações/testes usam migrator sem superuser.
- Status: **Corrigido** no ambiente local e na configuração CI. Nenhum ambiente remoto foi provisionado nesta tarefa.

### Baixo Risco

#### SEC-IMP-L01 — ausência de SAST e scanner dedicado de segredos

- Componente: verificações locais/CI.
- Risco/impacto: inspeção manual e audits de dependências não cobriam código inseguro nem credenciais versionadas.
- Tratamento: Semgrep e Gitleaks executáveis por scripts, imagens fixadas por digest, gates obrigatórios em workflow versionado.
- Evidência: scans descritos acima. A regra genérica de `dangerouslySetInnerHTML` foi revisada contra serializador que escapa todo `<` e testes XSS; três alertas HTTP são probes com origem loopback fixa. Supressões somente na linha e regra correspondentes.
- Status: **Corrigido**; scan final incluindo testes aprovado.

#### SEC-IMP-L02 — políticas de agente ausentes do repositório

- Componente: governança do workspace.
- Risco/impacto: ausência de instruções verificáveis para mudanças arquiteturais, comandos destrutivos, navegação e gate BMAD.
- Tratamento: `AGENTS.md` versiona revisão humana, limites de comandos, allowlist de navegação e revisão de segurança antes de ready-for-dev/conclusão; respeita autorizações já concedidas.
- Evidência: arquivo aplicável à raiz, sem alterar configurações globais.
- Status: **Corrigido no repositório**. A política é instrucional; sandbox/autoaprovação do cliente e enforcement externo não são controlados nem atestados por este arquivo. Não foi feita alteração de IDE/sandbox.

#### SEC-IMP-L03 — cobertura insuficiente de ignore para credenciais/exportações

- Componente: `.gitignore`.
- Risco/impacto: inclusão acidental de chaves, certificados, dumps e backups.
- Tratamento: padrões explícitos, mantendo SQL de bootstrap versionado e `.env.example` sem segredos reais. Scanners leem somente arquivos Git-visíveis e relatórios locais ficam ignorados.
- Evidência: regras versionadas e scanner do workspace aprovado.
- Status: **Corrigido**.

## Limites da conclusão

O gate cobre o código local e a configuração versionada. Não comprova execução remota do GitHub Actions, enforcement da IDE, infraestrutura de produção, teste de saturação, previews reais nas redes sociais ou ausência universal de vulnerabilidades. O volume medido é 100 mil produtos. Não houve commit, push ou deploy.

## Decisão do gate

**Aprovado para revisão.** Sem achados altos, médios ou baixos abertos no código/controles de repositório examinados. Dev concluído e story em review; implantação deve seguir README e aplicar os controles externos do ambiente. Scans automatizados não substituem a revisão independente.
