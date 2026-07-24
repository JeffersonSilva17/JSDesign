# Intenção — Website e loja online JS Designs

## Intenção escolhida

Criar uma loja online premium, intuitiva, segura e escalável em que a cliente descubra produtos, escolha, personalize, pague e acompanhe a encomenda sem depender do WhatsApp. A essência é tornar simples e prática a escolha e a finalização da compra, enquanto a operação absorve a complexidade de personalização e produção.

## Princípios inegociáveis

- Comunicação em português do Brasil, com acentuação correta e UTF-8.
- Elegância, confiança, exclusividade e profissionalismo; estética minimalista, sofisticada e atemporal.
- Branco, bege muito claro, dourado champanhe, preto suave e taupe claro; muito espaço em branco, fotografias grandes, ótima tipografia e animações suaves.
- Clareza, acabamento visível e acompanhamento transparente como fundamentos do posicionamento premium.
- Divulgação progressiva: mostrar somente a informação necessária em cada etapa, sem excesso visual ou textual.
- Navegação intuitiva, pesquisa fácil de encontrar e suporte humano disponível sem se tornar obrigatório.
- WhatsApp restrito a suporte e projetos totalmente exclusivos.

## Utilizadores e resultados

Para clientes que organizam celebrações e procuram variedade, qualidade artesanal, preço justo e personalização simples:

- encontrar rapidamente produtos por personagem ou tema;
- entender preço, prazo, material, quantidade e personalização antes da compra;
- comprar sem cadastro obrigatório;
- fornecer dados, aprovar a arte e acompanhar prazos e produção em uma área segura;
- receber o produto físico em toda a Europa ou o arquivo digital pelo canal escolhido.

Para a JS Designs:

- reduzir navegação confusa, pedidos de orçamento e dependência do WhatsApp;
- organizar briefing, aprovação, produção, qualidade, fiscalidade e atendimento;
- transformar descoberta no Instagram em compra recuperável e recompra.

## Modelos de produto

- **Produtos digitais prontos:** sem customização e com entrega automática após a compra.
- **Convites digitais personalizados:** compra do modelo seguida de briefing, criação e aprovação. Prazos por complexidade: Essenciais em até 24 horas, Interativos em até 48 horas, Infinito em até 72 horas e Cinemágicos entre três e cinco dias úteis.
- **Lembrancinhas e kits:** quantidades de 20, 30, 40, 50 ou mais unidades; personalização básica com nome e idade incluída. A miniatura enviada pela cliente não tem custo; sua criação pela JS Designs tem valor fixo, cobrado uma vez por miniatura e reutilizável em itens compatíveis do pedido.
- **Topos de bolo:** modelos prontos e opção separada de customização.
- **Projeto exclusivo:** alternativa para temas ou modelos ausentes do catálogo, conduzida pelo suporte.

## Jornada essencial

1. Descobrir produtos mais pedidos, navegar pela loja ou pesquisar por personagem ou tema.
2. Ver resultados exatos agrupados por tipo de produto; abaixo, sugestões semelhantes claramente separadas.
3. Consultar uma página de produto enxuta, com provas reais de acabamento, preço, prazo, material, quantidade e regras aplicáveis.
4. Comprar pelo CTA principal definido: **Comprar agora** na maioria dos produtos e convites digitais; **Comprar já** nas lembrancinhas.
5. Configurar no carrinho a personalização das lembrancinhas e adicionar poucos complementos contextuais com desconto progressivo de conjunto.
6. Finalizar em checkout de uma página, sem cadastro obrigatório, com entrega, pagamento e NIF opcional; botão final **Finalizar e comprar**.
7. Após o pagamento, receber acesso seguro e preencher um briefing adaptado, com salvamento automático e dados comuns reutilizados no mesmo evento.
8. Receber prévias pelo canal escolhido, aprovar ou solicitar alterações na área da cliente e confirmar a revisão dos dados antes da produção.
9. Acompanhar a linha do tempo: Pedido confirmado, Aguardando briefing, Arte em criação, Aguardando aprovação, Em produção, Verificação de qualidade, Preparando para envio e Enviado.

## Regras críticas

- O briefing fica disponível imediatamente após a confirmação do pagamento, deve ser preenchido em até três dias corridos e não cancela automaticamente o pedido após esse prazo.
- Cada arte personalizada inclui até três rodadas gratuitas de alterações, com versões numeradas, histórico e contador restante.
- O prazo normal de produção física é de sete dias corridos após a entrega do briefing completo e pausa quando a continuidade depende de resposta, alteração ou aprovação da cliente.
- Cada encomenda finalizada é fotografada antes da embalagem como evidência da verificação de qualidade.
- O frete é calculado pelo endereço e atende toda a Europa.
- Transferências bancárias reservam o pedido por 48 horas; sem confirmação, o pedido é cancelado e a cliente é notificada.
- Urgências inferiores ao prazo normal exigem contato e confirmação manual de viabilidade e taxa; não há promessa nem cálculo automático.
- A fatura é emitida em cada venda; o checkout oferece NIF opcional.
- Chat automático e WhatsApp aparecem como botões laterais compactos e ficam ocultos no checkout. O chat não inventa respostas; quando necessário, encaminha para atendimento posterior. Para clientes autenticadas, pode consultar com segurança status, prazo e próxima ação.
- O cupom individual de primeira compra concede 10%, sem valor mínimo, não acumula com outras promoções e o sistema aplica o melhor benefício.

## Prioridades do lançamento

Entregar a jornada completa de descoberta, compra, personalização, aprovação, produção e acompanhamento, junto ao núcleo administrativo, fiscal, internacional, de segurança e RGPD. Incluir no núcleo o briefing inteligente, a área da cliente, a fila visual de produção, a ficha automática por encomenda e as notificações pelo canal escolhido.

Depois do núcleo: comparação avançada de convites, pagamentos adicionais, pesquisa preditiva, favoritos, avaliações, suporte automático, recuperação de orçamentos, SEO e automações.

## Segurança e RGPD

- Segurança e privacidade são requisitos de lançamento e critérios de aceite, seguindo OWASP ASVS nível 2, RGPD por design e PCI DSS para pagamentos.
- Cartões são processados somente por fornecedor certificado, com campos hospedados ou tokenização; nunca armazenar número completo, CVV ou PIN.
- Aplicar HTTPS/TLS, HSTS, encriptação em repouso, gestão segura de segredos, cookies seguros, CSRF, CSP, WAF, limitação de tentativas e proteção contra bots.
- Usar Argon2id para palavras-passe, MFA obrigatório na administração, acesso por função e privilégio mínimo, com auditoria de dados e operações sensíveis.
- Validar e analisar uploads; armazená-los de forma privada e entregá-los por links temporários assinados.
- Fotografias de crianças e miniaturas não podem ser públicas nem usadas em marketing sem consentimento separado. Eliminá-las após a finalidade, salvo conservação opcional autorizada para recompra.
- Minimizar dados, definir retenção e permitir acesso, correção e eliminação. Encriptar backups e testar restauração e resposta a incidentes.
- Exigir revisão de código, análise de dependências, testes automatizados de segurança, varredura de vulnerabilidades e teste de intrusão antes do lançamento e após mudanças relevantes.
- Usar a mensagem **Pagamentos protegidos e processados por parceiros certificados**.

## Exclusões

- Consultoria guiada por orçamento.
- Vários CTAs por modalidade.
- Cartões de personagens como caminho principal da página inicial.
- Suporte durante o checkout.
- Urgência automática.
- Recompensa por avaliações.
- No lançamento: calendário de celebrações, fidelização, cartões-presente, recomendações avançadas, logins sociais, blog completo, francês, espanhol, pré-visualização automática e relatórios avançados.
