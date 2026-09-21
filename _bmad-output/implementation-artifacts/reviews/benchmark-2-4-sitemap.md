# Benchmark do sitemap — 2026-09-17

Comando: `CATALOG_SITEMAP_BENCHMARK=1 php artisan test --compact --filter=CatalogSitemapBenchmarkTest`, em `apps/api`.

Fixture transacional: 100.000 produtos publicados em uma categoria. PostgreSQL 18.4, Docker local Linux x86_64, host Windows com AMD Ryzen 5 1600 (12 processadores lógicos), aproximadamente 16 GiB de RAM física. Planner e índices normais, `ANALYZE` após carga; nenhum `enable_seqscan=off` ou timeout ampliado. Execução em banco de testes com rollback.

| Lote | Itens | Total | Consultas de dados | Chamada PHP | EXPLAIN execution | Buffers shared hit |
| --- | ---: | ---: | ---: | ---: | ---: | ---: |
| 1 | 500 | 100.000 | 1 | 36,42 ms | 29,76 ms | 3.009 |
| 200 | 500 | 100.000 | 1 | 94,23 ms | 107,91 ms | 103.008 |

`EXPLAIN (ANALYZE, BUFFERS)` foi aplicado ao SQL capturado da chamada real. Plano: agregado paralelo sobre produtos publicados, join com categorias; `LEFT JOIN` ao lote ordenado por `p.id`, usando `catalog_products_pkey`, `LIMIT 500 OFFSET N`. O total e as linhas usam o mesmo snapshot da instrução, inclusive página vazia. Nenhuma hidratação ou consulta a imagens/taxonomias. O teste imprime o plano integral para reprodução.

Resultado: 10 assertions passaram; ambas as chamadas abaixo de 3.000 ms e de duas consultas de dados. O lote final percorre mais linhas por causa do OFFSET: esta medição não prova trabalho constante. O maior volume medido é 100.000, não cinco milhões. O teto de cinco milhões é apenas contratual; uma expansão operacional exige novo benchmark com distribuição e hardware representativos.
# Revalidação após separar permissões de banco (2026-09-17)

Benchmark executado novamente como `jsdesign_migrator` (sem superuser), com 100 mil produtos: primeiro lote **33,07 ms**, último lote (página 200) **90,44 ms**, **1 consulta de dados em cada lote**. Teste aprovado com 10 assertions. O runtime foi validado separadamente com SELECT permitido e DDL negado. O plano/resultado completo local está em `security-reports/benchmark-final.log` (ignorado); as evidências abaixo preservam a primeira medição.
