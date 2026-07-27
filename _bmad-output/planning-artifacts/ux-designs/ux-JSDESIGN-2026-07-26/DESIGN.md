---
name: JS Designs
description: Sistema visual da loja online premium JS Designs para papelaria personalizada, lembrancinhas, convites e produtos digitais.
status: final
sources:
  - ../../prds/prd-JSDESIGN-2026-07-25/prd.md
  - ../../prds/prd-JSDESIGN-2026-07-25/addendum.md
  - ../../../../prototype/index.html
  - ../../../brainstorming/brainstorm-website-js-designs-2026-07-23/referencia-gio-home.png
  - imports/referencia-gio-home.png
updated: 2026-07-27
colors:
  surface-base: '#FFFFFF'
  surface-soft: '#F8F5F1'
  surface-muted: '#EFE8DF'
  surface-raised: '#FFFFFF'
  ink-primary: '#2D2D2D'
  ink-secondary: '#5D554E'
  ink-muted: '#74685E'
  accent-primary: '#765321'
  accent-soft: '#C8A46B'
  accent-calm: '#7D8974'
  border-subtle: '#D8CEC3'
  border-strong: '#A8947D'
  success: '#3F6F4B'
  warning: '#9A6A24'
  danger: '#A12626'
  focus-ring: '#2D2D2D'
typography:
  display:
    fontFamily: 'Georgia, Times New Roman, serif'
    fontSize: 42px
    fontWeight: '400'
    lineHeight: '1.08'
    letterSpacing: '0'
  display-mobile:
    fontFamily: 'Georgia, Times New Roman, serif'
    fontSize: 32px
    fontWeight: '400'
    lineHeight: '1.12'
    letterSpacing: '0'
  heading:
    fontFamily: 'Georgia, Times New Roman, serif'
    fontSize: 30px
    fontWeight: '400'
    lineHeight: '1.18'
    letterSpacing: '0'
  subheading:
    fontFamily: 'Georgia, Times New Roman, serif'
    fontSize: 22px
    fontWeight: '400'
    lineHeight: '1.25'
    letterSpacing: '0'
  body:
    fontFamily: 'Inter, Helvetica Neue, Arial, sans-serif'
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: '0'
  body-sm:
    fontFamily: 'Inter, Helvetica Neue, Arial, sans-serif'
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
    letterSpacing: '0'
  label:
    fontFamily: 'Inter, Helvetica Neue, Arial, sans-serif'
    fontSize: 13px
    fontWeight: '600'
    lineHeight: '1.35'
    letterSpacing: '0'
  caption:
    fontFamily: 'Inter, Helvetica Neue, Arial, sans-serif'
    fontSize: 12px
    fontWeight: '400'
    lineHeight: '1.4'
    letterSpacing: '0'
rounded:
  sm: 4px
  md: 8px
  lg: 12px
  xl: 16px
  full: 9999px
spacing:
  '1': 4px
  '2': 8px
  '3': 12px
  '4': 16px
  '5': 20px
  '6': 24px
  '8': 32px
  '10': 40px
  '12': 48px
  '16': 64px
  gutter-mobile: 20px
  gutter-tablet: 32px
  gutter-desktop: 48px
  content-max: 1240px
components:
  header-mobile:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
  search-panel:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    focus: '{colors.focus-ring}'
  button-primary:
    background: '{colors.ink-primary}'
    foreground: '{colors.surface-base}'
    radius: '{rounded.md}'
    focus: '{colors.focus-ring}'
  button-secondary:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.md}'
  product-card:
    background: '{colors.surface-raised}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  badge:
    background: '{colors.surface-soft}'
    foreground: '{colors.accent-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.full}'
  digital-badge:
    background: '{colors.accent-calm}'
    foreground: '{colors.surface-base}'
    radius: '{rounded.full}'
  product-digital-card:
    background: '{colors.surface-raised}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    badge: '{components.digital-badge.background}'
    radius: '{rounded.lg}'
  summary-panel:
    background: '{colors.surface-soft}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  form-field:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.md}'
    focus: '{colors.focus-ring}'
  configurator-panel:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  configurator-physical:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  configurator-invite:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  thumbnail-character:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.accent-soft}'
    radius: '{rounded.lg}'
  cart-summary:
    background: '{colors.surface-soft}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  checkout-section:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  discount-modal:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    accent: '{colors.accent-primary}'
    radius: '{rounded.xl}'
  briefing-stepper:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    active: '{colors.ink-primary}'
    complete: '{colors.accent-calm}'
  upload-list:
    background: '{colors.surface-soft}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.md}'
  approval-panel:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  annotation-tool:
    stroke: '{colors.accent-primary}'
    foreground: '{colors.ink-primary}'
    focus: '{colors.focus-ring}'
  timeline-step-current:
    background: '{colors.ink-primary}'
    foreground: '{colors.surface-base}'
    border: '{colors.ink-primary}'
  timeline-step-complete:
    background: '{colors.accent-calm}'
    foreground: '{colors.surface-base}'
    border: '{colors.accent-calm}'
  timeline:
    current: '{components.timeline-step-current.background}'
    complete: '{components.timeline-step-complete.background}'
    future: '{colors.border-subtle}'
  modal:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    radius: '{rounded.xl}'
  confirmation-panel:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    success: '{colors.accent-calm}'
    radius: '{rounded.xl}'
  qa-photo:
    background: '{colors.surface-soft}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  tracking-panel:
    background: '{colors.surface-soft}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  chat-widget:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  exclusive-form:
    background: '{colors.surface-base}'
    foreground: '{colors.ink-primary}'
    border: '{colors.border-subtle}'
    radius: '{rounded.lg}'
  status-error:
    foreground: '{colors.danger}'
    border: '{colors.danger}'
---

# JS Designs — Design Spine

> Este arquivo define como a loja deve parecer. `EXPERIENCE.md` define como ela funciona. Em qualquer conflito com protótipos, mockups ou referências importadas, estes dois spines vencem.

## Brand & Style

A JS Designs deve parecer uma loja premium, autoral e acolhedora para celebrações. A leitura visual é de ateliê organizado: clara o suficiente para comprar pelo celular, sofisticada o suficiente para sustentar produtos personalizados e humana o suficiente para transmitir cuidado.

A direção estética confirmada é minimalista, sofisticada, acolhedora e atemporal. A página deve favorecer fotografia real de produtos, respiro editorial, hierarquia clara e poucos elementos concorrendo por atenção. A referência Gio deve ser usada apenas como lógica comercial de varejo: anúncio inicial, topo com marca/busca/conta/carrinho, categorias, produtos fortes e confiança visível. A identidade coral, claims, composição e imagens da referência não devem ser copiadas.

O protótipo existente é uma base útil para linguagem leve, serifas editoriais, fundo claro, botões escuros e foco acessível. Ele deve ser corrigido onde diverge do PRD: prazos, modalidades, CTAs, digitais prontos, carrinho real, briefing, aprovação e ausência de botão flutuante permanente de WhatsApp.

Referências visuais promovidas: [Home mobile](mockups/key-home-mobile.html), [Busca e Projeto Exclusivo](mockups/key-busca-exclusivo.html), [Produto físico](mockups/key-produto-fisico.html), [Personalização de lembrancinha](mockups/key-personalizacao-lembrancinha.html), [Convites digitais](mockups/key-convites-modelos.html), [Carrinho e checkout](mockups/key-carrinho-checkout.html), [Confirmação pós-pagamento](mockups/key-confirmacao-pos-pagamento.html), [Briefing em passos](mockups/key-briefing-passos.html), [Área da Cliente com aprovação](mockups/key-area-cliente-aprovacao.html), [Produto digital pronto](mockups/key-produto-digital-pronto.html) e [Suporte Online](mockups/key-suporte-chat.html). Estes mockups ilustram composição e densidade; este `DESIGN.md` e o `EXPERIENCE.md` vencem se houver conflito.

## Colors

- **Branco (`{colors.surface-base}`)** é a superfície principal. Deve manter a loja limpa, aberta e orientada para fotografia.
- **Marfim suave (`{colors.surface-soft}`)** cria faixas, áreas editoriais, estados vazios e fundos de blocos sem pesar a página.
- **Champagne (`{colors.accent-soft}`)** é apoio premium: detalhes, filetes, divisórias especiais e realces discretos.
- **Dourado escuro (`{colors.accent-primary}`)** é o tom editorial de marca. Use para pequenos destaques, links especiais, selos e elementos de confiança. Não usar como cor dominante de botão quando isso reduzir contraste.
- **Preto suave (`{colors.ink-primary}`)** é o texto principal e a ação primária. Sustenta contraste e legibilidade mobile.
- **Taupe (`{colors.ink-secondary}`, `{colors.ink-muted}`, `{colors.border-subtle}`)** organiza textos secundários, bordas, metadados e estados de baixa ênfase.
- **Verde calmo (`{colors.accent-calm}`)** diferencia sinais positivos, digitais prontos e etapas concluídas sem parecer promocional.
- **Vermelho (`{colors.danger}`)** é exclusivo para erro, remoção, bloqueio ou aviso crítico. Não usar para descontos, urgência comercial ou decoração.

Combinações essenciais devem atingir WCAG 2.2 AA. Texto pequeno deve usar `{colors.ink-primary}` ou `{colors.ink-secondary}` sobre `{colors.surface-base}` ou `{colors.surface-soft}`. Selos com fundo colorido precisam ter contraste validado antes de implementação.

## Typography

A hierarquia combina serif editorial para títulos e sans-serif para operação.

- `display` e `display-mobile`: títulos principais da home, páginas de categoria e telas de confirmação importantes.
- `heading`: títulos de seções, páginas de produto e Área da Cliente.
- `subheading`: títulos dentro de cards expandidos, etapas de briefing, painéis e modais.
- `body`: texto de leitura, descrições e instruções.
- `body-sm`, `label` e `caption`: metadados, prazos, preço auxiliar, mensagens de ajuda e rótulos.

Não usar espaçamento negativo entre letras. Não escalar fonte com largura de viewport; usar papéis tipográficos fixos e ajustes por breakpoint. Textos de botão devem caber em 320 px sem truncamento.

## Layout & Spacing

A loja é mobile-first porque a expectativa é de 80% de descoberta e compra pelo celular. Desktop existe para conforto, comparação e administração, mas o celular deve permitir a jornada completa.

Regras:

- margem mobile mínima: `{spacing.gutter-mobile}`;
- conteúdo desktop máximo: `{spacing.content-max}`;
- grade mobile: coluna única, com carrosséis horizontais apenas onde a rolagem é óbvia e acessível;
- desktop: até 12 colunas em páginas editoriais/comerciais, sem transformar cada seção em card;
- seções devem ser bandas full-width ou layouts sem moldura; cards são para itens repetidos, modais e superfícies realmente enquadradas;
- primeira dobra mobile da home deve ser curta: marca, busca/acesso rápido e “Mais procurados” com produtos específicos visíveis cedo.

## Elevation & Depth

Hierarquia visual deve vir primeiro de espaço, tipografia, fotografia e contraste. Sombras são discretas e funcionais.

Use sombra apenas em:

- modais;
- drawer/menu;
- cards em hover/foco no desktop;
- elementos fixos quando precisarem se separar do conteúdo.

Não usar sombras grandes para criar aparência de marketplace genérico. Não empilhar cards dentro de cards.

## Shapes

A linguagem de forma é levemente arredondada, com raio moderado. O padrão é `{rounded.md}` para botões e campos, `{rounded.lg}` para cards e `{rounded.xl}` para modais. Pills (`{rounded.full}`) são reservados para badges pequenos, chips de filtro e contadores.

Fotos de produto devem respeitar o mesmo raio do container. Evitar formas excessivamente arredondadas em cards comerciais, porque a marca deve parecer ateliê premium, não aplicativo infantil.

## Components

| ID | Nome | Especificação visual |
|---|---|---|
| `header-mobile` | Cabeçalho mobile | Fundo `{colors.surface-base}`, logo JS Designs, busca, conta, carrinho e menu. Ícones finos, com alvo mínimo de toque. Separação inferior por `{colors.border-subtle}`. |
| `search-panel` | Busca | Campo de busca destacado, texto grande quando em painel e estado sem resultado com alternativa visível. |
| `button-primary` | Botão primário | `{components.button-primary.background}` com texto `{components.button-primary.foreground}`. Usado para compra, briefing, aprovação e ações finais. |
| `button-secondary` | Botão secundário | Fundo claro, borda sutil e texto escuro. Usado para editar, preencher depois, pedir ajuda ou voltar. |
| `modal` | Modal/drawer | Superfície elevada com fundo `{components.modal.background}`, raio `{components.modal.radius}`, foco preso e botão de fechar claro. |
| `product-card` | Card de produto | Foto real dominante, selo quando necessário, nome, tipo, preço/subtotal relevante, prazo ou entrega e CTA. |
| `badge` | Selo genérico | Selo pequeno com fundo claro, texto curto e uso restrito a modalidade, benefício ou estado. |
| `product-digital-card` | Card de Produto Digital Pronto | Mesmo rigor do `product-card`, com selo digital forte e sem linguagem de personalização. |
| `digital-badge` | Selo de produto digital | Usar `{components.digital-badge.background}` com texto curto: “Produto digital”, “Download imediato” ou “Silhouette Studio”. |
| `summary-panel` | Resumo compacto de produto | Bloco próximo ao CTA com preço, quantidade mínima, prazo, personalização e entrega. Visualmente mais denso que a descrição editorial. |
| `configurator-physical` | Configurador de físico | Página limpa, com bloco de quantidade/preço no alto, campos em sequência curta e subtotal persistente quando útil. |
| `configurator-invite` | Configurador de convite | Visual semelhante ao configurador de físico, mas com prévia/modelo em evidência e diferenças do tipo de convite visíveis. |
| `thumbnail-character` | Miniatura/personagem | Adicional com borda de destaque, explicação de cobrança única e sinal visual de reutilização no pedido. |
| `cart-summary` | Carrinho | Resumos em cards simples, editáveis, separados por modalidade. Miniatura compartilhada deve parecer um adicional único do pedido. |
| `checkout-section` | Checkout | Seções claras dentro de uma página, sem excesso de cards. Pagamento, contato, entrega, NIF/fatura e consentimentos precisam ser legíveis no celular. |
| `form-field` | Formulário mobile | Campos grandes, labels persistentes, ajuda abaixo do campo e erro textual junto ao campo. Foco visível com `{colors.focus-ring}`. |
| `discount-modal` | Modal de desconto | Leve, fácil de fechar, com título claro, campo de e-mail e consentimento. Não deve parecer bloqueio da loja. |
| `briefing-stepper` | Stepper de briefing | Etapas curtas, indicador de progresso e estado salvo. Não usar números pequenos demais para toque. |
| `upload-list` | Upload de referências | Lista de arquivos/links com progresso, estado e ações de remover/tentar novamente. Não usar miniaturas pequenas demais para toque. |
| `timeline` | Linha do tempo do pedido | Estado atual em maior contraste, etapas concluídas em `{colors.accent-calm}`, etapas futuras em borda/tonalidade baixa. |
| `approval-panel` | Tela de aprovação de arte | Prévia grande, ações principais “Aprovar arte” e “Pedir alteração”, contador de alterações restantes e aviso de consequência. |
| `annotation-tool` | Ferramenta de marcação | Controles discretos sobre ou abaixo da imagem, com cor de anotação ligada a `{colors.accent-primary}` e opção textual sempre visível. |
| `confirmation-panel` | Confirmação pós-pagamento | Confirmação clara, marca de sucesso e próxima ação dominante para briefing quando o pedido exigir. |
| `qa-photo` | Foto de QA | Área informativa com foto privada em bom tamanho, legenda e sem botão de aprovação. |
| `tracking-panel` | Painel de rastreio | Bloco calmo com transportadora, código/link quando existir e estado “ainda não disponível” quando aplicável. |
| `chat-widget` | Chat/suporte | Entrada compacta. WhatsApp aparece dentro do fluxo de suporte, não como botão flutuante permanente. |
| `exclusive-form` | Formulário de Projeto Exclusivo | Formulário curto, com campos obrigatórios e opcionais visualmente separados, e anexos/links sem parecer checkout. |
| `status-error` | Erro/alerta crítico | Texto em `{components.status-error.foreground}` e borda de erro quando o problema bloquear envio, pagamento, upload ou ação. |

Mockups de referência por componente:

| Mockup | Componentes ilustrados |
|---|---|
| [Home mobile](mockups/key-home-mobile.html) | Cabeçalho mobile, busca, modal/benefício de desconto em formato compacto, cards de produto, selo digital e categorias principais. |
| [Busca e Projeto Exclusivo](mockups/key-busca-exclusivo.html) | Busca sem resultado, chips de sugestão, formulário de Projeto Exclusivo e campos opcionais. |
| [Produto físico](mockups/key-produto-fisico.html) | Galeria/foto de produto, resumo compacto, explicação de personalização e CTA “Personalizar e comprar”. |
| [Personalização de lembrancinha](mockups/key-personalizacao-lembrancinha.html) | Configurador de físico, quantidade/preço dinâmico, adicional de miniatura, observação curta e CTA de carrinho. |
| [Convites digitais](mockups/key-convites-modelos.html) | Cards comparáveis de convite, selo Padrão/Complexo, pré-formulário, tema e miniatura compartilhável. |
| [Carrinho e checkout](mockups/key-carrinho-checkout.html) | Carrinho editável por modalidade, miniatura cobrada uma vez, checkout como visitante e confiança de pagamento. |
| [Confirmação pós-pagamento](mockups/key-confirmacao-pos-pagamento.html) | Confirmação de pedido, próximos passos e CTA para briefing. |
| [Briefing em passos](mockups/key-briefing-passos.html) | Stepper de briefing, autosave, upload múltiplo, links de referência e arquivos protegidos. |
| [Área da Cliente com aprovação](mockups/key-area-cliente-aprovacao.html) | Linha do tempo, aprovação de arte, contador de alterações gratuitas e aviso de QA. |
| [Produto digital pronto](mockups/key-produto-digital-pronto.html) | Selo digital, resumo de entrega imediata, licença, confiança de pagamento e CTA “Comprar agora”. |
| [Suporte Online](mockups/key-suporte-chat.html) | Chat com respostas cadastradas, perguntas frequentes e WhatsApp dentro do fluxo de suporte. |

## Do's and Don'ts

| Faça | Não faça |
|---|---|
| Priorize produto, preço, prazo e próxima ação no celular. | Abrir a home com hero longo que empurre os produtos para baixo. |
| Use fotografia real e espaço editorial para transmitir qualidade. | Copiar paleta, claims ou composição da referência Gio. |
| Diferencie visualmente físico personalizado, convite personalizado e digital pronto. | Usar o mesmo CTA e selo para modalidades diferentes sem contexto. |
| Use “Pagamentos protegidos e processados por parceiros certificados.” | Usar “100% seguro” ou promessa absoluta semelhante. |
| Mostre foco visível, erros próximos ao campo e contraste AA. | Depender de cor sozinha para comunicar estado. |
| Reserve animações para transições discretas e respeite redução de movimento. | Usar animações promocionais que atrasem compra ou briefing. |
| Mantenha WhatsApp dentro do suporte. | Exibir WhatsApp como botão flutuante permanente competindo com carrinho/compra. |
