---
stepsCompleted:
  - step-01-validate-prerequisites
  - step-02-design-epics
  - step-03-create-stories
  - step-04-final-validation
inputDocuments:
  - prds/prd-JSDESIGN-2026-07-25/prd.md
  - prds/prd-JSDESIGN-2026-07-25/addendum.md
  - ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md
  - ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md
  - architecture/architecture-JSDESIGN-2026-07-27/ARCHITECTURE-SPINE.md
---

# JSDESIGN — Quebra em Épicos e Histórias

## Visão geral

Este documento reúne o inventário de requisitos extraído do PRD, adendo, especificação UX e arquitetura final da loja online JS Designs. As próximas etapas transformarão este inventário em épicos, mapa de cobertura e histórias implementáveis.

## Inventário de requisitos

### Requisitos funcionais

FR-1: A página inicial deve apresentar proposta de valor, busca destacada, categorias essenciais, produtos mais pedidos, prova de acabamento, funcionamento da personalização e acesso discreto a Projeto Exclusivo.

FR-2: A cliente deve acessar Loja, categorias, busca, conta e Carrinho por uma navegação consistente em dispositivos móveis e desktop.

FR-3: O sistema deve permitir que páginas públicas relevantes sejam descobertas e compreendidas por mecanismos de busca.

FR-4: A cliente deve alternar entre português do Brasil, inglês e espanhol e escolher entre as moedas suportadas sem perder a página, o Carrinho ou o contexto do Pedido.

FR-5: A operação deve cadastrar produtos com natureza, categoria, fotos, preço, quantidade ou variante, tema demonstrado, ocasião, personalização, prazo, materiais, composição, disponibilidade e tipo de entrega. Personagens ou outros ativos protegidos somente podem ser cadastrados após verificação de direitos de uso comercial.

FR-6: A cliente deve pesquisar por título, produto, tema, personagem, ocasião e grafias alternativas.

FR-7: A página de produto físico deve informar fotos reais, preço, quantidades, material, acabamento, conteúdo, personalização incluída ou opcional, prazo e entrega.

FR-8: A descoberta e a página de convite devem explicar cada Tipo de Convite por demonstração, funcionamento, recursos, diferenciais, preço, prazo, dados necessários, Miniatura e processo de alterações.

FR-9: A página de Produto Digital Pronto deve informar que o produto não inclui personalização, quais arquivos serão entregues, compatibilidade com o Silhouette Studio, condições de uso e entrega imediata após pagamento.

FR-10: A cliente deve iniciar uma solicitação de Projeto Exclusivo quando desejar formato, recurso ou escopo fora das opções configuradas; ausência de tema de convite no catálogo não exige avaliação exclusiva.

FR-11: A cliente deve escolher quantidade, ativar ou dispensar personalização básica e selecionar opção de Miniatura compatível em produto físico.

FR-12: A cliente deve antecipar Tipo de Convite, tema em texto livre, cores, data, horário, endereço e opção de Miniatura antes de pagar.

FR-13: O Carrinho pode oferecer dois ou três produtos complementares do mesmo tema ou ocasião.

FR-14: Quando desconto progressivo por quantidade, desconto de conjunto ou cupom forem aplicáveis, o sistema deve recalcular e aplicar automaticamente o benefício válido mais vantajoso, sem acumulação indevida.

FR-15: O Carrinho deve aceitar vários produtos no mesmo Pedido e preservar cada item, quantidade e configuração durante a sessão e nas transições de idioma.

FR-16: A cliente deve concluir identificação, entrega quando aplicável, NIF opcional, pagamento e revisão do Pedido em uma única página, sem cadastro prévio obrigatório.

FR-17: Após pagamento confirmado, o sistema deve criar ou associar acesso seguro à Área da Cliente sem transformar cadastro prévio em barreira.

FR-18: A cliente deve pagar por métodos habilitados pela JS Designs por meio de parceiro certificado.

FR-19: Um Pedido por transferência deve permanecer reservado em “Aguardando pagamento” por 48 horas.

FR-20: No lançamento, o sistema deve calcular disponibilidade, custo e previsão de envio de produtos físicos para endereços nos 27 países da União Europeia.

FR-21: Toda venda deve gerar Fatura; a cliente pode selecionar “Desejo incluir NIF na fatura” e informar o dado quando aplicável.

FR-22: Após o pagamento, a confirmação deve explicar claramente a próxima ação conforme os itens do Pedido.

FR-23: Após pagamento confirmado, a cliente deve preencher um único Briefing por Pedido/evento, mesmo quando comprar convites e lembrancinhas juntos.

FR-24: Se o Briefing não for concluído, o Pedido deve permanecer em “Aguardando briefing”, sem iniciar criação ou Produção.

FR-25: O sistema deve calcular e comunicar o prazo de criação aplicável após todas as informações obrigatórias estarem completas.

FR-26: Sharom deve enviar Prévias numeradas e a cliente deve consultar o histórico na Área da Cliente.

FR-27: A cliente deve poder enviar até três Rodadas de Alteração gratuitas por Arte, com contador visível.

FR-28: A cliente deve registrar Aprovação Final explícita após revisar dados críticos.

FR-29: Produtos físicos devem entrar em Produção somente após Aprovação Final.

FR-30: A Área da Cliente deve mostrar Estado do Pedido, motivo, data prevista e Próxima Ação.

FR-31: A cliente deve preencher Briefing, comentar Prévia, solicitar alteração, aprovar Arte e consultar arquivos dentro do Pedido.

FR-32: Antes da postagem de produto físico, Sharom deve anexar fotografia privada da encomenda concluída.

FR-33: Sharom deve registrar transportadora e código ou link de rastreamento; a cliente deve recebê-los e consultá-los na Área da Cliente.

FR-34: Após Aprovação Final, a cliente deve escolher receber o convite por e-mail ou WhatsApp.

FR-35: Após pagamento confirmado, o sistema deve enviar imediatamente por e-mail o arquivo de Produto Digital Pronto adquirido.

FR-36: Notificações devem informar Estado do Pedido, Próxima Ação e impacto no prazo, usando canais consentidos.

FR-37: O site deve oferecer acesso compacto ao Suporte Online sem tornar atendimento etapa obrigatória.

FR-38: O Atendimento Automático deve responder apenas quando houver conteúdo cadastrado aplicável.

FR-39: A cliente deve continuar pelo Chat ou pelo WhatsApp quando o atendimento automático não resolver.

FR-40: Sharom deve cadastrar, revisar, traduzir, ativar e desativar perguntas e respostas do suporte.

FR-41: Sharom deve visualizar pedidos por estado, responsável, prazo restante, prioridade e Próxima Ação.

FR-42: O painel deve conectar cliente, itens, pagamento, Briefing, Prévias, Aprovação, Produção ou entrega digital, Fatura, notificações e suporte por identificador único.

FR-43: Após Aprovação Final, o sistema deve gerar Ficha de Produção com versão aprovada, itens, quantidades, materiais, acabamento, Miniatura, prazo, endereço e checklist.

FR-44: Sharom deve concluir checklist e adicionar fotografia antes de marcar encomenda como pronta para envio.

FR-45: O sistema deve tratar quarta alteração, urgência, pagamento pendente, erro, retrabalho, falha de integração e pedido pausado como exceções explícitas.

FR-46: Sharom deve administrar categorias, produtos, traduções, preços, variantes, quantidades, disponibilidade, temas, Miniaturas, complementos, descontos e conteúdo de confiança.

FR-47: Sharom deve consultar pagamentos, conciliação, NIF, Faturas, fretes, rastreamentos, cancelamentos e falhas.

FR-48: O painel deve limitar ações administrativas por papel e registrar operações sensíveis.

FR-49: O sistema deve medir, com consentimento adequado, descoberta, busca, visualização de produto, configuração, Carrinho, início e conclusão do Checkout, Briefing, Aprovação, entrega e suporte.

FR-50: O painel deve calcular tempo por estado, pedidos pausados, cumprimento de prazo, alterações, retrabalho, falhas e dependência de suporte.

### Requisitos não funcionais

NFR-1: As jornadas essenciais devem atender WCAG 2.2 AA, incluindo teclado, foco visível, nomes acessíveis, contraste, mensagens de erro compreensíveis, zoom, redução de movimento e leitores de tela.

NFR-2: Em conexão móvel representativa do público europeu, páginas públicas e etapas de compra devem carregar e responder sem que fotografias, vídeos, fontes ou scripts bloqueiem a tarefa principal; aceite quantitativo deve seguir Core Web Vitals bons no 75º percentil por dispositivo.

NFR-3: Falhas de chat, analytics, newsletter ou mídia complementar não devem impedir busca, Carrinho, Checkout, Briefing, Aprovação ou acesso ao Pedido; falhas críticas devem apresentar estado seguro e permitir retomada.

NFR-4: Repetição de confirmação, webhook, clique ou trabalho assíncrono não pode duplicar cobrança, Pedido, benefício, Miniatura, Fatura, notificação ou entrega.

NFR-5: O lançamento deve demonstrar controles proporcionais ao OWASP ASVS nível 2, revisão de código, análise de dependências, testes automatizados de segurança, bloqueio de upload malicioso, pentest antes da produção e correção de achados críticos/altos.

NFR-6: Dados completos de cartão e códigos de segurança não devem transitar nem ser armazenados pela aplicação da JS Designs; processamento deve permanecer no parceiro certificado e no escopo PCI DSS aplicável.

NFR-7: Coleta, uso, compartilhamento, retenção e eliminação de dados devem seguir RGPD por design, com mapa de tratamentos, fluxo de direitos, fornecedores documentados, avaliação de impacto quando aplicável, processo de violação de dados e exclusão/revisão automática por prazo.

NFR-8: Briefings, Miniaturas, fotografias de crianças, Prévias e fotos de qualidade devem permanecer privados, protegidos contra enumeração/acesso não autorizado e eliminados conforme política de retenção; autorização, finalidade, testes de acesso e exclusão ponta a ponta são obrigatórios.

NFR-9: Dados críticos devem ter cópias protegidas, restauração testada, procedimento de resposta a incidentes e evidência de recuperação dos fluxos críticos.

NFR-10: Português do Brasil, inglês e espanhol devem cobrir interface, conteúdo comercial, mensagens transacionais, e-mails, formulários, políticas, suporte cadastrado e SEO; datas, endereços, moedas e números devem ser apresentados sem ambiguidade.

NFR-11: Falhas em pagamento, Fatura, frete, e-mail, WhatsApp, upload, antimalware, entrega digital e mudança de estado devem ser detectáveis, correlacionadas ao Pedido e acionáveis pela operação.

NFR-12: O site deve ser responsivo e preservar jornadas essenciais em computador, tablet e celular, com suporte formal às duas versões estáveis mais recentes de Chrome, Safari, Edge e Firefox em desktop, Safari em iPhone/iPad e Chrome em Android; interface funcional a partir de 320 px.

### Requisitos adicionais de arquitetura e operação

AR-1: Implementar a aplicação como monólito modular full-stack com módulos explícitos de domínio e portas para pagamentos, storage, e-mail, WhatsApp e envio.

AR-2: Next.js App Router + TypeScript será a base inicial da aplicação web; Server Actions/API routes devem chamar serviços de domínio, não provedores externos diretamente.

AR-3: Postgres deve ser a fonte canônica de dados transacionais, incluindo pedidos, clientes, catálogo, briefing, arquivos, aprovações, suporte e notificações.

AR-4: `order_id` deve conectar todo o histórico comercial, operacional e de atendimento.

AR-5: `payment_state` e `order_state` devem ser separados; webhooks ou conciliação manual confirmada são autoridade para confirmação financeira.

AR-6: Transições de pedido/item devem ocorrer por máquina de estados explícita e comandos server-side idempotentes.

AR-7: Configuração pré-carrinho é obrigatória para itens personalizados e deve ser server-owned para cálculo de preço/subtotal.

AR-8: Miniatura/personagem é adicional compartilhado, cobrado uma única vez por pedido/personagem e reutilizável entre itens compatíveis.

AR-9: Briefing pós-pagamento deve ser mestre por pedido/evento, com seções condicionais por modalidade.

AR-10: Arquivos privados devem viver em storage privado, com metadados no Postgres e acesso por autorização/URLs temporárias.

AR-11: Notificações devem usar outbox transacional; e-mail e WhatsApp são efeitos assíncronos, e Área da Cliente é o registro principal.

AR-12: Suporte automático deve usar base de respostas cadastradas e revisadas; escalonamento humano deve preservar contexto.

AR-13: Checkout deve permitir compra como visitante e acesso pós-compra por código seguro/magic link ou mecanismo equivalente sem expor pedidos por enumeração.

AR-14: Internacionalização deve usar chaves de conteúdo e valores normalizados; troca de idioma não pode alterar carrinho, pedido, preço ou prazo.

AR-15: Admin deve usar a mesma aplicação, banco, agregado de pedido, comandos e autorização, sem planilha paralela como fonte de verdade.

AR-16: Conta comercial, pagamentos e fatura devem considerar pessoa singular/trabalhadora independente em Portugal, CIRS 1336, titularidade bancária compatível, NIF/fatura e validação contabilística/fiscal.

AR-17: Provedor de pagamento deve suportar checkout, cartões, wallets, MB WAY, reembolsos, disputas e conciliação; PayPal entra no MVP apenas se centralizado pelo provedor escolhido.

AR-18: Transferência bancária deve expirar em 48 horas, ser reprocessável e não liberar briefing/entrega sem confirmação.

AR-19: Frete físico automatizado no MVP deve cobrir os 27 países da União Europeia; outros países europeus devem seguir para suporte humano.

AR-20: Uploads devem validar tipo permitido, assinatura real, tamanho, quantidade, conteúdo malicioso, nomes não previsíveis, storage não público e retenção.

AR-21: Prévias devem ser versionadas de forma imutável; alteração deve ser solicitação consolidada; aprovação final deve registrar versão, autora, data e confirmações críticas.

AR-22: Suporte humano deve operar com horário de segunda a sábado, 8h–20h de Portugal continental, com fila fora do horário e prazo máximo de 1 dia útil.

AR-23: Capacidade inicial deve considerar 100 peças físicas por semana e 4 pontos diários para convites, sendo Convite Padrão 1 ponto e Complexo 2 pontos.

AR-24: Urgência deve exigir avaliação humana, registrar decisão, impedir atraso confirmado em pedidos anteriores e aplicar adicional de 30% com mínimo de €10 quando aceita.

AR-25: Busca/SEO devem usar tema demonstrado, ocasião, grafias alternativas, URLs estáveis, metadados localizados, sitemap, canonical e marcação estruturada.

AR-26: Catálogos atuais de convites/produtos digitais e papelaria personalizada devem ser inventariados separadamente e migrados para taxonomia unificada com revisão de licenças.

AR-27: Idioma e moeda devem ser preferências explícitas; EUR é moeda-base, moedas adicionais dependem de prova do provedor, taxa deve ser bloqueada por 30 minutos no checkout.

AR-28: Segurança e continuidade devem incluir TLS/HSTS, CSRF, proteção contra injeção/abuso, CSP, segredos seguros, MFA admin, RBAC, auditoria, backups, análise de dependências, testes e pentest.

AR-29: Publicação de trabalhos feitos para clientes deve exigir autorização opcional registrada. Convites só podem ser publicados com dados sensíveis modificados/anônimos, como local, data e horário; lembrancinhas e miniaturas podem aparecer sem modificação quando autorizadas, respeitando privacidade, direitos de imagem e direitos de uso comercial de ativos protegidos.

### Requisitos de design UX

UX-DR1: Implementar sistema visual premium, autoral e acolhedor com branco, marfim, champagne, dourado escuro, taupe, preto suave, verde calmo e vermelho apenas para erro/risco.

UX-DR2: Validar combinações de cor para WCAG 2.2 AA, especialmente textos pequenos, selos e estados em fundos claros.

UX-DR3: Implementar hierarquia tipográfica com papéis `display`, `display-mobile`, `heading`, `subheading`, `body`, `body-sm`, `label` e `caption`, sem truncar textos de botão em 320 px.

UX-DR4: Implementar layout mobile-first, considerando 80% celular e 20% computador, com primeira dobra curta mostrando marca, busca/acesso rápido e “Mais procurados” com produtos específicos.

UX-DR5: Usar sombras apenas em modais, drawers, hover/foco desktop e elementos fixos necessários; evitar aparência genérica de marketplace.

UX-DR6: Implementar tokens de raio para botões/campos, cards, modais e pills, mantendo estética de ateliê premium.

UX-DR7: Implementar `header-mobile` com logo, busca, Área da Cliente, carrinho e menu, com alvos mínimos de toque.

UX-DR8: Implementar `search-panel` com busca por produto, tema, ocasião, tipo e personagem/estilo, incluindo sugestões, semelhantes e Projeto Exclusivo em sem resultado.

UX-DR9: Implementar botões primário/secundário com textos específicos por ação, como “Personalizar e comprar”, “Comprar agora”, “Preencher briefing agora”, “Aprovar arte” e “Pedir alteração”.

UX-DR10: Implementar modal/drawer com foco preso, fechamento por Escape/botão explícito, restauração de foco e sem empilhamento de modais.

UX-DR11: Implementar cards de produto físicos/personalizados com foto real, modalidade, preço/subtotal relevante, prazo/entrega e navegação para página de produto, nunca direto ao carrinho quando exigir personalização.

UX-DR12: Implementar cards de Produto Digital Pronto com selo forte “Produto digital”, “Download imediato” e “Silhouette Studio” quando aplicável, CTA “Comprar agora” e linguagem sem personalização.

UX-DR13: Implementar `summary-panel` em página de produto físico com preço/faixa, quantidade mínima, prazo, personalização, entrega e próxima etapa antes do CTA.

UX-DR14: Implementar `configurator-physical` em página própria, coletando quantidade cedo, recalculando preço, exigindo tema, nome/texto, data quando aplicável, miniatura paga e observação curta opcional.

UX-DR15: Implementar `configurator-invite` para escolha de tipo/modelo, diferenças visuais/textuais, tema, dados principais e miniatura paga quando aplicável.

UX-DR16: Implementar `thumbnail-character` com explicação de cobrança única e indicação visual de reutilização no pedido.

UX-DR17: Implementar `cart-summary` com resumo editável por item, modalidade, quantidade, tema, nome, data, miniatura, observação e subtotal; miniatura compartilhada aparece como adicional único.

UX-DR18: Implementar checkout em uma página, legível no celular, com contato, entrega, NIF/fatura, pagamento, revisão e consentimentos.

UX-DR19: Implementar `discount-modal` de entrada para desconto por e-mail, fácil de fechar, não repetitivo na sessão, não bloqueante e com consentimento.

UX-DR20: Implementar `briefing-stepper` com passos curtos, autosave, progresso, validação por etapa e retomada posterior.

UX-DR21: Implementar `upload-list` com múltiplos arquivos e links, progresso, sucesso, falha, remoção e nova tentativa.

UX-DR22: Implementar `approval-panel` com prévia grande, ações “Aprovar arte” e “Pedir alteração”, contador de alterações gratuitas restantes e aviso de consequência.

UX-DR23: Implementar `annotation-tool` opcional para marcação em imagem, mantendo texto livre como alternativa suficiente e revisão antes do envio.

UX-DR24: Implementar `timeline` da Área da Cliente com estado atual, próximo passo, responsável, prazo e histórico.

UX-DR25: Implementar `confirmation-panel` pós-pagamento com confirmação clara, explicação de próximos passos e CTA dominante para briefing quando necessário.

UX-DR26: Implementar `qa-photo` como foto privada informativa, sem botão de aprovação da cliente antes do envio.

UX-DR27: Implementar `tracking-panel` com transportadora, código/link quando existir e estado “Rastreio ainda não disponível” quando aplicável.

UX-DR28: Implementar `chat-widget` compacto com respostas cadastradas, escalonamento humano e WhatsApp apenas dentro do fluxo de suporte.

UX-DR29: Implementar `exclusive-form` curto, sem pagamento imediato, com nome, WhatsApp/e-mail, tipo de produto, data do evento, tema/ideia, referências/anexos opcionais, orçamento opcional e outras observações.

UX-DR30: Implementar `status-error` com erro textual próximo ao campo/ação, dados preservados e orientação de correção.

UX-DR31: Implementar estados de UX para busca vazia, busca sem resultado, produto incompleto, subtotal recalculado, miniatura já paga, pagamento aguardando confirmação, briefing salvo, prévia disponível, alteração solicitada, arte aprovada, produção física, QA, rastreio indisponível, digital comprado, e-mail não recebido, suporte fora do horário e falha de notificação.

UX-DR32: Implementar acessibilidade comportamental: teclado completo, foco visível, nomes acessíveis, ordem de foco correta, `aria-describedby` para erros, anúncios de mudanças dinâmicas e redução de movimento.

UX-DR33: Implementar responsividade contínua nas faixas 320–419 px, 420–759 px, 760–1099 px e 1100 px+, sem remover capacidades mobile no desktop.

UX-DR34: Implementar privacidade UX: arquivos de briefing, fotos, referências, prévias, convites finais e fotos de QA privados; upload/prévia na Área da Cliente; licença digital clara; fatura por e-mail e Área da Cliente.

UX-DR35: Implementar controle de autorização de portfólio/divulgação com escolha opcional e desmarcada por padrão; para convites, explicar que dados como local, data e horário serão modificados antes da publicação; para lembrancinhas e miniaturas, permitir publicação sem modificação quando autorizada.

### Mapa de cobertura de requisitos

FR-1: Epic 1 — Loja pública, catálogo e descoberta mobile-first.

FR-2: Epic 1 — Loja pública, catálogo e descoberta mobile-first.

FR-3: Epic 1 — Loja pública, catálogo e descoberta mobile-first.

FR-4: Epic 1 — Loja pública, catálogo e descoberta mobile-first.

FR-5: Epic 1 — Loja pública, catálogo e descoberta mobile-first.

FR-6: Epic 1 — Loja pública, catálogo e descoberta mobile-first.

FR-7: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-8: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-9: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-10: Epic 6 — Suporte Online e Projeto Exclusivo.

FR-11: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-12: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-13: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-14: Epic 2 — Produto, modalidade e configuração antes do carrinho.

FR-15: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-16: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-17: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-18: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-19: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-20: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-21: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-22: Epic 3 — Carrinho, checkout, pagamento, frete e fatura.

FR-23: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-24: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-25: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-26: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-27: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-28: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-29: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-30: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-31: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-32: Epic 5 — Entrega digital, produção física, QA e rastreio.

FR-33: Epic 5 — Entrega digital, produção física, QA e rastreio.

FR-34: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-35: Epic 5 — Entrega digital, produção física, QA e rastreio.

FR-36: Epic 4 — Área da Cliente, briefing e aprovação de arte.

FR-37: Epic 6 — Suporte Online e Projeto Exclusivo.

FR-38: Epic 6 — Suporte Online e Projeto Exclusivo.

FR-39: Epic 6 — Suporte Online e Projeto Exclusivo.

FR-40: Epic 6 — Suporte Online e Projeto Exclusivo.

FR-41: Epic 7 — Administração, exceções, operação e métricas.

FR-42: Epic 7 — Administração, exceções, operação e métricas.

FR-43: Epic 5 — Entrega digital, produção física, QA e rastreio.

FR-44: Epic 5 — Entrega digital, produção física, QA e rastreio.

FR-45: Epic 7 — Administração, exceções, operação e métricas.

FR-46: Epic 7 — Administração, exceções, operação e métricas.

FR-47: Epic 7 — Administração, exceções, operação e métricas.

FR-48: Epic 7 — Administração, exceções, operação e métricas.

FR-49: Epic 7 — Administração, exceções, operação e métricas.

FR-50: Epic 7 — Administração, exceções, operação e métricas.

## Lista de épicos

### Epic 1: Loja pública, catálogo e descoberta mobile-first

A cliente consegue entrar no site, entender a JS Designs, buscar produtos/temas, navegar por categorias, ver “Mais procurados” e encontrar páginas públicas indexáveis.

**FRs cobertos:** FR-1, FR-2, FR-3, FR-4, FR-5, FR-6

**Notas de implementação:** inclui setup inicial da aplicação greenfield, design system base, catálogo público, navegação, busca, home e SEO essencial. Deve preservar mobile-first, acessibilidade e internacionalização desde o início.

### Epic 2: Produto, modalidade e configuração antes do carrinho

A cliente entende cada tipo de produto e configura corretamente lembrancinhas, convites e digitais antes de avançar, com preço/subtotal coerente.

**FRs cobertos:** FR-7, FR-8, FR-9, FR-11, FR-12, FR-13, FR-14

**Notas de implementação:** usa catálogo do Epic 1. Deve diferenciar produto físico personalizado, convite digital personalizado e Produto Digital Pronto, bloqueando carrinho quando configurações obrigatórias faltarem.

### Epic 3: Carrinho, checkout, pagamento, frete e fatura

A cliente compra como visitante, revisa itens, escolhe entrega quando aplicável, informa NIF opcional, paga por parceiro certificado e recebe confirmação correta por modalidade.

**FRs cobertos:** FR-15, FR-16, FR-17, FR-18, FR-19, FR-20, FR-21, FR-22

**Notas de implementação:** usa configurações dos Epics 1 e 2. Deve separar estado financeiro de estado comercial e não liberar briefing, produção ou download por retorno de navegador.

### Epic 4: Área da Cliente, briefing e aprovação de arte

A cliente acessa o pedido com segurança, preenche briefing protegido em passos curtos, acompanha prazos, recebe prévias, pede alterações e aprova a arte.

**FRs cobertos:** FR-23, FR-24, FR-25, FR-26, FR-27, FR-28, FR-29, FR-30, FR-31, FR-34, FR-36

**Notas de implementação:** usa pedido confirmado do Epic 3. Deve implementar briefing mestre, autosave, uploads protegidos, prévias numeradas, aprovação explícita e notificações acionáveis.

### Epic 5: Entrega digital, produção física, QA e rastreio

A cliente recebe Produto Digital Pronto imediatamente, recebe convite final aprovado, acompanha produção física, foto privada de QA e rastreio quando disponível.

**FRs cobertos:** FR-32, FR-33, FR-35, FR-43, FR-44

**Notas de implementação:** usa pedido, arquivos privados e aprovação dos Epics 3 e 4. Deve tratar entrega digital imediata, produção física, checklist de QA, foto informativa e rastreamento integrado/manual.

### Epic 6: Suporte Online e Projeto Exclusivo

A cliente consegue pedir ajuda sem sair do site, receber respostas cadastradas, escalar para humano/chat/WhatsApp e solicitar Projeto Exclusivo sem pagamento imediato.

**FRs cobertos:** FR-10, FR-37, FR-38, FR-39, FR-40

**Notas de implementação:** pode iniciar após Epic 1. Deve manter WhatsApp dentro do fluxo de suporte e Projeto Exclusivo, preservar contexto e evitar respostas automáticas fora da base curada.

### Epic 7: Administração, exceções, operação e métricas

Sharom administra catálogo, pedidos, exceções, pagamentos, faturas, fretes, papéis, auditoria e indicadores operacionais sem planilhas paralelas.

**FRs cobertos:** FR-41, FR-42, FR-45, FR-46, FR-47, FR-48, FR-49, FR-50

**Notas de implementação:** consolida a operação sobre os fluxos criados nos épicos anteriores. Deve usar o mesmo agregado de Pedido, mesma máquina de estados, RBAC, auditoria e métricas.

## Epic 1: Loja pública, catálogo e descoberta mobile-first

A cliente consegue entrar no site, entender a JS Designs, buscar produtos/temas, navegar por categorias, ver “Mais procurados” e encontrar páginas públicas indexáveis.

### Story 1.1: Acessar a loja com base visual e navegação global

Como cliente visitante,  
quero acessar a loja com identidade JS Designs, navegação clara, busca, conta e carrinho,  
para conseguir começar a compra pelo celular sem depender de atendimento.

**Acceptance Criteria:**

**Given** que a cliente abre a loja em celular a partir de 320 px  
**When** a página inicial carrega  
**Then** ela vê logo JS Designs, busca, acesso à Área da Cliente, carrinho e menu em um cabeçalho compacto  
**And** todos os controles têm alvo mínimo de toque de 44 x 44 px.

**Given** que a cliente navega por teclado ou leitor de tela  
**When** passa pelo cabeçalho, menu, busca, conta e carrinho  
**Then** a ordem de foco segue a leitura visual  
**And** cada ícone tem nome acessível e foco visível.

**Given** que a aplicação é iniciada como greenfield  
**When** a base técnica é criada  
**Then** ela usa Next.js App Router com TypeScript, estrutura mobile-first e módulos iniciais coerentes com a arquitetura  
**And** regras de negócio não ficam presas em componentes visuais.

**Given** que a cliente acessa a loja em português, inglês ou espanhol  
**When** muda o idioma  
**Then** a página atual permanece no mesmo contexto  
**And** carrinho, pedido, preço e prazo não são alterados pela troca de idioma.

**Given** que a cliente usa navegador desktop, tablet ou celular suportado  
**When** acessa a home  
**Then** a navegação permanece funcional em toque, mouse e teclado  
**And** a experiência não exige app nativo nem site separado.

### Story 1.2: Ver produtos mais procurados logo na entrada

Como cliente visitante,  
quero ver produtos específicos e mais procurados logo no início da home,  
para encontrar rapidamente uma lembrancinha ou item relevante sem precisar navegar muito.

**Acceptance Criteria:**

**Given** que a cliente acessa a home pelo celular  
**When** a primeira dobra ou início da rolagem é exibido  
**Then** a seção “Mais procurados” aparece cedo com produtos específicos  
**And** a seção não fica escondida depois de um hero longo.

**Given** que um produto mais procurado é personalizado  
**When** a cliente toca no card  
**Then** ela é levada para a página própria do produto  
**And** o produto não é adicionado direto ao carrinho.

**Given** que um produto mais procurado é Produto Digital Pronto  
**When** o card é exibido  
**Then** ele mostra selo claro de produto digital, download imediato e compatibilidade Silhouette Studio quando aplicável  
**And** o CTA usa “Comprar agora”.

**Given** que a cliente usa leitor de tela  
**When** navega pelos cards de produtos  
**Then** cada card informa nome, modalidade, preço ou faixa de preço, prazo/entrega e ação disponível.

**Given** que a cliente acessa a home em desktop  
**When** visualiza “Mais procurados”  
**Then** a grade pode usar mais colunas  
**And** nenhuma informação essencial disponível no celular é removida.

### Story 1.3: Buscar por produto, tema, ocasião, tipo e personagem

Como cliente visitante,  
quero pesquisar usando palavras do meu jeito,  
para encontrar produtos por tema, ocasião, personagem, estilo ou tipo sem depender do WhatsApp.

**Acceptance Criteria:**

**Given** que a cliente abre a busca  
**When** digita produto, tema, personagem, ocasião, tipo ou grafia alternativa  
**Then** o sistema retorna produtos relevantes quando existirem  
**And** diferencia resultados exatos de semelhantes.

**Given** que não há resultado exato  
**When** a busca retorna vazia  
**Then** o termo pesquisado é preservado  
**And** a tela mostra sugestões semelhantes, categorias principais e CTA para Projeto Exclusivo.

**Given** que a cliente pesquisa um tema de convite que ainda não tem exemplar publicado  
**When** há tipos/modelos de convite disponíveis  
**Then** o sistema orienta a escolher o tipo/modelo e informar o tema no pré-formulário  
**And** não força Projeto Exclusivo apenas pela ausência do tema publicado.

**Given** que a busca atualiza resultados dinamicamente  
**When** a lista muda, fica vazia ou carrega  
**Then** a mudança é comunicada de forma acessível sem excesso de anúncio para leitor de tela.

**Given** que filtros/chips são usados em celular  
**When** a cliente aplica ou remove filtros  
**Then** os controles funcionam por toque e teclado  
**And** não dependem de hover.

### Story 1.4: Navegar por categorias e catálogo estruturado

Como cliente visitante,  
quero navegar por categorias claras e produtos bem organizados,  
para comparar opções de lembrancinhas, kits, convites e digitais sem confusão.

**Acceptance Criteria:**

**Given** que Sharom cadastra um produto no catálogo  
**When** informa os dados do produto  
**Then** o sistema exige natureza/modalidade, categoria, fotos, preço, quantidade ou variante, tema demonstrado, ocasião, personalização, prazo, materiais, composição, disponibilidade e tipo de entrega.

**Given** que o produto usa personagem ou ativo protegido  
**When** Sharom tenta publicar o produto  
**Then** o sistema exige registro de licença, autorização ou verificação de direito de uso comercial  
**And** impede publicação quando o status de direito de uso estiver pendente ou rejeitado.

**Given** que a cliente navega pela loja  
**When** abre categorias como Lembrancinhas, Kits, Convites ou Produtos Digitais Prontos  
**Then** vê listagens consistentes com filtros relevantes  
**And** cada item mostra modalidade de forma clara.

**Given** que a cliente alterna idioma na listagem  
**When** os produtos são renderizados  
**Then** nomes, descrições, metadados e SEO aparecem no idioma escolhido quando disponíveis  
**And** fallback editorial controlado evita mistura silenciosa de idiomas.

**Given** que a cliente usa celular  
**When** navega por listagens e categorias  
**Then** os cards permanecem legíveis, tocáveis e comparáveis  
**And** a página não usa anúncios de terceiros nem ruído promocional externo.

### Story 1.5: Publicar páginas com SEO essencial e conteúdo localizado

Como cliente que chega pelo Google ou link externo,  
quero abrir páginas públicas claras e no idioma correto,  
para entender rapidamente se a JS Designs tem o produto ou tema que procuro.

**Acceptance Criteria:**

**Given** que uma página pública de produto, categoria ou tema existe  
**When** ela é publicada  
**Then** ela possui título, descrição, URL estável, canonical quando aplicável e metadados localizados  
**And** o conteúdo principal é compreensível sem depender de imagem.

**Given** que a cliente chega por busca externa em uma página de produto  
**When** a página abre  
**Then** ela vê diretamente o contexto relevante do produto  
**And** consegue seguir para configuração, compra ou Projeto Exclusivo conforme a modalidade.

**Given** que a página está em português, inglês ou espanhol  
**When** o conteúdo localizado não existe para algum campo  
**Then** o sistema usa fallback editorial controlado  
**And** não mistura idiomas de forma silenciosa em informações críticas de preço, prazo, entrega ou política.

**Given** que filtros, busca ou estados transacionais existem  
**When** mecanismos de busca acessam o site  
**Then** páginas privadas, estados de checkout, briefing, Área da Cliente e filtros infinitos não são indexados.

**Given** que a página usa fotos reais de produtos  
**When** imagens carregam lentamente ou falham  
**Then** a página mantém texto alternativo adequado, layout estável e caminho de compra utilizável.

## Epic 2: Produto, modalidade e configuração antes do carrinho

A cliente entende cada tipo de produto e configura corretamente lembrancinhas, convites e digitais antes de avançar, com preço/subtotal coerente.

### Story 2.1: Entender uma página de produto físico personalizado

Como cliente interessada em lembrancinhas físicas,  
quero ver uma página própria do produto com informações claras antes de configurar,  
para decidir se aquele item serve para minha festa.

**Acceptance Criteria:**

**Given** que a cliente abre uma página de produto físico personalizado  
**When** a página carrega  
**Then** ela vê fotos reais, nome do produto, preço ou faixa de preço, quantidade mínima, material, acabamento, conteúdo, prazo, entrega e personalização disponível  
**And** existe um resumo compacto próximo ao CTA.

**Given** que a cliente ainda não configurou o produto  
**When** toca no CTA principal  
**Then** o CTA diz “Personalizar e comprar”  
**And** leva para a página própria de personalização, não direto ao carrinho.

**Given** que o produto possui personalização opcional ou obrigatória  
**When** a página explica o processo  
**Then** a cliente entende quais dados serão pedidos antes do carrinho e quais fotos/referências serão enviadas depois no briefing protegido.

**Given** que a página é vista em celular  
**When** a cliente rola o conteúdo  
**Then** preço, prazo e próxima ação continuam fáceis de encontrar  
**And** textos e botões cabem a partir de 320 px.

**Given** que a cliente usa teclado ou leitor de tela  
**When** percorre galeria, resumo e CTA  
**Then** a ordem de foco é lógica  
**And** imagens têm texto alternativo útil quando transmitirem informação do produto.

### Story 2.2: Configurar lembrancinha física com quantidade, dados e miniatura

Como cliente comprando lembrancinhas personalizadas,  
quero escolher quantidade, tema, data, nome/texto e miniatura antes do carrinho,  
para ver o preço correto e enviar um pedido completo.

**Acceptance Criteria:**

**Given** que a cliente abre a página de personalização de uma lembrancinha  
**When** a tela carrega  
**Then** a quantidade aparece cedo na jornada  
**And** o preço unitário/subtotal é atualizado quando a quantidade muda.

**Given** que a cliente tenta adicionar ao carrinho  
**When** quantidade, tema, nome/texto ou data obrigatória não foram preenchidos  
**Then** o sistema bloqueia a ação  
**And** mostra erros textuais próximos aos campos correspondentes.

**Given** que a cliente escolhe miniatura/personagem criada pela JS Designs  
**When** o subtotal é recalculado  
**Then** o valor adicional aparece claramente  
**And** a interface informa que a miniatura será cobrada uma única vez por pedido/personagem.

**Given** que a cliente já tem miniatura incluída no mesmo pedido  
**When** configura outro item compatível  
**Then** o sistema mostra “Miniatura incluída neste pedido”  
**And** não duplica a cobrança.

**Given** que a cliente possui referências ou fotos para a miniatura  
**When** está na configuração pré-carrinho  
**Then** a tela informa que fotos e referências serão enviadas depois no briefing protegido  
**And** não exige upload sensível antes do pagamento.

**Given** que a cliente quer acrescentar instrução breve  
**When** preenche “outras observações”  
**Then** a observação é salva no resumo do item  
**And** permanece editável no carrinho.

### Story 2.3: Comparar e configurar convite digital personalizado

Como cliente procurando convite digital personalizado,  
quero escolher o tipo/modelo e informar o tema antes do carrinho,  
para comprar o convite correto com prazo e preço claros.

**Acceptance Criteria:**

**Given** que a cliente abre a área de convites digitais personalizados  
**When** os modelos/tipos são exibidos  
**Then** cada opção mostra diferenças visuais e escritas  
**And** informa recursos, prazo, preço, dados necessários e se é Convite Padrão ou Convite Complexo.

**Given** que a cliente escolhe um tipo/modelo  
**When** inicia a configuração  
**Then** o formulário coleta tema em texto livre, cores, data, horário, endereço quando aplicável e dados principais do convite  
**And** a ausência de exemplar publicado do tema não bloqueia a compra.

**Given** que o convite permite miniatura/personagem  
**When** a cliente escolhe contratar criação da miniatura  
**Then** o adicional aparece no preço  
**And** a cobrança é única por pedido/personagem e reutilizável em lembrancinhas compatíveis.

**Given** que a cliente tenta avançar sem dados obrigatórios  
**When** toca em adicionar ao carrinho  
**Then** o sistema bloqueia a ação  
**And** mostra mensagens próximas aos campos incompletos.

**Given** que o convite escolhido é Padrão  
**When** o resumo é exibido  
**Then** a cliente vê prazo de até 24 horas após pagamento e dados completos.

**Given** que o convite escolhido é Complexo  
**When** o resumo é exibido  
**Then** a cliente vê que precisará concluir briefing protegido  
**And** o prazo é de até 48 horas após briefing completo.

### Story 2.4: Comprar Produto Digital Pronto sem confundir com personalização

Como cliente que compra arquivo digital pronto,  
quero entender que o produto é digital, imediato e compatível com Silhouette Studio,  
para comprar sem esperar briefing, aprovação ou atendimento.

**Acceptance Criteria:**

**Given** que a cliente abre uma página de Produto Digital Pronto  
**When** a página carrega  
**Then** ela vê selo claro de “Produto digital”, “Download imediato” e compatibilidade com Silhouette Studio  
**And** a página não promete edição no Canva.

**Given** que a cliente lê a página  
**When** consulta os detalhes do produto  
**Then** vê quais arquivos serão entregues, condições de uso, licença, ausência de personalização e ausência de aprovação de arte.

**Given** que a cliente decide comprar  
**When** toca no CTA principal  
**Then** o botão diz “Comprar agora”  
**And** leva para carrinho/checkout sem pedir briefing ou dados de personalização.

**Given** que Produto Digital Pronto aparece em área mista da loja  
**When** é exibido em card, busca ou “Mais procurados”  
**Then** mantém rótulo forte de produto digital  
**And** não usa linguagem visual ou CTA de convite personalizado.

**Given** que a cliente usa leitor de tela  
**When** navega pela página  
**Then** modalidade, entrega imediata, compatibilidade e ausência de personalização são anunciadas em texto acessível.

### Story 2.5: Aplicar desconto progressivo, complementos e cupom manual

Como cliente montando um pedido,  
quero ver desconto por quantidade, complementos relevantes e campo para cupom,  
para saber o total correto antes de pagar.

**Acceptance Criteria:**

**Given** que um produto possui desconto progressivo por quantidade  
**When** a cliente altera a quantidade  
**Then** o sistema recalcula automaticamente preço unitário, subtotal e desconto progressivo aplicável  
**And** a mudança de subtotal é anunciada de forma acessível.

**Given** que a cliente recebeu um cupom por cadastrar o e-mail  
**When** está no carrinho ou checkout  
**Then** existe um campo para inserir o código do cupom  
**And** o desconto de primeira compra só é aplicado se o código válido for informado.

**Given** que a cliente não informa o cupom recebido  
**When** finaliza a compra  
**Then** o desconto do cupom não é aplicado  
**And** o desconto progressivo por quantidade continua funcionando normalmente quando aplicável.

**Given** que desconto progressivo e cupom manual são aplicáveis ao mesmo pedido  
**When** o total é calculado  
**Then** o sistema aplica a regra comercial configurada para combinação ou não combinação dos descontos  
**And** informa claramente qual desconto foi aplicado.

**Given** que existem complementos relevantes do mesmo tema ou ocasião  
**When** a cliente visualiza produto ou carrinho  
**Then** o sistema pode sugerir dois ou três complementos  
**And** as sugestões não bloqueiam a compra nem parecem obrigatórias.

**Given** que a cliente adiciona complemento compatível com miniatura já paga  
**When** o subtotal é atualizado  
**Then** a miniatura não é cobrada novamente  
**And** o resumo informa quais itens reutilizam a miniatura.

## Epic 3: Carrinho, checkout, pagamento, frete e fatura

A cliente compra como visitante, revisa itens, escolhe entrega quando aplicável, informa NIF opcional, paga por parceiro certificado e recebe confirmação correta por modalidade.

### Story 3.1: Revisar carrinho editável por modalidade

Como cliente com itens no carrinho,  
quero revisar e editar produtos físicos, convites e digitais em um único pedido,  
para confirmar quantidade, personalização, miniatura e subtotal antes do checkout.

**Acceptance Criteria:**

**Given** que a cliente adicionou produtos físicos personalizados, convites ou digitais prontos  
**When** abre o carrinho  
**Then** cada item aparece com modalidade, nome, quantidade, configuração relevante, subtotal e ação de editar/remover  
**And** o carrinho aceita vários produtos no mesmo pedido.

**Given** que um item possui miniatura/personagem compartilhada  
**When** o carrinho exibe o resumo  
**Then** a miniatura aparece como adicional único do pedido  
**And** os itens que reutilizam a miniatura são indicados.

**Given** que a cliente altera quantidade ou configuração de item editável  
**When** volta ao carrinho  
**Then** subtotal, descontos progressivos e resumo do pedido são recalculados pelo servidor  
**And** nenhum dado já preenchido é perdido sem confirmação.

**Given** que a cliente possui cupom de primeira compra recebido por e-mail  
**When** informa o código no campo de cupom  
**Then** o carrinho valida o código  
**And** mostra se foi aplicado, recusado, expirado ou incompatível.

**Given** que a cliente não informa cupom  
**When** avança para checkout  
**Then** nenhum desconto de cupom é aplicado  
**And** descontos progressivos válidos continuam aplicados automaticamente.

**Given** que a cliente remove item do carrinho  
**When** a remoção afeta miniatura compartilhada, complemento ou desconto  
**Then** o sistema recalcula o total  
**And** informa claramente o que mudou.

### Story 3.2: Calcular frete para produtos físicos no checkout

Como cliente comprando produto físico,  
quero informar o endereço e ver frete, prazo estimado e disponibilidade antes de pagar,  
para saber o total real da compra.

**Acceptance Criteria:**

**Given** que o carrinho contém pelo menos um produto físico  
**When** a cliente chega ao checkout  
**Then** o sistema solicita endereço de entrega antes de finalizar o pagamento  
**And** calcula disponibilidade, custo de frete e previsão de envio para os 27 países da União Europeia, com opções de envio normal e envio rápido quando configuradas.

**Given** que o carrinho contém apenas produto digital pronto ou convite digital sem entrega física  
**When** a cliente chega ao checkout  
**Then** o sistema não exige endereço de entrega física  
**And** não adiciona frete ao total.

**Given** que o endereço informado está fora da cobertura automática de frete  
**When** o sistema valida o país/região  
**Then** a cliente é orientada a abrir Suporte Online para avaliação manual  
**And** o checkout não promete preço ou prazo automático.

**Given** que o frete é calculado  
**When** o resumo do pedido é exibido  
**Then** o total separa subtotal dos produtos, descontos, miniatura, frete e total final  
**And** separa prazo de produção do prazo estimado da transportadora, indicando se a modalidade escolhida permite rastreio.

**Given** que a cliente altera endereço, país ou itens físicos  
**When** o checkout recalcula o pedido  
**Then** frete e previsão são atualizados  
**And** a mudança é comunicada antes do pagamento.

**Given** que há falha temporária no cálculo de frete  
**When** a cliente tenta prosseguir  
**Then** o sistema mostra estado seguro e recuperável  
**And** não permite pagamento com frete desconhecido para produto físico.

**Given** que envio normal e envio rápido estão disponíveis para o endereço informado  
**When** a cliente escolhe uma modalidade  
**Then** o checkout atualiza preço, prazo estimado e expectativa de rastreio  
**And** preserva a escolha no pedido.

### Story 3.3: Finalizar checkout como visitante com fatura e consentimentos

Como cliente pronta para comprar,  
quero concluir contato, entrega, fatura, consentimentos e revisão em uma página,  
para pagar sem criar conta antes.

**Acceptance Criteria:**

**Given** que a cliente chega ao checkout  
**When** informa seus dados  
**Then** o checkout aceita compra como visitante  
**And** não exige senha ou cadastro prévio.

**Given** que a cliente deseja incluir NIF na fatura  
**When** marca “Desejo incluir NIF na fatura”  
**Then** o campo de NIF é exibido e validado conforme regra configurada  
**And** o dado aparece no resumo fiscal antes do pagamento.

**Given** que a cliente não deseja incluir NIF  
**When** deixa a opção desmarcada  
**Then** o checkout continua normalmente  
**And** a venda ainda fica marcada para geração de fatura.

**Given** que o pedido contém Produto Digital Pronto com entrega imediata  
**When** a cliente revisa os aceites obrigatórios antes de pagar  
**Then** o sistema apresenta ciência clara sobre download/acesso imediato e regras de desistência quando aplicável  
**And** registra a aceitação ativa exigida antes de liberar a compra.

**Given** que a cliente informa e-mail para compra  
**When** existem autorizações opcionais como newsletter/desconto, portfólio/divulgação ou reutilização de arquivos  
**Then** cada autorização aparece separada e desmarcada por padrão  
**And** a compra não depende de aceitar marketing, divulgação ou reutilização opcional.

**Given** que a autorização de portfólio/divulgação é exibida  
**When** a cliente decide autorizar  
**Then** o sistema registra data/hora, versão do texto aceito, canais permitidos e nível de anonimização  
**And** explica que convites serão publicados apenas com local, data, horário e dados sensíveis modificados/anônimos, enquanto lembrancinhas e miniaturas podem aparecer sem modificação quando autorizado.

**Given** que a cliente revisa o pedido  
**When** o checkout mostra o total final  
**Then** aparecem itens, descontos, cupom manual se aplicado, miniatura, frete quando houver, NIF/fatura e canal de contato  
**And** dados editáveis podem ser corrigidos antes do pagamento.

### Story 3.4: Pagar por parceiro certificado com confirmação segura

Como cliente no checkout,  
quero pagar por um parceiro certificado,  
para concluir a compra com segurança sem expor dados sensíveis de cartão à JS Designs.

**Acceptance Criteria:**

**Given** que a cliente escolhe método de pagamento disponível  
**When** inicia o pagamento  
**Then** o pagamento é processado por parceiro certificado  
**And** dados completos de cartão e códigos de segurança não transitam nem ficam armazenados na aplicação JS Designs.

**Given** que o parceiro retorna a cliente ao site após o pagamento  
**When** a página de retorno carrega  
**Then** o sistema mostra estado de pagamento aguardando confirmação quando necessário  
**And** não libera briefing, produção ou download apenas pelo retorno do navegador.

**Given** que o webhook de pagamento confirma pagamento aprovado  
**When** o evento é processado  
**Then** `payment_state` muda para confirmado por comando idempotente  
**And** o pedido avança para a próxima etapa correta conforme a modalidade dos itens.

**Given** que o webhook é repetido ou chega fora de ordem  
**When** o sistema processa o evento  
**Then** não duplica pedido, cobrança, fatura, miniatura, notificação ou entrega  
**And** registra o evento com correlação ao pedido.

**Given** que há falha, disputa, cancelamento ou reembolso  
**When** o provedor envia evento ou Sharom registra conciliação  
**Then** o pedido entra em estado seguro ou fila de exceção  
**And** a cliente não recebe promessa incompatível com o estado financeiro.

### Story 3.5: Reservar pedido por transferência bancária com comprovante e prorrogação

Como cliente que prefere transferência bancária,  
quero receber instruções claras, enviar comprovante e poder pedir mais prazo se necessário,  
para pagar sem perder o pedido configurado imediatamente.

**Acceptance Criteria:**

**Given** que a cliente escolhe transferência bancária  
**When** finaliza o checkout  
**Then** o pedido fica em “Aguardando pagamento”  
**And** a reserva inicial expira em 48 horas se não houver confirmação.

**Given** que o pedido está aguardando transferência  
**When** a cliente vê a confirmação  
**Then** a tela mostra instruções de pagamento, prazo de 48 horas, referência do pedido e próxima ação  
**And** deixa claro que briefing, produção e downloads só liberam após confirmação real do pagamento.

**Given** que a cliente possui comprovante de transferência  
**When** envia o comprovante pela Área da Cliente ou suporte autorizado  
**Then** o pedido muda para “Comprovante recebido”  
**And** o sistema registra arquivo, data/hora, origem do envio e vínculo com o pedido.

**Given** que o pedido está em “Comprovante recebido”  
**When** Sharom confere o banco e confirma recebimento real  
**Then** o sistema permite confirmação manual do pagamento  
**And** registra quem confirmou, data/hora, observação opcional e evidência/auditoria.

**Given** que o comprovante foi enviado mas o dinheiro não entrou ou está divergente  
**When** Sharom analisa o pedido  
**Then** o pagamento não é confirmado  
**And** o pedido permanece aguardando pagamento ou entra em exceção com motivo visível.

**Given** que o pagamento ainda não foi confirmado e a reserva está próxima do fim  
**When** a cliente acessa a Área da Cliente ou link seguro do pedido  
**Then** ela pode solicitar prorrogação por mais 48 horas  
**And** o sistema registra data/hora da prorrogação.

**Given** que a cliente já usou a prorrogação disponível  
**When** tenta prorrogar novamente  
**Then** o sistema não prorroga automaticamente  
**And** oferece contato com Suporte Online para avaliação manual.

**Given** que o pagamento por transferência é confirmado manualmente ou por integração  
**When** a confirmação é registrada  
**Then** o sistema muda `payment_state` para confirmado por comando idempotente  
**And** o pedido avança conforme a modalidade dos itens.

**Given** que a reserva expira sem confirmação após o prazo disponível  
**When** a rotina de expiração roda  
**Then** o pedido é marcado como expirado/cancelado conforme regra configurada  
**And** estoque, capacidade ou janela reservada são liberados quando aplicável.

**Given** que a confirmação é registrada duas vezes  
**When** o sistema processa a repetição  
**Then** não duplica fatura, pedido, notificação ou liberação de briefing/download.

### Story 3.6: Salvar carrinho por 90 dias para cliente com conta

Como cliente que cria ou acessa uma conta,  
quero manter meu carrinho salvo por até 90 dias e receber lembretes se eu autorizar,  
para poder voltar depois sem perder os itens escolhidos.

**Acceptance Criteria:**

**Given** que a cliente está navegando sem conta  
**When** adiciona itens ao carrinho  
**Then** o carrinho é preservado durante a sessão conforme regra técnica disponível  
**And** a cliente pode continuar para checkout sem criar conta.

**Given** que a cliente cria conta ou acessa a Área da Cliente  
**When** possui itens no carrinho  
**Then** o sistema associa o carrinho à conta  
**And** mantém os itens salvos por até 90 dias.

**Given** que o carrinho salvo contém produto, preço, disponibilidade ou prazo que mudou  
**When** a cliente retorna ao carrinho  
**Then** o sistema recalcula os valores atuais pelo servidor  
**And** mostra claramente quais itens mudaram antes do checkout.

**Given** que a cliente autoriza lembretes de carrinho por e-mail ou WhatsApp  
**When** o carrinho permanece abandonado  
**Then** o sistema pode enviar notificações sobre os itens salvos  
**And** respeita consentimento separado, canal autorizado e opção de cancelar lembretes.

**Given** que a cliente não autoriza lembretes  
**When** abandona o carrinho  
**Then** o carrinho pode permanecer salvo por 90 dias se houver conta  
**And** nenhuma notificação promocional é enviada.

**Given** que o prazo de 90 dias expira  
**When** a rotina de limpeza executa  
**Then** o carrinho salvo é removido ou anonimizado conforme política de retenção  
**And** o sistema registra a execução sem conservar conteúdo eliminado.

### Story 3.7: Confirmar compra e criar acesso seguro à Área da Cliente

Como cliente que acabou de pagar,  
quero receber uma confirmação clara e acesso seguro ao meu pedido,  
para saber exatamente o próximo passo e como isso afeta os prazos.

**Acceptance Criteria:**

**Given** que o pagamento foi confirmado para pedido com item personalizado  
**When** a tela de confirmação é exibida  
**Then** a cliente vê “Pedido confirmado” e a próxima ação  
**And** o CTA principal é “Preencher briefing agora”.

**Given** que a cliente prefere preencher depois  
**When** escolhe “Preencher depois”  
**Then** o sistema informa onde retomar o briefing  
**And** avisa claramente que a primeira arte para aprovação só ficará pronta depois do briefing preenchido e entregue.

**Given** que o pedido contém produto físico personalizado  
**When** a cliente lê os próximos passos  
**Then** o sistema informa que a produção física só começa depois da arte aprovada  
**And** explica que o prazo de produção conta após a Aprovação Final.

**Given** que o pedido contém apenas Produto Digital Pronto  
**When** o pagamento é confirmado  
**Then** a confirmação informa entrega imediata por e-mail  
**And** oferece acesso à Área da Cliente para re-download.

**Given** que o pedido está aguardando confirmação financeira  
**When** a cliente chega à página de confirmação  
**Then** a tela mostra estado “Aguardando pagamento” ou equivalente  
**And** não exibe CTA de briefing, produção ou download ainda.

**Given** que o sistema cria ou associa acesso pós-compra  
**When** envia código seguro ou magic link  
**Then** o link/código permite acessar apenas pedidos autorizados  
**And** impede enumeração por número de pedido e e-mail.

**Given** que a confirmação dispara notificações  
**When** e-mail ou WhatsApp falha  
**Then** a Área da Cliente continua sendo o registro principal  
**And** a falha fica detectável para operação.

## Epic 4: Área da Cliente, briefing e aprovação de arte

A cliente acessa o pedido com segurança, preenche briefing protegido em passos curtos, acompanha prazos, recebe prévias, pede alterações e aprova a arte.

### Story 4.1: Acessar Área da Cliente com linha do tempo do pedido

Como cliente com pedido confirmado,  
quero acessar uma área protegida com estado, próxima ação e prazo,  
para acompanhar o pedido sem depender de mensagem manual.

**Acceptance Criteria:**

**Given** que a cliente recebeu código seguro ou magic link  
**When** acessa a Área da Cliente  
**Then** vê apenas pedidos autorizados para aquele acesso  
**And** o sistema impede enumeração por número de pedido ou e-mail.

**Given** que a cliente abre um pedido  
**When** a linha do tempo é exibida  
**Then** ela vê Estado do Pedido, motivo, Próxima Ação, responsável e data prevista  
**And** etapas concluídas, atuais e futuras são visualmente distintas.

**Given** que o pedido está aguardando pagamento, briefing, aprovação, produção, QA, envio, concluído, pausado ou em exceção  
**When** a cliente consulta a linha do tempo  
**Then** o texto explica o estado em linguagem clara  
**And** mostra o que precisa acontecer para avançar.

**Given** que a cliente usa celular  
**When** consulta a Área da Cliente  
**Then** a linha do tempo é legível e operável a partir de 320 px  
**And** ações principais ficam fáceis de tocar.

**Given** que e-mail ou WhatsApp falha  
**When** a cliente acessa a Área da Cliente  
**Then** a informação atual do pedido continua disponível  
**And** a Área da Cliente permanece como registro principal.

### Story 4.2: Preencher briefing mestre em passos curtos

Como cliente com item personalizado pago,  
quero preencher um briefing protegido em etapas curtas,  
para enviar todas as informações necessárias sem repetir dados.

**Acceptance Criteria:**

**Given** que o pedido possui lembrancinha, convite complexo ou outro item personalizado que exige briefing  
**When** a cliente abre o briefing  
**Then** existe um único briefing mestre por pedido/evento  
**And** ele mostra seções condicionais conforme os itens comprados.

**Given** que a cliente informa dados compartilhados do evento  
**When** preenche nome, tema, data, textos, preferências e observações  
**Then** esses dados podem ser reaproveitados pelos itens compatíveis do mesmo pedido  
**And** o sistema evita pedir a mesma informação duas vezes sem necessidade.

**Given** que a cliente avança entre etapas  
**When** preenche parcialmente o briefing  
**Then** o sistema salva rascunho automaticamente  
**And** mostra progresso, etapas pendentes e confirmação de salvamento.

**Given** que a cliente tenta enviar briefing incompleto  
**When** existem campos obrigatórios pendentes  
**Then** o envio é bloqueado  
**And** os erros aparecem próximos aos campos, com orientação clara.

**Given** que a cliente precisa enviar referências  
**When** adiciona arquivos ou links  
**Then** pode enviar múltiplos anexos e múltiplos links  
**And** vê progresso, sucesso, falha, remover e tentar novamente.

**Given** que a cliente envia fotos/referências de criança ou miniatura  
**When** conclui o upload  
**Then** os arquivos ficam privados na Área da Cliente  
**And** o sistema registra finalidade, vínculo com pedido/item e autorização necessária.

### Story 4.3: Pausar prazos e lembrar briefing pendente

Como cliente que ainda não terminou o briefing,  
quero receber avisos claros de que o pedido depende da minha ação,  
para entender que os prazos só começam depois do briefing completo.

**Acceptance Criteria:**

**Given** que o pagamento foi confirmado e o briefing é obrigatório  
**When** a cliente ainda não enviou o briefing completo  
**Then** o pedido permanece em “Aguardando briefing”  
**And** criação de arte, produção e entrega não começam.

**Given** que o briefing está incompleto  
**When** a Área da Cliente mostra o pedido  
**Then** a próxima ação é atribuída à cliente  
**And** o sistema informa que o prazo da primeira arte começa apenas após o briefing completo e entregue.

**Given** que o briefing permanece pendente  
**When** chega o momento configurado de lembrete  
**Then** o sistema envia lembrete acionável por canal consentido  
**And** o lembrete aponta para o briefing protegido.

**Given** que a cliente conclui o briefing  
**When** envia todas as etapas obrigatórias  
**Then** o pedido sai de “Aguardando briefing”  
**And** o prazo da primeira arte é calculado conforme a modalidade.

**Given** que a cliente atrasou o briefing  
**When** o pedido calcula prazos  
**Then** o tempo aguardando a cliente fica registrado como pausa  
**And** a operação consegue ver início, fim, motivo e responsável pela pausa.

### Story 4.4: Receber prévia numerada da arte

Como cliente aguardando personalização,  
quero receber uma prévia numerada quando a arte estiver pronta,  
para revisar a versão correta antes de aprovar ou pedir alteração.

**Acceptance Criteria:**

**Given** que o briefing está completo ou os dados necessários do convite padrão foram entregues  
**When** Sharom envia a primeira arte  
**Then** o sistema cria uma prévia numerada e imutável  
**And** vincula a prévia ao pedido, item, autora e data/hora.

**Given** que a prévia fica disponível  
**When** o sistema envia notificações  
**Then** a cliente recebe aviso por e-mail e WhatsApp quando consentido/aplicável  
**And** o aviso leva para a Área da Cliente protegida.

**Given** que a cliente abre a prévia  
**When** consulta a tela de aprovação  
**Then** vê a imagem/arquivo em bom tamanho, número da versão, item relacionado e prazo/impacto da decisão  
**And** a interface mostra as ações “Aprovar arte” e “Pedir alteração”.

**Given** que existem prévias anteriores  
**When** a cliente consulta o histórico  
**Then** consegue ver versões numeradas anteriores  
**And** nenhuma versão antiga pode ser confundida com a versão atualmente pendente.

**Given** que a prévia contém arquivo privado  
**When** a cliente acessa o arquivo  
**Then** o acesso ocorre por autorização e link temporário/controlado  
**And** outra cliente não consegue acessar o arquivo por URL previsível.

### Story 4.5: Pedir alteração com texto, imagem ou ambos

Como cliente revisando uma prévia,  
quero pedir alteração usando texto, marcação na imagem ou ambos,  
para explicar claramente o que precisa mudar antes da aprovação.

**Acceptance Criteria:**

**Given** que a cliente está na tela de uma prévia pendente  
**When** escolhe “Pedir alteração”  
**Then** o sistema abre formulário com campo de texto livre  
**And** oferece marcação em imagem como opção adicional, não obrigatória.

**Given** que a cliente envia uma alteração  
**When** confirma o pedido de alteração  
**Then** o sistema registra uma Rodada de Alteração consolidada  
**And** vincula texto, marcações, anexos opcionais, autora, data/hora e versão da prévia.

**Given** que a cliente ainda possui rodadas gratuitas  
**When** registra alteração  
**Then** o contador de até três Rodadas de Alteração gratuitas é atualizado  
**And** a tela informa quantas restam.

**Given** que a cliente tenta enviar quarta alteração gratuita  
**When** o limite já foi usado  
**Then** o sistema bloqueia alteração gratuita automática  
**And** encaminha para exceção/avaliação humana com explicação de possível custo ou prazo.

**Given** que a cliente revisa a alteração antes de enviar  
**When** há texto, marcações ou arquivos  
**Then** ela consegue confirmar ou voltar para editar  
**And** o sistema não perde os dados preenchidos.

**Given** que a alteração é enviada  
**When** o pedido muda de estado  
**Then** ele entra em “Alteração solicitada”  
**And** a próxima ação passa para Sharom.

### Story 4.6: Aprovar arte final e registrar autorização de publicação

Como cliente revisando a arte final,  
quero aprovar deliberadamente a versão correta e escolher se autorizo divulgação,  
para liberar produção/entrega com segurança e controlar o uso público do meu pedido.

**Acceptance Criteria:**

**Given** que a cliente está vendo a prévia pendente  
**When** toca em “Aprovar arte”  
**Then** o sistema mostra confirmação deliberada antes de finalizar  
**And** lista dados críticos para revisão, como nome/texto, tema, data e item relacionado.

**Given** que a cliente confirma a aprovação  
**When** a ação é registrada  
**Then** o sistema salva Aprovação Final com versão, autora, data/hora e confirmações críticas  
**And** mostra mensagem forte: “Arte aprovada para produção.”

**Given** que o item aprovado é produto físico  
**When** a Aprovação Final é registrada  
**Then** o pedido pode avançar para “Aprovado para produção”  
**And** o prazo de produção física passa a contar após essa aprovação.

**Given** que o item aprovado é convite digital personalizado  
**When** a Aprovação Final é registrada  
**Then** o pedido pode avançar para entrega digital do convite final  
**And** a cliente escolhe receber por e-mail ou WhatsApp, mantendo acesso na Área da Cliente.

**Given** que a cliente ainda não decidiu sobre divulgação/portfólio  
**When** confirma a arte ou conclui o briefing  
**Then** o sistema oferece autorização opcional e separada para publicação  
**And** a opção aparece desmarcada por padrão.

**Given** que a cliente autoriza divulgação  
**When** o sistema registra a autorização  
**Then** salva canais permitidos, data/hora, versão do texto aceito e nível de anonimização  
**And** informa que convites só serão publicados com local, data, horário e dados sensíveis modificados/anônimos.

**Given** que a autorização envolve lembrancinhas ou miniaturas  
**When** a cliente autoriza publicação  
**Then** o sistema permite registrar que esses itens podem aparecer sem modificação  
**And** mantém possibilidade de restringir ou retirar autorização futura.

**Given** que a cliente não autoriza divulgação  
**When** a aprovação da arte é concluída  
**Then** a compra, produção e entrega continuam normalmente  
**And** o pedido fica marcado como não autorizado para portfólio/divulgação.

### Story 4.7: Enviar notificações acionáveis e entregar convite aprovado

Como cliente acompanhando um pedido personalizado,  
quero receber avisos úteis e escolher o canal de entrega do convite,  
para agir no momento certo e receber o arquivo final no canal preferido.

**Acceptance Criteria:**

**Given** que ocorre mudança relevante no pedido  
**When** o sistema envia notificação  
**Then** a mensagem informa Estado do Pedido, Próxima Ação e impacto no prazo  
**And** usa apenas canais consentidos ou necessários ao serviço.

**Given** que a primeira arte fica pronta  
**When** a notificação é enviada  
**Then** a cliente recebe aviso por e-mail e WhatsApp quando aplicável  
**And** o link leva para a prévia protegida na Área da Cliente.

**Given** que a cliente aprova um convite digital personalizado  
**When** escolhe canal de entrega  
**Then** pode selecionar e-mail, WhatsApp ou ambos conforme opção disponível  
**And** o arquivo final também fica associado ao pedido na Área da Cliente.

**Given** que a entrega por WhatsApp foi escolhida  
**When** o sistema envia ou registra o envio  
**Then** o histórico do pedido preserva canal, data/hora e status  
**And** dados sensíveis só são enviados conforme regra/consentimento aplicável.

**Given** que uma notificação falha  
**When** e-mail ou WhatsApp não é entregue  
**Then** o pedido não muda incorretamente de estado  
**And** a falha fica visível para operação com possibilidade de nova tentativa.

**Given** que a cliente acessa a Área da Cliente sem receber notificação  
**When** abre o pedido  
**Then** encontra a mesma próxima ação e os mesmos arquivos disponíveis  
**And** não depende do canal externo para concluir a jornada.

## Epic 5: Entrega digital, produção física, QA e rastreio

A cliente recebe Produto Digital Pronto imediatamente, recebe convite final aprovado, acompanha produção física, foto privada de QA e rastreio quando disponível.

### Story 5.1: Entregar Produto Digital Pronto imediatamente após pagamento confirmado

Como cliente que comprou Produto Digital Pronto,  
quero receber o arquivo automaticamente após o pagamento confirmado,  
para usar o produto sem esperar atendimento ou briefing.

**Acceptance Criteria:**

**Given** que o pedido contém Produto Digital Pronto e pagamento confirmado  
**When** o sistema processa a confirmação  
**Then** envia automaticamente e-mail com acesso ao arquivo adquirido  
**And** libera o arquivo na Área da Cliente.

**Given** que o pagamento ainda está pendente ou aguardando confirmação  
**When** a cliente acessa a confirmação ou Área da Cliente  
**Then** o arquivo não é liberado  
**And** a tela mostra o estado financeiro correto.

**Given** que o arquivo é entregue  
**When** a cliente acessa o link  
**Then** o acesso usa link temporário/protegido  
**And** outra cliente não consegue acessar o arquivo por URL previsível.

**Given** que o e-mail não chega  
**When** a cliente entra na Área da Cliente por código/link seguro  
**Then** pode gerar novo link temporário para re-download  
**And** o sistema registra a tentativa.

**Given** que a cliente visualiza o produto adquirido  
**When** consulta os detalhes  
**Then** vê licença, compatibilidade com Silhouette Studio e ausência de personalização  
**And** não há etapa de briefing, prévia ou aprovação para Produto Digital Pronto.

**Given** que o pedido contém convite digital personalizado  
**When** o pagamento é confirmado  
**Then** o sistema não trata o convite como entrega imediata  
**And** mantém o fluxo de edição/criação por tema, dados do cliente, prévia e aprovação antes da entrega final.

### Story 5.2: Gerar ficha de produção a partir da arte aprovada

Como Sharom preparando um produto físico,  
quero uma ficha de produção vinculada à arte aprovada,  
para produzir a encomenda correta sem depender de conversa ou planilha.

**Acceptance Criteria:**

**Given** que um produto físico recebeu Aprovação Final  
**When** o pedido avança para “Aprovado para produção”  
**Then** o sistema gera ou disponibiliza Ficha de Produção  
**And** a ficha aponta para a versão exata da arte aprovada.

**Given** que Sharom abre a Ficha de Produção  
**When** consulta os detalhes  
**Then** vê itens, quantidades, materiais, acabamento, miniatura, tema, nome/texto, endereço, prazo e observações relevantes  
**And** tudo está ligado ao mesmo `order_id`.

**Given** que existe tentativa de produzir usando versão diferente da aprovada  
**When** a ficha é carregada  
**Then** o sistema impede ou sinaliza divergência crítica  
**And** não permite marcar produção como iniciada com versão incorreta.

**Given** que a produção começa  
**When** Sharom executa a ação de iniciar produção  
**Then** o pedido muda para “Em produção”  
**And** o prazo de sete dias corridos passa a ser exibido na Área da Cliente.

**Given** que o pedido possui múltiplos itens físicos  
**When** a ficha é exibida  
**Then** cada item aparece separado com quantidade e instruções próprias  
**And** dados compartilhados do briefing são reaproveitados sem duplicação confusa.

### Story 5.3: Registrar QA com foto privada informativa

Como Sharom finalizando uma encomenda física,  
quero concluir uma verificação de qualidade e anexar foto privada,  
para registrar que o produto está pronto antes do envio.

**Acceptance Criteria:**

**Given** que um pedido físico está em produção  
**When** Sharom conclui a produção  
**Then** o pedido pode avançar para “Verificação de qualidade”  
**And** a próxima ação passa para checklist de QA.

**Given** que Sharom abre o checklist de QA  
**When** verifica a encomenda  
**Then** consegue marcar itens de conferência configurados, como quantidade, acabamento, arte aprovada, embalagem e dados de envio  
**And** pode registrar observação interna quando necessário.

**Given** que a encomenda passa na verificação  
**When** Sharom anexa a foto final privada  
**Then** o sistema salva a foto vinculada ao pedido  
**And** a Área da Cliente exibe a foto como informativa, sem pedir nova aprovação.

**Given** que a cliente vê a foto de QA  
**When** abre a Área da Cliente  
**Then** entende que a foto é apenas informativa  
**And** não vê botão de aprovar/reprovar nessa etapa.

**Given** que a foto de QA contém produto personalizado, miniatura ou dado sensível  
**When** o arquivo é armazenado  
**Then** ele permanece privado e protegido por autorização  
**And** segue a política de retenção definida.

**Given** que a verificação identifica erro ou retrabalho  
**When** Sharom registra falha  
**Then** o pedido entra em exceção ou retrabalho  
**And** a cliente recebe informação clara quando houver impacto no prazo.

### Story 5.4: Registrar modalidade de envio e rastreio quando disponível

Como cliente aguardando uma encomenda física,  
quero escolher/entender a modalidade de envio e receber rastreio quando ela permitir,  
para acompanhar a entrega com expectativas corretas.

**Acceptance Criteria:**

**Given** que o pedido físico passou pelo QA  
**When** Sharom prepara o envio  
**Then** o pedido muda para “Preparando para envio”  
**And** a Área da Cliente mostra a modalidade escolhida no checkout: envio normal ou envio rápido.

**Given** que Sharom posta nos CTT ou em transportadora  
**When** registra os dados de envio  
**Then** salva modalidade, transportadora, data de postagem e código/link quando existir  
**And** o pedido muda para “Enviado”.

**Given** que a modalidade/transportadora permite rastreio  
**When** a cliente abre o painel de rastreio  
**Then** vê transportadora, código/link e instrução clara de acompanhamento  
**And** recebe notificação acionável por canal consentido.

**Given** que a modalidade escolhida não permite rastreio, como pode ocorrer no envio normal  
**When** a cliente consulta o pedido  
**Then** o sistema mostra que aquela entrega não possui rastreio disponível  
**And** não apresenta isso como erro.

**Given** que o rastreio ainda não foi gerado  
**When** a cliente consulta o pedido antes do registro  
**Then** o sistema mostra “Rastreio ainda não disponível”  
**And** explica que o código aparecerá ali se a modalidade/transportadora permitir.

**Given** que o endereço ou envio entra em exceção  
**When** Sharom registra problema de logística  
**Then** o pedido entra na fila de exceções  
**And** a cliente recebe explicação e próxima ação quando necessário.

**Given** que o pedido é entregue ou finalizado  
**When** Sharom ou integração registra conclusão  
**Then** o pedido muda para “Concluído”  
**And** o histórico preserva eventos de envio, modalidade e rastreio quando houver.

## Epic 6: Suporte Online e Projeto Exclusivo

A cliente consegue pedir ajuda sem sair do site, receber respostas cadastradas, escalar para humano/chat/WhatsApp e solicitar Projeto Exclusivo sem pagamento imediato.

### Story 6.1: Abrir suporte online contextual sem tornar WhatsApp obrigatório

Como cliente navegando na loja ou Área da Cliente,  
quero abrir suporte online contextual dentro do site,  
para tirar dúvidas sem abandonar a jornada de compra ou acompanhamento.

**Acceptance Criteria:**

**Given** que a cliente está navegando na loja, produto, carrinho, checkout ou Área da Cliente  
**When** abre o Suporte Online  
**Then** o chat aparece de forma compacta e contextual  
**And** não há botão flutuante permanente de WhatsApp competindo com carrinho, compra ou aprovação.

**Given** que a cliente abre o suporte em uma página de pedido  
**When** inicia a conversa  
**Then** o sistema pode anexar contexto consentido, como pedido, página atual, item ou estado  
**And** não envia dados sensíveis ao WhatsApp sem regra e consentimento adequados.

**Given** que a cliente usa celular  
**When** conversa pelo chat  
**Then** o widget não cobre ações críticas da página  
**And** pode ser fechado e reaberto sem perder a conversa.

**Given** que a cliente usa teclado ou leitor de tela  
**When** abre, navega e fecha o suporte  
**Then** foco, nomes acessíveis e ordem de navegação funcionam corretamente  
**And** o foco retorna ao ponto anterior ao fechar.

**Given** que o suporte está indisponível tecnicamente  
**When** a cliente navega pela loja  
**Then** busca, carrinho, checkout, briefing, aprovação e Área da Cliente continuam funcionando  
**And** a falha fica detectável para operação.

### Story 6.2: Responder automaticamente apenas com conteúdo cadastrado

Como cliente com uma dúvida comum,  
quero receber uma resposta confiável no chat,  
para continuar a compra ou acompanhamento sem esperar atendimento humano quando a resposta já existir.

**Acceptance Criteria:**

**Given** que existe resposta cadastrada, ativa e aplicável à dúvida  
**When** a cliente pergunta no suporte  
**Then** o Atendimento Automático retorna apenas o conteúdo aprovado  
**And** não inventa resposta sobre preço, prazo, política ou pedido privado.

**Given** que não há resposta cadastrada aplicável  
**When** a cliente pergunta no suporte  
**Then** o sistema informa que não encontrou resposta confiável  
**And** oferece atendimento humano por chat ou WhatsApp dentro do fluxo.

**Given** que Sharom administra a base de suporte  
**When** cadastra ou edita uma resposta  
**Then** informa pergunta/intenção, resposta, idioma, status, data de revisão e responsável  
**And** pode ativar ou desativar a resposta.

**Given** que a loja tem português, inglês e espanhol  
**When** a cliente usa suporte no idioma escolhido  
**Then** o sistema usa resposta cadastrada no mesmo idioma quando existir  
**And** não mistura idiomas silenciosamente.

**Given** que uma resposta envolve etapa de pedido  
**When** é exibida no chat  
**Then** ela usa termos consistentes como briefing, aprovação, produção, QA, rastreio e fatura  
**And** aponta para a próxima ação correta quando houver contexto.

### Story 6.3: Escalar atendimento humano por chat ou WhatsApp

Como cliente que não resolveu a dúvida automaticamente,  
quero continuar com atendimento humano pelo canal que eu escolher,  
para receber ajuda sem repetir todo o contexto.

**Acceptance Criteria:**

**Given** que o Atendimento Automático não encontrou resposta confiável  
**When** oferece atendimento humano  
**Then** a cliente pode escolher continuar no chat ou ir para WhatsApp  
**And** WhatsApp aparece apenas dentro desse fluxo, não como botão flutuante permanente.

**Given** que a cliente escolhe atendimento humano no chat  
**When** a conversa é encaminhada  
**Then** Sharom recebe histórico, página/pedido relacionado quando autorizado e estado da conversa  
**And** pode responder pelo painel.

**Given** que a cliente escolhe WhatsApp  
**When** o encaminhamento é iniciado  
**Then** o sistema preserva contexto consentido de forma apropriada  
**And** não envia briefing, prévias, fotos ou dados sensíveis sem regra e consentimento.

**Given** que a solicitação ocorre fora do horário humano  
**When** a cliente pede atendimento  
**Then** o sistema informa horário de atendimento humano de segunda a sábado, 8h às 20h no horário de Portugal continental  
**And** informa retorno em até 1 dia útil.

**Given** que a conversa é respondida  
**When** Sharom finaliza ou marca como resolvida  
**Then** o histórico fica vinculado ao cliente/pedido quando aplicável  
**And** a cliente consegue retomar compra ou acompanhamento.

**Given** que a cliente indica que a resposta não resolveu  
**When** o sistema registra feedback  
**Then** a conversa pode voltar para fila humana  
**And** a base automática não é alterada sem revisão de Sharom.

### Story 6.4: Solicitar Projeto Exclusivo sem pagamento imediato

Como cliente que não encontrou exatamente o que precisa,  
quero enviar uma solicitação de Projeto Exclusivo,  
para receber avaliação humana antes de qualquer pagamento.

**Acceptance Criteria:**

**Given** que a cliente acessa Projeto Exclusivo pela home, busca sem resultado, produto ou suporte  
**When** abre o formulário  
**Then** vê que a solicitação não exige pagamento imediato  
**And** entende que o aceite depende de avaliação humana.

**Given** que a cliente preenche o formulário  
**When** informa os dados principais  
**Then** o sistema coleta nome, WhatsApp ou e-mail, tipo de produto desejado, data do evento, tema/ideia e outras observações  
**And** permite referências/anexos e orçamento aproximado como opcionais.

**Given** que a cliente adiciona referências  
**When** envia arquivos ou links  
**Then** o sistema aceita múltiplos anexos/links conforme limites configurados  
**And** mostra sucesso, falha, remover e tentar novamente.

**Given** que a cliente tenta enviar sem contato ou dados mínimos  
**When** clica em enviar  
**Then** o sistema bloqueia envio  
**And** mostra erros próximos aos campos obrigatórios.

**Given** que a solicitação é enviada  
**When** a confirmação aparece  
**Then** informa recebimento, canal de retorno e resposta humana em até 1 dia útil  
**And** não promete aceite automático.

**Given** que Sharom recebe a solicitação  
**When** abre no painel  
**Then** vê dados, referências, origem da solicitação e histórico de suporte relacionado quando existir  
**And** pode marcar como aceita, recusada, precisa de mais informações ou convertida em proposta/pedido.

## Epic 7: Administração, exceções, operação e métricas

Sharom administra catálogo, pedidos, exceções, pagamentos, faturas, fretes, papéis, auditoria e indicadores operacionais sem planilhas paralelas.

### Story 7.1: Visualizar fila administrativa por próxima ação

Como Sharom administrando a loja,  
quero ver pedidos organizados por próxima ação, responsável, prazo e prioridade,  
para saber o que precisa ser feito sem usar planilha paralela.

**Acceptance Criteria:**

**Given** que Sharom acessa o painel administrativo  
**When** abre a fila de pedidos  
**Then** vê pedidos agrupados por próxima ação, como aguardando pagamento, aguardando briefing, criar arte, aguardando aprovação, alteração solicitada, aprovado para produção, QA, envio e exceções  
**And** cada item mostra responsável, prazo restante, prioridade e estado.

**Given** que há pedidos pausados por ação da cliente  
**When** a fila é exibida  
**Then** o sistema mostra motivo da pausa, início da pausa e responsável  
**And** não mistura atraso da cliente com atraso operacional.

**Given** que há pedidos próximos do prazo  
**When** Sharom consulta a fila  
**Then** o sistema destaca prioridade conforme regra configurada  
**And** permite filtrar por modalidade, data, estado e responsável.

**Given** que Sharom abre um item da fila  
**When** seleciona o pedido  
**Then** vai para a visão consolidada do pedido  
**And** todas as ações continuam ligadas ao mesmo `order_id`.

**Given** que um usuário sem permissão administrativa tenta acessar a fila  
**When** solicita a página  
**Then** o acesso é negado  
**And** a tentativa é registrada para auditoria quando aplicável.

### Story 7.2: Consultar visão consolidada do pedido

Como Sharom analisando um pedido,  
quero ver todo o histórico comercial, financeiro, criativo e operacional em uma tela,  
para decidir a próxima ação sem reconstruir contexto em mensagens externas.

**Acceptance Criteria:**

**Given** que Sharom abre um pedido no admin  
**When** a visão consolidada carrega  
**Then** ela vê cliente, itens, pagamento, briefing, arquivos, miniatura, prévias, aprovação, produção ou entrega digital, fatura, envio, notificações e suporte  
**And** tudo está vinculado ao mesmo `order_id`.

**Given** que o pedido possui miniatura/personagem compartilhada  
**When** Sharom consulta os itens  
**Then** vê quais itens usam a miniatura  
**And** confirma que a cobrança ocorreu uma única vez por pedido/personagem.

**Given** que o pedido possui briefing mestre  
**When** Sharom consulta respostas e uploads  
**Then** vê dados compartilhados e seções por item  
**And** consegue identificar quais respostas/arquivos alimentam cada arte ou produto.

**Given** que há prévias e alterações  
**When** Sharom consulta o histórico criativo  
**Then** vê versões numeradas, pedidos de alteração, contador de rodadas e versão aprovada  
**And** a versão aprovada aparece como referência oficial.

**Given** que existem fatura, frete, rastreio ou exceções  
**When** Sharom consulta a visão consolidada  
**Then** vê estado atual, histórico e próximos passos  
**And** não precisa usar planilha paralela como fonte de verdade.

**Given** que Sharom executa uma ação sensível no pedido  
**When** salva a ação  
**Then** o sistema registra autoria, data/hora, mudança feita e motivo quando aplicável.

### Story 7.3: Administrar catálogo, preços, temas e conteúdo comercial

Como Sharom gerenciando a loja,  
quero cadastrar e manter produtos, categorias, preços, disponibilidade e traduções,  
para publicar a loja sem depender de alterações manuais no código.

**Acceptance Criteria:**

**Given** que Sharom cria ou edita um produto  
**When** salva o cadastro  
**Then** pode administrar categoria, modalidade, nome, descrição, fotos, preço, variantes, quantidades, materiais, acabamento, prazo, disponibilidade, tipo de entrega e status de publicação  
**And** o sistema valida campos obrigatórios conforme modalidade.

**Given** que o produto é convite  
**When** Sharom define o cadastro  
**Then** pode informar família/tipo/modelo, formato, recursos, classificação Padrão/Complexo, prazo, preço e dados necessários  
**And** pode indicar que temas demonstrados são exemplos e não limitam tema livre.

**Given** que o produto é Produto Digital Pronto  
**When** Sharom define o cadastro  
**Then** pode informar compatibilidade Silhouette Studio, arquivos entregues, licença, ausência de personalização e entrega imediata  
**And** o produto não usa campos de briefing/aprovação.

**Given** que o produto usa tema, personagem ou ativo protegido  
**When** Sharom tenta publicar  
**Then** o sistema exige status de licença/autorização de uso comercial  
**And** impede publicação quando a autorização estiver ausente, pendente ou rejeitada.

**Given** que Sharom cadastra preços por quantidade ou descontos  
**When** salva regras comerciais  
**Then** o sistema permite configurar descontos progressivos, complementos, cupons e disponibilidade  
**And** deixa claro quais descontos são automáticos e quais exigem código manual.

**Given** que a loja opera em três idiomas  
**When** Sharom edita conteúdo comercial  
**Then** pode cadastrar traduções em pt-BR, inglês e espanhol  
**And** o sistema mostra pendências de tradução antes de publicar.

### Story 7.4: Tratar exceções operacionais com decisão explícita

Como Sharom operando pedidos com situações fora do fluxo normal,  
quero uma fila de exceções com motivos e ações controladas,  
para resolver casos especiais sem alterar silenciosamente o pedido.

**Acceptance Criteria:**

**Given** que ocorre quarta alteração, urgência, pagamento pendente, erro de produção, retrabalho, falha de integração, pedido pausado, endereço fora da UE ou problema de mídia  
**When** o sistema detecta ou Sharom registra o caso  
**Then** o pedido entra na fila de exceções  
**And** o motivo fica visível e vinculado ao `order_id`.

**Given** que Sharom abre uma exceção  
**When** analisa o caso  
**Then** vê tipo, impacto em preço, prazo, produção, pagamento ou comunicação  
**And** pode escolher uma ação permitida conforme papel e estado do pedido.

**Given** que a exceção é urgência  
**When** Sharom avalia a possibilidade  
**Then** o sistema permite registrar decisão humana, adicional de 30% com mínimo de €10 quando aceita e impacto no prazo  
**And** impede reordenar fila se isso causar atraso confirmado em pedidos anteriores.

**Given** que a exceção envolve quarta alteração  
**When** Sharom decide cobrar ou aceitar manualmente  
**Then** o sistema registra decisão, valor/prazo quando aplicável e comunicação para cliente  
**And** não reinicia o contador de alterações gratuitas.

**Given** que a exceção envolve falha de pagamento, fatura, frete, e-mail, WhatsApp, upload ou antimalware  
**When** o sistema registra a falha  
**Then** ela fica correlacionada ao pedido  
**And** aparece como acionável para operação.

**Given** que a exceção é resolvida  
**When** Sharom finaliza a ação  
**Then** o pedido volta ao estado correto da máquina de estados  
**And** o histórico preserva motivo, decisão, autora e data/hora.

### Story 7.5: Administrar pagamentos, faturas, fretes e conciliação

Como Sharom controlando a operação financeira e logística,  
quero consultar pagamentos, faturas, fretes, rastreamentos e divergências,  
para manter cada pedido correto e auditável.

**Acceptance Criteria:**

**Given** que Sharom abre a área financeira/logística  
**When** consulta pedidos  
**Then** vê estado financeiro, método de pagamento, tentativas, confirmação, fatura, NIF quando informado, frete, transportadora e rastreio quando houver  
**And** cada registro está vinculado ao `order_id`.

**Given** que um pagamento por transferência aguarda confirmação  
**When** Sharom abre o pedido  
**Then** vê prazo restante, comprovante quando enviado, estado “Comprovante recebido” quando aplicável e opção de confirmar manualmente após conferir entrada real no banco  
**And** a ação de confirmar exige registro de autoria e data/hora.

**Given** que existe divergência de pagamento, valor, fatura ou reembolso  
**When** o sistema detecta ou Sharom registra  
**Then** cria item acionável de conciliação  
**And** impede liberação indevida de briefing, produção ou download.

**Given** que uma venda exige fatura  
**When** o pagamento é confirmado  
**Then** o sistema gera ou enfileira emissão de fatura de forma idempotente  
**And** mantém a fatura disponível por e-mail e Área da Cliente.

**Given** que o pedido físico possui envio normal ou rápido  
**When** Sharom consulta logística  
**Then** vê modalidade escolhida, preço cobrado, prazo estimado, transportadora e expectativa de rastreio  
**And** pode registrar postagem, código/link quando existir ou ausência de rastreio conforme modalidade.

**Given** que há falha em fatura, frete, rastreio ou notificação logística  
**When** a falha é registrada  
**Then** aparece como exceção ou pendência acionável  
**And** o pedido não muda para estado incompatível.

**Given** que a cliente vê problema, dúvida ou exceção no pedido  
**When** consulta Área da Cliente, pagamento, fatura, frete ou rastreio  
**Then** sempre existe opção clara de chamar Suporte Online para avaliação  
**And** o suporte recebe contexto do pedido quando a cliente autoriza.

### Story 7.6: Controlar papéis administrativos e auditoria

Como dona da operação,  
quero limitar permissões por papel e auditar ações sensíveis,  
para proteger pedidos, arquivos, pagamentos e dados das clientes.

**Acceptance Criteria:**

**Given** que Sharom acessa o admin  
**When** executa ações administrativas  
**Then** possui papel com permissões completas configuradas  
**And** ações sensíveis exigem sessão autenticada e autorizada.

**Given** que existe Assistente de Produção ou outro papel operacional  
**When** esse usuário acessa o painel  
**Then** vê apenas informações necessárias para sua função  
**And** não acessa pagamento, NIF, fatura, dados sensíveis ou arquivos privados sem necessidade.

**Given** que uma ação sensível é realizada  
**When** altera pedido, pagamento, fatura, aprovação, arquivo, frete, autorização de publicação, catálogo ou permissões  
**Then** o sistema registra autoria, data/hora, antes/depois quando aplicável, motivo e `order_id` ou entidade relacionada.

**Given** que alguém tenta acessar arquivo ou pedido sem autorização  
**When** a solicitação é feita  
**Then** o acesso é negado  
**And** a tentativa relevante fica registrada.

**Given** que Sharom precisa revisar histórico  
**When** consulta auditoria de um pedido ou entidade  
**Then** consegue ver linha do tempo de ações sensíveis  
**And** filtrar por tipo, usuário, data e resultado.

**Given** que MFA administrativo está configurado  
**When** um usuário admin inicia sessão  
**Then** o sistema exige o segundo fator conforme política definida  
**And** bloqueia ou alerta tentativas suspeitas.

### Story 7.7: Medir jornadas, operação e qualidade do serviço

Como Sharom acompanhando o lançamento,  
quero ver métricas de jornada e operação desde o primeiro dia,  
para entender conversão, prazos, gargalos e dependência de suporte.

**Acceptance Criteria:**

**Given** que a cliente usa a loja com consentimento adequado  
**When** navega pelas jornadas principais  
**Then** o sistema registra eventos de descoberta, busca, visualização de produto, configuração, carrinho, início/conclusão de checkout, briefing, aprovação, entrega e suporte  
**And** respeita preferências de privacidade e consentimento.

**Given** que Sharom abre o painel de indicadores  
**When** consulta o período de baseline inicial  
**Then** vê visitas qualificadas, buscas, visualizações, configurações, carrinhos, checkouts concluídos e abandono  
**And** consegue filtrar por idioma, modalidade, origem e dispositivo quando disponível.

**Given** que existem pedidos personalizados  
**When** o painel calcula operação  
**Then** mostra tempo por estado, pedidos pausados, cumprimento de prazo, alterações, retrabalho, falhas e dependência de suporte  
**And** separa tempo da cliente de tempo operacional.

**Given** que há falhas em pagamento, fatura, frete, e-mail, WhatsApp, upload, antimalware, entrega digital ou mudança de estado  
**When** a falha ocorre  
**Then** ela é detectável, correlacionada ao pedido e acionável  
**And** pode gerar exceção quando bloquear a jornada.

**Given** que métricas são exibidas  
**When** Sharom consulta dados agregados  
**Then** não expõem dados pessoais desnecessários  
**And** respeitam retenção e controle de acesso.

**Given** que as metas de 90 dias ainda não foram definidas  
**When** o baseline de 30 dias termina  
**Then** o sistema permite exportar ou consultar dados necessários para definir metas futuras  
**And** preserva contramétricas como acessibilidade, clareza, retrabalho e dependência de suporte.
