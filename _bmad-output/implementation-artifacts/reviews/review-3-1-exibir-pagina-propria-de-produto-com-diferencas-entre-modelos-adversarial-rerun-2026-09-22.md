# Revisao Adversarial Geral - Story 3.1 - Rerun

Artefato revisado: `_bmad-output/implementation-artifacts/3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md`

Status no momento da revisao: `ready-for-dev`

Data: 2026-09-22

## Achados

- A story ainda deixa a decisao de modelagem central em aberto: "decidir se `variants_reference` atual e suficiente" e, se nao for, criar migration. Isso e uma decisao de arquitetura/dados grande demais para uma story `ready-for-dev`; o dev pode escolher caminhos incompatíveis e ainda afirmar que cumpriu a tarefa.
- A selecao do modelo antes da navegacao futura nao define como a escolha sera preservada: URL param, form GET/POST futuro, estado local apenas, hidden input, rota de configuracao ou contrato BFF. Sem isso, o dev pode implementar uma selecao visual que se perde ao clicar no CTA.
- Os CTAs exigidos ("Personalizar e comprar", "Escolher modelo", "Comprar agora") continuam ambíguos porque carrinho/configuracao nao existem. A nota de "placeholder" nao basta: falta definir o href/estado exato, comportamento sem JS, texto de indisponibilidade e criterio testavel para nao simular compra.
- A story mistura "preco base em EUR" com comparacao de modelos sem dizer se modelo pode ter preco proprio, label "a partir de", preco indisponivel, ou preco herdado do produto. O aviso "calculo final fica para 3.2+" reduz o risco, mas nao remove a ambiguidade de apresentacao.
- O shape de modelos e insuficiente: id, label e descricao/diferenca nao dizem se ha imagem, disponibilidade por modelo, ordem, modelo padrao, slug/key canonico, compatibilidade por modelo ou relacao com `variants_reference`. O dev ainda precisa inventar detalhes para passar AC3.
- A galeria limitada a 8 imagens nao define se a imagem primaria deve sempre aparecer primeiro, se imagens inseguras sao simplesmente omitidas ou se a pagina deve mostrar estado parcial quando algumas imagens sao rejeitadas. Isso deixa margem para quebrar SEO/social image ou esconder falhas de resolver.
- A story pede "material/acabamento/conteudo quando disponiveis", mas o contrato/task lista `materials` e `composition` sem diferenciar acabamento, conteudo entregue e conteudo do kit. O dev pode mapear campos de forma confusa e a UI pode prometer informacao que a API nao representa.
- Produto digital pronto precisa explicar "termos de uso" e possivel regra legal de download imediato, mas a story proibe transacionalidade e nao define copy minima ou link institucional. O resultado pode ser uma pagina que diz "download imediato" sem explicar condicoes de uso de forma verificavel.
- Os criterios de acessibilidade pedem anuncio de mudancas de modelo, mas a story tambem exige que JS seja apenas melhoria local. Falta um padrao concreto, como `fieldset`/radio group, `aria-live` limitado ou fallback de lista expandida, para evitar uma implementacao semi-acessivel dificil de testar.
- O teste de "no-JS para conteudo essencial" e vago: nao define se os modelos devem ser radios nativos funcionais sem JS, se apenas a leitura das diferencas basta, nem se o CTA deve carregar modelo padrao. Isso enfraquece o AC de selecao antes do carrinho.
- A story exige "payload publico verificavel" para modelos, mas nao pede teste de contrato OpenAPI contra exemplos invalidos de modelo: duplicata de id, mais de 12 modelos, modelo sem label, ordem duplicada, imagem de modelo insegura e campos extras dentro de cada modelo.
- O risco de N+1 foi citado, mas o limite de consultas esperado nao foi definido. "Medir/afirmar limite" permite que o dev escolha qualquer limite depois; deveria haver uma expectativa concreta ou, no minimo, um teto comparavel ao padrao atual.
- A story nao explicita como atualizar `ProductDetail` mantendo compatibilidade com listagem/search que usam `ProductCard`. Um dev pode vazar campos novos para cards ou exigir dados de detalhe nas listagens, degradando performance.
- O tratamento de indisponibilidade por payload invalido pode entrar em conflito com `generateMetadata`: a story nao exige que metadata e corpo usem exatamente a mesma leitura/classificacao para os novos campos enriquecidos, repetindo um risco que 2.4 precisou controlar.
- O status `ready-for-dev` e forte demais enquanto os achados acima deixam comportamento de modelos, dados e CTA abertos. A story esta bem documentada, mas ainda nao esta suficientemente determinada para impedir implementacoes divergentes.

## Resolucao

Correcoes aplicadas em `_bmad-output/implementation-artifacts/3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md` em 2026-09-22:

- Decisao de dados fechada: criar migration incremental `catalog_product_models`; `variants_reference` fica legado/admin e nao e fonte publica.
- Shape publico fechado: `ProductDetail` ganha `gallery`, campos editoriais de detalhe e `models`; `ProductCard` nao muda.
- Modelo publico fechado: `key`, `label`, `difference`, `is_default`, `image`, exatamente um default, maximo 12 modelos.
- Galeria fechada: maximo 8 imagens, primaria segura primeiro, omissao individual de imagens inseguras, consistencia com `primary_image`.
- Handoff fechado: CTA sempre usa `/carrinho?produto=<slug>&modelo=<model_key>` como futuro placeholder, com aviso visivel de que compra/configuracao ainda sera ativada.
- Query fechada: `modelo` e a unica query nova do detalhe; valida corpo/selecionado, mas nao entra em canonical/OG/JSON-LD/sitemap.
- Acessibilidade/no-JS fechadas: modelos por links SSR e, se houver melhoria client-side, radio group/fieldset ou semantica equivalente com `aria-live="polite"` curto.
- Preco fechado: produto com multiplos modelos mostra "A partir de"; sem preco por modelo nesta story.
- Testes reforcados: modelos invalidos, CTA com query, no-JS, query budget maximo de 8 SQL queries para detalhe enriquecido.

Resultado: achados do rerun tratados. A story pode permanecer `ready-for-dev` do ponto de vista da revisao adversarial geral documental.
