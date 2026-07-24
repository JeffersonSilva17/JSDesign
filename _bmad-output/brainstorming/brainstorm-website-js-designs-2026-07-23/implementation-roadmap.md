# Roadmap de implementação — website JS Designs

## 1. Objetivo e regra de escopo

Este roadmap transforma as decisões da sessão de brainstorming em uma sequência de implementação orientada por gates. A fonte única deste documento é o arquivo `.memlog.md` da sessão.

O objetivo do produto é lançar uma loja premium, intuitiva, segura e escalável em que a cliente consiga descobrir produtos, escolher, comprar, fornecer os dados de personalização, aprovar a arte e acompanhar a encomenda sem depender do WhatsApp. O WhatsApp permanece como canal de suporte e de projetos totalmente exclusivos.

Não há estimativas de prazo, custo ou equipe porque a fonte não oferece base para isso. O avanço deve acontecer por critérios de saída verificáveis.

### Legenda de prioridade

- **Obrigatório — lançamento:** necessário para disponibilizar a jornada principal completa e operável.
- **Importante — logo após o núcleo:** aprovado para a evolução imediata, mas não bloqueia o primeiro lançamento.
- **Futuro:** adiado explicitamente para uma fase posterior.
- **Excluído:** rejeitado ou mantido fora da direção do produto.
- **Lacuna:** decisão necessária que não foi resolvida na sessão; não deve ser preenchida por suposição.

## 2. Princípios que governam a implementação

1. **Praticidade primeiro:** escolher e finalizar a compra deve ser simples.
2. **Divulgação progressiva:** cada etapa mostra apenas a informação necessária naquele momento.
3. **Premium pela clareza:** elegância, confiança, acabamento visível e acompanhamento transparente têm prioridade sobre excesso visual ou funcional.
4. **Complexidade absorvida pela operação:** briefing inteligente, aprovações, automações, fila de produção e notificações devem simplificar a experiência da cliente.
5. **Jornada completa no lançamento:** catálogo sem pós-compra operacional não atende ao objetivo.
6. **Segurança e privacidade por design:** dados pessoais, fiscais, pagamentos, fotografias e arquivos de crianças são requisitos de lançamento, não melhorias posteriores.
7. **Sem promessas não sustentadas:** urgência depende de confirmação humana; pagamentos devem ser descritos como “Pagamentos protegidos e processados por parceiros certificados”.
8. **Português do Brasil e UTF-8:** toda a interface, conteúdo e comunicação devem preservar acentuação correta.

## 3. Workstreams

| Código | Workstream | Resultado esperado | Prioridade |
|---|---|---|---|
| W1 | Produto, regras e operação | Regras comerciais, estados, exceções e responsabilidades operacionais aprovados | Obrigatório |
| W2 | Marca, conteúdo e UX | Experiência minimalista, sofisticada, intuitiva e coerente em toda a jornada | Obrigatório |
| W3 | Catálogo, busca e dados | Catálogo unificado, pesquisável por personagem/tema e preparado para migração | Obrigatório |
| W4 | Carrinho, checkout e benefícios | Compra em página única, sem cadastro obrigatório e com regras comerciais corretas | Obrigatório |
| W5 | Pagamentos, tributação e frete | Pagamento, transferência, fatura com NIF e frete europeu integrados | Obrigatório |
| W6 | Personalização, briefing e aprovação | Coleta de dados, prévias, alterações e aprovação final rastreáveis | Obrigatório |
| W7 | Pedidos, produção e acompanhamento | Linha do tempo da encomenda e operação interna conectadas | Obrigatório |
| W8 | Conta, arquivos e comunicações | Acesso pós-compra, arquivos privados, notificações e entrega digital | Obrigatório |
| W9 | Administração | Gestão de catálogo, pedidos, valores editáveis, produção e auditoria | Obrigatório |
| W10 | Segurança, privacidade e RGPD | Controles técnicos e operacionais aprovados antes do lançamento | Obrigatório |
| W11 | Testes, migração e lançamento | Catálogo validado, jornadas testadas e operação pronta | Obrigatório |
| W12 | Crescimento, suporte e automações | Conversão, recuperação, conteúdo comparativo e suporte automático | Importante depois |

## 4. Ordem recomendada e dependências principais

```text
Fundamentos e decisões pendentes
    ↓
Modelo de catálogo + estados do pedido + regras comerciais
    ↓
UX base + arquitetura das integrações + modelo de segurança
    ↓
Catálogo e produto ─────→ carrinho e checkout ─────→ pagamento/fatura/frete
    │                                                    ↓
    └──────────────→ briefing e aprovação ─────→ produção e acompanhamento
                                                         ↓
Conta, comunicações, arquivos privados e administração
                                                         ↓
Migração final + testes ponta a ponta + segurança + prontidão operacional
                                                         ↓
Lançamento
                                                         ↓
Funcionalidades importantes após o núcleo
                                                         ↓
Evolução futura
```

Dependências que não podem ser invertidas:

- A modelagem do catálogo precede migração, busca, produto, recomendações e comparação de convites.
- As regras de modalidade de produto precedem carrinho, briefing, entrega e estados do pedido.
- A máquina de estados do pedido precede notificações, conta da cliente, fila administrativa e cálculo de prazo.
- A seleção dos fornecedores de pagamento, faturamento, frete, e-mail e armazenamento precede a conclusão das respectivas integrações.
- O briefing e a aprovação devem estar funcionais antes de ligar o prazo de produção.
- A política de retenção e os controles de upload devem existir antes de aceitar fotografias e miniaturas reais.
- A administração deve estar pronta antes da carga final do catálogo e antes de pedidos reais.
- Migração, segurança, restauração de backup e testes ponta a ponta são gates de lançamento.

## 5. Fase 0 — fundamentos e descoberta

**Prioridade:** obrigatório antes da construção do núcleo.

### 5.1 Objetivos

- Converter decisões do brainstorming em regras implementáveis.
- Fechar lacunas que bloqueiam arquitetura, integrações, operação ou aceite.
- Definir os modelos de dados e estados que sustentam toda a jornada.
- Preparar inventário, limpeza e migração dos dois catálogos existentes.
- Validar o fluxo operacional com a JS Designs antes de automatizá-lo.

### 5.2 Entregas por workstream

#### W1 — produto, regras e operação

- Mapear as modalidades:
  - arquivo digital pronto, sem customização e com entrega automática;
  - convite digital personalizado;
  - lembrancinha com personalização básica incluída;
  - kit de lembrancinhas;
  - topo de bolo em modelo pronto;
  - topo de bolo com opção separada de customização;
  - projeto exclusivo para modelo ou tema ausente do catálogo.
- Formalizar as famílias de convites digitais:
  - Essenciais;
  - Interativos;
  - Infinito;
  - Cinemágicos.
- Preservar os oito formatos existentes como opções dentro das famílias aplicáveis.
- Registrar os prazos aprovados:
  - Essenciais: até 24 horas;
  - Interativos: até 48 horas;
  - Infinito: até 72 horas;
  - Cinemágicos: de três a cinco dias úteis;
  - produção física: sete dias corridos após briefing completo.
- Definir a transição operacional entre:
  - Pedido confirmado;
  - Aguardando pagamento, quando aplicável;
  - Aguardando briefing;
  - Arte em criação;
  - Aguardando aprovação;
  - Em produção;
  - Verificação de qualidade;
  - Preparando para envio;
  - Enviado.
- Incorporar estados finais e de exceção necessários à operação, sem inventar regras comerciais. **Lacuna:** a sessão apenas definiu explicitamente o cancelamento automático de transferência não confirmada; os demais estados de cancelamento, reembolso, falha de pagamento, devolução e disputa precisam de decisão.
- Documentar quando o contador de produção fica ativo ou pausado e qual ação da cliente o reativa.
- Definir como as três rodadas gratuitas são consumidas. **Lacuna:** a sessão não define se uma rodada pode conter várias observações nem o preço e o fluxo da quarta alteração.
- Formalizar o checklist de revisão antes da aprovação final: nome, idade, data, horário, endereço e textos, conforme aplicável.
- Definir a operação para fotografar toda encomenda física finalizada antes da embalagem e anexar a evidência na verificação de qualidade.

#### W2 — marca, conteúdo e UX

- Criar o sistema visual a partir de:
  - branco;
  - bege muito claro;
  - dourado champanhe;
  - preto suave;
  - taupe claro;
  - espaço em branco abundante;
  - fotografias grandes;
  - tipografia de alta qualidade;
  - animações suaves.
- Tratar as referências externas e imagens recebidas como referência de lógica comercial, organização e navegação, sem copiar identidade visual.
- Definir o cabeçalho simplificado, com categorias reunidas em um menu “Loja” e pesquisa fácil de encontrar.
- Definir padrões de cartões enxutos com fotografia, nome, preço, selo discreto quando aplicável e chamada principal aprovada.
- Criar a hierarquia de conteúdo das páginas de produto com resumo visual e seções expansíveis.
- Incorporar as quatro promessas iniciais:
  - variedade para cada celebração;
  - qualidade artesanal;
  - personalização simples;
  - apoio humano disponível.
- Planejar “Qualidade que você pode ver” e “Detalhes que fazem a diferença” com fotografias macro e vídeos curtos de laços, pedras, embalagem, cortes, dobras, encaixes, impressão, montagem e recortes de topos de bolo.
- Usar “Comprar agora” na maioria dos produtos, “Comprar já” nas lembrancinhas e “Finalizar e comprar” no checkout.

#### W3 — catálogo, busca e dados

- Inventariar separadamente:
  - catálogo de convites e produtos digitais;
  - catálogo de papelaria personalizada.
- Criar taxonomia unificada por categoria, tipo de produto, família, formato, tema, personagem e ocasião.
- Definir metadados de busca para personagens, temas e variações de escrita, mesmo quando não estiverem no título.
- Definir compatibilidades entre itens do mesmo tema para a seção “Complete sua festa”.
- Preparar o modelo de kits com:
  - nome;
  - quantidade total;
  - modelos incluídos;
  - materiais;
  - personalização incluída;
  - processo pós-compra;
  - opção sob medida para tema não encontrado.
- Preparar o modelo dos convites para registrar demonstrações e recursos, incluindo imagem ou vídeo, música, fotografia opcional, botões clicáveis, página longa, até oito fotos, contagem regressiva, narração e confirmação por formulário, quando aplicável.

#### W4 e W5 — comércio e integrações

- Desenhar o checkout em página única com:
  - dados pessoais;
  - endereço e entrega;
  - opção de NIF;
  - pagamento;
  - resumo recolhível;
  - botão “Finalizar e comprar”.
- Definir o acesso seguro criado automaticamente após a compra sem cadastro prévio.
- Modelar a reserva de 48 horas para transferência bancária.
- Especificar o cancelamento e a notificação automáticos após 48 horas sem confirmação.
- Definir o cálculo e a apresentação do frete para toda a Europa.
- Definir a emissão e disponibilização de fatura por venda, com NIF opcional para consumidor particular quando solicitado.
- Modelar a regra de melhor benefício aplicável quando houver descontos não acumuláveis.

#### W6 e W7 — pós-compra e produção

- Prototipar o briefing inteligente por modalidade e nível de customização.
- Definir o briefing mestre do evento e quais campos podem ser reutilizados entre produtos compatíveis.
- Definir uploads, progresso, salvamento automático e retomada.
- Definir versionamento de prévias, comentários, contador de alterações e aprovação final.
- Prototipar a linha do tempo, o contador de produção e as mensagens de pausa.
- Prototipar o quadro administrativo e a ficha de produção.

#### W10 — segurança, privacidade e RGPD

- Fazer inventário dos dados coletados, finalidade, acesso, retenção e descarte.
- Classificar fotografias de crianças e miniaturas como dados privados de tratamento restrito.
- Definir exclusão por padrão após a finalidade e conservação opcional somente mediante autorização para recompra.
- Definir funções administrativas e privilégios mínimos.
- Definir eventos obrigatórios de auditoria.
- Definir o modelo de ameaças para conta, checkout, uploads, área da cliente, administração e integrações.
- Transformar OWASP ASVS nível 2, RGPD por design e PCI DSS em checklist verificável.
- Definir plano de backup, restauração e resposta a incidentes.

### 5.3 Lacunas que precisam de decisão antes do Gate F0

| Lacuna | Por que bloqueia | Saída necessária |
|---|---|---|
| Plataforma e arquitetura tecnológica | Define viabilidade das integrações, segurança, catálogo e administração | Decisão registrada e validada contra todos os fluxos obrigatórios |
| Fornecedor de pagamento certificado | Bloqueia checkout e PCI DSS | Fornecedor, métodos aceitos, tokenização/campos hospedados e eventos de confirmação |
| Processo de confirmação da transferência | Bloqueia reserva de 48 horas e cancelamento | Decisão entre confirmação manual ou integração e regra operacional |
| Sistema de faturamento | Bloqueia emissão automática e NIF | Fornecedor/processo, numeração, impostos e correções |
| Regras fiscais detalhadas | A sessão só decidiu fatura por venda e NIF opcional | Validação responsável das regras aplicáveis em Portugal e vendas europeias |
| Solução de frete | Bloqueia cotação por endereço para toda a Europa | Transportadoras/agregador, serviços e tratamento de falhas |
| Provedor de e-mail e canal WhatsApp | Bloqueia notificações, prévias e entrega digital | Fornecedores, consentimentos e entregabilidade |
| Quantidades acima dos kits padrão | A cliente pode escolher quantidade maior | Regra de incremento, preço e limite operacional |
| Valor da criação de miniatura | Deve ser fixo e editável | Valor inicial e regra fiscal aplicável |
| Regras de desconto progressivo de conjunto | Bloqueia carrinho | Faixas, itens elegíveis e limites |
| Política de alteração após três rodadas | Bloqueia aprovação e cobrança adicional | Preço, aceite e forma de pagamento |
| Conteúdo e lista final do catálogo de lançamento | Bloqueia migração e testes | Relação aprovada de produtos, preços, imagens e status |
| Política de retenção por tipo de dado | Bloqueia RGPD e descarte automático | Prazos e base para dados pessoais, fiscais, pedidos, uploads e backups |
| Responsáveis operacionais | Bloqueia fila, atendimento e exceções | Papéis e permissões por atividade |

### 5.4 Marco M0 — fundação aprovada

**Critérios de saída:**

- modalidades de produto e suas diferenças estão documentadas;
- estados do pedido, transições, pausas e exceções críticas estão aprovados;
- taxonomia e esquema de dados do catálogo estão aprovados;
- protótipos dos fluxos de catálogo, compra, briefing, aprovação, acompanhamento e administração foram revisados;
- fornecedores críticos foram selecionados ou há prova técnica que retire o bloqueio;
- inventário e plano de migração dos dois catálogos existem;
- mapa de dados, retenção, papéis e ameaças foi aprovado;
- não há lacuna crítica sem responsável e decisão de gate.

## 6. Fase 1 — MVP de lançamento

**Prioridade:** obrigatório.

O MVP não é apenas uma loja com pagamento. O gate de lançamento exige uma encomenda completa atravessando o fluxo externo e interno.

### 6.1 Incremento 1A — experiência, catálogo e descoberta

#### Entregas

- Página inicial premium com:
  - produtos mais pedidos no topo;
  - pesquisa em destaque;
  - produtos relevantes em diferentes pontos;
  - promessas visuais curtas;
  - fotografias reais e provas de acabamento;
  - apoio humano disponível sem tornar suporte obrigatório.
- Cabeçalho simplificado e menu “Loja” organizado.
- Catálogo com produtos de papelaria, convites e digitais.
- Busca básica por personagem e tema usando metadados e variações cadastradas.
- Resultados exatos agrupados por categoria, cobrindo convites, lembrancinhas, kits, topos de bolo e digitais.
- Sugestões semelhantes em seção visualmente separada abaixo dos resultados exatos.
- Estado sem resultado com alternativas e acesso a projeto exclusivo.
- Páginas de produto com divulgação progressiva.
- Página de kit com resumo visual de quantidade, modelos, material e personalização; composição, processo, produção e cuidados em seções expansíveis.
- Diferenciação inequívoca entre arquivo digital pronto e convite digital personalizado.
- Prazos por família de convite apresentados no produto aplicável.
- Nota discreta sobre prazo físico padrão e consulta de urgência via suporte, sem cálculo ou promessa automática.
- Chamada “Não encontrou o que queria? Envie uma mensagem e fazemos seu projeto exclusivo”.
- Acesso compacto ao WhatsApp para suporte fora do checkout, levando o nome e o link do produto quando iniciado pela página do item.
- Links de publicações e stories do Instagram preparados para apontar diretamente ao produto ou tema correspondente.

#### Critérios de aceite

- uma cliente encontra um produto por personagem sem saber o título exato;
- os resultados exatos aparecem antes de sugestões semelhantes;
- todos os tipos de produto associados ao tema podem aparecer na mesma busca, agrupados;
- produto digital pronto não sugere briefing nem aprovação;
- produto personalizado explica antes da compra o que acontece após o pagamento;
- página de kit informa todos os elementos aprovados sem sobrecarregar o cartão;
- chamadas, prazos e modalidade exibidos são coerentes com o item cadastrado;
- interface preserva a linguagem visual aprovada em desktop e dispositivos móveis.

### 6.2 Incremento 1B — carrinho, checkout, pagamento, tributação e frete

#### Entregas

- Carrinho com quantidades e opções coerentes por modalidade.
- Lembrancinhas com kits de 20, 30, 40 e 50 unidades e suporte à quantidade maior conforme regra fechada no Gate F0.
- Opção de ativar ou dispensar a personalização básica gratuita no carrinho.
- Miniatura com três escolhas:
  - não quero miniatura;
  - já tenho o arquivo para enviar;
  - quero que a JS Designs crie para mim.
- Cobrança única da criação de uma mesma miniatura, reutilizável em vários produtos compatíveis do mesmo pedido.
- Valor fixo da criação de miniatura configurável na administração e visível antes da adição.
- Seção discreta “Complete sua festa”, limitada a dois ou três complementos altamente relevantes.
- Complementos relacionados por tema e ocasião.
- Desconto progressivo automático de conjunto, sem cupom, conforme regra aprovada no Gate F0.
- Checkout em página única sem cadastro obrigatório.
- Resumo recolhível, dados pessoais, endereço, entrega, NIF opcional e pagamento.
- Cálculo de frete após informação do endereço, cobrindo destinos em toda a Europa.
- Pagamentos instantâneos confirmados automaticamente.
- Transferência bancária em “Aguardando pagamento”, com reserva de 48 horas.
- Cancelamento e notificação automáticos quando a transferência não for confirmada no prazo.
- Emissão de fatura por venda e inclusão opcional do NIF quando solicitado.
- Criação automática de acesso seguro após o pagamento.
- Botões flutuantes de suporte ocultos durante o checkout.
- Mensagem “Pagamentos protegidos e processados por parceiros certificados”.

#### Critérios de aceite

- compra sem cadastro chega à confirmação e gera acesso seguro sem senha exposta;
- o total do carrinho reflete quantidade, miniatura, complementos, desconto, frete e demais valores aplicáveis;
- a mesma miniatura não é cobrada duas vezes no mesmo pedido quando aplicada a itens compatíveis;
- descontos incompatíveis não acumulam e, quando aplicável, o melhor benefício é escolhido automaticamente;
- um pagamento instantâneo confirmado move o pedido para o estado correto uma única vez, mesmo se o evento for reenviado;
- uma transferência não confirmada permanece reservada e, ao completar 48 horas, é cancelada e notificada;
- falha ou atraso de integração não cria pedido pago indevidamente;
- o NIF é opcional e aparece na fatura quando fornecido;
- o número completo do cartão, CVV e PIN nunca são armazenados ou processados pelo sistema da JS Designs;
- chat e WhatsApp não aparecem no checkout.

### 6.3 Incremento 1C — briefing, prévia e aprovação

#### Entregas

- Após a confirmação de pagamento, abertura imediata do briefing aplicável.
- Acesso ao briefing pela confirmação, área da cliente e link enviado no canal escolhido.
- Prazo informativo de três dias corridos para preenchimento.
- Pedido mantido ativo em “Aguardando briefing” após o prazo, sem cancelamento automático.
- Briefing mestre do evento com reutilização de dados comuns entre produtos compatíveis.
- Perguntas adaptadas ao produto:
  - convites: data, local, personagem, cores e demais dados específicos;
  - lembrancinhas predefinidas: nome, idade, alterações e miniatura;
  - projeto inexistente no catálogo: questionário detalhado de criação.
- Salvamento automático, indicador de progresso e retomada sem perda.
- Upload seguro dos arquivos necessários.
- Histórico numerado de prévias.
- Comentários e pedidos de alteração registrados na área da cliente.
- Contador das três rodadas gratuitas restantes.
- Link seguro da prévia enviado por WhatsApp ou e-mail, conforme escolha.
- Confirmação explícita de revisão de nome, idade, data, horário, endereço e textos aplicáveis antes da aprovação final.
- Bloqueio do início da produção até a aprovação final.

#### Critérios de aceite

- o briefing correto é exibido para cada modalidade e não aparece para arquivo digital pronto;
- respostas comuns do mesmo evento são reutilizadas sem sobrescrever dados específicos;
- interromper e retomar o briefing não perde respostas;
- prazo de produção não começa com briefing incompleto;
- cada prévia preserva versão, autor, data e histórico;
- cliente e administração veem o mesmo número de alterações gratuitas restantes;
- aprovação final exige a confirmação de revisão;
- após aprovação, a versão liberada para produção não pode ser substituída silenciosamente;
- links de prévia e uploads não são públicos e expiram conforme política aprovada.

### 6.4 Incremento 1D — pedidos, produção, entrega e área da cliente

#### Entregas

- Área da cliente com:
  - pedidos;
  - briefing;
  - prévias;
  - ações pendentes;
  - faturas;
  - produtos digitais;
  - acompanhamento.
- Linha do tempo:
  - Pedido confirmado;
  - Aguardando briefing;
  - Arte em criação;
  - Aguardando aprovação;
  - Em produção;
  - Verificação de qualidade;
  - Preparando para envio;
  - Enviado.
- Contador de produção de sete dias corridos iniciado somente após o briefing completo.
- Pausa do contador quando a continuidade depender de resposta, alteração ou aprovação da cliente.
- Motivo da pausa e próxima ação exibidos claramente.
- Datas estimadas recalculadas conforme briefing, aprovação, produção e transporte, com lógica aprovada na Fase 0.
- Fotografia privada da encomenda finalizada anexada à verificação de qualidade.
- Entrega automática de arquivo digital pronto por e-mail e disponibilização na área da cliente.
- Preferência entre e-mail e WhatsApp para comunicações pós-compra e produtos digitais, conforme aplicável.
- Notificações de mudança de estado e ações pendentes.

#### Critérios de aceite

- um pedido real de cada modalidade percorre apenas os estados aplicáveis;
- prazo físico só começa após os pré-requisitos definidos;
- pausa e retomada do contador são auditáveis e coerentes;
- cliente vê estado, prazo restante e próxima ação sem precisar do WhatsApp;
- fotografia de verificação de qualidade só é acessível à cliente do pedido e à equipe autorizada;
- arquivo digital pronto é entregue automaticamente após pagamento confirmado;
- falha de envio não elimina o arquivo da área da cliente e pode ser tratada pela operação;
- comunicação usa o canal escolhido e não expõe dados sensíveis na mensagem.

### 6.5 Incremento 1E — administração e operação

#### Entregas

- Administração protegida por autenticação multifator.
- Controle de acesso por função e privilégio mínimo.
- Gestão de:
  - produtos, famílias, formatos, temas e personagens;
  - preços e disponibilidade;
  - kits, materiais e quantidades;
  - valor fixo da miniatura;
  - complementos e descontos progressivos;
  - pedidos, pagamentos e faturas;
  - briefing, prévias e aprovações;
  - prazos, pausas e estados;
  - arquivos e fotografia de qualidade.
- Quadro visual da produção com pedido, prazo restante, prioridade, arte aprovada, materiais e próxima ação.
- Ficha automática de produção com versão aprovada, itens, quantidades, materiais, acabamento, prazo, endereço e checklist de qualidade.
- Registros de auditoria para acesso e alteração de pedidos, arquivos, dados fiscais, produção e configurações sensíveis.
- Ações administrativas críticas com validação e rastreabilidade.

#### Critérios de aceite

- nenhuma conta administrativa acessa o painel sem MFA;
- cada função só vê e altera os dados necessários;
- quadro e ficha refletem a versão aprovada e o estado atual;
- alterações de preço não mudam retroativamente pedidos confirmados;
- mudança de estado inválida é impedida ou exige tratamento explícito;
- acessos a dados fiscais e arquivos privados ficam registrados;
- uma encomenda pode ser produzida e enviada usando apenas as informações da ficha e do pedido.

### 6.6 Incremento 1F — segurança, privacidade e resiliência

#### Controles obrigatórios

- OWASP ASVS nível 2 como base de verificação.
- RGPD por design.
- PCI DSS limitado pelo uso de fornecedor certificado, campos hospedados ou tokenização.
- HTTPS/TLS e HSTS.
- Criptografia em repouso para dados e backups aplicáveis.
- Gestão segura de segredos.
- Cookies seguros.
- Proteção CSRF.
- Content Security Policy.
- WAF, limitação de tentativas e proteção contra bots.
- Senhas armazenadas com Argon2id.
- MFA para todas as contas administrativas.
- Controle de acesso por função e privilégio mínimo.
- Registros de auditoria.
- Uploads com:
  - tipos e tamanhos permitidos;
  - validação de assinatura;
  - renomeação aleatória;
  - análise antimalware;
  - armazenamento privado;
  - links temporários assinados.
- Fotografias de crianças e miniaturas nunca públicas.
- Consentimento separado para qualquer uso de imagem em marketing.
- Minimização, acesso, correção e eliminação de dados.
- Retenção e descarte automatizáveis conforme política aprovada.
- Backups criptografados, restauração testada e resposta a incidentes ensaiada.
- Revisão de código, análise de dependências, testes automatizados de segurança, varredura de vulnerabilidades e teste de intrusão.

#### Critérios de aceite

- não há achado crítico ou alto sem correção ou decisão formal de não lançamento;
- teste de intrusão foi concluído antes do lançamento;
- autorização horizontal e vertical foi testada na conta e na administração;
- um usuário não consegue acessar pedido, prévia, fatura ou arquivo de outro usuário;
- upload disfarçado, malicioso, fora do tipo ou tamanho permitido é bloqueado;
- links assinados expiram e não expõem o caminho interno do arquivo;
- segredos não aparecem em código, logs, mensagens ou artefatos;
- backups são restaurados com sucesso em teste documentado;
- pedidos de acesso, correção e eliminação têm fluxo operacional;
- consentimento de marketing é separado da finalidade operacional;
- cartão completo, CVV e PIN não aparecem em banco, logs ou ferramentas administrativas.

### 6.7 Marco M1 — núcleo funcional completo

**Critérios de saída:**

- uma jornada completa de cada modalidade foi executada em ambiente de validação;
- catálogo, checkout, pagamento, fatura, frete, briefing, aprovação, produção e acompanhamento funcionam de ponta a ponta;
- administração consegue operar pedidos sem planilha paralela obrigatória;
- migração piloto foi validada;
- controles de segurança estão implementados e testáveis;
- todas as lacunas críticas da Fase 0 foram fechadas.

### 6.8 Marco M2 — pronto para lançamento

**Gate cumulativo de aceite:**

- catálogo final migrado e aprovado;
- preços, prazos, materiais, modalidades e imagens conferidos;
- integrações de pagamento, faturamento, frete, e-mail e arquivos foram testadas em sucesso, falha, repetição e indisponibilidade;
- fluxos de transferência e cancelamento de 48 horas foram ensaiados;
- atendimento e operação conhecem as exceções e responsabilidades;
- teste de restauração de backup concluído;
- varredura de vulnerabilidades e teste de intrusão concluídos;
- nenhum defeito bloqueador permanece aberto;
- mensagens legais, privacidade, retenção e consentimentos foram aprovados;
- plano de publicação, verificação pós-publicação e reversão existe;
- o site não depende de suporte via WhatsApp para concluir uma encomenda normal.

## 7. Dados e migração do catálogo

### 7.1 Modelo mínimo por produto

Cada item do catálogo de lançamento deve ser mapeado, conforme aplicável, para:

- identificador estável;
- nome;
- status de publicação;
- categoria e tipo;
- família e formato;
- modalidade de entrega e personalização;
- tema, personagem e variações de busca;
- descrição curta;
- descrição detalhada e seções expansíveis;
- preço e regra de quantidade;
- quantidade total e composição de kit;
- materiais e acabamentos;
- personalização incluída;
- compatibilidade com miniatura;
- compatibilidade com briefing mestre;
- prazo aplicável;
- imagens, vídeo ou demonstração;
- recursos de convite digital;
- produtos complementares;
- compatibilidade para desconto de conjunto;
- instruções pós-compra;
- necessidade de produção e frete;
- arquivo digital associado, quando aplicável;
- dados necessários à ficha de produção.

**Lacunas:** a sessão não informa identificadores existentes, contagem de produtos, formato das fontes atuais, estoque, variantes técnicas nem regras de indisponibilidade. Esses pontos precisam ser levantados sem presumir uma resposta.

### 7.2 Sequência de migração

1. **Inventariar:** exportar ou registrar os dois catálogos atuais e suas fontes.
2. **Classificar:** atribuir categoria, modalidade, família, formato, tema e personagem.
3. **Normalizar:** padronizar nomes, quantidades, preços, materiais, prazos e variações de escrita.
4. **Enriquecer:** adicionar metadados de busca, compatibilidades, complementos, briefing e ficha de produção.
5. **Validar privacidade:** impedir publicação acidental de fotografias privadas ou conteúdo sem autorização de marketing.
6. **Preparar mídia:** associar imagens e demonstrações à versão correta do produto, mantendo arquivos privados separados.
7. **Importar em homologação:** executar carga repetível com relatório de erros.
8. **Revisar:** validar amostra e depois a totalidade do catálogo de lançamento pela JS Designs.
9. **Ensaiar delta:** definir como incorporar alterações entre a carga piloto e a publicação.
10. **Congelar e carregar:** realizar a carga final conforme plano de publicação.
11. **Conciliar:** comparar fonte aprovada e site publicado.
12. **Manter reversão:** preservar exportação da fonte e procedimento de desfazer a carga sem apagar dados legítimos.

### 7.3 Critérios de aceite da migração

- todo item aprovado para lançamento foi importado uma única vez;
- nenhum item fora do conjunto aprovado foi publicado;
- preços, quantidades, materiais e prazos coincidem com a fonte aprovada;
- produtos aparecem nas categorias e pesquisas corretas;
- variações de personagem e tema retornam os itens esperados;
- complementos só relacionam produtos compatíveis;
- arquivos digitais corretos estão ligados aos produtos corretos e permanecem privados;
- imagens de crianças ou pedidos anteriores não foram tornadas públicas sem consentimento separado;
- relatório de itens rejeitados, incompletos ou com conflito foi resolvido antes da publicação.

## 8. Integrações e seus contratos de aceite

| Integração | Uso no núcleo | Dependência/lacuna | Aceite mínimo |
|---|---|---|---|
| Pagamentos certificados | Pagamentos instantâneos e estado do pedido | Fornecedor não escolhido | Tokenização/campos hospedados; eventos autenticados; repetição idempotente; cartão completo e CVV fora do sistema |
| Transferência bancária | Reserva por 48 horas | Confirmação manual ou integrada não definida | Estado “Aguardando pagamento”; confirmação rastreável; cancelamento e aviso após 48 horas |
| Faturamento | Documento por venda e NIF opcional | Sistema e detalhes fiscais não definidos | Fatura emitida uma vez, vinculada ao pedido, enviada por e-mail e disponível na conta |
| Frete | Cotação por endereço e envios europeus | Transportadoras/agregador não escolhidos | Cotação consistente, serviço selecionado registrado e falha tratada sem total incorreto |
| E-mail | Acesso, briefing, prévia, fatura, digital e notificações | Provedor não escolhido | Entrega testada, links seguros, falha observável e reenvio possível |
| WhatsApp | Suporte, projeto exclusivo e canal pós-compra escolhido | Forma de integração não definida | Contexto do produto no contato; sem dados sensíveis desnecessários; indisponibilidade não bloqueia a conta |
| Armazenamento privado | Uploads, prévias, digitais e fotografia de qualidade | Solução não escolhida | Antimalware, acesso privado, links temporários, retenção e auditoria |
| Antibot/WAF | Proteção de conta, formulários e checkout | Solução não escolhida | Limitação de tentativas e bloqueios sem impedir o uso normal validado |

Para toda integração crítica, testar:

- sucesso;
- recusa ou falha;
- indisponibilidade;
- tempo excedido;
- evento duplicado;
- evento fora de ordem;
- reprocessamento seguro;
- conciliação;
- logs sem dados sensíveis;
- ação manual de recuperação.

## 9. Estratégia de testes

### 9.1 Camadas obrigatórias

- **Regras unitárias:** descontos, cobrança única de miniatura, prazos, pausas, consumo de alterações, transições e elegibilidade de briefing.
- **Integração:** pagamento, transferência, faturamento, frete, e-mail, armazenamento e eventos.
- **Ponta a ponta:** jornadas reais por modalidade e por canal.
- **Administração:** permissões, fila, ficha, catálogo e auditoria.
- **Migração:** completude, unicidade, associação de mídia e busca.
- **Segurança:** revisão de código, dependências, testes automatizados, varredura e intrusão.
- **Privacidade:** acesso, correção, eliminação, consentimento, retenção e isolamento de arquivos.
- **Recuperação:** backup, restauração e retomada de integrações.
- **Experiência:** navegação intuitiva, divulgação progressiva, clareza de modalidade e uso em desktop e dispositivos móveis.

### 9.2 Jornadas mínimas de aceite

1. Comprar arquivo digital pronto e recebê-lo automaticamente.
2. Comprar convite Essencial, preencher briefing, receber prévia, solicitar alteração e aprovar.
3. Comprar convite Cinemágico e verificar prazo específico.
4. Comprar kit físico com personalização gratuita.
5. Comprar vários produtos compatíveis usando uma miniatura já existente.
6. Comprar vários produtos e pagar uma única vez pela criação da mesma miniatura.
7. Adicionar complementos do mesmo tema e validar desconto progressivo.
8. Comprar sem conta, criar acesso pós-pagamento e retomar briefing.
9. Pagar instantaneamente e validar confirmação única.
10. Selecionar transferência, confirmar dentro do período e avançar.
11. Não confirmar transferência e validar cancelamento após 48 horas.
12. Informar NIF opcional e obter fatura correta.
13. Simular endereço em destino europeu e calcular frete.
14. Pausar pedido aguardando cliente e validar contador e mensagem.
15. Aprovar arte após confirmação dos dados e gerar ficha de produção.
16. Anexar fotografia de qualidade privada e avançar até envio.
17. Tentar acessar pedido, fatura, prévia e fotografia de outra conta e ser bloqueado.
18. Enviar upload inválido ou malicioso e ser bloqueado.
19. Falhar e reenviar notificação sem duplicar pedido, cobrança ou fatura.
20. Executar pedido de acesso/correção/eliminação conforme política.

### 9.3 Lacunas de qualidade

- Não foram definidos volumes, metas de tempo de resposta, disponibilidade, navegadores suportados ou limites de concorrência.
- Esses limites devem ser decididos no Gate F0 antes de transformar desempenho e compatibilidade em critérios quantitativos. Até lá, não se deve inventar números.

## 10. Fase 2 — importante, logo após o núcleo

**Entrada:** M2 concluído e operação do núcleo estabilizada.

### 10.1 Itens explicitamente priorizados para esta fase

- Comparação avançada de convites:
  - famílias Essenciais, Interativos e Cinemágicos;
  - oito formatos preservados;
  - demonstrações reais;
  - comparação de entrega, animação, música, botões, fotos, narração, prazo e preço.
- Pagamentos adicionais, especialmente cobranças posteriores relacionadas a alterações além das incluídas ou outros adicionais, conforme regras ainda a definir.
- Busca preditiva com sugestões durante a digitação e tolerância a variações ou erros.
- Favoritos.
- Avaliações, inclusive fotografias espontâneas, sem recompensa.
- Suporte automático:
  - botão flutuante de chat acima do WhatsApp;
  - respostas somente com conteúdo aprovado;
  - admissão clara quando não souber;
  - coleta de mensagem para atendimento posterior;
  - consulta segura de status, prazo restante e próxima ação para cliente autenticada;
  - encaminhamento ao WhatsApp com resumo e contexto quando necessário.
- Orçamentos recuperáveis com produto, quantidade, opções e preço salvos.
- Lembretes automáticos de orçamento e carrinho abandonado.
- SEO.
- Automações pós-núcleo, incluindo lembretes de briefing pelo canal escolhido.

### 10.2 Itens aprovados sem fase explícita

Os seguintes itens foram aprovados ou desejados após a priorização MoSCoW, mas não receberam posição inequívoca no roadmap. Devem passar por decisão de prioridade antes da implementação:

- cupom individual de 10% na primeira compra, sem valor mínimo;
- captura de e-mail com exibição atrasada ou após envolvimento, uma vez por visitante e nunca no checkout;
- não acumulação do cupom de primeira compra com outras promoções;
- seleção automática do melhor benefício;
- descontos progressivos por quantidade;
- promoções pontuais em datas especiais;
- newsletter com promoções exclusivas de packs e descontos selecionados;
- recuperação de carrinho com promoção ou oportunidade real.

Até essa decisão, esses itens **não bloqueiam o lançamento** e não devem ser tratados como obrigatórios. Caso sejam priorizados para a Fase 2, precisam compartilhar um motor único de promoções para evitar totais conflitantes.

### 10.3 Ordem recomendada na fase

1. Resolver regras de cobranças adicionais e promoções.
2. Implementar comparação de convites e busca preditiva sobre os dados já normalizados.
3. Implementar avaliações e favoritos.
4. Implementar orçamentos/carrinhos recuperáveis e suas automações.
5. Implantar suporte automático com base de conteúdo aprovada e acesso autenticado restrito.
6. Expandir SEO e automações somente depois que conteúdo e URLs estiverem estáveis.

### 10.4 Marco M3 — evolução imediata validada

**Critérios de saída:**

- funcionalidades novas usam os mesmos modelos de produto, pedido, cliente e permissões do núcleo;
- cobranças adicionais e promoções não geram dupla cobrança ou benefício incompatível;
- busca preditiva preserva a ordenação de resultado exato antes de semelhantes;
- chat não inventa respostas e não expõe dados de pedido sem autenticação e autorização;
- avaliações são espontâneas e imagens só são publicadas com consentimento adequado;
- lembretes respeitam preferência de canal e consentimentos;
- mudanças relevantes passaram por testes de segurança e regressão.

## 11. Fase 3 — evolução futura

**Prioridade:** futuro, explicitamente adiado.

- Calendário de celebrações.
- Recompra rápida com edição livre de idade, data, tema e demais dados necessários.
- Programa de fidelização.
- Cartões-presente.
- Recomendações avançadas.
- Logins sociais.
- Blog completo.
- Francês.
- Espanhol.
- Pré-visualização automática.
- Relatórios avançados.

### Gate de entrada da fase futura

Antes de iniciar qualquer item:

- validar que o núcleo e a Fase 2 estão estáveis;
- confirmar necessidade e prioridade com evidência operacional ou de cliente;
- revisar impacto em privacidade, consentimento e retenção;
- definir critérios de aceite específicos;
- garantir que a expansão não reintroduza dependência de suporte para a jornada normal.

## 12. Itens excluídos da direção

Não devem ser reintroduzidos sem uma nova decisão explícita:

- consultoria guiada por orçamento;
- vários CTAs por modalidade como padrão;
- cartões de personagens como caminho principal da página inicial;
- formulário para dúvidas de produto;
- suporte exibido durante o checkout;
- menu inicial de escolha entre robô e humano;
- troca automática de “Comprar agora” por fluxo de urgência;
- promessa ou cálculo automático de urgência;
- recompensa por avaliações com fotografia;
- afirmação “Pagamentos 100% seguros”;
- cancelamento automático de pedido apenas porque o briefing não foi preenchido em três dias;
- cópia da identidade visual das referências externas.

## 13. Riscos e mitigação

| Risco | Impacto | Mitigação e gate |
|---|---|---|
| Construir vitrine antes da operação pós-compra | Pedidos voltam a depender do WhatsApp | M1 só é aceito com briefing, aprovação, produção e acompanhamento completos |
| Regras comerciais incompletas | Totais errados, retrabalho e conflito com clientes | Fechar descontos, miniatura, quantidades e alterações no Gate F0 |
| Divergência entre dois catálogos | Busca incoerente, duplicação e preço incorreto | Taxonomia única, carga repetível e conciliação total antes do M2 |
| Prazos conflitantes entre produtos | Promessa incorreta | Prazo como dado por modalidade e testes por família |
| Estado ou contador incorreto | Perda de confiança e prazo inexato | Máquina de estados central, histórico e testes de pausa/retomada |
| Cobrança duplicada por evento repetido | Perda financeira e reputacional | Idempotência e conciliação em pagamentos, faturas e notificações |
| Vazamento de fotos, miniaturas ou prévias | Alto risco de privacidade, especialmente para crianças | Armazenamento privado, autorização, links assinados, auditoria e retenção |
| Conta criada automaticamente ser tomada por terceiro | Exposição de pedidos e dados fiscais | Fluxo seguro de ativação, limitação de tentativas e testes de autorização |
| Administração com privilégios excessivos | Acesso indevido e fraude interna | MFA, papéis mínimos e auditoria |
| Upload malicioso | Comprometimento do sistema | Assinatura, tipo/tamanho, antimalware, isolamento e bloqueio |
| Dependência de fornecedor externo | Checkout ou operação indisponível | Falhas tratadas, reprocessamento, conciliação e ação manual documentada |
| Frete europeu ou tributação subespecificados | Total ou documento incorreto | Escolha e validação dos processos no Gate F0 |
| Chat automático inventar respostas | Orientação comercial errada | Conteúdo aprovado, resposta de incerteza e encaminhamento; implementação só na Fase 2 |
| Excesso de informação | Abandono e perda do posicionamento premium | Divulgação progressiva e testes de experiência em cada incremento |
| Descontos sobrepostos | Margem e total incorretos | Motor único de benefícios, não acumulação e melhor condição testada |
| Migração publicar imagem sem consentimento | Violação de privacidade | Etapa explícita de revisão de direitos e privacidade |
| Urgência ser entendida como garantida | Promessa operacional inviável | Nota discreta e confirmação humana; sem cálculo automático |
| Backup existir sem poder ser restaurado | Perda definitiva de dados | Teste documentado de restauração como gate do M2 |

## 14. Checklist consolidado dos gates

### Gate F0 — pode iniciar o núcleo

- [ ] modalidades e regras essenciais fechadas;
- [ ] taxonomia e modelos de dados aprovados;
- [ ] estados e transições aprovados;
- [ ] protótipos principais validados;
- [ ] integrações críticas escolhidas ou tecnicamente validadas;
- [ ] catálogo de lançamento inventariado;
- [ ] mapa de dados, retenção, acessos e ameaças aprovado;
- [ ] operação e responsáveis definidos.

### Gate M1 — núcleo funcional

- [ ] catálogo e busca básica funcionam;
- [ ] carrinho e checkout calculam corretamente;
- [ ] pagamento, transferência, fatura e frete funcionam;
- [ ] conta pós-compra e comunicações funcionam;
- [ ] briefing, prévias e aprovação funcionam;
- [ ] produção, prazo, pausas e linha do tempo funcionam;
- [ ] administração opera a encomenda;
- [ ] segurança obrigatória está implementada.

### Gate M2 — pode lançar

- [ ] migração final conciliada;
- [ ] jornadas mínimas aprovadas;
- [ ] exceções de integrações testadas;
- [ ] permissões e isolamento de dados testados;
- [ ] vulnerabilidades críticas e altas resolvidas;
- [ ] teste de intrusão concluído;
- [ ] restauração de backup comprovada;
- [ ] operação e resposta a incidentes ensaiadas;
- [ ] conteúdo, preços, prazos e mensagens aprovados;
- [ ] plano de publicação e reversão pronto.

### Gate M3 — evolução imediata concluída

- [ ] comparação, busca preditiva e crescimento não quebram o núcleo;
- [ ] pagamentos adicionais e promoções são conciliáveis;
- [ ] chat usa conteúdo aprovado e autorização correta;
- [ ] automações respeitam preferência e consentimento;
- [ ] regressão funcional e de segurança concluída.

## 15. Resultado esperado do lançamento

O lançamento está concluído quando uma cliente pode encontrar um produto pelo personagem ou tema, entender o que receberá, comprar sem cadastro obrigatório, pagar com proteção de fornecedor certificado, receber acesso seguro, preencher o briefing aplicável, revisar e aprovar a arte, acompanhar produção e envio, acessar fatura e arquivos e resolver o fluxo normal sem depender do WhatsApp. Ao mesmo tempo, a JS Designs consegue administrar catálogo, pagamentos, personalização, prazos, produção, qualidade e envio com rastreabilidade e proteção adequada dos dados.
