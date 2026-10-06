# Revisão adversarial — Story 3.2

Data: 2026-10-05 (rerun após achados)  
Artefato: `_bmad-output/implementation-artifacts/3-2-configurar-quantidade-e-calcular-preco-antes-do-carrinho.md`

## Achados e tratamento

1. **Contrato HTTP indefinido.** Request, sucesso e erros tinham apenas campos mínimos e rota. Resolvido: OpenAPI definido em `pricing-v1.openapi.yaml`, schemas estritos, payload máximo 4 KiB, resposta allowlist e códigos/status explícitos.
2. **Semântica de faixas ambígua.** Inclusividade, limites abertos e lacunas não tinham regra. Resolvido: limites inclusivos, máximo nulo como aberto, sobreposição/intervalo invertido rejeitados e lacunas usam preço base.
3. **Desconto e cálculo sem fórmula.** Não estava definido se o desconto incidia na unidade ou subtotal, nem como chegava ao total. Resolvido: faixa fornece preço unitário final em centavos; subtotal é base × quantidade; desconto é subtotal menos aplicado × quantidade; total é subtotal menos desconto. Sem float ou arredondamento percentual.
4. **`maximum_quantity` sem persistência definida.** Resolvido: armazenado em `pricing_product_rules`, com máximo absoluto 10000 e retorno no contrato.
5. **Quantidade digital pronta indefinida.** Resolvido: `digital_ready` aceita somente quantidade 1.
6. **Modelo podia ou não mudar o preço.** Resolvido para este escopo: modelo público é validado e retornado, e todos os modelos do produto usam as mesmas faixas; preço por modelo requer outra story.
7. **Versão sem regra de estabilidade.** Resolvido: versão monotônica aumenta em alterações de preço, mínimo/máximo, faixas, publicação ou disponibilidade; request não fornece versão e o carrinho futuro recalcula.
8. **Snapshot concorrente de preço/publicação indefinido.** Resolvido: leitura em snapshot consistente do PostgreSQL, e versão cobre todos os dados comerciais usados no cálculo.
9. **Overflow de multiplicação.** Resolvido: limite absoluto 10000 e verificação `quantity <= intdiv(PHP_INT_MAX, unit_price_minor)` antes da multiplicação.
10. **Elegibilidade de produto vaga.** Resolvido: status publicado, janela UTC de publicação/despublicação e disponibilidade; indisponíveis e não públicos compartilham `404 quote_unavailable`; `made_to_order` segue cotável.
11. **Rate limit/timeout sem valores.** Resolvido no plano: 60 requisições/minuto por IP e timeout BFF total de 3 s, corpo até 4 KiB.
12. **Resposta obsoleta na UI sem critério verificável.** Resolvido: debounce 250 ms, cancelamento, checagem de identificador e quantidade, e cenário Playwright de respostas fora de ordem.
13. **Erros do BFF/Laravel sem tradução estável.** Resolvido: códigos e status documentados; BFF apresenta conteúdo localizado por idioma e falha recuperável sem total inventado.
14. **Advisories no grafo de desenvolvimento e scanners indisponíveis no gate anterior.** Corrigido em 2026-10-06: a cadeia `eslint-config-next`/`fast-glob`/`micromatch`/`braces` foi removida do lockfile, substituída por configuração ESLint plana para TypeScript/React/hooks; `npm audit` passou sem vulnerabilidades. SAST examinou 284 arquivos e reportou 0 achados; Gitleaks examinou ~82,75 MB sem encontrar segredos. Composer audit e lint também passaram. O gate de segurança foi aprovado para desenvolvimento; revisar a implementação antes de concluir a story.

## Decisão

As lacunas documentais da revisão foram corrigidas. O gate de segurança foi aprovado para desenvolvimento em 2026-10-06 após resolver os advisories e executar os scanners versionados. Estado da story: `ready-for-dev`; revisar novamente a implementação antes de concluir.
