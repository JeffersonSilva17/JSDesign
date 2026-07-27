---
name: JS Designs
status: final
sources:
  - ../../prds/prd-JSDESIGN-2026-07-25/prd.md
  - ../../prds/prd-JSDESIGN-2026-07-25/addendum.md
  - ../../../../prototype/index.html
  - ../../../brainstorming/brainstorm-website-js-designs-2026-07-23/referencia-gio-home.png
  - imports/referencia-gio-home.png
updated: 2026-07-27
---

# JS Designs — Experience Spine

> Este arquivo define como a loja funciona: arquitetura de informação, fluxos, estados, interações, acessibilidade e regras de experiência. `DESIGN.md` define a camada visual. Em qualquer conflito com mockups, protótipos ou imports, `DESIGN.md` e `EXPERIENCE.md` vencem.

## Foundation

Loja B2C premium e responsiva para web. O lançamento cobre celular, tablet e computador, com prioridade mobile-first: cerca de 80% das clientes devem descobrir e comprar pelo celular. Todas as jornadas essenciais precisam funcionar no celular sem depender de atendimento, WhatsApp ou computador.

Não há UI system externo nomeado. A implementação deve seguir os padrões definidos em `DESIGN.md`, com referência visual por tokens como `{colors.surface-base}`, `{colors.ink-primary}`, `{typography.body}`, `{rounded.md}` e `{spacing.gutter-mobile}`.

Idiomas do lançamento: português do Brasil, inglês e espanhol. A preferência manual da cliente vence qualquer sugestão regional. Carrinho, pedido e Área da Cliente devem preservar contexto ao trocar idioma.

Modalidades essenciais:

| Modalidade | Compra | Pós-compra | Entrega |
|---|---|---|---|
| Produto físico personalizado | Produto > personalização > carrinho > checkout | briefing, prévia, aprovação, produção, QA, envio | item físico com rastreio quando disponível |
| Convite digital personalizado | tipo/modelo > pré-formulário > carrinho > checkout | briefing quando necessário, prévia, aprovação | arquivo final na Área da Cliente e por e-mail/WhatsApp |
| Produto Digital Pronto | produto > carrinho > checkout | sem briefing e sem aprovação | e-mail imediato e download na Área da Cliente |
| Projeto Exclusivo | formulário curto | análise humana | orçamento/continuidade manual |

## Information Architecture

| Surface | Reached from | Purpose |
|---|---|---|
| Início | abertura, SEO, links sociais | reconhecimento da JS Designs, busca, produtos mais procurados e entrada para categorias |
| Busca | topo, home, menu | localizar por produto, tema, ocasião, tipo ou personagem/estilo |
| Loja / Listagem | menu, busca, SEO | listar produtos com filtros por ocasião/tema e rótulos de modalidade |
| Lembrancinhas | menu, busca, home | descoberta de produtos físicos personalizados |
| Topos de bolo | menu, busca | descoberta de produtos físicos/topos configuráveis |
| Kits | menu, busca | descoberta de conjuntos e itens coordenados |
| Convites | menu, busca, home | comparação de tipos/modelos de convite e entrada no pré-formulário |
| Produtos Digitais Prontos | menu, busca, home | arquivos prontos, download imediato e compatibilidade com Silhouette Studio |
| Página de produto físico | cards, busca, SEO | esclarecer fotos, preço, quantidade, material, prazo, personalização, entrega e CTA |
| Personalização de produto físico | CTA “Personalizar e comprar” | coletar quantidade, tema, nome, data, miniatura e observação antes do carrinho |
| Página/configuração de convite | listagem de convites | escolher modelo, tema, dados principais e miniatura antes do carrinho |
| Página de Produto Digital Pronto | cards, busca, SEO | confirmar arquivo, uso, licença, Silhouette Studio, entrega imediata e compra |
| Carrinho | topo, após adicionar item | revisar itens, modalidades, subtotal, miniatura compartilhada, frete e edição |
| Checkout | carrinho | compra como visitante, pagamento, NIF/fatura, endereço e consentimentos |
| Confirmação de compra | pós-pagamento | explicar pedido confirmado e próxima ação por modalidade |
| Área da Cliente | e-mail/WhatsApp/código, conta | briefing, timeline, prévias, aprovação, arquivos, faturas, rastreio e suporte |
| Briefing | confirmação, Área da Cliente | coletar dados finais, fotos, referências, links e instruções protegidas |
| Aprovação de arte | notificação, Área da Cliente | aprovar ou pedir alteração em prévias numeradas |
| Projeto Exclusivo | busca sem resultado, menu, produto | solicitar análise humana sem pagamento imediato |
| Suporte Online | botão de suporte, estados de erro | respostas cadastradas e escalonamento para atendimento humano/WhatsApp |
| Administração | acesso interno | fila por próxima ação, pedidos, produção, QA, notificações e exceções |
| Políticas e institucional | footer, checkout, produto | privacidade, reembolso, licença, termos, contato, entrega e confiança |

Menu principal mobile: por tipo de produto. Ocasiões e temas aparecem como filtros dentro das listagens.

Topo mobile sempre visível: logo JS Designs, busca, conta/Área da Cliente, carrinho e menu. A busca é ação de descoberta principal.

Referências visuais promovidas:

| Mockup | Superfícies que ilustra |
|---|---|
| [Home mobile](mockups/key-home-mobile.html) | Início, Busca de entrada, Mais procurados, categorias principais e rótulos de modalidade. |
| [Busca e Projeto Exclusivo](mockups/key-busca-exclusivo.html) | Busca sem resultado, sugestões relacionadas e formulário de Projeto Exclusivo. |
| [Produto físico](mockups/key-produto-fisico.html) | Página de produto físico, resumo antes do CTA e explicação de personalização. |
| [Personalização de lembrancinha](mockups/key-personalizacao-lembrancinha.html) | Personalização de produto físico, preço por quantidade, adicional de miniatura e observação curta. |
| [Convites digitais](mockups/key-convites-modelos.html) | Comparação de tipos/modelos, pré-formulário, tema desejado, dados principais e miniatura. |
| [Carrinho e checkout](mockups/key-carrinho-checkout.html) | Carrinho editável, miniatura compartilhada e compra como visitante. |
| [Confirmação pós-pagamento](mockups/key-confirmacao-pos-pagamento.html) | Pedido confirmado, explicação de prazo e CTA para preencher briefing. |
| [Briefing em passos](mockups/key-briefing-passos.html) | Briefing protegido, passos curtos, autosave, uploads múltiplos e links de inspiração. |
| [Área da Cliente com aprovação](mockups/key-area-cliente-aprovacao.html) | Área da Cliente, linha do tempo, aprovação de arte, pedido de alteração e aviso de QA. |
| [Produto digital pronto](mockups/key-produto-digital-pronto.html) | Página de Produto Digital Pronto, download imediato, Silhouette Studio, licença e compra direta. |
| [Suporte Online](mockups/key-suporte-chat.html) | Chat automático, perguntas frequentes e escalonamento para humano/WhatsApp. |

## Voice and Tone

Microcopy deve ser direta, humana e confiável. A voz de marca visual vive em `DESIGN.md`; aqui valem as regras de operação.

| Use | Evite |
|---|---|
| “Personalizar e comprar” | “Finalizar agora” para item que ainda precisa configurar |
| “O prazo da primeira arte começa após o briefing completo.” | “Logo começamos sua arte” sem condição |
| “Arte aprovada para produção.” | “Tudo certo!” sem explicar consequência |
| “Produto digital. Download imediato.” | “Arquivo personalizado” para digital pronto |
| “Pagamentos protegidos e processados por parceiros certificados.” | “100% seguro” |
| “Não encontrou o tema? Peça um Projeto Exclusivo.” | Estado vazio sem alternativa |
| “Rastreio ainda não disponível.” | “Erro no rastreio” quando o código ainda não existe |

Textos devem informar preço, prazo, modalidade e próxima ação sempre que a cliente estiver perto de comprar ou esperar algo.

## Component Patterns

| ID | Uso | Regras comportamentais |
|---|---|---|
| `header-mobile` | Global | Mostra logo, busca, Área da Cliente, carrinho e menu. Deve permanecer simples e não competir com modais. |
| `search-panel` | Topo/home/menu | Aceita produto, tema, ocasião, tipo e personagem/estilo. Termo sem resultado oferece semelhantes e Projeto Exclusivo. |
| `button-primary` | Ações finais | Executa compra, briefing, aprovação, envio de formulário ou continuação. Deve ter texto específico da ação, não genérico. |
| `button-secondary` | Ações alternativas | Edita, volta, adia ou abre ajuda. Nunca deve competir visualmente com a ação principal da etapa. |
| `modal` | Promoções, menu e superfícies temporárias | Prende foco, fecha por Escape/botão explícito, restaura foco ao fechar e não empilha sobre outro modal. |
| `product-card` | Home/listagens/busca | Tap leva à página de produto, não direto ao carrinho para personalizados. Deve exibir modalidade e CTA compatível. |
| `badge` | Cards, status e filtros | Comunica modalidade, estado ou benefício. Não pode ser o único meio de diferenciar informação crítica. |
| `product-digital-card` | Home/listagens/busca | Exibe “Produto digital”, “Download imediato” e “Silhouette Studio” quando aplicável. CTA: “Comprar agora”. |
| `summary-panel` | Página de produto físico | Mostra preço/faixa, quantidade mínima, prazo, personalização, entrega e próxima etapa antes do CTA. |
| `configurator-physical` | Página própria | Coleta quantidade primeiro ou cedo; recalcula preço; exige tema, nome/texto e data; oferece miniatura paga e observação curta. |
| `configurator-invite` | Página/modelo | Coleta modelo/tipo, tema, dados principais e miniatura paga quando aplicável. |
| `thumbnail-character` | Configuradores e carrinho | Cobrança única por pedido/personagem. Pode ser reutilizada em convite e lembrancinha sem duplicar preço. |
| `cart-summary` | Pré-checkout | Mostra resumo editável por item: quantidade, tema, nome, data, miniatura, observação, modalidade e subtotal. |
| `checkout-section` | Compra | Uma página. Compra como visitante. Acesso posterior por código/link seguro via e-mail/WhatsApp. |
| `discount-modal` | Entrada no site | Oferece desconto por e-mail. Fácil de fechar, não repetitivo na sessão e não bloqueante. Deve pedir consentimento. |
| `briefing-stepper` | Área da Cliente | Passos curtos, autosave, indicador de progresso, validação por etapa e retomada posterior. |
| `upload-list` | Briefing | Aceita múltiplos arquivos e múltiplos links, com progresso, sucesso, falha e nova tentativa. |
| `approval-panel` | Área da Cliente | Dois botões principais: “Aprovar arte” e “Pedir alteração”. Exibe contador de alterações gratuitas restantes. |
| `annotation-tool` | Aprovação | Cliente pode usar texto livre, marcação na imagem ou ambos. Deve revisar antes de enviar. |
| `timeline` | Área da Cliente | Mostra estado atual, próximo passo, responsável, prazo e histórico. |
| `confirmation-panel` | Pós-pagamento | Confirma pedido, explica que o prazo depende do briefing completo e destaca a próxima ação. |
| `qa-photo` | Área da Cliente | Foto privada informativa; não exige confirmação da cliente antes do envio. |
| `tracking-panel` | Área da Cliente | Mostra código/link quando CTT/transportadora permitir; informa quando ainda não disponível. |
| `chat-widget` | Global/contextual | Respostas cadastradas. Escala para humano/WhatsApp dentro do fluxo de suporte. |
| `exclusive-form` | Busca/Projeto Exclusivo | Coleta pedido de análise sem pagamento imediato e confirma prazo de resposta humana. |
| `status-error` | Formulários, upload, pagamento e aprovação | Erros aparecem junto ao campo ou ação, explicam como resolver e mantêm os dados já preenchidos. |

## State Patterns

| Estado | Surface | Tratamento |
|---|---|---|
| Primeiro acesso | Início | Pode abrir modal de desconto após engajamento/tempo curto; fechar deve devolver foco corretamente. |
| Busca vazia inicial | Busca | Mostrar sugestões populares e categorias principais. |
| Busca sem resultado | Busca | Preservar termo, sugerir semelhantes e apresentar CTA para Projeto Exclusivo. |
| Produto personalizado incompleto | Personalização | Bloquear “Adicionar ao carrinho” até campos obrigatórios; erros próximos aos campos. |
| Quantidade muda preço | Personalização | Atualizar preço unitário/subtotal imediatamente e anunciar mudança de subtotal para leitor de tela. |
| Miniatura já paga | Carrinho/configuradores | Mostrar “Miniatura incluída neste pedido” e itens que reutilizam o ativo. |
| Pagamento aguardando confirmação | Confirmação/Área da Cliente | Não liberar briefing ou download com base apenas no retorno do navegador; mostrar estado financeiro separado. |
| Pedido confirmado | Pós-pagamento | Tela curta com CTA “Preencher briefing agora” e opção “Preencher depois”. |
| Briefing salvo parcialmente | Briefing | Mostrar autosave, permitir retomar e sinalizar etapas pendentes. |
| Briefing enviado | Área da Cliente | Confirmar recebimento, próximo passo e prazo da primeira arte. |
| Cliente atrasou briefing | Área da Cliente/Admin | Pausar relógio aplicável e deixar claro que prazo depende do briefing completo. |
| Prévia disponível | Área da Cliente + notificação | Notificar por e-mail e WhatsApp; ação principal leva à aprovação protegida. |
| Alteração solicitada | Área da Cliente | Registrar rodada, comentário, marcações e contador restante. |
| Arte aprovada | Área da Cliente | Confirmação forte; versão aprovada vira referência de produção. |
| Produção física | Área da Cliente | Linha do tempo informa produção iniciada e prazo de 7 dias corridos após Aprovação Final. |
| QA com foto | Área da Cliente | Foto privada informativa e status de preparação/envio. |
| Rastreio indisponível | Área da Cliente | “Rastreio ainda não disponível” ou explicação de transportadora sem consulta direta. |
| Digital pronto comprado | Pós-pagamento | Entrega imediata por e-mail e disponibilidade na Área da Cliente. |
| E-mail de digital não chegou | Área da Cliente | Regerar link temporário protegido. |
| Suporte fora do horário | Chat | Informar horário e prazo de resposta humana de até 1 dia útil. |
| Falha de notificação | Área da Cliente | Não bloquear acesso; a Área da Cliente é o registro principal. |

Estados operacionais do pedido a suportar: aguardando pagamento, pedido confirmado, aguardando briefing, criação de arte, aguardando aprovação, alteração solicitada, aprovado para produção, produção, QA, preparando envio, enviado, concluído, pausado, exceção e cancelado.

## Interaction Primitives

- Toque simples é a interação principal no celular.
- Campos obrigatórios devem ser preenchidos antes de carrinho quando alteram escopo ou preço.
- Listagens usam filtros por chips/controles de seleção; filtros não devem depender de hover.
- Carrosséis horizontais precisam ter controle por toque, teclado, leitor de tela e botões no desktop.
- Modais/drawers prendem foco, restauram foco ao fechar e fecham por Escape.
- Ações irreversíveis ou custosas exigem confirmação deliberada: aprovação final da arte, consentimento de download imediato com perda de desistência quando aplicável, remoção de item e envio de alteração.
- Uploads devem aceitar seleção múltipla, progresso, cancelamento por arquivo quando tecnicamente possível e nova tentativa.
- Marcação em imagem é opcional; texto livre sempre deve ser suficiente para pedir alteração.
- WhatsApp não é fluxo normal de compra. Ele aparece dentro do suporte, entrega/notificação ou Projeto Exclusivo quando adequado.

## Accessibility Floor

Piso: WCAG 2.2 AA em todas as jornadas essenciais.

- Navegação por teclado completa em catálogo, busca, carrinho, checkout, briefing, upload, aprovação, downloads e suporte.
- Foco visível usando `{colors.focus-ring}` ou equivalente com contraste AA.
- Nomes acessíveis para ícones de topo, busca, carrinho, conta, menu, favoritos, fechar modal e ações de aprovação.
- Ordem de foco segue ordem visual e de leitura.
- Mensagens de erro devem ser textuais, próximas ao campo, associadas por `aria-describedby` ou mecanismo equivalente.
- Mudanças dinâmicas de subtotal, upload, autosave, busca e estado de pedido devem ser anunciadas sem excesso.
- Redução de movimento: remover animações promocionais, smooth scroll e transições não essenciais.
- Alvos de toque mínimos: 44 x 44 px.
- Zoom/reflow: conteúdo legível e operável a 320 px e com zoom do navegador.
- Contraste visual fica em `DESIGN.md`; comportamento acessível fica neste spine.

## Responsive & Platform

| Faixa | Comportamento |
|---|---|
| 320–419 px | Coluna única; header compacto; cards em rolagem horizontal ou lista; formulários em passos; botões ocupam largura segura. |
| 420–759 px | Cards podem ganhar metadados adicionais; busca continua prioritária; resumo de produto pode ficar fixo no fim se não cobrir conteúdo. |
| 760–1099 px | Layout tablet com duas colunas em produto/checkout quando útil; menu pode virar navegação expandida parcial. |
| 1100 px+ | Header com navegação visível, grid editorial e comparação mais confortável; sem retirar capacidades mobile. |

Suporte formal: duas versões estáveis mais recentes de Chrome, Safari, Edge e Firefox no desktop; Safari em iPhone/iPad; Chrome em Android.

## Inspiration & Anti-patterns

- **Referência Gio:** aproveitar lógica de varejo: benefício inicial, topo claro, busca, categorias, hero comercial, confiança e produtos mais vendidos. Não copiar identidade visual, cores, claims ou composição.
- **Protótipo JS Designs:** preservar foco acessível, modais com `inert`, busca com estado sem resultado, linguagem editorial e estrutura de produtos mais pedidos. Corrigir prazos, modalidades, carrinho real, CTA e WhatsApp flutuante.
- **Anti-padrão rejeitado:** WhatsApp como início obrigatório da compra.
- **Anti-padrão rejeitado:** produto personalizado indo direto para carrinho sem dados mínimos.
- **Anti-padrão rejeitado:** produto digital pronto parecendo convite personalizado.
- **Anti-padrão rejeitado:** promessas absolutas de segurança ou prazo sem condição.

## Key Flows

### UJ-1 — Mariana encontra e encomenda lembrancinhas personalizadas sem depender de atendimento

1. Mariana abre a loja pelo celular.
2. Vê JS Designs, busca e “Mais procurados” logo no início.
3. Toca em uma lembrancinha física personalizada.
4. Lê página de produto com fotos, resumo compacto, preço/prazo/personalização e CTA “Personalizar e comprar”.
5. Na página própria de personalização, escolhe quantidade e vê o preço recalcular.
6. Informa tema por tema popular ou campo livre, nome/texto, data, miniatura sim/não e observação opcional.
7. Adiciona ao carrinho e revisa resumo editável.
8. Faz checkout como visitante e paga por parceiro certificado.
9. Vê tela “Pedido confirmado” com CTA “Preencher briefing agora”.
10. Preenche briefing em passos curtos, envia fotos/referências/links e confirma.
11. Recebe e-mail/WhatsApp quando a primeira arte está pronta.
12. Na Área da Cliente, aprova a arte ou pede alteração com texto/marcação.
13. **Clímax:** Mariana toca em “Aprovar arte”, confirma “Arte aprovada para produção” e vê a linha do tempo mudar para produção, com prazo de 7 dias corridos após Aprovação Final.

Falha: briefing incompleto pausa o prazo e a Área da Cliente mostra próxima ação clara. Alteração após aprovação exige atendimento/custo.

### UJ-2 — Beatriz encontra e compra um convite digital personalizado

1. Beatriz chega por busca externa procurando convite digital.
2. A página de convites apresenta tipos/modelos com diferenças escritas e visíveis.
3. Ela compara Convite Padrão e Convite Complexo por recursos, prazo, preço e briefing.
4. Escolhe modelo, informa tema desejado e dados principais do convite antes do carrinho.
5. Marca miniatura/personagem se quiser; o carrinho cobra a miniatura uma única vez por pedido/personagem.
6. Paga como visitante.
7. Acompanha criação pela Área da Cliente; quando aplicável, conclui briefing protegido.
8. Recebe notificação de prévia por e-mail e WhatsApp.
9. Aprova ou pede alteração com texto/marcação.
10. **Clímax:** Beatriz aprova o convite e recebe o arquivo final na Área da Cliente e por e-mail/WhatsApp.

Falha: se não houver exemplar do tema no catálogo, a busca preserva o tema e encaminha Beatriz para escolher o tipo/modelo; ausência de tema publicado não vira Projeto Exclusivo por si só.

### UJ-3 — Camila compra e recebe imediatamente um arquivo digital pronto

1. Camila busca um arquivo para usar no Silhouette Studio.
2. Entra em um Produto Digital Pronto.
3. Confirma selo “Produto digital”, “Download imediato”, compatibilidade com Silhouette Studio, ausência de personalização e condições de uso.
4. Toca em “Comprar agora”.
5. Conclui checkout como visitante.
6. O sistema envia o arquivo/link por e-mail e libera acesso na Área da Cliente.
7. **Clímax:** Camila recebe o e-mail e consegue abrir o arquivo no Silhouette Studio.

Falha: se o e-mail não chegar, Camila acessa a Área da Cliente por código/link seguro e gera novo link temporário protegido.

### UJ-4 — Sharom administra um pedido personalizado sem planilhas paralelas

1. Sharom entra na Administração.
2. Vê fila organizada por próxima ação: aguardando briefing, criar arte, aguardando aprovação, alteração solicitada, aprovado para produção, QA, envio e exceções.
3. Abre pedido com cliente, itens, pagamento, briefing, arquivos, miniatura compartilhada, prévias, aprovação, fatura, suporte e prazos em um único identificador.
4. Envia prévia numerada.
5. Recebe aprovação ou alteração com histórico.
6. Quando aprovado, a ficha de produção aponta para a versão aprovada.
7. Registra QA com foto privada informativa.
8. Registra transportadora e código/link quando CTT/transportadora permitir.
9. **Clímax:** Sharom conclui o pedido sem planilha paralela, com histórico e próxima ação rastreáveis.

Falha: pagamento pendente, cliente atrasada, urgência, endereço fora da UE ou mídia problemática entram em fila de exceções.

### UJ-5 — Mariana obtém ajuda automática e continua com atendimento humano quando necessário

1. Mariana abre Suporte Online durante a navegação ou na Área da Cliente.
2. Escolhe uma pergunta frequente ou digita intenção.
3. O chat responde com conteúdo cadastrado e revisado.
4. Se não resolver, oferece atendimento humano por chat/WhatsApp dentro do fluxo.
5. O contexto do pedido, página ou dúvida segue para Sharom quando autorizado.
6. **Clímax:** Mariana recebe orientação sem reiniciar a conversa, e a compra ou acompanhamento continua sem depender do WhatsApp como etapa obrigatória.

Falha: fora do horário humano, o chat informa horário de atendimento e retorno em até 1 dia útil.

## Mock Coverage

| Surface | Cobertura |
|---|---|
| Início | Mockado em [Home mobile](mockups/key-home-mobile.html). |
| Busca | Mockada pela entrada da home e pelo estado sem resultado em [Busca e Projeto Exclusivo](mockups/key-busca-exclusivo.html). |
| Loja / Listagem | Spine-only. |
| Lembrancinhas | Parcialmente mockada por card na home e personalização. |
| Topos de bolo | Spine-only, exceto presença como produto digital demonstrativo no mock de home. |
| Kits | Spine-only. |
| Convites | Mockado em [Convites digitais](mockups/key-convites-modelos.html), além de card na home. |
| Produtos Digitais Prontos | Mockado em [Produto digital pronto](mockups/key-produto-digital-pronto.html). |
| Página de produto físico | Mockada em [Produto físico](mockups/key-produto-fisico.html). |
| Personalização de produto físico | Mockada em [Personalização de lembrancinha](mockups/key-personalizacao-lembrancinha.html). |
| Página/configuração de convite | Mockada em [Convites digitais](mockups/key-convites-modelos.html). |
| Carrinho | Mockado em [Carrinho e checkout](mockups/key-carrinho-checkout.html). |
| Checkout | Parcialmente mockado em [Carrinho e checkout](mockups/key-carrinho-checkout.html); detalhes de frete/NIF/pagamento permanecem spine-only. |
| Confirmação de compra | Mockada em [Confirmação pós-pagamento](mockups/key-confirmacao-pos-pagamento.html). |
| Área da Cliente | Mockada em [Área da Cliente com aprovação](mockups/key-area-cliente-aprovacao.html). |
| Briefing | Mockado em [Briefing em passos](mockups/key-briefing-passos.html). |
| Aprovação de arte | Mockada em [Área da Cliente com aprovação](mockups/key-area-cliente-aprovacao.html). |
| Projeto Exclusivo | Mockado em [Busca e Projeto Exclusivo](mockups/key-busca-exclusivo.html). |
| Suporte Online | Mockado em [Suporte Online](mockups/key-suporte-chat.html). |
| Administração | Spine-only. |
| Políticas e institucional | Spine-only. |

## Product-Specific Rules

### Lembrancinhas físicas personalizadas

- Jornada norteadora do lançamento.
- Home deve levar a produtos específicos em “Mais procurados”.
- Produto personalizado nunca entra direto no carrinho.
- Página de produto explica antes de configurar.
- Configuração pré-carrinho exige quantidade, tema, data e nome/texto.
- Preço recalcula ao selecionar quantidade.
- Miniatura é adicional pago, mas foto/referências são enviadas só no briefing pós-pagamento.
- Observação curta pré-carrinho é opcional.

### Convites digitais personalizados

- Cliente entende tipo/modelo antes de tema.
- Modelos devem mostrar diferenças visualmente e por texto.
- Pré-formulário antes do carrinho coleta tema e dados principais.
- Convite Padrão: até 24 horas após pagamento e dados completos.
- Convite Complexo: até 48 horas após briefing completo.
- Prévia e aprovação são obrigatórias antes da entrega final.

### Produtos Digitais Prontos

- São para público diferente das lembrancinhas personalizadas.
- Podem aparecer em áreas mistas, mas sempre com rótulo forte de produto digital.
- Compatibilidade correta: Silhouette Studio. Não prometer Canva.
- Sem personalização, sem briefing e sem aprovação.
- CTA: “Comprar agora”.
- Entrega por e-mail e re-download na Área da Cliente.

### Projeto Exclusivo

- Formulário curto, sem pagamento imediato.
- Campos: nome, WhatsApp ou e-mail, tipo de produto desejado, data do evento, tema/ideia, referências/anexos opcionais, orçamento aproximado opcional e outras observações.
- Confirmação final informa recebimento, canal de retorno e resposta humana em até 1 dia útil.
- Não prometer aceite automático.

## Privacy, Trust and Legal UX

- Arquivos de briefing, fotos de crianças, referências, prévias, convites finais e fotos de QA são privados.
- Uploads e prévias devem ficar na Área da Cliente, não soltos em WhatsApp sem regra/consentimento.
- Produto Digital Pronto com entrega imediata exige ciência clara sobre acesso/download e regras de desistência quando aplicável.
- Licença de Produto Digital Pronto deve explicar uso permitido em peças físicas e proibição de redistribuir o arquivo digital.
- Linguagem de confiança aprovada: “Pagamentos protegidos e processados por parceiros certificados.”
- Fatura deve ser enviada por e-mail e ficar disponível na Área da Cliente.

## Implementation Dependencies

| Item | Impacto |
|---|---|
| Mapeamento final entre Família de Convite, formato, recursos e classificação Padrão/Complexo | Necessário para cards, filtros, preço, prazo e capacidade diária. |
| Tabela de preços por quantidade para lembrancinhas e kits | Necessária para subtotal dinâmico e resumo de produto. |
| Valor exato e regras finais do desconto por e-mail | Necessário para modal, cupom e consentimento. |
| Limites técnicos de upload por arquivo e formatos aceitos | Necessário para mensagens de ajuda e falha sem contrariar “quantos quiser”. |
| Regras operacionais de CTT/transportadora | Necessárias para rastreio integrado, link externo ou estado manual. |
