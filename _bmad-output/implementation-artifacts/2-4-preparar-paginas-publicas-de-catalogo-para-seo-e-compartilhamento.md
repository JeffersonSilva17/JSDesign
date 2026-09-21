---
baseline_commit: 140887ad11875de3da2f57588dff62d00506ab94
---

# Story 2.4: Preparar páginas públicas de catálogo para SEO e compartilhamento

Status: done

## Story

Como cliente que chega pelo Google ou link compartilhado,
quero abrir páginas claras e indexáveis de catálogo,
para entender rapidamente o produto ou categoria antes de comprar.

Requisitos: FR-3; NFR-2 e NFR-10. Dependências 2.1, 2.2 e 2.3 concluídas. Esta é uma especificação de implementação; nenhuma funcionalidade abaixo foi implementada nesta criação.

## Acceptance Criteria

1. **Metadados SSR em pt-BR**
   - **Given** uma página pública válida de catálogo, categoria, produto ou busca, **When** o HTML é solicitado, **Then** existe título e descrição específicos, sem marca duplicada, UTF-8 preservado e `lang="pt-BR"`.
   - **And** bots de compartilhamento recebem metadados resolvidos no HTML sem executar JavaScript; validar o comportamento de streaming da versão instalada, sem desabilitá-lo globalmente sem necessidade.
   - **And** textos editoriais vêm do i18n existente, e nomes/descrições de produto vêm exclusivamente da projeção pública validada do Laravel.

2. **Canonical e indexação determinísticos**
   - **Given** as rotas e estados da matriz em Dev Notes, **When** são renderizados, **Then** seguem exatamente a política indicada, com uma única canonical absoluta quando aplicável.
   - **And** `/produtos?category=<slug>` identifica uma categoria real publicada, usa o label público no título, descrição e H1 e não depende de uma nova rota `/categorias/[slug]`.
   - **And** paginação válida preserva seu número no canonical; `page=1` é normalizado para a URL base equivalente. Não canonicalizar páginas 2+ para a página 1.
   - **And** `return_to` nunca aparece no canonical, OG, JSON-LD ou sitemap do produto, mas continua funcional no link de retorno.
   - **And** parâmetros inválidos/duplicados/desconhecidos não viram metadados indexáveis nem passam a ser aceitos silenciosamente pelo parser existente.
   - **And** `SEO_INDEXING_ENABLED=false` prevalece sobre qualquer regra positiva, conforme política de ambientes; falhas de identidade entre consulta e payload nunca produzem conteúdo do recurso errado.

3. **Compartilhamento coerente e seguro**
   - **Given** produto publicado ou categoria conhecida, **When** um crawler lê a página, **Then** Open Graph e Twitter Card têm título, descrição e imagem coerentes; OG inclui URL canônica, nome da loja, locale `pt_BR` e tipo suportado.
   - **And** imagens são URLs absolutas da origem pública configurada, com alt; usar somente mídia pública aprovada e validada. Quando ausente/incompatível, usar fallback editorial real da JS Designs, PNG/JPEG, sem fingir ser foto do produto.
   - **And** categorias usam fallback editorial da marca: hoje o contrato de facetas não tem imagem nem descrição editorial de categoria. Não inventar esses campos nem escolher produto aleatório como identidade da categoria.
   - **And** busca usa descrição editorial genérica, sem copiar `q` para tags sociais, canonical, JSON-LD, logs ou terceiros. O termo continua no formulário e na URL operacional conforme a 2.3.
   - **And** nenhuma superfície SEO expõe personagem/alias interno, direitos, notas administrativas, PII, caminhos de storage ou URLs privadas.

4. **Dados estruturados verdadeiros**
   - **Given** produto publicado, **When** a página é renderizada, **Then** JSON-LD `Product` contém apenas nome, descrição pública, URL canônica e imagem real do produto quando disponível.
   - **Given** listagem/categoria indexável com itens, **Then** `CollectionPage` e `ItemList` representam apenas itens efetivamente visíveis e seus links públicos.
   - **And** não fabricar avaliações, SKU, estoque, prazos, promoções ou `Offer`: a compra/configuração completa pertence aos próximos épicos. Não prometer elegibilidade a rich results.
   - **And** JSON-LD é serializado por rotina única que escapa `<` como `\u003c`; um conteúdo `</script><script>` permanece dado e não executa código.

5. **Sitemap completo por lotes e robots**
   - **Given** catálogo publicado, **When** `/sitemap.xml` e seus documentos filhos são solicitados, **Then** o índice referencia páginas de sitemap limitadas e todos os produtos publicados são alcançáveis em um catálogo estável, sem truncamento silencioso.
   - **And** o sitemap editorial inclui `/produtos`, `/categorias` e primeiras páginas de categorias públicas conhecidas; não inclui busca, combinações de filtros, paginação de UI, placeholders, áreas privadas ou transacionais.
   - **And** cada URL de produto é formada de slug público validado. Um produto retirado deixa de aparecer em novas respostas; não há cache persistente de catálogo sem invalidação.
   - **And** uma falha de API produz erro HTTP sanitizado, não XML parcial com `200` alegando sucesso. Limites e contrato de lotes estão descritos em Dev Notes.
   - **And** com indexação habilitada, `robots.txt` referencia o índice absoluto e não bloqueia o crawl das páginas que precisam comunicar `noindex`; com indexação desabilitada não anuncia sitemap. Robots não substitui autenticação/autorizações.
   - **And** índice e editorial passam pela mesma validação de facetas/tamanho; nenhum índice 200 anuncia um editorial que já se sabe inválido. O crawl não consome a cota de navegação e os limites upstream são aplicados antes de JSON.parse.

6. **Estados inválidos, retirada e indisponibilidade**
   - **Given** produto inexistente, slug inválido ou não publicado, **When** acessado, **Then** preserva `notFound()` e `noindex`, sem metadata/JSON-LD de produto anterior ou inventado.
   - **Given** timeout, payload inválido ou API indisponível, **Then** mantém estado de indisponibilidade sanitizado com `noindex`, sem tratar a falha como produto inexistente e sem dados estruturados comerciais.
   - **And** metadata e corpo usam o mesmo resultado de leitura durante a requisição, inclusive nos erros; a próxima requisição reconsulta a publicação.
   - **And** status e presença de noindex são verificados separadamente para respostas não transmitidas (404 no not-found) e respostas já em streaming, conforme a matriz de ambientes/crawlers.
   - **And** categoria inexistente/vazia, página fora do intervalo e filtros inválidos seguem estados existentes e não recebem indexação positiva.

7. **Testes e regressões**
   - **Given** fixtures de produtos/categorias e respostas adversas, **When** testes unitários, HTTP e Playwright executam, **Then** verificam a matriz SEO, canonical/OG absolutos, XML completo por lotes, JSON-LD escapado, retirada de publicação e ausência de campos privados.
   - **And** verificar respostas HTTP com user agent de crawler de compartilhamento e navegação sem JavaScript; não validar apenas DOM após hidratação.
   - **And** executar as matrizes de bots, HTTP e ambiente, os testes de identidade/memoização/limites, integridade de todos os filhos e benchmark de sitemap definidos em Dev Notes.
   - **And** manter filtros, paginação, busca, retorno do detalhe, acentos, teclado/foco e reflow em 320/420/760/1100 px. SEO não adiciona dependência de JavaScript cliente, chamada browser→Laravel ou mídia bloqueando a tarefa.

## Tasks / Subtasks

- [x] 1. Fixar política SEO e configuração de origem (AC: 1–3, 6)
  - [x] Criar testes da matriz e construtores de canonical; reutilizar parsers e `catalogHref`.
  - [x] Criar módulo `server-only` para `SITE_URL`, documentar `.env.example`, README e configuração de testes/CI, incluindo builds nos workflows frontend e smoke; origem absoluta, sem usuário/senha, path além de `/`, query ou fragmento.
  - [x] Exigir HTTPS fora de loopback; permitir HTTP somente para loopback local/CI. Ausência ou valor inválido falham com diagnóstico sanitizado, sem fallback para Host, forwarded headers, localhost em produção ou `API_INTERNAL_URL`.
  - [x] Implementar SEO_INDEXING_ENABLED com default false, precedência global, header HTML e modos de robots/XML; documentar local/preview/público e executar CI em ambos os modos.

- [x] 2. Implementar metadados e apresentação de categoria (AC: 1–3, 6)
  - [x] Centralizar composição em `src/features/catalog-seo/`; usar Metadata API e conteúdo i18n tipado.
  - [x] Compartilhar leitura validada entre `generateMetadata` e corpo com memoização restrita à requisição; manter `no-store` entre requisições.
  - [x] Usar chaves primitivas canônicas e provar contagem de chamadas em sucesso/erro; implementar validação semântica da listagem, igualdade do slug retornado e classificação de query do detalhe.
  - [x] Atualizar catálogo, índice de categorias, busca e detalhe; remover o `noindex` herdado do detalhe somente no sucesso publicado.
  - [x] Compor título/descrição/H1 com label de categoria validado pelo Laravel; nunca com slug bruto arbitrário. Preservar filtros e estados da 2.2.
  - [x] Configurar metadataBase no root sem canonical global herdável, preservar title template e referrer policy.
  - [x] Entregar fallback social real com identidade editorial existente e dimensões conhecidas; não habilitar carregamento remoto arbitrário nem resolver storage privado.

- [x] 3. Adicionar dados estruturados seguros (AC: 4, 6)
  - [x] Criar composição tipada para Product/CollectionPage/ItemList e serializador seguro único.
  - [x] Renderizar somente em estados elegíveis; testar HTML malicioso, Unicode, imagem ausente e nenhum campo administrativo.

- [x] 4. Expor projeção mínima para sitemap (AC: 5)
  - [x] Criar caso de uso/DTO no Catalog/Application e ampliar porta/adapter de query existentes; reutilizar restrição de publicação, sem hidratar imagens, descrição ou detalhes por produto.
  - [x] Implementar endpoint paginado e contrato descritos abaixo, Form Request estrito, Resource allowlist, limiter e no-store; atualizar OpenAPI e testes PostgreSQL.
  - [x] Expor sitemap-facets com projeção editorial limitada e limiter dedicado para ambos os endpoints; registrar configuração/binding e prova de isolamento da navegação.
  - [x] Criar cliente BFF server-only que valida runtime envelope, bounds, slugs, duplicatas e consistência da paginação.
  - [x] Limitar streaming de JSON antes do parse e implementar matriz HTTP/Retry-After, incluindo erros de corpo e timeout durante leitura.
  - [x] Criar índice XML e filhos editoriais/produtos, com trabalho limitado por requisição e erro sanitizado. Não usar loop que baixa o catálogo inteiro na requisição do índice.
  - [x] Adicionar robots e testes de cobertura completa num conjunto estável com mais de um lote, retirada, lote final, vazio, lote inválido e falha da API.
  - [x] Validar editorial antes de publicar índice; testar 5000/5001 URLs e percorrer todos os filhos em catálogo estável. Executar benchmark PostgreSQL de primeiro/último lote e registrar limites medidos antes de concluir.

- [x] 5. Integrar testes e validar qualidade (AC: 1–7)
  - [x] Adicionar `tests/unit/catalog-seo.test.mjs`, incluir sua execução em script npm explícito e adicionar `tests/e2e/catalog-seo.spec.ts`.
  - [x] Testar origem adulterada, parâmetros duplicados, `return_to` externo, XSS, estados degradados e ausência de conteúdo administrativo em HTML/XML/JSON-LD/OG.
  - [x] Preservar suites de listagem/busca/foundation, incluindo retorno para `/buscar?q=...`.
  - [x] Executar os gates do projeto listados abaixo e registrar resultados reais. Não atualizar dependências/lockfiles nesta story.

### Review Findings

- [x] [Review][Patch] Validar slugs públicos antes de responder sitemap e facetas [apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php:34]
- [x] [Review][Patch] Garantir `Cache-Control: no-store, private` também nas respostas 422 do sitemap [apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogSitemapRequest.php:20]
- [x] [Review][Patch] Alinhar contrato OpenAPI dos erros de sitemap com 403/404/Retry-After reais [packages/contracts/catalog-public-v1.openapi.yaml:12]
- [x] [Review][Patch] Preservar 429/404 upstream antes de exigir corpo JSON no transporte de sitemap [apps/web/src/bff/catalogSitemapTransport.ts:20]
- [x] [Review][Patch] Validar facetas de ocasião com as mesmas regras dos links gerados [apps/web/src/bff/catalogValidation.ts:53]
- [x] [Review][Patch] Restringir textos públicos usados em metadata/JSON-LD aos limites do contrato [apps/web/src/bff/catalogValidation.ts:145]
- [x] [Review][Patch] Exigir arquivo regular antes de aceitar `primary_image` pública [apps/web/src/bff/catalogValidation.ts:150]
- [x] [Review][Security] Reexecutar Semgrep/Gitleaks versionados e suite HTTP de sitemap em ambiente com Docker/PostgreSQL disponíveis [reviews/review-2-4-code-review-security-postpatch.md]

## Dev Notes

### Contexto, escopo e decisões

`epics.md#Story 2.4` é a numeração autoritativa. `epics-next-only-2026-07-27.md` é histórico e não redefine a chave. FR-3 e addendum 3.10/3.11 fundamentam canonical, sitemap e marcação estruturada além dos AC resumidos do épico.

A política abaixo é uma decisão de implementação desta story para as rotas existentes. Não criar páginas de tema, versões EN/ES, hreflang fictício, CMS SEO, analytics, botão de compartilhamento, Search Console, novo provedor ou UI de compra. FR-3 global também cita temas/institucionais: essas superfícies serão tratadas quando tiverem conteúdo real; não incluir placeholders como se estivessem prontos. Epic 3.1 permanece dono do enriquecimento/configuração do detalhe.

### Matriz de indexação e canonical

Aplicar `SITE_URL` como origem em todos os valores abaixo. A matriz vale com `SEO_INDEXING_ENABLED=true`; o modo false tem precedência conforme seção de ambientes. Indexável significa `index,follow`, nunca garantia de indexação pelo buscador.

| Rota/estado | Robots | Canonical | Conteúdo social/estruturado |
| --- | --- | --- | --- |
| `/produtos`, resposta válida, inclusive catálogo vazio | index,follow | `/produtos` | Editorial; ItemList somente se houver itens |
| `/categorias`, facetas válidas | index,follow | `/categorias` | Editorial; sem inventar produtos |
| `/produtos?category=<conhecida>` com itens | index,follow | Mesma query canônica | Label público e imagem editorial; coleção |
| Listagem base ou categoria conhecida com `page>1` dentro do intervalo | index,follow | Mesma URL com `page` | Título distingue número da página |
| `page=1` em listagem elegível | Conforme base | Remover `page=1` | Igual à base |
| `occasion`, `modality` ou combinação de filtros válida | noindex,follow | URL normalizada própria via `catalogHref` | Genérico; sem JSON-LD de coleção indexável |
| `/buscar`, com ou sem query | noindex,follow | Omitir | Editorial genérico, sem q e sem JSON-LD |
| Produto publicado, inclusive `availability=unavailable` | index,follow | `/produtos/<slug>` sem query | Product público, sem Offer |
| Produto publicado com query inválida segundo política do detalhe | noindex,follow | Omitir | Corpo público preservado, sem OG específico/JSON-LD |
| Produto inexistente/não publicado | noindex | Omitir | Sem OG de produto/JSON-LD |
| Filtro inválido, categoria sem itens/desconhecida, página fora do intervalo ou upstream indisponível | noindex,follow | Omitir | Estado editorial sanitizado, sem JSON-LD |
| Carrinho/conta e demais rotas fora do escopo | Preservar política existente | Não herdar canonical de catálogo | Não incluídas em sitemap |

Não relaxar aceitação de query por conveniência de SEO. Categoria só recebe título específico quando `applied_filters` e `filter_labels` validados correspondem à consulta e a resposta tem produtos; ausência de facetas complementares não derruba listagem válida. A validação semântica abaixo é obrigatória antes de usar os dados no corpo ou nos metadados.

### Validação de identidade, parâmetros e leitura compartilhada

- `fetchCatalogListing` passa critérios esperados ao validador: página, per_page=12 e filtros canônicos. Exigir igualdade exata de filtros aplicados, mesmas chaves nos labels, labels não vazios e coerentes com categoria/modalidade/ocasião dos itens; IDs únicos, `last_page=max(1,ceil(total/per_page))` e quantidade de itens igual a `min(per_page,max(0,total-offset))`. Para categoria, todos os itens têm slug/label correspondente. Metadados inconsistentes são `invalid-payload`, nunca corrigidos silenciosamente nem indexados. Testar payload de outra consulta, total/página incoerente e label divergente sem depender de facetas complementares.
- Validar slug do detalhe antes da chamada (padrão público existente, até 180 caracteres); slug inválido segue notFound. Após validar o envelope, exigir `data.slug === slugSolicitado`; divergência é indisponibilidade por payload inválido, não 404 nem redirecionamento para o produto recebido. Testar produto B retornado para URL de A, sem expor nenhum campo de B.
- No detalhe, a única chave aceita é `return_to`, opcional, única e válida segundo a allowlist existente de `/produtos` ou `/buscar`. Sem query ou com retorno válido: aplicar política do produto e canonical limpo. Chave desconhecida, valor array/duplicado, retorno vazio, malformado ou externo: manter corpo público válido e retorno seguro `/produtos`, mas emitir `noindex,follow`, omitir canonical, OG específico e JSON-LD. Essa classificação ocorre antes de metadata e corpo; não relaxa os parsers de busca/listagem. Produto inexistente sempre prevalece como notFound, qualquer que seja a query.
- Criar wrappers `cache` do React em escopo de módulo, compartilhados por metadata e página, com argumentos primitivos: slug ou tupla canônica `(category ?? '', occasion ?? '', modality ?? '', page)`. Criar objetos de filtros dentro do wrapper; nunca usar objetos recriados como chave nem cache persistente. Reutilizar também leitura de facetas na mesma requisição. Provar por contagem upstream uma chamada por recurso, em sucesso e erro; duas requisições independentes fazem duas leituras, inclusive após retirada de publicação.

### Ambientes, crawlers e status de página

- `SEO_INDEXING_ENABLED` é configuração server-side explícita, aceita somente `true`/`false`, default `false`; valor inválido falha com diagnóstico sanitizado. `true` significa publicação editorial autorizada daquele ambiente, não consequência de HTTPS ou NODE_ENV. Documentar false em local/preview/homologação e true somente no ambiente público liberado. CI testa os dois modos com origem loopback explícita, inclusive build de produção.
- Com false, todas as páginas recebem `noindex,nofollow` efetivo, nenhuma rota filha pode sobrescrever essa decisão; omitir canonical e JSON-LD. Aplicar também `X-Robots-Tag: noindex, nofollow` às respostas HTML via configuração Next. Robots permite crawl para leitura do noindex e não anuncia sitemap; todos os endpoints XML retornam 404/no-store sem consultar a API. Com true, aplicar a matriz desta story; páginas transacionais preservam suas restrições. Tags sociais editoriais continuam permitidas para validação de prévia em ambiente não indexável. Não tratar essa opção como autenticação de homologação.
- Matriz mínima de agentes: `Twitterbot/1.0`, `facebookexternalhit/1.1` e `Slackbot-LinkExpanding 1.0`, além de navegador Chromium comum. Para os três crawlers, exigir no HTML bruto um único title, description, canonical quando aplicável, cada propriedade OG/Twitter esperada e robots sem conflito, dentro de head antes de body. Validar imagem via GET, status 200 e MIME PNG/JPEG; incluir caso sem imagem de produto. Preservar lista padrão de bots caso seja necessário ajustar `htmlLimitedBots`, sem trocar por uma lista que exclua os demais. No navegador, admitir streaming nativo e verificar resultado final; testar também navegação com JavaScript desativado.
- NotFound detectado antes de iniciar streaming retorna HTTP 404, inclusive para os três crawlers; testar slug inválido/inexistente/retirado no HTML bruto. Se uma resposta de navegador já iniciou streaming, HTTP 200 é permitido apenas acompanhado do estado not-found e noindex efetivo, sem canonical/OG de produto/JSON-LD. Não capturar a exceção de `notFound()` em catch genérico: tratar erro da consulta primeiro e invocá-lo fora do catch. Falha de dependência mantém estado indisponível/noindex e pode retornar 200 no Server Component; não alegar 503 de página sem implementá-lo. Os handlers XML seguem a matriz HTTP específica abaixo e nunca iniciam corpo antes da validação.

### Contrato de sitemap e limites operacionais

Adicionar `GET /api/v1/catalog/sitemap?page=<1..10000>`, tamanho fixo de 500 produtos por lote, sem filtros, q ou per_page público. Envelope fechado: `data: [{slug}]`, `meta: {current_page, per_page:500, last_page, total}`. `last_page=max(1,ceil(total/500))`. Apenas produtos publicados, ordenação total estável por ID, sem campos administrativos. Página 1 de catálogo vazio retorna lista vazia; página acima de last_page retorna 404 sanitizado. Query inválida/duplicada/array-style retorna 422, abuso 429, dependência/limite operacional 503. Não confundir total inválido ou superior a 5 milhões com catálogo vazio.

Cada filho de produtos corresponde a exatamente uma página da API. `/sitemap.xml` referencia um filho editorial e os filhos numerados, com no máximo 10000 lotes de produtos. O índice lê um lote para obter totais e valida também o editorial pela mesma função usada pelo filho; não percorre produtos nem consulta detalhe por item. Filho de produtos busca só seu lote. Limite de 5000 URLs editoriais (incluindo `/produtos` e `/categorias`): exceder faz índice e editorial retornarem 503, sem XML parcial/200 nem truncamento. Facetas inválidas/indisponíveis também bloqueiam ambos. Requisições independentes podem observar alterações concorrentes; não há promessa de snapshot global. Testar catálogo estável percorrendo todos os filhos anunciados, limites 5000/5001, catálogo vazio e falha de facetas. XML usa escape contextual para URLs, especialmente `&`. O teto de cinco milhões é limite do protocolo da aplicação, não capacidade de desempenho demonstrada.

Sugestão concreta: Route Handlers `src/app/sitemap.xml/route.ts`, `src/app/sitemap-catalogo.xml/route.ts` (query `page` obrigatória e estrita) e `src/app/sitemap-editorial.xml/route.ts`, mais `src/app/robots.ts`. Os filhos têm URLs na raiz, como `/sitemap-catalogo.xml?page=1`. Não criar simultaneamente `sitemap.ts` e `sitemap.xml/route.ts`. Estes handlers são saídas XML/robots públicas do Next, não uma segunda API comercial. Usar `Cache-Control: no-store` e `X-Robots-Tag: noindex` em todas as respostas XML, inclusive erros. Transporte: deadline de 5 s incluindo leitura completa do corpo, sem retries automáticos.

### Orçamento de banco, isolamento de crawl e limites de entrada

- `COUNT/window count + OFFSET` é estratégia inicial sujeita a medição, não prova de trabalho constante por lote. Exigir `EXPLAIN (ANALYZE, BUFFERS)` da consulta real com índices e planner normais, primeiro/último lote em fixture de 100000 produtos publicados, além de fixtures de elegibilidade. Registrar hardware, volume e tempos; orçamento de no máximo duas consultas SQL de dados por lote, nenhuma hidratação/N+1 e conclusão dentro de 3000 ms por chamada. Se falhar, otimizar índice/consulta e repetir antes de concluir. Não elevar timeout para ocultar falha nem declarar capacidade de cinco milhões validada; registrar o maior volume medido e exigir novo benchmark antes de expansão operacional. Preservar total/linhas da mesma leitura e semântica de vazio.
- Criar limiter `public-catalog-sitemap`, default 60/minuto por hash do IP visto pelo Laravel, com chaves distintas de `public-catalog` e `public-catalog-search`. Aplicar somente o limiter dedicado aos endpoints sitemap, preservando middleware no-store. Para facetas editoriais, adicionar `GET /api/v1/catalog/sitemap-facets`, sem query, reutilizando a consulta de categorias publicadas das facetas por colaboração interna, com Resource editorial próprio e consulta limitada a 4999 linhas para detectar excesso sem carregar todas as facetas; aceitar até 4998 categorias e envelope fechado apenas `{data:{categories:[{slug,label}]}}`; não duplicar regra de publicação. Essa rota usa o limiter sitemap e falha com 503 se exceder limites. Next usa essa projeção no índice/editorial, evitando consumir o limiter normal. Não repassar IP arbitrário recebido em headers. Saturar sitemap em teste não altera disponibilidade das rotas normais de produtos/facetas/busca.
- No adapter BFF de sitemap, limitar corpo JSON descompactado a 256 KiB para produtos e 4 MiB para facetas editoriais, inclusive respostas de erro. Se Content-Length exceder, rejeitar cedo; independentemente do header, contar bytes efetivamente recebidos por reader, cancelar stream no excesso/deadline e só então decodificar UTF-8 estrito e fazer JSON.parse. Rejeitar Content-Type não JSON e corpo inválido, sem registrar seu conteúdo. Testar ausência/header falso de Content-Length, chunked, JSON grande e truncado, timeout durante body e resposta de erro gigante. Limites XML são adicionais, não substituem esses limites. Preservar o comportamento dos clientes antigos ao acrescentar transporte específico ou opções explícitas.

### Matriz HTTP dos documentos XML

Erros usam corpo JSON fixo `{message: <texto pt-BR>}`, Content-Type JSON, sem corpo upstream, URL interna ou stack. Sucesso usa XML UTF-8. Validar query antes de consultar Laravel; índice/editorial não aceitam parâmetros, filho exige page único canônico 1..10000.

| Condição | Status Next | Headers/observação |
| --- | --- | --- |
| Documento válido e dependências válidas | 200 | XML completo, no-store, noindex |
| Query externa ausente no filho, repetida, array, desconhecida ou inválida | 422 | Mensagem fixa de parâmetro inválido, nenhuma chamada upstream |
| API retorna 404 para lote de produto solicitado válido | 404 | Lote inexistente, sem normalizar para página 1 |
| API retorna 404/422 em chamada interna válida do índice/editorial | 503 | Inconsistência de dependência, não erro atribuído ao visitante |
| API retorna 422 para filho já validado pelo Next | 503 | Divergência de contratos, diagnóstico interno sanitizado |
| API retorna 429 | 429 | Preservar Retry-After apenas como inteiro entre 1 e 3600; ausente/inválido usa 60 |
| Timeout, rede, demais status não 2xx, payload inválido/excessivo, limite XML/editorial | 503 | Retry-After: 60; não emitir XML parcial |
| SEO_INDEXING_ENABLED=false | 404 | Sem chamada upstream, no-store/noindex |

Modo desabilitado tem precedência sobre validação de query. Atualizar OpenAPI de ambos os endpoints, incluindo 200/422/429/503 e 404 apenas no paginado; o índice consulta página 1, que deve existir inclusive em catálogo vazio. Nesse vazio, pode anunciar o filho vazio de página 1 com XML válido. Nenhum erro de produto pode expor facetas parcialmente coletadas ou vice-versa.

Offset pode mudar entre requisições se houver publicação concorrente; não prometer snapshot global. Rejeitar payload com slugs duplicados com erro sanitizado 503, sem deduplicação/reparo silencioso; gerar a partir da publicação atual e comprovar exaustividade em catálogo estável. Não manter snapshot antigo que reexponha produto retirado. `lastmod` só com dado público confiável do backend; omitir nesta projeção em vez de usar a hora da requisição.

Limites XML: no máximo 50 MB (52.428.800 bytes) descompactados por documento e cada `loc` com menos de 2048 caracteres; aplicar antes de enviar qualquer corpo, retornar falha sanitizada se exceder. Validar Content-Type XML e encoding UTF-8.

Serialização JSON-LD em TypeScript: `JSON.stringify(value).replace(/</g, '\\u003c')`. Testar que o HTML não contém fechamento de script injetado e que `JSON.parse` do conteúdo recupera o texto original, sem barras extras.

### Estado atual dos arquivos e preservação

| Arquivo existente | Hoje | Mudança/preservação |
| --- | --- | --- |
| `apps/web/src/app/layout.tsx` | title template, description, referrer e lang | Adicionar metadataBase validada; preservar template, referrer e layout; não definir canonical global |
| `apps/web/src/app/(public)/produtos/page.tsx` | Qualquer query recebe noindex; fetch listagem/facetas com Promise.allSettled | Aplicar matriz e categoria contextual; preservar degradação independente de facetas, filtros e paginação |
| `apps/web/src/app/(public)/categorias/page.tsx` | Facetas viram links de query; metadata estática | Compartilhamento/indexação conforme resultado; preservar links atuais de categoria/ocasião/modalidade |
| `apps/web/src/app/(public)/buscar/page.tsx` | Só busca parametrizada recebe noindex; estados e ranking já prontos | noindex em todos os estados, metadata editorial sem q; preservar integralmente busca/GET/agrupamentos/handoff |
| `apps/web/src/app/(public)/produtos/[slug]/page.tsx` | Fetch em metadata e corpo; detail herda noindex; erros metadata usam fallback genérico | Resultado coerente por requisição, OG/canonical/JSON-LD; preservar notFound, indisponibilidade, preço e safeReturnHref |
| `apps/web/src/bff/catalogApi.ts` | Fetch/parser/href públicos; produtos e facetas validados | Reutilizar, acrescentar projeção sitemap via módulo dedicado se útil; não mudar contratos antigos |
| `apps/web/src/bff/catalogValidation.ts` | Allowlists e imagem local real, sem hosts remotos | Exigir critérios esperados/consistência semântica de listagem e identidade de slug; preservar fail-closed |
| `apps/web/src/bff/catalogTransport.ts` | Timeout 5 s, no-store, erros sanitizados | Preservar clientes antigos; adapter sitemap limita bytes e distingue status conforme matriz HTTP |
| `apps/web/src/i18n/publicContent.ts` e `.types.ts` | Metadados editoriais tipados; detalhe tem robots noindex | Adicionar textos SEO/estados e tipo correspondente; preservar copy comercial e UTF-8 |
| `apps/web/src/features/public-store/publicLayoutContent.ts` | Exports centrais de conteúdo | Exportar conteúdo adicional se necessário; preservar imports consumidores |
| `apps/web/package.json` | test:bff executa apenas catalog-validation.test.mjs | Incluir teste SEO em comando executável; não adicionar biblioteca |
| `apps/web/.env.example`, `apps/web/playwright.config.ts`, `.github/workflows/smoke.yml` | Só API_INTERNAL_URL configurada no web | Adicionar SITE_URL e SEO_INDEXING_ENABLED no build e runtime/servidor Playwright usando origem loopback de teste; nenhum domínio de produção inventado |
| `.github/workflows/frontend.yml` | npm ci, lint, typecheck e build sem SITE_URL | Configurar origem de teste e matriz SEO_INDEXING_ENABLED no build/runtime e executar teste SEO/BFF; preservar os gates existentes |
| `apps/web/next.config.ts` | distDir configurável e strict mode | Header noindex/nofollow global quando SEO desabilitado; ajustar bots somente se testes exigirem, preservando os padrões |
| `README.md` | Descreve fundação/catálogo e instruções locais, com trechos anteriores à 2.3 | Documentar SITE_URL e sitemap/política SEO entregue; atualizar somente trechos diretamente afetados e preservar setup |

Novos módulos sugeridos: `apps/web/src/features/catalog-seo/{siteUrl,catalogMetadata,catalogStructuredData}.ts`, serializador XML/serviço sitemap no mesmo módulo, `apps/web/src/bff/catalogSitemapApi.ts`, handlers citados e testes SEO. Fallback em `apps/web/public/` ou gerador editorial estático com `next/og`; sem imagens IA obrigatórias.

Backend: caminhos abaixo relativos a `apps/api/app/Modules/Catalog`, exceto rotas/contrato. Arquivos existentes foram inspecionados na contextualização.

| Arquivo | Hoje | Mudança/preservação |
| --- | --- | --- |
| `Application/Queries/PublicCatalogQuery.php` | Porta de listagem, facetas e produto publicado | Acrescentar `sitemapPage(int $page): PublicCatalogSitemapPage`; preservar assinaturas atuais |
| `Infrastructure/Persistence/PostgresPublicCatalogQuery.php` | `publishedQuery()` e `withStatementTimeout()` reutilizáveis; hidratação de imagem/taxonomia na listagem | Projeção somente de `p.slug`, ordenação `p.id`, lote 500 e total coerente; reutilizar predicado published e timeout, sem `hydrate()` ou resolver de imagens |
| `apps/api/routes/api.php` | Grupo público no-store com throttle | Adicionar `/catalog/sitemap` e `/catalog/sitemap-facets` com no-store e somente throttle dedicado; preservar grupos de busca, produtos e admin |
| `apps/api/app/Providers/AppServiceProvider.php` | Binding de query e limiters normais | Preservar binding; registrar public-catalog-sitemap com namespace separado |
| `apps/api/config/catalog.php` e `apps/api/.env.example` | Limites de catálogo/busca | Documentar CATALOG_SITEMAP_RATE_LIMIT_PER_MINUTE, default 60, inteiro 1..240; parsing estrito e fallback conservador 60 |
| `packages/contracts/catalog-public-v1.openapi.yaml` | Schemas fechados de catálogo/busca | Acrescentar endpoint/schema sitemap e respostas 200/404/422/429/503; não alterar contratos anteriores |

Novos: DTO `PublicCatalogSitemapPage`, caso de uso `ListPublicCatalogSitemapProducts`, Request/controller/Resource em `Interfaces/Http`, projeção HTTP sitemap-facets e teste `apps/api/tests/Feature/Catalog/PublicCatalogSitemapApiTest.php`. `AppServiceProvider.php` já vincula a porta à implementação: manter binding e adicionar limiter dedicado. Reutilizar timeout de leitura, mas não a cota normal. Não reaproveitar o limite de 48 itens de `PublicCatalogFilters` como limite de entrada do contrato sitemap. `count(*) over()` mantém total/linhas da mesma leitura, condicionado ao benchmark; fallback de contagem para página vazia deve preservar consistência e distinguir catálogo vazio de página inexistente.

### Arquitetura e versões

Next.js 16.3.5, React/React DOM 19.2.0, TypeScript 5.9.x strict, Node 24.x; Laravel 13/PHP 8.5/PostgreSQL 18/Redis 8; Playwright 1.60.0. Locks e manifests prevalecem. `apps/web/AGENTS.md` exige consultar docs instaladas em `node_modules/next/dist/docs/` antes de codificar. Next/eslint-config-next foram atualizados para 16.3.5 na remediação autorizada do gate; sharp 0.35.4, js-yaml 4.3.2 e Predis 3.6.0 estão nos locks revalidados. Não atualizar novamente dependências fora de necessidade aprovada.

Navegador/crawler → Next SSR/BFF → Laravel `/api/v1` → PostgreSQL. Elegibilidade/publicação continuam no Laravel; Next só projeta informação validada. Controllers finos, portas/casos de uso em Application, queries em Infrastructure, DTOs/Resources explícitos. Nenhum acesso direto do Next ao banco. Manter `AdminIdentityResolver`, `FileReferenceValidator` e resolução de imagens fail-closed.

### Segurança e STRIDE

Superfícies: slug/query/headers públicos, origem configurada, payload upstream, XML, tags HTML/JSON-LD, imagens e logs. Não há nova autenticação, upload, pagamento ou coleta de PII.

| Categoria | Ameaça | Controle exigido e prova |
| --- | --- | --- |
| Spoofing | Host/forwarded host falsifica canonical e imagem | Origem fixa validada em SITE_URL, ignorar headers; teste com origem atacante |
| Tampering | Query/slug ou texto encerra script/XML | Schemas, bounds, queries parametrizadas, Metadata API, JSON-LD escapado e escape XML; fixtures hostis |
| Repudiation | Crawl abusivo sem diagnóstico seguro | Status e correlação técnica minimizada; não logar q, return_to, headers sensíveis ou payload |
| Information Disclosure | SEO enumera drafts/direitos/PII ou expõe API interna | Projeção published-only/allowlist, sem metadata de erro comercial, imagens públicas e no-store; testes negativos |
| Denial of Service | Índice percorre milhões de detalhes ou parâmetros ampliam consulta | 500 itens/lote, 10000 lotes, teto editorial, throttle, timeout, COUNT+SELECT sem N+1; testes de limites |
| Elevation of Privilege | Sitemap usa leitura admin ou storage privilegiado | Porta pública read-only, middleware admin preservado; nenhum segredo/credencial no contrato |

Segredos apenas em configuração apropriada; nunca copiar `.env` real para documentação ou fixtures. SITE_URL é configuração pública não secreta, mas só usada pelo construtor server-side. `robots` e `noindex` não são controles de acesso. Não introduzir `dangerouslySetInnerHTML` fora do serializador JSON-LD auditado, nem fetch de imagem arbitrária/SSRF. Preservar proteção de referrer da busca.

### Aprendizados da story anterior e Git

- 2.3 está done após revisão e rerun: validação semântica entre query e resposta foi indispensável; não confiar apenas no shape para indexar conteúdo.
- Acentos/Unicode, página fora do intervalo, sugestões não comprovadas e retorno do detalhe já têm testes; não simplificá-los para encaixar SEO.
- Produção mantém imagem fail-closed: fixture SVG E2E não é automaticamente imagem social compatível. Testar fallback PNG/JPEG real e não alterar o resolver para permitir caminhos privados.
- Histórico: merge `140887a` integra `345bb17` (2.3); `ed2ae84` integra 2.2; `e1ef326` alinha smoke CI; `6046a58` implementa 2.2. O CI constrói antes de iniciar API Playwright: não exigir API disponível no build para gerar sitemap completo.
- Árvore estava limpa na descoberta. Esta criação altera apenas artefatos de planejamento/revisão.

### Testes e gates de implementação

Unidade: matriz canonical/robots, categoria conhecida vs query arbitrária, Unicode, origem inválida, normalização page=1, escape JSON-LD/XML e envelope sitemap. HTTP PostgreSQL: published-only, retirada, 501+ produtos distribuídos sem perda em catálogo estável, bounds, campos exatos, throttle, falhas e query budget. E2E: produto/category/busca/listagem, headers de bot, HTML sem JS, asset social com MIME correto, XML parseável, links absolutos, ausência de q/return_to e regressões de navegação.

Executar em `apps/web`: teste SEO, `npm run test:bff`, `npm run lint`, `npm run typecheck`, `npm run build`, `npm run test:e2e`, `npm audit --audit-level=moderate`. Em `apps/api`: `composer test`, `vendor/bin/pint --test`, `composer audit --locked`. Raiz: `git diff --check`. SAST/secret-scan se disponíveis; registrar ausências e achados, sem tratá-los como aprovação. Tests locais de CWV são evidência de laboratório, não dados de campo. Não declarar prévia real em rede social validada só porque as tags passaram: testes comprovam o contrato HTML/imagem.

### Referências e informação técnica consultada em 2026-09-17

- [Source: _bmad-output/planning-artifacts/epics.md#Story 2.4]
- [Source: _bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md#FR-3] e NFR-2/NFR-10.
- [Source: _bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/addendum.md#3.10] e 3.11.
- [Source: _bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md] AD1/2, AD9, AD11, AD14/15.
- [Source: _bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md] e DESIGN.md: modalidades, estados, acessibilidade e navegação.
- [Source: _bmad-output/implementation-artifacts/2-3-implementar-busca-publica-por-intencao.md] e _bmad-output/project-context.md.
- Docs locais: `apps/web/node_modules/next/dist/docs/01-app/01-getting-started/14-metadata-and-og-images.md` e referências de Metadata/sitemap/robots/JSON-LD.
- [Next Metadata API](https://nextjs.org/docs/app/api-reference/functions/generate-metadata): metadataBase, composição e streaming; filhos precisam preservar campos OG necessários ao sobrescrever objetos.
- [Next JSON-LD](https://nextjs.org/docs/app/guides/json-ld): serialização com escape de `<`.
- [Google: paginação](https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading): páginas da sequência têm canonical próprio.
- [Google: URLs de e-commerce](https://developers.google.com/search/docs/specialty/ecommerce/designing-a-url-structure-for-ecommerce-sites): URLs estáveis, canonical próprio e links navegáveis.
- [Protocolo Sitemaps](https://www.sitemaps.org/protocol.html): escopo por localização do documento, escape XML e limites de tamanho/entradas.

## Security Gate - bmad-review-security

- Revisão inicial e revalidação após remediação: 2026-09-17.
- Relatório: `reviews/review-2-4-preparar-paginas-publicas-de-catalogo-para-seo-e-compartilhamento-security.md`, seção de revalidação vigente.
- Revalidação pós-code-review: 2026-09-21.
- Relatório pós-code-review: `reviews/review-2-4-code-review-security-postpatch.md`.
- Resultado pós-code-review: **Aprovado**. Sem alto risco confirmado; médio M01 e baixo L01 resolvidos em 2026-09-22 com Semgrep/Gitleaks versionados e suite HTTP de sitemap executados com sucesso.
- Retentativa inicial em 2026-09-22: Docker daemon Linux continuou indisponivel, `node scripts/scan-sast.mjs` e `node scripts/scan-secrets.mjs` falharam antes do scan, e `127.0.0.1:5432` retornou `TcpTestSucceeded: False`.
- Fechamento em 2026-09-22: Docker Desktop iniciado; `node scripts/scan-sast.mjs` passou com 247 regras em 283 arquivos e 0 achados, `node scripts/scan-secrets.mjs` passou com 82.60 MB escaneados e sem leaks, e `php artisan test --filter PublicCatalogSitemapApiTest` passou com 7 testes e 83 assertions.
- Resultado: **Aprovado com ressalvas para ready-for-dev**.
- H01/H02/M01/M02 corrigidos nos manifests/locks e instalação: Next/eslint-config-next 16.3.5, sharp 0.35.4, js-yaml 4.3.2, Predis 3.6.0.
- Nenhum alto/médio aberto e nenhum aceite de vulnerabilidade presumido. Audits npm/Composer sem vulnerabilidades conhecidas.
- Regressões: 11 testes BFF, 128 PHP (551 assertions), 55 Playwright; lint, typecheck, build e Pint passaram.

### Condições para Desenvolvimento

- Pronta para implementação. Funcionalidades SEO ainda não implementadas; manter tarefas abertas até haver código e evidência.
- A remediação autorizada alterou package.json/package-lock.json web e composer.lock API; gates e versões atuais prevalecem sobre o histórico inicial.
- Baixos L01/L02/L03 permanecem rastreados: Dev/DevOps para scanners/ignore; Sharom/DevOps para políticas e privilégios. Respeitar checkpoints de implementação/pré-produção e não interpretar ausência de scanner como aprovação.

### Evidência Exigida na Implementação

- Testes de published-only/retirada, isolamento admin, escape JSON-LD/XML, Host poisoning, URLs e paths, bounds/throttle e erros sanitizados.
- Verificar ausência de PII/segredos em HTML/XML/metadados/logs, executar SAST/SCA/secret scan disponíveis e registrar limitações reais.

## Story Review Gate - bmad-review-adversarial-general

- Revisão executada por solicitação explícita do usuário sobre a especificação, em 2026-09-17.
- Relatório: `reviews/review-2-4-preparar-paginas-publicas-de-catalogo-para-seo-e-compartilhamento-adversarial.md`.
- Resultado: 12 achados corrigidos na especificação por solicitação do usuário, com regras e testes verificáveis; nenhuma pendência da revisão geral documental. Não constitui aprovação da implementação. O bloqueio anterior de segurança foi resolvido pela remediação separada acima.

## Dev Agent Record

### Agent Model Used

Codex (criação de contexto).

### Debug Log References

- 2026-09-17 (dev): resolver Python indisponível; customização resolvida manualmente (base + override de equipe). Sem activation prepend/append. Revisão adversarial obrigatória ao final. Baseline preservado. Alterações prévias de dependências/configuração não foram revertidas.
- 2026-09-17 (dev): ciclos red/green observados para configuração SITE_URL, validação de identidade, serializador JSON-LD e endpoints HTTP de sitemap. Testes detalhados em `reviews/review-2-4-implementation.md`; benchmark em `reviews/benchmark-2-4-sitemap.md`.

- 2026-09-17: skill/config/customização padrão, contexto persistente, fontes de planejamento, story 2.3, código, Git e docs técnicas analisados. Resolver Python indisponível; fallback manual aplicado. Nenhum override de create-story encontrado; activation prepend/append e on_complete vazios.

### Completion Notes List

- Desenvolvimento e remediação concluídos em 2026-09-17; story/sprint em review. ACs 1–7 e checklist DoD conferidos; nenhum achado High/Medium/Low aberto nas revisões de implementação e segurança. Revisão de código independente e deploy não foram executados.
- SEO SSR em pt-BR, canonical/indexação global, Open Graph/Twitter, categoria contextual, JSON-LD escapado, memoização por requisição e PNG editorial estático implementados. Sitemap/robots usam projeção mínima, lotes limitados, validação de identidade, limites de bytes/deadline e matriz HTTP sanitizada.
- IMP-01 resolvido sob a autorização “corrija tudo”: preservado o contrato vazio legado (total zero, sem itens, página 1/per_page 12) com filtros exatos e labels sem chaves extras. Essa exceção explícita não relaxa parsers nem permite produtos de outra consulta ou categoria indexável sem itens; supera a ambiguidade registrada na etapa anterior.
- Segurança: identidade de socket assinada pelo servidor Node e validada antes do throttle; imagem gerada no build; papéis runtime/migrator separados, efetivamente provisionados localmente, sem superuser; CI usa os mesmos papéis. Scanners, políticas de agentes e ignore de credenciais/exportações versionados. Segredos gerados somente em arquivos ignorados.
- Gates finais: 65 E2E com SEO true e 65 com false; 10 unitários SEO e 11 BFF; 135 testes PHP/619 assertions, mais benchmark separado com 10 assertions; builds true/false, lint, typecheck, Pint e diff check aprovados. Startup dev também respondeu HTTP 200 com noindex. Métricas de interação mantiveram os limites originais, com execução final sem scanner concorrente.
- Benchmark de 100 mil produtos: 33,07 ms no primeiro lote e 90,44 ms no último, uma consulta por lote. Não extrapolar esse resultado para o teto contratual de cinco milhões sem nova medição.
- Semgrep: 247 regras/283 arquivos, zero achados finais, com quatro supressões locais justificadas. Gitleaks: workspace Git-visível e 16 commits aprovados após exceções pontuais de documentação; npm/Composer audit sem advisories. Gates CI versionados, sem alegar execução remota nesta tarefa.
- Mudanças prévias de dependências, locks e contexto foram preservadas; não houve atualização de dependências durante esta implementação. Sem commit/push/deploy. Políticas da IDE/sandbox e configuração de produção estão fora do alcance da evidência local.

### File List

- `.github/workflows/backend.yml`
- `.github/workflows/frontend.yml`
- `.github/workflows/security.yml`
- `.github/workflows/smoke.yml`
- `.gitignore`
- `.gitleaks.toml`
- `AGENTS.md`
- `README.md`
- `_bmad-output/implementation-artifacts/2-4-preparar-paginas-publicas-de-catalogo-para-seo-e-compartilhamento.md`
- `_bmad-output/implementation-artifacts/reviews/benchmark-2-4-sitemap.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-4-implementation.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-4-preparar-paginas-publicas-de-catalogo-para-seo-e-compartilhamento-adversarial.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-4-preparar-paginas-publicas-de-catalogo-para-seo-e-compartilhamento-security.md`
- `_bmad-output/implementation-artifacts/reviews/review-2-4-security-remediation.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `_bmad-output/project-context.md`
- `apps/api/.env.example`
- `apps/api/app/Modules/Catalog/Application/Queries/ListPublicCatalogSitemapProducts.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogQuery.php`
- `apps/api/app/Modules/Catalog/Application/Queries/PublicCatalogSitemapPage.php`
- `apps/api/app/Modules/Catalog/Infrastructure/Persistence/PostgresPublicCatalogQuery.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Controllers/PublicCatalogSitemapController.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Middleware/SitemapClientIdentity.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Requests/CatalogSitemapRequest.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogSitemapFacetsResource.php`
- `apps/api/app/Modules/Catalog/Interfaces/Http/Resources/PublicCatalogSitemapResource.php`
- `apps/api/app/Providers/AppServiceProvider.php`
- `apps/api/bootstrap/app.php`
- `apps/api/composer.lock`
- `apps/api/config/catalog.php`
- `apps/api/phpunit.xml`
- `apps/api/routes/api.php`
- `apps/api/scripts/provision-local-security.php`
- `apps/api/tests/Feature/Catalog/CatalogSitemapBenchmarkTest.php`
- `apps/api/tests/Feature/Catalog/PublicCatalogApiTest.php`
- `apps/api/tests/Feature/Catalog/PublicCatalogSitemapApiTest.php`
- `apps/api/tests/Feature/RuntimeDatabasePrivilegesTest.php`
- `apps/web/.env.example`
- `apps/web/next-env.d.ts`
- `apps/web/next.config.ts`
- `apps/web/package-lock.json`
- `apps/web/package.json`
- `apps/web/playwright.config.ts`
- `apps/web/scripts/server.mjs`
- `apps/web/src/app/(public)/buscar/page.tsx`
- `apps/web/src/app/(public)/categorias/page.tsx`
- `apps/web/src/app/(public)/produtos/[slug]/page.tsx`
- `apps/web/src/app/(public)/produtos/page.tsx`
- `apps/web/src/app/catalog-social.png/route.tsx`
- `apps/web/src/app/layout.tsx`
- `apps/web/src/app/robots.ts`
- `apps/web/src/app/sitemap-catalogo.xml/route.ts`
- `apps/web/src/app/sitemap-editorial.xml/route.ts`
- `apps/web/src/app/sitemap.xml/route.ts`
- `apps/web/src/bff/catalogApi.ts`
- `apps/web/src/bff/catalogParams.ts`
- `apps/web/src/bff/catalogSitemapApi.ts`
- `apps/web/src/bff/catalogSitemapTransport.ts`
- `apps/web/src/bff/catalogSitemapValidation.ts`
- `apps/web/src/bff/catalogValidation.ts`
- `apps/web/src/features/catalog-seo/StructuredData.tsx`
- `apps/web/src/features/catalog-seo/catalogMetadata.ts`
- `apps/web/src/features/catalog-seo/catalogPolicy.ts`
- `apps/web/src/features/catalog-seo/catalogReads.ts`
- `apps/web/src/features/catalog-seo/catalogStructuredData.ts`
- `apps/web/src/features/catalog-seo/detailQuery.ts`
- `apps/web/src/features/catalog-seo/seoConfig.ts`
- `apps/web/src/features/catalog-seo/siteUrl.ts`
- `apps/web/src/features/catalog-seo/sitemapHandler.ts`
- `apps/web/src/features/catalog-seo/sitemapIdentity.ts`
- `apps/web/src/features/catalog-seo/sitemapXml.ts`
- `apps/web/src/features/catalog-seo/socialImage.ts`
- `apps/web/src/i18n/catalogSeoContent.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/tests/e2e/catalog-listing.spec.ts`
- `apps/web/tests/e2e/catalog-search.spec.ts`
- `apps/web/tests/e2e/catalog-seo-upstream.spec.ts`
- `apps/web/tests/e2e/catalog-seo.spec.ts`
- `apps/web/tests/e2e/database-env.ts`
- `apps/web/tests/e2e/foundation.spec.ts`
- `apps/web/tests/e2e/global-setup.ts`
- `apps/web/tests/unit/catalog-seo.test.mjs`
- `apps/web/tsconfig.json`
- `packages/contracts/catalog-public-v1.openapi.yaml`
- `scripts/scan-sast.mjs`
- `scripts/scan-secrets.mjs`
- `scripts/stage-security-source.mjs`

A lista inclui arquivos previamente modificados e preservados (locks, contexto, metadados/configuração de ferramentas), para rastreabilidade do workspace; não atribui essas alterações à implementação atual.

## Change Log

- 2026-09-17: criada especificação de SEO/compartilhamento, canonical/indexação, JSON-LD e sitemap por lotes.
- 2026-09-17: revisão adversarial geral executada; 12 achados documentados, sem alteração de implementação ou transição de status.
- 2026-09-17: corrigidos os 12 achados documentais: identidade/queries, memoização, benchmark, isolamento de crawl, limites upstream, HTTP, ambientes, bots, streaming e integridade do índice/editorial.

- 2026-09-17: dependências corrigidas, audits e regressões aprovados; gate revalidado e story/sprint atualizadas para ready-for-dev.

- 2026-09-17: implementação SEO/sitemap e testes adicionados; benchmark aprovado e revisão de implementação registrada. Story permanece in-progress devido a IMP-01 e regressão completa pendente.

- 2026-09-17: concluída remediação autorizada de todos os achados de implementação/segurança; regressões completas e scanners aprovados, checklist final marcado e story/sprint promovidas a review.
