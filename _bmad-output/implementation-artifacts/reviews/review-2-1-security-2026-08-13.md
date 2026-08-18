# Revisao Adversarial de Seguranca - Story 2.1

## Metadados

- Artefato auditado: Story 2.1 - Cadastrar produtos com estrutura de catalogo comercial
- Escopo: File List da story 2.1, endpoints administrativos de Catalog, contratos, migrations, testes e SCA local do repo
- Data: 2026-08-13
- Auditor: Vex - Security Auditor
- Resultado do gate: Aprovado apos correcoes de 2026-08-17

## Evidencias Coletadas

- Arquivos lidos: story 2.1, `routes/api.php`, Catalog Domain/Application/Infrastructure/Http, migration Catalog, testes Catalog, OpenAPI, `.gitignore`, lockfiles
- Comandos executados: `composer audit`, `npm audit --audit-level=moderate`, `php artisan test --filter=CatalogAdminApiTest --stop-on-failure`, `php artisan route:list --path=api/v1/admin/catalog/products`, buscas `rg` para segredos/logs/SQL cru
- Ferramentas nao executadas: `detect-secrets`, `gitleaks`, `trufflehog`, `semgrep`, `sqlfluff` nao instalados no ambiente
- Referencias consultadas: OWASP API Security Top 10 2023, OWASP Top 10 for LLM Applications 2025, GitHub Advisory Database para `league/commonmark` e `nanoid`

## Threat Model STRIDE

| Categoria | Superficie | Risco | Mitigacao existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | Escritas admin `/api/v1/admin/catalog/products` | Identidade admin falsa | Resolver fail-closed e fake apenas em testes | Estrategia operacional real ainda fora de escopo |
| Tampering | PATCH/publish/unpublish por `{product}` | ID malformado ou stale version | Versionamento otimista no repository | Rota nao restringe UUID antes do banco; publish valida antes da versao |
| Repudiation | Verificacao de direitos | Evidencia verified incompleta | DB exige evidencia/ator/data para `verified` | HTTP nao valida `evidence_reference` antes da constraint |
| Information Disclosure | Projecao publica futura | Referencia opaca de imagem rejeitada pode aparecer | Public resource remove dados admin de direitos | Secundarias nao sao revalidadas na publicacao |
| Denial of Service | Dependencias backend/frontend | Advisories altas de DoS em `league/commonmark` e `nanoid` | SCA local detectou | Lockfiles ainda vulneraveis |
| Elevation of Privilege | Rotas admin | Escrita sem permissao | Middleware `catalog.manage` fail-closed | Sem RBAC operacional real ate story posterior |

## Achados

### Alto Risco

#### SEC-001

- Item / Componente Afetado: `apps/api/composer.lock`, `apps/web/package-lock.json`
- Risco Detectado: SCA local encontrou vulnerabilidades altas: `league/commonmark 2.8.3` afetado por DoS corrigido em `2.9.0`; `nanoid <3.3.18` afetado por loop infinito quando size zero.
- Impacto para o Projeto: Se alguma superficie processar Markdown ou gerar IDs com tamanho controlavel, um atacante pode consumir CPU/event loop e degradar disponibilidade.
- Solucao Recomendada: Decidir se o escopo desta revisao inclui atualizar lockfiles. Backend: atualizar `league/commonmark` para `>=2.9.0` com testes. Frontend: executar `npm audit fix`/atualizar `nanoid` e validar build/testes.
- Evidencia: `composer audit` retornou 6 advisories para `league/commonmark`; `npm audit` retornou 1 high para `nanoid`.
- Status: Corrigido em 2026-08-17; `league/commonmark` atualizado para 2.10.0, `npm audit fix` removeu advisory de `nanoid`, `composer audit` e `npm audit --audit-level=moderate` limpos.

### Medio Risco

#### SEC-002

- Item / Componente Afetado: `apps/api/routes/api.php:24-26`
- Risco Detectado: Rotas com `{product}` nao restringem UUID antes de consultas PostgreSQL.
- Impacto para o Projeto: IDs malformados podem produzir erro de banco (`22P02`) e quebrar o contrato previsivel de erro.
- Solucao Recomendada: Aplicar `whereUuid('product')` nas rotas de update/publish/unpublish e adicionar teste HTTP para ID malformado.
- Evidencia: OpenAPI declara `format: uuid`, mas `route:list` mostra rotas sem constraint visivel.
- Status: Corrigido em 2026-08-17.

#### SEC-003

- Item / Componente Afetado: `CatalogRules.php:46-48`, `PostgresCatalogProductRepository.php:174-180`
- Risco Detectado: `status=verified` aceita `evidence_reference=null`; a constraint de banco rejeita depois e o repository nao traduz check violations para `422`.
- Impacto para o Projeto: Payload admin invalido pode virar 500, sem erro acionavel por campo.
- Solucao Recomendada: Exigir evidencia quando status for `verified` na validacao/aplicacao e mapear SQLSTATE/check violation relevante para `CatalogValidationFailed`.
- Evidencia: Migration `catalog_verified_rights_complete_check` exige evidencia/ator/data; Form Request nao.
- Status: Corrigido em 2026-08-17.

#### SEC-004

- Item / Componente Afetado: `Product.php:68-79`
- Risco Detectado: Apenas imagem primaria precisa estar validada para publicar; imagens secundarias com referencia rejeitada podem persistir e aparecer em projecoes futuras.
- Impacto para o Projeto: Catalogo publicado pode carregar referencias inexistentes ou nao aprovadas, quebrando entrega visual e abrindo brecha para referencia opaca indevida.
- Solucao Recomendada: Validar todas as imagens associadas durante publicacao e reportar `images.{index}.storage_reference`.
- Evidencia: `PublishCatalogProduct` marca todas as imagens, mas `Product::publicationErrors()` confere apenas a primaria.
- Status: Corrigido em 2026-08-17.

#### SEC-005

- Item / Componente Afetado: `Product.php:62`, `Product.php:135-178`
- Risco Detectado: Campos textuais obrigatorios aceitam strings compostas apenas por whitespace.
- Impacto para o Projeto: Produto publicado pode satisfazer regra comercial com conteudo vazio na pratica.
- Solucao Recomendada: Usar checagem `trim($value) === ''` para campos textuais gerais e por modalidade.
- Evidencia: Validacao atual compara apenas `=== ''` ou `empty()`.
- Status: Corrigido em 2026-08-17.

### Baixo Risco

#### SEC-006

- Item / Componente Afetado: Politicas de IDE/sandbox do projeto
- Risco Detectado: Nao encontrei configuracao verificavel de `Artifact Review Policy`, `Terminal Command Auto Execution Policy` ou `Browser URL Allowlist`.
- Impacto para o Projeto: Governanca depende de configuracao externa nao auditavel pelo repo.
- Solucao Recomendada: Registrar essas politicas no projeto ou em configuracao controlada, se o ambiente suportar.
- Evidencia: Ausencia de arquivos/configs localizados no escopo do repo.
- Status: Aberto, deferivel.

## Decisao do Gate

- Decisao: Aprovado
- Condicoes antes de avancar: Nenhuma condicao bloqueante restante; SEC-006 permanece deferido como hardening de governanca fora do repo.
- Owner: Sharom / Dev
- Prazo / proximo checkpoint: antes de mover Story 2.1 para `done`
