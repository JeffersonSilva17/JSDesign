# Reconciliação de entrada — `prototype/index.html`

## Escopo

- **Entrada reconciliada:** `prototype/index.html`.
- **Artefatos comparados:** `prd.md` e `addendum.md`.
- **Limite da análise:** somente a marcação e o conteúdo de `index.html`; `app.js`, `styles.css` e os SVGs referenciados não foram tratados como entradas desta reconciliação.
- **Critério:** o protótipo é demonstrativo, não uma especificação transacional. Decisões confirmadas posteriormente no PRD prevalecem quando houver divergência.

## Resultado geral

O PRD e o adendo capturam corretamente a direção principal do protótipo: home premium e responsiva, proposta editorial, busca destacada, produtos mais pedidos, categorias, explicação da jornada, prova de acabamento, Projeto Exclusivo, Instagram, menu móvel, Carrinho e conta demonstrativos, Chat, WhatsApp e cuidados iniciais de acessibilidade.

O próprio protótipo informa que pedidos e pagamentos não são reais, e o adendo documenta de forma adequada que catálogo, páginas detalhadas, Checkout, Área da Cliente, Briefing, Aprovação, Produção, Administração, integrações e segurança ainda não estão demonstrados. Portanto, a ausência desses fluxos no HTML não representa falha de cobertura do PRD.

Há, contudo, textos e comportamentos demonstrados no protótipo que ficaram desatualizados após decisões registradas no PRD.

## 1. Elementos do protótipo preservados

### 1.1 Posicionamento e linguagem visual

Preservados no PRD ou adendo:

- marca apresentada como papelaria afetiva e design autoral;
- promessa de experiência simples, acabamento artesanal e cuidado;
- estética clara, editorial, premium e acolhedora;
- fotografias ou representações grandes de produto;
- hierarquia com anúncio, cabeçalho, hero, busca, mais pedidos, categorias, processo, qualidade e conteúdo social;
- ausência de publicidade de terceiros;
- Projeto Exclusivo como caminho secundário quando o tema não é encontrado.

### 1.2 Descoberta e navegação

Preservados:

- busca em posição destacada;
- pesquisa por tema, ocasião ou estilo;
- produtos mais pedidos;
- categorias de Topos de bolo, Kits e lembrancinhas e Convites digitais;
- entrada discreta para Área da Cliente e Carrinho;
- menu móvel;
- acesso ao Instagram;
- chamadas para entender como a personalização funciona.

O PRD amplia corretamente a descoberta para Google, tema, personagem, ocasião, resultados exatos agrupados, semelhantes separados, SEO e estado sem resultado.

### 1.3 Confiança e jornada

Preservados:

- personalização após a compra;
- Briefing protegido;
- Prévia antes da Produção;
- acompanhamento humano opcional;
- acabamento visível e qualidade artesanal;
- entrega física europeia;
- Chat e WhatsApp como suporte lateral;
- mensagem de pagamentos por parceiros certificados sem promessa absoluta.

### 1.4 Acessibilidade inicial

O HTML demonstra:

- `lang="pt-BR"` e UTF-8;
- link para pular ao conteúdo;
- landmarks e títulos;
- rótulos acessíveis;
- `aria-expanded`, `aria-controls`, `aria-live` e diálogos modais;
- textos alternativos nas imagens.

O PRD preserva e amplia essa intenção ao exigir WCAG 2.2 AA nas jornadas essenciais, incluindo teclado, foco, contraste, erros, zoom, movimento reduzido e leitores de tela.

### 1.5 Separação entre demonstração e produto final

O protótipo declara explicitamente:

- “Protótipo demonstrativo”;
- “Nenhum pedido ou pagamento é real”;
- catálogo e preços demonstrativos;
- nenhuma coleta ou armazenamento nos formulários demonstrativos;
- Área da Cliente, Carrinho, Chat, WhatsApp, políticas e Instagram ainda desconectados.

O adendo registra corretamente essas limitações e afirma que fotografias e conteúdo finais substituirão os SVGs demonstrativos.

## 2. Lacunas e conflitos principais

### Alta — Prazo de Produção física usa o gatilho antigo

**Protótipo:** a faixa de confiança e a nota de catálogo afirmam “Produção em 7 dias corridos” **após o Briefing completo** (`index.html`, linhas 23 e 166).

**PRD:** FR-29 determina que a Produção física começa somente após a **Aprovação Final**, com prazo de sete dias corridos a partir desse momento.

**Impacto:** essa é uma divergência material de promessa comercial. Se a cópia do protótipo for reutilizada, a cliente receberá uma expectativa diferente daquela que governa a operação e a linha do tempo.

**Reconciliação recomendada:** tratar as duas ocorrências do protótipo como texto superado e atualizar a futura implementação para “Produção em 7 dias corridos após a aprovação final da arte”, com separação entre prazo de criação e prazo de Produção.

### Alta — CTA das lembrancinhas diverge do requisito confirmado

**Protótipo:** Kit Festa Fazendinha e Lembrancinhas Jardim Encantado usam “Comprar agora” (`index.html`, linhas 114 e 130).

**PRD:** FR-7 confirma “Comprar já” para lembrancinhas; FR-8 reserva “Comprar agora” aos convites.

**Impacto:** o protótipo não demonstra a distinção de CTA aprovada e pode induzir reutilização inconsistente de componentes ou conteúdo.

**Reconciliação recomendada:** marcar os CTAs de produtos físicos personalizados como desatualizados em relação ao PRD. O CTA editorial genérico do hero pode permanecer “Comprar agora” por direcionar à descoberta, desde que não seja confundido com a ação específica da página de produto.

### Alta — Taxonomia antiga de convite permanece visível

**Protótipo:** o produto “Jardim Dourado” é classificado como “Convite digital · Interativo” (`index.html`, linha 159).

**PRD:** a decisão atual usa **Convite Padrão** e **Convite Complexo**, com prazos de até 24 e 48 horas. O adendo registra explicitamente que a classificação e os prazos anteriores foram superados.

**Impacto:** “Interativo” pode ainda ser um recurso do convite, mas não deve aparecer como Tipo de Convite sem mapeamento para a taxonomia comercial vigente.

**Reconciliação recomendada:** substituir ou mapear “Interativo” para Padrão/Complexo antes de migrar conteúdo. Se “Interativo” permanecer como atributo funcional, separar visualmente atributo de modalidade e prazo.

### Alta — Ações de produto na home sugerem salto direto ao Carrinho

**Protótipo:** os botões dos cartões usam a classe `add-cart`, inclusive para produtos personalizados, sem link para uma página de produto ou configuração (`index.html`, linhas 114, 130, 146 e 162).

**PRD:** a jornada exige página de produto com preço, prazo, materiais, quantidade e regras, seguida de configuração de personalização e Miniatura antes da compra. Convites também precisam comunicar tipo, demonstração, dados necessários e processo de alterações.

**Impacto:** mesmo como demonstração, o padrão de interação sugere que uma cliente pode adicionar produto personalizado sem compreender ou configurar seus requisitos.

**Reconciliação recomendada:** no produto real, cartões da home devem levar à página/configuração correspondente. “Adicionar ao Carrinho” direto deve ser limitado a modalidades realmente prontas e inequívocas, se vier a ser aprovado.

### Média — Produto Digital Pronto não aparece na descoberta demonstrada

**Protótipo:** a home e o rodapé mostram Topos de bolo, Kits e lembrancinhas e Convites digitais, mas não apresentam Produtos Digitais Prontos.

**PRD:** Produtos Digitais Prontos são modalidade obrigatória do MVP, com página, regras e jornada próprias.

**Impacto:** a arquitetura demonstrada não valida se a cliente distinguirá arquivo editável pronto de convite personalizado, um risco explicitamente tratado em FR-9.

**Reconciliação recomendada:** incluir a modalidade na futura validação visual de navegação, busca e catálogo, com rotulagem clara de “sem personalização” e “entrega imediata”.

## 3. Lacunas secundárias e alertas de escopo

### Média — Protótipo não demonstra troca de idioma

O HTML contém somente português do Brasil e não apresenta seletor de idioma. O PRD exige português do Brasil, inglês e espanhol no lançamento, com preservação de contexto e Carrinho. O adendo já registra internacionalização como trabalho futuro, mas a direção de cabeçalho do protótipo não reserva claramente espaço para esse controle.

### Média — Busca estática não demonstra separação entre exatos e semelhantes

O HTML fornece campo, rótulo de resultados e caminho para Projeto Exclusivo, porém a marcação não demonstra grupos distintos de resultados exatos e semelhantes. O PRD exige essa separação em FR-6. Como o comportamento pode residir em `app.js`, esta constatação se limita ao que `index.html` consegue provar.

### Média — Favoritos e newsletter têm presença maior que sua prioridade

Cada cartão possui controle de Favoritos e a newsletter ocupa uma seção completa. O PRD deixa Favoritos para depois do lançamento, e o adendo afirma que Favoritos e newsletter não devem consumir capacidade antes das nove áreas obrigatórias. Esses elementos podem permanecer como referência estética, mas não devem orientar esforço do MVP ou competir com as jornadas essenciais.

### Média — Modal promocional de 10% ainda não corresponde a regra final

O protótipo apresenta modal de primeira compra com 10% mediante e-mail, mas declara o benefício como demonstrativo e sem validade comercial. O PRD trata o cupom como pressuposto pendente. Antes de reutilizar o modal, precisam ser resolvidos prioridade, elegibilidade, emissão individual, ausência de valor mínimo, não acumulação, melhor benefício, consentimento e retenção do e-mail.

### Baixa — “Entrega para toda a Europa” exige reconciliação operacional

O protótipo repete a promessa na faixa de confiança, no processo e no menu móvel. O PRD mantém “frete europeu”, mas deixa os países efetivamente atendidos como pergunta aberta. A futura interface não deve publicar uma promessa geográfica mais ampla do que pagamentos, fiscalidade, transportadoras e políticas conseguirem cumprir no lançamento.

## 4. Observações qualitativas que merecem preservação

O protótipo contém nuances úteis que estão em geral representadas no adendo e não devem desaparecer durante a transformação em requisitos e telas:

- “Detalhes que transformam momentos em memória” comunica valor emocional sem perder clareza comercial;
- “Você celebra. Nós cuidamos dos detalhes” resume bem a transferência de complexidade operacional;
- “Qualidade que você pode ver” conecta premium a evidência de acabamento, não apenas a aparência;
- “Não encontrou o tema que imaginou?” apresenta Projeto Exclusivo como saída acolhedora do estado sem resultado;
- microtextos como “sem excesso”, “feito com cuidado” e “acompanhamento humano” sustentam o tom premium, próximo e não invasivo;
- o protótipo evita alegações absolutas de segurança e deixa explícito seu caráter demonstrativo.

## 5. Conclusão

O PRD e o adendo extraem corretamente a estrutura, o tom e a direção visual de `prototype/index.html`, além de documentarem com precisão que ele cobre somente uma home demonstrativa. Antes que o protótipo seja usado como base de implementação, quatro elementos precisam ser tratados como superados ou incompletos:

1. prazo de Produção contado do Briefing, em vez da Aprovação Final;
2. CTA “Comprar agora” nas lembrancinhas, em vez de “Comprar já”;
3. Tipo de Convite “Interativo”, em vez da classificação Padrão/Complexo;
4. adição direta de produtos personalizados ao Carrinho sem página e configuração.

A ausência de Produto Digital Pronto e de troca de idioma também deve entrar na próxima validação visual, pois ambos são obrigatórios no MVP.

