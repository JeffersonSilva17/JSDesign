---
title: PRD — Loja Online JS Designs
status: final
created: 2026-07-25
updated: 2026-07-26
---

# PRD — Loja Online JS Designs

## 0. Finalidade do documento

Este PRD define o produto, o escopo de lançamento, as jornadas, os requisitos funcionais e os requisitos de qualidade da loja online JS Designs. Ele orienta UX, arquitetura, criação de épicos e histórias, desenvolvimento, testes e preparação operacional. Requisitos descrevem capacidades e resultados verificáveis; decisões de mecanismo e tecnologia ficam no `addendum.md`.

Fontes principais:

- `brainstorm-intent.md`;
- `website-blueprint.md`;
- `implementation-roadmap.md`;
- protótipo atual em `prototype/index.html`;
- referência visual `referencia-gio-home.png`;
- decisões confirmadas durante a descoberta guiada.

Quando houver conflito, a decisão mais recente da proprietária registrada neste processo prevalece sobre os materiais anteriores.

## 1. Público e necessidades

### 1.1 Clientes principais

- **Organizadora de celebração:** procura lembrancinhas, papelaria ou convites bonitos, confiáveis e adequados ao tema, mas não quer depender de trocas manuais para entender preço, prazo e personalização.
- **Compradora de arquivo digital:** deseja um arquivo pronto, editável e compatível com o Silhouette Studio, com compra e recebimento imediatos.
- **Operadora da JS Designs:** precisa transformar pedidos em arte, aprovação, produção ou entrega digital, faturamento e acompanhamento sem planilhas paralelas.

### 1.2 Trabalhos a realizar

- Encontrar rapidamente produtos por tema, personagem, ocasião ou tipo.
- Comparar opções sem anúncios, ruído ou excesso de informações.
- Entender preço total, quantidade, material, prazo, personalização, entrega e condições antes de pagar.
- Comprar como visitante, com segurança e sem atendimento obrigatório.
- Informar os dados da personalização de forma simples, salvar o progresso e evitar repetição.
- Aprovar a arte com clareza sobre versão, prazo e alterações restantes.
- Saber o estado do pedido, a próxima ação e a previsão atualizada.
- Receber produtos físicos em destinos europeus e produtos digitais pelo canal adequado.
- Obter ajuda automática confiável e chegar ao atendimento humano quando necessário.
- Operar cada pedido usando uma única fonte de verdade.

### 1.3 Limites de público no lançamento

- O lançamento é B2C; fluxos de atacado, revenda e contratos empresariais não terão tratamento específico.
- Projetos totalmente exclusivos serão aceitos, mas continuarão sujeitos a avaliação humana de viabilidade.
- A plataforma não será um marketplace de vendedores externos nem uma ferramenta genérica de criação gráfica.

### 1.4 Contexto da atividade

A JS Designs opera em Portugal como atividade de pessoa singular/trabalhadora independente, com atividade aberta no código CIRS 1336. A solução fiscal, o cadastro do provedor e a conta de recebimento devem refletir essa titularidade.

## 2. Visão e estratégia de sucesso

### 2.1 Visão confirmada

A JS Designs será uma loja online premium de papelaria personalizada, lembrancinhas, convites e arquivos digitais, na qual a cliente encontra o que procura, entende claramente preço e prazo e conclui a compra sem depender de atendimento.

A experiência reduz a complexidade da personalização por meio de busca simples, informação progressiva, Briefing pós-compra, Aprovação organizada e acompanhamento transparente. Para a JS Designs, a mesma plataforma conecta venda, criação, Produção, qualidade, entrega e faturamento sem planilhas paralelas.

A promessa central é unir a praticidade de uma compra online à atenção e ao acabamento de um trabalho personalizado.

### 2.2 Estratégia inicial de medição

Os primeiros 30 dias após o lançamento formarão o baseline da loja. A instrumentação deverá medir, desde o primeiro dia:

- visibilidade orgânica e visitas qualificadas;
- progressão da busca e das páginas de produto até a compra;
- compras concluídas e abandono do Checkout;
- proporção de pedidos concluídos sem atendimento humano obrigatório;
- tempo de Briefing, Arte, Aprovação, Produção e entrega;
- cumprimento dos prazos prometidos;
- erros, retrabalho, cancelamentos e contatos de suporte;
- acessibilidade das jornadas essenciais;
- uso e conclusão das jornadas em português do Brasil, inglês e espanhol.

Ao final do período de baseline, a JS Designs definirá metas quantitativas para os 90 dias seguintes. A otimização não poderá aumentar vendas por meio de informações confusas, promessas de prazo não sustentadas, barreiras de acessibilidade ou dependência maior do atendimento manual.

## 3. Jornadas confirmadas

### UJ-1 — Mariana encontra e encomenda lembrancinhas personalizadas sem depender de atendimento

- **Persona e contexto:** Mariana está organizando uma celebração e procura uma solução bonita, prática e confiável para as lembrancinhas.
- **Estado de entrada:** sem cadastro, chega à loja por uma pesquisa no Google por “lembrancinha de papelaria”, produto ou tema.
- **Caminho:**
  1. Pesquisa pelo tema desejado e encontra opções relevantes, organizadas sem anúncios ou excesso de informações.
  2. Escolhe um produto e entende preço, quantidade, prazo e processo de personalização antes de adicioná-lo ao Carrinho.
  3. Conclui a compra como visitante e, após a confirmação do pagamento, recebe acesso protegido ao Briefing.
  4. Preenche o Briefing em até três dias úteis; após o Briefing completo, recebe a primeira Arte em até 24 horas.
  5. Analisa a Prévia e pode solicitar até três Rodadas de Alteração gratuitas antes de registrar a Aprovação Final.
  6. Após a Aprovação Final, acompanha os sete dias corridos de Produção e recebe uma fotografia privada da encomenda concluída antes da postagem.
  7. Acompanha o Envio e recebe a encomenda em casa.
- **Clímax:** Mariana aprova a Arte e vê, na Área da Cliente, que a Produção começou com prazo atualizado.
- **Resolução:** recebe uma encomenda correspondente à Arte aprovada, com histórico e rastreamento preservados.
- **Exceção:** se Mariana atrasar o Briefing, a Produção não começa. O pedido permanece em “Aguardando briefing”, o prazo fica pausado e ela recebe lembretes para concluir a próxima ação.

### UJ-2 — Beatriz encontra e compra um convite digital personalizado

- **Persona e contexto:** Beatriz está organizando um evento e quer um convite digital adequado ao tema, sem precisar começar a compra por uma conversa no WhatsApp.
- **Estado de entrada:** sem cadastro, chega à loja após pesquisar por convites no Google.
- **Caminho:**
  1. Pesquisa por convite e visualiza os Tipos de Convite disponíveis, como tradicional, animado e interativo, com uma explicação visual de como cada um funciona e de seus diferenciais.
  2. Escolhe o Tipo de Convite pela experiência e pelos recursos desejados; os exemplares publicados servem como demonstração, mas não limitam os temas que podem ser encomendados.
  3. Preenche um Pré-formulário simples com o tema que deseja, mesmo que ele não esteja publicado no site, além de cores, data, horário, endereço e preferência de Miniatura.
  4. Informa se já possui a Miniatura ou se deseja contratar sua criação pela JS Designs por €5; o preço total fica visível antes da compra.
  5. Conclui o pagamento e acompanha a criação pela Área da Cliente.
  6. Para um Convite Padrão, recebe a primeira versão em até 24 horas após a confirmação do pagamento e o fornecimento dos dados necessários. Para um Convite Complexo, conclui um Briefing protegido e recebe a primeira versão em até 48 horas após o Briefing completo.
  7. Analisa a Prévia e pode solicitar até três Rodadas de Alteração gratuitas.
  8. Após a Aprovação Final, escolhe receber o convite final por WhatsApp ou e-mail, mantendo-o também associado ao Pedido.
- **Clímax:** Beatriz aprova o convite e recebe o arquivo final no canal escolhido.
- **Resolução:** fica com um convite pronto para compartilhar e com acesso ao histórico da criação.
- **Exceção:** se o tema ainda não tiver exemplar publicado, a compra continua normalmente porque o tema é definido no Pré-formulário. Somente recursos ou formatos fora dos Tipos de Convite oferecidos seguem para avaliação humana como Projeto Exclusivo.

### UJ-3 — Camila compra e recebe imediatamente um arquivo digital pronto

- **Persona e contexto:** Camila produz peças de papelaria e quer adquirir um arquivo editável para utilizar no Silhouette Studio.
- **Estado de entrada:** sem cadastro, chega à página de um Produto Digital Pronto por busca externa ou pela Loja.
- **Caminho:**
  1. Confirma antes da compra que o produto é um arquivo editável compatível com o Silhouette Studio, sem personalização ou aprovação de Arte.
  2. Consulta claramente o conteúdo entregue, a compatibilidade, as condições de uso e o caráter digital do produto.
  3. Conclui a compra como visitante.
  4. Após a confirmação do pagamento, o sistema envia o arquivo automaticamente e sem espera para o e-mail informado.
- **Clímax:** Camila recebe o e-mail e consegue abrir o arquivo no Silhouette Studio.
- **Resolução:** utiliza o arquivo adquirido sem depender de atendimento ou de uma etapa manual da JS Designs.
- **Exceção:** se o e-mail não chegar, a cliente gera outro link temporário pela Área da Cliente e mantém acesso ao arquivo adquirido.

### UJ-4 — Sharom administra um pedido personalizado sem planilhas paralelas

- **Persona e contexto:** Sharom precisa conduzir encomendas personalizadas com clareza de prioridade, prazo e dependências, sem reconstruir o histórico em conversas ou planilhas externas.
- **Estado de entrada:** autenticada com acesso administrativo, abre a fila de pedidos e vê para cada item o Estado do Pedido, o responsável, o prazo restante e a Próxima Ação.
- **Caminho:**
  1. Após o pagamento, confirma que o Briefing foi liberado e que a cliente recebeu as instruções.
  2. Acompanha separadamente pedidos em “Aguardando briefing”; o sistema pausa os prazos dependentes da cliente e envia lembretes.
  3. Quando o Briefing fica completo, o pedido entra em “Arte em criação” com prazo compatível com o tipo de produto.
  4. Envia uma Prévia numerada e acompanha a Aprovação ou uma solicitação de alteração, com histórico e contador das três Rodadas de Alteração gratuitas.
  5. Após a Aprovação Final, utiliza a versão bloqueada como referência oficial. Para produtos físicos, inicia os sete dias corridos e consulta uma Ficha de Produção com itens, quantidades, materiais, acabamento, endereço e prazo.
  6. Ao concluir a Produção, executa a Verificação de Qualidade, registra uma fotografia privada e prepara o Envio.
  7. Adiciona a transportadora e o rastreamento; a cliente recebe a notificação e mantém acesso à fotografia, Fatura e histórico.
  8. Para convites digitais, após a Aprovação Final, entrega o arquivo pelo canal escolhido sem passar pelas etapas de Produção física e Envio.
- **Clímax:** Sharom conclui o pedido usando uma única fonte de verdade que conecta pagamento, Briefing, Arte aprovada, Produção ou entrega digital e Fatura.
- **Resolução:** o pedido termina com histórico auditável, cliente informada e nenhuma etapa mantida apenas em planilha ou conversa externa.
- **Exceção:** quarta alteração, urgência, pagamento pendente ou erro de produção entram em uma fila de exceções e exigem decisão explícita, sem modificar silenciosamente o fluxo normal.

### UJ-5 — Mariana obtém ajuda automática e continua com atendimento humano quando necessário

- **Persona e contexto:** durante a escolha ou o acompanhamento de um pedido, Mariana tem uma dúvida e quer resolvê-la sem abandonar o site.
- **Estado de entrada:** está navegando na loja ou autenticada na Área da Cliente e abre o botão de Suporte Online.
- **Caminho:**
  1. Informa sua dúvida no Chat.
  2. O Atendimento Automático responde somente quando encontra uma resposta previamente cadastrada e aplicável.
  3. Se a resposta resolver a dúvida, Mariana continua a jornada de compra ou acompanhamento.
  4. Se não houver resposta confiável ou Mariana indicar que o problema não foi resolvido, o Chat oferece atendimento humano.
  5. Mariana escolhe continuar pelo WhatsApp ou permanecer no Chat; neste caso, a conversa é encaminhada para Sharom responder pelo próprio painel.
- **Clímax:** a conversa chega ao atendimento humano com o histórico e o contexto necessários, sem obrigar a cliente a repetir tudo.
- **Resolução:** Mariana recebe a resposta no canal escolhido e consegue retomar sua jornada.
- **Exceção:** fora do horário humano, o sistema informa que a mensagem foi recebida e que será respondida na próxima janela, respeitando o prazo máximo de um dia útil.

## 4. Glossário

- **Aprovação Final** — confirmação explícita da cliente de que uma versão da Arte pode ser usada para Produção ou entrega.
- **Área da Cliente** — ambiente protegido criado ou associado à cliente após a compra, no qual ela acessa pedidos, Briefings, Prévias, arquivos, faturas e próximas ações.
- **Arte** — composição visual criada ou adaptada pela JS Designs para um item personalizado.
- **Atendimento Automático** — respostas do Chat baseadas exclusivamente em conteúdo previamente cadastrado.
- **Briefing** — formulário protegido pós-pagamento, único por Pedido/evento, que reúne dados compartilhados e campos específicos necessários para criar as Artes dos itens personalizados.
- **Carrinho** — etapa na qual a cliente reúne itens e configura quantidades, personalização, Miniatura e complementos.
- **Chat** — canal de suporte dentro do site, capaz de atender automaticamente e transferir a conversa para Sharom.
- **Checkout** — página única de identificação, entrega, faturamento e pagamento.
- **Convite Complexo** — convite digital que exige Briefing completo e tem prazo de criação de até 48 horas.
- **Convite Padrão** — convite digital produzido com os dados essenciais e prazo de criação de até 24 horas.
- **Estado do Pedido** — situação operacional atual de um Pedido.
- **Família de Convite** — agrupamento comercial de convites: Essenciais, Interativos, Infinito ou Cinemágicos; não determina sozinha o prazo.
- **Ficha de Produção** — registro operacional consolidado da versão aprovada, itens, quantidades, materiais, acabamentos, endereço, prazo e verificação.
- **Kit Composto** — produto físico com quantidade total e preço próprios, cuja composição é distribuída pela cliente entre modelos permitidos, como cones, caixas e caixas milk.
- **Miniatura** — imagem fornecida pela cliente ou criada pela JS Designs para uso em um produto compatível.
- **Pedido** — registro comercial que conecta cliente, itens, pagamento, Briefing, Arte, Produção ou entrega digital, Fatura e histórico.
- **Pré-formulário** — coleta breve anterior ao pagamento que antecipa dados e permite calcular o valor de um convite.
- **Prévia** — versão numerada da Arte apresentada para análise.
- **Produção** — confecção de um produto físico após a Aprovação Final.
- **Produto Digital Pronto** — arquivo editável compatível com o Silhouette Studio, sem personalização e com entrega automática.
- **Projeto Exclusivo** — criação com formato, recurso ou escopo fora das opções comerciais configuradas; em convites, a ausência de um exemplar do tema no catálogo não torna o pedido exclusivo.
- **Próxima Ação** — ação necessária para que um Pedido avance, atribuída à cliente ou à JS Designs.
- **Rodada de Alteração** — ciclo no qual a cliente consolida solicitações sobre uma Prévia e recebe uma nova versão.
- **Suporte Online** — conjunto formado pelo Atendimento Automático, Chat humano e acesso ao WhatsApp.
- **Tipo de Convite** — classificação comercial de um convite conforme formato e complexidade.
- **Verificação de Qualidade** — conferência da encomenda física antes da embalagem, registrada com fotografia privada.

## 5. Princípios de produto, experiência e conteúdo

### 5.1 Princípios

1. **Clareza antes da compra:** preço, prazo, quantidade, material, personalização, entrega e condições aparecem no momento em que influenciam a decisão.
2. **Informação progressiva:** cartões são enxutos; detalhes ficam na página do produto; escolhas no Carrinho; dados sensíveis no Briefing protegido.
3. **Autonomia com apoio humano:** o fluxo normal não depende do WhatsApp, mas o atendimento humano permanece acessível.
4. **Uma próxima ação por vez:** cliente e operação sempre sabem quem deve agir e o impacto no prazo.
5. **Arte aprovada é a referência:** Produção e entrega usam somente a versão que recebeu Aprovação Final.
6. **Prova real de acabamento:** fotografia, detalhe e Verificação de Qualidade sustentam confiança sem promessas absolutas.
7. **Sem anúncios e sem padrões enganosos:** a loja não introduz publicidade de terceiros, caixas pré-selecionadas ou urgência artificial.

### 5.2 Arquitetura de informação do lançamento

- Início;
- Busca;
- Loja;
- Topos de bolo;
- Lembrancinhas;
- Kits;
- Convites;
- Produtos digitais prontos;
- página de produto;
- Carrinho;
- Checkout;
- confirmação;
- Área da Cliente;
- Projeto Exclusivo;
- Suporte Online;
- páginas institucionais, políticas e informações legais;
- Administração.

### 5.3 Direção estética e tom

A experiência será minimalista, sofisticada, acolhedora e atemporal, usando espaço em branco, fotografia real e hierarquia editorial clara. A referência Gio orienta a lógica comercial — faixa de anúncio, busca visível, categorias, hero, benefícios e mais vendidos — sem copiar identidade, paleta, textos ou composição. A direção própria prioriza branco, bege claro, dourado champanhe, taupe e preto suave.

O texto será direto, humano e confiável. Não usará “Pagamentos 100% seguros”. A mensagem aprovada é: **“Pagamentos protegidos e processados por parceiros certificados.”**

O hero deve usar fotografia editorial real de produtos ou celebração, com recorte responsivo e contraste que preserve leitura e controles em telas pequenas. As categorias continuarão acessíveis na Início, mas o cabeçalho manterá o agrupamento simplificado em “Loja” em vez de copiar a barra extensa da referência.

## 6. Funcionalidades e requisitos funcionais

### 6.1 Descoberta, início e navegação

**Descrição:** a cliente deve chegar por Google, Instagram, link direto ou navegação interna e encontrar rapidamente produtos e temas sem ruído. Realiza UJ-1, UJ-2 e UJ-3.

#### FR-1 — Início orientado à descoberta

A página inicial deve apresentar proposta de valor, busca destacada, categorias essenciais, produtos mais pedidos, prova de acabamento, funcionamento da personalização e acesso discreto a Projeto Exclusivo.

**Condições verificáveis:**

- Não usa anúncios de terceiros nem personagens anunciados como produtos, temas ou caminho de descoberta.
- Os links de produto vindos de busca externa ou rede social abrem diretamente o contexto relevante.

#### FR-2 — Navegação simples

A cliente deve acessar Loja, categorias, busca, conta e Carrinho por uma navegação consistente em dispositivos móveis e desktop.

**Condições verificáveis:**

- O cabeçalho preserva busca, conta e Carrinho sem sobrecarregar a tela.
- Menus, diálogos e controles funcionam por teclado e tecnologia assistiva.

#### FR-3 — SEO essencial de lançamento

O sistema deve permitir que páginas públicas relevantes sejam descobertas e compreendidas por mecanismos de busca.

**Condições verificáveis:**

- Produtos, categorias, temas e páginas institucionais têm título, descrição, URL canônica e dados estruturados adequados.
- Conteúdo equivalente em cada idioma indica corretamente idioma e variante.
- Carrinho, Checkout, Área da Cliente e Administração não são indexados.

#### FR-4 — Idioma e preferência regional

A cliente deve alternar entre português do Brasil, inglês e espanhol e escolher entre as moedas suportadas sem perder a página, o Carrinho ou o contexto do Pedido.

**Condições verificáveis:**

- As jornadas essenciais estão completas nos três idiomas.
- Preço, prazo, mensagens transacionais, formulários e políticas não exibem mistura involuntária de idiomas.
- O site pode sugerir idioma e moeda pela localização aproximada, mas não exige localização precisa nem impede alteração manual.
- A sugestão não substitui uma escolha já feita pela cliente.
- A moeda selecionada permanece visível e estável no Carrinho e no Checkout.
- O catálogo usa EUR como moeda-base e o lançamento pretende cobrar em EUR, GBP, USD, CHF e BRL, sujeito à validação técnica e contratual do provedor de pagamentos.
- Métodos limitados a uma moeda, como MB WAY em EUR, só aparecem quando elegíveis; qualquer mudança de moeda exigida é informada antes da confirmação.
- Conversão, arredondamento, validade do preço e reembolso na moeda original devem ser auditáveis e não podem mudar silenciosamente depois da obrigação de pagar.
- Os preços convertidos do catálogo usam uma fonte aprovada e são atualizados diariamente.
- Ao iniciar o Checkout, a taxa definitiva do provedor e o preço convertido ficam bloqueados por 30 minutos, sem representar cobrança antecipada.
- Se o bloqueio expirar, o sistema recalcula e exige que a cliente veja e confirme o novo total antes do pagamento.
- Valores seguem as casas decimais aplicáveis à moeda; reembolsos retornam o mesmo valor nominal na moeda originalmente paga, sujeito apenas às regras legais e do método, com taxas e diferenças conciliadas internamente.

### 6.2 Catálogo, busca e páginas de produto

**Descrição:** o catálogo separa claramente produto físico personalizado, convite personalizado e Produto Digital Pronto, preservando suas regras próprias.

#### FR-5 — Catálogo estruturado

A operação deve cadastrar produtos com natureza, categoria, fotos, preço, quantidade ou variante, tema demonstrado, ocasião, personalização, prazo, materiais, composição, disponibilidade e tipo de entrega. Personagens ou outros ativos protegidos somente podem ser cadastrados após a verificação de direitos de uso comercial.

**Condições verificáveis:**

- Um produto não pode ser publicado sem os dados obrigatórios para sua modalidade.
- A cliente identifica antes da compra se receberá item físico, convite personalizado ou arquivo pronto.
- Em convites, o tema associado a um exemplar é metadado de inspiração e descoberta, não uma lista fechada de temas aceitos.

#### FR-6 — Busca por intenção

A cliente deve pesquisar por título, produto, tema, personagem, ocasião e grafias alternativas.

**Condições verificáveis:**

- Resultados exatos aparecem primeiro, agrupados por categoria.
- Resultados semelhantes aparecem em seção separada e identificada.
- Uma busca com intenção de convite apresenta primeiro os Tipos de Convite e permite comparar funcionamento, recursos, diferenciais, preço e prazo.
- Se não existir exemplar para o tema pesquisado, a resposta informa que a cliente pode solicitar qualquer tema e a encaminha à escolha do Tipo de Convite, preservando o tema no Pré-formulário. A ausência no catálogo nunca bloqueia a solicitação.
- O estado sem resultado para produtos ou escopos não cobertos oferece Projeto Exclusivo e preserva o termo pesquisado.

#### FR-7 — Página de produto físico

A página de produto físico deve informar fotos reais, preço, quantidades, material, acabamento, conteúdo, personalização incluída ou opcional, prazo e entrega.

**Condições verificáveis:**

- Lembrancinhas e kits exibem preço para todas as quantidades comercializadas, incluindo quantidades superiores a 50, sem encaminhar a cliente para orçamento manual por volume.
- A alteração da quantidade atualiza imediatamente preço unitário, desconto progressivo e total.
- A tabela comercial inicial da papelaria personalizada é:

| Quantidade | Clássica | Preço unitário efetivo | Luxo | Preço unitário efetivo |
|---:|---:|---:|---:|---:|
| 1 | €2,00 | €2,00 | €3,00 | €3,00 |
| 20 | €38,00 | €1,90 | €58,00 | €2,90 |
| 24 | €45,00 | €1,88 | €67,00 | €2,79 |
| 32 | €60,00 | €1,88 | €89,00 | €2,78 |
| 40 | €72,00 | €1,80 | €110,00 | €2,75 |
| 50 | €87,50 | €1,75 | €135,00 | €2,70 |
| 60 | €102,00 | €1,70 | €159,00 | €2,65 |
| 70 | €115,50 | €1,65 | €182,00 | €2,60 |

- A tabela acima é administrável; novas faixas superiores a 70 unidades devem manter preço unitário não crescente.
- Nome e idade incluídos são distinguidos de personalizações pagas.
- Topos de bolo distinguem claramente modelo pronto e personalização contratada separadamente.
- Produtos físicos publicam identificação do produto e da responsável, materiais, uso pretendido, advertências aplicáveis e informações de rastreabilidade.
- O CTA de lembrancinhas é “Comprar já”.

#### FR-8 — Página de convite

A descoberta e a página de convite devem explicar cada Tipo de Convite por meio de demonstração, funcionamento, recursos, diferenciais, preço, prazo, dados necessários, Miniatura e processo de alterações.

**Condições verificáveis:**

- Convite Padrão comunica prazo de até 24 horas.
- Convite Complexo comunica exigência de Briefing e prazo de até 48 horas.
- O catálogo preserva as famílias Essenciais, Interativos, Infinito e Cinemágicos e os oito formatos atuais — tradicional, dois tipos de animado, interativo, interativo plus, infinito, cinemágico e cinemágico interativo.
- O mapeamento final entre Família de Convite, formato, recursos e classificação Padrão/Complexo deve ser concluído no Gate F0.
- A cliente escolhe o Tipo de Convite antes do tema; uma comparação simples evidencia o que muda entre os tipos.
- Exemplares publicados demonstram possibilidades de resultado e podem ser adicionados gradualmente pela operação, sem formar uma lista fechada de temas disponíveis.
- A interface afirma claramente que a cliente pode solicitar qualquer tema e que o convite será personalizado ao seu gosto dentro do Tipo escolhido.
- Personagens não são anunciados como produtos, temas ou exemplos públicos do catálogo.
- O CTA principal é “Comprar agora”.

#### FR-9 — Página de Produto Digital Pronto

A página deve informar que o produto não inclui personalização, quais arquivos serão entregues, compatibilidade com o Silhouette Studio, condições de uso e entrega imediata após pagamento.

**Condições verificáveis:**

- A cliente não confunde o produto com convite personalizado.
- A licença permite uso pessoal e uso comercial vitalício para produzir e vender peças físicas feitas com o arquivo.
- Não existe limite quantitativo de peças físicas produzidas sob a licença.
- É proibido revender, compartilhar, oferecer, publicar ou redistribuir o arquivo digital, original ou modificado.
- A cliente pode editar o arquivo para os usos permitidos, sem adquirir direito de redistribuição digital.
- A compra não transfere direitos autorais, marca ou propriedade intelectual do conteúdo.
- A entrega contém um pacote `.zip` com arquivo editável `.studio3`, imagem de prévia e PDF de instruções e licença.
- Antes do pagamento, a cliente reconhece as condições aplicáveis ao início imediato do acesso digital.

#### FR-10 — Projeto Exclusivo

A cliente deve iniciar uma solicitação de Projeto Exclusivo quando desejar formato, recurso ou escopo fora das opções configuradas. A ausência de um tema de convite no catálogo não exige avaliação exclusiva.

**Condições verificáveis:**

- O sistema encaminha o contexto da busca e os dados já informados.
- Prazo e preço não são prometidos antes de avaliação humana.
- Uma cliente de um país europeu fora da União Europeia pode solicitar pelo Suporte Online uma avaliação manual para comprar produto físico.

### 6.3 Configuração, Carrinho e benefícios

#### FR-11 — Configuração de produto físico

A cliente deve escolher quantidade, ativar ou dispensar personalização básica e selecionar a opção de Miniatura compatível.

**Condições verificáveis:**

- A quantidade pode ser alterada na configuração e no Carrinho; cada mudança recalcula o preço sem recarregar ou perder a personalização.
- Quantidades elegíveis recebem automaticamente o desconto progressivo configurado, inclusive acima de 50 unidades.
- Produtos físicos diferentes calculam suas faixas separadamente; suas quantidades não são somadas para criar uma faixa maior.
- Um Kit Composto é a exceção explícita: a cliente escolhe uma quantidade total configurada, como 20, 30 ou 70 peças, e distribui esse total entre os modelos permitidos no kit.
- A soma das quantidades dos componentes deve ser exatamente igual ao total do Kit Composto antes de adicioná-lo ao Carrinho.
- Cada modelo escolhido no Kit Composto exige no mínimo 5 unidades e só pode ser incrementado em múltiplos de 5.
- O preço e o desconto do Kit Composto usam sua quantidade total; a Administração define modelos compatíveis e quantidades totais disponíveis.
- As opções são “não quero Miniatura”, “já tenho o arquivo” e “quero que a JS Designs crie”.
- Arquivo de Miniatura fornecido pela cliente não gera cobrança; a criação pela JS Designs custa €5 uma única vez por Pedido/evento, e a mesma Miniatura pode ser reutilizada em todos os convites e produtos físicos compatíveis do Pedido.
- Uploads sensíveis são solicitados somente no Briefing protegido após pagamento.

#### FR-12 — Pré-formulário de convite

A cliente deve antecipar Tipo de Convite, tema em texto livre, cores, data, horário, endereço e opção de Miniatura antes de pagar.

**Condições verificáveis:**

- Os dados alteram o preço quando aplicável.
- O tema pode ser informado mesmo sem exemplar correspondente no catálogo e permanece sujeito às regras de propriedade intelectual.
- A criação de Miniatura para convite acrescenta €5 e exige consentimento explícito, nunca pré-selecionado.
- Dados do Pré-formulário são reutilizados no Briefing sem exigir redigitação.

#### FR-13 — Complementos relevantes

O Carrinho pode oferecer dois ou três produtos complementares do mesmo tema ou ocasião.

**Condições verificáveis:**

- A inclusão é opcional e não impede a compra original.
- Preço e benefício do conjunto ficam explícitos.

#### FR-14 — Cálculo do melhor benefício

Quando desconto progressivo por quantidade, desconto de conjunto ou cupom forem aplicáveis, o sistema deve recalcular e aplicar automaticamente o benefício válido mais vantajoso, sem acumulação indevida.

**Condições verificáveis:**

- O Carrinho mostra quantidade elegível, faixa alcançada, percentual ou valor descontado, preço unitário resultante e total.
- Aumentar ou reduzir quantidades aplica ou remove a faixa correspondente imediatamente.
- A tabela comercial pode configurar faixas, escopo dos itens elegíveis, percentuais e limites sem alteração de código.
- A quantidade de linhas independentes nunca é agregada; apenas os componentes pertencentes ao mesmo Kit Composto compartilham a faixa do kit.
- O cupom individual de primeira compra concede 10%, sem valor mínimo, mediante e-mail válido e apenas na primeira compra.
- O cupom não é cumulativo com desconto progressivo nem desconto de conjunto.
- O resumo explica qual benefício foi aplicado.
- Reprocessamentos não duplicam desconto.

#### FR-15 — Persistência do Carrinho

O Carrinho deve aceitar vários produtos no mesmo Pedido e preservar cada item, quantidade e configuração durante a sessão e nas transições de idioma.

**Condições verificáveis:**

- A cliente pode adicionar, editar e remover itens independentemente.
- Produtos físicos, convites e Produtos Digitais Prontos podem coexistir; o resumo separa preço, personalização, prazo, entrega digital e frete de cada modalidade.
- Todos os produtos físicos do Pedido são consolidados em uma única remessa e uma única cobrança de frete.
- Todos os produtos digitais do Pedido são consolidados em uma única entrega digital, sem mensagens ou pacotes separados por item.
- Falha de rede ou retorno à loja não apaga silenciosamente escolhas confirmadas.
- Mudança de disponibilidade ou preço exige nova confirmação antes do pagamento.

### 6.4 Checkout, pagamento, faturamento e frete

#### FR-16 — Checkout em uma página

A cliente deve concluir identificação, entrega quando aplicável, NIF opcional, pagamento e revisão do Pedido em uma única página, sem cadastro prévio obrigatório.

**Condições verificáveis:**

- O CTA final é “Finalizar e comprar”.
- Imediatamente antes do CTA aparecem itens, características principais, preço total, encargos, entrega e condições relevantes.
- O Checkout informa inequivocamente a moeda em que a cliente será cobrada e não a altera após a confirmação sem novo consentimento.
- Se uma moeda escolhida não puder ser processada, a alternativa e qualquer conversão são apresentadas antes da obrigação de pagar.
- Chat e WhatsApp flutuantes ficam ocultos no Checkout; a página mantém apenas ajuda não intrusiva pelas informações e políticas necessárias.

#### FR-17 — Conta após a compra

Após pagamento confirmado, o sistema deve criar ou associar acesso seguro à Área da Cliente sem transformar cadastro prévio em barreira.

**Condições verificáveis:**

- A cliente recebe instrução segura para acessar o Pedido.
- Recuperação de acesso não depende de informar dados sensíveis pelo Chat.
- O acesso verifica a posse do e-mail ou telefone, usa sessão revogável e exige nova verificação para ações sensíveis.
- Links de ativação, recuperação e arquivo privado são temporários, de uso limitado e não concedem acesso a outro Pedido.

#### FR-18 — Pagamento por parceiro certificado

A cliente deve pagar por métodos habilitados pela JS Designs por meio de parceiro certificado.

**Condições verificáveis:**

- O MVP oferece cartão, Apple Pay, Google Pay, MB WAY e transferência bancária quando forem compatíveis com o país, o dispositivo e a moeda.
- Um provedor central de pagamentos deve orquestrar Checkout, confirmação, reembolso, disputa, conciliação e webhooks; a Wise não será usada como única infraestrutura de pagamento.
- PayPal integra o lançamento somente se puder ser centralizado pelo provedor escolhido sem exigir conciliação operacional separada; caso contrário, fica para a fase imediatamente posterior.
- A JS Designs não armazena número completo de cartão, CVV ou PIN.
- Confirmações repetidas não duplicam cobrança, Pedido ou Fatura.
- A interface exibe “Pagamentos protegidos e processados por parceiros certificados.”

#### FR-19 — Transferência bancária

Um Pedido por transferência deve permanecer reservado em “Aguardando pagamento” por 48 horas.

**Condições verificáveis:**

- A confirmação libera a jornada correta uma única vez.
- Sem confirmação em 48 horas, o Pedido é cancelado e a cliente é notificada.

#### FR-20 — Frete europeu

No lançamento, o sistema deve calcular disponibilidade, custo e previsão de envio de produtos físicos para endereços nos 27 países da União Europeia.

**Condições verificáveis:**

- O Checkout não cobra frete de produtos exclusivamente digitais.
- Havendo vários produtos físicos no Pedido, o Checkout calcula um único frete para a remessa consolidada.
- Em Portugal continental, um subtotal de pelo menos €70 em produtos físicos, calculado após descontos e antes do frete, recebe frete grátis.
- Produtos digitais não contam para alcançar o limite de frete grátis.
- Para ilhas portuguesas e demais países da União Europeia, a Administração configura limites próprios por destino conforme o custo logístico.
- Produto, Carrinho e Checkout mostram o progresso até o frete grátis e atualizam o benefício quando quantidade, destino, desconto ou itens mudarem.
- A remessa é liberada somente quando todos os itens físicos estiverem aprovados, produzidos e concluírem a Verificação de Qualidade.
- Para endereço europeu fora da União Europeia, o Checkout não promete nem conclui automaticamente a entrega física; oferece contato com o Suporte Online para avaliação manual de impostos, alfândega, transportadora, prazo e preço.
- Restrições de destino e custos aparecem antes da obrigação de pagar.

#### FR-21 — NIF e Fatura

Toda venda deve gerar Fatura; a cliente pode selecionar “Desejo incluir NIF na fatura” e informar o dado quando aplicável.

**Condições verificáveis:**

- O emitente é a pessoa singular titular da atividade, com nome legal, domicílio fiscal e NIF; “JS Designs” aparece adicionalmente como marca comercial.
- A Fatura é enviada por e-mail e permanece disponível na Área da Cliente.
- Reprocessamento não gera Fatura duplicada.
- O tipo documental aplicável — Fatura, Fatura-recibo, Fatura de adiantamento e/ou Recibo — respeita a modalidade e o momento fiscal validados pelo contabilista.
- Um Pedido misto gera uma única Fatura por padrão, com modalidades e tratamentos fiscais identificados em linhas separadas, quando a solução fiscal e a legislação permitirem.
- Se a regra fiscal exigir documentos distintos, o sistema os gera automaticamente, mantém a conciliação por modalidade e apresenta todos agrupados no mesmo Pedido para a cliente.

#### FR-22 — Confirmação de compra por modalidade

Após o pagamento, a confirmação deve explicar claramente a próxima ação conforme os itens do Pedido.

**Condições verificáveis:**

- Produto Digital Pronto dispara entrega automática.
- Um Pedido com um ou mais produtos personalizados libera um único Briefing e informa os prazos aplicáveis.
- Produto físico apresenta separadamente criação, Produção e transporte.
- Em Pedido misto, cada item segue seu próprio fluxo e prazo sob o mesmo pagamento e histórico, mas o cumprimento é consolidado em no máximo uma entrega digital e uma remessa física.
- A confirmação informa quando a entrega digital e a remessa física consolidadas estarão elegíveis para envio.

### 6.5 Briefing, Arte e Aprovação

#### FR-23 — Briefing adaptado

Após pagamento confirmado, a cliente deve preencher um único Briefing por Pedido/evento, mesmo quando comprar convites e lembrancinhas juntos.

**Condições verificáveis:**

- O Briefing solicita uma única vez tema, cores, nome, idade, data, horário, endereço e demais dados comuns do evento.
- Seções condicionais coletam apenas informações específicas necessárias a cada tipo de produto.
- Os mesmos dados são reutilizados em todos os itens compatíveis do Pedido e nas informações válidas do Pré-formulário.
- A cliente revisa quais itens usarão cada dado antes de concluir.
- O sistema salva automaticamente o progresso e permite retomada.
- O prazo de três dias úteis para preenchimento fica visível.

#### FR-24 — Lembrete e pausa por Briefing

Se o Briefing não for concluído, o Pedido deve permanecer em “Aguardando briefing”, sem iniciar a criação ou a Produção.

**Condições verificáveis:**

- A cliente recebe lembretes configuráveis.
- O sistema mostra que o cronograma será recalculado após o Briefing completo.
- O atraso não cancela automaticamente um Pedido já pago.

#### FR-25 — Prazo da primeira Arte

O sistema deve calcular e comunicar o prazo de criação aplicável após todas as informações obrigatórias estarem completas.

**Condições verificáveis:**

- Primeira Arte de produto físico: até 24 horas após o Briefing completo.
- Convite Padrão: até 24 horas após pagamento e dados completos.
- Convite Complexo: até 48 horas após o Briefing completo.
- Um prazo pausado informa motivo e dependência.

#### FR-26 — Prévias numeradas

Sharom deve enviar Prévias numeradas e a cliente deve consultar o histórico na Área da Cliente.

**Condições verificáveis:**

- Uma nova versão não substitui silenciosamente a anterior.
- Notificação e registro referenciam a mesma versão.

#### FR-27 — Rodadas de Alteração

A cliente deve poder enviar até três Rodadas de Alteração gratuitas por Arte, com contador visível.

**Condições verificáveis:**

- Uma rodada é o conjunto consolidado de mudanças enviado sobre a Prévia atual.
- Mensagens adicionais recebidas antes de Sharom iniciar a nova versão pertencem à mesma rodada; após a entrega de uma nova Prévia, outra solicitação inicia nova rodada.
- Cada rodada preserva solicitação, data, autora e versão resultante.
- A partir da quarta rodada, uma alteração simples custa €5 e exige pagamento antes da execução.
- Mudança estrutural ou complexa recebe avaliação e orçamento manual antes da execução.

#### FR-28 — Aprovação Final

A cliente deve registrar Aprovação Final explícita após revisar os dados críticos.

**Condições verificáveis:**

- Antes de aprovar, o sistema destaca nome, idade, data, horário, endereço e textos aplicáveis.
- A versão aprovada é bloqueada e identificada como referência oficial.
- Para convite digital, alteração após Aprovação Final torna-se nova solicitação paga, com preço e prazo avaliados antes da execução.
- Para produto físico ainda não iniciado, Sharom pode aceitar excepcionalmente alteração paga e recalcular o prazo.
- Alteração posterior à Aprovação nunca ocorre silenciosamente.

#### FR-29 — Início da Produção física

Produtos físicos devem entrar em Produção somente após a Aprovação Final.

**Condições verificáveis:**

- O prazo normal de Produção é de sete dias corridos a partir da Aprovação Final.
- O prazo pausa quando a continuidade depende de ação da cliente e registra o motivo.
- Se a cliente pedir mudança após o início da Produção, Sharom pausa quando possível e registra unidades concluídas, materiais não reaproveitáveis e trabalho de refação.
- A cliente recebe orçamento proporcional dos custos já incorridos e do retrabalho; o novo prazo começa após aceite e pagamento.
- Se a encomenda já estiver totalmente concluída, a mudança é tratada como nova Produção.

### 6.6 Área da Cliente, entrega e acompanhamento

#### FR-30 — Linha do tempo do Pedido

A Área da Cliente deve mostrar Estado do Pedido, motivo, data prevista e Próxima Ação.

**Condições verificáveis:**

- Estados normais incluem: Pedido confirmado, Aguardando briefing, Arte em criação, Aguardando aprovação, Em produção, Verificação de qualidade, Preparando para envio, Enviado e Concluído.
- Estados complementares incluem Aguardando pagamento, Pausado, Cancelado e Exceção.

#### FR-31 — Central de ações da cliente

A cliente deve preencher Briefing, comentar Prévia, solicitar alteração, aprovar Arte e consultar arquivos dentro do Pedido.

**Condições verificáveis:**

- Cada ação gera confirmação e histórico.
- A cliente não precisa recorrer ao WhatsApp para completar o fluxo normal.

#### FR-32 — Fotografia final privada

Antes da postagem de um produto físico, Sharom deve anexar uma fotografia privada da encomenda concluída.

**Condições verificáveis:**

- A fotografia fica vinculada ao Pedido e visível à cliente.
- Não é publicada nem reutilizada em marketing sem consentimento separado.

#### FR-33 — Rastreamento físico

Sharom deve registrar transportadora e código ou link de rastreamento; a cliente deve recebê-los e consultá-los na Área da Cliente.

**Condições verificáveis:**

- Todos os itens físicos do Pedido ficam associados à mesma remessa consolidada, frete e rastreamento.
- Atualizações não apagam o histórico de envio.
- Falha de notificação não remove o acesso pela Área da Cliente.

#### FR-34 — Entrega de convite

Após Aprovação Final, a cliente deve escolher receber o convite por e-mail ou WhatsApp.

**Condições verificáveis:**

- Vários convites personalizados do mesmo Pedido são entregues juntos quando todos tiverem Aprovação Final.
- O canal e o momento da entrega ficam registrados no Pedido.
- Falha no canal escolhido permite nova tentativa sem gerar uma nova compra.

#### FR-35 — Entrega automática de Produto Digital Pronto

Após pagamento confirmado, o sistema deve enviar imediatamente por e-mail o arquivo adquirido.

**Condições verificáveis:**

- Em Pedido composto apenas por Produtos Digitais Prontos, todos os arquivos são reunidos em uma única entrega automática e imediata, sem depender de ação administrativa.
- Quando o Pedido também contém convite personalizado, a entrega digital aguarda todos os itens digitais ficarem prontos para enviá-los juntos uma única vez.
- Clientes de países europeus fora da União Europeia podem comprar e receber produtos digitais sem restrição de frete, sujeito ao cálculo fiscal e às condições digitais aplicáveis ao destino.
- Falhas são detectadas e permitem reenvio sem cobrança duplicada.
- O link enviado por e-mail expira em sete dias.
- A Área da Cliente permite downloads ilimitados e gera links temporários protegidos.
- A licença permanece vitalícia; se a JS Designs descontinuar a hospedagem, deve avisar previamente e oferecer uma janela razoável para baixar os arquivos.

#### FR-36 — Notificações acionáveis

Notificações devem informar Estado do Pedido, Próxima Ação e impacto no prazo, usando os canais consentidos.

**Condições verificáveis:**

- A Área da Cliente é o registro principal.
- A cliente pode definir e alterar sua preferência pós-compra entre e-mail e WhatsApp quando ambos forem suportados para o evento.
- Mensagens não incluem arquivos privados ou dados sensíveis em links públicos permanentes.

### 6.7 Suporte Online

#### FR-37 — Botão de suporte contextual

O site deve oferecer acesso compacto ao Suporte Online sem tornar atendimento uma etapa obrigatória.

**Condições verificáveis:**

- O botão não cobre conteúdo, controles ou mensagens importantes.
- No Checkout, o suporte não abre automaticamente nem compete com o CTA final.

#### FR-38 — Respostas cadastradas

O Atendimento Automático deve responder apenas quando houver conteúdo cadastrado aplicável.

**Condições verificáveis:**

- O sistema não inventa políticas, preços, prazos ou estados do Pedido.
- Quando não tiver confiança ou conteúdo, reconhece a limitação e oferece atendimento humano.

#### FR-39 — Escalonamento humano

A cliente deve continuar pelo Chat ou pelo WhatsApp quando o atendimento automático não resolver.

**Condições verificáveis:**

- No Chat, Sharom recebe histórico, idioma, página e contexto consentido.
- A cliente não precisa repetir a dúvida já registrada.
- O Atendimento Automático fica disponível 24 horas.
- O atendimento humano funciona de segunda a sábado, das 8h às 20h, no horário de Portugal continental.
- Fora do horário, a mensagem é preservada para a próxima janela; o site comunica prazo máximo de resposta de um dia útil, sem prometer presença imediata.

#### FR-40 — Administração de conteúdo do suporte

Sharom deve cadastrar, revisar, traduzir, ativar e desativar perguntas e respostas.

**Condições verificáveis:**

- Alterações têm autoria e data.
- Uma resposta desatualizada pode ser retirada sem implantação técnica.

### 6.8 Administração, produção e qualidade

#### FR-41 — Fila por Próxima Ação

Sharom deve visualizar pedidos por estado, responsável, prazo restante, prioridade e Próxima Ação.

**Condições verificáveis:**

- Pedidos dependentes da cliente aparecem separados dos acionáveis pela operação.
- A fila identifica atrasos e exceções sem depender de planilha paralela.
- A capacidade física inicial segura é de 100 peças por semana; Sharom pode abrir capacidade adicional quando houver disponibilidade operacional.
- A capacidade diária inicial de convites é de 4 pontos: Convite Padrão consome 1 ponto e Convite Complexo consome 2 pontos, permitindo até quatro Padrão, dois Complexos ou combinação equivalente.
- Sharom pode ajustar a capacidade futura sem alterar código, mas pedidos já pagos preservam a janela prometida.
- Quando os pontos de um dia estiverem ocupados, a página do convite e o Checkout exibem a próxima data disponível antes do pagamento.
- No pagamento, as unidades físicas reservam provisoriamente uma janela semanal compatível com a capacidade e a previsão exibida à cliente.
- Se o atraso do Briefing ou da Aprovação fizer a cliente perder a janela reservada, o sistema libera a capacidade, aloca a próxima janela disponível e comunica a nova previsão antes do início da Produção.
- Quando uma semana estiver completa, produto, Carrinho e Checkout exibem a próxima janela disponível antes da obrigação de pagar, sem ocultar o impacto no prazo.

#### FR-42 — Visão consolidada do Pedido

O painel deve conectar cliente, itens, pagamento, Briefing, Prévias, Aprovação, Produção ou entrega digital, Fatura, notificações e suporte por identificador único.

**Condições verificáveis:**

- Cada mudança relevante mantém autoria e data.
- O histórico não pode ser alterado sem registro de auditoria.

#### FR-43 — Ficha de Produção

Após Aprovação Final, o sistema deve gerar uma Ficha de Produção com versão aprovada, itens, quantidades, materiais, acabamento, Miniatura, prazo, endereço e checklist.

**Condições verificáveis:**

- A ficha referencia inequivocamente a versão aprovada.
- Alterações posteriores exigem exceção registrada.

#### FR-44 — Verificação de Qualidade

Sharom deve concluir um checklist e adicionar fotografia antes de marcar a encomenda como pronta para envio.

**Condições verificáveis:**

- Um item obrigatório não concluído impede avanço silencioso.
- Correção ou retrabalho registra motivo e novo prazo.

#### FR-45 — Fila de exceções

O sistema deve tratar quarta alteração, urgência, pagamento pendente, erro, retrabalho, falha de integração e pedido pausado como exceções explícitas.

**Condições verificáveis:**

- Cada exceção tem responsável, decisão, consequência financeira e impacto no prazo quando aplicável.
- Quarta rodada simples aplica a cobrança definida de €5; alteração complexa permanece sujeita a orçamento manual.
- Mudança após início da Produção calcula custo proporcional com evidência de unidades produzidas, materiais perdidos e retrabalho.
- Urgência nunca recebe promessa automática; depende de avaliação humana.
- Uma urgência aceita acrescenta 30% ao valor elegível, com cobrança mínima de €10, apresentada e aceita antes do pagamento.
- Convite Padrão urgente tem primeira Arte em até 12 horas após pagamento e dados completos; Convite Complexo urgente, em até 24 horas após Briefing completo.
- Produto físico urgente recebe prazo individual aprovado antes do pagamento; a urgência não inclui nem garante o tempo da transportadora.
- Sharom pode elevar um Pedido urgente acima de outro com maior folga, desde que a simulação demonstre que nenhum Pedido já confirmado ultrapassará a data prometida.
- A reordenação registra autora, motivo, posição anterior e nova, impacto previsto e horário; o sistema bloqueia ou alerta de forma destacada quando houver risco de violar um prazo confirmado.

#### FR-46 — Gestão do catálogo

Sharom deve administrar categorias, produtos, traduções, preços, variantes, quantidades, disponibilidade, temas, Miniaturas, complementos, descontos e conteúdo de confiança.

**Condições verificáveis:**

- Publicação valida os campos obrigatórios por modalidade.
- Mudanças de preço não alteram retroativamente um Pedido pago.
- Promoções próprias têm período, elegibilidade, texto legal, idiomas e estado de publicação configuráveis.
- Moedas suportadas, regras de preço ou conversão e arredondamento são configuráveis e auditáveis.

#### FR-47 — Gestão fiscal e logística

Sharom deve consultar pagamentos, conciliação, NIF, Faturas, fretes, rastreamentos, cancelamentos e falhas.

**Condições verificáveis:**

- O Novo Banco é a conta principal de recebimentos em euro.
- A Wise Business é a conta auxiliar para moedas estrangeiras suportadas; uma conta Wise pessoal não será usada para recebimentos comerciais.
- O provedor central repassa EUR prioritariamente ao Novo Banco e pode repassar GBP, USD, CHF e BRL à Wise Business somente quando a combinação de país, moeda, contrato e dados bancários for suportada e validada.
- Se uma moeda de cobrança não puder ser liquidada diretamente, o provedor a converte para a moeda de liquidação configurada, expondo taxa, câmbio e valor líquido na conciliação.
- A indisponibilidade de recebimento direto em uma moeda na Wise não remove essa moeda do Checkout quando o provedor puder cobrá-la e liquidá-la de forma transparente em EUR.
- Cada repasse preserva pagamentos de origem, moeda, valor bruto, taxa, conversão, reembolso e valor líquido recebido.
- Reprocessamento é seguro contra duplicação.
- Divergências permanecem visíveis até resolução.

#### FR-48 — Papéis e auditoria

O painel deve limitar ações administrativas por papel e registrar operações sensíveis.

**Condições verificáveis:**

- Acesso administrativo exige autenticação multifator.
- Permissões seguem privilégio mínimo.
- Se o marido de Sharom ou outra pessoa auxiliar a Produção pelo painel, recebe o papel de Assistente de Produção, limitado às Fichas de Produção, versões aprovadas, quantidades, materiais, checklist e estados necessários; dados financeiros, fiscais, conversas e arquivos não necessários permanecem bloqueados.
- Alterações em pagamento, preço, Arte aprovada, Fatura, endereço, consentimento e exclusão de dados são auditadas.

### 6.9 Instrumentação

#### FR-49 — Eventos de jornada

O sistema deve medir, com consentimento adequado, descoberta, busca, visualização de produto, configuração, Carrinho, início e conclusão do Checkout, Briefing, Aprovação, entrega e suporte.

**Condições verificáveis:**

- Eventos não coletam conteúdo sensível do Briefing, arquivos ou mensagens.
- Os dados permitem separar modalidade, idioma, dispositivo e origem sem identificar desnecessariamente a cliente.

#### FR-50 — Indicadores operacionais

O painel deve calcular tempo por estado, pedidos pausados, cumprimento de prazo, alterações, retrabalho, falhas e dependência de suporte.

**Condições verificáveis:**

- O cálculo distingue tempo de criação, Produção e transporte.
- Períodos pausados por dependência da cliente são identificados separadamente.

## 7. Escopo do MVP

### 7.1 Incluído e obrigatório

- experiência premium, responsiva, trilíngue e acessível;
- SEO técnico e conteúdo essencial para descoberta no Google;
- catálogo, busca exata agrupada, semelhantes e Projeto Exclusivo;
- produtos físicos, convites personalizados e Produtos Digitais Prontos;
- Carrinho, configuração, Miniatura, complementos e benefícios aplicáveis;
- Checkout em uma página, compra como visitante, pagamentos, transferência em 48 horas, frete físico automatizado para os 27 países da União Europeia, NIF e Fatura;
- Área da Cliente criada ou associada após a compra;
- Pré-formulário, Briefing com salvamento automático, Prévias, três Rodadas de Alteração e Aprovação Final;
- prazos de 24/48 horas para Arte e sete dias corridos para Produção física após Aprovação;
- linha do tempo, pausas, Próxima Ação, notificações, fotografia final e rastreamento;
- entrega automática de Produto Digital Pronto e entrega de convite por e-mail ou WhatsApp;
- Suporte Online com respostas cadastradas e escalonamento humano;
- fila administrativa, Ficha de Produção, qualidade, exceções, catálogo, fiscalidade, logística, permissões e auditoria;
- instrumentação do baseline de 30 dias;
- segurança, privacidade, conformidade, recuperação e testes de lançamento.

### 7.2 Fora do MVP

- consultoria guiada por orçamento;
- vários CTAs concorrentes para a mesma modalidade;
- personagens anunciados como produtos, temas ou caminho de descoberta;
- promessa ou cálculo automático de urgência;
- recompensa por avaliações;
- calendário de celebrações;
- fidelização e pontos;
- cartões-presente;
- recomendações avançadas;
- login social;
- blog completo;
- francês;
- pré-visualização automática produzida pela cliente;
- comparação avançada de convites;
- relatórios avançados além do baseline;
- marketplace, atacado ou portal de revendedores.

### 7.3 Após o lançamento

Favoritos, avaliações com consentimento, busca preditiva, recuperação automatizada de Carrinho, recompra rápida, automações adicionais, comparação avançada e relatórios ampliados poderão ser priorizados a partir dos dados do baseline.

## 8. Requisitos não funcionais

### NFR-1 — Acessibilidade

As jornadas essenciais devem atender ao nível AA da WCAG 2.2, incluindo navegação por teclado, foco visível, nomes acessíveis, contraste, mensagens de erro compreensíveis, zoom, redução de movimento e compatibilidade com leitores de tela.

### NFR-2 — Desempenho percebido

Em conexão móvel representativa do público europeu, páginas públicas e etapas de compra devem carregar e responder sem que fotografias, vídeos, fontes ou scripts bloqueiem a tarefa principal. `[PRESSUPOSTO]` O aceite quantitativo usará os limites “bons” dos Core Web Vitals vigentes na implementação, medidos no 75º percentil por dispositivo.

### NFR-3 — Disponibilidade e degradação

Falhas de chat, analytics, newsletter ou mídia complementar não devem impedir busca, Carrinho, Checkout, Briefing, Aprovação ou acesso ao Pedido. Falhas de provedor crítico devem apresentar estado seguro e permitir retomada.

### NFR-4 — Idempotência e consistência

Repetição de confirmação, webhook, clique ou trabalho assíncrono não pode duplicar cobrança, Pedido, benefício, Miniatura, Fatura, notificação ou entrega.

### NFR-5 — Segurança de aplicação

O lançamento deve demonstrar controles proporcionais ao OWASP ASVS nível 2, revisão de código, análise de dependências, testes automatizados de segurança e avaliação independente antes da entrada em produção.

**Aceite adicional:** o gate de segurança inclui varredura, pentest antes do lançamento e depois de alterações relevantes, bloqueio de upload malicioso e correção dos achados críticos e altos antes da produção.

### NFR-6 — Pagamentos

Dados completos de cartão e códigos de segurança não devem transitar nem ser armazenados pela aplicação da JS Designs; o processamento deve permanecer no ambiente do parceiro certificado e no escopo PCI DSS aplicável.

### NFR-7 — Privacidade por design

Coleta, uso, compartilhamento, retenção e eliminação de dados devem seguir necessidade, finalidade, transparência e direitos previstos pelo RGPD. Consentimentos opcionais devem ser separados e revogáveis.

**Condições verificáveis:**

- Antes do lançamento existe mapa de tratamentos com finalidade, categoria de dados, titular, base legal, destinatários, transferências, retenção, controles e responsável.
- A operação possui fluxo testado para acesso, correção, portabilidade, oposição e eliminação quando aplicáveis.
- Fornecedores que tratam dados têm função, local de tratamento e instrumento contratual documentados.
- Tratamentos de maior risco são submetidos à avaliação de impacto apropriada.
- Existe processo testado de detecção, avaliação, registro e notificação de violação de dados dentro dos prazos aplicáveis.
- A tabela de retenção parte dos prazos abaixo e é validada por responsável jurídico/RGPD e contabilista antes do lançamento:
  - Carrinhos e Pré-formulários abandonados: 30 dias;
  - fotografias de crianças, Miniaturas e uploads sensíveis: 30 dias após a entrega final;
  - os mesmos arquivos para possível recompra: até 12 meses, somente com autorização separada e revogável;
  - Briefing, Prévias, versões e arquivos de Produção: 12 meses após a conclusão do Pedido;
  - convite final personalizado: acesso por 12 meses, com orientação para download;
  - Produto Digital Pronto adquirido: acesso vitalício conforme a licença;
  - conversas de suporte: 12 meses após o encerramento;
  - logs técnicos e de auditoria: 12 meses, exceto quando incidente ou obrigação legal justificar bloqueio e extensão;
  - backups: rotação máxima de 90 dias, propagando exclusões ao término do ciclo;
  - Faturas e registros fiscais: prazo legal aplicável, usando 10 anos como referência inicial para validação;
  - dados estritamente necessários a garantia, disputa ou obrigação legal: somente pelo prazo justificável e com acesso restrito.
- O sistema deve executar exclusão ou revisão automática ao fim de cada prazo e registrar a conclusão sem conservar o próprio conteúdo eliminado.

### NFR-8 — Arquivos privados

Briefings, Miniaturas, fotografias de crianças, Prévias e fotos de qualidade devem permanecer privados, protegidos contra enumeração e acesso não autorizado, e ser eliminados conforme política de retenção.

**Condições verificáveis:**

- A pessoa que envia fotografia ou dado de criança confirma que possui autoridade para fornecê-lo para a finalidade declarada.
- Cada finalidade tem base legal validada, dados mínimos, prazo e destinatários definidos.
- Uma avaliação específica documenta riscos e controles para imagens de crianças.
- Testes de autorização demonstram que cliente, suporte e Administração só acessam Pedidos permitidos.
- A exclusão é testada de ponta a ponta, incluindo derivados e ciclos de backup conforme política aprovada.

### NFR-9 — Continuidade

Dados críticos devem ter cópias protegidas e restauração testada. O lançamento exige procedimento de resposta a incidentes e evidência de recuperação dos fluxos críticos.

### NFR-10 — Internacionalização

Português do Brasil, inglês e espanhol devem cobrir interface, conteúdo comercial, mensagens transacionais, e-mails, formulários, políticas, suporte cadastrado e metadados de SEO. Datas, endereços, moedas e números devem ser apresentados sem ambiguidade. A detecção regional é apenas uma sugestão e a preferência manual da cliente prevalece.

### NFR-11 — Observabilidade

Falhas em pagamento, Fatura, frete, e-mail, WhatsApp, upload, antimalware, entrega digital e mudança de estado devem ser detectáveis, correlacionadas ao Pedido e acionáveis pela operação.

### NFR-12 — Compatibilidade

O site deve ser responsivo e preservar as jornadas essenciais em computadores, tablets e celulares, sem exigir sites separados ou aplicativo nativo.

**Condições verificáveis:**

- O suporte formal cobre as duas versões estáveis mais recentes de Chrome, Safari, Edge e Firefox em desktop, Safari em iPhone/iPad e Chrome em Android.
- A interface funciona a partir de 320 px de largura e se adapta continuamente a celular, tablet e computador, sem depender apenas de pontos fixos por dispositivo.
- Busca, comparação de convites, configuração, Kit Composto, Carrinho, Checkout, Briefing, upload, Prévia, Aprovação, Área da Cliente, arquivos e Suporte Online mantêm a mesma capacidade essencial em todos os formatos.
- A navegação suporta toque, mouse, teclado e tecnologias assistivas; alvos de toque, foco, zoom e orientação de tela são testados.
- Navegadores fora da matriz podem funcionar, mas não recebem garantia formal; quando incompatíveis com uma função essencial, o site apresenta orientação clara e alternativa segura.

## 9. Guardrails legais, de privacidade e de confiança

- Informações pré-contratuais devem ser claras e próximas da decisão de compra: identidade comercial, características, preço total, pagamento, restrições de entrega, prazos, devoluções, cancelamento e atendimento.
- Produtos claramente personalizados e conteúdo digital exigem políticas próprias, revisadas por profissional competente para os países atendidos.
- Antes do download imediato, a cliente deve fornecer os reconhecimentos legalmente exigidos sobre início do fornecimento digital e eventual perda do direito de arrependimento.
- Produtos defeituosos ou divergentes da descrição continuam sujeitos às garantias legais aplicáveis; a Aprovação Final não elimina obrigações da JS Designs.
- A política comercial de cancelamento, devolução e reembolso adota a seguinte base, sujeita à validação jurídica para Portugal e cada mercado atendido:
  - antes do início da criação de um produto personalizado, a cliente pode cancelar com reembolso integral;
  - após o início da criação e antes da Aprovação Final, o reembolso desconta somente o trabalho e os custos comprovadamente já realizados;
  - após a Aprovação Final ou o início da Produção física, não há cancelamento comum; aplicam-se as regras aprovadas de alteração, retrabalho e custo proporcional;
  - um Produto Digital Pronto admite reembolso antes do primeiro acesso ou download; a liberação imediata exige consentimento expresso e registro da ciência sobre a perda do direito de desistência;
  - um produto físico não personalizado observa o prazo legal de desistência aplicável;
  - produto errado, danificado, defeituoso, inacessível ou divergente da descrição ou da Arte aprovada permanece elegível às soluções legais cabíveis, incluindo correção, substituição ou reembolso;
  - uma transferência bancária não confirmada em 48 horas causa o cancelamento automático do Pedido, sem tratar o Pedido como pago.
- Em Pedido misto, as regras de cancelamento, devolução, reembolso, garantia e não conformidade são apresentadas e aplicadas por item e modalidade.
- Uso comercial de personagens, músicas, imagens, fontes e conteúdo enviado pela cliente requer governança de propriedade intelectual.
- A cliente pode solicitar livremente qualquer tema, mesmo que ele não exista nos exemplares publicados; personagens não serão anunciados no catálogo.
- A JS Designs aceita criações próprias, temas genéricos, fotografias pessoais devidamente autorizadas e ativos cuja licença permita expressamente o uso comercial pretendido.
- Personagens famosos, marcas, logotipos, músicas, fontes, imagens ou outros ativos protegidos não podem ser anunciados nem produzidos sem autorização ou licença comercial válida; o envio pela cliente não constitui autorização automática nem transfere integralmente a responsabilidade.
- Quando uma solicitação envolver ativo protegido sem autorização comprovada, a operação deve recusar esse elemento e oferecer uma alternativa original baseada somente em características genéricas, sem copiar o ativo.
- A origem, a licença, as condições e a evidência de autorização dos ativos usados devem ser registradas. A loja deve manter canal de denúncia, análise e retirada rápida de conteúdo.
- A licença do Produto Digital Pronto autoriza peças físicas, mas não transfere titularidade nem autoriza distribuição digital do arquivo ou derivados.
- Fotografias de crianças, Miniaturas e Prévias não são públicas por padrão e não podem ser usadas em marketing sem consentimento separado.
- Fotografias de crianças e Miniaturas são eliminadas após a finalidade operacional por padrão; conservação para recompra exige autorização separada e prazo definido.
- Produtos físicos exigem avaliação de segurança, identificação e rastreabilidade proporcionais, advertências quando aplicáveis e procedimento de retirada/recall e comunicação à cliente.
- A loja deve publicar políticas de privacidade, cookies, compra, personalização, alterações, cancelamento, devolução, reembolso, entrega, conteúdo digital e propriedade intelectual.
- A mensagem de confiança sobre pagamentos não pode prometer segurança absoluta.
- Requisitos legais e fiscais devem ser validados para Portugal e demais países efetivamente atendidos antes do lançamento.

## 10. Dependências e riscos

### 10.1 Dependências críticas

- regras finais de catálogo, preços, quantidades, disponibilidade e benefícios;
- parceiro de pagamento e método de confirmação da transferência;
- solução de faturamento e regras de NIF, IVA/OSS e países atendidos;
- matriz fiscal para vendas digitais a clientes de países europeus fora da União Europeia;
- transportadoras, cálculo de frete e rastreamento;
- provedores de e-mail, WhatsApp e Chat;
- armazenamento privado, análise de uploads e entrega de arquivos;
- traduções e revisão de conteúdo nos três idiomas;
- políticas legais, retenção, consentimentos e direitos autorais;
- catálogo final e fotografias reais;
- definição de horário, capacidade e responsáveis operacionais.

### 10.2 Riscos prioritários e respostas

- **Lançar apenas a vitrine:** bloqueado pelo gate de Pedido ponta a ponta e operação sem planilha.
- **Prazos incoerentes:** uma única regra de prazo por modalidade, exibida em produto, confirmação, Área da Cliente e painel.
- **Cobrança ou Fatura duplicada:** idempotência, conciliação e fila de divergências.
- **Vazamento de arquivos privados:** acesso autenticado, retenção mínima, rastreabilidade e testes.
- **Uso indevido de propriedade intelectual:** revisão de catálogo e processo de denúncia/retirada.
- **Sobrecarga operacional:** fila por Próxima Ação, capacidade visível e urgência somente manual.
- **Tradução incompleta:** gate de paridade das jornadas essenciais nos três idiomas.
- **Dependência do WhatsApp:** fluxo normal completo dentro da plataforma.
- **Suporte automático incorreto:** respostas exclusivamente cadastradas e escalonamento seguro.
- **Baixa descoberta orgânica:** SEO essencial no MVP e baseline desde o primeiro dia.

## 11. Métricas de sucesso

### 11.1 Baseline inicial

Nos primeiros 30 dias, o sistema deve registrar:

- impressões e cliques orgânicos por página, tema e idioma;
- visitas qualificadas por origem;
- buscas com resultado, semelhantes e sem resultado;
- visualização de produto, adição ao Carrinho, início e conclusão do Checkout;
- compras pagas por modalidade, idioma e dispositivo;
- abandono por etapa;
- pedidos personalizados concluídos sem atendimento humano obrigatório;
- tempo até Briefing, primeira Arte, Aprovação, Produção e entrega;
- percentual de prazos cumpridos, pedidos pausados e exceções;
- Rodadas de Alteração, retrabalho, cancelamentos e reembolsos;
- dúvidas resolvidas automaticamente, transferidas ao humano e tempo de resposta;
- falhas de entrega digital, pagamento, Fatura, frete e notificação;
- conclusão das jornadas essenciais e defeitos de acessibilidade por idioma.

### 11.2 Definição das metas

Ao final do 30º dia, a JS Designs definirá metas para os 90 dias seguintes a partir do baseline. A revisão deverá nomear meta, valor inicial, alvo, prazo e responsável.

### 11.3 Contramétricas

- Aumentar conversão não pode reduzir clareza pré-compra nem elevar cancelamentos, reclamações ou retrabalho.
- Reduzir contatos humanos não pode diminuir resolução, satisfação ou acessibilidade.
- Acelerar Produção não pode aumentar divergência em relação à Arte aprovada.
- Aumentar tráfego não pode degradar desempenho, privacidade ou relevância da busca.

## 12. Gates de lançamento

### Gate F0 — Fundação decidida

Antes da construção do núcleo transacional, devem estar aprovados: regras comerciais e de prazo; taxonomia de produtos, famílias e formatos; Estados do Pedido; Briefing e Aprovação; políticas de privacidade e retenção; papéis operacionais; países, moeda e fiscalidade; e fornecedores críticos.

| Decisão | Responsável | Condição de desbloqueio |
|---|---|---|
| Moeda, IVA/OSS, Fatura e tributação digital | Sharom + contabilista/fiscalista | Matriz aprovada para UE e vendas digitais europeias fora da UE antes de Checkout e faturamento; frete físico automático limitado à UE |
| Enquadramento da atividade e documentos fiscais | Sharom + contabilista | Validar eventual CAE complementar ao CIRS 1336 e o documento fiscal por modalidade/momento antes da integração de faturamento |
| Desistência, cancelamento, devolução, reembolso, garantia e conteúdo digital | Sharom + assessoria jurídica | Validar juridicamente a política comercial definida por modalidade e os textos/consentimentos antes de UX do Checkout |
| Retenção, bases legais, direitos, crianças, fornecedores e violações | Sharom + responsável RGPD/jurídico + contabilista | Validar a tabela-base definida, o mapa de tratamento e os procedimentos antes de usar dados reais |
| Propriedade intelectual do catálogo e de pedidos personalizados | Sharom + assessoria jurídica | Política validada, inventário de ativos revisado e evidências de licença/autorização registradas antes da publicação |
| Família, formato, recursos e classificação Padrão/Complexo | Sharom | Matriz de catálogo aprovada antes da migração |
| Quantidades, Miniatura física, descontos, quarta alteração e pós-aprovação | Sharom | Tabela comercial aprovada antes de Carrinho e Briefing |
| Pagamento, câmbio, transferência, Fatura, frete, e-mail, WhatsApp, Chat e armazenamento | Arquitetura + Sharom | Fornecedores escolhidos, fonte de câmbio aprovada e provas de conceito dos fluxos críticos, incluindo EUR, GBP, USD, CHF e BRL |
| Disponibilidade, desempenho, RPO/RTO, alertas e navegadores | Arquitetura + Sharom | Orçamentos de qualidade aprovados antes do desenvolvimento dos NFRs |
| Capacidade, urgência, horário e SLA humano | Sharom | Política operacional aprovada antes de publicar prazos e suporte |

### Gate M1 — Jornada funcional

Uma encomenda realista de cada modalidade deve funcionar ponta a ponta: produto físico personalizado, Convite Padrão, Convite Complexo e Produto Digital Pronto. Cada uma deve ser operável sem planilha paralela e sem depender do WhatsApp no fluxo normal.

### Gate M2 — Prontidão operacional

- catálogo final e traduções migrados;
- protótipo alinhado às regras finais de prazo, CTA, taxonomia, configuração e modalidades de produto;
- pagamentos, transferência, Fatura, frete, e-mail, WhatsApp e arquivos testados em sucesso, falha e repetição;
- segurança, acessibilidade, privacidade, restauração e políticas aprovadas;
- mapa RGPD, tabela de retenção, testes de direitos e resposta a violações aprovados;
- matriz B2C, fiscal e logística aprovada por país e modalidade;
- avaliação de segurança e rastreabilidade dos produtos físicos preparada;
- operação treinada em fila normal e exceções;
- dados do baseline validados;
- procedimento de reversão do lançamento preparado.

## 13. Referências externas de conformidade

As referências abaixo contextualizam requisitos e não substituem validação jurídica ou técnica:

- [W3C — visão geral da WCAG 2](https://www.w3.org/WAI/standards-guidelines/wcag/);
- [União Europeia — comércio eletrônico B2C, venda a distância e conteúdo digital](https://europa.eu/youreurope/business/selling-in-eu/selling-goods-services/ecommerce-distance-selling/index_en.htm);
- [Comissão Europeia — proteção de dados por design](https://commission.europa.eu/document/download/0a85ce87-f284-44cf-9c0c-1ef5c3dffdcf_en?filename=data-protection-factsheet-business_en.pdf);
- [Comissão Europeia — limitação da conservação de dados pessoais](https://commission.europa.eu/law/law-topic/data-protection/rules-business-and-organisations/principles-gdpr/how-long-can-data-be-kept-and-it-necessary-update-it_en);
- [Portal das Finanças — conservação de registros e documentos fiscais](https://info.portaldasfinancas.gov.pt/pt/informacao_fiscal/codigos_tributarios/civa_rep/ra/pages/ivara52-0808.aspx);
- [European IP Helpdesk — uso comercial de personagens famosos](https://intellectual-property-helpdesk.ec.europa.eu/news-events/news/using-famous-cartoon-characters-products-or-artworks-2024-03-29_en);
- [EUR-Lex — proteção e aplicação dos direitos de propriedade intelectual](https://eur-lex.europa.eu/eli/dir/2004/48/oj/eng);
- [PCI Security Standards Council](https://www.pcisecuritystandards.org/).
