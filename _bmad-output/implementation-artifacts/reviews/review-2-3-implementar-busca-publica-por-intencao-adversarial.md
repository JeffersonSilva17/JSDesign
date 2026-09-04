# Revisão Adversarial Geral — Story 2.3

Artefato: `_bmad-output/implementation-artifacts/2-3-implementar-busca-publica-por-intencao.md`  
Data: 2026-08-26  
Contexto adicional: PRD FR-6/FR-10, UX, arquitetura Laravel/BFF, Stories 2.1/2.2, código atual, segurança, Git e documentação oficial.

## Achados

1. “Exato” ainda estava semanticamente ambíguo: igualdade normalizada, prefixo e termo completo poderiam ser implementados de maneiras incompatíveis. A story precisa separar `exact`, `prefix` e `fuzzy`, definir que o maior score por produto vence e manter a classe fora do payload do card.
2. A paginação sobre uma sequência combinada de exatos e semelhantes não definia página fora do intervalo, ordem dos grupos nem comportamento quando uma página cruza a fronteira entre classes. Isso permitiria envelopes diferentes entre implementações igualmente “válidas”.
3. A intenção `invitation` não possuía regra verificável. Inferir por qualquer tema ou por texto livre no React produziria falsos positivos e violaria o domínio do Laravel.
4. “Preservar o termo para o futuro pré-formulário” não era testável porque esse pré-formulário pertence ao Epic 3/Story 7.3 e não existe. A entrega atual precisa limitar-se a um contexto seguro no contrato/href, sem alegar persistência em uma superfície inexistente.
5. Sugestões não tinham quantidade máxima, deduplicação, ordenação nem restrição formal de href same-origin. Um adapter poderia devolver lista ilimitada, personagem/alias ou URL externa.
6. O limite de 120 caracteres não cobria bytes, controles Unicode, normalização pós-expansão ou caracteres invisíveis. Isso deixava bypasses de custo/validação e comportamento divergente entre Next/PHP/PostgreSQL.
7. “Limiter dedicado ou endurecido” mantinha uma decisão operacional em aberto. Busca fuzzy é mais cara que listagem e não pode herdar silenciosamente `240/min` sem evidência.
8. A orientação para indexar `unaccent` ignorava a armadilha de volatilidade/dicionário em expression indexes. Um dev poderia criar índice inválido, wrapper inseguro ou índice que não corresponde à query.
9. O filtro de direitos precisava ocorrer antes de score/candidatos, não só antes do payload. Caso contrário, tempo, contagem, paginação ou sugestões poderiam funcionar como canal lateral de personagem não verificado.
10. Reutilizar `PublicCatalogProductResource` não impedia duplicação da hidratação privada existente. Uma segunda implementação de imagens/taxonomia poderia divergir da allowlist e reintroduzir N+1.
11. A fronteira SEO não estava fechada. A Story 2.4 é dona de canonical/OG/indexação; a nova `/buscar?q=` poderia ser indexada acidentalmente antes dessa decisão.
12. NFR-2 foi citado, mas não havia critério/evidência de desempenho percebido para a nova rota. Build verde não prova que busca SSR e fontes/mídia não degradam CWV.
13. Configurações novas eram mencionadas genericamente, sem lista de variáveis, ranges ou comportamento para valores inválidos. Isso permite defaults inseguros ou divergentes por ambiente.
14. A story não exigia falha explícita quando `CREATE EXTENSION` não fosse permitido. Um fallback silencioso poderia entregar busca sem aproximação enquanto os ACs seriam marcados como concluídos.
15. O contrato não tornava obrigatório que totais, grupos e itens fossem matematicamente consistentes e sem IDs duplicados. O BFF poderia aceitar payload “bem tipado” porém contraditório.
16. Os testes não explicitavam timing/contagem indistinguíveis para termos protegidos não verificados, nem o cruzamento de página entre exatos e semelhantes, os dois pontos com maior risco de implementação sutilmente incorreta.

## Correções aplicadas

- Fechadas classes de match, desempate, agrupamento e paginação combinada.
- Definida classificação de convite somente no Laravel por vocabulário/taxonomia configurada e testada.
- Limitado o handoff futuro a contexto minimizado/same-origin, sem alegação de pré-formulário entregue.
- Fechado contrato de sugestões e consistência matemática do envelope.
- Reforçada validação Unicode/bytes/invisíveis nas duas fronteiras.
- Fixado default inicial do limiter em 60/min por chave não reversível, configurável apenas dentro de range seguro e sujeito a load test.
- Exigida estratégia indexável comprovada; proibido assumir índice direto sobre expressão não imutável de `unaccent`.
- Tornado obrigatório filtrar direitos antes de candidatos/score/contagem/sugestões.
- Exigido extrair/reutilizar projeção/hidratação, sem cópia paralela.
- Marcada busca parametrizada como `noindex,follow` até a Story 2.4.
- Acrescentada evidência CWV e testes dos edge cases ausentes.
- Fechadas variáveis/config ranges e falha de migration sem fallback enganoso.

## Veredito

Aprovado após aplicação das correções. Nenhum achado exige julgamento adicional do usuário; todos preservam requisitos existentes e reduzem ambiguidade técnica, risco de regressão ou scope creep.

