---
baseline_commit: 7d3c15aa461f6c634b215ab3a32445e49f4102b5
---

# Story 1.5: Exibir captura de e-mail com cupom de primeira compra

Status: done

## Story

Como visitante da loja,
quero solicitar um cupom individual de primeira compra usando meu e-mail,
para receber o benefício sem ser inscrita em comunicações adicionais e sem perder controle sobre o código.

## Correções Obrigatórias Incorporadas

Esta versão substitui a revisão anterior da story e fecha os pontos que impediam implementação segura:

- `display` deixa de ser modo público para dados reais. Ele só é permitido em `local`/`testing` ou ambiente explicitamente marcado como teste, com dados de teste.
- Produção usa entrega por e-mail operacional; se o canal real não estiver pronto, a funcionalidade falha fechada e não coleta e-mails reais.
- A interface recebe o benefício de um contrato público sanitizado servido pelo Laravel/BFF; frontend e BFF não mantêm literal comercial.
- A autorização específica para emissão/entrega do cupom é separada de newsletter, carrinho abandonado, analytics e marketing.
- A base legal, texto aprovado, versão, retenção e canal de entrega são pré-requisitos bloqueantes para ativação com dados reais.
- Há uma única campanha ativa de primeira compra e um único cupom ativo por e-mail canônico para essa finalidade, mesmo entre campanhas.
- Idempotência de cupom e de notificação é garantida por constraints persistentes e transações, não apenas por lógica em memória.
- Fechamento, foco, supressão por sessão, fallback de storage e coordenação de superfícies têm comportamento único e testável.

## Acceptance Criteria

1. **Exibição após engajamento real, sem bloquear a loja**
   - **Dado** o primeiro acesso elegível da visitante a uma rota pública na sessão da aba
   - **Quando** ocorrer engajamento real e nenhuma outra superfície global estiver ativa
   - **Então** a captura pode ser exibida como diálogo leve e descartável, sem depender apenas de tempo decorrido
   - **E** deve informar o benefício configurado retornado pelo backend: percentual, ausência de valor mínimo, uso manual futuro e validação de elegibilidade no checkout
   - **E** a home, incluindo "Mais procurados", continua renderizada por baixo sem mudança de layout ou perda de conteúdo.

2. **Contrato público de oferta sem regra no frontend**
   - **Dado** a renderização pública da loja
   - **Quando** a promoção estiver habilitada para o ambiente
   - **Então** Next.js deve obter de rota same-origin uma configuração pública sanitizada originada do Laravel, contendo `enabled`, `delivery_mode`, `discount_percent`, `minimum_amount`, `non_cumulative`, `manual_checkout_required`, `authorization_text_version` e textos públicos aprovados
   - **E** o frontend não pode hardcodar "10%", valor mínimo, cumulatividade, expiração, texto legal vigente ou modo de entrega
   - **E** se a configuração pública estiver ausente, inválida ou incompleta, o diálogo não deve abrir e nenhum e-mail deve ser coletado.

3. **Campo de e-mail e finalidade específica**
   - **Dado** o diálogo aberto
   - **Quando** a visitante consultar a oferta
   - **Então** deve encontrar label persistente, campo `type="email"` com `autocomplete="email"`, explicação de finalidade, link para privacidade e ação explícita para solicitar o cupom
   - **E** a autorização específica para usar o e-mail na emissão e entrega do cupom deve ser não pré-marcada, registrada com versão aprovada do texto, data/hora, base legal configurada e canal de entrega
   - **E** solicitar o cupom não cria conta, não inscreve em newsletter, não autoriza recuperação de carrinho e não ativa analytics ou marketing.

4. **Validação acessível e preservação dos dados**
   - **Dado** e-mail vazio, sintaticamente inválido, domínio inválido conforme política aceita ou autorização específica ausente
   - **Quando** a visitante enviar o formulário
   - **Então** a submissão deve ser rejeitada antes ou pela Laravel API com erro textual em português do Brasil, próximo e programaticamente associado ao campo ou controle
   - **E** `aria-invalid`, `aria-describedby`, foco e anúncio de estado devem permitir identificar e corrigir o problema
   - **E** o valor digitado deve ser preservado no erro.

5. **Emissão idempotente pelo domínio Laravel**
   - **Dado** e-mail válido, autorização específica aceita e configuração ativa
   - **Quando** a visitante solicitar o desconto uma ou mais vezes, inclusive sob retry ou concorrência
   - **Então** o navegador deve enviar a ação ao BFF Next.js, que chama a API Laravel REST `/api/v1`
   - **E** a Laravel API deve emitir ou reassociar um único cupom individual por e-mail canônico e finalidade `first_purchase`, sem duplicar benefício ou notificação
   - **E** o cupom deve representar o benefício configurado de primeira compra: 10%, sem valor mínimo, não cumulativo com desconto progressivo ou de conjunto e sem expiração enquanto não existir regra comercial aprovada
   - **E** geração, persistência, idempotência, elegibilidade preliminar e decisão de entrega pertencem ao domínio Laravel, nunca ao browser ou BFF.

6. **Entrega segura por ambiente**
   - **Dado** um cupom emitido ou já associado
   - **Quando** a operação concluir
   - **Então** em produção e ambientes com dados reais, a API deve aceitar a solicitação para entrega por e-mail e nunca devolver `coupon_code` ao cliente
   - **E** o modo `display` só pode ser habilitado em `local`/`testing` ou ambiente explicitamente marcado como teste, sem coleta de e-mails reais, e deve ser recusado pela API em produção
   - **E** o modo `email` deve registrar notificação idempotente e agendar envio após commit por porta/adaptador Laravel
   - **E** o modo `email` não pode afirmar envio concluído quando não houver provedor, credenciais ou worker operacional
   - **E** respostas para e-mail novo, repetido, já emitido ou inelegível devem ser uniformes e não revelar cadastro, compra anterior ou outro dado pessoal.

7. **Sucesso claro sem aplicação antecipada**
   - **Dado** retorno bem-sucedido no modo `display` permitido para teste
   - **Quando** o código for apresentado
   - **Então** ele deve poder ser lido e copiado, com aviso de que será inserido manualmente no carrinho/checkout quando esse fluxo existir
   - **E** o diálogo não deve fechar automaticamente antes de a visitante copiar ou fechar explicitamente
   - **E** no modo `email`, a interface deve mostrar apenas confirmação honesta de que a solicitação foi aceita para entrega, sem exibir código e sem prometer prazo não configurado
   - **E** a interface não deve simular carrinho, validar/aplicar desconto nem prometer elegibilidade definitiva nesta story.

8. **Fechamento, foco e supressão por sessão**
   - **Dado** o diálogo aberto
   - **Quando** a visitante usar o botão explícito de fechar/"Agora não", pressionar `Escape`, copiar o código no modo `display` ou confirmar o sucesso no modo `email`
   - **Então** a superfície deve fechar, devolver foco ao elemento anteriormente ativo ou a um fallback lógico e liberar imediatamente a navegação
   - **E** a promoção deve ser marcada como vista ao abrir e não reaparecer em navegação ou refresh na mesma sessão da aba
   - **E** uma nova aba ou nova sessão pode voltar a ser elegível
   - **E** falha de `sessionStorage` deve degradar somente para memória da aba; é proibido usar `window.name`, `localStorage`, query string ou analytics para e-mail, cupom ou supressão.

9. **Diálogo acessível, responsivo e isolado**
   - **Dado** uso por teclado, leitor de tela, zoom ou viewport de 320 px
   - **Quando** a superfície abrir
   - **Então** título e descrição devem nomear o diálogo, o foco deve permanecer dentro dele, o conteúdo externo deve ficar inerte enquanto aberto e não pode haver empilhamento de modais
   - **E** fechar, CTA e controles precisam ter alvo mínimo de 44 x 44 px, contraste WCAG 2.2 AA, reflow sem overflow horizontal e comportamento compatível com redução de movimento
   - **E** em navegadores sem `HTMLDialogElement` e `showModal()`, a promoção não deve abrir automaticamente; pode renderizar alternativa inline não modal e não bloqueante, com os mesmos textos e validações
   - **E** o componente interativo deve ser um Client Component isolado; a home e o layout público continuam Server Components.

10. **Falha segura e proteção contra abuso**
    - **Dado** timeout, API indisponível, falha de persistência, falha do canal de entrega, configuração incompleta ou excesso de tentativas
    - **Quando** a solicitação não puder ser concluída
    - **Então** a interface deve preservar o e-mail, mostrar orientação genérica de retry e continuar permitindo fechar/navegar
    - **E** Laravel deve aplicar limites configuráveis com defaults de 5 tentativas por minuto por IP confiável e 3 solicitações de entrega por hora por fingerprint de e-mail
    - **E** solicitações idempotentes que apenas encontram cupom existente não devem gerar novo benefício; solicitações acima da cota não devem revelar se já existia cupom
    - **E** logs, traces, métricas e respostas não devem conter e-mail completo, código do cupom, URL interna, payload Laravel ou detalhes internos
    - **E** repetição, concorrência ou retry não podem duplicar cupom, benefício ou envio.

11. **Gate de ativação com dados reais**
    - **Dado** qualquer ambiente com coleta de e-mail real
    - **Quando** a funcionalidade for habilitada
    - **Então** Laravel deve validar de forma fail-closed: política de privacidade publicada, base legal definida, texto aprovado, versão ativa, retenção definida, canal de entrega operacional, trusted proxies configurados e chaves criptográficas versionadas
    - **E** se qualquer pré-requisito faltar, a API deve responder indisponibilidade segura e a configuração pública deve retornar `enabled=false`
    - **E** ambientes local/teste podem habilitar `display` com dados de teste
    - **E** nenhum aceite técnico desta story autoriza coleta de e-mails reais antes desse gate.

12. **Retenção, revogação e direitos do titular**
    - **Dado** e-mail, fingerprint, autorização, timestamps e cupom persistidos
    - **Quando** a política de retenção aprovada for aplicada ou a titular exercer direitos
    - **Então** o sistema deve permitir localizar, exportar, suprimir, anonimizar ou eliminar registros conforme base legal e política vigente
    - **E** se a base legal configurada for consentimento, deve existir registro de revogação e efeito definido sobre novos envios, sem apagar indevidamente evidências que devam ser conservadas por obrigação legal
    - **E** a story deve implementar ao menos campos, estados e comandos administrativos mínimos para retenção/revogação, ainda que a interface administrativa completa fique para épico posterior.

## Development Slices

Esta story continua identificada como 1.5, mas a implementação deve ser entregue internamente em fatias verificáveis. Uma PR única só é aceitável se preservar estes marcos como commits ou seções de evidência:

1. Domínio Laravel, migrations, constraints, configuração e testes unitários.
2. Contrato REST Laravel, gates, rate limit, privacidade, retenção e testes feature.
3. Outbox/notificação idempotente e adapters de entrega.
4. BFF same-origin para oferta pública e solicitação.
5. Client Component do diálogo e conteúdo centralizado.
6. E2E, acessibilidade, screenshots e validação final.

## Tasks / Subtasks

- [x] 1. Criar domínio mínimo de promoção/cupom no Laravel (AC: 5, 6, 10, 11, 12)
  - [x] Criar módulo `apps/api/app/Modules/Promotions/` nas camadas `Domain`, `Application`, `Infrastructure` e `Interfaces/Http`; não colocar regra em controller, Job ou Eloquent.
  - [x] Modelar campanha `first_purchase`, cupom individual, autorização específica, estado de emissão, estado de entrega, retenção, revogação e timestamps.
  - [x] Criar migrations PostgreSQL com uma única campanha ativa `first_purchase` por ambiente/tenant e um único cupom ativo por `email_fingerprint` + finalidade `first_purchase`, mesmo entre campanhas.
  - [x] Garantir unicidade do digest do código e idempotência por constraint persistente; usar transação e lock/upsert seguro contra concorrência.
  - [x] Gerar código humano digitável com CSPRNG e pelo menos 80 bits de entropia; persistir digest pesquisável e valor recuperável criptografado com `key_version`.
  - [x] Implementar rotação de chaves com identificador de versão: novas emissões usam chave ativa, leitura tenta pela versão gravada, rotação não quebra lookup HMAC nem descriptografia de cupons existentes.
  - [x] Implementar `IssueOrAssociateFirstPurchaseCoupon`, deixando aplicação, comparação do melhor benefício e resgate para Stories 4.2/3.x.
  - [x] Registrar bindings de repository/ports em provider Laravel, mantendo Eloquent somente em `Infrastructure`.

- [x] 2. Implementar configuração pública e gates fail-closed (AC: 1, 2, 11)
  - [x] Criar configuração Laravel `promotions.first_purchase` com `enabled`, `delivery_mode`, `discount_percent`, `minimum_amount`, `non_cumulative`, `manual_checkout_required`, `authorization_text_version`, `legal_basis`, `retention_policy_version`, `real_data_allowed`, `provider_ready`, `worker_required` e `test_display_allowed`.
  - [x] Criar endpoint público sanitizado `GET /api/v1/promotions/first-purchase-offer` sem PII, segredos ou URLs internas.
  - [x] Retornar `enabled=false` quando qualquer pré-requisito obrigatório estiver ausente.
  - [x] Recusar `delivery_mode=display` em produção ou ambiente com `real_data_allowed=true`.
  - [x] Documentar `.env.example` sem segredos, com defaults: produção desabilitada; local/teste podem habilitar `display` para dados de teste.

- [x] 3. Implementar contrato REST de solicitação e proteção do endpoint público (AC: 3, 4, 5, 6, 10)
  - [x] Adicionar `POST /api/v1/promotions/first-purchase-coupons` em `apps/api/routes/api.php` com Form Request e controller fino.
  - [x] Entrada mínima: `email`, `authorization_accepted=true`, `authorization_text_version` e identificador da oferta pública recebida; Laravel deve validar a versão contra a configuração ativa, não confiar no valor do browser.
  - [x] Responder `202 Accepted` no modo `email`; responder `200 OK` com `coupon_code` somente no modo `display` permitido para teste.
  - [x] Usar shape estável (`status`, `delivery`, `message`, `request_id`; `coupon_code` somente em `display` de teste); usar `422`, `429` e `503` sem enumeração.
  - [x] Definir canonicalização de e-mail: trim, validação sintática, normalização Unicode NFC, domínio convertido por IDNA/lowercase, local-part preservado para entrega e fingerprint derivado por política documentada do produto. Não remover pontos, aliases `+` nem aplicar heurística de provedor.
  - [x] Derivar fingerprint HMAC com chave server-side versionada; nunca usar e-mail bruto em chave, log, métrica ou exception.
  - [x] Configurar trusted proxies Laravel para extrair IP confiável; sem proxy confiável configurado em produção, gate deve falhar fechado.
  - [x] Aplicar rate limits: 5/min por IP confiável e 3/h por fingerprint para emissão/entrega; idempotência deve impedir duplicação e respostas não podem revelar existência.

- [x] 4. Implementar entrega por porta/adaptador e outbox idempotente (AC: 6, 10, 11)
  - [x] Em `display`, devolver o código somente em local/teste e nunca persistir no navegador.
  - [x] Em `email`, usar `EmailProvider`/Laravel Mail como adapter e outbox/Job idempotente após commit.
  - [x] Criar `notification_key` única por cupom + canal + propósito + versão de template, com estados `pending`, `queued`, `sent`, `failed_retryable`, `failed_final` e timestamps.
  - [x] Garantir que retries e workers concorrentes não dupliquem entrega.
  - [x] Usar mailer `array` nos testes e `log` apenas no desenvolvimento; `log` não conta como entrega real.
  - [x] Se o canal real, credenciais ou worker obrigatório não estiverem configurados, manter a feature desligada ou responder indisponibilidade honesta; não marcar como enviado.

- [x] 5. Criar a rota BFF same-origin (AC: 1, 2, 5, 6, 10)
  - [x] Criar `apps/web/src/app/api/promotions/first-purchase-offer/route.ts` para buscar a oferta pública sanitizada no Laravel com `cache: 'no-store'`.
  - [x] Criar `apps/web/src/app/api/promotions/first-purchase-coupons/route.ts` para aceitar somente o payload mínimo e chamar a API interna.
  - [x] Estender `apps/web/src/bff/apiClient.ts` sem quebrar `fetchApiHealth`: URL somente server-side, timeout, headers JSON, validação explícita do payload e mapeamento seguro de erros.
  - [x] Não gerar código, interpretar elegibilidade, guardar regra comercial nem expor `API_INTERNAL_URL` no browser.

- [x] 6. Criar a experiência de captura como Client Component isolado (AC: 1, 3, 4, 7, 8, 9, 10)
  - [x] Criar `apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx` com estados `idle`, `submitting`, `success` e `error`.
  - [x] Injetar o componente por `PublicShell.globalSurface` em `apps/web/src/app/(public)/layout.tsx`; reutilizar o slot existente e não alterar a estrutura `header/main/footer`.
  - [x] Calcular engajamento por scroll novo da sessão: `scrollY / max(scrollHeight - innerHeight, 1) >= 0.22`; em páginas sem distância rolável, usar primeiro `wheel`, `touchmove`, `pointerdown` ou `keydown` não originado em campo editável.
  - [x] Não abrir por scroll restaurado ou navegação client-side sem novo evento de engajamento na página visível.
  - [x] Abrir 700 ms após elegibilidade somente com `document.visibilityState='visible'`, nenhum campo editável focado, suporte a `showModal()` e lock global livre.
  - [x] Coordenar superfícies via lock explícito em React/contexto ou evento global versionado; consulta ao DOM isolada não é suficiente para impedir corrida.
  - [x] Marcar a promoção vista ao abrir usando chave versionada em `sessionStorage`, com fallback somente em memória da aba; não usar `window.name`.
  - [x] Controlar foco inicial, `Escape`, fechamento, retorno de foco, prevenção de empilhamento e alternativa inline para browser sem `<dialog>`.
  - [x] Evitar foco automático no e-mail quando isso abrir teclado mobile inesperadamente; o botão de fechar é foco inicial seguro para promoção automática.
  - [x] Impedir duplo submit, preservar dados no erro, anunciar sucesso/erro via região de status e manter o diálogo aberto no modo `display` até cópia ou fechamento explícito.

- [x] 7. Centralizar conteúdo, autorização e design system (AC: 1, 2, 3, 4, 7, 9)
  - [x] Estender `publicContent.types.ts` e `publicContent.ts`; nenhuma copy visível ou mensagem de erro deve ficar espalhada no componente.
  - [x] Renderizar percentual, mínimo e condições a partir da oferta pública, não de literal local.
  - [x] Incluir título, descrição, explicação de uso manual, label, ajuda, autorização específica, CTAs, erros e estados em português do Brasil/UTF-8; atualizar `qualityCopy`.
  - [x] Usar linguagem de "autorização específica para emissão e entrega do cupom"; usar "consentimento" apenas quando a base legal configurada for consentimento.
  - [x] Estender `globals.css` usando tokens existentes: fundo branco, texto `--ink-primary`, accent `--accent-primary`, borda `--border-subtle`, raio de modal 16 px e sombra apenas funcional.
  - [x] Garantir coluna única/CTA seguro em 320-419 px, foco visível, erro em vermelho apenas como estado e `prefers-reduced-motion`.
  - [x] Não copiar layout, paleta, assets ou claims da referência Gio; protótipos são apenas referência comportamental.

- [x] 8. Implementar privacidade, retenção e revogação operacional mínima (AC: 3, 11, 12)
  - [x] Registrar finalidade específica, base legal configurada, versão do texto, timestamp, IP derivado de forma segura quando aplicável, user agent minimizado ou omitido, e canal de entrega.
  - [x] Implementar estado de revogação quando `legal_basis=consent`, impedindo novos envios opcionais após revogação.
  - [x] Implementar comando/serviço interno para anonimizar ou eliminar registros vencidos conforme `retention_policy_version`.
  - [x] Permitir busca administrativa por e-mail submetido calculando fingerprint server-side, sem expor listagem pública.
  - [x] Substituir placeholder de privacidade somente quando houver texto aprovado; até lá, manter feature desabilitada para dados reais.
  - [x] Não criar opt-in de newsletter, analytics de marketing, recuperação de carrinho ou tags opcionais.

- [x] 9. Criar testes backend, BFF e E2E (AC: todos)
  - [x] Laravel Unit/Feature: oferta pública sanitizada, gate fail-closed, produção recusando `display`, sucesso `email`, sucesso `display` em teste, `422`, versão de autorização inválida, canonicalização, idempotência sequencial/concorrente, unicidade global de primeira compra, config 10%/sem mínimo/não cumulativo/sem expiração, outbox idempotente, throttle, trusted proxy ausente, retenção/revogação e ausência de PII em logs.
  - [x] Testar rotação de chave: registros antigos continuam pesquisáveis/descriptografáveis pela versão gravada; novas emissões usam chave ativa.
  - [x] Testar contrato BFF: oferta pública, payload mínimo, timeout/upstream inválido, status mapping, `no-store` e ausência de URL/erro interno na resposta.
  - [x] Playwright dedicado: não abre por timer sozinho; abre após engajamento determinístico; não abre por scroll restaurado; texto de aplicação manual; autorização não marcada; validação/ARIA/foco; pending/duplo clique; sucesso `display` de teste; sucesso `email`; retry.
  - [x] Cobrir fechar por botão/"Agora não"/Escape/cópia/confirmar sucesso, trap e restauração de foco, background inerte, uma superfície, supressão na mesma sessão/refresh/navegação e elegibilidade em nova sessão.
  - [x] Cobrir browser sem `<dialog>` com alternativa inline, 320 px sem overflow, alvos 44 x 44, reduced motion e screenshot; manter os 14 testes atuais e o assert de home Server Component.
  - [x] E2E padrão deve usar `display` apenas em modo teste; smoke de produção deve provar `enabled=false` ou entrega `email` com provedor fake controlado.

- [x] 10. Executar validação final e registrar evidência (AC: todos)
  - [x] Backend: `composer test` e `vendor/bin/pint --test` em `apps/api` com PostgreSQL/Redis ativos.
  - [x] Frontend: `npm run lint`, `npm run typecheck`, `npm run build` e `npm run test:e2e` em `apps/web` com Laravel API ativa.
  - [x] Verificar que `package.json`, lockfiles e versões não mudaram.
  - [x] Verificar que `/health`, `/api/health`, metadata, placeholders, UTF-8 e "Mais procurados" continuam funcionando.
  - [x] Registrar evidência por slice em `Dev Agent Record`.

### Review Findings

- [x] [Review][Patch] Configurar cadeia de trusted proxies padrão: API privada, edge sanitizando `Forwarded`/`X-Forwarded-For`, BFF propagando a cadeia e Laravel confiando somente nos proxies configurados; tornar os limites configuráveis [apps/web/src/bff/apiClient.ts:176; apps/api/app/Modules/Promotions/Interfaces/Http/Controllers/FirstPurchaseCouponController.php:28]
- [x] [Review][Patch] Tornar o fingerprint promocional case-insensitive para o local-part, preservando a grafia original somente no endereço de entrega [apps/api/app/Modules/Promotions/Domain/EmailCanonicalizer.php:12]
- [x] [Review][Patch] Implementar despacho real da outbox após commit e tornar o gate de produção dependente de provider e worker operacionais [apps/api/app/Modules/Promotions/Infrastructure/Delivery/LaravelMailEmailProvider.php:7]
- [x] [Review][Patch] Impedir que retry rearme notificações `sent`/terminais ou sobrescreva `created_at` [apps/api/app/Modules/Promotions/Infrastructure/Persistence/PostgresPromotionCouponRepository.php:133]
- [x] [Review][Patch] Tornar lookup, idempotência e direitos do titular compatíveis com todas as versões HMAC suportadas [apps/api/app/Modules/Promotions/Application/Privacy/AnonymizeFirstPurchaseCoupon.php:19]
- [x] [Review][Patch] Usar de fato a chave de criptografia indicada por `code_key_version` e validar sua existência no gate [apps/api/app/Modules/Promotions/Application/IssueOrAssociateFirstPurchaseCoupon.php:47]
- [x] [Review][Patch] Recuperar conflitos de unicidade concorrentes na criação da campanha e do primeiro cupom por e-mail [apps/api/app/Modules/Promotions/Infrastructure/Persistence/PostgresPromotionCouponRepository.php:13]
- [x] [Review][Patch] Completar retenção, revogação, exportação, supressão, eliminação e anonimização sem manter entrega pendente correlacionável [apps/api/app/Modules/Promotions/Application/Privacy/AnonymizeFirstPurchaseCoupon.php:17]
- [x] [Review][Patch] Invalidar ofertas antigas quando qualquer termo comercial mudar e manter campanha/cupom coerentes [apps/api/app/Modules/Promotions/Domain/FirstPurchasePromotionSettings.php:75]
- [x] [Review][Patch] Registrar a nova autorização/base legal/retenção ao reassociar cupom sem contradizer a versão da notificação [apps/api/app/Modules/Promotions/Infrastructure/Persistence/PostgresPromotionCouponRepository.php:24]
- [x] [Review][Patch] Validar configuração fail-closed, incluindo enum de entrega, textos não vazios, chaves, percentuais e `minimum_amount` vazio como `null` [apps/api/app/Modules/Promotions/Domain/FirstPurchasePromotionSettings.php:43]
- [x] [Review][Patch] Validar invariantes cruzadas no BFF para nunca encaminhar `coupon_code` fora de `accepted/display` de teste nem aceitar sucesso incompleto [apps/web/src/bff/apiClient.ts:131]
- [x] [Review][Patch] Preservar `422` e mapear erros Laravel para campos em vez de convertê-los em `503` genérico [apps/web/src/bff/apiClient.ts:192]
- [x] [Review][Patch] Enviar o e-mail aparado e mover foco ao primeiro controle inválido preservando o valor digitado [apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx:229]
- [x] [Review][Patch] Corrigir abertura segura: reagendar timer, distinguir scroll restaurado, compartilhar lock e liberar lock/supressão se `showModal()` falhar [apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx:136]
- [x] [Review][Patch] Tornar fallback e diálogo descartáveis por todos os caminhos, fechando após cópia e restaurando foco também no Escape [apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx:217]
- [x] [Review][Patch] Adicionar guarda síncrona contra dois submits no mesmo tick [apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx:252]

## Dev Notes

### Decisões de escopo

- Esta story implementa aquisição, emissão/associação e entrega inicial do cupom. Aplicação manual e validação completa no carrinho/checkout pertencem à Story 4.2; cálculo/comparação do melhor benefício pertence às Stories 3.x/4.2.
- A regra vigente do PRD é 10%, sem valor mínimo, somente primeira compra e não cumulativa. Deve nascer como configuração/persistência Laravel e ser exposta ao frontend apenas por contrato público sanitizado.
- "Primeira compra" é restrição do cupom a ser revalidada contra a autoridade de pedidos/pagamentos quando o checkout existir. Esta story não deve criar Pedido nem fingir essa validação.
- Emissão não prova elegibilidade final. A microcopy deve dizer "sujeito à validação no checkout" e nunca confirmar que um e-mail com compra anterior receberá desconto.
- Não existe expiração aprovada. Persistir `expires_at = null`/equivalente e não mostrar prazo inventado.
- Reenvio/retry do mesmo e-mail deve reassociar o mesmo benefício; nunca gerar vários benefícios. A resposta pública permanece uniforme.
- Newsletter está explicitamente fora do escopo. A autorização pedida é específica para emissão/entrega do cupom e não pode ser reutilizada para marketing.
- Provedor de e-mail, retenção, base legal operacional, trusted proxies e chaves versionadas são gates de produção. O modo `display` permite concluir e testar a story sem fingir integração externa, mas não é canal para dados reais.

### Arquitetura obrigatória

```text
Browser
  -> GET Next.js same-origin /api/promotions/first-purchase-offer
    -> GET Laravel /api/v1/promotions/first-purchase-offer
  -> POST Next.js same-origin /api/promotions/first-purchase-coupons
    -> POST Laravel /api/v1/promotions/first-purchase-coupons
      -> Promotions/Application/IssueOrAssociateFirstPurchaseCoupon
        -> Promotions/Domain
        -> PostgreSQL repository
        -> delivery=email -> outbox/job -> EmailProvider adapter
        -> delivery=display somente local/teste
```

- Browser -> Next.js Frontend/BFF -> Laravel API. O BFF adapta transporte; Laravel é autoridade de regra, estado, idempotência, gates, retenção e entrega.
- PostgreSQL é fonte durável. Redis pode servir a rate limiting/fila, nunca ser a única fonte do cupom.
- Controllers, route handlers, Jobs e Eloquent não contêm regra de negócio principal.
- E-mail assíncrono deve ser registrado/transacionado e despachado após commit, sem duplicação.

### Estado atual e arquivos a preservar

- `apps/web/src/components/layout/PublicShell.tsx`
  - Atual: já recebe `globalSurface?: ReactNode` e o renderiza entre header e main.
  - Story: reutilizar o slot; mudança provavelmente desnecessária.
  - Preservar: skip link, um `header`, um `main#conteudo`, um footer e navegação acessível.
- `apps/web/src/app/(public)/layout.tsx`
  - Atual: apenas envolve children com `PublicShell`.
  - Story: injetar `FirstPurchaseDiscountDialog` em `globalSurface`.
  - Preservar: layout como Server Component.
- `apps/web/src/app/(public)/page.tsx`
  - Atual: home Server Component com hero, "Mais procurados" e continuação.
  - Story: nenhuma mudança funcional esperada.
  - Preservar: ausência de `use client`, metadata, ordem/primeira viewport e copy honesta da Story 1.4.
- `apps/web/src/bff/apiClient.ts`
  - Atual: valida `API_INTERNAL_URL`, timeout de 5 s, `no-store` e payload de health.
  - Story: estender ou extrair helper sem afrouxar validações/health.
- `apps/web/src/i18n/publicContent.ts` e `publicContent.types.ts`
  - Atual: todo conteúdo público pt-BR centralizado/tipado.
  - Story: acrescentar contrato completo da promoção.
  - Preservar: UTF-8, `qualityCopy`, placeholders e contratos existentes.
- `apps/web/src/app/globals.css`
  - Atual: tokens, foco, alvos de toque, mobile-first 320 px, breakpoints e reduced motion.
  - Story: acrescentar somente estilos do modal/form/status usando tokens.
- `apps/web/tests/e2e/foundation.spec.ts`
  - Atual: 14 testes de fundação/home/layout/health.
  - Story: manter intactos; preferir spec dedicado para promoção.
- `apps/api/routes/api.php`, `AppServiceProvider.php`, `app/Modules/README.md`
  - Atual: apenas health real; README reserva as quatro camadas.
  - Story: primeira implementação de domínio deve estabelecer o padrão sem violá-lo.

### Stack e versões a preservar

- Frontend/BFF: Node `24.x`, Next.js `16.3.0`, React/React DOM `19.2.0`, TypeScript `5.9.x`.
- Backend: PHP `8.5.x`, Laravel `13.23.x`, Predis `3.0.x`.
- Dados: PostgreSQL `18.x`, Redis `8-alpine` na stack local.
- Testes: Playwright `1.60.0`, PHPUnit `12.5.x`, Pint `1.30.x`.
- Não adicionar biblioteca de modal, form, validação, cupom ou geração de código; usar plataforma/Laravel/React existentes.
- Não alterar package/composer lockfiles nesta story.

### Segurança e privacidade

- E-mail é dado pessoal; minimizar coleta, acesso, resposta e log. Não usar e-mail bruto em rate-limit key, analytics ou exceptions.
- Código é segredo portador. Produção nunca o devolve por endpoint público; entrega deve ocorrer pelo canal de e-mail operacional.
- Em local/teste, `display` pode devolver código para E2E e desenvolvimento, mas o ambiente deve bloquear dados reais.
- Usar CSPRNG com pelo menos 80 bits de entropia, digest para lookup/validação e criptografia com `key_version` para cópia recuperável.
- HMAC de e-mail deve ter versão de chave. Rotação exige coexistência de chave antiga para lookup de registros existentes ou migração controlada.
- Constraint + transação devem garantir idempotência mesmo sob concorrência; validação client-side nunca substitui Laravel.
- Mensagens uniformes reduzem enumeração. Não confirmar se e-mail já comprou, já existe, já tem conta ou já recebeu cupom.
- A autorização do cupom não pode ser consentimento guarda-chuva. Newsletter, recuperação e analytics de marketing exigiriam escolhas separadas, desmarcadas e revogáveis, fora desta story.
- Não coletar dados reais até resolver/publicar política, retenção, direitos, subprocessadores quando aplicável e canal operacional. Feature flag desligada é requisito, não pendência opcional.

### UX e acessibilidade

- Reutilizar a semântica aprovada no protótipo (`sessionStorage`, intenção real, uma superfície, foco/retorno), mas não copiar sua regra demonstrativa nem seu HTML/CSS literalmente.
- Promoção automática não abre só por timer: exige engajamento novo, seguido de 700 ms, página visível, sem campo editável focado, suporte a `showModal()` e lock global livre.
- Não usar `window.name`. Se `sessionStorage` falhar, supressão dura apenas na memória viva da aba.
- Preferir `<dialog>.showModal()` para top layer, inert e Escape nativos; testar explicitamente foco inicial, ciclo Tab/Shift+Tab, botão fechar e restauração.
- Browser sem `<dialog>` não recebe abertura automática. A alternativa inline deve ser não modal, não bloqueante e testável.
- Para promoção automática em mobile, focar inicialmente o fechar/declinar evita abrir teclado virtual sem pedido da visitante.
- No modo `display`, não fechar antes de a pessoa copiar o código ou fechar explicitamente. Não salvar o código em storage, query string ou analytics.

### Testing Requirements

- Testes devem provar a divisão Browser/BFF/Laravel e não apenas mockar toda a cadeia no componente.
- Backend deve cobrir idempotência/concorrência, constraints, config, versão de autorização, rate limit, trusted proxies, rotação de chaves, falhas de entrega, retenção e ausência de PII.
- Playwright deve controlar o gatilho sem esperar timer longo/flaky; usar scroll/engajamento explícito e marcador estável de elegibilidade.
- Validar teclado, leitor de tela por atributos/roles, 320 px, overflow, 44 x 44, reduced motion, estados e retry.
- Preservar regressões da 1.4: home Server Component, "Mais procurados" cedo, metadata indexável, conteúdo pt-BR sem mojibake, placeholders e health BFF/API.

### Informação técnica atual

- Next.js App Router mantém pages/layouts como Server Components por padrão e recomenda Client Components somente para estado, handlers e APIs do browser. Isolar o modal como leaf client preserva o bundle e a home server-side.
- Laravel suporta Form Requests, rate limiting sobre cache/Redis e mailables enfileirados. E-mail dependente de registros recém-criados deve ser agendado depois do commit.
- `<dialog>` nativo com `showModal()` fornece top layer/inert e Escape; ainda exige mecanismo explícito de fechamento e decisão consciente de foco inicial. Não aplicar `tabindex` ao elemento `dialog`.
- Consentimento usado como base precisa representar escolha real, específica e informada. Nesta story, a autorização é limitada ao cupom; ativação real continua sujeita à validação jurídica/RGPD do projeto.

Fontes oficiais consultadas:

- https://nextjs.org/docs/app/getting-started/server-and-client-components
- https://nextjs.org/docs/app/api-reference/directives/use-client
- https://laravel.com/docs/13.x/validation
- https://laravel.com/docs/13.x/rate-limiting
- https://laravel.com/docs/13.x/mail
- https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/dialog
- https://www.edpb.europa.eu/system/files/2026-04/edpb-summary-consent_en.pdf

### Project Structure Notes

Arquivos UPDATE esperados:

- `apps/web/src/app/(public)/layout.tsx`
- `apps/web/src/app/globals.css`
- `apps/web/src/i18n/publicContent.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/src/features/public-store/publicLayoutContent.ts` somente se precisar export explícito
- `apps/web/src/bff/apiClient.ts`
- `apps/web/.env.example`
- `apps/api/routes/api.php`
- `apps/api/app/Providers/AppServiceProvider.php`
- `apps/api/.env.example`
- `.github/workflows/smoke.yml` somente se habilitar worker; evitar no modo `display`

Arquivos/diretórios NEW esperados:

- `apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx`
- `apps/web/src/app/api/promotions/first-purchase-offer/route.ts`
- `apps/web/src/app/api/promotions/first-purchase-coupons/route.ts`
- `apps/web/tests/e2e/first-purchase-discount.spec.ts`
- `apps/api/config/promotions.php`
- `apps/api/app/Modules/Promotions/Domain/**`
- `apps/api/app/Modules/Promotions/Application/**`
- `apps/api/app/Modules/Promotions/Infrastructure/**`
- `apps/api/app/Modules/Promotions/Interfaces/Http/**`
- `apps/api/database/migrations/*_create_promotion_campaigns_table.php`
- `apps/api/database/migrations/*_create_promotion_coupons_table.php`
- `apps/api/database/migrations/*_create_promotion_outbox_table.php`
- `apps/api/tests/Unit/Modules/Promotions/**`
- `apps/api/tests/Feature/FirstPurchaseCouponTest.php`

Não criar módulo `Pricing` apenas para a captura se isso misturar emissão com cálculo do carrinho. `Promotions` mantém a fronteira desta story e poderá ser consumido por Pricing/Cart nas stories futuras.

### References

- [Source: `_bmad-output/planning-artifacts/epics.md` - Epic 1 e Story 1.5, linhas 530-558]
- [Source: `_bmad-output/planning-artifacts/epics.md` - FR-14, AR-18, UX-DR19]
- [Source: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md` - FR-14, NFR-1/3/4/7, gates F0/M2]
- [Source: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/addendum.md` - escopo do MVP e dados/privacidade]
- [Source: `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/review-security-privacy-eu.md` - M-3]
- [Source: `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md` - AD-1/2/3/4/5/9/12/13, Stack, Estrutura Inicial, Decisões Adiadas]
- [Source: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md` - modal, discount-modal, responsividade, acessibilidade e dependências]
- [Source: `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md` - tokens e especificação do discount-modal/form-field]
- [Source: `prototype/app.js` e `prototype/index.html` - padrão comportamental de sessão/engajamento/foco, apenas referência]
- [Source: `_bmad-output/implementation-artifacts/1-4-criar-a-home-publica-com-entrada-para-os-produtos-mais-procurados.md` - learnings, testes e arquivos]
- [Source: `apps/web/AGENTS.md` - obrigação de consultar docs locais do Next.js antes de alterar código]
- [Source: `apps/web/package.json`, `apps/api/composer.json`, lockfiles e configs - versões reais]

## Dev Agent Record

### Agent Model Used

Codex GPT-5

### Debug Log References

- 2026-08-10: `php artisan route:list --path=promotions` passou e mostrou `GET /api/v1/promotions/first-purchase-offer` e `POST /api/v1/promotions/first-purchase-coupons`.
- 2026-08-10: `php artisan test --testsuite=Unit --filter PromotionDomainTest` passou: 2 testes, 67 assertions.
- 2026-08-10: `vendor/bin/pint --test` passou após aplicar `vendor/bin/pint`.
- 2026-08-10: `npm run lint`, `npm run typecheck` e `npm run build` passaram em `apps/web`.
- 2026-08-10: `npx playwright test tests/e2e/first-purchase-discount.spec.ts` passou: 6 testes.
- 2026-08-10: `composer test -- --filter FirstPurchaseCouponTest` falhou porque o script Composer não aceita `--filter`.
- 2026-08-10: `php artisan test --filter FirstPurchaseCouponTest --stop-on-failure` ficou bloqueado sem PostgreSQL/Redis acessíveis.
- 2026-08-10: `docker compose -f infra\docker\compose.yaml up -d postgres redis` falhou porque Docker Desktop não está ativo: pipe `dockerDesktopLinuxEngine` ausente.
- 2026-08-10: `Get-NetTCPConnection -LocalPort 5432/6379` não encontrou listeners; `php artisan migrate:status` retornou conexão recusada em `127.0.0.1:5432`.
- 2026-08-10: fallback SQLite em memória não pôde ser usado porque esta instalação PHP não possui driver SQLite (`could not find driver`).
- 2026-08-10: code review adversarial consolidou 2 decisões e 15 patches; as decisões foram resolvidas para trusted proxies padrão e fingerprint case-insensitive com grafia de entrega preservada.
- 2026-08-10: `vendor/bin/pint --test`, lint de sintaxe PHP e `php artisan test --testsuite=Unit --filter PromotionDomainTest` passaram após os patches.
- 2026-08-10: `npm run lint`, `npm run typecheck` e `npm run build` passaram após os patches.
- 2026-08-10: `npx playwright test tests/e2e/first-purchase-discount.spec.ts` passou com 12 testes; o teste de UTF-8 da foundation também passou isoladamente.
- 2026-08-10: a suíte E2E completa chegou a 24/26; após corrigir o item de copy condicional, o respectivo teste passou isoladamente. O único cenário ainda bloqueado é `/api/health`, que exige Laravel/PostgreSQL/Redis ativos.
- 2026-08-10: `php artisan test --filter FirstPurchaseCouponTest --stop-on-failure` voltou a aguardar conexão até timeout; `docker info` confirmou Docker Desktop indisponível e não há listeners em 5432/6379.
- 2026-08-10: Docker Desktop foi iniciado, PostgreSQL 18 e Redis 8 ficaram `healthy`, e as quatro migrations de Promotions foram aplicadas com sucesso no banco local.
- 2026-08-10: teste de concorrência com dois processos Laravel/PostgreSQL provou que primeiras solicitações simultâneas retornam o mesmo `request_id`/cupom e persistem uma única linha.
- 2026-08-10: `composer test` final passou com 18 testes e 144 assertions; `vendor/bin/pint --test` passou.
- 2026-08-10: `npm run lint`, `npm run typecheck` e `npm run build` finais passaram; `npm run test:e2e` passou com 33 testes, incluindo contrato BFF, foco, alvos de 44 px, reduced motion, modo e-mail e health integrado.
- 2026-08-10: servidor Laravel temporário e containers PostgreSQL/Redis usados na validação foram encerrados ao final, sem remover volumes.

### Completion Notes List

- Story revisada para remover ambiguidades de segurança, privacidade, UX, idempotência, configuração e ativação.
- `display` agora é restrito a local/teste; produção exige entrega por e-mail operacional ou feature desabilitada.
- Contrato público de oferta, gates fail-closed, retenção/revogação, chaves versionadas, trusted proxies e outbox idempotente foram incorporados aos ACs e tarefas.
- Implementação inicial adicionada em Laravel: configuração `promotions.first_purchase`, domínio Promotions, migrations PostgreSQL, contrato REST, gates fail-closed, canonicalização/fingerprint, geração de cupom, idempotência por repositório transacional, outbox e serviço mínimo de anonimização.
- Implementação inicial adicionada em Next.js: BFF same-origin para oferta/solicitação, componente cliente isolado no `PublicShell.globalSurface`, conteúdo centralizado, estilos acessíveis e E2E do diálogo.
- Na implementação inicial, Tasks/Subtasks permaneceram abertas enquanto PostgreSQL/Redis estavam indisponíveis; após ativar a infraestrutura e concluir todas as validações, os itens foram marcados como concluídos.
- Os 17 action items da revisão foram implementados: cadeia de trusted proxies, rate limits configuráveis, identidade promocional case-insensitive, rotação HMAC/criptográfica, concorrência recuperável, autorização histórica, outbox não rearmável, job de entrega, direitos do titular, oferta versionada por todos os termos, contrato BFF estrito, mapeamento 422 e correções de acessibilidade/concorrência do diálogo.
- Migrations, concorrência, outbox/job, rotação de chaves, direitos do titular, BFF e experiência acessível foram validados; a story foi promovida para `done` e sincronizada com o sprint.

### File List

- `_bmad-output/implementation-artifacts/1-5-exibir-captura-de-e-mail-com-cupom-de-primeira-compra.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `apps/api/.env.example`
- `apps/api/app/Providers/AppServiceProvider.php`
- `apps/api/bootstrap/app.php`
- `apps/api/routes/api.php`
- `apps/api/config/promotions.php`
- `apps/api/app/Modules/Promotions/Application/IssueFirstPurchaseCouponResult.php`
- `apps/api/app/Modules/Promotions/Application/IssueOrAssociateFirstPurchaseCoupon.php`
- `apps/api/app/Modules/Promotions/Application/Privacy/AnonymizeFirstPurchaseCoupon.php`
- `apps/api/app/Modules/Promotions/Application/Privacy/ManageFirstPurchaseCouponPrivacy.php`
- `apps/api/app/Modules/Promotions/Domain/CouponCodeGenerator.php`
- `apps/api/app/Modules/Promotions/Domain/EmailCanonicalizer.php`
- `apps/api/app/Modules/Promotions/Domain/FirstPurchasePromotionSettings.php`
- `apps/api/app/Modules/Promotions/Domain/PromotionCouponRepository.php`
- `apps/api/app/Modules/Promotions/Domain/PromotionUnavailable.php`
- `apps/api/app/Modules/Promotions/Domain/PromotionCodeCipher.php`
- `apps/api/app/Modules/Promotions/Infrastructure/Delivery/EmailProvider.php`
- `apps/api/app/Modules/Promotions/Infrastructure/Delivery/LaravelMailEmailProvider.php`
- `apps/api/app/Modules/Promotions/Infrastructure/Delivery/Jobs/DeliverFirstPurchaseCoupon.php`
- `apps/api/app/Modules/Promotions/Infrastructure/Persistence/PostgresPromotionCouponRepository.php`
- `apps/api/app/Modules/Promotions/Interfaces/Http/Controllers/FirstPurchaseCouponController.php`
- `apps/api/app/Modules/Promotions/Interfaces/Http/Controllers/FirstPurchaseOfferController.php`
- `apps/api/app/Modules/Promotions/Interfaces/Http/Requests/FirstPurchaseCouponRequest.php`
- `apps/api/database/migrations/2026_08_10_000001_create_promotion_campaigns_table.php`
- `apps/api/database/migrations/2026_08_10_000002_create_promotion_coupons_table.php`
- `apps/api/database/migrations/2026_08_10_000003_create_promotion_outbox_table.php`
- `apps/api/database/migrations/2026_08_10_000004_create_promotion_authorizations_table.php`
- `apps/api/tests/Feature/FirstPurchaseCouponTest.php`
- `apps/api/tests/Unit/Modules/Promotions/PromotionDomainTest.php`
- `apps/web/src/app/(public)/layout.tsx`
- `apps/web/.env.example`
- `apps/web/src/app/globals.css`
- `apps/web/src/app/api/promotions/first-purchase-coupons/route.ts`
- `apps/web/src/app/api/promotions/first-purchase-offer/route.ts`
- `apps/web/src/bff/apiClient.ts`
- `apps/web/src/features/first-purchase-discount/FirstPurchaseDiscountDialog.tsx`
- `apps/web/src/i18n/publicContent.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/tests/e2e/first-purchase-discount.spec.ts`
- `apps/web/tests/e2e/promotion-bff.spec.ts`
