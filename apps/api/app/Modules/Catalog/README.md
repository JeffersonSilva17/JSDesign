# Módulo Catalog

`Catalog` é a autoridade Laravel para cadastro, validação comercial, publicação e retirada de produtos. Controllers delegam a casos de uso; regras condicionais ficam em `Domain`; PostgreSQL e Query Builder ficam em `Infrastructure`.

## Contratos de leitura futuros

As Stories 2.2 e 2.3 devem criar portas de query em `Application` e implementá-las em `Infrastructure`. Não devem importar Models Eloquent nem consultar tabelas do catálogo a partir de controllers, BFF ou outros módulos.

A projeção pública pode expor apenas: ID, slug, nome, descrição, modalidade, preço/moeda, disponibilidade, entrega, taxonomia editorial não protegida e imagens. `PublicCatalogProductResource` é a allowlist inicial; ainda não existe rota pública nesta story.

São estritamente administrativos: estado/observações da verificação de direitos, referência de evidência, autora e timestamp da verificação. Personagens/ativos protegidos não são categoria pública.

## Fronteiras ainda fail-closed

- `AdminIdentityResolver`: o adapter real precisa produzir identidade com `catalog.manage`; o adapter atual sempre nega.
- `FileReferenceValidator`: o adapter real precisa comprovar existência, pertença e estado aceito da referência; o adapter atual sempre rejeita.

Testes substituem essas portas por fakes explícitos. Nenhum header confiado, token fixo, path, URL, base64 ou binário é aceito como atalho operacional.

Categorias são entidades referenciadas e precisam ser provisionadas por uma operação administrativa/migração de dados aprovada antes do cadastro de produtos. A UI e o CRUD administrativo de categorias permanecem no escopo da Story 8.2.
