# Reconciliação de entrada — `website-blueprint.md`

## Escopo e regra de precedência

Esta análise compara:

- entrada: `_bmad-output/brainstorming/brainstorm-website-js-designs-2026-07-23/website-blueprint.md`;
- artefato principal: `prd.md`;
- detalhe complementar: `addendum.md`;
- autoridade de decisão posterior: `.memlog.md`.

As decisões mais recentes registradas no memlog prevalecem sobre o blueprint. Uma diferença decorrente dessas decisões não é tratada como lacuna.

## Resultado geral

**Cobertura substancial, com cinco pontos que merecem correção ou decisão explícita antes da finalização.** O PRD e o adendo preservam a visão, a arquitetura principal, as jornadas distintas por modalidade, o catálogo, a busca, o Checkout, a Área da Cliente, o fluxo de Briefing/Arte/Aprovação, a operação administrativa, os principais NFRs e a maior parte do escopo de lançamento. As substituições posteriores também foram aplicadas corretamente.

## Decisões posteriores corretamente reconciliadas

Não constituem conflitos:

1. **Prazo do Briefing:** o blueprint registra três dias corridos; a cliente decidiu posteriormente por **três dias úteis**. O PRD usa três dias úteis.
2. **Produção física:** o blueprint deixa ambíguo se os sete dias começam após o Briefing ou incluem criação; a decisão posterior fixa **sete dias corridos após a Aprovação Final**. O PRD usa essa regra.
3. **Convites:** as famílias com até 24 h, 48 h, 72 h e 3–5 dias foram superadas por **Convite Padrão em até 24 horas** e **Convite Complexo em até 48 horas**. O PRD e o adendo registram a substituição.
4. **SEO:** o blueprint colocava SEO logo após o núcleo; a descoberta guiada o tornou requisito do lançamento. `FR-3` e o MVP refletem a decisão.
5. **Suporte automático:** o blueprint posicionava a automação completa após o núcleo; a cliente a tornou parte do MVP. `FR-37` a `FR-40` refletem a decisão.
6. **Idiomas:** o blueprint previa apenas português no lançamento e espanhol posteriormente; a cliente decidiu por português do Brasil, inglês e espanhol no lançamento. `FR-4`, `NFR-10` e o MVP refletem a decisão.
7. **Miniatura de convite:** o blueprint deixava o preço futuro; a cliente fixou **€5 para convites**. `UJ-2` e `FR-12` preservam a decisão, enquanto o valor para produtos físicos continua corretamente não confirmado.
8. **Produto Digital Pronto:** o PRD incorpora a decisão posterior de arquivo editável compatível com Silhouette Studio e entrega automática imediata por e-mail.

## Cobertura confirmada

### Visão, experiência e conteúdo

- Loja premium de papelaria personalizada, lembrancinhas, convites e arquivos digitais, sem dependência do WhatsApp na jornada normal.
- Clareza pré-compra, divulgação progressiva, suporte contextual, urgência somente humana e prova visual de acabamento.
- Direção visual em branco, bege, dourado champanhe, taupe e preto suave, com fotografias reais, espaço em branco e animações discretas.
- Uso da referência Gio como hierarquia comercial, sem copiar identidade, paleta, textos ou alegações.
- Mensagem de confiança preservada: “Pagamentos protegidos e processados por parceiros certificados.”

### Arquitetura, catálogo e descoberta

- Início, Busca, Loja, categorias, páginas de produto, Carrinho, Checkout, confirmação, Área da Cliente, Projeto Exclusivo, páginas legais e Administração.
- Busca por título, tema, personagem, ocasião e grafias alternativas.
- Resultados exatos primeiro e agrupados; semelhantes separados; estado sem resultado com Projeto Exclusivo.
- Separação entre produto físico, convite personalizado e Produto Digital Pronto.
- Dados essenciais de catálogo e publicação por modalidade em `FR-5` a `FR-10`.

### Compra e pós-compra

- Carrinho com quantidade, personalização, Miniatura e complementos.
- Checkout em página única, compra como visitante, NIF opcional e CTA “Finalizar e comprar”.
- Pagamento por parceiro certificado, transferência reservada por 48 horas, frete europeu e Fatura.
- Conta pós-compra, Briefing adaptado com autosave, versões numeradas, três Rodadas de Alteração e Aprovação Final.
- Linha do tempo, Próxima Ação, pausas, fotografia privada, rastreamento, entrega de convite e entrega automática de arquivo pronto.

### Operação, segurança e qualidade

- Fila administrativa por Próxima Ação, visão consolidada do Pedido, Ficha de Produção, controle da versão aprovada, Verificação de Qualidade e fila de exceções.
- Papéis, MFA, auditoria, idempotência, pagamentos fora do ambiente da JS Designs, arquivos privados, RGPD e continuidade.
- Integrações e decisões técnicas relevantes preservadas no adendo, sem prescrever a arquitetura no corpo do PRD.

## Lacunas e conflitos principais

### 1. Cupom de primeira compra foi rebaixado sem decisão posterior que o revogue

**Blueprint:** trata como **Definido** o desconto de 10% em troca do e-mail, sem valor mínimo, válido na primeira compra, não cumulativo com outros cupons ou promoções e com aplicação automática do benefício mais vantajoso.

**PRD/adendo:** `FR-14` cobre genericamente o melhor benefício, mas o índice de pressupostos afirma que o cupom de 10% “depende de confirmação de prioridade e regras finais”; o adendo também o lista entre decisões comerciais.

**Avaliação:** conflito de maturidade. O memlog não contém decisão posterior que retire o cupom do lançamento ou reverta seus elementos já definidos. Elegibilidade, expiração e antifraude podem continuar abertas, mas a existência, percentual, ausência de valor mínimo e não acumulação deveriam ser preservadas como decisão, ou a alteração de prioridade deveria ser explicitamente decidida e registrada.

**Impacto:** escopo comercial, UX de captura de e-mail, cálculo de benefício, testes de preço e instrumentação.

### 2. Regra funcional de cobrança única e reutilização da Miniatura não está preservada

**Blueprint:** quando a JS Designs cria uma Miniatura, o serviço é cobrado uma única vez para a mesma Miniatura e pode ser aplicado a vários produtos compatíveis do mesmo Pedido. O carrinho possui critérios de aceite específicos contra cobrança por item.

**PRD/adendo:** `FR-11` preserva as três opções de Miniatura; `FR-12` fixa €5 para convite; `FR-43` leva a Miniatura à Ficha de Produção. Entretanto, nenhum requisito assegura cobrança única, vínculo compartilhado entre itens compatíveis ou proteção contra duplicação dessa cobrança.

**Avaliação:** lacuna funcional. A decisão posterior limita o preço confirmado a convites, mas não revoga a semântica de uma mesma Miniatura ser reutilizável no Pedido.

**Impacto:** modelagem de Carrinho/Pedido, cálculo de preço, idempotência, Briefing mestre e testes de pedidos com múltiplos itens.

### 3. Os formatos atuais de convite e sua preservação no catálogo desapareceram

**Blueprint:** registra oito formatos atuais — tradicional, dois tipos de animado, interativo, interativo plus, infinito, cinemágico e cinemágico interativo — que deveriam ser preservados como opções, com mapeamento final e matriz de recursos ainda por decidir.

**PRD/adendo:** substituem corretamente os prazos antigos por Convite Padrão/Complexo, mas não preservam a existência dos oito formatos como conteúdo de catálogo. O adendo menciona apenas “classificação final dos Tipos de Convite” como decisão comercial.

**Avaliação:** lacuna de conteúdo e catálogo, não conflito com os novos prazos. A decisão posterior simplificou classes de prazo, mas não registrou eliminação dos formatos comerciais existentes.

**Impacto:** migração de catálogo, páginas de convite, Pré-formulário, demonstrações, preço e matriz de recursos. Deve-se registrar se os oito formatos continuam, serão renomeados ou serão realmente retirados.

### 4. Política de fotografias de crianças perdeu uma salvaguarda explícita do blueprint

**Blueprint:** fotografias de crianças nunca são públicas por padrão, nunca são usadas em marketing sem consentimento separado, são **eliminadas por padrão após cumprir a finalidade** e só podem ser conservadas para recompra mediante autorização.

**PRD/adendo:** preservam privacidade, consentimento separado e necessidade de política de retenção, mas não mantêm de forma inequívoca a regra de eliminação por padrão nem a condição específica de autorização para conservação voltada à recompra.

**Avaliação:** lacuna de princípio de privacidade. O prazo exato pode continuar aberto, porém a disposição padrão deveria permanecer explícita salvo decisão posterior.

**Impacto:** RGPD, desenho de consentimentos, retenção, exclusão verificável e critérios de aceite de mídia privada.

### 5. O gate de segurança está menos verificável que os critérios do blueprint

**Blueprint:** além de ASVS nível 2 e pentest, define critérios objetivos antes do lançamento: restauração de backup testada; requisitos aplicáveis do ASVS verificados; nenhum achado crítico ou alto de pentest sem tratamento aceito; links privados assinados e expirando; arquivo malicioso bloqueado antes do uso; toda rota administrativa protegida por MFA.

**PRD/adendo:** cobrem os controles em termos gerais e exigem segurança/restauração aprovadas no `Gate M2`, mas não preservam integralmente os resultados de aceite, sobretudo a ausência de vulnerabilidade crítica/alta sem tratamento aceito e o bloqueio prévio de upload malicioso como condição verificável.

**Avaliação:** lacuna de verificabilidade. Não é necessário prescrever fornecedor ou implementação, mas o gate deveria declarar a evidência mínima esperada.

**Impacto:** prontidão de lançamento, critério de aprovação do pentest, aceite de uploads e decisão de go/no-go.

## Diferenças menores ou corretamente mantidas como abertas

- A presença do suporte no Checkout foi reaberta no memlog; a pergunta 4 do PRD é coerente, apesar de o blueprint ter indicado ocultação completa.
- Países, moeda, métodos de pagamento, IVA/OSS, transportadoras, regras para pedidos mistos e políticas de cancelamento/devolução/reembolso continuam corretamente abertos.
- Licença, formatos, limite de downloads e período de recuperação do Produto Digital Pronto continuam corretamente abertos.
- Acessibilidade foi fortalecida de “decisão futura” no blueprint para WCAG 2.2 AA no PRD.
- Compatibilidade, observabilidade, desempenho quantitativo, capacidade e SLA de suporte continuam como decisões ou pressupostos identificados.
- Favoritos, avaliações, busca preditiva, recuperação de Carrinho, recompra e relatórios avançados permanecem corretamente após o lançamento.

## Conclusão

O PRD e o adendo reconciliam a maior parte do blueprint e aplicam corretamente todas as substituições explícitas do memlog. Antes da finalização, recomenda-se resolver ou corrigir os cinco pontos acima. Os mais urgentes são a maturidade do cupom de 10%, a cobrança/reutilização da Miniatura e os critérios verificáveis do gate de segurança; os formatos de convite e a retenção padrão de fotografias precisam ao menos de decisão explícita para não serem perdidos silenciosamente.
