# PRD Quality Review — Loja Online JS Designs

## Overall verdict

O PRD tem uma tese clara, jornadas específicas e uma estrutura de requisitos funcionais excepcionalmente útil para um produto ainda em definição; não é um documento de fachada e preserva bem as tensões entre autonomia, personalização, operação e confiança. Ainda assim, não está pronto para sinal verde de construção: decisões comerciais, fiscais, legais e de catálogo permanecem no Gate F0, enquanto vários requisitos não funcionais e promessas de serviço ainda não possuem limites verificáveis.

## Decision-readiness — thin

As decisões já tomadas estão visíveis e coerentes: compra como visitante, Briefing pós-pagamento, Aprovação Final como gatilho da Produção, três Rodadas de Alteração, prazos de 24/48 horas, sete dias de Produção, atendimento automático controlado e operação sem planilhas. O adendo também registra alternativas rejeitadas e decisões superadas, o que torna o histórico compreensível para quem questionar por que SEO, Chat e três idiomas entraram no MVP.

Entretanto, o próprio Gate F0 (§11) impede iniciar o núcleo transacional antes de fechar taxonomia, regras comerciais, países, moeda, fiscalidade, políticas, papéis e fornecedores. As 16 perguntas de §12 incluem decisões que afetam contrato, modelo de dados, arquitetura, preço e critérios de aceite; são bloqueadores reais, não detalhes de acabamento. A separação em Gate F0 é honesta, mas confirma que o documento ainda não habilita a decisão de construir.

### Findings

- **high** Gate F0 concentra decisões estruturais ainda abertas (§11 Gate F0; §12 questões 1, 2, 5, 7–15) — Países e moeda, IVA/OSS, políticas por modalidade, licença digital, capacidade, propriedade intelectual, alterações pagas, pedidos mistos e regras de catálogo afetam diretamente arquitetura, UX, conteúdo legal, preço e operação. O PRD nomeia corretamente o bloqueio, mas ainda não contém as decisões necessárias para autorizar implementação do núcleo. *Fix:* resolver o Gate F0 em um registro de decisões aprovado e converter cada resultado em regra do PRD ou dependência com proprietário, data e condição de desbloqueio.
- **medium** Taxonomia comercial ainda não sustenta o catálogo final (§3 “Família de Convite” e “Tipo de Convite”; FR-8) — O PRD preserva quatro famílias e oito formatos, mas deixa para o Gate F0 o mapeamento entre família, formato, recursos e classificação Padrão/Complexo. Sem esse mapa, preço, prazo, filtro, página de produto, Briefing e Ficha de Produção não podem compartilhar uma regra inequívoca. *Fix:* adicionar uma definição normativa do modelo comercial ou declarar explicitamente quais campos e combinações serão válidos, com exemplos por classe de prazo.
- **medium** Uma pergunta aberta já tem resposta normativa no próprio PRD (§12 questão 4; FR-16 e FR-37) — A questão pergunta se o Suporte Online ficará oculto no Checkout ou será um link compacto, mas FR-16 já determina que “Chat e WhatsApp flutuantes ficam ocultos” e que apenas ajuda não intrusiva permanece. Isso é uma falsa abertura e pode reabrir uma decisão já tomada. *Fix:* remover a questão ou reformulá-la apenas para definir a forma da ajuda não intrusiva permitida por FR-16.

## Substance over theater — strong

O conteúdo é específico à JS Designs. As jornadas fazem trabalho real: UJ-1 fixa os relógios de Briefing, Arte, Aprovação e Produção; UJ-2 separa Convite Padrão e Complexo; UJ-3 distingue arquivo digital pronto; UJ-4 demonstra a operação administrativa; UJ-5 delimita suporte automático e escalonamento. Mariana, Beatriz, Camila e Sharom não formam uma seção de personas decorativa; carregam contexto nos momentos em que uma decisão muda.

Os NFRs e guardrails também nascem dos riscos concretos do produto: fotos de crianças, Miniaturas, uploads, Fatura, NIF, PCI DSS, autorização de personagens, entrega digital e operação europeia. A visão — “unir a praticidade de uma compra online à atenção e ao acabamento de um trabalho personalizado” — não poderia ser transferida para qualquer e-commerce sem perder sentido. Não há achados substantivos nesta dimensão.

## Strategic coherence — adequate

A tese é consistente: tornar autônoma uma compra que hoje tende a exigir conversa, sem perder confiança e controle operacional. Descoberta por Google, informação progressiva, Briefing, Aprovação, Próxima Ação, Ficha de Produção e acompanhamento formam uma cadeia coerente; as métricas medem tanto conversão quanto autonomia, prazo, qualidade, suporte e acessibilidade. As contramétricas de §10.3 protegem explicitamente contra conversão obtida por confusão, velocidade obtida por erro e automação obtida por pior atendimento.

O risco estratégico está na amplitude: §6.1 declara simultaneamente obrigatórios catálogo, SEO, três idiomas, três modalidades, Checkout, pagamento, fiscalidade, frete europeu, Área da Cliente, criação/aprovação, suporte automático/humano, Administração completa, instrumentação e hardening. Essa decisão veio da proprietária e deve ser respeitada, mas o PRD não explicita o custo de oportunidade nem uma ordem de corte dentro do MVP caso capacidade, fornecedores ou conformidade atrasem.

### Findings

- **medium** MVP obrigatório não explicita trade-off de capacidade (§6.1; §9.2 “Sobrecarga operacional”; addendum §6) — As nove áreas obrigatórias compõem quase uma plataforma completa, e o sequenciamento técnico do adendo não define qual fatia funcional deve ficar operacional primeiro nem o que acontece se um provedor ou frente regulatória atrasar. “Tudo obrigatório” é uma decisão válida, mas o documento não nomeia prazo, orçamento, equipe ou concessão aceita para sustentá-la. *Fix:* registrar o trade-off assumido e definir uma ordem de integração vertical por modalidade, mantendo Gate M1 como barreira de lançamento sem transformar todas as frentes em trabalho simultâneo.

## Done-ness clarity — thin

Os 50 FRs seguem uma disciplina forte: cada um declara uma capacidade e quase todos apresentam duas ou mais consequências verificáveis. Estados, CTAs, prazos, contadores, regras de duplicação, privacidade, papéis e histórico são concretos. Gate M1 exige uma encomenda realista ponta a ponta por modalidade e Gate M2 inclui falhas, repetição, segurança, acessibilidade, restauração e reversão.

A fragilidade está nos NFRs e em algumas palavras de serviço. NFR-1 e NFR-5 têm referências e critérios sólidos, mas desempenho, disponibilidade, continuidade, observabilidade e compatibilidade ainda dependem de termos como “representativa”, “estado seguro”, “protegidas”, “acionáveis” e “versões suportadas”. Para um PRD de lançamento que alimenta arquitetura, histórias e testes, esses termos não definem done.

### Findings

- **high** NFRs críticos não têm limites operacionais verificáveis (§7 NFR-2, NFR-3, NFR-9, NFR-11 e NFR-12; §12 questão 16) — O desempenho usa um pressuposto móvel de Core Web Vitals “vigentes na implementação”; disponibilidade não tem SLO; continuidade não define RPO/RTO; observabilidade não define tempo de detecção/alerta; compatibilidade não define matriz ou janela de versões. Engenharia e QA não conseguem concluir o aceite de confiabilidade a partir dessas frases. *Fix:* antes de histórias não funcionais, definir ambiente de medição e limites para CWV, disponibilidade das jornadas críticas, RPO/RTO, latência de detecção/alerta e política de navegadores/dispositivos.
- **medium** Personalização básica é simultaneamente incluída e dispensável (FR-7 e FR-11) — FR-7 afirma que nome e idade incluídos são distinguidos de customizações pagas, enquanto FR-11 pede que a cliente possa “ativar ou dispensar personalização básica”. Não está claro se dispensar apenas remove nome/idade sem alterar preço, se cria uma variante não personalizada ou se a inclusão depende do produto. *Fix:* definir estados válidos por modalidade e o efeito de cada escolha em preço, Briefing, Produção e exibição na página.
- **medium** Promessas de entrega e lembrete não têm SLA mínimo (UJ-3; FR-24; FR-35) — “Imediatamente”, “sem espera” e “lembretes configuráveis” não indicam em quanto tempo a entrega automática deve ocorrer, quantas tentativas são feitas, quando a falha vira exceção ou qual cadência padrão protege o prazo de três dias úteis. *Fix:* definir janela máxima de entrega digital após confirmação, política de retentativa/escalonamento e cadência mínima de lembretes.

## Scope honesty — adequate

O documento é transparente sobre o que entra, o que fica fora e o que vem depois. §6.2 evita omissões silenciosas; §6.3 protege o MVP de Favoritos, avaliações, busca preditiva e automações adicionais; §12 lista decisões em aberto; §13 reúne pressupostos; o adendo separa mecanismo técnico e registra alternativas superadas. O Gate F0 explicita que as perguntas estruturais não podem ser empurradas para implementação.

A classificação é “adequate”, e não “strong”, porque a densidade de itens abertos é alta para um produto de lançamento e duas entradas do Índice de pressupostos não fazem roundtrip com marcações inline. A honestidade está presente, mas o PRD ainda precisa concluir a triagem prevista no próprio fluxo de finalização.

### Findings

- **low** Índice de pressupostos não faz roundtrip completo (§13; UJ-3; NFR-2) — FR-35 e NFR-2 possuem `[PRESSUPOSTO]` inline e estão indexados. Já “Miniatura criada por €5...” e “linha visual do protótipo...” aparecem no índice sem uma marcação equivalente no ponto normativo correspondente; a direção visual inclusive é declarada de forma afirmativa em §4.3. *Fix:* marcar inline o que realmente for pressuposto, converter decisões confirmadas em texto normativo e remover do índice tudo que não for inferência pendente.

## Downstream usability — adequate

O documento foi claramente escrito para alimentar UX, arquitetura, épicos e testes. Há Glossário, UJs nomeadas, FRs contínuos de FR-1 a FR-50, agrupamentos funcionais compreensíveis, condições verificáveis e um adendo que preserva mecanismo sem contaminar o corpo principal. Seções como busca, Aprovação, entrega digital e operação podem ser extraídas isoladamente com pouco contexto perdido.

A lacuna principal é que as métricas não têm IDs estáveis nem definições formais. A estratégia de baseline é coerente, mas histórias de instrumentação, dashboards e Gate M2 terão de referenciar frases e posições, enquanto FRs e UJs podem ser referenciados por identificador. Além disso, o mapeamento aberto entre Família, formato e Tipo de Convite ainda limita extração segura do domínio de catálogo.

### Findings

- **medium** Métricas de sucesso não possuem IDs ou definições operacionais (§10; FR-49 e FR-50) — A lista mede impressões, visitas qualificadas, abandono, autonomia, prazo, suporte e acessibilidade, mas não define numerador, denominador, janela, fonte, proprietário ou identificadores SM. Isso dificulta referenciar métricas em histórias, eventos, painéis e gates e permite interpretações diferentes de “visita qualificada”, “prazo cumprido” e “conclusão da jornada”. *Fix:* criar SMs com IDs estáveis e, para cada uma, definição, segmentação, fonte, janela e proprietário; valores-alvo podem continuar diferidos até o fim do baseline.

## Shape fit — strong

O formato é adequado a um produto B2C de experiência significativa com uma operação interna igualmente importante. As cinco jornadas são justificadas: três modalidades de compra, uma jornada operacional e uma jornada de suporte. Todas têm protagonista nomeada, contexto, estado de entrada, sequência, clímax, resolução e exceção; não há UJ flutuante nem persona separada que não influencie requisitos.

A combinação de narrativa de jornada, capacidades agrupadas, NFRs transversais, guardrails europeus, escopo e gates serve bem a uma cadeia UX → arquitetura → épicos/histórias. O adendo absorve detalhes de mecanismos, migração e sequenciamento sem forçar o PRD a virar desenho de solução. Não há achados substantivos nesta dimensão.

## Mechanical notes

- IDs de requisitos funcionais estão contínuos e únicos de FR-1 a FR-50.
- IDs de jornadas estão contínuos e únicos de UJ-1 a UJ-5; todas as jornadas têm protagonista nomeada.
- Não existem IDs SM para as métricas de §10.
- O Índice de pressupostos não faz roundtrip completo: duas marcações inline estão indexadas, mas duas entradas do índice não aparecem marcadas no corpo.
- A numeração estrutural salta de §1 para títulos sem número (“Visão confirmada”, “Estratégia inicial de medição”, “Jornadas confirmadas”) e depois para §3; isso não quebra referências atuais, mas deve ser normalizado antes da publicação.
- “Família de Convite”, “Tipo de Convite”, “Convite Padrão” e “Convite Complexo” estão definidos, porém o relacionamento normativo entre eles permanece pendente no Gate F0.
- FR-16 resolve ocultação de Chat e WhatsApp no Checkout, mas §12 questão 4 ainda apresenta essa decisão como aberta.
- Não há `[NOTE FOR PM]`; as tensões reais estão concentradas em Gate F0, Dependências e Perguntas abertas, o que é funcional, embora menos local.

