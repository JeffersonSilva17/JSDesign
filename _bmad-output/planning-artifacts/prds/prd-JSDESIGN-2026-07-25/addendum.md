# Adendo ao PRD — Loja Online JS Designs

## Finalidade

Este adendo preserva detalhes técnicos, operacionais e visuais importantes para arquitetura, UX e implementação, mas que não devem prescrever mecanismos no corpo do PRD. Nenhuma sugestão técnica abaixo substitui uma decisão de arquitetura validada.

## 1. Direção visual e relação com o protótipo

### 1.1 Referência Gio

A referência principal fornece uma hierarquia comercial útil:

1. faixa de anúncio;
2. cabeçalho com marca, busca, conta e Carrinho;
3. categorias;
4. hero editorial;
5. benefícios de confiança;
6. produtos mais vendidos.

Não devem ser copiados a identidade Gio, a paleta coral, textos promocionais, fotografias ou composição exata. Também não devem ser importadas alegações como cashback, parcelamento, “entrega garantida” ou “compra 100% segura” sem base comercial, operacional e jurídica.

### 1.2 Direção da JS Designs

- branco, bege claro, dourado champanhe, taupe e preto suave;
- espaço em branco generoso;
- fotografia real de produtos e detalhes de acabamento;
- hierarquia editorial premium, acolhedora e clara;
- animações suaves, respeitando preferência por movimento reduzido;
- divulgação progressiva de informações;
- nenhuma publicidade de terceiros.

### 1.3 Estado do protótipo

O protótipo já demonstra:

- home responsiva;
- navegação, busca e estados sem resultado;
- hero, produtos, categorias e explicação da jornada;
- prova de qualidade, Projeto Exclusivo, Instagram e rodapé;
- menu móvel, modais, favoritos, Carrinho demonstrativo, Chat e WhatsApp;
- atenção inicial a teclado, foco, regiões dinâmicas e movimento reduzido.

Ainda não demonstra:

- catálogo e busca reais;
- páginas detalhadas;
- Carrinho e Checkout transacionais;
- pagamentos, frete, NIF ou Fatura;
- conta e Área da Cliente;
- Briefing, versões, aprovação e estados;
- Produção, qualidade, rastreamento e Administração;
- persistência, integrações ou controles de segurança.

Favoritos e newsletter não devem consumir capacidade do MVP antes das nove áreas obrigatórias.

Antes de servir como base de implementação, o protótipo também precisa:

- mudar a promessa de Produção para sete dias corridos após a Aprovação Final, não após o Briefing;
- usar “Comprar já” em lembrancinhas;
- substituir a taxonomia isolada “Interativo” pelo modelo final de famílias, formatos e classificação Padrão/Complexo;
- encaminhar produtos personalizados à página e configuração corretas antes do Carrinho;
- incluir Produto Digital Pronto na descoberta e demonstrar sua diferença em relação ao convite personalizado.

## 2. Modelo operacional sugerido

### 2.1 Estados principais

1. Aguardando pagamento;
2. Pedido confirmado;
3. Aguardando briefing;
4. Arte em criação;
5. Aguardando aprovação;
6. Alteração solicitada;
7. Aprovado para produção;
8. Em produção;
9. Verificação de qualidade;
10. Preparando para envio;
11. Enviado;
12. Concluído;
13. Pausado;
14. Exceção;
15. Cancelado.

Convites digitais saem de “Aguardando aprovação” para entrega digital após a Aprovação Final. Produtos Digitais Prontos não percorrem Briefing nem Aprovação.

### 2.2 Relógios separados

- prazo para a cliente concluir o Briefing;
- prazo para primeira Arte;
- tempo aguardando a cliente;
- prazo de Produção;
- prazo de preparação e transporte;
- prazo de atendimento humano.

Os relógios não devem ser somados ou apresentados como um único prazo opaco. Pausas precisam guardar início, fim, motivo e responsável.

### 2.3 Identidade do Pedido

Um identificador deve relacionar:

- pagamento e conciliação;
- cliente e consentimentos;
- itens e preços;
- Briefing e arquivos;
- versões da Arte e Aprovação Final;
- Ficha de Produção;
- fotografia de qualidade;
- envio e rastreamento;
- Fatura;
- notificações e eventos de auditoria.

## 3. Orientações para arquitetura

### 3.1 Pagamentos

- Modelar a conta do comerciante para pessoa singular/trabalhadora independente em Portugal, código CIRS 1336, com IBAN de titularidade correspondente.
- Usar um provedor central para Checkout, cartões, wallets, MB WAY, reembolsos, disputas e conciliação; não depender exclusivamente da Wise.
- Usar Novo Banco como conta principal em EUR e Wise Business como conta auxiliar para moedas estrangeiras efetivamente suportadas pelo provedor.
- Habilitar no MVP cartão, Apple Pay, Google Pay, MB WAY e transferência bancária conforme país, dispositivo e moeda.
- Incluir PayPal no MVP somente se o provedor escolhido centralizar confirmação, saldo, reembolso, disputa e conciliação; caso contrário, priorizá-lo após o lançamento.
- Preferir campos hospedados ou redirecionamento/tokenização do parceiro.
- Tratar confirmações por webhook como repetíveis e fora de ordem.
- Manter chave idempotente por tentativa comercial.
- Separar estado comercial do Pedido e estado financeiro.
- Criar conciliação visível e fila de divergências.

### 3.2 Transferência

- Definir fonte de confirmação: manual, bancária ou parceiro.
- Usar expiração de 48 horas com rotina reprocessável.
- Não liberar Briefing ou entrega com base apenas no retorno do navegador.

### 3.3 Faturamento e fiscalidade

- Emitir em nome legal, domicílio fiscal e NIF da titular da atividade; “JS Designs” pode aparecer como marca comercial adicional.
- Usar Novo Banco como conta principal em euro e Wise Business como conta auxiliar multimoeda.
- Validar com contabilista se a venda habitual de produtos físicos e digitais exige CAE complementar ao CIRS 1336.
- Configurar Fatura, Fatura-recibo, adiantamento e Recibo conforme modalidade, pagamento e disponibilização.
- Selecionar solução compatível com Portugal e vendas europeias.
- Validar IVA/OSS, moeda, NIF, numeração, notas de crédito, reembolso e retenção.
- Emitir uma única Fatura por Pedido misto quando permitido; se houver obrigação de separar documentos por modalidade, gerá-los automaticamente e mantê-los agrupados no mesmo Pedido.
- Tratar Fatura como consequência idempotente de um evento financeiro confirmado.

### 3.4 Frete

- No lançamento, automatizar entrega física somente para os 27 países da União Europeia.
- Para outros países europeus, encaminhar a cliente ao Suporte Online e avaliar manualmente impostos, alfândega, transportadora, prazo e preço.
- Definir transportadoras, regras de peso/volume, rastreamento e exceções para a UE.
- Separar prazo de Produção da estimativa da transportadora.
- Preservar snapshot de endereço e opção de frete no Pedido.

### 3.5 Conta pós-compra

- Não exigir cadastro antes do pagamento.
- Após a compra, associar o Pedido a acesso seguro por criação de senha, link temporário ou mecanismo equivalente.
- Impedir enumeração de pedidos por número e e-mail.
- Definir recuperação que não exponha dados pelo Chat.

### 3.6 Uploads e mídia privada

- Validar tipo permitido, assinatura real, tamanho e quantidade.
- Gerar nomes não previsíveis e analisar conteúdo malicioso.
- Armazenar fora de área pública.
- Distribuir por autorização ou links temporários.
- Remover metadados desnecessários quando aplicável.
- Definir retenção por tipo e exclusão verificável.

### 3.7 Briefing, versões e Aprovação

- Manter um único Briefing mestre por Pedido/evento, com dados compartilhados e seções condicionais por tipo de produto.
- Relacionar explicitamente cada resposta e upload aos itens que a utilizam.
- Salvar rascunho automaticamente e mostrar completude.
- Versionar Prévias de forma imutável.
- Registrar alterações como solicitações consolidadas.
- Vincular a Aprovação Final à versão, autora, data e confirmações críticas.
- Impedir que a Ficha de Produção aponte para versão diferente da aprovada.

### 3.8 Suporte

- A base automática deve ser conteúdo editorial controlado, não resposta generativa aberta.
- Cada resposta precisa de idioma, status, data de revisão e responsável.
- O encaminhamento humano deve preservar transcrição e contexto consentido.
- Definir caixa de entrada, notificações, atribuição e estado da conversa.
- Não enviar Briefings, Prévias ou dados sensíveis ao WhatsApp sem regra e consentimento adequados.
- Manter Atendimento Automático 24 horas e atendimento humano de segunda a sábado, das 8h às 20h no horário de Portugal continental.
- Enfileirar mensagens fora do horário e comunicar prazo máximo de um dia útil, sem sinalizar presença humana falsa.

### 3.9 Capacidade de produção

- Usar 100 peças físicas por semana como capacidade segura inicial.
- Usar 4 pontos diários para convites: Padrão consome 1 e Complexo consome 2.
- Permitir que Sharom abra capacidade adicional conforme sua disponibilidade e a ajuda do marido.
- Exibir a próxima data disponível quando os pontos diários de convites estiverem completos.
- Reservar provisoriamente uma janela semanal no pagamento e liberá-la/recalcular se o atraso da cliente deslocar Briefing ou Aprovação.
- Exibir a próxima janela disponível antes do pagamento quando a semana pretendida estiver completa.
- Se um auxiliar usar o painel, aplicar papel de Assistente de Produção com acesso mínimo às informações necessárias.
- Permitir reordenar a fila para uma urgência quando pedidos anteriores tiverem folga suficiente para continuar dentro das datas prometidas; registrar a decisão e impedir troca que cause atraso confirmado.
- Urgência depende de avaliação humana e acrescenta 30%, com mínimo de €10. Convite Padrão urgente: até 12 horas; Complexo: até 24 horas após Briefing completo. Físico urgente: prazo individual, sem garantia do tempo da transportadora.

### 3.10 Busca e SEO

- Modelar tema demonstrado, ocasião e grafias alternativas como atributos de descoberta. Para convites, esses atributos representam exemplares e não limitam o tema em texto livre informado no Pré-formulário.
- Separar exatos, semelhantes e ausência de resultado.
- Definir URLs estáveis e metadados localizados.
- Gerar sitemap, canonicals e marcação estruturada.

### 3.11 Compatibilidade responsiva

- Implementar um único site responsivo para computador, tablet e celular.
- Suportar formalmente as duas versões estáveis mais recentes de Chrome, Safari, Edge e Firefox em desktop, Safari em iPhone/iPad e Chrome em Android.
- Validar as jornadas essenciais desde 320 px, com toque, mouse, teclado, zoom, mudança de orientação e tecnologias assistivas.
- Evitar indexação de filtros infinitos, páginas privadas e estados transacionais.

### 3.12 Migração dos catálogos

Os dois acervos atuais — convites/produtos digitais e papelaria personalizada — devem ser inventariados separadamente e migrados para uma taxonomia unificada.

Sequência:

1. inventariar fontes, produtos, identificadores, mídia e estados de publicação;
2. classificar categoria, modalidade, Família de Convite, formato, tema demonstrado e ocasião, registrando licença ou autorização de qualquer ativo protegido;
3. normalizar nomes, preços, quantidades, materiais, prazos e grafias alternativas;
4. enriquecer busca, compatibilidades, complementos, Briefings e Fichas de Produção;
5. revisar licenças, privacidade e autorização de publicação;
6. validar amostras por modalidade e relatórios de contagem;
7. congelar ou controlar alterações no acervo de origem durante a carga final;
8. executar carga, reconciliar totais e preservar rollback.

### 3.13 Internacionalização

- Tratar idioma como propriedade explícita de conteúdo e comunicação.
- Evitar textos fixos diretamente nos componentes.
- Definir fallback editorial controlado; não misturar idiomas silenciosamente.
- Localizar datas, moeda, números, endereços, políticas, e-mails e respostas do suporte.
- Preservar o Carrinho e o Pedido ao trocar idioma.
- Sugerir idioma e moeda por localização aproximada sem solicitar localização precisa como requisito.
- Persistir e priorizar a escolha manual da cliente.
- Usar EUR como moeda-base e cobrar em EUR, GBP, USD, CHF e BRL, sujeito à prova do provedor; atualizar conversões do catálogo diariamente, bloquear a taxa definitiva por 30 minutos no Checkout e exigir nova confirmação após expiração; reembolsar o mesmo valor nominal na moeda paga e conciliar taxas.
- Preservar no Pedido a moeda, taxa aplicada e valor de referência necessários à contabilidade.

### 3.14 Segurança e continuidade

As fontes recomendam:

- OWASP ASVS nível 2;
- TLS e HSTS;
- proteção contra CSRF, injeção, abuso e automação maliciosa;
- política de segurança de conteúdo;
- armazenamento seguro de segredos;
- senhas com algoritmo moderno e MFA administrativo;
- RBAC e privilégio mínimo;
- auditoria de operações sensíveis;
- backups protegidos e restauração testada;
- análise de dependências, revisão de código, testes de segurança e pentest antes do lançamento.

A arquitetura deve converter esses tópicos em controles verificáveis e evidências de teste.

## 4. Dados e privacidade

### 4.1 Categorias sensíveis para o contexto

- identidade e contato;
- endereço e NIF;
- dados de evento;
- fotografias de crianças;
- Miniaturas e arquivos enviados;
- Prévias e Artes;
- conversas de suporte;
- rastreamento e Fatura;
- preferências de comunicação;
- consentimentos de marketing ou reutilização.

### 4.2 Política-base de retenção

- Carrinhos e Pré-formulários abandonados: 30 dias.
- Fotografias de crianças, Miniaturas e uploads sensíveis: 30 dias após a entrega final.
- Reutilização dos mesmos arquivos em recompra: até 12 meses, mediante autorização separada e revogável.
- Briefings, Prévias, versões e arquivos de Produção: 12 meses após a conclusão.
- Convite final personalizado: acesso por 12 meses; Produto Digital Pronto: acesso vitalício conforme licença.
- Histórico do Chat: 12 meses após encerramento.
- Logs técnicos e de auditoria: 12 meses, salvo incidente ou obrigação legal.
- Backups: rotação máxima de 90 dias.
- Faturas e registros fiscais: prazo legal aplicável, com referência inicial de 10 anos.
- Garantia, disputa e obrigação legal: apenas dados necessários, durante o prazo justificável e com acesso restrito.

A tabela será validada por responsável jurídico/RGPD e contabilista. Retenção para recompra não deve ser confundida com consentimento para marketing ou publicação.

## 5. Dependências abertas

- selecionar e provar o provedor central de pagamentos, incluindo moedas, câmbio, MB WAY, carteiras digitais, repasses, reembolsos, disputas e eventual centralização do PayPal;
- aprovar a matriz fiscal por país e modalidade com contabilista/fiscalista;
- validar juridicamente as políticas B2C, RGPD, retenção e propriedade intelectual;
- concluir o inventário de licenças e autorizações do acervo antes da publicação;
- concluir o mapeamento entre Família de Convite, formato, recursos e classificação Padrão/Complexo;
- configurar limites de frete grátis para ilhas portuguesas e demais destinos da União Europeia;
- escolher e provar os demais fornecedores críticos listados no Gate F0.

## 6. Sequenciamento recomendado

1. Fechar regras comerciais, fiscais, legais e operacionais.
2. Modelar catálogo, modalidades, estados e identidade do Pedido.
3. Selecionar e validar provedores críticos.
4. Construir descoberta, produto, Carrinho e Checkout.
5. Implementar conta pós-compra, Briefing, versões e Aprovação.
6. Implementar Produção, qualidade, envio e entrega digital.
7. Implementar Administração, exceções e suporte.
8. Aplicar idiomas, acessibilidade, observabilidade e hardening transversalmente, não apenas no fim.
9. Migrar catálogo e conteúdo final.
10. Executar gates M1 e M2, testes de restauração e reversão.

## 7. Alternativas rejeitadas ou superadas

- **Loja de moda:** classificação incorreta, rejeitada pela proprietária.
- **SEO somente após o núcleo:** superado; Google é entrada principal e SEO essencial integra o MVP.
- **Chat automático após o núcleo:** superado; Atendimento Automático com escalonamento humano integra o MVP.
- **Espanhol posterior:** superado; português do Brasil, inglês e espanhol integram o lançamento.
- **Convites com prazos de até 72 horas ou 3–5 dias:** superado pela decisão atual de até 24 horas para Convite Padrão e até 48 horas para Convite Complexo.
- **WhatsApp como fluxo de compra:** rejeitado; é suporte e canal opcional de entrega/comunicação.
- **Urgência automática:** rejeitada; exige avaliação humana.
- **“Pagamentos 100% seguros”:** rejeitado por ser promessa absoluta.
