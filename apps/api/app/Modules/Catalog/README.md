# Módulo Catalog

`Catalog` é a autoridade Laravel para cadastro, validação comercial, publicação e retirada de produtos. Controllers delegam a casos de uso; regras condicionais ficam em `Domain`; PostgreSQL e Query Builder ficam em `Infrastructure`.

## Leitura pública

A leitura usa `PublicCatalogQuery`, implementada por `PostgresPublicCatalogQuery`, e não reutiliza o repository de comandos. Apenas produtos `published` participam de dados, totais e facetas. Categoria, ocasião e modalidade combinam por `AND`; a ordem é `published_at DESC, id DESC` e o tamanho máximo é 48.

Listagem, detalhe e facetas possuem throttle e statement timeout configuráveis por `CATALOG_PUBLIC_RATE_LIMIT_PER_MINUTE` e `CATALOG_PUBLIC_STATEMENT_TIMEOUT_MS`. O limite usa hash do endereço resolvido pela conexão/proxy confiável do Laravel; uma topologia BFF compartilhada deve configurar proxies confiáveis e dimensionar o bucket antes da produção.

## Busca pública

`GET /api/v1/catalog/search` usa a porta `PublicCatalogSearchQuery` e mantém relevância, intenção e restrições de publicação no Laravel. Nome exato recebe prioridade; categoria, modalidade, tema, ocasião, alias e personagem verificado formam a segunda classe exata; prefixos e similaridade vêm depois. Score, fonte do match, alias, personagem e direitos nunca entram na projeção pública. A paginação percorre todos os exatos antes dos semelhantes e conserva a ordenação total por classe, score, `published_at DESC` e `id DESC`.

A migration de busca instala `unaccent` e `pg_trgm`, cria a função imutável versionada `catalog_public_search_normalize_v1` e índices GIN vinculados à expressão usada pelo predicado `%`. O rollback remove somente função e índices próprios; extensões potencialmente compartilhadas são preservadas. A função depende das regras do dicionário `unaccent`; se essas regras mudarem, os índices de busca do catálogo devem passar por `REINDEX`. Os testes PostgreSQL comprovam o plano com fixture de 5.000 produtos.

Os controles dedicados são `CATALOG_SEARCH_SIMILARITY_THRESHOLD`, `CATALOG_SEARCH_CANDIDATE_LIMIT`, `CATALOG_SEARCH_RATE_LIMIT_PER_MINUTE` e `CATALOG_SEARCH_STATEMENT_TIMEOUT_MS`. Valores inválidos ou fora dos ranges seguros retornam aos defaults conservadores. O threshold de `pg_trgm` é aplicado localmente à transação, sem alterar a sessão global fora da operação.

Sugestões são limitadas a seis valores editoriais de categoria, tema e ocasião, com href same-origin e ordenação por relevância antes do rótulo. A intenção de convite usa vocabulário configurado no backend e só aparece quando a busca não encontra produto publicado. O endpoint não registra nem devolve o termo bruto fora de `meta.query`/`preserved_term`, usa `Cache-Control: no-store, private` e sanitiza falhas como `422`, `429` ou `503`.

`PublicCatalogImageResolver` resolve referências em lote. O binding padrão `FailClosedPublicCatalogImageResolver` não devolve URL: fotos reais exigem adapter aprovado que produza somente paths servidos pelo mesmo origin do Next.js. Fixtures não equivalem à prontidão operacional.

Handoff: canonical/OG/sitemap pertencem à 2.4; detalhes completos, configuração e compra aos Epics 3/4.

### Evidência operacional de 2026-08-19

- `composer audit --locked`: nenhuma vulnerabilidade conhecida.
- `npm audit --audit-level=moderate`: zero vulnerabilidades.
- O repositório não possui ferramenta SAST nem secret scanner configurado; esse gate permanece explicitamente pendente para o CI aprovado e não é declarado como coberto.
- O projeto ainda não definiu o ambiente representativo exigido pelo NFR-2 para medir Core Web Vitals no 75º percentil. O fixture valida reflow, mídia dimensionada e tarefa sem JavaScript, mas não substitui a futura evidência de campo/laboratório no ambiente aprovado.

A projeção pública pode expor apenas: ID, slug, nome, categoria pública, descrição ou excerpt conforme endpoint, modalidade, preço/moeda, disponibilidade, entrega, prazo, taxonomia editorial não protegida e `primary_image` já resolvida ou `null`. `PublicCatalogProductResource` é a allowlist dos GETs públicos desta story; referências opacas de storage, direitos, personagem e campos administrativos não entram no contrato.

São estritamente administrativos: estado/observações da verificação de direitos, referência de evidência, autora e timestamp da verificação. Personagens/ativos protegidos não são categoria pública.

## Fronteiras ainda fail-closed

- `AdminIdentityResolver`: o adapter real precisa produzir identidade com `catalog.manage`; o adapter atual sempre nega.
- `FileReferenceValidator`: o adapter real precisa comprovar existência, pertença e estado aceito da referência; o adapter atual sempre rejeita.

Testes substituem essas portas por fakes explícitos. Nenhum header confiado, token fixo, path, URL, base64 ou binário é aceito como atalho operacional.

Categorias são entidades referenciadas e precisam ser provisionadas por uma operação administrativa/migração de dados aprovada antes do cadastro de produtos. A UI e o CRUD administrativo de categorias permanecem no escopo da Story 8.2.
