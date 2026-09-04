# Revisão adversarial da implementação — Story 2.3

- O validador BFF aceita `data.intent.preserved_term` diferente de `meta.query`, permitindo que a interface apresente ou encaminhe contexto que não corresponde à busca executada.
- O validador BFF não confirma que cada card de um grupo exato pertence à categoria declarada pelo próprio grupo, portanto um payload upstream inconsistente pode produzir agrupamento público falso.
- O validador BFF confere apenas o total visível da página e não prova que a quantidade de itens em `exact_groups` e `similar` corresponde à fatia matemática de `total_exact` e `total_similar`.
- Uma sugestão pode expor um `label` e apontar para outro valor em `q`; a allowlist de origem/caminho está correta, mas o vínculo semântico não é validado.
- O handoff de convite pode preservar no fragmento um termo diferente de `preserved_term`, e o contrato BFF também aceita combinações incoerentes entre `intent.type` e presença de `handoff_href`.
- O teste de índice executa um predicado isolado equivalente, mas não captura nem explica o SQL completo realmente usado pelo endpoint; isso deixa a alegação de uso do índice mais forte que a evidência.
- A degradação de sugestões foi implementada em transação separada, porém não há teste que force a falha complementar e prove que resultados principais continuam disponíveis.
- Os defaults e ranges de configuração estão implementados, mas a suíte não demonstra explicitamente o fallback conservador para valores de ambiente inválidos.
- Cards dentro de grupos exatos usam `h2` abaixo de um título de grupo `h3`, e cards semelhantes também usam `h2` sob o título da seção; a hierarquia de headings não representa a estrutura visual.
- A inspeção de query bruta ignora segmentos vazios como `&&`, prefixo `&` ou sufixo `&`, aceitando formas não canônicas que deveriam ser rejeitadas cedo pela fronteira estrita.
- O teste E2E de upstream quebrado inicia outro `next dev` com `NEXT_DIST_DIR` alternativo e deixa `next-env.d.ts`/`tsconfig.json` modificados após a execução, sujando a árvore de trabalho.
- O OpenAPI expressa limite de caracteres para `q`, mas não documenta claramente o teto adicional de 512 bytes UTF-8, os controles/invisíveis proibidos e a política `Cache-Control: no-store, private` das respostas.
