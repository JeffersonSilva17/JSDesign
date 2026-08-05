# Revisão adversarial — Story 1.2: Criar o layout público base da loja

- A story manda “mover a home pública atual” de `apps/web/src/app/page.tsx`, mas a página atual não é uma home pública; é uma página técnica temporária da fundação. Essa formulação pode levar o dev a preservar texto técnico antigo dentro da loja real em vez de substituir o conteúdo por uma home pública mínima.

- A recomendação de criar `apps/web/src/app/(public)/page.tsx` não declara explicitamente que `apps/web/src/app/page.tsx` deve ser removido ou transformado sem conflito de rota. A frase “não existam duas páginas competindo pela rota `/`” ajuda, mas deveria ser uma instrução objetiva para evitar erro de App Router.

- O cabeçalho exige “Entrar/Criar conta” nos Acceptance Criteria, mas as tasks alternam entre “Área da Cliente/Entrar” e `/entrar`. Isso deixa ambíguo se a ação deve se chamar “Área da Cliente”, “Entrar”, “Criar conta” ou uma combinação visível, o que pode quebrar teste, UX e consistência com a navegação exigida.

- O CTA principal “deve conduzir para descoberta/compra de produtos” não define texto, destino nem comportamento. Um dev pode criar “Comprar agora” apontando para carrinho vazio, “Ver produtos” apontando para placeholder, ou botão sem link, e ainda alegar que cumpriu o critério.

- A story cria obrigação de rotas placeholder para `/carrinho` e `/entrar`, mas não exige `noindex` ou metadata apropriada para superfícies que não devem ser indexadas. O PRD indica que Carrinho, Checkout, Área da Cliente e Administração não devem ser indexados; criar placeholders indexáveis introduz ruído de SEO.

- A busca está subespecificada: não está claro se “Buscar” no header deve ser link para `/buscar`, botão que abre painel, campo inline ou todos esses elementos. Como FR-2 e UX colocam busca como ação principal de descoberta, essa indefinição pode gerar layout visualmente correto e funcionalmente fraco.

- O menu mobile fica opcional demais. A story permite drawer/modal ou disclosure simples, mas não define o conteúdo mínimo do menu nem se ele precisa expor Produtos, Categorias, Buscar, Carrinho, Entrar/Criar conta e Suporte em 320 px. Isso pode gerar um header compacto que passa visualmente, mas esconde ou duplica navegação de forma inconsistente.

- O requisito “estrutura semântica mínima para acessibilidade e SEO” é fraco para aceite. Não define landmarks esperados, `aria-label`s obrigatórios, heading hierarchy, metadata mínima, skip link testável ou ausência de headings duplicados entre shell e página.

- Os testes Playwright exigidos validam presença de links e viewport 320 px, mas não exigem navegação por teclado, foco visível, ordem de foco ou nome acessível dos ícones. Isso deixa parte relevante da WCAG 2.2 AA como intenção textual sem evidência automatizada mínima.

- A story diz para respeitar `prefers-reduced-motion`, mas não exige teste, implementação CSS concreta nem limite para transições/animações. Isso vira requisito fácil de ignorar em uma implementação visual.

- A cobertura e2e permanece apenas em Chromium pela configuração atual. A story cita suporte amplo de UX, mas não orienta como lidar com Safari/iPhone/iPad/Android, nem deixa claro que esta story só cobre smoke Chromium e que cobertura cross-browser fica fora do gate.

- A story não define orçamento de performance para o layout base. Como a UX enfatiza mobile-first e NFR-2 fala de desempenho percebido em conexão móvel, o dev deveria receber limites práticos: evitar fonte externa bloqueante, evitar imagens pesadas, não introduzir scripts client-side sem necessidade e manter Server Components por padrão.

- A story posterga corretamente o modal de desconto para a Story 1.5, mas não pede nenhum ponto de extensão no shell público para superfícies globais não bloqueantes. Isso pode forçar refatoração do layout logo na próxima story, especialmente porque o desconto aparece na entrada do site e precisa conviver com header, foco e sessão.

- As páginas placeholder institucionais/políticas são mencionadas, mas não há lista fechada de slugs e labels. A estrutura recomendada inclui `politicas`, `privacidade`, `termos`, `entrega`, `trocas-e-reembolso`, enquanto as tasks falam genericamente em “páginas institucionais/políticas necessárias”. Essa diferença pode gerar links faltantes ou nomes divergentes nos testes.

- A story não exige manter ou atualizar o teste da página técnica `/health`. Ela diz para preservar a página, mas a seção de testes só preserva `/api/health`. Alterações em `globals.css` podem degradar `/health` sem teste visual/semântico pegando a regressão.

- O arquivo de conteúdo sugerido `publicLayoutContent.ts` reduz espalhamento de textos, mas a story não define o shape mínimo desse conteúdo. Sem contrato simples para nav/footer/CTA, o dev pode criar constantes soltas ou objetos difíceis de evoluir na Story 1.3 de idiomas.

- A story não inclui `baseline_commit` ou referência explícita do estado base do repositório no cabeçalho, enquanto a Story 1.1 tinha esse tipo de rastreabilidade. Isso não bloqueia implementação, mas reduz a precisão da revisão futura sobre o que mudou durante a Story 1.2.

- A Definition of Done diz “Rotas/links essenciais não levam a 404 sem explicação”, o que permite páginas de placeholder vagas. Para uma loja pública, o esperado deveria ser “página placeholder clara, semanticamente válida, com CTA de retorno/continuação e sem simular funcionalidade ainda não pronta”.

## Revisão adversarial pós-implementação — 2026-08-04

Conteúdo revisado: diff não commitado da Story 1.2, incluindo App Router público, componentes de layout, CSS global, rotas placeholder, testes Playwright, story e sprint status.

### Achados

- O rodapé gerava IDs HTML a partir de títulos com espaços e acentos (`footer-Informações`), o que criava IDs frágeis e desnecessariamente dependentes de texto traduzível.
- Os links do rodapé tinham `min-height: 44px`, mas continuavam como elementos inline; nesse estado, o alvo de toque mínimo não era garantido de forma confiável.
- O placeholder de `/produtos` usava CTA “Voltar para produtos” apontando para a própria página, criando navegação circular sem valor para a cliente.
- A cobertura e2e inicial não provava que todas as rotas placeholder exigidas renderizavam conteúdo e não caíam em 404.
- O bloco visual da hero usava `aria-label` em um `div` genérico sem papel semântico, o que poderia ser ignorado por tecnologia assistiva.
- O teste de ausência de WhatsApp valida apenas links e botões, não texto estático. Isso é aceitável para a proibição de botão flutuante, mas não prova ausência total da palavra em conteúdo.
- O menu mobile foi implementado como navegação horizontal simples, não como drawer. Isso evita JavaScript e foco preso, mas significa que a experiência de “menu” é menos explícita do que um botão dedicado.
- A home contém dois CTAs com o mesmo texto “Ver produtos” quando considerados header e hero. Isso é permitido, mas exige que testes sempre sejam escopados para evitar ambiguidade.
- As páginas placeholder públicas não exportam metadata individual além das superfícies `noindex`; isso é suficiente para a story, mas deverá ser revisitado na Story 1.3/SEO.
- O CSS global cresceu bastante para uma única story; ainda está aceitável porque não há sistema de componentes separado, mas pode virar dívida quando mais componentes visuais entrarem.
- O teste de navegação por teclado cobre o início da ordem de foco, mas não percorre todos os links do header/footer.
- A validação automatizada permanece em Chromium, conforme CI atual; cobertura real de Safari/iPhone/Android continua fora do gate desta story.

### Correções aplicadas nesta revisão

- `footerSections` passou a ter IDs estáveis (`loja`, `atendimento`, `informacoes`) e o `SiteFooter` deixou de derivar IDs de texto visível.
- Links do rodapé agora usam `display: inline-flex` com `min-height: 44px`.
- `PlaceholderPage` agora evita CTA circular em `/produtos`, usando “Voltar para a home” nessa rota.
- Teste Playwright novo valida todas as rotas placeholder exigidas e conteúdo honesto de preparação.
- O bloco visual da hero virou `aside` com `aria-label`, melhorando a semântica sem adicionar JavaScript.

### Resultado

- Nenhum achado bloqueante restante dentro do escopo da Story 1.2.
- Pontos não corrigidos são limitações aceitas da própria story: sem drawer mobile, sem metadata completa por placeholder, sem cross-browser CI e sem teste exaustivo de toda a ordem de foco.
