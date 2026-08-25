## Deferred from: code review of 1-1-inicializar-a-fundacao-tecnica-da-plataforma (2026-08-04)

- Pin de digest/imagem Docker exata para PostgreSQL/Redis — deferred porque a story pediu PostgreSQL 18.x e Redis, não uma política completa de pin por digest; tratar como hardening operacional.
- Pin de GitHub Actions por SHA em vez de tags versionadas — deferred porque os workflows já usam versões maiores explícitas e a política de pin por SHA deve ser definida como hardening operacional.
- Tornar criação de `jsdesign_test` idempotente quando volume Docker já existe — deferred porque afeta apenas ambientes com volume antigo; tratar junto da documentação/troubleshooting Docker.
- Criar testes negativos dedicados para BFF degradado/API indisponível — deferred porque exige estrutura adicional de teste/servidor com ambiente alternativo; tratar em hardening de testes do BFF.

## Deferred from: code review of 2-1-cadastrar-produtos-com-estrutura-de-catalogo-comercial (2026-08-13)

- Politicas de IDE/sandbox (`Artifact Review Policy`, bloqueio de comandos destrutivos e allowlist de browser) nao foram encontradas em arquivo auditavel no repo; tratar como hardening de governanca do ambiente.

## Deferred from: code review of 2-2-exibir-listagens-publicas-por-categoria-ocasiao-e-tipo (2026-08-25)

- SAST/secret scan seguem sem ferramenta configurada no repositório — deferred porque a ausência da ferramenta é um hardening operacional preexistente; a story não deve declarar cobertura inexistente, e o gate deve ser resolvido por CI/ferramenta aprovada.
