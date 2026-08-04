---
baseline_commit: 6382c2c8089d49c1824213eba94064aab56f5d58
---

# Story 1.2: Criar o layout público base da loja

Status: done

## Story

Como cliente da JS Designs,  
quero acessar uma loja com identidade visual clara, navegação simples e estrutura inicial confiável,  
para entender rapidamente que estou em uma loja de personalizados físicos e digitais.

## Requisitos cobertos

- FR-2
- UX-DR1, UX-DR2, UX-DR4, UX-DR5, UX-DR6, UX-DR7, UX-DR9, UX-DR10, UX-DR32, UX-DR33

## Acceptance Criteria

1. **Identidade e responsividade**
   - **Given** a página inicial da loja  
     **When** a cliente acessar pelo navegador  
     **Then** deve ver a identidade JS Designs aplicada com logo, cores, tipografia e estilo visual coerente com a UX aprovada  
     **And** a interface deve priorizar experiência mobile, mantendo adaptação correta para desktop.

2. **Cabeçalho e navegação pública**
   - **Given** a navegação pública  
     **When** a cliente visualizar o cabeçalho  
     **Then** deve encontrar acesso para Home, Produtos, Categorias, Buscar, Carrinho, Entrar/Criar conta e Suporte  
     **And** o botão principal deve conduzir para descoberta/compra de produtos.

3. **Rodapé e limites de suporte**
   - **Given** o layout base  
     **When** a cliente rolar a página  
     **Then** deve existir rodapé com links essenciais, políticas, contato/suporte e informações da JS Designs  
     **And** não deve existir botão flutuante permanente de WhatsApp fora dos fluxos definidos.

4. **Arquitetura Frontend/BFF preservada**
   - **Given** a arquitetura Frontend/BFF  
     **When** a página pública carregar  
     **Then** o Next.js deve renderizar a estrutura base  
     **And** qualquer dado dinâmico necessário deve vir pelo BFF, sem expor segredos ou regras de negócio no navegador.

5. **Qualidade mínima**
   - **Given** critérios básicos de qualidade  
     **When** a página for validada  
     **Then** deve passar em smoke test de renderização  
     **And** deve manter estrutura semântica mínima para acessibilidade e SEO.

## Tasks / Subtasks

- [x] Preparar a estrutura pública do App Router sem quebrar a fundação existente (AC: 1, 4, 5)
  - [x] Antes de editar código Next.js, ler `apps/web/AGENTS.md` e os guias locais relevantes em `apps/web/node_modules/next/dist/docs/01-app/01-getting-started/`.
  - [x] Preservar `apps/web/src/app/layout.tsx` como root layout com `<html lang="pt-BR">`, import global de CSS e `metadata`.
  - [x] Criar uma route group pública em `apps/web/src/app/(public)/` para o shell público da loja, evitando aplicar o shell comercial à rota técnica `/health`.
  - [x] Substituir a página técnica provisória de `apps/web/src/app/page.tsx` pela home pública mínima em `apps/web/src/app/(public)/page.tsx`.
  - [x] Remover `apps/web/src/app/page.tsx` depois de criar `apps/web/src/app/(public)/page.tsx`; não podem existir duas páginas competindo pela rota `/`.
  - [x] Manter `apps/web/src/app/api/health/route.ts`, `apps/web/src/bff/apiClient.ts` e a página técnica `/health` funcionais.

- [x] Implementar o shell público reutilizável da loja (AC: 1, 2, 3)
  - [x] Criar componentes em `apps/web/src/components/layout/`, no mínimo `PublicShell`, `SiteHeader` e `SiteFooter`.
  - [x] O `PublicShell` deve renderizar estrutura semântica com link de pular para o conteúdo, `header`, `main` e `footer`.
  - [x] O `SiteHeader` deve expor logo JS Designs, busca, Área da Cliente, carrinho, menu e CTA principal.
  - [x] Usar labels visíveis padronizadas no topo: `Home`, `Produtos`, `Categorias`, `Buscar`, `Carrinho`, `Área da Cliente`, `Suporte` e CTA `Ver produtos`.
  - [x] O CTA principal `Ver produtos` deve apontar para `/produtos`.
  - [x] O acesso de cliente deve apontar para `/entrar`, mas o texto visível principal deve ser `Área da Cliente`; a página placeholder pode explicar “Entrar ou criar conta”.
  - [x] A busca do topo deve apontar para `/buscar` nesta story. Não implementar painel de busca real ainda.
  - [x] O menu mobile deve expor, no mínimo, os mesmos destinos essenciais do topo: Home, Produtos, Categorias, Buscar, Carrinho, Área da Cliente e Suporte.
  - [x] O `SiteFooter` deve expor links essenciais: Políticas, Privacidade, Termos, Entrega, Trocas e reembolso, Contato/Suporte e informações da JS Designs.
  - [x] Criar exatamente estas rotas placeholder para evitar navegação quebrada: `/produtos`, `/categorias`, `/buscar`, `/carrinho`, `/entrar`, `/suporte`, `/politicas`, `/privacidade`, `/termos`, `/entrega`, `/trocas-e-reembolso`.
  - [x] Placeholders devem ter título claro, texto honesto sobre funcionalidade futura, CTA de retorno/continuação e não podem simular carrinho, login, busca, produto, preço ou suporte funcional.
  - [x] Rotas não indexáveis já identificadas devem exportar metadata com `robots: { index: false, follow: false }`: `/carrinho` e `/entrar`. Se forem criadas páginas futuras de checkout, Área da Cliente real ou admin nesta story por engano, elas também devem ser `noindex`; o correto é não criá-las agora.
  - [x] Não implementar carrinho, autenticação, busca real, catálogo real, checkout, suporte funcional ou integração WhatsApp nesta story; placeholders devem comunicar claramente que a funcionalidade completa vem depois.

- [x] Aplicar a identidade visual aprovada sem copiar a referência Gio (AC: 1, 3)
  - [x] Atualizar `apps/web/src/app/globals.css` com tokens CSS compatíveis com `DESIGN.md`: branco, bege claro, dourado champanhe, taupe, preto suave, superfícies claras, bordas sutis, serif editorial para títulos e sans-serif para corpo.
  - [x] Usar a direção visual minimalista, sofisticada, acolhedora e atemporal; não copiar paleta coral, claims, composição ou imagens da referência Gio.
  - [x] Usar botões escuros/claros conforme padrões `button-primary` e `button-secondary`.
  - [x] Evitar sombras grandes e aparência de marketplace genérico; usar hierarquia por espaço, tipografia, contraste e superfícies simples.
  - [x] Não adicionar biblioteca de UI, CSS framework ou dependência visual nova sem justificativa explícita.

- [x] Implementar comportamento responsivo e acessível do layout base (AC: 1, 2, 5)
  - [x] Garantir suporte visual e operacional desde 320 px.
  - [x] Garantir alvos de toque mínimos de 44 x 44 px para ações do topo.
  - [x] Em 320–419 px, usar coluna única, header compacto e controles legíveis sem truncamento crítico.
  - [x] Em 1100 px+, exibir navegação mais confortável sem remover capacidades mobile.
  - [x] Garantir foco visível, ordem de foco compatível com a leitura visual e nomes acessíveis para logo, busca, conta, carrinho, menu e links do rodapé.
  - [x] Respeitar `prefers-reduced-motion`; não usar animações promocionais ou smooth scroll essencial.
  - [x] Implementar CSS explícito para `@media (prefers-reduced-motion: reduce)` removendo transições/animações não essenciais.
  - [x] Se o menu mobile for implementado como drawer/modal, cumprir foco preso, Escape, botão de fechar e restauração de foco; caso contrário, preferir uma solução simples sem JavaScript pesado, como disclosure acessível.
  - [x] Garantir landmarks e semântica testáveis: um `header` global, um `main` com `id="conteudo"` ou equivalente para o skip link, um `footer`, heading `h1` único na home e `aria-label` nos navs principais quando houver mais de uma navegação.

- [x] Preparar conteúdo público mínimo sem invadir stories futuras (AC: 1, 2, 3)
  - [x] A home desta story deve demonstrar o layout base e a proposta da JS Designs, mas não deve implementar a home completa de “Mais procurados”; isso pertence à Story 1.4.
  - [x] Pode existir chamada/placeholder para “produtos mais procurados” ou “ver produtos”, desde que não simule catálogo real nem preço real.
  - [x] Textos visíveis devem estar em português do Brasil e preservados em UTF-8.
  - [x] Para reduzir espalhamento antes da Story 1.3, concentrar labels de navegação/rodapé em `apps/web/src/features/public-store/publicLayoutContent.ts`, sem criar sistema i18n completo agora.
  - [x] O arquivo `publicLayoutContent.ts` deve exportar objetos/arrays simples para `mainNavItems`, `footerSections`, `publicCta` e `placeholderPages`, com labels, `href` e descrição curta.
  - [x] Preparar um ponto de extensão no `PublicShell` para superfícies globais futuras não bloqueantes, por exemplo uma região opcional acima do conteúdo ou slot/componente futuro para o desconto por e-mail da Story 1.5. Não implementar o modal de desconto nesta story.

- [x] Atualizar testes de frontend/smoke (AC: 2, 3, 5)
  - [x] Atualizar ou complementar `apps/web/tests/e2e/foundation.spec.ts` para validar que a home pública carrega com identidade JS Designs.
  - [x] Adicionar teste Playwright para cabeçalho com links/ações Home, Produtos, Categorias, Buscar, Carrinho, Área da Cliente e Suporte.
  - [x] Adicionar teste Playwright para o CTA principal `Ver produtos` apontando para `/produtos`.
  - [x] Adicionar teste para rodapé com links essenciais e ausência de botão flutuante permanente de WhatsApp.
  - [x] Adicionar cobertura Playwright em viewport 320 px, usando `page.setViewportSize({ width: 320, height: 800 })` ou configuração equivalente.
  - [x] Adicionar teste mínimo de teclado/foco: `Tab` deve alcançar o skip link, navegação principal e CTA principal com nome acessível.
  - [x] Adicionar teste mínimo de metadata/robots para `/carrinho` e `/entrar` validando `noindex` ou comportamento equivalente renderizado pelo Next.
  - [x] Manter ou adicionar teste da página técnica `/health`, validando o heading “Fundação web operacional”.
  - [x] Manter teste da rota BFF `/api/health` passando sem alteração de contrato.

- [x] Rodar validações locais proporcionais ao escopo (AC: 4, 5)
  - [x] Em `apps/web`: `npm run lint`.
  - [x] Em `apps/web`: `npm run typecheck`.
  - [x] Em `apps/web`: `npm run build`.
  - [x] Em `apps/web`: `npm run test:e2e`, com Laravel API ativa para preservar o smoke BFF existente.
  - [x] Rodar backend somente se algum arquivo backend for alterado; o esperado desta story é não alterar `apps/api`.

### Review Findings

- [x] [Review][Patch] Topo mobile depende de rolagem horizontal para destinos essenciais [apps/web/src/app/globals.css:210] — Corrigido: navegação mobile agora quebra em múltiplas linhas sem `overflow-x` e os links essenciais são verificados no viewport de 320 px.
- [x] [Review][Patch] Teste mobile/teclado não alcança todos os destinos nem o CTA principal [apps/web/tests/e2e/foundation.spec.ts:59] — Corrigido: teste tabula por todos os links do header e valida foco no CTA `Ver produtos`.
- [x] [Review][Patch] Card accent tem contraste insuficiente para texto pequeno [apps/web/src/app/globals.css:307] — Corrigido: `--accent-calm` escurecido para `#5f6a57`, elevando o contraste com branco para cerca de 5.70:1.
- [x] [Review][Patch] Tipografia usa escala por largura de viewport contra o DESIGN.md [apps/web/src/app/globals.css:85] — Corrigido: tamanhos críticos passaram a usar papéis fixos com ajustes por breakpoint, sem `vw`.
- [x] [Review][Patch] Testes validam labels, mas não validam hrefs dos links obrigatórios [apps/web/tests/e2e/foundation.spec.ts:16] — Corrigido: header e footer agora validam label e `href` esperado.
- [x] [Review][Patch] Rodapé não explicita Contato/Suporte como informação de atendimento [apps/web/src/components/layout/SiteFooter.tsx:12] — Corrigido: rodapé informa que contato e suporte devem usar a página de Suporte, sem criar atendimento funcional.
- [x] [Review][Patch] Metadata de placeholders e title da home precisam ser endurecidos [apps/web/src/app/(public)/page.tsx:6] — Corrigido: home não duplica a marca no title e placeholders receberam metadata descritiva; carrinho/entrar preservam `noindex`.
- [x] [Review][Patch] Região global vazia usa `aria-live` sem mensagem dinâmica [apps/web/src/components/layout/PublicShell.tsx:18] — Corrigido: removido `aria-live` do slot genérico vazio.
- [x] [Review][Patch] Teste de ausência de WhatsApp é fácil de burlar [apps/web/tests/e2e/foundation.spec.ts:45] — Corrigido: teste cobre também URLs e seletores estruturais relacionados a WhatsApp.
- [x] [Review][Patch] Testes não garantem unicidade dos landmarks e do h1 [apps/web/tests/e2e/foundation.spec.ts:13] — Corrigido: teste da home valida exatamente um `header`, um `main`, um `footer` e um `h1`.

## Dev Notes

### Escopo exato desta story

Esta story cria a base visual pública da loja: shell, cabeçalho, navegação, rodapé, tokens visuais e testes de renderização. Ela não implementa catálogo real, busca real, carrinho transacional, login, checkout, cupom, suporte funcional, Produto Digital Pronto, produto personalizado, briefing, aprovação de arte ou admin.

Resultado esperado:

- A rota `/` exibe a loja JS Designs com identidade visual aprovada.
- O cabeçalho e o rodapé existem como componentes reutilizáveis.
- A navegação pública não leva a 404 em rotas essenciais; páginas futuras são placeholders claros, honestos e não transacionais.
- O layout funciona em mobile primeiro, incluindo 320 px.
- A fundação técnica da Story 1.1 continua passando.
- Carrinho e entrada/conta são apenas superfícies placeholder nesta story e devem ser `noindex`.

### Arquitetura obrigatória

- Browser → Next.js Frontend/BFF → Laravel API.
- Next.js é responsável por Home, SEO, navegação e UX mobile.
- Laravel continua sendo autoridade de domínio, estado e regras comerciais.
- O BFF pode compor tela, SSR/SEO, sessão/cookies, cache de leitura e proxy seguro.
- O BFF não pode calcular preço, decidir disponibilidade, validar pedido, criar regra de carrinho, autenticar por conta própria ou duplicar regra comercial do Laravel.
- Não expor `API_INTERNAL_URL`, tokens, credenciais ou segredos no bundle do navegador.

Fontes:

- `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md` — linhas 46–101, 149–189 e 257–272.

### Stack e versões que devem ser preservadas

- Frontend/BFF: Next.js `16.3.0`, React `19.2.0`, TypeScript.
- Node local/CI: `24.x`.
- E2E: Playwright `1.60.0`.
- Backend/API preservado: PHP `8.5.x`, Laravel `13.x`.
- Banco/cache preservados: PostgreSQL `18.x`, Redis.

A arquitetura original cita Next.js 16.2.x, mas a Story 1.1 atualizou para Next.js 16.3.0 por decisão explícita de segurança e `npm audit --omit=dev` limpo. Não voltar para 16.2.x.

Performance prática para esta story:

- Manter componentes como Server Components por padrão.
- Evitar `use client` salvo se for necessário para menu/drawer; se usar, isolar no menor componente possível.
- Não carregar fonte externa bloqueante nesta story; usar a pilha tipográfica aprovada com fallbacks locais/sistema.
- Não adicionar imagens pesadas nem hero com asset grande; fotografia real completa entra nas stories de home/produto.
- Não adicionar analytics, chat, newsletter, biblioteca de animação ou script global.

Fontes:

- `apps/web/package.json`
- `_bmad-output/implementation-artifacts/1-1-inicializar-a-fundacao-tecnica-da-plataforma.md` — Completion Notes e Change Log.

### Estado atual dos arquivos que serão alterados

- `apps/web/src/app/layout.tsx`
  - Estado atual: root layout mínimo com `metadata`, `<html lang="pt-BR">`, `<body>{children}</body>` e import de `globals.css`.
  - Mudança esperada: ajustar `metadata` para loja pública, mantendo root layout simples.
  - Preservar: `lang="pt-BR"`, import global de CSS e compatibilidade com App Router.

- `apps/web/src/app/page.tsx`
  - Estado atual: página inicial técnica com card “Loja online em preparação” e link para `/health`.
  - Mudança esperada: substituir/mover para home pública dentro do route group `(public)`.
  - Preservar: não deixar rota `/` sem página; não manter duas páginas para a mesma rota.

- `apps/web/src/app/globals.css`
  - Estado atual: tokens provisórios rosa/coral e estilos simples de card técnico.
  - Mudança esperada: substituir por tokens do `DESIGN.md`, estilos globais, foco visível, tipografia, botões e layout responsivo.
  - Preservar: `box-sizing`, `min-width: 320px`, foco visível e estilos necessários para `/health`.

- `apps/web/src/app/health/page.tsx`
  - Estado atual: página técnica que valida renderização do Frontend/BFF.
  - Mudança esperada: idealmente nenhuma; se o CSS global mudar, garantir que a página continue legível.
  - Preservar: heading “Fundação web operacional” e explicação de `/api/health`, salvo se os testes forem atualizados de forma equivalente.

- `apps/web/src/app/api/health/route.ts` e `apps/web/src/bff/apiClient.ts`
  - Estado atual: rota BFF pública sanitizada que consulta Laravel API com timeout, validação de payload e erro degradado.
  - Mudança esperada: nenhuma.
  - Preservar: contrato JSON atual, status 503 em degradação e não vazamento de erro interno.

- `apps/web/tests/e2e/foundation.spec.ts`
  - Estado atual: valida heading da home técnica e BFF `/api/health`.
  - Mudança esperada: atualizar teste de home para layout público e acrescentar cobertura de header/footer/mobile.
  - Preservar: teste BFF `/api/health`.

### Estrutura recomendada

```text
apps/web/src/
  app/
    layout.tsx
    globals.css
    (public)/
      layout.tsx
      page.tsx
      produtos/page.tsx
      categorias/page.tsx
      buscar/page.tsx
      carrinho/page.tsx
      entrar/page.tsx
      suporte/page.tsx
      politicas/page.tsx
      privacidade/page.tsx
      termos/page.tsx
      entrega/page.tsx
      trocas-e-reembolso/page.tsx
    api/health/route.ts
    health/page.tsx
  components/
    layout/
      PublicShell.tsx
      SiteHeader.tsx
      SiteFooter.tsx
  features/
    public-store/
      publicLayoutContent.ts
  bff/
    apiClient.ts
  tests/e2e/
    foundation.spec.ts
```

Esta estrutura é recomendada, não obrigatória, mas qualquer variação deve preservar a separação entre shell público, rota técnica `/health` e BFF `/api/health`.

### Contrato de navegação pública

Labels e destinos mínimos:

| Label visível | Destino | Observação |
| --- | --- | --- |
| Home | `/` | Volta para a home pública. |
| Produtos | `/produtos` | Placeholder; CTA principal também aponta para cá. |
| Categorias | `/categorias` | Placeholder para listagens futuras. |
| Buscar | `/buscar` | Placeholder; não implementar busca real nesta story. |
| Carrinho | `/carrinho` | Placeholder `noindex`; não simular itens, subtotal ou checkout. |
| Área da Cliente | `/entrar` | Placeholder `noindex`; texto interno pode dizer “Entrar ou criar conta”. |
| Suporte | `/suporte` | Placeholder; não implementar chat nem WhatsApp funcional. |

Links obrigatórios de rodapé:

| Label visível | Destino |
| --- | --- |
| Políticas | `/politicas` |
| Privacidade | `/privacidade` |
| Termos | `/termos` |
| Entrega | `/entrega` |
| Trocas e reembolso | `/trocas-e-reembolso` |
| Suporte | `/suporte` |

### Metadata e SEO nesta story

- Root/home pública deve ter metadata estática coerente com a loja JS Designs.
- Placeholders públicos informativos, como `/produtos` e `/categorias`, podem ser indexáveis apenas se o texto não prometer catálogo real nem produto disponível.
- `/carrinho` e `/entrar` devem ser `noindex, nofollow`.
- Não criar sitemap, robots global, dados estruturados de produto ou SEO avançado nesta story; isso pertence a stories futuras.
- Não indexar superfícies transacionais ou privadas.

### UX obrigatória para esta story

- Mobile-first: cerca de 80% das clientes devem descobrir e comprar pelo celular.
- Topo mobile sempre visível/claramente acessível: logo JS Designs, busca, conta/Área da Cliente, carrinho e menu.
- Menu principal mobile por tipo de produto; ocasiões e temas ficam para filtros/listagens futuras.
- Em desktop, a navegação pode expandir, mas não pode retirar capacidades mobile.
- Piso de acessibilidade: WCAG 2.2 AA, foco visível, nomes acessíveis, ordem de foco visual, alvo mínimo 44 x 44 px, reflow em 320 px e redução de movimento.
- WhatsApp não é fluxo normal de compra e não deve aparecer como botão flutuante permanente.
- A referência Gio é apenas lógica comercial: anúncio/topo claro, busca, categorias, hero, confiança e produtos fortes. Não copiar identidade, cores, claims, fotografias ou composição.

Escopo de compatibilidade:

- O gate automatizado desta story usa Playwright em Chromium conforme CI atual.
- A implementação, porém, não deve usar APIs experimentais/não suportadas que prejudiquem as duas versões estáveis mais recentes de Chrome, Safari, Edge e Firefox, Safari iPhone/iPad e Chrome Android.
- Testes cross-browser completos ficam fora desta story, salvo se a configuração de CI já suportar sem custo relevante.

Fontes:

- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md` — linhas 17–64, 97–126 e 158–200.
- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md` — linhas 250–262 e 289–380.
- `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md` — linhas 222–252 e 890–900.
- `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/addendum.md` — linhas 7–62.

### Conteúdo visual e tom

Usar os tokens aprovados:

- `surface-base`: `#FFFFFF`
- `surface-soft`: `#F8F5F1`
- `surface-muted`: `#EFE8DF`
- `ink-primary`: `#2D2D2D`
- `ink-secondary`: `#5D554E`
- `ink-muted`: `#74685E`
- `accent-primary`: `#765321`
- `accent-soft`: `#C8A46B`
- `accent-calm`: `#7D8974`
- `border-subtle`: `#D8CEC3`
- `focus-ring`: `#2D2D2D`
- Títulos: Georgia/Times New Roman/serif.
- Corpo: Inter/Helvetica Neue/Arial/sans-serif, com fallback de sistema.

Mensagem de confiança aprovada, quando aparecer: “Pagamentos protegidos e processados por parceiros certificados.” Não usar “100% seguro”.

Fonte:

- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md` — linhas 12–80 e 370–380.

### Informação técnica atual do Next.js

Usar App Router. Root layout é obrigatório e deve conter `html` e `body`. Layouts e Pages são Server Components por padrão; Client Components só devem ser introduzidos quando houver interação client-side real. Para links internos, usar `next/link`. Metadata estática deve continuar tipada com `Metadata` e exportada de layout/page Server Component.

Antes da implementação, consultar a documentação instalada localmente em:

- `apps/web/node_modules/next/dist/docs/01-app/01-getting-started/03-layouts-and-pages.md`
- `apps/web/node_modules/next/dist/docs/01-app/01-getting-started/04-linking-and-navigating.md`
- `apps/web/node_modules/next/dist/docs/01-app/01-getting-started/05-server-and-client-components.md`
- `apps/web/node_modules/next/dist/docs/01-app/01-getting-started/14-metadata-and-og-images.md`

Fontes externas oficiais verificadas em 2026-08-04 para confirmar práticas atuais:

- Next.js docs — App Router, layouts/pages, navegação e metadata: `https://nextjs.org/docs/app`
- React docs — Server Components: `https://react.dev/reference/rsc/server-components`

### Inteligência da Story 1.1

- Story 1.1 está `done` e foi mergeada em `main`; branch atual `story_1_2` está baseada no merge `6382c2c`.
- A fundação já criou `apps/web`, `apps/api`, `infra/docker`, `packages/contracts` e workflows de CI.
- Next.js foi atualizado para `16.3.0` por decisão de segurança.
- O smoke Playwright atual valida `/` e `/api/health`; a parte de `/` precisa ser atualizada para a nova home pública.
- Itens deferidos da Story 1.1 são hardenings operacionais e não bloqueiam esta story: pin de digests Docker, banco de teste idempotente, pin de GitHub Actions por SHA e teste negativo dedicado de BFF degradado.

### Anti-patterns proibidos

- Não alterar backend Laravel para resolver layout público.
- Não implementar carrinho/autenticação/busca/catálogo reais nesta story.
- Não criar regra de preço, desconto, frete, pagamento, pedido ou miniatura no frontend.
- Não expor API interna ou segredo no navegador.
- Não adicionar WhatsApp flutuante permanente.
- Não copiar a referência Gio.
- Não usar Python em scripts, build, testes, tooling ou documentação do produto.
- Não remover nem mover o protótipo estático em `prototype/`; ele é referência, não base runtime.
- Não substituir o App Router por Pages Router.
- Não adicionar dependências visuais desnecessárias.

### Testing Requirements

Mínimo obrigatório:

- `npm run lint`
- `npm run typecheck`
- `npm run build`
- `npm run test:e2e`

Cobertura mínima de teste:

- Home pública renderiza identidade JS Designs.
- Header expõe navegação pública exigida.
- Footer expõe links essenciais e informações JS Designs.
- Viewport 320 px mantém controles visíveis e operáveis.
- Navegação por teclado alcança skip link, links do header e CTA principal.
- `/carrinho` e `/entrar` têm `noindex`/metadata equivalente.
- `/health` continua renderizando “Fundação web operacional”.
- Não existe botão flutuante permanente de WhatsApp.
- Rota BFF `/api/health` continua retornando contrato saudável quando Laravel API está ativa.

### Definition of Done

- Story implementada somente no escopo frontend público/base visual.
- Shell público, cabeçalho e rodapé criados como componentes reutilizáveis.
- Home pública usa a identidade visual aprovada.
- Rotas/links essenciais não levam a 404 e exibem placeholders claros, não transacionais e semanticamente válidos.
- Carrinho e entrada/conta placeholder são `noindex`.
- Layout responsivo validado em 320 px e desktop.
- Acessibilidade mínima validada por semântica, nomes acessíveis, foco visível e testes Playwright.
- Performance mobile preservada: sem fonte externa bloqueante, sem scripts globais desnecessários e sem imagens pesadas introduzidas nesta story.
- `/health` técnico e `/api/health` BFF preservados.
- Nenhuma regressão em `/api/health`.
- Checks locais relevantes passam.
- Nenhum Python adicionado.

## Dev Agent Record

### Agent Model Used

GPT-5 Codex.

### Debug Log References

- 2026-08-04 — Customização BMAD resolvida manualmente por leitura de TOML, sem Python, preservando a regra do projeto.
- 2026-08-04 — Story marcada como `in-progress` em `sprint-status.yaml` e no arquivo da story.
- 2026-08-04 — RED: testes Playwright novos para layout público, 320 px, placeholders `noindex` e `/health` foram criados; falharam inicialmente contra a página técnica antiga.
- 2026-08-04 — GREEN: implementado shell público no App Router com route group `(public)`, componentes server-side, tokens visuais e rotas placeholder.
- 2026-08-04 — Validação frontend: `npm run lint`, `npm run typecheck`, `npm run build` passaram.
- 2026-08-04 — Validação e2e completa: Docker PostgreSQL/Redis saudáveis, migrations Laravel aplicadas, API iniciada via `Start-Job` durante o teste, `npm run test:e2e` passou com 6 testes.
- 2026-08-04 — Validação backend preservada: `php artisan test` passou com 2 testes/9 assertions e `vendor/bin/pint --test` passou.
- 2026-08-04 — Revisão adversarial pós-implementação executada e registrada em `_bmad-output/implementation-artifacts/review-adversarial-1-2-criar-o-layout-publico-base-da-loja.md`; achados de implementação corrigidos e e2e rerodado com 7 testes passando.
- 2026-08-04 — Code review BMAD executada com Blind Hunter, Edge Case Hunter e Acceptance Auditor; 10 findings de patch aplicados e validados.

### Completion Notes List

- Layout público base implementado com shell reutilizável, cabeçalho, navegação principal, CTA `Ver produtos`, rodapé e região global futura para superfícies não bloqueantes.
- Home pública movida para `apps/web/src/app/(public)/page.tsx`; a página técnica provisória `apps/web/src/app/page.tsx` foi removida para evitar conflito de rota `/`.
- Rotas placeholder criadas para todos os destinos exigidos, sem simular catálogo, busca, carrinho, login, preço, checkout ou suporte funcional.
- `/carrinho` e `/entrar` exportam metadata `robots` com `noindex` e `nofollow`.
- Tokens visuais atualizados para a direção aprovada da JS Designs: branco, bege claro, dourado champanhe, taupe, preto suave, serif editorial e sans-serif para corpo.
- Acessibilidade base coberta por skip link, landmarks, `main#conteudo`, foco visível, alvos de 44 px, navegação por teclado e labels acessíveis.
- Não foram adicionadas dependências, scripts globais, fontes externas bloqueantes, imagens pesadas ou integração WhatsApp.
- Backend Laravel não foi alterado.
- Revisão adversarial pós-implementação não deixou achado bloqueante aberto dentro do escopo da Story 1.2.
- Code review final corrigiu acessibilidade mobile, contraste AA, metadata de placeholders, semântica testável, cobertura de links/hrefs, ausência estrutural de WhatsApp e remoção de live region vazia.
- Validação pós-code-review aprovada: `npm run lint`, `npm run typecheck`, `npm run build` e `npm run test:e2e` com Laravel API ativa.

### File List

- `_bmad-output/implementation-artifacts/1-2-criar-o-layout-publico-base-da-loja.md`
- `_bmad-output/implementation-artifacts/review-adversarial-1-2-criar-o-layout-publico-base-da-loja.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `apps/web/src/app/(public)/buscar/page.tsx`
- `apps/web/src/app/(public)/carrinho/page.tsx`
- `apps/web/src/app/(public)/categorias/page.tsx`
- `apps/web/src/app/(public)/entrar/page.tsx`
- `apps/web/src/app/(public)/entrega/page.tsx`
- `apps/web/src/app/(public)/layout.tsx`
- `apps/web/src/app/(public)/page.tsx`
- `apps/web/src/app/(public)/politicas/page.tsx`
- `apps/web/src/app/(public)/privacidade/page.tsx`
- `apps/web/src/app/(public)/produtos/page.tsx`
- `apps/web/src/app/(public)/suporte/page.tsx`
- `apps/web/src/app/(public)/termos/page.tsx`
- `apps/web/src/app/(public)/trocas-e-reembolso/page.tsx`
- `apps/web/src/app/globals.css`
- `apps/web/src/app/layout.tsx`
- `apps/web/src/app/page.tsx` — removido.
- `apps/web/src/components/layout/PlaceholderPage.tsx`
- `apps/web/src/components/layout/PublicShell.tsx`
- `apps/web/src/components/layout/SiteFooter.tsx`
- `apps/web/src/components/layout/SiteHeader.tsx`
- `apps/web/src/features/public-store/publicLayoutContent.ts`
- `apps/web/tests/e2e/foundation.spec.ts`

### Change Log

- 2026-08-04 — Implementado layout público base da JS Designs com App Router route group, shell público, navegação, rodapé, placeholders, metadata `noindex` para superfícies transacionais, CSS mobile-first e testes Playwright.
- 2026-08-04 — Aplicadas correções da revisão adversarial pós-implementação: IDs estáveis no rodapé, alvos de toque efetivos no footer, CTA sem loop no placeholder de produtos, semântica da hero e teste de todas as rotas placeholder.
- 2026-08-04 — Aplicadas correções da code review BMAD e story marcada como `done`.
