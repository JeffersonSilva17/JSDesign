## Deferred from: code review of 1-1-inicializar-a-fundacao-tecnica-da-plataforma (2026-08-04)

- Pin de digest/imagem Docker exata para PostgreSQL/Redis — deferred porque a story pediu PostgreSQL 18.x e Redis, não uma política completa de pin por digest; tratar como hardening operacional.
- Pin de GitHub Actions por SHA em vez de tags versionadas — deferred porque os workflows já usam versões maiores explícitas e a política de pin por SHA deve ser definida como hardening operacional.
- Tornar criação de `jsdesign_test` idempotente quando volume Docker já existe — deferred porque afeta apenas ambientes com volume antigo; tratar junto da documentação/troubleshooting Docker.
- Criar testes negativos dedicados para BFF degradado/API indisponível — deferred porque exige estrutura adicional de teste/servidor com ambiente alternativo; tratar em hardening de testes do BFF.
