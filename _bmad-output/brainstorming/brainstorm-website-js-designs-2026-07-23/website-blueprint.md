# Blueprint do website JS Designs

## 1. Finalidade e autoridade deste documento

Este blueprint consolida a visão funcional, comercial, operacional e técnica do website premium e da loja online da JS Designs. Ele deve orientar as etapas posteriores de UX, arquitetura, requisitos, implementação, conteúdo, operação e testes.

**Fonte única:** `.memlog.md` da sessão de brainstorming “Website premium e loja online completa da JS Designs”, atualizada em 24 de julho de 2026. Nenhuma decisão externa foi acrescentada.

### 1.1 Legenda de maturidade

Para evitar que ideias sejam tratadas como decisões, os requisitos usam três estados:

- **Definido:** decisão ou direção explícita registrada na memória.
- **Proposto:** ideia registrada, mas sem aprovação explícita; precisa de validação antes de virar requisito definitivo.
- **Decisão futura:** assunto necessário ao produto, porém ausente, divergente ou incompleto na memória.

### 1.2 Objetivo do produto

Criar uma experiência de compra premium, intuitiva, segura e escalável na qual a cliente consiga:

1. descobrir produtos e modelos;
2. entender preço, prazo e possibilidades de personalização;
3. configurar e comprar sem cadastro prévio obrigatório;
4. pagar de forma protegida;
5. fornecer o briefing depois do pagamento;
6. revisar e aprovar artes;
7. acompanhar criação, produção, qualidade, envio e entrega;
8. receber produtos digitais ou físicos;
9. obter suporte quando necessário;

sem depender do WhatsApp para a jornada normal.

O WhatsApp fica reservado a suporte, escalonamento do chat, consulta de urgência e projetos totalmente exclusivos.

## 2. Visão, posicionamento e princípios

### 2.1 Proposta de valor

A JS Designs deve ajudar a cliente a realizar uma celebração marcante com produtos de qualidade e preço justo, tornando simples escolher, finalizar a compra e acompanhar o pedido. A marca não deve se posicionar como “barata”; o valor percebido vem de clareza, confiança, acabamento visível e acompanhamento transparente.

### 2.2 Atributos de marca

**Definido:**

- elegância;
- confiança;
- exclusividade;
- profissionalismo;
- versatilidade;
- qualidade artesanal;
- facilidade de escolha;
- suporte disponível quando necessário.

### 2.3 Direção visual

**Definido:**

- estética minimalista, sofisticada e atemporal;
- branco, bege muito claro, dourado champanhe, preto suave e taupe claro;
- muito espaço em branco;
- fotografias grandes de produtos reais;
- tipografia de alta qualidade;
- animações suaves;
- provas visuais de acabamento: laços, aplicação de pedras, embalagem, precisão de corte, dobras, encaixes, impressão, montagem e recortes dos topos de bolo.

As referências externas e imagens de layout orientam lógica comercial, organização e navegação; não autorizam copiar identidade visual, cores ou design.

### 2.4 Princípios de experiência

1. **Praticidade acima de complexidade visível.** A operação interna absorve a complexidade para que a experiência da cliente permaneça simples.
2. **Divulgação progressiva.** Cada momento revela apenas o necessário: cartão, página de produto, carrinho, checkout e pós-compra possuem níveis crescentes de detalhe.
3. **Catálogo em vez de consultoria por orçamento.** Produtos e modelos devem estar disponíveis diretamente para descoberta e compra.
4. **Transparência antes da compra.** Preço, prazo normal, material, quantidade, personalização e processo aplicável devem estar claros no ponto certo da jornada.
5. **Um CTA principal.** “Comprar agora” é a chamada principal na maioria dos produtos; lembrancinhas usam “Comprar já”. Não criar vários CTAs por modalidade.
6. **Pós-compra autocontido.** Briefing, prévias, alterações, aprovação, histórico e acompanhamento ficam registrados na área da cliente.
7. **Suporte contextual, não obrigatório.** O suporte ajuda em dúvidas de customização e prazo, mas não se torna etapa do checkout.
8. **Urgência verdadeira.** Não prometer ou calcular produção urgente automaticamente; disponibilidade e taxa dependem de confirmação humana.
9. **Qualidade demonstrável.** Fotografias e vídeos devem provar o acabamento, não apenas afirmá-lo.
10. **Privacidade e segurança por design.** Dados pessoais, fiscais, pagamentos, uploads e fotografias de crianças devem ser protegidos desde o lançamento.
11. **Urgência comercial somente real.** Promoções podem usar prazo apenas quando a oferta tiver validade verdadeira.

## 3. Públicos e necessidades

### 3.1 Cliente compradora

Procura produtos para uma celebração e precisa perceber rapidamente:

- variedade para diferentes festas;
- qualidade do acabamento;
- facilidade de escolha;
- preços e prazos claros;
- como funciona a personalização;
- como receberá ou acompanhará o pedido;
- onde obter ajuda.

### 3.2 Cliente recorrente

Valoriza resultado visual, qualidade consistente, atendimento rápido e cumprimento das expectativas. Deve conseguir reencontrar ou repetir produtos, especialmente topos de bolo, sem reconstruir todo o pedido.

### 3.3 Cliente de projeto exclusivo

Não encontrou tema ou modelo adequado no catálogo. Deve receber uma chamada discreta para contactar a JS Designs e iniciar um projeto sob medida, sem transformar esse caminho no fluxo principal da loja.

### 3.4 Operação JS Designs

Precisa transformar pagamentos em trabalho executável, controlar briefing, versões, alterações, aprovações, prazos pausados, produção, materiais, qualidade, embalagem, envio, fiscalidade e suporte em uma única operação rastreável.

## 4. Arquitetura da informação e sitemap

### 4.1 Navegação global

**Definido:**

- cabeçalho simplificado;
- busca fácil de encontrar e visualmente destacada;
- categorias reunidas em um menu **Loja** bem organizado;
- poucos itens visíveis na navegação principal;
- chat e WhatsApp como botões flutuantes laterais, compactos e recolhidos;
- botões de suporte ocultos durante o checkout.

**Proposto para validação:** faixa promocional, banner rotativo, provas de confiança e área de mais vendidos, inspirados na lógica comercial da referência indicada.

### 4.2 Sitemap proposto a partir da memória

```text
Início
├── Busca
│   ├── Resultados exatos por categoria
│   ├── Sugestões semelhantes
│   └── Projeto exclusivo quando não houver resultado
├── Loja
│   ├── Topos de bolo
│   ├── Lembrancinhas
│   ├── Kits
│   ├── Convites
│   │   ├── Essenciais
│   │   ├── Interativos
│   │   ├── Infinito
│   │   └── Cinemágicos
│   └── Produtos digitais prontos
├── Página de produto
│   ├── Informações essenciais
│   ├── Opções aplicáveis
│   ├── Qualidade e acabamentos
│   ├── Processo e prazo
│   └── Ajuda com este produto
├── Carrinho
│   ├── Configuração de personalização
│   ├── Miniatura
│   └── Complete sua festa
├── Checkout
├── Confirmação da compra
├── Área da cliente
│   ├── Pedidos
│   │   ├── Linha do tempo
│   │   ├── Briefing
│   │   ├── Prévias e alterações
│   │   ├── Aprovação
│   │   ├── Fatura
│   │   └── Downloads
│   ├── Dados e preferências
│   └── Celebrações e recompra [fase posterior]
├── Projeto exclusivo / contato
├── Políticas e informações legais
└── Administração
    ├── Pedidos
    ├── Briefings
    ├── Artes e versões
    ├── Fila de produção
    ├── Fichas de produção
    ├── Qualidade e expedição
    ├── Catálogo e preços
    ├── Promoções
    ├── Clientes e suporte
    ├── Fiscalidade
    ├── Conteúdo do chat
    ├── Segurança e auditoria
    └── Relatórios [fase posterior]
```

### 4.3 Decisões futuras de arquitetura da informação

- nomes finais dos itens do cabeçalho e rodapé;
- existência e conteúdo de páginas institucionais;
- taxonomia detalhada de ocasiões, temas e personagens;
- estrutura de URLs;
- filtros globais e filtros específicos por categoria;
- políticas de envio, cancelamento, devolução, reembolso e licenciamento digital;
- páginas legais obrigatórias e mecanismo de consentimento;
- conteúdo e ordem final da página inicial.

## 5. Modelo de catálogo, tipos e estados de produto

### 5.1 Eixos do modelo de produto

Cada item deve ser descrito por eixos independentes, evitando misturar formato, entrega e personalização:

| Eixo | Valores derivados da memória | Maturidade |
|---|---|---|
| Natureza | físico; digital | Definido |
| Categoria | topo de bolo; lembrancinha; kit; convite; produto digital pronto | Definido |
| Personalização | sem personalização; personalização básica incluída; customização; projeto exclusivo | Fluxos definidos; nomenclatura final a validar |
| Forma de entrega | envio físico; arquivo digital automático; entrega digital após criação e aprovação | Definido |
| Modelo comercial | modelo de catálogo; modelo de catálogo customizável; criação não existente no catálogo | Definido |
| Tema e descoberta | personagem, tema, ocasião e variações de escrita | Definido para metadados de busca |

### 5.2 Tipos comerciais

#### A. Produto físico com personalização incluída

Exemplo principal: lembrancinhas e kits.

- kits de 20, 30, 40 e 50 unidades;
- quantidade maior escolhida pela cliente;
- personalização básica com nome e idade incluída no preço;
- cliente ativa ou dispensa a personalização no carrinho;
- briefing curto depois do pagamento;
- pode incluir miniatura fornecida pela cliente sem custo ou criação paga pela JS Designs;
- segue prévia, aprovação, produção e envio.

#### B. Produto físico pronto ou customizável

Exemplo principal: topo de bolo.

- modelos prontos para compra;
- opção separada de customização;
- segue o fluxo correspondente à opção comprada.

**Maturidade:** estrutura proposta pela cliente na memória; detalhes de configuração, preço e fronteira entre “pronto” e “customizado” são decisão futura.

#### C. Convite digital personalizado

- comprado a partir de um modelo;
- customização total ocorre depois do pagamento;
- exige briefing do evento;
- exige criação, prévia e aprovação;
- prazo depende da família e complexidade;
- não deve ser confundido com arquivo digital pronto.

#### D. Produto digital pronto

- não possui customização;
- compra com “Comprar agora”;
- entrega automática por e-mail após a compra;
- a memória também propõe escolha entre e-mail e WhatsApp para recebimento; o canal definitivo de entrega precisa ser reconciliado, pois a entrega automática por e-mail foi expressamente registrada.

#### E. Projeto exclusivo

- serve para temas ou modelos não encontrados no catálogo;
- começa por contato com a JS Designs;
- exige questionário de criação mais detalhado;
- não deve dominar a navegação nem substituir o catálogo.

### 5.3 Classificação comunicada à cliente

**Proposto, pendente de ratificação da nomenclatura:**

- **Modelo pronto**
- **Personalização incluída**
- **Projeto exclusivo**

O objetivo é mostrar, antes da compra, qual fluxo ocorrerá. A taxonomia pode ser mantida como atributo/selo mesmo que a categoria comercial use outro nome.

### 5.4 Estados editoriais e de disponibilidade

A memória não define estados como rascunho, publicado, esgotado, oculto, sob encomenda ou indisponível. O painel precisará deles, mas valores e regras são **decisões futuras**.

### 5.5 Dados mínimos do produto

**Derivados diretamente das necessidades de catálogo e busca:**

- nome;
- categoria;
- fotografias;
- preço;
- quantidade ou opções de quantidade;
- natureza física ou digital;
- modelo/variante;
- tema;
- personagem;
- ocasião;
- variações de escrita para busca;
- personalização disponível e/ou incluída;
- prazo aplicável;
- material e gramatura, quando físico;
- composição do kit, quando aplicável;
- recursos do convite, quando aplicável;
- compatibilidade com miniatura;
- produtos complementares relacionados;
- status de “Mais pedido”, quando aplicável;
- demonstração real para convites e digitais, quando disponível.

Campos, validações, unidades, estrutura de variantes e regras de estoque são **decisões futuras**.

## 6. Busca e descoberta

### 6.1 Objetivo

A busca é o caminho principal para encontrar produtos por personagem ou tema. Ela deve evitar que a cliente navegue por longas listas ou dependa de cartões de personagens na página inicial.

### 6.2 Comportamento dos resultados

**Definido:**

1. consultar título e metadados de personagem, tema e variações de escrita;
2. mostrar primeiro os resultados exatos;
3. incluir todos os tipos relacionados: convites, lembrancinhas, kits, topos de bolo e digitais;
4. agrupar resultados exatos por categoria;
5. mostrar sugestões semelhantes abaixo, em seção visualmente separada;
6. quando não houver resultado exato, informar com clareza, sugerir alternativas e oferecer projeto exclusivo.

O mockup de busca mencionado na memória foi aprovado como referência dessa organização.

### 6.3 Cartões de resultado

Aplicar divulgação progressiva:

- fotografia;
- nome;
- preço;
- CTA principal;
- selo discreto “Mais pedido” quando aplicável;
- selo de personalização quando necessário para evitar ambiguidade.

Regras, opções e prazos detalhados devem aparecer na página do produto, no carrinho ou no pós-compra correspondente.

### 6.4 Pesquisa preditiva

**Fase posterior ao núcleo:**

- sugestões durante a digitação;
- tolerância a erros e variações de escrita;
- correspondência por metadados não presentes no título.

### 6.5 Fluxo textual de busca

```text
Cliente digita personagem ou tema
→ sistema procura correspondência exata em título e metadados
→ se houver: mostra todos os produtos relacionados agrupados por categoria
→ abaixo: mostra seção separada de sugestões semelhantes
→ cliente abre um produto e continua para a página correspondente

Se não houver correspondência exata
→ informa “não encontramos resultado exato”
→ mostra alternativas semelhantes
→ oferece contato para projeto exclusivo
```

### 6.6 Critérios de aceite

- uma pesquisa por personagem retorna categorias diferentes relacionadas ao mesmo universo;
- resultados semelhantes nunca aparecem misturados aos exatos;
- a ausência de resultado não gera página vazia;
- o projeto exclusivo aparece como alternativa discreta;
- a busca permanece fácil de localizar no cabeçalho e na página inicial;
- cartões não exibem excesso de regras ou textos.

### 6.7 Decisões futuras

- algoritmo e pesos de relevância;
- quantidade de resultados por grupo;
- filtros, ordenação e paginação;
- sinônimos e governança de metadados;
- comportamento multilíngue;
- moderação de nomes de personagens e questões de direitos de propriedade intelectual.

## 7. Páginas principais

### 7.1 Página inicial

**Conteúdo definido ou diretamente derivado:**

1. busca em posição de destaque;
2. produtos mais pedidos no topo;
3. produtos relevantes distribuídos ao longo da página;
4. comunicação breve de variedade, qualidade artesanal, personalização simples e apoio humano;
5. fotografias reais e amplas;
6. provas visuais do acabamento;
7. acesso organizado à Loja;
8. chamada discreta para projeto exclusivo;
9. promoção de primeira compra, fora do checkout e sem interromper imediatamente a visita.

**Proposto para validação:**

- faixa promocional;
- banner rotativo;
- bloco de provas de confiança;
- seção “Qualidade que você pode ver”;
- galeria “Detalhes que fazem a diferença”.

**Não usar como caminho principal:** cartões de temas ou personagens na página inicial.

### 7.2 Página de categoria

Deve:

- mostrar produtos com cartões enxutos;
- preservar busca visível;
- permitir descoberta por categoria sem impedir busca por personagem;
- comunicar prazo e personalização apenas no nível necessário;
- dar destaque inicial à categoria de topos de bolo por já demonstrar recompra.

Filtros, ordenação, paginação, conteúdo editorial e regras de destaque são **decisões futuras**.

### 7.3 Página genérica de produto

Estrutura recomendada pela divulgação progressiva:

1. galeria com fotos grandes e, quando disponível, vídeo;
2. nome, preço e CTA;
3. seleção de variante/quantidade aplicável;
4. resumo de personalização;
5. prazo padrão;
6. aviso discreto sobre urgência;
7. resumo do processo pós-compra;
8. qualidade, materiais e acabamentos;
9. seções expansíveis de detalhes;
10. ajuda contextual com nome e link do produto anexados ao suporte;
11. chamada discreta para projeto exclusivo se o tema desejado não existir.

### 7.4 Página de kit

Deve informar:

- nome e quantidade total;
- modelos incluídos;
- personalização com nome e idade;
- materiais utilizados;
- processo após a compra;
- alternativa sob medida para temas não encontrados.

Resumo visual recomendado:

- quantidade;
- número de modelos;
- material;
- personalização incluída.

Em seções expansíveis:

- composição;
- processo;
- produção;
- cuidados.

O exemplo registrado de 20 peças em quatro tipos de caixas, papel offset 180 g e personalização como a fotografia é uma referência de conteúdo, não uma regra para todos os kits.

### 7.5 Página de convite

Deve:

- usar “Comprar agora”;
- explicar que a customização ocorre após o pagamento;
- mostrar demonstração;
- informar recursos, prazo e preço;
- distinguir a família e o formato;
- indicar briefing, prévia e aprovação.

Não usar uma promessa universal de 24 horas: o prazo varia conforme complexidade.

### 7.6 Página de produto digital pronto

Deve:

- usar “Comprar agora”;
- informar claramente “sem customização”;
- diferenciar-se de convite digital personalizado;
- explicar entrega automática;
- apresentar formato do arquivo, condições de uso e compatibilidade somente depois que essas regras forem definidas.

Licenciamento, limites de download, validade do link e política de reembolso digital são **decisões futuras**.

### 7.7 Página de busca

Deve seguir o mockup aprovado:

- termo pesquisado;
- resultados exatos agrupados por categoria;
- seções completas para os tipos encontrados;
- sugestões semelhantes separadas no final;
- estado sem resultado com projeto exclusivo.

### 7.8 Página de projeto exclusivo

O caminho deve iniciar contato com contexto do tema ou produto procurado. O formulário definitivo não foi aprovado; a direção favorece chat ou WhatsApp, e pedidos inexistentes no catálogo exigem questionário detalhado após o enquadramento.

## 8. Carrinho

### 8.1 Objetivo

Consolidar itens e aplicar escolhas relevantes antes do checkout, sem sobrecarregar a página de produto.

### 8.2 Configuração de lembrancinhas

No carrinho, a cliente deve:

- escolher se deseja ou dispensa a personalização básica gratuita;
- escolher uma das opções de miniatura:
  - não quero miniatura;
  - já tenho o arquivo para enviar;
  - quero que a JS Designs crie para mim.

A criação da miniatura:

- possui valor fixo;
- deve mostrar o preço antes da inclusão;
- é cobrada uma única vez para a mesma miniatura;
- pode ser aplicada a vários produtos compatíveis do mesmo pedido;
- terá preço editável no painel administrativo, conforme proposta registrada e pendente apenas de definição do valor.

### 8.3 “Complete sua festa”

**Definido:**

- seção discreta antes do checkout;
- dois ou três complementos altamente relevantes;
- relação por mesmo tema e ocasião;
- adição simples ao carrinho;
- desconto de conjunto;
- desconto progressivo conforme a quantidade de complementos adicionados;
- aplicação sem depender de cupom, conforme mecanismo proposto na memória.

### 8.4 Regras de benefício

- o cupom de primeira compra não acumula com outros cupons ou promoções;
- o sistema aplica automaticamente o benefício mais vantajoso;
- desconto de conjunto é progressivo.

**Decisão futura:** interação exata entre desconto de conjunto e demais promoções, pois a memória especifica explicitamente a não acumulação do cupom de primeira compra, mas não todas as combinações possíveis.

### 8.5 Fluxo textual de miniatura

```text
Produto compatível no carrinho
→ cliente escolhe “não quero”, “já tenho” ou “criar para mim”

Se “já tenho”
→ upload será solicitado no briefing pós-pagamento
→ nenhum valor adicional

Se “criar para mim”
→ valor fixo é adicionado uma única vez
→ a mesma miniatura pode ser vinculada a vários itens compatíveis
→ dados e fotografias serão solicitados no briefing protegido
```

### 8.6 Critérios de aceite

- a cliente entende que a personalização básica da lembrancinha é gratuita;
- dispensar a personalização não gera cobrança nem briefing desnecessário;
- o serviço de miniatura não é cobrado uma vez por item compatível;
- o total e o desconto são recalculados antes de continuar;
- recomendações não excedem três itens e são contextuais;
- a melhor condição elegível é aplicada sem exigir tentativa manual de cupons.

## 9. Checkout

### 9.1 Estrutura

**Definido:** checkout em uma única página, sem cadastro prévio obrigatório, contendo:

- resumo recolhível do pedido;
- dados pessoais;
- dados de entrega, quando necessários;
- opção de NIF;
- pagamento;
- botão final **“Finalizar e comprar”**.

Chat e WhatsApp ficam ocultos durante o checkout.

### 9.2 Compra como visitante e criação de acesso

```text
Cliente entra no checkout sem conta
→ informa os dados necessários
→ finaliza a compra
→ após confirmação do pagamento, o sistema cria acesso seguro automaticamente
→ cliente recebe instruções para acessar a área da cliente
→ briefing, pedido, fatura, prévias e downloads ficam disponíveis conforme o produto
```

Forma de ativação do acesso, recuperação e verificação de identidade são **decisões futuras**.

### 9.3 Conteúdo de confiança

Usar a mensagem aprovada:

> Pagamentos protegidos e processados por parceiros certificados.

Não usar “Pagamentos 100% seguros”.

### 9.4 Critérios de aceite

- checkout ocorre em uma página;
- resumo pode ser expandido ou recolhido;
- nenhuma conta é exigida antes da compra;
- o CTA final contém exatamente “Finalizar e comprar”;
- suporte flutuante não aparece;
- NIF é opcional;
- dados de entrega não são exigidos para compra exclusivamente digital, sujeito à validação fiscal futura;
- preço, descontos, frete, impostos aplicáveis e total são visíveis antes da confirmação.

O último critério deriva da necessidade de clareza, mas a composição fiscal e monetária ainda precisa ser definida.

## 10. Pagamentos, frete e fiscalidade

### 10.1 Pagamentos

**Definido:**

- pagamentos instantâneos podem ser confirmados automaticamente;
- transferência bancária permanece em **Aguardando pagamento** até compensação;
- pedido por transferência fica reservado por 48 horas;
- após 48 horas sem confirmação, o pedido é cancelado automaticamente e a cliente é notificada;
- cartões devem ser processados somente por fornecedor certificado, com campos hospedados ou tokenização;
- a JS Designs nunca armazena número completo do cartão, CVV ou PIN.

### 10.2 Fluxo de transferência bancária

```text
Cliente escolhe transferência bancária
→ pedido criado em “Aguardando pagamento”
→ reserva iniciada por 48 horas
→ se pagamento for confirmado: “Pedido confirmado” e segue o fluxo do produto
→ se não for confirmado até o limite: cancelamento automático
→ cliente recebe notificação do cancelamento
```

### 10.3 Decisões futuras de pagamento

- métodos instantâneos específicos;
- fornecedor ou fornecedores;
- moedas aceitas;
- autenticação reforçada e regras antifraude do provedor;
- reembolsos, estornos e chargebacks;
- pagamentos parciais ou entrada de 50%;
- tratamento da entrada de 50% descrita no catálogo atual de convites;
- “pagamentos adicionais” previstos para fase posterior;
- conciliação financeira;
- cobrança e confirmação da taxa de urgência;
- tratamento de pedidos mistos físicos e digitais.

Não assumir que a entrada de 50% do catálogo atual continuará válida: a nova jornada parte de compra e pagamento antes do briefing, e a memória não resolveu a forma de pagamento parcial.

### 10.4 Frete

**Definido:**

- frete calculado quando a cliente informa o endereço;
- atendimento a destinos em toda a Europa;
- acompanhamento deve incluir envio e evolução da entrega.

**Decisões futuras:**

- transportadoras e agregadores;
- países e territórios efetivamente atendidos;
- modalidades, preços, pesos, dimensões e embalagens;
- prazo de transporte por destino;
- rastreamento;
- frete grátis ou promocional;
- restrições alfandegárias;
- impostos e taxas transfronteiriças;
- endereço de origem e regras para pedidos mistos;
- política para extravio, atraso e avaria.

### 10.5 Fiscalidade

A memória registra como premissa que cada venda deve gerar fatura em Portugal e que o NIF de consumidor particular é opcional, devendo ser incluído quando solicitado.

**Requisitos definidos:**

- opção **“Desejo incluir NIF na fatura”** no checkout;
- emissão de documento fiscal por venda;
- entrega automática da fatura por e-mail;
- disponibilidade da fatura na área da cliente.

**Decisões e validações futuras:**

- validar juridicamente e contabilmente a premissa antes do lançamento;
- formato, numeração e sistema de emissão;
- integração com solução fiscal;
- regras de IVA por natureza do produto e destino;
- OSS e vendas digitais transfronteiriças, se aplicáveis;
- notas de crédito, cancelamentos e reembolsos;
- dados obrigatórios da cliente e da vendedora;
- retenção legal de documentos;
- idioma e moeda da fatura.

## 11. Conta e área da cliente

### 11.1 Criação e acesso

- compra sem cadastro prévio;
- acesso seguro criado automaticamente após pagamento confirmado;
- área disponível por login e por links seguros enviados no canal escolhido, conforme o contexto;
- dados de autenticação protegidos;
- recuperação de acesso ainda a definir.

### 11.2 Módulos do núcleo

- pedidos atuais e anteriores;
- status e linha do tempo;
- prazo restante, data prevista e motivo de pausa;
- briefing com progresso e retomada;
- prévias numeradas;
- histórico de comentários e alterações;
- contador de alterações gratuitas;
- aprovação final;
- fotografia de verificação de qualidade;
- rastreamento do envio;
- faturas;
- downloads digitais;
- preferências de comunicação;
- dados pessoais e exercício de direitos RGPD.

### 11.3 Fase posterior

- calendário de celebrações;
- lembretes antes do próximo aniversário;
- recompra rápida;
- edição livre de idade, data, tema e demais dados;
- favoritos;
- logins sociais;
- mecanismos ampliados de fidelização.

### 11.4 Critérios de aceite

- a cliente consegue continuar o briefing sem perder dados;
- todo pedido mostra a próxima ação;
- quando o prazo estiver pausado, o motivo é explícito;
- versões e aprovações permanecem auditáveis;
- links de arquivos privados expiram e não expõem outros pedidos;
- produtos digitais e faturas ficam associados ao pedido correto.

## 12. Briefing, versões e aprovação

### 12.1 Disparo do briefing

Todo pedido com customização recebe briefing após confirmação do pagamento.

O briefing:

- aparece imediatamente na confirmação;
- permanece na área da cliente;
- pode ser acessado pelo link enviado no canal escolhido;
- deve ser preenchido em até três dias corridos;
- não provoca cancelamento automático se o prazo expirar;
- mantém o pedido em **Aguardando briefing** até a conclusão;
- pausa prazos de criação, produção e entrega enquanto faltarem dados.

### 12.2 Briefing inteligente

**Proposto na memória e coerente com os fluxos aprovados:** adaptar perguntas, uploads e instruções ao produto e ao nível de customização.

Tipos:

- **convite:** data, local, personagem, cores e demais informações do evento;
- **lembrancinha predefinida:** nome, idade, alterações desejadas e miniatura;
- **modelo inexistente/projeto exclusivo:** questionário de criação detalhado;
- **briefing mestre do evento:** coleta dados comuns uma única vez e os reutiliza em produtos compatíveis do mesmo pedido.

### 12.3 Salvamento e retomada

**Definido:**

- salvar cada resposta automaticamente;
- mostrar progresso;
- permitir interrupção e continuação sem perda;
- reutilizar dados comuns do evento.

### 12.4 Lembretes

Lembretes automáticos de briefing pelo canal escolhido foram propostos, mas não possuem aprovação explícita e “automações” foram priorizadas para logo após o núcleo. Portanto, são **fase posterior**, salvo se a implementação considerar um aviso transacional mínimo indispensável.

### 12.5 Prévias e alterações

**Definido:**

- até três rodadas de alterações gratuitas por arte personalizada;
- cada prévia é numerada;
- histórico de versões é preservado;
- cliente vê quantas alterações gratuitas restam;
- link seguro é enviado por WhatsApp ou e-mail, conforme escolha;
- aprovação, pedidos de alteração e histórico ficam registrados na área da cliente.

### 12.6 Aprovação final

Antes da aprovação, a cliente confirma ter revisado:

- nome;
- idade;
- data;
- horário;
- endereço;
- textos.

O sistema avisa que a produção será iniciada após a aprovação.

### 12.7 Fluxo textual completo

```text
Pagamento confirmado
→ acesso seguro criado
→ briefing disponibilizado e link enviado
→ pedido em “Aguardando briefing”
→ cliente preenche; respostas são salvas automaticamente
→ briefing completo inicia o prazo aplicável
→ arte entra em criação
→ prévia numerada é publicada
→ aviso enviado por WhatsApp ou e-mail

Se cliente pedir alteração
→ registra comentário
→ consome uma rodada gratuita
→ contador é atualizado
→ nova versão numerada é publicada

Se cliente aprovar
→ confirma revisão dos dados críticos
→ aprovação fica registrada
→ produto físico segue para produção
→ produto digital personalizado segue para finalização/entrega
```

### 12.8 Critérios de aceite

- briefing só é exigido para itens que precisam de dados/customização;
- o prazo não começa com briefing incompleto;
- dados comuns do evento não precisam ser repetidos por produto compatível;
- autosave evita perda ao sair ou fechar;
- cada versão possui número, data e vínculo com comentários;
- o sistema não permite aprovação final sem confirmação de revisão;
- saldo de alterações gratuitas é visível;
- regras e preço de alterações excedentes são **decisão futura**.

### 12.9 Decisões futuras

- definição exata do que constitui uma “rodada”;
- preço e cobrança depois das três rodadas;
- prazo de resposta da JS Designs e da cliente;
- formatos e limites de upload;
- aprovação por item, por arte ou por pedido;
- efeitos da aprovação em pedidos com vários produtos;
- possibilidade e custo de alterações após aprovação;
- expiração e reenvio de links.

## 13. Produção e acompanhamento

### 13.1 Linha do tempo

Estados definidos para encomendas personalizadas:

1. **Pedido confirmado**
2. **Aguardando briefing**
3. **Arte em criação**
4. **Aguardando aprovação**
5. **Em produção**
6. **Verificação de qualidade**
7. **Preparando para envio**
8. **Enviado**

Estados complementares necessários:

- **Aguardando pagamento** para transferência;
- **Cancelado** após expiração sem pagamento.

Estado de entrega concluída, falha de pagamento, reembolso, devolução e exceções de transporte são **decisões futuras**.

### 13.2 Prazo de produção física

**Definido:**

- sete dias corridos;
- começa somente depois do briefing completo;
- contador pausa sempre que a continuidade depender de resposta, alteração ou aprovação da cliente;
- área da cliente mostra data prevista, estado ativo ou pausado e motivo;
- estimativa deve evoluir conforme briefing, aprovação, produção e transporte.

Há uma tensão a resolver: “produção” é nomeada como sete dias após o briefing, mas a linha do tempo inclui criação e aprovação antes da produção. A memória também afirma que o contador pausa durante aprovação. A interpretação operacional exata — sete dias para criação mais confecção ou apenas confecção — é **decisão futura** e deve ser resolvida antes de comunicar a promessa.

### 13.3 Urgência

- fluxo normal mantém o prazo padrão;
- se a cliente precisar em menos de sete dias, ela procura suporte;
- página mostra nota discreta junto ao prazo;
- JS Designs confirma capacidade e valor;
- sistema não promete, calcula ou troca o CTA automaticamente.

### 13.4 Verificação de qualidade

- cada encomenda física finalizada é fotografada antes da embalagem;
- fotografia é disponibilizada na etapa **Verificação de qualidade**;
- evidência deve permanecer privada e associada ao pedido.

### 13.5 Fluxo textual de pedido físico

```text
Pedido confirmado
→ Aguardando briefing
→ briefing completo
→ contador de 7 dias corridos inicia, conforme regra operacional a confirmar
→ Arte em criação
→ Aguardando aprovação
→ se depende da cliente: contador pausado e motivo exibido
→ aprovação final
→ Em produção
→ Verificação de qualidade + fotografia privada
→ Preparando para envio
→ Enviado + dados de rastreamento
```

### 13.6 Critérios de aceite

- cada transição registra data, autor ou origem e motivo;
- cliente sempre vê status, prazo e próxima ação;
- pausa não reduz indevidamente o saldo de prazo;
- retomada recalcula a previsão;
- fotografia de qualidade não é pública;
- envio somente usa a arte final aprovada;
- ficha de produção referencia a versão aprovada correta.

## 14. Produtos digitais e convites

### 14.1 Separação obrigatória

| Tipo | Personalização | Briefing | Aprovação | Entrega |
|---|---|---|---|---|
| Arquivo digital pronto | Não | Não | Não | Automática após compra |
| Convite digital personalizado | Sim, após pagamento | Sim | Sim | Após criação e aprovação |

Essa diferença deve aparecer no cartão quando necessária, na página, no carrinho e na confirmação.

### 14.2 Famílias de convites

**Proposta de organização registrada, com prazos aprovados:**

- **Essenciais:** até 24 horas;
- **Interativos:** até 48 horas;
- **Infinito:** até 72 horas;
- **Cinemágicos:** entre três e cinco dias úteis.

Os oito formatos atuais — tradicional, dois tipos de animado, interativo, interativo plus, infinito, cinemágico e cinemágico interativo — devem ser preservados como opções dentro de famílias, mas o mapeamento final de cada formato é **decisão futura**.

### 14.3 Recursos registráveis

O catálogo atual possui combinações de:

- imagem ou vídeo;
- música;
- fotografia opcional;
- botões clicáveis;
- site de página longa;
- até oito fotos;
- contagem regressiva;
- personagem com narração personalizada;
- confirmação por formulário;
- botões extras;
- confirmação de presença com planilha;
- moldura para Instagram com QR Code;
- retrospectiva;
- arte para impressão;
- convite individual.

Nem todo recurso pertence a todo formato. A matriz definitiva é **decisão futura**.

### 14.4 Comparação

**Fase posterior ao núcleo:** tabela visual por formato com:

- formato de entrega;
- animação;
- música;
- botões;
- fotos;
- narração;
- prazo;
- preço;
- demonstração real.

### 14.5 Prazos digitais

Os prazos aprovados substituem uma promessa genérica de 24 horas. A contagem deve depender de briefing completo e pausas por ação da cliente, mas a memória não confirma se é em horas corridas ou úteis para Essenciais, Interativos e Infinito. Isso é **decisão futura**.

### 14.6 Entrega e comunicação

- produto digital pronto: entrega automática por e-mail após a compra;
- comunicações e links de prévia: WhatsApp ou e-mail, conforme preferência;
- a ideia de escolher WhatsApp ou e-mail também para receber o produto digital precisa ser reconciliada com a decisão de entrega automática por e-mail;
- arquivos e links devem ser privados, temporários quando apropriado e vinculados à compradora.

### 14.7 Decisões futuras

- formato de arquivo de cada produto;
- hospedagem de experiências interativas;
- duração da hospedagem;
- domínio e subdomínio;
- limites de alterações;
- licença de uso;
- marca d’água em prévias;
- prevenção de compartilhamento indevido;
- expiração e quantidade de downloads;
- políticas para músicas, personagens, imagens e outros direitos;
- regras de confirmação de presença e tratamento de dados dos convidados;
- contagem de prazo e fuso horário.

## 15. Suporte

### 15.1 Modelo definido

Dois botões flutuantes na lateral:

1. chat automático acima;
2. WhatsApp direto abaixo.

Os botões:

- são compactos, recolhidos e visualmente distintos;
- não competem com produtos nem CTAs;
- ficam ocultos durante o checkout.

### 15.2 Chat

**Fase posterior ao núcleo para automação completa:**

- começa sempre pelo robô;
- responde somente com conteúdo aprovado sobre produtos, customização, prazos, frete e encomendas;
- não inventa respostas;
- admite quando não sabe;
- se não resolver, permite deixar mensagem para atendimento posterior;
- pode encaminhar ao WhatsApp com resumo e contexto;
- quando autenticada, a cliente pode consultar com segurança status, prazo restante e próxima ação.

### 15.3 WhatsApp

Usos:

- suporte direto;
- escalonamento do chat;
- consulta de disponibilidade e taxa de urgência;
- projeto exclusivo;
- recebimento de avisos ou links quando escolhido.

Ao partir de um produto, a conversa deve incluir nome e link do produto; a inclusão automática do contexto foi proposta e deve ser validada na especificação do suporte.

### 15.4 Mensagem assíncrona

Quando o chat não resolver, a cliente deixa uma mensagem para resposta posterior. Horário, disponibilidade, tempo estimado e canal de resposta foram propostos, mas não aprovados de forma definitiva.

### 15.5 Critérios de aceite

- nenhum botão de suporte aparece no checkout;
- robô só utiliza base aprovada;
- resposta incerta é escalada, não inventada;
- consulta de pedido exige autenticação e autorização;
- o contexto de uma cliente nunca revela dados de outra;
- WhatsApp permanece opcional para a jornada normal.

### 15.6 Decisões futuras

- ferramenta de chat;
- horário e SLA;
- equipe e fila de atendimento;
- política de gravação e retenção;
- consentimento e aviso de privacidade;
- idiomas;
- critérios de escalonamento;
- autenticação adicional para informações sensíveis;
- conteúdo aprovado e governança da base de respostas.

## 16. Administração e operação interna

### 16.1 Núcleo obrigatório

#### Gestão de pedidos

- visualizar pedido, pagamento, cliente, canal preferido e itens;
- gerir estados e exceções;
- ver prazo ativo/pausado e motivo;
- registrar ações em auditoria.

#### Briefings

- visualizar completude e respostas;
- identificar dados comuns do evento;
- solicitar complementos;
- tratar uploads protegidos.

#### Artes, versões e aprovação

- publicar prévias numeradas;
- receber comentários;
- controlar três rodadas gratuitas;
- identificar versão aprovada;
- bloquear uso acidental de versão não aprovada.

#### Fila visual de produção

Cartões por pedido contendo:

- prazo restante;
- prioridade;
- arte aprovada;
- materiais;
- próxima ação;
- estado de produção.

#### Ficha automática de produção

Gerada por encomenda com:

- versão aprovada;
- itens;
- quantidades;
- materiais;
- acabamento;
- prazo;
- endereço;
- checklist de qualidade.

#### Qualidade e envio

- checklist;
- upload da fotografia da encomenda finalizada;
- preparação para envio;
- registro de rastreamento.

#### Catálogo e comércio

- produtos, categorias, variantes e preços;
- quantidade dos kits;
- compatibilidade de miniatura;
- valor fixo da criação de miniatura;
- relações entre produtos;
- descontos progressivos;
- promoções e cupom de primeira compra.

#### Fiscalidade

- emissão e associação de faturas;
- NIF quando solicitado;
- disponibilização por e-mail e conta;
- correções e cancelamentos, após regras fiscais serem definidas.

#### Segurança e acesso

- funções e permissões;
- privilégio mínimo;
- MFA obrigatório para contas administrativas;
- auditoria de acesso e alterações.

### 16.2 Estados e prioridades

O quadro possui fila visual e estados de produção, mas a forma de prioridade — manual, por prazo, por urgência confirmada ou por outro critério — é **decisão futura**.

### 16.3 Relatórios

Relatórios avançados foram adiados. O lançamento ainda precisa dos registros operacionais e fiscais mínimos, mas métricas, dashboards e exportações são **decisões futuras**.

### 16.4 Critérios de aceite

- somente pessoas autorizadas acessam dados, arquivos e ações correspondentes à função;
- toda mudança sensível fica registrada;
- ficha de produção usa dados imutáveis da versão aprovada;
- preço da miniatura pode ser administrado sem alteração de código;
- fotografia de qualidade é vinculada ao pedido correto;
- alterações de status podem acionar notificações transacionais, conforme matriz a definir;
- MFA é obrigatório para toda conta administrativa.

## 17. Marketing, aquisição e recompra

### 17.1 Instagram

Canal principal atual de descoberta e pedidos de orçamento.

Direção:

- publicações e stories devem levar diretamente ao produto ou tema correspondente;
- links devem encurtar o caminho até a compra;
- promoções e preços competitivos podem impulsionar visitas;
- topos de bolo merecem prioridade inicial por evidência de recompra.

### 17.2 Primeira compra

**Definido:**

- 10% de desconto;
- em troca do cadastro do e-mail;
- sem valor mínimo;
- uso na primeira compra;
- não acumula com outros cupons ou promoções;
- sistema aplica automaticamente o benefício mais vantajoso.

**Comportamento proposto:**

- mostrar após algum envolvimento ou atraso;
- apenas uma vez por visitante;
- fechamento fácil;
- nunca no checkout;
- não exibir a clientes cadastrados ou compradores;
- enviar cupom individual por e-mail.

Detalhes de elegibilidade, expiração, antifraude e identificação da primeira compra são **decisões futuras**.

### 17.3 Promoções

- descontos progressivos por quantidade;
- promoções pontuais em datas especiais;
- evitar descontos permanentes;
- comunicar urgência apenas quando houver prazo real;
- newsletter com promoções exclusivas de packs e descontos selecionados.

### 17.4 Carrinho abandonado e orçamento

**Logo após o núcleo:**

- orçamento recuperável com produto, quantidade, opções e preço salvos;
- link que retoma a configuração;
- lembrete automático;
- recuperação de carrinho com promoção, oportunidade ou oferta real.

Consentimento, frequência, prazo, canais e regras de descadastro são **decisões futuras**.

### 17.5 Avaliações

**Fase posterior ao núcleo:**

- avaliações curtas e espontâneas;
- fotografias reais;
- foco no resultado visual;
- nenhuma recompensa por avaliação com fotografia.

Consentimento para publicar imagens, sobretudo quando houver crianças, é obrigatório e separado.

### 17.6 Recompra e fidelização

Princípios:

- qualidade consistente;
- atendimento rápido;
- peças que atendam às expectativas;
- preço justo.

**Fase posterior:**

- calendário de celebrações;
- lembretes;
- repetir compra;
- editar livremente idade, data, tema e demais informações;
- programa ampliado de fidelização.

### 17.7 SEO

SEO foi priorizado para logo após o núcleo. Estrutura, conteúdo, dados estruturados, estratégia de palavras-chave e governança são **decisões futuras**.

## 18. Internacionalização

### 18.1 Escopo definido

- comunicação e documentos do projeto em português do Brasil;
- frete para destinos em toda a Europa;
- núcleo internacional faz parte do lançamento;
- francês e espanhol ficam para fase posterior.

### 18.2 Requisitos derivados

O sistema deve estar preparado para:

- endereços de diferentes países europeus;
- formatos de NIF ou identificadores fiscais aplicáveis, sem presumir que todos seguem o mesmo padrão;
- cálculo de frete por destino;
- datas, horários e fusos;
- textos e conteúdos traduzíveis no futuro;
- regras fiscais por destino e natureza física/digital.

### 18.3 Decisões futuras

- país de operação principal exibido;
- variante linguística da interface de produção;
- português do Brasil versus português europeu para a loja;
- moeda ou moedas;
- conversão cambial;
- países excluídos;
- idiomas de atendimento;
- francês e espanhol: ordem e cobertura;
- IVA, OSS, alfândega e faturação transfronteiriça;
- formatos de telefone e endereço;
- política para prazo internacional;
- acessibilidade de conteúdos traduzidos.

Não assumir suporte multilíngue no lançamento além do português definido.

## 19. Dados e integrações

### 19.1 Entidades centrais

Modelo conceitual derivado:

```text
Cliente
├── Preferências de comunicação
├── Endereços
├── Dados fiscais opcionais
└── Celebrações [fase posterior]

Produto
├── Categoria e natureza
├── Modelos/variantes
├── Tema/personagem/ocasião
├── Personalização
├── Materiais e recursos
└── Complementos relacionados

Pedido
├── Itens e configurações
├── Benefícios e totais
├── Pagamento
├── Fatura
├── Briefing do evento
├── Artes e versões
├── Aprovação
├── Produção
├── Qualidade
├── Envio
└── Notificações
```

### 19.2 Integrações necessárias ou previstas

| Integração | Finalidade | Maturidade |
|---|---|---|
| Provedor de pagamentos certificado | pagamentos tokenizados e confirmação | Obrigatória; fornecedor futuro |
| Transferência/conciliação | confirmar compensação em até 48 h | Necessária; mecanismo futuro |
| Sistema fiscal | emitir e entregar faturas | Obrigatória; fornecedor futuro |
| Frete/transportadora | cotação e rastreamento europeu | Obrigatória; fornecedor futuro |
| E-mail transacional | acesso, fatura, links, downloads, avisos | Obrigatória |
| WhatsApp | suporte, escalonamento e avisos escolhidos | Definida; integração futura |
| Chat | autoatendimento e mensagem assíncrona | Após o núcleo |
| Armazenamento privado | briefings, prévias, fotos e downloads | Obrigatória |
| Antimalware | verificar uploads | Obrigatória |
| Instagram/deep links | aquisição direta para produtos/temas | Estratégia definida |
| Newsletter/automação | cupom, promoções e recuperação | Após o núcleo |
| Analytics/SEO | medição e descoberta orgânica | Não especificada; decisão futura |

### 19.3 Eventos de domínio mínimos

**Proposta técnica derivada dos fluxos:**

- pagamento confirmado;
- reserva de transferência iniciada;
- transferência expirada;
- acesso criado;
- briefing disponibilizado;
- briefing salvo;
- briefing concluído;
- prazo iniciado, pausado e retomado;
- prévia publicada;
- alteração solicitada;
- arte aprovada;
- produção iniciada;
- verificação de qualidade concluída;
- pedido preparado;
- pedido enviado;
- arquivo digital liberado;
- fatura emitida.

A tecnologia de eventos, filas e reprocessamento é **decisão futura**.

### 19.4 Qualidade e governança de dados

- um identificador único deve conectar pedido, arte aprovada, ficha de produção, fotografia de qualidade, envio e fatura;
- dados comuns do evento devem ser reutilizados sem duplicação desnecessária;
- alterações sensíveis devem manter trilha de auditoria;
- uploads devem guardar dono, finalidade, estado de análise, retenção e consentimento quando aplicável;
- dados de busca precisam de curadoria de personagens, temas e grafias.

## 20. Segurança, privacidade e RGPD

### 20.1 Referenciais obrigatórios

**Definido para o lançamento e como critério de aceite:**

- OWASP ASVS nível 2;
- RGPD por design;
- PCI DSS no tratamento de pagamentos por parceiro certificado.

### 20.2 Pagamentos

- usar campos hospedados ou tokenização;
- nunca armazenar número completo, CVV ou PIN;
- comunicar “Pagamentos protegidos e processados por parceiros certificados”;
- avaliar o escopo PCI resultante da integração escolhida.

### 20.3 Proteções da aplicação e infraestrutura

- HTTPS/TLS;
- HSTS;
- encriptação em repouso;
- gestão segura de segredos;
- cookies seguros;
- proteção CSRF;
- Content Security Policy;
- WAF;
- limitação de tentativas;
- proteção contra bots;
- palavras-passe com Argon2id;
- MFA para todas as contas administrativas;
- controle de acesso por função;
- privilégio mínimo.

Parâmetros, algoritmos auxiliares, provedores e configuração operacional são **decisões técnicas futuras**, preservando esses requisitos.

### 20.4 Auditoria

Registrar acessos e alterações relacionados a:

- encomendas;
- arquivos;
- dados fiscais;
- produção;
- versões e aprovações;
- ações administrativas.

Retenção, imutabilidade, monitoramento e alertas são **decisões futuras**.

### 20.5 Uploads

Aplicar:

- lista de tipos permitidos;
- limites de tamanho;
- validação de assinatura real do arquivo;
- renomeação aleatória;
- análise antimalware;
- armazenamento privado;
- links temporários assinados.

Arquivos não devem se tornar públicos por URL previsível.

### 20.6 Fotografias de crianças e miniaturas

- nunca públicas por padrão;
- nunca usadas em marketing sem consentimento separado;
- eliminação por padrão após cumprir a finalidade;
- conservação opcional somente com autorização para facilitar recompra;
- acesso limitado às pessoas e processos necessários.

O prazo específico de eliminação e o mecanismo de consentimento são **decisões futuras**.

### 20.7 Direitos e minimização

- coletar apenas dados necessários;
- definir prazos de retenção;
- permitir acesso, correção e eliminação;
- documentar finalidades e bases legais;
- separar consentimento de marketing do processamento necessário ao pedido;
- oferecer revogação quando o tratamento depender de consentimento.

Base legal por finalidade, encarregado, registros de tratamento, política de cookies e processo de solicitações são **decisões futuras**.

### 20.8 Continuidade e incidentes

- backups encriptados;
- restauração testada regularmente;
- plano e testes de resposta a incidentes;
- gestão segura de segredos e rotação quando necessário.

RPO, RTO, frequência de backup e plano de comunicação são **decisões futuras**.

### 20.9 Verificação antes do lançamento

- revisão de código;
- análise de dependências;
- testes automatizados de segurança;
- varredura de vulnerabilidades;
- teste de intrusão;
- repetição após mudanças relevantes.

### 20.10 Critérios de aceite de segurança

- nenhuma informação completa de cartão é persistida pela JS Designs;
- toda rota administrativa exige MFA;
- cliente só acessa seus próprios pedidos e arquivos;
- links privados expiram e são assinados;
- arquivo inválido ou malicioso é bloqueado antes do uso;
- fotografias de crianças não entram em áreas públicas;
- logs registram ações sensíveis sem armazenar segredos ou dados de cartão;
- restauração de backup foi testada;
- requisitos aplicáveis do OWASP ASVS nível 2 foram verificados;
- teste de intrusão não possui vulnerabilidade crítica ou alta sem tratamento aceito antes do lançamento.

## 21. Requisitos não funcionais

### 21.1 Usabilidade

- navegação intuitiva;
- busca fácil de encontrar;
- cabeçalho simples;
- checkout em uma página;
- conteúdo progressivo;
- estado, prazo e próxima ação claros;
- formulários recuperáveis e com salvamento automático;
- ausência de distrações no checkout.

### 21.2 Desempenho

Fotografias grandes, vídeos e animações não podem comprometer a experiência. Orçamentos numéricos de desempenho não foram definidos.

**Decisões futuras:**

- metas de Core Web Vitals;
- limites de peso por página;
- formatos e transformação de mídia;
- estratégia de cache e CDN;
- desempenho da busca e do checkout.

### 21.3 Disponibilidade e confiabilidade

Fluxos críticos:

- pagamento;
- confirmação;
- criação de acesso;
- briefing;
- aprovação;
- downloads;
- emissão fiscal;
- acompanhamento.

Devem tolerar reprocessamento sem duplicar cobrança, fatura, benefício ou serviço de miniatura.

SLA, SLO, redundância, filas, idempotência e recuperação são **decisões futuras**.

### 21.4 Acessibilidade

A memória não define padrão. Nível de conformidade, testes, legendas, contraste, teclado, leitores de tela e textos alternativos são **decisões futuras obrigatórias para especificação**, especialmente pela direção premium e intuitiva.

### 21.5 Escalabilidade

O objetivo exige solução escalável, principalmente para:

- expansão do catálogo;
- pedidos simultâneos;
- armazenamento de mídias;
- busca por metadados;
- notificações;
- internacionalização;
- novas formas de pagamento.

Metas de volume e arquitetura são **decisões futuras**.

### 21.6 Observabilidade

Logs de auditoria são obrigatórios. Métricas, rastreamento, alertas, dashboards, retenção e dados pessoais em logs são **decisões futuras**.

### 21.7 Compatibilidade

Dispositivos, navegadores, tamanhos de tela e requisitos de conexão não foram especificados. Devem ser definidos antes dos testes.

### 21.8 Manutenibilidade

- preços, promoções e valor da miniatura devem ser configuráveis;
- conteúdo aprovado do chat precisa de governança;
- taxonomias e metadados de busca precisam ser editáveis;
- mudanças relevantes exigem nova verificação de segurança.

Tecnologias, ambientes, CI/CD e estratégia de testes são **decisões futuras**.

## 22. Notificações e canais

### 22.1 Preferência da cliente

Após o pagamento, a cliente escolhe WhatsApp ou e-mail para receber prévias e comunicações pós-compra.

### 22.2 Matriz mínima a especificar

| Evento | Área da cliente | E-mail | WhatsApp |
|---|---:|---:|---:|
| Pagamento confirmado | Sim | A definir | A definir |
| Briefing disponível | Sim | Conforme preferência/link | Conforme preferência/link |
| Prévia publicada | Sim | Conforme preferência | Conforme preferência |
| Alteração registrada | Sim | A definir | A definir |
| Aprovação concluída | Sim | A definir | A definir |
| Qualidade concluída | Sim, com foto | A definir | A definir |
| Pedido enviado | Sim | A definir | A definir |
| Produto digital pronto | Sim/a definir | Entrega automática | Divergência a resolver |
| Fatura | Sim | Sim | Não definido |
| Transferência expirada | Sim | Notificação necessária | A definir |

Frequência, conteúdo, opt-in, opt-out, mensagens obrigatórias e provedor são **decisões futuras**.

## 23. Fluxos ponta a ponta

### 23.1 Produto físico personalizado

```text
Descoberta/busca
→ página do produto
→ escolha de quantidade
→ carrinho
→ ativa ou dispensa personalização
→ escolhe opção de miniatura
→ adiciona complementos opcionais
→ checkout em uma página
→ pagamento confirmado
→ acesso automático + briefing
→ briefing completo
→ arte em criação
→ prévias/até 3 alterações gratuitas
→ confirmação de revisão + aprovação
→ produção
→ fotografia de qualidade
→ preparação
→ envio e acompanhamento
```

### 23.2 Produto digital pronto

```text
Descoberta
→ página informa “sem customização”
→ Comprar agora
→ checkout
→ pagamento confirmado
→ entrega automática por e-mail
→ download associado ao pedido/conta conforme regra futura
```

### 23.3 Convite digital personalizado

```text
Descoberta
→ demonstração, recursos, família, formato, preço e prazo
→ Comprar agora
→ checkout
→ pagamento confirmado
→ briefing do evento
→ criação
→ prévia numerada
→ alteração ou aprovação
→ entrega final no prazo da família, sujeito às regras de contagem
```

### 23.4 Projeto exclusivo

```text
Busca sem resultado ou chamada discreta
→ chat/WhatsApp com contexto
→ JS Designs confirma escopo e viabilidade
→ questionário detalhado
→ orçamento/configuração recuperável [fase após o núcleo]
→ compra
→ briefing, criação e aprovação conforme o produto
```

### 23.5 Recompra

```text
Cliente abre pedido anterior
→ escolhe repetir compra
→ edita livremente idade, data, tema e demais dados
→ revisa disponibilidade, preço e prazo atuais
→ compra novamente
```

**Prioridade:** fase posterior.

## 24. Prioridades de lançamento

### 24.1 MUST — lançamento

Jornada completa e núcleo aprovado:

- marca e UX premium com divulgação progressiva;
- catálogo direto de produtos e modelos;
- categorias físicas e digitais essenciais;
- busca exata por personagem/tema com resultados agrupados;
- páginas de produto e kits;
- carrinho com personalização, miniatura e complementos;
- descontos de conjunto e regra de melhor benefício;
- checkout em página única;
- compra sem cadastro e acesso automático pós-pagamento;
- pagamentos do núcleo e reserva de transferência por 48 horas;
- frete para a Europa;
- NIF opcional e emissão/entrega fiscal;
- briefing pós-pagamento, autosave e briefing único do evento;
- prévias numeradas, três alterações gratuitas e aprovação;
- linha do tempo, prazo, pausas e próxima ação;
- fila visual administrativa e ficha de produção;
- fotografia de verificação de qualidade;
- entrega de produtos digitais prontos;
- convites personalizados com prazos por complexidade;
- suporte essencial por WhatsApp e estrutura para chat, respeitando a fase da automação;
- internacionalização estrutural do núcleo;
- segurança, privacidade, uploads, RGPD, PCI e testes obrigatórios.

### 24.2 SHOULD — logo após o núcleo

Conforme priorização aprovada:

- comparação avançada de convites;
- pagamentos adicionais;
- busca preditiva e tolerante a erros;
- favoritos;
- avaliações;
- suporte automático;
- recuperação de orçamentos;
- SEO;
- automações;
- lembretes e recuperação de carrinho/orçamento;
- aprofundamento das campanhas e newsletter.

### 24.3 COULD — fase posterior

- calendário de celebrações;
- recompra rápida e lembretes de aniversário;
- fidelização ampliada;
- cartões-presente;
- recomendações avançadas;
- logins sociais;
- blog completo;
- francês;
- espanhol;
- pré-visualização automática;
- relatórios avançados.

### 24.4 WON’T — fora da direção atual

- consultoria guiada por orçamento;
- vários CTAs por modalidade;
- cartões de personagens como caminho principal na página inicial;
- suporte flutuante durante o checkout;
- urgência calculada ou prometida automaticamente;
- troca automática do CTA por urgência;
- recompensa por avaliações;
- uso da identidade visual das referências externas;
- dependência de WhatsApp para compra, briefing, aprovação ou acompanhamento normal.

## 25. Fora de escopo e limites

Além dos itens explicitamente excluídos na priorização:

- este blueprint não escolhe plataforma, linguagem, framework, hospedagem ou fornecedores;
- não define política jurídica, fiscal ou contábil sem validação especializada;
- não define valores de produtos, miniatura, frete, urgência ou descontos progressivos;
- não cria matriz final de recursos dos convites;
- não resolve propriedade intelectual de personagens, músicas, imagens e fontes;
- não define SLAs, volumes, métricas ou metas numéricas ausentes;
- não presume idiomas, moedas ou países além do registrado;
- não adiciona funcionalidades de marketplace, múltiplos vendedores ou orçamento automatizado.

## 26. Registro consolidado de decisões futuras

### 26.1 Comercial e catálogo

- nomenclatura final dos tipos de personalização;
- regras de estoque e disponibilidade;
- valores e degraus dos descontos progressivos;
- preço da criação de miniatura;
- cobrança após três alterações;
- regras de urgência e taxa;
- mapeamento dos oito formatos de convite nas famílias;
- matriz de recursos e adicionais;
- política de entrada de 50%.

### 26.2 Jornada e conteúdo

- navegação final;
- ordem definitiva da página inicial;
- filtros e ordenação;
- políticas de cancelamento, devolução e reembolso;
- licenças e downloads digitais;
- contagem exata dos prazos;
- significado operacional dos sete dias corridos;
- matriz final de notificações.

### 26.3 Operação

- regras de prioridade na produção;
- tratamento de exceções e retrabalho;
- SLA de suporte;
- política de alterações após aprovação;
- estados de entrega, reembolso e devolução;
- retenção de arquivos e fotografias.

### 26.4 Técnica e integrações

- arquitetura e stack;
- provedores de pagamento, fiscal, frete, e-mail, WhatsApp, chat e armazenamento;
- idempotência e reprocessamento;
- estratégia de observabilidade;
- metas de desempenho, disponibilidade e escalabilidade;
- suporte de navegadores e dispositivos;
- padrão de acessibilidade.

### 26.5 Internacional e legal

- moeda;
- variante linguística da loja;
- países atendidos e exceções;
- IVA, OSS e alfândega;
- validação do processo fiscal;
- propriedade intelectual;
- bases legais, consentimentos e retenção RGPD;
- política de cookies e marketing.

## 27. Critérios globais de sucesso do blueprint

O produto implementado estará alinhado à memória quando:

1. uma cliente encontrar um produto por personagem sem conhecer a estrutura do catálogo;
2. resultados exatos e semelhantes forem claramente separados;
3. preço, prazo e personalização forem compreensíveis sem excesso de informação;
4. a compra puder ser concluída sem criar conta previamente;
5. o checkout não tiver distrações nem suporte flutuante;
6. o briefing for recuperável, contextual e reutilizar dados do evento;
7. versões, alterações e aprovação estiverem registradas;
8. o prazo pausar e retomar com motivo transparente;
9. a produção utilizar a arte aprovada e gerar ficha operacional;
10. cada encomenda física tiver evidência privada de qualidade antes do envio;
11. produto digital pronto e convite personalizado seguirem fluxos inequivocamente diferentes;
12. a cliente acompanhar tudo na conta, usando WhatsApp apenas quando quiser ou precisar;
13. o painel proteger dados por função, MFA e auditoria;
14. pagamentos e uploads não expuserem dados sensíveis;
15. fotografias de crianças permanecerem privadas e forem eliminadas ou conservadas conforme consentimento;
16. requisitos de segurança e RGPD forem verificados antes do lançamento;
17. qualquer lacuna listada neste documento for decidida explicitamente, em vez de ser preenchida por suposição de implementação.
