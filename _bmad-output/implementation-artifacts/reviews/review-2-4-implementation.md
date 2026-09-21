# Revisão adversarial da implementação 2.4

Data: 2026-09-17. Skill: `bmad-review-adversarial-general`; formato com IDs/severidade conforme customização local de `bmad-dev-story`. Revisão local do diff, arquivos novos e evidências executadas, sem agente independente. O relatório documental anterior permanece separado.

Resultado final: **aprovado para revisão**. Todos os achados High, Medium e Low desta revisão foram tratados. A revalidação de segurança está em `review-2-4-security-remediation.md`.

| ID | Severidade | Evidência e problema | Recomendação / tratamento |
| --- | --- | --- | --- |
| IMP-01 | High | A API normaliza coleções vazias para página 1 e omite labels desconhecidas; a validação nova convertia vazio em indisponibilidade. | **Corrigido:** exceção estrita somente para total zero/dados vazios/página e limite legados, mantendo filtros exatos e rejeitando labels extras. Não permite produtos nem identidade indexável de categoria. Testes negativos e regressões completas true/false aprovados; autorizado pelo pedido de corrigir tudo. |
| IMP-02 | Medium | Validação somente de `URL.pathname` aceitava `https://example.com/a/..` após normalização. | Corrigido: sintaxe de origem também validada antes de `new URL`; teste negativo incluído. |
| IMP-03 | Medium | Retorno do detalhe aceitava caminho normalizado e percent-escape malformado. | Corrigido: prefixos exatos `/produtos`/`/buscar`, controles, fragmentos, duplicatas e escapes inválidos rejeitados; testes unitários. |
| IMP-04 | Medium | Índice de categorias ignorava query desconhecida para indexação. | Corrigido: query presente impede canonical e indexação positiva; corpo continua disponível. |
| IMP-05 | Medium | Combinação categoria + modalidade/ocasião herdava identidade específica de categoria, embora a matriz exija compartilhamento genérico. | Corrigido: apresentação específica somente na categoria isolada elegível. |
| IMP-06 | Medium | Extensão PNG/JPEG sozinha não comprova formato da imagem social. | Corrigido: assinatura de bytes verificada em arquivo público validado; erro/ausência/incompatibilidade usa fallback editorial. |
| IMP-07 | Medium | Labels de modalidade divergentes poderiam passar pela validação semântica. | Corrigido: comparação com vocabulário público fechado, além da modalidade dos itens; teste negativo. |
| IMP-08 | Low | Fallback inicial usava paleta distinta da identidade existente em `globals.css`. | Corrigido: cores `#f8f5f1`, `#2d2d2d`, `#c8a46b`, `#765321`, identidade textual JS Designs, PNG 1200×630. |
| IMP-09 | Medium | Servidores isolados dos testes anteriores não definiam a nova origem obrigatória quando executados sem variáveis no processo pai. | Corrigido: SITE_URL e modo de indexação explícitos nos servidores isolados de listagem/busca. |
| IMP-10 | Low | Documentação escrita via pipe PowerShell perdeu acentos em README e `.env.example`. | Corrigido: trechos regravados em UTF-8; conteúdo público em português preservado. |
| IMP-11 | Low | Benchmark estava executável localmente, mas não tinha gate dedicado no CI. | Corrigido: workflow backend executa benchmark de 100 mil produtos com EXPLAIN da consulta real. |
| IMP-12 | Medium | Medições locais sob carga atingiram 1026/1004 ms contra limite de 1000 ms. | **Tratado:** regressões completas finais com um worker e sem scanner concorrente passaram nos dois modos, sem ampliar limite ou remover verificação. Teste continua sujeito à carga do host e não é medição de campo. |

## Histórico anterior à remediação (superado pelos resultados finais)

Evidência até esta revisão: builds nos modos true/false; nove testes SEO/HTTP/sem JS nos dois modos; oito testes unitários SEO, onze BFF; benchmark com dez assertions. A execução completa Playwright teve 59 aprovados e duas falhas, sendo a de desempenho aprovada no rerun isolado. Não declarar a suíte completa verde nem promover a story a review.

SCA: `npm audit --audit-level=moderate` e `composer audit --locked` sem vulnerabilidades conhecidas. Semgrep, Gitleaks e Trivy não encontrados no PATH; não houve aprovação SAST/secret-scan. Inspeção manual e testes de allowlist/erros sanitizados não substituem essas ferramentas. Locks alterados antes da implementação foram preservados.

Gate backend final deste trecho: `composer test` com 133 aprovados (601 assertions), um benchmark opt-in omitido na execução comum e aprovado separadamente com dez assertions; Pint aprovado. `git diff --check`, lint e typecheck aprovados. Não foram criados commits nem realizado deploy.

## Revalidação final — 2026-09-17

- 65 E2E aprovados com SEO true e 65 com false; inclui filtros, vazio, busca, retorno, bots, HTML sem JavaScript, responsividade e limites de desempenho originais. Novo teste de ingresso rejeita headers forjados.
- 135 testes PHP/619 assertions; benchmark opt-in aprovado separadamente (10 assertions, uma consulta/lote, 33,07/90,44 ms).
- 10 unitários SEO + 11 BFF, builds nos dois modos, lint, typecheck, Pint e git diff --check aprovados.
- Semgrep: 247 regras em 283 arquivos, zero achados após revisão de quatro falsos positivos com supressões pontuais. Gitleaks atual e 16 commits: zero segredos confirmados após exceções restritas de documentação. npm/Composer audit sem advisories.
- Revisão adicional encontrou teste antigo da home incompatível com o modo global false; corrigido para exigir noindex nesse modo. Cotas por cliente assinadas, imagem estática, banco com privilégio mínimo e políticas/scanners versionados revalidados.
- DoD: contexto, ACs, tarefas, testes, edge cases, documentação e rastreamento conferidos. Desenvolvimento concluído; story/sprint em review. Nenhum commit, push ou deploy.
