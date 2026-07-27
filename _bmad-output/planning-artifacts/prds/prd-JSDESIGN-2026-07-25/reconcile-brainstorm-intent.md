# Reconciliação de entrada — `brainstorm-intent.md`

## Escopo e precedência

- **Entrada reconciliada:** `_bmad-output/brainstorming/brainstorm-website-js-designs-2026-07-23/brainstorm-intent.md`
- **Artefatos comparados:** `prd.md`, `addendum.md` e `.memlog.md`.
- **Regra aplicada:** decisões, mudanças e sobrescritas posteriores registradas no memlog prevalecem sobre a fonte de brainstorming.
- **Objetivo:** identificar conteúdo preservado, conteúdo legitimamente superado e lacunas ou conflitos ainda existentes. Esta análise não modifica o PRD nem o adendo.

## Resultado geral

A intenção foi incorporada com alta fidelidade. O PRD preserva a visão de uma loja premium e autônoma, diferencia as modalidades de produto, cobre descoberta, compra, personalização, aprovação, produção, entrega e administração, e converte grande parte das regras da fonte em requisitos verificáveis com IDs estáveis. O adendo preserva direção visual e detalhes técnicos adequados a UX, arquitetura e implementação.

As diferenças mais importantes decorrem de decisões posteriores válidas do memlog: prazo do Briefing em dias úteis, início da Produção após Aprovação Final, simplificação dos Tipos de Convite e respectivos prazos, SEO, Atendimento Automático e três idiomas no MVP. Restam quatro conflitos ou lacunas materiais e alguns pontos secundários.

## 1. Conteúdo fielmente preservado

### 1.1 Visão, público e proposta de valor

Preservado no PRD:

- loja de lembrancinhas de papelaria personalizada, convites digitais e arquivos digitais prontos, sem associação com moda;
- experiência premium, intuitiva, segura, escalável e sem dependência obrigatória do WhatsApp;
- simplificação da descoberta e da compra para a cliente, com a complexidade operacional absorvida pela JS Designs;
- clareza de preço, prazo, quantidade, material, personalização, entrega e condições antes do pagamento;
- compra sem cadastro prévio obrigatório;
- área protegida para Briefing, Prévias, Aprovação, arquivos, Faturas e próximas ações;
- operação ponta a ponta sem planilhas paralelas.

A estética minimalista, sofisticada e atemporal, a paleta branca/bege/dourado champanhe/taupe/preto suave, o espaço em branco, as fotografias reais, a tipografia e as animações suaves foram preservados adequadamente no `addendum.md`.

### 1.2 Descoberta e catálogo

Preservado:

- entrada por Google, Instagram, link direto ou navegação interna;
- busca destacada por tema ou personagem;
- resultados exatos agrupados por tipo;
- semelhantes em seção separada;
- estado sem resultado com acesso a Projeto Exclusivo;
- produtos mais pedidos e prova de acabamento na página inicial;
- páginas de produto enxutas com informações comerciais e materiais essenciais;
- personagens não constituem o caminho principal da página inicial.

### 1.3 Compra e checkout

Preservado:

- CTA “Comprar já” para lembrancinhas;
- CTA “Comprar agora” para convites;
- CTA final “Finalizar e comprar”;
- complementos contextuais opcionais no Carrinho;
- aplicação automática do melhor benefício sem acumulação indevida;
- Checkout em página única, com NIF opcional e compra como visitante;
- pagamento por parceiro certificado, sem armazenamento de número completo de cartão, CVV ou PIN;
- mensagem “Pagamentos protegidos e processados por parceiros certificados.”;
- transferência bancária com reserva de 48 horas, cancelamento e notificação em caso de não confirmação;
- Fatura em toda venda;
- urgência somente após avaliação humana, sem cálculo ou promessa automática.

### 1.4 Briefing, criação, aprovação e produção

Preservado:

- Briefing adaptado após pagamento confirmado;
- salvamento automático e reutilização de dados;
- atraso no Briefing sem cancelamento automático do Pedido;
- Prévias numeradas e histórico;
- até três Rodadas de Alteração gratuitas, com contador;
- Aprovação Final explícita e versão bloqueada como referência oficial;
- pausa do prazo quando a continuidade depende da cliente;
- fotografia privada da encomenda física antes da postagem;
- linha do tempo da encomenda, Próxima Ação e notificações acionáveis;
- fila operacional, Ficha de Produção, Verificação de Qualidade e fila de exceções.

### 1.5 Produtos e entregas

Preservado:

- Produto Digital Pronto sem personalização, entregue automaticamente após confirmação do pagamento;
- convite personalizado adquirido antes da criação e sujeito a dados, Arte e Aprovação;
- lembrancinhas e kits em lotes de 20, 30, 40, 50 ou mais;
- Topos de bolo como categoria;
- Projeto Exclusivo sujeito a avaliação humana;
- entrega de convite por e-mail ou WhatsApp;
- entrega física com cálculo por endereço e rastreamento.

### 1.6 Segurança, privacidade e conformidade

Preservado no PRD ou adendo:

- OWASP ASVS nível 2, RGPD por design e escopo PCI DSS;
- TLS/HSTS, proteção contra CSRF, injeção, abuso e automação maliciosa, política de segurança de conteúdo e gestão segura de segredos;
- MFA administrativo, papéis, privilégio mínimo e auditoria;
- uploads privados, validados, analisados e distribuídos com autorização ou links temporários;
- fotografias de crianças, Miniaturas e Prévias privadas por padrão, sem marketing sem consentimento separado;
- minimização, retenção, direitos das titulares, backups protegidos, restauração e resposta a incidentes;
- revisão de código, análise de dependências, testes de segurança e avaliação independente antes do lançamento;
- governança de propriedade intelectual para personagens e demais ativos.

### 1.7 Exclusões

Preservado:

- consultoria guiada por orçamento;
- múltiplos CTAs concorrentes na mesma modalidade;
- personagens como caminho principal da página inicial;
- urgência automática;
- recompensa por avaliações;
- calendário de celebrações, fidelização, cartões-presente, recomendações avançadas, login social, blog completo, francês, pré-visualização automática, comparação avançada de convites e relatórios avançados fora do MVP.

Favoritos, avaliações, busca preditiva, recuperação automatizada do Carrinho, recompra rápida e automações adicionais aparecem corretamente como oportunidades posteriores.

## 2. Decisões posteriores que prevalecem corretamente

Não constituem lacunas:

1. **Loja de moda rejeitada:** o memlog confirma que a JS Designs vende papelaria personalizada, lembrancinhas, convites e arquivos digitais.
2. **Briefing:** três dias **úteis**, em vez dos três dias corridos da fonte.
3. **Produção física:** sete dias corridos contados da **Aprovação Final**, em vez do Briefing completo.
4. **Primeira Arte física:** até 24 horas após Briefing completo.
5. **Tipos e prazos de convite:** Convite Padrão em até 24 horas e Convite Complexo em até 48 horas; essa regra supera Essenciais, Interativos, Infinito e Cinemágicos e seus prazos originais.
6. **Produto Digital Pronto:** definido posteriormente como arquivo editável compatível com Silhouette Studio, enviado imediatamente por e-mail.
7. **SEO:** integra o MVP porque Google foi confirmado como origem principal da descoberta.
8. **Atendimento Automático:** integra o MVP, com respostas exclusivamente cadastradas e escalonamento humano.
9. **Idiomas:** português do Brasil, inglês e espanhol integram o lançamento; isso supera a exclusão original do espanhol.
10. **Métricas:** os primeiros 30 dias formarão o baseline; metas quantitativas serão definidas para os 90 dias seguintes, sem invenção de números.

## 3. Lacunas e conflitos principais

### Alta — Regra do cupom de primeira compra foi rebaixada indevidamente a pressuposto

**Fonte:** cupom individual de primeira compra de 10%, sem valor mínimo, não cumulativo; o sistema aplica o melhor benefício.

**Estado atual:** FR-14 preserva apenas a aplicação do melhor benefício e a não acumulação. O índice de pressupostos afirma que o cupom de 10% “depende de confirmação de prioridade e regras finais”, e a pergunta aberta 13 volta a tratar sua regra como indefinida.

**Problema:** não existe decisão posterior no memlog que revogue ou coloque em dúvida o desconto explícito da fonte. Foram perdidos três elementos: caráter individual de primeira compra, percentual de 10% e ausência de valor mínimo.

**Reconciliação recomendada:** preservar esses três elementos como decisão de origem ou obter uma sobrescrita explícita da proprietária antes de mantê-los como questão aberta.

### Alta — Ocultação do suporte no Checkout virou questão aberta

**Fonte:** Chat e WhatsApp ficam ocultos no Checkout; “suporte durante o checkout” também aparece nas exclusões.

**Estado atual:** FR-16 e FR-37 apenas impedem sobreposição, abertura automática ou competição com o CTA. A pergunta aberta 4 pede decidir entre ocultar o Suporte Online e oferecer um link compacto.

**Problema:** o memlog registra a necessidade de esclarecer o comportamento, mas não contém decisão posterior que revogue a regra explícita da fonte. Transformar uma decisão da entrada em pergunta aberta enfraquece a reconciliação.

**Reconciliação recomendada:** manter Chat e WhatsApp ocultos no Checkout, ou registrar uma decisão posterior explícita autorizando ajuda compacta.

### Alta — Cobertura de frete para toda a Europa foi enfraquecida

**Fonte:** “o frete [...] atende toda a Europa”.

**Estado atual:** FR-20 fala em cálculo conforme endereço e em restrições de destino; o PRD deixa os países europeus do lançamento como pergunta aberta. “Frete europeu” aparece no MVP, mas não garante a abrangência originalmente declarada.

**Problema:** não há sobrescrita posterior no memlog reduzindo a cobertura. A pergunta fiscal e logística é válida, mas deveria esclarecer como cumprir a abrangência, não se a abrangência existe.

**Reconciliação recomendada:** manter “toda a Europa” como intenção comercial e tratar países temporariamente indisponíveis, moedas, transportadoras e requisitos fiscais como dependências ou exceções de lançamento explicitamente aprovadas.

### Alta — Regras de Miniatura e personalização básica de produtos físicos não foram preservadas integralmente

**Fonte:** nome e idade incluídos nas lembrancinhas; Miniatura enviada pela cliente sem custo; criação pela JS Designs cobrada uma única vez por Miniatura e reutilizável em itens compatíveis do mesmo Pedido.

**Estado atual:** FR-7 informa personalização “incluída ou opcional”; FR-11 permite “ativar ou dispensar” personalização básica. O PRD define opções de Miniatura, mas só confirma preço de €5 para convite. Não explicita gratuidade do arquivo fornecido, cobrança única da criação nem reutilização entre itens físicos compatíveis.

**Problema:** a aplicação do valor de €5 a físicos realmente permanece aberta, mas gratuidade do arquivo da cliente, cobrança única por criação e reutilização no Pedido são regras independentes do valor e não foram revogadas.

**Reconciliação recomendada:** confirmar nome e idade como personalização básica incluída nas lembrancinhas e preservar as regras de gratuidade, cobrança única e reutilização, deixando aberto apenas o preço da criação para produtos físicos.

## 4. Lacunas secundárias

### Média — Topos de bolo sem diferenciação funcional

A fonte distingue modelos prontos e opção separada de customização. O PRD inclui Topos de bolo na arquitetura de informação, mas não define como a cliente distingue ou compra essas duas modalidades.

### Média — Alguns objetivos comerciais qualitativos ficaram implícitos

“Preço justo”, redução de pedidos de orçamento e transformação da descoberta no Instagram em compra recuperável e recompra não aparecem como objetivos nomeados. Há cobertura indireta em clareza de preço, autonomia, origem de aquisição e funcionalidades futuras, mas preservar a formulação ajudaria a avaliar se o produto entrega o posicionamento desejado.

### Média — Controles de segurança específicos foram generalizados

O conjunto geral está bem representado por ASVS nível 2 e pelas orientações do adendo, mas a fonte nomeia WAF, cookies seguros, limitação de tentativas, proteção contra bots, Argon2id, criptografia em repouso e pentest também após mudanças relevantes. Alguns aparecem apenas como conceitos mais amplos e o requisito de repetição do pentest após mudanças relevantes não está explícito.

### Baixa — “Recuperação de orçamentos” desapareceu do pós-lançamento

A fonte lista recuperação de orçamentos após o núcleo. O PRD exclui consultoria guiada por orçamento e prioriza recuperação automatizada de Carrinho. Se “orçamento” era apenas terminologia antiga para fluxo comercial conversacional, a omissão é coerente; caso represente propostas de Projeto Exclusivo, falta preservar a recuperação desse fluxo.

## 5. Conclusão de reconciliação

O PRD e o adendo representam corretamente a maior parte do `brainstorm-intent.md`, incluindo suas nuances de experiência premium, autonomia, confiança, divulgação progressiva e operação pós-compra. As decisões posteriores do memlog estão, em geral, aplicadas corretamente e registradas no adendo como alternativas superadas.

Antes da finalização, os pontos que mais exigem resolução são:

1. restaurar ou explicitamente revogar a regra completa do cupom de primeira compra;
2. resolver a divergência sobre suporte oculto no Checkout;
3. confirmar se “toda a Europa” permanece compromisso de lançamento;
4. completar as regras de personalização básica e Miniatura para produtos físicos.

