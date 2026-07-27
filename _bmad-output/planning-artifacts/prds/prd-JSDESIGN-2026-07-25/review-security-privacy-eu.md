# Revisão adversarial — segurança, privacidade e comércio europeu

## Escopo

Artefatos revisados:

- `prd.md`;
- `addendum.md`.

Domínios: RGPD, dados e imagens de crianças, consentimento, retenção, pagamentos e PCI DSS, uploads, conta e autorização, Chat/WhatsApp, conteúdo digital, consumo B2C na UE, produtos personalizados, IVA/faturação, frete europeu, propriedade intelectual, acessibilidade e gates.

Validações externas usam exclusivamente fontes oficiais da União Europeia, EDPB, Comissão Europeia, EUR-Lex, W3C e PCI Security Standards Council. Esta revisão identifica requisitos de produto e evidências necessárias; não substitui parecer jurídico em Portugal e nos países efetivamente atendidos.

## Veredito

**NÃO APROVADO PARA FINALIZAÇÃO OU LANÇAMENTO.**

Não há falha crítica irremediável no conceito, e a base é substancialmente melhor do que a média: o PRD já inclui privacidade por design, arquivos privados, RGPD, PCI, segurança de aplicação, consentimento separado para marketing, conteúdo digital, personalizados, acessibilidade e gates F0/M2.

Contudo, sete achados altos impedem considerar os requisitos seguros para lançamento europeu. Os maiores bloqueios são: governança RGPD incompleta, tratamento de dados de crianças sem base e autoridade documentadas, retenção ainda aberta, acesso pós-compra pouco verificável, regras B2C ainda não transformadas em fluxos, ausência de segurança geral de produto e fiscalidade transfronteiriça sem decisão.

## Contagem

| Severidade | Quantidade |
|---|---:|
| Crítica | 0 |
| Alta | 7 |
| Média | 6 |
| Baixa | 2 |
| **Total** | **15** |

## Achados críticos

Nenhum achado crítico no nível de PRD. O veredito negativo decorre da combinação de achados altos e perguntas abertas que bloqueiam o Gate F0.

## Achados altos

### H-1 — O modelo de governança RGPD não é verificável

**Localização:** `prd.md` NFR-7 (linhas 794–796), guardrails (818–829), dependências (833–844), Gate F0/M2 (890–908); `addendum.md` 4.1–4.2 (220–250).

**Risco:** o texto exige “RGPD por design”, mas não exige explicitamente um inventário de operações com finalidade, titular, categoria de dados, base legal, destinatários, transferência, prazo e controle. Também não exige registro das atividades, triagem de DPIA, contratos com operadores, procedimento completo de direitos do titular ou fluxo de violação de dados. “Política de privacidade aprovada” não demonstra accountability.

O EDPB lista como responsabilidades do controlador: princípios do art. 5, direitos, registros, segurança, contratos com operadores, notificação de violações, DPIA quando necessária e transferências internacionais. A Comissão informa que pedidos de titulares devem ser respondidos, em princípio, em um mês.

**Correção recomendada:**

- acrescentar ao Gate F0 um mapa de tratamento aprovado por finalidade e base legal;
- exigir registro das atividades aplicável e triagem documentada de DPIA;
- exigir fluxo para informação, acesso, retificação, eliminação, restrição, portabilidade e oposição, com verificação de identidade e prazo;
- acrescentar ao Gate M2 ensaio de pedido de titular e de violação de dados, incluindo decisão e notificação em até 72 horas quando aplicável;
- manter mecanismos e modelos contratuais no addendum/arquitetura.

**Fontes oficiais:** [EDPB — controlador e operador](https://www.edpb.europa.eu/sme/learn-the-basics/data-controller-or-data-processor_en), [Comissão Europeia — pedidos de titulares](https://commission.europa.eu/law/law-topic/data-protection/information-business-and-organisations/dealing-requests-individuals_en), [EDPB — violações de dados](https://www.edpb.europa.eu/system/files/2025-03/edpb_summary_092022-012021_data_breach_en.pdf).

### H-2 — Fotografias e dados de crianças não têm base, autoridade e transparência suficientemente definidas

**Localização:** `prd.md` NFR-8 (798–800), guardrails (824–826), FR-32 (fotografia final); `addendum.md` 4.1 (222–233).

**Risco:** o sistema pode receber nome, idade, fotografia e outros dados de uma criança fornecidos por um adulto. O PRD protege acesso e marketing, mas não define:

- quem é o titular de cada dado;
- como o adulto declara ter autoridade para fornecer a imagem/dados;
- qual base legal sustenta criação, prova, suporte, conservação e reutilização;
- como informação e direitos serão oferecidos quando o titular é a criança;
- como reduzir ou evitar fotografia quando ela não é necessária.

Consentimento separado para marketing é correto, porém não resolve a licitude do tratamento operacional nem autoriza automaticamente dados de terceiro. O EDPB orienta que a base legal seja escolhida por finalidade, que dados sejam minimizados e que comunicações dirigidas a crianças sejam claras.

**Correção recomendada:**

- mapear separadamente cliente adulta, criança retratada e destinatários;
- exigir confirmação de autoridade/legitimidade do uploader e aviso contextual antes do upload;
- definir base legal por finalidade sem usar consentimento como solução genérica;
- minimizar nome completo, data exata e imagem; permitir alternativas sem foto quando viável;
- incluir revogação apenas onde a base é consentimento, sem prometer apagar registros sujeitos a obrigação legal;
- exigir avaliação específica de risco/DPIA antes do Gate M2.

**Fontes oficiais:** [EDPB — tratamento lícito](https://www.edpb.europa.eu/sme/be-compliant/process-personal-data-lawfully_en), [EDPB — direitos e linguagem para crianças](https://www.edpb.europa.eu/sme/be-compliant/respect-individuals-rights_ga).

### H-3 — A política de retenção continua aberta para todos os dados de maior impacto

**Localização:** `prd.md` NFR-7/NFR-8 (794–800), guardrail 826, Gate F0 894, pergunta aberta 12 (923); `addendum.md` 4.2 (235–250).

**Risco:** o PRD depende de uma política futura para Briefings, fotos de crianças, Miniaturas, Prévias, arquivos finais, Chat, logs, Faturas e backups. Sem prazos e fundamentos, não é possível desenhar exclusão, backup, recuperação, exportação, contrato com fornecedor nem aceite.

O Gate F0 menciona retenção, mas o Gate M2 apenas diz “privacidade aprovada”, sem evidência de execução ponta a ponta.

**Correção recomendada:**

- manter este item como bloqueador explícito do F0;
- exigir tabela por categoria/finalidade com prazo ou critério, fundamento, início da contagem, exceção legal e responsável;
- exigir propagação de exclusão a caches, derivados, links, operadores e backups conforme política;
- separar retenção fiscal/legal, operação do pedido, recuperação de download, recompra, marketing e defesa de reclamações;
- no M2, testar expiração, exclusão, suspensão por litígio e restauração sem reintrodução indevida.

**Fonte oficial:** [EDPB — fundamentos e política interna de retenção](https://www.edpb.europa.eu/sme/learn-the-basics/data-protection-basics_en).

### H-4 — “Acesso seguro” à Área da Cliente não possui critérios suficientes para proteger pedidos, endereço, NIF e fotos

**Localização:** `prd.md` FR-17 (394–401), FR-30 a FR-36, NFR-8; `addendum.md` 3.5 (140–145).

**Risco:** criação automática de conta ou link pós-compra concentra dados fiscais, endereço, arquivos e fotografias. O PRD não torna verificáveis:

- prova de posse do e-mail;
- validade e uso único de links;
- ativação e troca de credencial;
- expiração e revogação de sessões;
- reautenticação para download, alteração de e-mail/endereço ou eliminação;
- proteção contra tomada de conta e enumeração além do número/e-mail;
- revogação de acesso após mudança de titularidade ou fraude.

Sem esses critérios, uma arquitetura pode cumprir literalmente “acesso seguro” e ainda expor pedidos.

**Correção recomendada:**

- adicionar condições de aceite de verificação de posse, links temporários de uso único, sessões revogáveis e reautenticação em ações sensíveis;
- testar autorização horizontal e vertical para Pedido, Fatura, foto, Prévia, arquivo e painel;
- exigir fluxo de alteração de e-mail e recuperação com notificações e bloqueio de abuso;
- proibir suporte de contornar controles de identidade.

### H-5 — Direitos do consumidor estão corretos em princípio, mas não especificados como fluxos por modalidade

**Localização:** `prd.md` FR-9 (311–318), FR-16 (384–392), guardrails (820–829), pergunta aberta 9 (920); `addendum.md` decisão comercial 261.

**Risco:** a UE exige informação pré-contratual clara, confirmação durável do contrato, botão que indique obrigação de pagar, regras de desistência, reembolso e garantia legal. Conteúdo digital com entrega imediata exige consentimento prévio expresso para iniciar e reconhecimento da perda do direito de desistência, confirmados no contrato. Produtos claramente personalizados podem ser exceção ao desistimento, mas continuam sujeitos a direitos por falta de conformidade.

O PRD registra esses princípios, mas deixa política por modalidade em aberto e não define fluxos para Pedido misto, produto não personalizado, personalizado, convite/serviço criativo e download imediato.

**Correção recomendada:**

- tornar a política por modalidade bloqueadora do F0;
- definir informação pré-contratual, formulário/canal de desistência, prazos, devolução, custo de retorno, reembolso, reclamação e garantia;
- registrar consentimento expresso e reconhecimento do download imediato separadamente e incluí-los na confirmação enviada;
- não usar uma caixa genérica para renunciar direitos em itens mistos;
- testar cancelamento/reembolso parcial de Pedido misto e não conformidade após Aprovação Final;
- validar o texto “Finalizar e comprar” no direito nacional como indicação inequívoca de obrigação de pagar.

**Fontes oficiais:** [UE — comércio eletrônico B2C](https://europa.eu/youreurope/business/selling-in-eu/selling-goods-services/ecommerce-distance-selling/index_en.htm), [UE — garantias legais](https://europa.eu/youreurope/business/selling-in-eu/consumer-contracts-guarantees/consumer-guarantees/index_en.htm).

### H-6 — O PRD omite a segurança geral dos produtos físicos e as informações obrigatórias da oferta online

**Localização:** ausência em FR-7, Administração, guardrails e gates.

**Risco:** lembrancinhas, topos, caixas e peças de papelaria são produtos físicos vendidos a consumidores europeus e podem ser usados por crianças. O Regulamento (UE) 2023/988 exige produtos seguros e, na oferta online, identificação do fabricante, endereço postal/eletrônico, identificação do produto e advertências/informações de segurança compreensíveis no Estado-Membro.

Riscos previsíveis incluem peças pequenas, pontas, fitas, cordões, ímãs, adesivos, materiais inflamáveis ou aparência semelhante a alimento. O catálogo atual não exige avaliação de segurança, rastreabilidade, lote/identificador, avisos, recall ou registro de acidente.

**Correção recomendada:**

- adicionar capacidade de dados de segurança por produto, material e público;
- exibir fabricante/operador, identificador e advertências aplicáveis na página do produto;
- exigir avaliação documentada de risco e rastreabilidade de materiais/lotes quando aplicável;
- criar fluxo de incidente, retirada/recall e comunicação à cliente;
- incluir GPSR no Gate F0 e ensaio de retirada no M2.

**Fonte oficial:** [EUR-Lex — Regulamento Geral de Segurança dos Produtos, arts. 4–6 e 19–20](https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=celex%3A32023R0988).

### H-7 — Países, IVA/OSS, prova de localização, Fatura e frete ainda não formam uma regra comercial executável

**Localização:** `prd.md` FR-20/FR-21 (422–438), dependências (835–843), Gate F0 894, perguntas 1, 10 e 14; `addendum.md` 3.3–3.4 (128–138).

**Risco:** “toda a Europa” não é uma unidade fiscal ou logística. Bens físicos e serviços/conteúdo eletrônico podem ter tratamentos distintos; vendas B2C transfronteiriças podem usar tributação no destino e OSS. O sistema precisa determinar país atendido, moeda, evidência de localização, alíquota, preço exibido, documento fiscal, retenção de registros, restrição de destino e responsabilidade por taxas.

As fontes oficiais indicam regras específicas de IVA transfronteiriço e OSS, inclusive registro por até dez anos para operações declaradas no OSS. O PRD reconhece a lacuna, mas não impede explicitamente a expressão “toda a Europa” antes da matriz estar aprovada.

**Correção recomendada:**

- substituir a promessa absoluta por “países europeus atendidos” até a matriz final;
- fechar por modalidade: país, moeda, IVA, OSS, evidência de localização, Fatura/nota de crédito, retenção e reembolso;
- separar cálculo fiscal e logístico de físicos, convites personalizados e Produtos Digitais Prontos;
- exigir teste por país representativo, falha de cotação e Pedido misto;
- validar em Portugal a obrigação e o formato da Fatura B2C.

**Fontes oficiais:** [UE — IVA transfronteiriço](https://europa.eu/youreurope/business/taxation/vat/cross-border-vat/index_en.htm), [UE — OSS](https://europa.eu/youreurope/business/taxation/vat/one-stop-shop/index_en.htm), [UE — faturação e IVA](https://europa.eu/youreurope/business/taxation/vat/charging-deducting-vat/indexamp_en.htm).

## Achados médios

### M-1 — Fornecedores, WhatsApp e Chat não têm requisitos de operador, subprocessador e transferência internacional

**Localização:** `prd.md` FR-34, FR-36 a FR-40, dependências; `addendum.md` 3.8.

**Risco:** consentimento para contexto e minimização são úteis, mas não substituem classificação de papéis, contrato de operador, subprocessadores, localização, transferência para fora do EEE, retenção, exportação e eliminação. WhatsApp pode atuar sob termos próprios em partes do tratamento.

**Correção:** exigir due diligence e mapa por fornecedor, contrato adequado, mecanismo de transferência quando necessário, lista de subprocessadores, retenção, resposta a direitos e plano de saída. Manter Pedido/Área da Cliente como fonte principal e enviar ao WhatsApp apenas o mínimo.

**Fonte oficial:** [EDPB — contratos, subprocessadores e transferências](https://www.edpb.europa.eu/sme/learn-the-basics/data-controller-or-data-processor_en).

### M-2 — Terceirizar o pagamento não encerra o escopo PCI da loja

**Localização:** `prd.md` FR-18 e NFR-6; `addendum.md` 3.1.

**Risco:** “parceiro certificado” e não armazenar cartão são necessários, mas o escopo depende da integração. Página incorporada pode exigir confirmação de proteção contra ataques de script; redirecionamento e iframe têm implicações diferentes. Falta aceite de qual SAQ/validação se aplica, evidência anual e responsabilidade do adquirente.

**Correção:** no Gate F0, registrar arquitetura de pagamento e escopo PCI com adquirente/parceiro; no M2, exigir evidência de conformidade do provedor e da JS Designs, inventário/controle de scripts quando aplicável e teste de adulteração da página.

**Fonte oficial:** [PCI SSC — critérios SAQ A para comércio eletrônico](https://blog.pcisecuritystandards.org/faq-clarifies-new-saq-a-eligibility-criteria-for-e-commerce-merchants).

### M-3 — Cookies, analytics e cupom por e-mail não possuem comportamento de consentimento

**Localização:** `prd.md` FR-14, FR-49, política de cookies e NFR-3; protótipo/referência no addendum.

**Risco:** “com consentimento adequado” é genérico. O produto mede aquisição e comportamento, oferece cupom por e-mail e poderá usar newsletter/recuperação. Falta separar cookies estritamente necessários, analytics, marketing, prova de consentimento, recusa tão fácil quanto aceitação e continuidade das jornadas sem consentimento opcional.

**Correção:** adicionar requisitos de preferência granular, ausência de tags opcionais antes da escolha, revogação, registro e degradação sem bloqueio; separar e-mail operacional, cupom, newsletter e recuperação.

### M-4 — WCAG 2.2 AA é forte, mas a aplicabilidade da Lei Europeia de Acessibilidade não foi avaliada

**Localização:** `prd.md` NFR-1, NFR-10, Gate M2.

**Risco:** serviços de comércio eletrônico estão no âmbito da Diretiva (UE) 2019/882, aplicável a serviços colocados no mercado após 28 de junho de 2025, com possíveis exceções nacionais para microempresas prestadoras de serviços. Apenas declarar WCAG não cobre automaticamente informação acessível, suporte, documentação e deveres nacionais.

**Correção:** determinar estatuto e transposição portuguesa; manter WCAG 2.2 AA como padrão de produto mesmo se houver exceção; exigir auditoria manual e assistiva, declaração/processo acessível e canal de suporte equivalente nos três idiomas.

**Fontes oficiais:** [UE — produtos e serviços acessíveis](https://europa.eu/youreurope/business/selling-in-eu/selling-goods-services/accessibility/index_en.htm), [EUR-Lex — Diretiva (UE) 2019/882](https://eur-lex.europa.eu/eli/dir/2019/882/oj/eng).

### M-5 — Conteúdo digital não define remédio para arquivo incompatível, corrompido ou divergente

**Localização:** `prd.md` FR-9, FR-35, guardrails 821–823, perguntas 5–6.

**Risco:** entrega automática e perda de desistimento não eliminam direitos relativos a conteúdo defeituoso ou diferente do anunciado. O PRD mede falha de entrega, mas não exige validação de integridade, versão, compatibilidade prometida, substituição ou reembolso.

**Correção:** definir hash/integridade ou verificação equivalente, versão entregue, compatibilidade suportada, canal de reclamação e remédio por não conformidade; separar “não consigo usar por incompatibilidade não informada” de simples mudança de ideia.

**Fonte oficial:** [UE — direitos e garantias de consumo](https://europa.eu/youreurope/citizens/consumers/shopping/shopping-consumer-rights/index_en.htm).

### M-6 — Propriedade intelectual está tratada como política, não como controle operacional

**Localização:** `prd.md` guardrail 824, risco 852, pergunta 11; `addendum.md` decisão 266.

**Risco:** catálogo e uploads podem conter personagens, músicas, fontes e imagens protegidos. “Governança” e “processo de retirada” não definem origem, licença, território, duração, prova, autorização do cliente ou bloqueio de publicação.

**Correção:** exigir registro de proveniência/licença por ativo, declaração do cliente sobre direitos de conteúdo enviado, revisão antes de publicação/marketing, expiração de licença e procedimento de denúncia/retirada com preservação de evidência.

## Achados baixos

### L-1 — “Categorias sensíveis” pode ser confundido com categorias especiais do art. 9 RGPD

**Localização:** `addendum.md` 4.1.

**Risco:** fotografias de crianças são de alto impacto contextual, mas uma fotografia comum não é automaticamente dado biométrico de categoria especial; isso depende do processamento técnico para identificação única.

**Correção:** renomear a seção para “Dados de maior impacto no contexto” e classificar separadamente categorias especiais/biometria quando realmente aplicáveis.

### L-2 — “Consentimento explícito” para a Miniatura de €5 mistura linguagem comercial e RGPD

**Localização:** `prd.md` FR-12, linha 348.

**Risco:** selecionar um adicional pago é aceitação comercial, não necessariamente consentimento como base legal de dados. A terminologia pode induzir modelagem errada de revogação e registro.

**Correção:** usar “confirmação expressa do adicional pago, nunca pré-selecionado”; reservar “consentimento” para tratamento opcional de dados quando essa for a base legal.

## Avaliação dos gates

### Gate F0

**Estado: bloqueado.**

O F0 já exige políticas, países, fiscalidade e fornecedores, mas deve incorporar ou tornar verificáveis:

- mapa de tratamento, bases legais e papéis RGPD;
- retenção completa;
- direitos do titular e violação de dados;
- avaliação de risco/DPIA para dados de crianças;
- política B2C por modalidade e Pedido misto;
- GPSR e segurança dos produtos físicos;
- matriz de países/IVA/OSS/faturação/frete;
- escopo PCI;
- governança dos fornecedores e transferências.

### Gate M1

**Estado: insuficiente para segurança sem cenários adversariais.**

Além da jornada feliz por modalidade, deve testar:

- acesso indevido entre contas;
- takeover/recuperação de conta;
- upload malicioso;
- cancelamento/reembolso parcial;
- falha e repetição de integração;
- exercício de direitos;
- produto digital defeituoso;
- retirada/recall de produto físico.

### Gate M2

**Estado: formulação ampla demais.**

“Segurança, acessibilidade, privacidade e políticas aprovadas” deve exigir evidências nomeadas:

- zero achados críticos/altos sem resolução formal;
- pentest e teste de autorização;
- restauração;
- retenção/exclusão;
- resposta a violação;
- contratos e transferências;
- acessibilidade manual/assistiva;
- aceites B2C por modalidade;
- IVA/frete por país;
- GPSR e procedimento de retirada.

## Pontos fortes a preservar

- checkout sem cadastro obrigatório;
- Área da Cliente como registro principal;
- ocultação de Chat/WhatsApp flutuantes no Checkout;
- parceiro de pagamento e não armazenamento de dados completos de cartão;
- idempotência de cobrança, Pedido e Fatura;
- arquivos privados, antimalware, links temporários e autorização;
- consentimento separado para marketing/reutilização;
- eliminação por padrão após finalidade operacional;
- distinção entre personalizados, convites e Produto Digital Pronto;
- reconhecimento específico antes do download imediato;
- garantia legal não anulada pela Aprovação Final;
- WCAG 2.2 AA e internacionalização trilíngue;
- Gate F0 antes do núcleo.

## Fontes oficiais consultadas

- [EDPB — guia para pequenas empresas](https://www.edpb.europa.eu/sme-data-protection-guide/home_en)
- [EDPB — controlador, operador e subprocessadores](https://www.edpb.europa.eu/sme/learn-the-basics/data-controller-or-data-processor_en)
- [EDPB — tratamento lícito](https://www.edpb.europa.eu/sme/be-compliant/process-personal-data-lawfully_en)
- [Comissão Europeia — direitos dos titulares](https://commission.europa.eu/law/law-topic/data-protection/information-business-and-organisations/dealing-requests-individuals_en)
- [União Europeia — comércio eletrônico B2C](https://europa.eu/youreurope/business/selling-in-eu/selling-goods-services/ecommerce-distance-selling/index_en.htm)
- [União Europeia — garantias legais](https://europa.eu/youreurope/business/selling-in-eu/consumer-contracts-guarantees/consumer-guarantees/index_en.htm)
- [EUR-Lex — Regulamento (UE) 2023/988](https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=celex%3A32023R0988)
- [União Europeia — IVA transfronteiriço](https://europa.eu/youreurope/business/taxation/vat/cross-border-vat/index_en.htm)
- [União Europeia — OSS](https://europa.eu/youreurope/business/taxation/vat/one-stop-shop/index_en.htm)
- [União Europeia — acessibilidade de produtos e serviços](https://europa.eu/youreurope/business/selling-in-eu/selling-goods-services/accessibility/index_en.htm)
- [W3C — WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/)
- [PCI SSC — SAQ A para comércio eletrônico](https://blog.pcisecuritystandards.org/faq-clarifies-new-saq-a-eligibility-criteria-for-e-commerce-merchants)

