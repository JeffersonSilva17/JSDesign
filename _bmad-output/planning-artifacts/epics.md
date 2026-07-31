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
  - architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md
---

# JSDESIGN — Quebra em Épicos e Histórias

## Visão geral

Este documento reinicia a quebra de épicos e histórias da loja online JS Designs com a arquitetura atual: Next.js como Frontend+BFF, Laravel como API de domínio, PostgreSQL como banco transacional principal, Redis para cache/fila, SOLID + Domain Pattern e CI/CD com testes.

## Inventário de requisitos

### Requisitos funcionais

FR-1: A página inicial deve apresentar proposta de valor, busca destacada, categorias essenciais, produtos mais pedidos, prova de acabamento, funcionamento da personalização e acesso discreto a Projeto Exclusivo.

FR-2: A cliente deve acessar Loja, categorias, busca, conta e Carrinho por uma navegação consistente em dispositivos móveis e desktop.

FR-3: O sistema deve permitir que páginas públicas relevantes sejam descobertas e compreendidas por mecanismos de busca.

FR-4: A cliente deve alternar entre português do Brasil, inglês e espanhol e escolher entre moedas suportadas sem perder página, Carrinho ou contexto do Pedido.

FR-5: A operação deve cadastrar produtos com natureza, categoria, fotos, preço, quantidade ou variante, tema demonstrado, ocasião, personalização, prazo, materiais, composição, disponibilidade e tipo de entrega. Personagens ou ativos protegidos só podem ser cadastrados após verificação de direitos de uso comercial.

FR-6: A cliente deve pesquisar por título, produto, tema, personagem, ocasião e grafias alternativas.

FR-7: A página de produto físico deve informar fotos reais, preço, quantidades, material, acabamento, conteúdo, personalização incluída ou opcional, prazo e entrega.

FR-8: A descoberta e a página de convite devem explicar cada Tipo de Convite por demonstração, funcionamento, recursos, diferenciais, preço, prazo, dados necessários, Miniatura e processo de alterações.

FR-9: A página de Produto Digital Pronto deve informar que o produto não inclui personalização, quais arquivos serão entregues, compatibilidade com o Silhouette Studio, condições de uso e entrega imediata após pagamento.

FR-10: A cliente deve iniciar solicitação de Projeto Exclusivo quando desejar formato, recurso ou escopo fora das opções configuradas; ausência de tema de convite no catálogo não exige avaliação exclusiva.

FR-11: A cliente deve escolher quantidade, ativar ou dispensar personalização básica e selecionar opção de Miniatura compatível em produto físico.

FR-12: A cliente deve antecipar Tipo de Convite, tema em texto livre, cores, data, horário, endereço e opção de Miniatura antes de pagar.

FR-13: O Carrinho pode oferecer dois ou três produtos complementares do mesmo tema ou ocasião.

FR-14: Quando desconto progressivo por quantidade, desconto de conjunto ou cupom forem aplicáveis, o sistema deve recalcular e aplicar o benefício válido conforme regra comercial, sem acumulação indevida.

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

FR-33: Sharom deve registrar transportadora e código ou link de rastreamento quando houver; a cliente deve recebê-los e consultá-los na Área da Cliente.

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

NFR-1: Jornadas essenciais devem atender WCAG 2.2 AA, incluindo teclado, foco visível, nomes acessíveis, contraste, mensagens de erro compreensíveis, zoom, redução de movimento e leitores de tela.

NFR-2: Em conexão móvel representativa do público europeu, páginas públicas e etapas de compra devem carregar e responder sem que fotografias, vídeos, fontes ou scripts bloqueiem a tarefa principal; aceite quantitativo deve seguir Core Web Vitals bons no 75º percentil por dispositivo.

NFR-3: Falhas de chat, analytics, newsletter ou mídia complementar não devem impedir busca, Carrinho, Checkout, Briefing, Aprovação ou acesso ao Pedido; falhas críticas devem apresentar estado seguro e permitir retomada.

NFR-4: Repetição de confirmação, webhook, clique ou trabalho assíncrono não pode duplicar cobrança, Pedido, benefício, Miniatura, Fatura, notificação ou entrega.

NFR-5: O lançamento deve demonstrar controles proporcionais ao OWASP ASVS nível 2, revisão de código, análise de dependências, testes automatizados de segurança, bloqueio de upload malicioso, pentest antes da produção e correção de achados críticos/altos.

NFR-6: Dados completos de cartão e códigos de segurança não devem transitar nem ser armazenados pela aplicação da JS Designs; processamento deve permanecer no parceiro certificado e no escopo PCI DSS aplicável.

NFR-7: Coleta, uso, compartilhamento, retenção e eliminação de dados devem seguir RGPD por design, com mapa de tratamentos, fluxo de direitos, fornecedores documentados, avaliação de impacto quando aplicável, processo de violação de dados e exclusão/revisão automática por prazo.

NFR-8: Briefings, Miniaturas, fotografias de crianças, Prévias e fotos de qualidade devem permanecer privados, protegidos contra enumeração/acesso não autorizado e eliminados conforme política de retenção.

NFR-9: Dados críticos devem ter cópias protegidas, restauração testada, procedimento de resposta a incidentes e evidência de recuperação dos fluxos críticos.

NFR-10: Português do Brasil, inglês e espanhol devem cobrir interface, conteúdo comercial, mensagens transacionais, e-mails, formulários, políticas, suporte cadastrado e SEO; datas, endereços, moedas e números devem ser apresentados sem ambiguidade.

NFR-11: Falhas em pagamento, Fatura, frete, e-mail, WhatsApp, upload, antimalware, entrega digital e mudança de estado devem ser detectáveis, correlacionadas ao Pedido e acionáveis pela operação.

NFR-12: O site deve ser responsivo e preservar jornadas essenciais em computador, tablet e celular, com suporte formal às duas versões estáveis mais recentes de Chrome, Safari, Edge e Firefox em desktop, Safari em iPhone/iPad e Chrome em Android; interface funcional a partir de 320 px.

### Requisitos adicionais de arquitetura e operação

AR-1: Implementar Next.js como Frontend+BFF e Laravel como API/backend de domínio.

AR-2: O navegador deve falar prioritariamente com Next.js; Next.js chama Laravel por contratos server-side/controlados.

AR-3: BFF não pode conter regra de negócio principal; pode compor dados de tela, cuidar de SSR/SEO, sessão/cookies, cache de leitura e adaptação de payloads.

AR-4: Laravel é autoridade de domínio para carrinho, preço, pedido, pagamento, briefing, aprovação, produção, entrega, fatura, suporte, autorização e auditoria.

AR-5: Backend Laravel deve seguir SOLID + Domain Pattern, organizado por módulos com camadas Domain, Application, Infrastructure e Interfaces/Http.

AR-6: Eloquent está incluso como ORM do Laravel, mas restrito à camada Infrastructure/persistência, repositories, queries e migrations; Models Eloquent não devem virar entidades de domínio.

AR-7: PostgreSQL é a fonte transacional de verdade para pedidos, clientes, catálogo, carrinhos, pagamentos, briefing, arquivos, aprovações, suporte, auditoria e métricas.

AR-8: Redis é seed para cache/fila; estado durável de negócio não deve depender de Redis.

AR-9: `order_id` deve conectar cliente, itens, pagamento, briefing, arquivos, prévias, aprovação, produção, entrega digital, fatura, rastreio, suporte, notificações e auditoria.

AR-10: `payment_state` e `order_state` devem ser separados; webhook confirmado ou confirmação manual auditada são autoridade de pagamento confirmado.

AR-11: Fluxo do pedido deve usar máquina de estados explícita com comandos/casos de uso idempotentes no Laravel.

AR-12: Laravel deve expor API REST JSON versionada inicialmente em `/api/v1`, com contratos testados por HTTP tests e documentáveis por OpenAPI.

AR-13: Laravel é autoridade de autenticação/autorização; Next.js BFF guarda sessão/cookies e não expõe segredo server-to-server ao navegador.

AR-14: Arquivos privados são controlados pelo Laravel com metadados no PostgreSQL, storage privado e acesso por autorização/URLs temporárias.

AR-15: Efeitos assíncronos como e-mail, WhatsApp, fatura, webhooks, antimalware, entrega digital, rastreio e lembretes devem rodar em jobs/filas Laravel, de forma idempotente.

AR-16: Precificação, configuração, carrinho, descontos, miniatura e frete são server-owned no Laravel; Next.js apenas exibe e solicita ações.

AR-17: Miniatura/personagem é adicional compartilhado, cobrado uma única vez por pedido/personagem e reutilizável entre itens compatíveis.

AR-18: Desconto progressivo por quantidade é automático; cupom de primeira compra gerado por cadastro de e-mail só aplica se a cliente inserir código válido no carrinho/checkout.

AR-19: Carrinho pode ser salvo por até 90 dias quando a cliente cria/acessa conta; lembretes de carrinho exigem consentimento separado por canal.

AR-20: Transferência bancária deve reservar pedido por 48 horas, permitir prorrogação por mais 48 horas, aceitar envio de comprovante e exigir confirmação manual/bancária real antes de liberar fluxo.

AR-21: Produto físico deve permitir envio normal ou rápido quando configurado; frete é calculado no checkout; rastreio só aparece quando modalidade/CTT/transportadora permitir.

AR-22: Produto Digital Pronto tem entrega imediata após pagamento confirmado; convite digital personalizado não é imediato e exige criação/edição, prévia e aprovação.

AR-23: Briefing pós-pagamento deve ser mestre por pedido/evento, com seções condicionais por modalidade, autosave, progresso e upload protegido.

AR-24: Prévias devem ser numeradas e imutáveis; aprovação final deve registrar versão, autora, data/hora e confirmações críticas.

AR-25: Publicação de trabalhos exige autorização opcional registrada. Convites só podem ser publicados com local, data, horário e dados sensíveis modificados/anônimos; lembrancinhas e miniaturas podem aparecer sem modificação quando autorizadas.

AR-26: Suporte automático deve usar respostas cadastradas/revisadas; WhatsApp fica dentro do fluxo de suporte/projeto/entrega, não como botão flutuante permanente.

AR-27: Admin deve usar a mesma API/domínio Laravel, RBAC, auditoria e filas por próxima ação, sem planilha paralela como fonte de verdade.

AR-28: CI/CD com GitHub Actions deve rodar lint/typecheck/build do frontend, testes Laravel, análise estática quando configurada e Playwright para fluxos críticos antes de merge/deploy.

AR-29: Hospedagem, estratégia exata de auth BFF↔Laravel, storage, pagamento, fatura, e-mail, WhatsApp e transportadora permanecem deferidos atrás de portas/adaptadores.

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

UX-DR35: Implementar controle de autorização de portfólio/divulgação com escolha opcional marcada por padrão e removível pela cliente; para convites, explicar que dados como local, data e horário serão modificados antes da publicação; para lembrancinhas e miniaturas, permitir publicação sem modificação quando autorizada.

### Mapa de cobertura de requisitos

FR-1: Epic 1 — Fundação da plataforma, BFF e experiência pública base.

FR-2: Epic 1 — Fundação da plataforma, BFF e experiência pública base.

FR-3: Epic 1 — Fundação da plataforma, BFF e experiência pública base.

FR-4: Epic 1 — Fundação da plataforma, BFF e experiência pública base.

FR-5: Epic 2 — Catálogo, descoberta e busca orientada por intenção.

FR-6: Epic 2 — Catálogo, descoberta e busca orientada por intenção.

FR-7: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-8: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-9: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-10: Epic 7 — Suporte Online e Projeto Exclusivo.

FR-11: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-12: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-13: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-14: Epic 3 — Produto, modalidade e configuração antes do carrinho.

FR-15: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-16: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-17: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-18: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-19: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-20: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-21: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-22: Epic 4 — Carrinho, checkout, pagamento, frete e fatura.

FR-23: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-24: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-25: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-26: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-27: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-28: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-29: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-30: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-31: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-32: Epic 6 — Entrega digital, produção física, QA e rastreio.

FR-33: Epic 6 — Entrega digital, produção física, QA e rastreio.

FR-34: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-35: Epic 6 — Entrega digital, produção física, QA e rastreio.

FR-36: Epic 5 — Área da Cliente, briefing e aprovação de arte.

FR-37: Epic 7 — Suporte Online e Projeto Exclusivo.

FR-38: Epic 7 — Suporte Online e Projeto Exclusivo.

FR-39: Epic 7 — Suporte Online e Projeto Exclusivo.

FR-40: Epic 7 — Suporte Online e Projeto Exclusivo.

FR-41: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-42: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-43: Epic 6 — Entrega digital, produção física, QA e rastreio.

FR-44: Epic 6 — Entrega digital, produção física, QA e rastreio.

FR-45: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-46: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-47: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-48: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-49: Epic 8 — Administração, exceções, operação, auditoria e métricas.

FR-50: Epic 8 — Administração, exceções, operação, auditoria e métricas.

## Lista de épicos

### Epic 1: Fundação da plataforma, BFF e experiência pública base

A cliente consegue abrir a loja com identidade JS Designs, navegação, idioma, busca inicial e páginas públicas base; o time ganha a fundação técnica mínima com Next.js BFF, Laravel API, PostgreSQL, Redis, Eloquent em Infrastructure e CI/CD.

**FRs cobertos:** FR-1, FR-2, FR-3, FR-4

**Notas de implementação:** inclui scaffold inicial, contratos BFF↔API, layout base, i18n, CI/CD mínimo e testes de smoke. Não cria todas as entidades de domínio antecipadamente.

### Story 1.1: Inicializar a fundação técnica da plataforma

**Requisitos cobertos:** AR-1, AR-2, AR-3, AR-4, AR-5, AR-6, AR-7, AR-8, AR-12, AR-28; NFR-3, NFR-4, NFR-5.

As a equipe de desenvolvimento,
I want uma base funcional com Next.js BFF, Laravel API, PostgreSQL, Redis e pipeline CI/CD inicial,
So that as próximas funcionalidades da loja sejam implementadas sobre uma arquitetura consistente, testável e alinhada ao padrão definido.

**Acceptance Criteria:**

**Given** o repositório da JS Designs
**When** a aplicação for iniciada em ambiente local
**Then** devem existir aplicações separadas para Frontend/BFF em Next.js e Backend/API em Laravel
**And** o Frontend/BFF deve conseguir chamar um endpoint saudável da Laravel API

**Given** a Laravel API
**When** o backend for configurado
**Then** deve usar PostgreSQL como banco principal
**And** deve usar Redis para cache/fila, sem tratar Redis como fonte durável de dados
**And** Eloquent deve ficar restrito à camada de Infrastructure/persistência

**Given** a arquitetura aprovada
**When** o backend for organizado
**Then** deve existir separação inicial por Domain, Application, Infrastructure e Interfaces
**And** regras principais de negócio não devem ser colocadas no BFF

**Given** o pipeline CI/CD
**When** houver push ou pull request
**Then** devem rodar checks mínimos de backend, frontend e testes automatizados iniciais
**And** falhas nesses checks devem bloquear a aprovação técnica

**Given** a base inicial
**When** um dev executar os comandos documentados
**Then** deve conseguir subir a stack local e validar a comunicação Frontend/BFF → Laravel API

### Story 1.2: Criar o layout público base da loja

**Requisitos cobertos:** FR-2; UX-DR1, UX-DR2, UX-DR4, UX-DR5, UX-DR6, UX-DR7, UX-DR9, UX-DR10, UX-DR32, UX-DR33.

As a cliente da JS Designs,
I want acessar uma loja com identidade visual clara, navegação simples e estrutura inicial confiável,
So that eu consiga entender rapidamente que estou em uma loja de personalizados físicos e digitais.

**Acceptance Criteria:**

**Given** a página inicial da loja
**When** a cliente acessar pelo navegador
**Then** deve ver a identidade JS Designs aplicada com logo, cores, tipografia e estilo visual coerente com a UX aprovada
**And** a interface deve priorizar experiência mobile, mantendo adaptação correta para desktop

**Given** a navegação pública
**When** a cliente visualizar o cabeçalho
**Then** deve encontrar acesso para Home, Produtos, Categorias, Buscar, Carrinho, Entrar/Criar conta e Suporte
**And** o botão principal deve conduzir para descoberta/compra de produtos

**Given** o layout base
**When** a cliente rolar a página
**Then** deve existir rodapé com links essenciais, políticas, contato/suporte e informações da JS Designs
**And** não deve existir botão flutuante permanente de WhatsApp fora dos fluxos definidos

**Given** a arquitetura Frontend/BFF
**When** a página pública carregar
**Then** o Next.js deve renderizar a estrutura base
**And** qualquer dado dinâmico necessário deve vir pelo BFF, sem expor segredos ou regras de negócio no navegador

**Given** critérios básicos de qualidade
**When** a página for validada
**Then** deve passar em smoke test de renderização
**And** deve manter estrutura semântica mínima para acessibilidade e SEO

### Story 1.3: Implementar idioma, textos base e estrutura pública em português do Brasil

**Requisitos cobertos:** FR-4; NFR-10; UX-DR3, UX-DR30, UX-DR33.

As a cliente da JS Designs,
I want navegar pela loja com textos claros em português do Brasil,
So that eu entenda os produtos, etapas de compra e próximos passos sem confusão.

**Acceptance Criteria:**

**Given** a loja pública
**When** a cliente acessar qualquer página base
**Then** todos os textos visíveis devem estar em português do Brasil
**And** os arquivos e documentos do projeto devem permanecer em UTF-8

**Given** a estrutura de conteúdo da loja
**When** o time precisar alterar textos de cabeçalho, rodapé, chamadas principais e mensagens informativas
**Then** esses textos devem estar organizados de forma centralizada ou padronizada no frontend
**And** não devem ficar espalhados de forma difícil de manter

**Given** a cliente em fluxo público
**When** encontrar informações sobre produtos físicos, produtos digitais, suporte ou compra
**Then** os textos devem diferenciar claramente produto físico personalizado, produto digital pronto e produto digital personalizado
**And** convites digitais personalizados devem informar que não são entregues imediatamente porque precisam de edição/criação e aprovação

**Given** a experiência mobile-first
**When** os textos forem exibidos em telas pequenas
**Then** devem ser curtos, legíveis e sem quebrar o layout
**And** mensagens mais longas devem aparecer apenas quando fizer sentido no contexto

**Given** a validação técnica
**When** o frontend for testado
**Then** deve existir verificação mínima de renderização dos textos principais
**And** caracteres acentuados devem aparecer corretamente

### Story 1.4: Criar a home pública com entrada para os produtos mais procurados

**Requisitos cobertos:** FR-1, FR-3; UX-DR4, UX-DR8, UX-DR11, UX-DR12.

As a cliente da JS Designs,
I want ver logo na entrada da loja os produtos mais procurados e caminhos claros de compra,
So that eu encontre rapidamente lembrancinhas físicas personalizadas, convites digitais e outros itens relevantes.

**Acceptance Criteria:**

**Given** a cliente acessando a home
**When** a página carregar
**Then** deve existir uma seção inicial com chamada clara para a JS Designs
**And** deve ficar evidente que a loja vende lembrancinhas físicas personalizadas e produtos digitais personalizados/prontos

**Given** a preferência de descoberta definida
**When** a cliente visualizar a primeira dobra da home
**Then** os produtos ou categorias mais procurados devem aparecer logo de cara
**And** devem ter cards com imagem, nome, tipo de produto e chamada para ver detalhes ou comprar

**Given** a cliente procurando produtos específicos
**When** ela usar a entrada de busca ou navegar pelos destaques
**Then** deve conseguir seguir para resultados ou página do produto
**And** a busca não deve depender de cadastro/login

**Given** a arquitetura BFF/API
**When** os produtos mais procurados forem exibidos
**Then** o Next.js deve receber dados pelo BFF
**And** a Laravel API deve ser a fonte dos dados de catálogo, sem regra de catálogo hardcoded no frontend

### Story 1.5: Exibir captura de e-mail com cupom de primeira compra

**Requisitos cobertos:** FR-14; AR-18; UX-DR19.

As a visitante da loja,
I want receber uma oferta de desconto ao cadastrar meu e-mail,
So that eu tenha incentivo para fazer a primeira compra sem perder controle sobre o cupom.

**Acceptance Criteria:**

**Given** uma visitante acessando o site pela primeira vez
**When** a janela de desconto for exibida
**Then** deve haver campo para informar e-mail
**And** deve ficar claro que o desconto depende do uso do cupom no checkout

**Given** um e-mail válido informado
**When** a visitante solicitar o desconto
**Then** o sistema deve gerar ou associar um código de cupom de primeira compra
**And** deve exibir ou enviar o código conforme a configuração definida

**Given** a visitante fechando a janela
**When** ela continuar navegando
**Then** a navegação não deve ser bloqueada
**And** a janela não deve reaparecer de forma agressiva na mesma sessão

**Given** a regra de arquitetura
**When** o cupom for criado ou validado
**Then** a regra deve ficar na Laravel API
**And** o BFF deve apenas intermediar a experiência pública

### Epic 2: Catálogo, descoberta e busca orientada por intenção

A cliente encontra produtos por categoria, tema, personagem, ocasião, tipo e grafias alternativas; Sharom consegue cadastrar catálogo estruturado com licenças/autorização de uso comercial.

**FRs cobertos:** FR-5, FR-6

**Notas de implementação:** Laravel é dono do catálogo/busca; Next.js BFF compõe páginas públicas e SEO.

### Story 2.1: Cadastrar produtos com estrutura de catálogo comercial

**Requisitos cobertos:** FR-5; AR-4, AR-6, AR-7.

As a administradora da JS Designs,
I want cadastrar produtos com dados estruturados de categoria, tipo, tema, ocasião e imagens,
So that a loja consiga exibir e organizar os itens de forma pesquisável.

**Acceptance Criteria:**

**Given** a administradora autenticada
**When** cadastrar um produto
**Then** deve informar nome, descrição, tipo de produto, categoria, status de publicação e imagens
**And** deve poder indicar se o produto é físico personalizado, digital pronto ou digital personalizado

**Given** um produto com tema ou personagem
**When** salvar o cadastro
**Then** o sistema deve permitir registrar tema, personagem, ocasião e termos alternativos de busca
**And** deve permitir registrar observações sobre licença/autorização de uso comercial quando aplicável

**Given** a arquitetura aprovada
**When** o produto for persistido
**Then** a Laravel API deve controlar validação e persistência
**And** Eloquent deve ser usado apenas na camada de Infrastructure

### Story 2.2: Exibir listagens públicas por categoria, ocasião e tipo

**Requisitos cobertos:** FR-3, FR-5; UX-DR11, UX-DR12, UX-DR33.

As a cliente da JS Designs,
I want navegar por categorias, ocasiões e tipos de produto,
So that eu consiga encontrar produtos mesmo sem saber o nome exato.

**Acceptance Criteria:**

**Given** produtos publicados no catálogo
**When** a cliente acessar uma página de categoria, ocasião ou tipo
**Then** deve ver apenas produtos publicados
**And** cada card deve mostrar imagem, nome, tipo e chamada para detalhes

**Given** uma listagem com muitos produtos
**When** a cliente navegar
**Then** deve haver paginação, carregamento incremental ou outro padrão performático
**And** a experiência deve funcionar bem em celular

**Given** produtos digitais e físicos na mesma listagem
**When** os cards forem exibidos
**Then** cada produto digital deve aparecer explicitamente identificado como produto digital
**And** produtos físicos personalizados devem aparecer como personalizados físicos

### Story 2.3: Implementar busca pública por intenção

**Requisitos cobertos:** FR-6, FR-10; UX-DR8, UX-DR31.

As a cliente da JS Designs,
I want buscar produtos por nome, tema, personagem, ocasião ou grafia aproximada,
So that eu encontre produtos específicos rapidamente.

**Acceptance Criteria:**

**Given** a cliente usando a busca
**When** informar um termo
**Then** o sistema deve pesquisar nome, categoria, tema, personagem, ocasião e sinônimos cadastrados
**And** deve retornar resultados relevantes sem exigir login

**Given** uma busca sem resultado
**When** a cliente pesquisar
**Then** deve receber mensagem clara
**And** deve ter caminho para suporte ou pedido de projeto exclusivo quando fizer sentido

**Given** a arquitetura BFF/API
**When** a busca for executada
**Then** a consulta deve ser controlada pela Laravel API
**And** o Next.js deve consumir os resultados via BFF

### Story 2.4: Preparar páginas públicas de catálogo para SEO e compartilhamento

**Requisitos cobertos:** FR-3; NFR-2, NFR-10.

As a cliente que chega pelo Google ou link compartilhado,
I want abrir páginas claras e indexáveis de catálogo,
So that eu entenda rapidamente o produto ou categoria antes de comprar.

**Acceptance Criteria:**

**Given** uma página pública de produto, categoria ou busca
**When** a página carregar
**Then** deve ter título, descrição e metadados adequados
**And** deve usar textos em português do Brasil

**Given** uma página de produto ou categoria
**When** for compartilhada
**Then** deve apresentar prévia coerente com nome, imagem e descrição
**And** não deve expor dados privados ou informações administrativas

**Given** a validação técnica
**When** os testes rodarem
**Then** deve existir verificação mínima das páginas públicas principais
**And** a renderização deve preservar caracteres acentuados

### Epic 3: Produto, modalidade e configuração antes do carrinho

A cliente entende e configura lembrancinhas, convites e produtos digitais antes do carrinho, com preço/subtotal correto, miniatura única e distinção clara entre digital pronto e convite personalizado.

**FRs cobertos:** FR-7, FR-8, FR-9, FR-11, FR-12, FR-13, FR-14

**Notas de implementação:** Laravel controla precificação/configuração; Next.js exibe e coleta.

### Story 3.1: Exibir página própria de produto com diferenças entre modelos

**Requisitos cobertos:** FR-7, FR-8, FR-9; UX-DR11, UX-DR12, UX-DR13, UX-DR15.

As a cliente da JS Designs,
I want abrir uma página própria do produto e comparar os modelos disponíveis,
So that eu escolha corretamente a versão desejada antes de comprar.

**Acceptance Criteria:**

**Given** um produto publicado
**When** a cliente acessar a página do produto
**Then** deve ver galeria, descrição, tipo de produto, informações de produção/entrega e CTA "Comprar agora"
**And** deve ficar claro se o produto é físico, digital pronto ou digital personalizado

**Given** um produto com modelos diferentes
**When** a cliente visualizar as opções
**Then** os modelos devem aparecer com diferenças escritas ou visualmente identificáveis
**And** a cliente deve conseguir escolher o modelo desejado antes do carrinho

**Given** um convite digital personalizado
**When** a página do produto for exibida
**Then** deve informar que não é entregue imediatamente
**And** deve explicar que haverá edição/criação, prévia e aprovação

### Story 3.2: Configurar quantidade e calcular preço antes do carrinho

**Requisitos cobertos:** FR-11, FR-14; AR-16; UX-DR13, UX-DR14, UX-DR31.

As a cliente comprando lembrancinhas ou itens personalizados,
I want escolher a quantidade e ver o preço atualizado,
So that eu entenda o valor antes de adicionar o produto ao carrinho.

**Acceptance Criteria:**

**Given** um produto com preço por quantidade
**When** a cliente selecionar uma quantidade
**Then** o preço/subtotal deve atualizar antes do produto ser adicionado ao carrinho
**And** descontos progressivos por quantidade devem ser aplicados automaticamente quando existirem

**Given** uma quantidade inválida ou abaixo do mínimo
**When** a cliente tentar comprar
**Then** o sistema deve bloquear a ação
**And** deve mostrar mensagem objetiva sobre a quantidade permitida

**Given** a regra de precificação
**When** o preço for calculado
**Then** a Laravel API deve ser a autoridade do cálculo
**And** o frontend não deve conter regra final de preço hardcoded

### Story 3.3: Coletar personalização essencial no adicionar ao carrinho

**Requisitos cobertos:** FR-11, FR-12; UX-DR14, UX-DR15, UX-DR30.

As a cliente comprando produto personalizado,
I want informar tema, data, nome e observações no momento de adicionar ao carrinho,
So that o pedido já tenha as informações mínimas para orientar a produção.

**Acceptance Criteria:**

**Given** um produto personalizado
**When** a cliente clicar em adicionar ao carrinho ou comprar agora
**Then** deve ser solicitado tema, data, nome da pessoa e outras observações quando aplicável
**And** a cliente deve entender que detalhes completos virão no briefing após o pagamento

**Given** a cliente preenchendo os campos
**When** algum campo obrigatório estiver ausente
**Then** o sistema deve indicar o campo pendente
**And** não deve adicionar o item com configuração inválida

**Given** a cliente optando por preencher detalhes depois
**When** essa opção for permitida
**Then** o sistema deve avisar que a primeira arte para aprovação só ficará pronta depois do briefing preenchido e entregue
**And** deve avisar que a produção só começa depois da arte aprovada

### Story 3.4: Oferecer miniatura do aniversariante como adicional pago uma única vez

**Requisitos cobertos:** FR-11, FR-12, FR-14; AR-17; UX-DR16.

As a cliente da JS Designs,
I want escolher se quero miniatura do aniversariante,
So that eu saiba quando esse adicional aumenta o valor do pedido.

**Acceptance Criteria:**

**Given** um produto compatível com miniatura
**When** a cliente configurar o item
**Then** deve haver opção para incluir ou não miniatura do aniversariante
**And** deve ficar claro que a miniatura adiciona valor ao pedido

**Given** a cliente usando a mesma miniatura em convite e lembrancinha no mesmo pedido
**When** o carrinho for calculado
**Then** a miniatura deve ser cobrada apenas uma vez por pedido/personagem
**And** os itens devem referenciar a mesma miniatura quando aplicável

**Given** a regra de cobrança
**When** o subtotal for recalculado
**Then** a Laravel API deve controlar a regra de cobrança única
**And** o frontend deve apenas exibir o resultado retornado

### Story 3.5: Diferenciar produtos digitais prontos e personalizados antes da compra

**Requisitos cobertos:** FR-8, FR-9; AR-22; UX-DR12.

As a cliente interessada em produto digital,
I want saber se o arquivo é pronto ou se precisa ser personalizado,
So that eu tenha expectativa correta sobre entrega e aprovação.

**Acceptance Criteria:**

**Given** um produto digital pronto
**When** a página do produto for exibida
**Then** deve informar que o envio ocorre por e-mail após pagamento confirmado
**And** deve indicar que o arquivo é editável via Silhouette Studio quando aplicável

**Given** um convite digital personalizado
**When** a página do produto for exibida
**Then** deve informar que ele precisa ser editado ou criado com tema e dados solicitados
**And** deve informar que haverá prévia e aprovação antes da entrega final

**Given** páginas de site ou listagem
**When** um produto digital aparecer
**Then** deve haver indicação visível de que é um produto digital
**And** essa indicação deve aparecer sem misturar o público de digital com o público de lembrancinhas físicas quando a navegação estiver segmentada

### Story 3.6: Sugerir produtos complementares por tema ou ocasião

**Requisitos cobertos:** FR-13, FR-14; UX-DR17.

As a cliente configurando ou revisando um produto,
I want ver sugestões complementares relevantes do mesmo tema ou ocasião,
So that eu possa montar um conjunto sem procurar manualmente item por item.

**Acceptance Criteria:**

**Given** um produto com tema, ocasião ou categoria compatível
**When** a cliente visualizar a página do produto ou etapa compatível antes do carrinho
**Then** o sistema pode sugerir dois ou três produtos complementares
**And** as sugestões devem ser claramente opcionais

**Given** produto complementar selecionado
**When** a cliente adicionar o item
**Then** o item deve preservar sua própria configuração no carrinho
**And** descontos de conjunto ou quantidade devem ser recalculados pela Laravel API quando aplicáveis

**Given** nenhuma sugestão relevante
**When** a cliente visualizar a página
**Then** o sistema não deve exibir bloco vazio ou genérico
**And** a experiência principal de compra deve continuar sem interrupção

### Epic 4: Carrinho, checkout, pagamento, frete e fatura

A cliente compra como visitante, revisa itens, aplica cupom manual, escolhe frete normal/rápido quando físico, informa NIF opcional, paga por parceiro certificado ou transferência, e recebe confirmação por modalidade.

**FRs cobertos:** FR-15, FR-16, FR-17, FR-18, FR-19, FR-20, FR-21, FR-22

**Notas de implementação:** inclui carrinho salvo por 90 dias com conta, transferência com comprovante/prorrogação, frete rastreável ou não rastreável conforme modalidade.

### Story 4.1: Gerenciar carrinho com itens configurados e persistência por conta

**Requisitos cobertos:** FR-15; AR-19; UX-DR17.

As a cliente da JS Designs,
I want revisar produtos configurados no carrinho,
So that eu confirme quantidades, adicionais e dados básicos antes do checkout.

**Acceptance Criteria:**

**Given** itens adicionados ao carrinho
**When** a cliente abrir o carrinho
**Then** deve ver produto, modelo, quantidade, personalização resumida, adicionais, subtotal e total
**And** deve conseguir alterar quantidade ou remover item antes do checkout

**Given** uma cliente com conta
**When** ela deixar itens no carrinho
**Then** o carrinho deve poder ficar salvo por até 90 dias
**And** notificações de carrinho devem respeitar consentimento e regras de comunicação

**Given** alterações de preço ou disponibilidade
**When** a cliente retornar ao carrinho
**Then** o sistema deve recalcular valores pela Laravel API
**And** deve informar mudanças relevantes antes do pagamento

### Story 4.2: Aplicar cupom manual e desconto progressivo automático

**Requisitos cobertos:** FR-14; AR-18.

As a cliente com cupom de primeira compra,
I want inserir o código do cupom no checkout,
So that o desconto seja aplicado somente quando eu informar o código.

**Acceptance Criteria:**

**Given** a cliente no carrinho ou checkout
**When** houver campo de cupom
**Then** ela deve poder inserir o código recebido pelo cadastro de e-mail
**And** o desconto do cupom só deve ser aplicado se o código for informado e validado

**Given** produtos com desconto progressivo por quantidade
**When** a cliente selecionar quantidade elegível
**Then** o desconto progressivo deve ser aplicado automaticamente
**And** deve aparecer separado do cupom manual

**Given** cupom inválido, expirado ou já usado
**When** a cliente tentar aplicar
**Then** o sistema deve recusar o cupom
**And** deve manter o desconto progressivo normal quando aplicável

### Story 4.3: Coletar dados de checkout, NIF opcional e consentimentos

**Requisitos cobertos:** FR-16, FR-21; UX-DR18, UX-DR35.

As a cliente finalizando a compra,
I want informar dados essenciais, entrega e fatura opcional,
So that o pedido possa ser confirmado corretamente.

**Acceptance Criteria:**

**Given** a cliente no checkout
**When** preencher os dados
**Then** deve informar dados de contato e dados necessários para entrega física quando houver produto físico
**And** deve poder informar NIF opcional para fatura quando desejar

**Given** autorizações e consentimentos no checkout
**When** a cliente revisar as opções
**Then** autorizações de publicação devem aparecer marcadas por padrão
**And** a cliente deve poder desmarcar se não quiser autorizar

**Given** um pedido com produto digital apenas
**When** o checkout for exibido
**Then** não deve exigir endereço físico desnecessário
**And** deve confirmar o e-mail de entrega digital

### Story 4.4: Calcular frete para produtos físicos com envio normal ou rápido

**Requisitos cobertos:** FR-20; AR-21; UX-DR27, UX-DR31.

As a cliente comprando produto físico,
I want escolher envio normal ou rápido e ver o frete calculado,
So that eu saiba o custo e a expectativa de entrega antes de pagar.

**Acceptance Criteria:**

**Given** o carrinho com produto físico
**When** a cliente informar endereço de entrega
**Then** o sistema deve calcular opções de frete disponíveis
**And** deve permitir escolher envio normal ou rápido quando disponíveis

**Given** uma modalidade sem rastreio
**When** a opção for exibida
**Then** deve informar que o rastreio pode não estar disponível
**And** deve explicar que o rastreio depende dos CTT ou transportadora escolhida

**Given** um carrinho somente digital
**When** a cliente chegar ao checkout
**Then** não deve cobrar frete
**And** deve seguir para entrega digital conforme tipo de produto

### Story 4.5: Confirmar pagamento por parceiro certificado ou transferência bancária

**Requisitos cobertos:** FR-18, FR-19; AR-10, AR-20; NFR-4, NFR-6.

As a cliente da JS Designs,
I want escolher uma forma segura de pagamento,
So that meu pedido avance somente depois da confirmação correta.

**Acceptance Criteria:**

**Given** checkout finalizado
**When** a cliente escolher pagamento por parceiro certificado
**Then** o sistema deve registrar a tentativa de pagamento
**And** deve confirmar o pedido somente após retorno válido do parceiro

**Given** a cliente escolhendo transferência bancária
**When** o pedido for criado
**Then** o sistema deve reservar o pedido por 48 horas
**And** deve permitir prorrogar por mais 48 horas caso o pagamento ainda não tenha sido confirmado

**Given** a cliente enviando comprovante
**When** a administradora analisar o comprovante
**Then** deve ser possível confirmar manualmente o pagamento
**And** a produção ou entrega só deve avançar após confirmação real/manual registrada

### Story 4.6: Enviar confirmação do pedido e próximos passos por modalidade

**Requisitos cobertos:** FR-17, FR-22; AR-10, AR-11, AR-15; UX-DR25.

As a cliente que pagou ou iniciou pagamento,
I want receber confirmação clara do estado do meu pedido,
So that eu saiba o que acontece depois.

**Acceptance Criteria:**

**Given** um pagamento confirmado
**When** o pedido mudar de estado
**Then** a cliente deve receber confirmação com número do pedido, resumo e próximos passos
**And** produtos personalizados devem orientar sobre briefing e aprovação

**Given** um pagamento pendente por transferência
**When** o pedido for criado
**Then** a cliente deve receber instruções de pagamento, prazo de 48 horas e opção de prorrogação
**And** deve poder enviar comprovante quando disponível

**Given** a arquitetura de estados
**When** pedido, pagamento ou entrega mudarem
**Then** a Laravel API deve registrar os estados separadamente
**And** os efeitos assíncronos devem ser processados por filas/jobs

### Epic 5: Área da Cliente, briefing e aprovação de arte

A cliente acessa o pedido com segurança, preenche briefing protegido, recebe prévias, pede alterações, aprova a arte e escolhe autorização de publicação.

**FRs cobertos:** FR-23, FR-24, FR-25, FR-26, FR-27, FR-28, FR-29, FR-30, FR-31, FR-34, FR-36

**Notas de implementação:** Laravel controla estados, arquivos, autorização e notificações; Next.js/BFF apresenta a experiência.

### Story 5.1: Criar acesso seguro à Área da Cliente

**Requisitos cobertos:** FR-17, FR-30, FR-31; AR-13; NFR-5, NFR-8.

As a cliente da JS Designs,
I want acessar meus pedidos em uma área segura,
So that eu acompanhe briefing, prévias, aprovações e entregas sem expor dados pessoais.

**Acceptance Criteria:**

**Given** uma cliente com pedido
**When** acessar a Área da Cliente
**Then** deve autenticar ou validar acesso seguro antes de ver dados do pedido
**And** deve ver apenas pedidos associados a ela

**Given** a arquitetura aprovada
**When** a sessão estiver ativa
**Then** a autorização deve ser controlada pela Laravel API
**And** o BFF deve proteger cookies/sessão sem expor segredos ao navegador

**Given** uma tentativa de acesso inválida
**When** alguém tentar abrir pedido de outra cliente
**Then** o sistema deve negar acesso
**And** deve registrar evento relevante de segurança quando aplicável

### Story 5.2: Preencher briefing pós-pagamento com referências e observações

**Requisitos cobertos:** FR-23, FR-24, FR-25, FR-31; AR-23; UX-DR20, UX-DR21, UX-DR34.

As a cliente com pedido personalizado,
I want preencher o briefing e enviar referências depois do pagamento,
So that a criação da arte tenha todas as informações necessárias.

**Acceptance Criteria:**

**Given** um pedido personalizado com pagamento confirmado
**When** a cliente abrir o briefing
**Then** deve poder informar tema, nome, data, preferências, textos, referências e outras observações
**And** deve poder anexar arquivos de referência quando permitido

**Given** a cliente optando por preencher depois
**When** o briefing ainda não estiver entregue
**Then** o sistema deve avisar que a primeira arte para aprovação só ficará pronta após briefing preenchido e entregue
**And** deve avisar que a produção só começa depois da arte aprovada

**Given** todas as informações obrigatórias do briefing preenchidas
**When** a cliente entregar o briefing
**Then** o sistema deve calcular ou comunicar o prazo de criação aplicável
**And** o pedido deve sair de “Aguardando briefing” para o próximo estado permitido

**Given** dados sensíveis no briefing
**When** arquivos ou textos forem salvos
**Then** devem ficar privados
**And** o acesso deve depender da autorização do pedido

### Story 5.3: Gerenciar prévias de arte e pedidos de alteração

**Requisitos cobertos:** FR-26, FR-27, FR-31; UX-DR22, UX-DR23, UX-DR24, UX-DR31.

As a cliente aguardando arte personalizada,
I want receber prévias e pedir alterações dentro do site,
So that eu aprove a arte com clareza antes da produção.

**Acceptance Criteria:**

**Given** uma primeira arte pronta
**When** a administradora publicar a prévia
**Then** a cliente deve ser notificada
**And** deve conseguir visualizar a prévia na Área da Cliente

**Given** a cliente analisando uma prévia
**When** solicitar alteração
**Then** deve poder escrever comentários objetivos
**And** o pedido deve registrar histórico de versões e solicitações

**Given** uma arte com rodadas gratuitas disponíveis
**When** a cliente solicitar alteração
**Then** o sistema deve permitir até três rodadas gratuitas por arte
**And** deve mostrar contador visível de alterações gratuitas restantes

**Given** uma prévia publicada
**When** outra versão for enviada
**Then** a versão anterior deve permanecer registrada para auditoria
**And** arquivos privados não devem ficar acessíveis publicamente

### Story 5.4: Aprovar arte e bloquear início de produção até aprovação

**Requisitos cobertos:** FR-28, FR-29, FR-31, FR-34; AR-24; UX-DR22, UX-DR24.

As a cliente da JS Designs,
I want aprovar explicitamente a arte final,
So that a produção ou entrega personalizada avance somente com minha confirmação.

**Acceptance Criteria:**

**Given** uma prévia pendente
**When** a cliente clicar em aprovar
**Then** o sistema deve registrar data, hora, versão aprovada e usuário responsável
**And** o pedido deve mudar para estado compatível com produção ou finalização

**Given** um pedido sem arte aprovada
**When** houver tentativa de iniciar produção física ou entrega personalizada final
**Then** o sistema deve bloquear o avanço
**And** deve informar que a arte precisa ser aprovada

**Given** a arquitetura de estados
**When** a aprovação ocorrer
**Then** a Laravel API deve controlar a transição de estado
**And** o registro deve ser auditável

**Given** um convite aprovado
**When** a cliente confirmar a aprovação final
**Then** deve poder escolher receber o convite por e-mail ou WhatsApp
**And** a escolha deve ficar registrada no pedido

### Story 5.5: Registrar autorização de publicação com opção marcada e removível

**Requisitos cobertos:** AR-25; UX-DR35.

As a cliente da JS Designs,
I want decidir se autorizo a publicação do produto feito,
So that eu tenha controle sobre uso de imagens do meu pedido no portfólio.

**Acceptance Criteria:**

**Given** o checkout ou Área da Cliente
**When** a autorização de publicação aparecer
**Then** ela deve vir marcada por padrão
**And** a cliente deve conseguir desmarcar se não quiser autorizar

**Given** autorização para convite exclusivo
**When** o item for publicado no portfólio
**Then** local, data, horário e dados sensíveis devem ser modificados ou anonimizados
**And** o registro deve indicar que houve anonimização

**Given** autorização para lembrancinhas ou miniaturas
**When** o item for publicado
**Then** pode aparecer sem modificação visual quando autorizado
**And** a decisão da cliente deve ficar registrada no pedido

### Story 5.6: Notificar a cliente sobre pendências e mudanças de estado

**Requisitos cobertos:** FR-30, FR-36; AR-15; UX-DR24, UX-DR31.

As a cliente acompanhando um pedido,
I want receber avisos sobre briefing, prévias, aprovação e pendências,
So that eu saiba quando preciso agir.

**Acceptance Criteria:**

**Given** uma pendência de briefing
**When** o pedido estiver aguardando informações da cliente
**Then** o sistema deve exibir pendência na Área da Cliente
**And** pode enviar notificação conforme consentimentos aplicáveis

**Given** uma nova prévia ou resposta da produção
**When** o estado do pedido mudar
**Then** a cliente deve receber aviso claro
**And** o histórico do pedido deve ser atualizado

**Given** a cliente precisando de ajuda
**When** visualizar uma pendência
**Then** deve haver caminho para suporte dentro do fluxo
**And** a conversa deve ficar vinculada ao contexto do pedido quando aplicável

### Epic 6: Entrega digital, produção física, QA e rastreio

A cliente recebe Produto Digital Pronto imediatamente após pagamento confirmado, recebe convite personalizado só após criação/aprovação, acompanha produção física, foto privada de QA e rastreio quando disponível.

**FRs cobertos:** FR-32, FR-33, FR-35, FR-43, FR-44

**Notas de implementação:** separa Produto Digital Pronto de convite digital personalizado.

### Story 6.1: Entregar produto digital pronto após pagamento confirmado

**Requisitos cobertos:** FR-9, FR-35; AR-22; UX-DR12, UX-DR34.

As a cliente que comprou produto digital pronto,
I want receber o arquivo por e-mail após confirmação do pagamento,
So that eu possa usar o produto sem aguardar personalização.

**Acceptance Criteria:**

**Given** um produto digital pronto pago e confirmado
**When** o pagamento for validado
**Then** o sistema deve liberar o arquivo digital
**And** deve enviar instruções por e-mail

**Given** um arquivo editável
**When** a cliente receber a entrega
**Then** deve ficar claro que é editável via Silhouette Studio quando aplicável
**And** não deve mencionar Canva como editor suportado

**Given** pagamento pendente ou recusado
**When** a cliente tentar acessar o arquivo
**Then** o sistema não deve liberar a entrega
**And** deve indicar o estado do pagamento

### Story 6.2: Entregar convite digital personalizado somente após criação e aprovação

**Requisitos cobertos:** FR-8, FR-28, FR-34; AR-22, AR-24; UX-DR22, UX-DR34.

As a cliente que comprou convite digital personalizado,
I want receber o arquivo final somente depois da edição/criação e aprovação,
So that o convite tenha tema e dados corretos.

**Acceptance Criteria:**

**Given** um convite digital personalizado
**When** o pagamento for confirmado
**Then** o pedido deve seguir para briefing/criação
**And** não deve liberar entrega imediata

**Given** a arte aprovada
**When** a produção finalizar o arquivo
**Then** a cliente deve receber a versão final por e-mail
**And** deve ficar claro se o arquivo é editável via Silhouette Studio quando aplicável

**Given** o pedido sem aprovação
**When** houver tentativa de entrega final
**Then** o sistema deve bloquear a entrega
**And** deve informar que falta aprovação da arte

### Story 6.3: Acompanhar produção física e foto privada de QA

**Requisitos cobertos:** FR-29, FR-32, FR-43, FR-44; UX-DR24, UX-DR26, UX-DR31, UX-DR34.

As a cliente de lembrancinha física personalizada,
I want acompanhar a produção e ver uma foto informativa quando fizer sentido,
So that eu saiba que o pedido está avançando.

**Acceptance Criteria:**

**Given** arte aprovada e produção iniciada
**When** a administradora atualizar o estado
**Then** a cliente deve ver o progresso na Área da Cliente
**And** os estados devem refletir produção, QA, embalagem e envio quando aplicável

**Given** uma arte aprovada para produto físico
**When** a produção for preparada
**Then** o sistema deve gerar Ficha de Produção com versão aprovada, itens, quantidades, materiais, acabamento, Miniatura, prazo, endereço e checklist
**And** a ficha deve ficar vinculada ao pedido

**Given** uma foto de QA disponível
**When** a cliente visualizar a foto
**Then** deve ficar claro que a foto é apenas informativa
**And** a foto deve ser privada e vinculada ao pedido

**Given** problema identificado na produção
**When** o estado for atualizado
**Then** a cliente deve poder chamar suporte para avaliar a situação
**And** a equipe deve registrar a exceção no pedido

### Story 6.4: Registrar envio e rastreio quando disponível

**Requisitos cobertos:** FR-33; AR-21; UX-DR27, UX-DR31.

As a cliente de produto físico,
I want receber informação de envio e rastreio quando existir,
So that eu acompanhe a entrega conforme a modalidade escolhida.

**Acceptance Criteria:**

**Given** um pedido físico pronto para envio
**When** a administradora registrar postagem nos CTT ou transportadora
**Then** deve informar modalidade, data de envio e transportador quando aplicável
**And** deve registrar número de rastreio quando existir

**Given** uma modalidade sem rastreio
**When** a cliente visualizar o envio
**Then** deve ficar claro que o rastreio não está disponível
**And** deve explicar que o rastreio depende de quem faz a entrega

**Given** rastreio disponível
**When** a cliente acessar o pedido
**Then** deve ver o código ou link de rastreio
**And** o sistema pode consultar status automaticamente se o serviço permitir

### Epic 7: Suporte Online e Projeto Exclusivo

A cliente recebe suporte dentro do site, com respostas cadastradas, escalonamento humano/chat/WhatsApp e Projeto Exclusivo sem pagamento imediato.

**FRs cobertos:** FR-10, FR-37, FR-38, FR-39, FR-40

**Notas de implementação:** WhatsApp dentro do fluxo, não como botão flutuante permanente.

### Story 7.1: Oferecer suporte dentro do site com contexto da navegação

**Requisitos cobertos:** FR-37, FR-39; AR-26; UX-DR28.

As a cliente com dúvida,
I want chamar suporte dentro do site,
So that eu consiga resolver dúvidas sem sair do fluxo de compra ou pedido.

**Acceptance Criteria:**

**Given** a cliente em página pública, carrinho ou Área da Cliente
**When** abrir o suporte
**Then** o chat deve aparecer dentro do site
**And** deve carregar contexto da página ou pedido quando aplicável

**Given** a regra de UX definida
**When** a cliente navegar no site
**Then** não deve haver botão flutuante permanente de WhatsApp
**And** o WhatsApp deve aparecer apenas como opção dentro do fluxo de suporte quando fizer sentido

**Given** atendimento indisponível
**When** a cliente enviar mensagem
**Then** o sistema deve registrar a solicitação
**And** deve informar expectativa de resposta

### Story 7.2: Usar respostas cadastradas e escalonar para atendimento humano

**Requisitos cobertos:** FR-38, FR-39, FR-40; AR-26; UX-DR28.

As a administradora da JS Designs,
I want cadastrar respostas de suporte e escalar conversas quando necessário,
So that dúvidas comuns sejam respondidas rápido e casos específicos recebam atenção humana.

**Acceptance Criteria:**

**Given** perguntas frequentes cadastradas
**When** a cliente iniciar atendimento
**Then** o sistema deve sugerir respostas úteis dentro do chat
**And** deve manter linguagem em português do Brasil

**Given** uma dúvida não resolvida
**When** a cliente solicitar ajuda humana ou o sistema identificar necessidade
**Then** a conversa deve ser escalada
**And** deve manter o histórico do chat

**Given** opção de WhatsApp habilitada
**When** o atendimento escalar para WhatsApp
**Then** a transição deve ocorrer dentro do fluxo
**And** deve preservar o contexto mínimo necessário

### Story 7.3: Solicitar Projeto Exclusivo sem pagamento imediato

**Requisitos cobertos:** FR-10; UX-DR29.

As a cliente que não encontrou o produto ideal,
I want solicitar um projeto exclusivo com detalhes e referências,
So that a JS Designs possa avaliar antes de cobrar ou confirmar.

**Acceptance Criteria:**

**Given** a cliente em produto, busca sem resultado ou suporte
**When** escolher Projeto Exclusivo
**Then** deve preencher formulário com tema, tipo de produto, prazo, referências e outras observações
**And** não deve haver pagamento imediato nessa etapa

**Given** a solicitação enviada
**When** a administradora analisar
**Then** deve poder responder, pedir mais informações ou transformar em proposta/pedido
**And** a cliente deve ser notificada da resposta

**Given** arquivos de referência enviados
**When** forem armazenados
**Then** devem ficar privados
**And** devem ser vinculados à solicitação

### Epic 8: Administração, exceções, operação, auditoria e métricas

Sharom administra pedidos, catálogo, exceções, pagamentos, faturas, fretes, papéis, auditoria e métricas sem planilhas paralelas.

**FRs cobertos:** FR-41, FR-42, FR-45, FR-46, FR-47, FR-48, FR-49, FR-50

**Notas de implementação:** admin usa a mesma API Laravel/domínio; RBAC e auditoria no backend.

### Story 8.1: Criar painel administrativo de pedidos e estados operacionais

**Requisitos cobertos:** FR-41, FR-42; AR-9, AR-11, AR-27.

As a administradora da JS Designs,
I want visualizar pedidos por estado, modalidade e prioridade,
So that eu controle produção, briefing, pagamento, entrega e exceções sem planilhas paralelas.

**Acceptance Criteria:**

**Given** pedidos existentes
**When** a administradora abrir o painel
**Then** deve ver lista filtrável por estado, tipo de produto, pagamento, produção e entrega
**And** deve conseguir abrir o detalhe operacional do pedido

**Given** um pedido com pendência
**When** ele aparecer no painel
**Then** deve destacar briefing pendente, pagamento pendente, aprovação pendente ou envio pendente
**And** deve permitir ação compatível com o papel da usuária

**Given** a arquitetura aprovada
**When** o admin consumir dados
**Then** deve usar a mesma Laravel API/domínio da loja
**And** não deve manter regra operacional em planilhas paralelas

### Story 8.2: Administrar catálogo, preços, descontos e adicionais

**Requisitos cobertos:** FR-46; AR-16, AR-17, AR-18.

As a administradora da JS Designs,
I want configurar catálogo, preços, descontos progressivos e adicionais,
So that a loja calcule valores corretamente sem edição manual por pedido.

**Acceptance Criteria:**

**Given** acesso administrativo
**When** cadastrar ou editar produto
**Then** deve poder configurar preço base, faixas de quantidade, descontos progressivos e adicionais como miniatura
**And** deve poder publicar, ocultar ou arquivar produtos

**Given** regra de miniatura
**When** configurar adicional
**Then** deve existir suporte para cobrança única por pedido/personagem
**And** a regra deve ser aplicada pela Laravel API

**Given** alteração de preço
**When** a administradora salvar
**Then** novas compras devem usar a nova regra
**And** pedidos já pagos não devem ser alterados sem exceção administrativa registrada

### Story 8.3: Confirmar manualmente pagamentos e controlar exceções financeiras

**Requisitos cobertos:** FR-19, FR-47; AR-10, AR-20.

As a administradora da JS Designs,
I want confirmar pagamentos manuais e tratar exceções financeiras,
So that pedidos por transferência ou casos especiais avancem com rastreabilidade.

**Acceptance Criteria:**

**Given** um pedido por transferência com comprovante
**When** a administradora revisar
**Then** deve poder aprovar ou rejeitar o comprovante
**And** aprovação manual deve confirmar o pagamento e registrar responsável, data e observação

**Given** prazo de 48 horas expirando
**When** a cliente solicitar prorrogação
**Then** a administradora ou regra do sistema deve permitir mais 48 horas conforme política definida
**And** o histórico deve registrar a prorrogação

**Given** uma exceção financeira
**When** houver ajuste, cancelamento ou correção
**Then** o sistema deve exigir motivo
**And** deve preservar trilha de auditoria

### Story 8.4: Administrar produção, QA, envio e rastreio

**Requisitos cobertos:** FR-32, FR-33, FR-43, FR-44, FR-47; AR-21.

As a administradora da JS Designs,
I want atualizar produção, QA, envio e rastreio,
So that a cliente receba informações corretas durante a execução do pedido.

**Acceptance Criteria:**

**Given** um pedido físico aprovado para produção
**When** a administradora atualizar etapas
**Then** deve poder marcar produção iniciada, QA, embalagem, enviado e entregue quando aplicável
**And** cada transição deve respeitar a máquina de estados

**Given** foto de QA
**When** a administradora anexar imagem
**Then** a imagem deve ficar privada
**And** deve aparecer para a cliente apenas como informativa

**Given** envio registrado
**When** houver código de rastreio
**Then** deve salvar transportador, modalidade e código
**And** deve indicar se o rastreio é consultável automaticamente ou apenas informativo

### Story 8.5: Gerenciar papéis, permissões e auditoria

**Requisitos cobertos:** FR-48; AR-13, AR-14, AR-27; NFR-5, NFR-8.

As a responsável pela operação,
I want controlar permissões e auditar ações sensíveis,
So that dados de clientes, pagamentos e arquivos fiquem protegidos.

**Acceptance Criteria:**

**Given** usuários administrativos
**When** configurar acesso
**Then** deve haver papéis/permissões para funções administrativas relevantes
**And** ações sensíveis devem exigir usuário autorizado

**Given** alteração de pedido, pagamento, briefing, arquivo, autorização ou preço
**When** a ação ocorrer
**Then** o sistema deve registrar quem fez, quando fez e o que mudou
**And** o registro deve ser consultável por usuário autorizado

**Given** tentativa não autorizada
**When** alguém acessar recurso protegido
**Then** o sistema deve negar acesso
**And** deve registrar evento quando aplicável

### Story 8.6: Exibir métricas operacionais e comerciais essenciais

**Requisitos cobertos:** FR-49, FR-50; NFR-11.

As a administradora da JS Designs,
I want acompanhar métricas de vendas, pedidos e produção,
So that eu tome decisões operacionais sem depender de controles manuais.

**Acceptance Criteria:**

**Given** pedidos e eventos registrados
**When** abrir a área de métricas
**Then** deve ver indicadores de pedidos, receita, conversão, produtos mais procurados e estados pendentes
**And** filtros por período e modalidade devem estar disponíveis

**Given** produtos físicos e digitais
**When** consultar métricas
**Then** deve ser possível separar desempenho por tipo de produto
**And** deve evidenciar produtos mais procurados para alimentar a home e catálogo

**Given** dados sensíveis
**When** métricas forem exibidas
**Then** não devem expor informações privadas desnecessárias
**And** devem respeitar permissões administrativas

### Story 8.7: Tratar exceções operacionais explicitamente

**Requisitos cobertos:** FR-45, FR-47; AR-11, AR-15, AR-27; NFR-3, NFR-11.

As a administradora da JS Designs,
I want registrar e tratar exceções operacionais de forma explícita,
So that quarta alteração, urgência, pagamento pendente, erro, retrabalho, falha de integração e pedido pausado não fiquem perdidos em mensagens soltas.

**Acceptance Criteria:**

**Given** um pedido com quarta alteração, urgência, pagamento pendente, erro, retrabalho, falha de integração ou pausa
**When** a exceção for identificada
**Then** a administradora deve conseguir registrar o tipo da exceção, motivo, responsável e próxima ação
**And** a exceção deve ficar vinculada ao pedido

**Given** uma exceção aberta
**When** a cliente consultar o pedido
**Then** deve ver estado, motivo e próxima ação quando a informação puder ser exibida
**And** deve ter opção de chamar suporte para avaliar a situação

**Given** uma exceção resolvida
**When** a administradora encerrar o caso
**Then** o sistema deve registrar solução, data, responsável e impacto no prazo
**And** a máquina de estados deve avançar apenas para estados permitidos
