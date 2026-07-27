# Reconciliação de entrada — `referencia-gio-home.png`

## Escopo e método

Esta análise reconcilia a referência visual:

- `_bmad-output/brainstorming/brainstorm-website-js-designs-2026-07-23/referencia-gio-home.png`;

contra:

- `prd.md`;
- `addendum.md`;
- decisões posteriores registradas em `.memlog.md`, que prevalecem em caso de conflito.

A imagem foi inspecionada visualmente. Ela é tratada como referência de lógica comercial, hierarquia e padrões de experiência, não como autorização para copiar identidade, composição, conteúdo ou alegações.

## Leitura visual da entrada

### Estrutura e hierarquia observadas

1. **Faixa promocional superior:** ticker coral com mensagens repetidas em caixa alta, separadas por pequenos ícones.
2. **Cabeçalho amplo e arejado:** marca à esquerda, busca central muito dominante, conta e Carrinho à direita.
3. **Barra de categorias:** faixa bege com categorias expostas horizontalmente e “combos promocionais” em uma segunda linha.
4. **Hero de grande impacto:** banner fotográfico de largura quase total, com produtos personalizados em uso, mãos visíveis, escala real e texto promocional sobreposto ao centro.
5. **Carrossel:** indicadores discretos na base do hero sugerem múltiplas campanhas.
6. **Faixa de confiança:** três benefícios com ícones — parcelamento, entrega garantida e compra segura.
7. **Mais vendidos:** título central coral, sublinhado curto e grade de fotografias de produto.
8. **Contato flutuante:** elemento de WhatsApp no canto inferior direito.

### Linguagem visual e tom

- Paleta clara e calorosa, dominada por coral/rosa, bege e branco.
- Mistura de sans-serif comercial em navegação e benefícios com serif de alto contraste no hero.
- Fotografia real como principal instrumento de persuasão.
- Produtos mostrados com volume, escala e acabamento visíveis, não apenas isolados em fundo neutro.
- Tom afetivo, celebrativo e promocional, com forte ênfase em benefício financeiro e garantia.
- Densidade moderada: o cabeçalho tem bastante espaço, mas a navegação expõe muitas categorias.

### Padrões de UX reutilizáveis

- busca como ponto focal do cabeçalho;
- acesso imediato a conta e Carrinho;
- descoberta por categoria;
- hero editorial apoiado em fotografia real;
- faixa curta de benefícios de confiança;
- prova social/comercial por produtos mais vendidos;
- contato de suporte persistente.

## Cobertura confirmada no PRD e no addendum

### Estrutura comercial

O addendum preserva corretamente a sequência principal da referência:

1. faixa de anúncio;
2. cabeçalho com marca, busca, conta e Carrinho;
3. categorias;
4. hero editorial;
5. benefícios de confiança;
6. produtos mais vendidos.

O PRD transforma essa lógica em resultados de produto:

- FR-1 exige proposta de valor, busca destacada, categorias essenciais, produtos mais pedidos, prova de acabamento e Projeto Exclusivo;
- FR-2 preserva busca, conta e Carrinho em desktop e dispositivos móveis;
- FR-5 a FR-10 estruturam catálogo, busca e modalidades;
- a direção estética exige experiência minimalista, sofisticada, acolhedora e atemporal;
- fotografias reais e prova de acabamento são dependências e critérios do produto.

### Diferenciação de marca

Os artefatos registram corretamente que não devem ser copiados:

- identidade Gio;
- paleta coral;
- textos e composição exata;
- fotografias;
- alegações promocionais sem base.

A direção própria da JS Designs — branco, bege claro, dourado champanhe, taupe e preto suave — está explícita no PRD e no addendum.

### Confiança e promessas

O conflito mais sensível da imagem já foi tratado:

- “compra 100% segura” foi rejeitada;
- a mensagem aprovada é “Pagamentos protegidos e processados por parceiros certificados”;
- cashback, parcelamento e entrega garantida não são assumidos;
- garantias, benefícios e condições dependem de base comercial, operacional e jurídica.

### Mercado e operação

A imagem menciona entrega para todo o Brasil; o produto atual está orientado a Portugal e destinos europeus. O PRD corretamente substitui esse conteúdo por frete europeu, NIF, faturação, RGPD e políticas adequadas aos países efetivamente atendidos.

## Lacunas e conflitos remanescentes

### 1. Navegação por categorias: barra exposta ou menu “Loja”

**Severidade:** média-alta — decisão de arquitetura de informação.

A referência expõe várias categorias em uma barra horizontal de alta visibilidade. O addendum registra genericamente “categorias”, enquanto o PRD lista categorias essenciais e exige navegação sem sobrecarga. As fontes anteriores também recomendam reunir categorias em um menu “Loja”.

Falta decidir qual comportamento prevalece por viewport:

- categorias principais expostas no desktop;
- todas as categorias dentro de “Loja”;
- ou modelo híbrido, com poucas categorias prioritárias expostas e o restante no menu.

A decisão afeta descoberta, densidade, acessibilidade, internacionalização e adaptação móvel.

**Destino recomendado:** especificação UX, com uma condição simples no PRD somente se a exposição de categorias for requisito de produto.

### 2. Governança das superfícies promocionais permanece ambígua

**Severidade:** média.

A referência é fortemente promocional: ticker superior, hero de cashback, combos promocionais e faixa de benefícios. O PRD afirma “sem anúncios” e “sem ruído”, mas o contexto parece rejeitar publicidade de terceiros e padrões enganosos, não necessariamente campanhas próprias.

Falta explicitar:

- se a home terá faixa promocional permanente, sazonal ou opcional;
- se o hero poderá comunicar benefício financeiro;
- quantas promoções simultâneas são aceitáveis;
- como evitar que promoção concorra com busca, clareza de modalidade e proposta premium;
- quem aprova texto, validade, elegibilidade e remoção de uma campanha.

Sem essa distinção, UX pode interpretar “sem anúncios” como proibição de qualquer merchandising próprio ou, no extremo oposto, reproduzir a densidade promocional da referência.

**Destino recomendado:** regra de produto enxuta no PRD e padrões de campanha no UX/design system.

### 3. Qualidade fotográfica do hero está descrita de forma genérica

**Severidade:** média — lacuna qualitativa.

A referência não usa apenas “fotografia real”: mostra produtos em escala, nas mãos, com volume, materiais e contexto de uso. A composição aproxima visualmente a cliente do acabamento artesanal. O texto sobreposto transforma a fotografia em narrativa comercial.

O PRD e o addendum preservam fotografia real, detalhes e hero editorial, mas não deixam claro se a direção desejada inclui:

- produto em mãos ou em contexto de celebração;
- escala e proporção visíveis;
- detalhes de acabamento sem expor indevidamente fotografias de crianças;
- texto sobre imagem com contraste e legibilidade;
- equilíbrio entre imagem aspiracional e representação fiel do produto comprado.

**Destino recomendado:** especificação UX/conteúdo fotográfico. O PRD já contém o resultado correto — prova real de acabamento — e não precisa prescrever enquadramentos.

### 4. A referência é somente desktop e não resolve adaptação responsiva

**Severidade:** média.

A captura tem largura de desktop e não informa:

- colapso da barra de categorias;
- comportamento da busca no mobile;
- prioridade entre marca, conta e Carrinho;
- recorte e reordenação do hero;
- legibilidade do texto sobre a fotografia;
- tratamento da faixa de benefícios;
- comportamento do contato flutuante;
- navegação por teclado, foco, redução de movimento ou leitores de tela.

O PRD cobre corretamente responsividade, WCAG 2.2 AA e suporte móvel, e o protótipo já explora soluções próprias. A lacuna não exige mudança de escopo, mas impede que a referência seja aplicada literalmente.

**Destino recomendado:** registrar na especificação UX que desktop e mobile devem preservar a mesma prioridade de tarefa — descobrir, buscar e comprar — sem obrigatoriedade de paridade composicional.

## Elementos que não devem virar requisito

- uso de coral/rosa como cor principal;
- logotipo, ícones ou tipografia específicos da Gio;
- reprodução da composição fotográfica;
- cashback;
- parcelamento;
- “entrega garantida”;
- “compra 100% segura”;
- entrega “para todo o Brasil”;
- “combos promocionais” sem regra comercial confirmada;
- WhatsApp como caminho obrigatório da compra;
- número exato de categorias ou produtos exibidos na captura.

## Detalhes adequados a UX/design system, não ao corpo do PRD

- tokens de cor, tipografia, escala, espaçamento e ícones;
- dimensões e persistência da faixa superior;
- grid e breakpoints;
- recorte do hero e pontos focais da imagem;
- posição do texto sobre fotografia;
- comportamento e velocidade do carrossel;
- forma dos indicadores;
- aparência da faixa de benefícios;
- componentes e estados da busca, conta, Carrinho e contato flutuante;
- regras de contraste, foco, movimento e adaptação móvel.

## Síntese para triagem

| Item | Situação | Destino sugerido |
|---|---|---|
| Hierarquia comercial | Bem preservada | Nenhuma correção essencial |
| Identidade e alegações Gio | Corretamente rejeitadas | Manter guardrails |
| Categorias expostas × menu “Loja” | Não resolvido | UX/arquitetura de informação |
| Promoção própria × “sem anúncios” | Ambíguo | PRD enxuto + UX/conteúdo |
| Linguagem fotográfica do hero | Parcialmente preservada | UX/conteúdo fotográfico |
| Adaptação mobile e acessível | Não fornecida pela referência | UX; respeitar NFRs existentes |

