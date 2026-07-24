---
title: 'Protótipo visual da página inicial JS Designs'
type: 'feature'
created: '2026-07-24'
status: 'done'
review_loop_iteration: 1
baseline_commit: '92022c6efd06c7e391572b148110e3db4c2df9bf'
context:
  - '{project-root}/_bmad-output/brainstorming/brainstorm-website-js-designs-2026-07-23/brainstorm-intent.md'
  - '{project-root}/_bmad-output/brainstorming/brainstorm-website-js-designs-2026-07-23/website-blueprint.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** A apresentação aberta anteriormente resume o brainstorming, mas não permite visualizar como será a loja real. A cliente precisa avaliar uma página inicial que se pareça com um e-commerce premium da JS Designs e siga a composição da referência aprovada.

**Approach:** Criar um protótipo estático, responsivo e navegável da página inicial, usando a identidade clara da JS Designs, fotografias ou composições visuais de produtos, hierarquia editorial e interações demonstrativas. O protótipo deve parecer uma loja real, sem representar que checkout, pagamentos ou back-end já estejam implementados.

## Boundaries & Constraints

**Always:**

- Usar português do Brasil, UTF-8 e a paleta `#FFFFFF`, `#F8F5F1`, `#C8A46B`, `#2D2D2D` e `#D8CEC3`.
- Manter estilo minimalista, sofisticado e atemporal, com espaço em branco, tipografia elegante, imagens amplas e animações discretas.
- Seguir a lógica visual da referência sem copiar sua identidade: faixa de confiança, cabeçalho, hero, categorias, funcionamento, produtos, diferenciais, Instagram e rodapé.
- Destacar a busca por personagem ou tema e apresentar produtos mais pedidos no início.
- Exibir um CTA principal por contexto, com “Comprar agora” e chamada secundária discreta para projeto exclusivo.
- Comunicar personalização pós-pagamento, produção em sete dias corridos após briefing completo e entrega para toda a Europa sem sobrecarregar a página.
- Garantir navegação por teclado, foco visível, contraste legível, textos alternativos e comportamento responsivo.
- Implementar interações demonstrativas para menu móvel, pesquisa, favoritos, carrinho, carrosséis e captação de e-mail com 10% na primeira compra.

**Ask First:**

- Alterar a paleta ou a direção visual aprovada.
- Usar fotografias externas, marcas de terceiros ou personagens protegidos.
- Transformar o protótipo em loja publicada, integrar pagamentos ou coletar dados reais.

**Never:**

- Copiar literalmente o site de referência.
- Exibir excesso de texto, vários CTAs concorrentes ou cartões de personagens como navegação principal.
- Afirmar que pagamentos, entregas, descontos ou pedidos demonstrativos são reais.
- Implementar checkout, autenticação, banco de dados, painel administrativo ou integrações externas nesta entrega.

## I/O & Edge-Case Matrix

| Cenário | Entrada / Estado | Saída / Comportamento esperado | Tratamento |
|---|---|---|---|
| Busca demonstrativa | Termo como “fazendinha” | Sugestões relacionadas aparecem em painel acessível | Termo vazio mantém sugestões populares |
| Menu móvel | Tela estreita e acionamento do menu | Navegação lateral abre, bloqueia o fundo e pode ser fechada | Escape e clique externo fecham |
| Produto | Clique em favorito ou compra | Ícone muda de estado ou contador do carrinho aumenta | Mensagem discreta confirma a ação |
| Cupom | E-mail válido no modal | Confirmação visual do benefício demonstrativo | E-mail inválido mostra erro local |
| Movimento reduzido | Preferência do sistema ativada | Animações e rolagens suaves são desativadas | Conteúdo permanece completo |

</frozen-after-approval>

## Code Map

- `prototype/index.html` — estrutura semântica da página inicial e conteúdo demonstrativo.
- `prototype/styles.css` — identidade visual, layout responsivo, componentes e estados acessíveis.
- `prototype/app.js` — interações locais do menu, pesquisa, cartões, carrosséis, modal e feedback.
- `prototype/assets/` — recursos visuais locais criados ou reaproveitados sem dependências externas.

## Tasks & Acceptance

**Execution:**

- [x] `prototype/index.html` — construir todas as seções da homepage com marcação semântica, conteúdo conciso, avisos de protótipo e relações acessíveis entre campos e mensagens.
- [x] `prototype/styles.css` — implementar o sistema visual premium, componentes, breakpoints, áreas roláveis e contraste WCAG AA.
- [x] `prototype/app.js` — implementar interações demonstrativas, uma única superfície modal por vez, gestão completa de foco e estados acessíveis, sem rede ou armazenamento de dados pessoais.
- [x] `prototype/assets/` — preparar recursos visuais locais e leves, evitando imagens externas e propriedade intelectual de terceiros.

**Acceptance Criteria:**

- Dado o protótipo aberto em desktop, quando a página carrega, então a composição lembra uma loja premium completa e segue a hierarquia da referência aprovada.
- Dado um celular, quando a cliente navega, então conteúdo, menu, busca, cartões e botões permanecem claros e utilizáveis sem rolagem horizontal.
- Dada uma interação demonstrativa, quando a cliente usa busca, favorito, carrinho ou cupom, então recebe feedback imediato sem sair da página nem enviar dados.
- Dado o uso por teclado, quando a cliente percorre os controles, então a ordem, os rótulos, o foco e o fechamento por Escape são compreensíveis.
- Dado o conteúdo comercial, quando a cliente lê a homepage, então entende variedade, qualidade, personalização, prazo, suporte e entrega europeia sem excesso de informação.
- Dada uma superfície aberta, quando menu, busca ou promoção é acionado, então apenas uma permanece ativa, o fundo fica inerte, o foco não escapa e retorna ao acionador correto ao fechar.
- Dada uma tela baixa ou uma mudança para o breakpoint desktop, quando menu ou busca estão abertos, então seus conteúdos continuam roláveis e qualquer drawer incompatível é fechado.
- Dado movimento reduzido, quando a cliente usa busca ou carrossel, então nenhuma rolagem suave é executada.
- Dado um resultado de busca, quando a cliente o seleciona, então ele corresponde a conteúdo presente ou é identificado claramente como projeto exclusivo.
- Dado o carregamento inicial, quando a cliente percorre o primeiro conjunto de conteúdo, então “Produtos mais pedidos” aparece antes de categorias e funcionamento.
- Dada a promoção de primeira compra, quando não houve rolagem relevante nem interação, então o tempo sozinho não a abre.

## Spec Change Log

- **Revisão 1 — sobreposições, acessibilidade e prioridade comercial:** a revisão encontrou superfícies concorrentes sem gestão unificada de foco, menus sem rolagem em telas baixas, promoção acionada apenas por tempo, resultados de busca sem produto correspondente, contrastes insuficientes e produtos mais pedidos distantes do início. A execução e os critérios foram ampliados para exigir uma única superfície por vez, contenção e restauração de foco, áreas roláveis, gatilho por envolvimento real, conteúdo demonstrativo consistente, WCAG AA e ordem comercial correta. Isso evita conteúdo inacessível, foco perdido, mensagens enganosas e desvio da prioridade aprovada. **KEEP:** preservar a direção visual premium, a composição editorial, os oito SVGs locais, a hierarquia geral, o bom comportamento responsivo de base, a ausência de dependências externas e as interações demonstrativas.

## Design Notes

A referência orienta a sequência e a densidade, enquanto a JS Designs preserva sua própria linguagem: fundo claro, dourado champanhe discreto, serifas editoriais nos títulos e sans-serif funcional na interface. Imagens e composições devem priorizar acabamentos, laços, papel, recortes e embalagens. A promoção de primeira compra surge apenas depois de envolvimento, fecha facilmente e não reaparece na mesma sessão.

Menu, busca e modal de boas-vindas formam um único sistema de superfícies: abrir uma fecha as demais, bloqueia o conteúdo de fundo para ponteiro e teclado, contém o foco, fecha por Escape ou acionador explícito e devolve o foco ao controle correto. Menu e busca devem ter rolagem própria em telas baixas; o menu fecha automaticamente ao entrar no breakpoint desktop.

“Produtos mais pedidos” aparece imediatamente depois do conjunto hero/busca, antes de categorias e funcionamento. Toda sugestão de busca corresponde a um cartão real ou comunica claramente que é uma sugestão de projeto. A promoção de 10% só abre após um sinal de envolvimento, como rolagem relevante ou interação; tempo isolado nunca é gatilho suficiente.

Textos, controles, badges e rodapé atingem contraste WCAG AA. Favoritos atualizam estado visual e nome acessível; o carrinho usa singular e plural corretamente; erros de formulário são associados programaticamente aos campos. Em larguras intermediárias, marca e ações permanecem alinhadas sem colunas vazias.

## Verification

**Commands:**

- `node --check prototype/app.js` — esperado: JavaScript sem erros de sintaxe.
- `npx --yes html-validate prototype/index.html` — esperado: nenhum erro estrutural crítico.

**Manual checks:**

- Abrir `prototype/index.html` em desktop e celular, testar menu, busca, modal, favoritos, carrinho, teclado e preferência de movimento reduzido.

## Suggested Review Order

**Composição da loja**

- A entrada combina promessa de marca, busca destacada e chamada comercial principal.
  [`index.html:62`](../../prototype/index.html#L62)

- Mais pedidos surge antes da exploração por categoria e do funcionamento.
  [`index.html:89`](../../prototype/index.html#L89)

- A identidade premium nasce dos tokens, tipografia e proporções editoriais.
  [`styles.css:1`](../../prototype/styles.css#L1)

**Interação e acessibilidade**

- Um controlador único coordena menu, busca, promoção, foco e fundo inerte.
  [`app.js:123`](../../prototype/app.js#L123)

- A busca deriva resultados dos cartões reais e evita promessas inexistentes.
  [`app.js:293`](../../prototype/app.js#L293)

- A promoção exige intenção humana e preserva contexto e sessão.
  [`app.js:215`](../../prototype/app.js#L215)

- Controles do carrossel comunicam limites e desativam ações sem efeito.
  [`app.js:526`](../../prototype/app.js#L526)

**Responsividade e acabamento**

- Breakpoints reorganizam conteúdo sem duplicar navegação nem perder ações.
  [`styles.css:1544`](../../prototype/styles.css#L1544)

- Movimento reduzido elimina transições e rolagens animadas.
  [`styles.css:1850`](../../prototype/styles.css#L1850)

- O foco do rodapé mantém contraste visível sobre o fundo escuro.
  [`styles.css:1178`](../../prototype/styles.css#L1178)
