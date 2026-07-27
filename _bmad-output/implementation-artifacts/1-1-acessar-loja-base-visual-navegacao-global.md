# Story 1.1: Acessar a loja com base visual e navegação global

Status: ready-for-dev

## Story

Como cliente visitante,  
quero acessar a loja com identidade JS Designs, navegação clara, busca, conta e carrinho,  
para conseguir começar a compra pelo celular sem depender de atendimento.

## Acceptance Criteria

1. **Given** que a cliente abre a loja em celular a partir de 320 px  
   **When** a página inicial carrega  
   **Then** ela vê logo JS Designs, busca, acesso à Área da Cliente, carrinho e menu em um cabeçalho compacto  
   **And** todos os controles têm alvo mínimo de toque de 44 x 44 px.

2. **Given** que a cliente navega por teclado ou leitor de tela  
   **When** passa pelo cabeçalho, menu, busca, conta e carrinho  
   **Then** a ordem de foco segue a leitura visual  
   **And** cada ícone tem nome acessível e foco visível.

3. **Given** que a aplicação é iniciada como greenfield  
   **When** a base técnica é criada  
   **Then** ela usa Next.js App Router com TypeScript, estrutura mobile-first e módulos iniciais coerentes com a arquitetura  
   **And** regras de negócio não ficam presas em componentes visuais.

4. **Given** que a cliente acessa a loja em português, inglês ou espanhol  
   **When** muda o idioma  
   **Then** a página atual permanece no mesmo contexto  
   **And** carrinho, pedido, preço e prazo não são alterados pela troca de idioma.

5. **Given** que a cliente usa navegador desktop, tablet ou celular suportado  
   **When** acessa a home  
   **Then** a navegação permanece funcional em toque, mouse e teclado  
   **And** a experiência não exige app nativo nem site separado.

## Requirements Covered

- FR-1: início orientado à descoberta, com proposta de valor e entrada clara para jornada.
- FR-2: navegação simples com busca, conta e carrinho em mobile e desktop.
- FR-4: troca de idioma sem perda de contexto ou mutação comercial.
- NFR-1: acessibilidade WCAG 2.2 AA nas interações essenciais do shell.
- NFR-2: base de performance percebida mobile, evitando bloqueio da tarefa principal.
- NFR-10: preparação de pt-BR, inglês e espanhol.
- NFR-12: responsividade contínua a partir de 320 px.
- AR-1, AR-2, AR-14: monólito modular, Next.js App Router + TypeScript e i18n por chaves/valores normalizados.
- UX-DR1, UX-DR2, UX-DR3, UX-DR4, UX-DR7, UX-DR10, UX-DR32, UX-DR33: base visual, header mobile, drawer/modal acessível e responsividade.

## Tasks / Subtasks

- [ ] Criar base greenfield da aplicação web (AC: 3)
  - [ ] Inicializar Next.js App Router com TypeScript.
  - [ ] Configurar estrutura inicial `app/`, `modules/`, `providers/`, `db/` e `tests/` conforme arquitetura.
  - [ ] Adicionar scripts mínimos de `dev`, `build`, `lint`, `typecheck` e `test:e2e` quando a stack estiver instalada.
  - [ ] Não mover nem substituir `prototype/`; usar apenas como referência visual/comportamental.

- [ ] Implementar base visual global JS Designs (AC: 1, 5)
  - [ ] Criar tokens globais de cor, tipografia, espaçamento, raio, foco e breakpoints.
  - [ ] Aplicar direção visual premium/autoral: branco, marfim, champagne, dourado escuro, taupe, preto suave, verde calmo e vermelho apenas para erro/risco.
  - [ ] Definir hierarquia tipográfica `display`, `display-mobile`, `heading`, `subheading`, `body`, `body-sm`, `label` e `caption`.
  - [ ] Garantir layout responsivo a partir de 320 px.

- [ ] Implementar shell público e cabeçalho global (AC: 1, 2, 5)
  - [ ] Criar layout raiz com skip link, região principal e landmarks semânticos.
  - [ ] Criar `header-mobile` com logo, busca, Área da Cliente, carrinho e menu.
  - [ ] Criar navegação desktop sem remover capacidades mobile.
  - [ ] Garantir alvos mínimos de toque de 44 x 44 px.

- [ ] Implementar menu/drawer acessível inicial (AC: 1, 2, 5)
  - [ ] Abrir/fechar por botão explícito.
  - [ ] Controlar `aria-expanded`, `aria-controls`, foco preso e restauração de foco.
  - [ ] Fechar por Escape.
  - [ ] Não empilhar modais/drawers.

- [ ] Implementar seletor inicial de idioma sem mutar estado comercial (AC: 4)
  - [ ] Expor pt-BR, inglês e espanhol.
  - [ ] Persistir preferência de idioma no cliente/rota quando possível.
  - [ ] Preservar a rota atual ao trocar idioma.
  - [ ] Garantir que troca de idioma não altere carrinho, pedido, preço ou prazo.

- [ ] Preparar pontos de entrada para busca, Área da Cliente e carrinho sem inventar fluxo futuro (AC: 1, 3)
  - [ ] Busca pode abrir painel/placeholder funcional mínimo; implementação completa fica para Story 1.3.
  - [ ] Área da Cliente pode apontar para rota/estado inicial seguro; acesso real pós-compra fica para Epic 3/4.
  - [ ] Carrinho pode apontar para rota/estado inicial; lógica real fica para Epic 3.

- [ ] Validar acessibilidade e responsividade (AC: 1, 2, 5)
  - [ ] Testar teclado: Tab, Shift+Tab, Enter, Espaço e Escape.
  - [ ] Verificar foco visível em todos os controles do cabeçalho.
  - [ ] Testar viewport 320 px, 420 px, 760 px e 1100 px.
  - [ ] Validar que texto de botões não trunca em 320 px.
  - [ ] Respeitar `prefers-reduced-motion`.

## Dev Notes

### Escopo exato desta história

Esta história cria a base inicial da aplicação e a navegação global. Não implementar ainda:

- catálogo real e busca completa;
- produtos “Mais procurados”;
- configuração de produto;
- carrinho transacional;
- checkout;
- login/Área da Cliente real;
- pagamento;
- briefing;
- aprovação de arte;
- suporte real.

Use placeholders seguros e rotas/stubs somente quando necessários para cumprir a navegação global.

### Contexto do repositório

- O repositório ainda não contém aplicação de produção (`package.json`, `app/`, `next.config.*` não existem).
- Existe protótipo estático em `prototype/` com HTML/CSS/JS demonstrativo. Ele é referência, não base transacional.
- O protótipo já demonstra skip link, cabeçalho, menu, busca, modais com foco, fallback de `sessionStorage`, carrosséis e redução de movimento.
- Corrigir divergências do protótipo ao criar a aplicação real:
  - não manter WhatsApp como botão flutuante permanente;
  - não usar “Comprar agora” para item personalizado;
  - não prometer produção após briefing; produção física só começa após aprovação final;
  - preservar a linguagem aprovada de confiança: “Pagamentos protegidos e processados por parceiros certificados.”

### Requisitos de arquitetura que o dev deve seguir

- Paradigma: monólito modular full-stack com portas de domínio.
- Stack seed: Node.js 24.x LTS, TypeScript 6.x, React 19.2.x, Next.js 16.2.x, Tailwind CSS 4.3.x, PostgreSQL 18.x, Prisma ORM 7.x, Stripe API `2026-06-24.dahlia`, Playwright 1.60.x.
- Next.js App Router + TypeScript é a base web.
- Componentes de UI não devem conter regras de negócio duráveis.
- Criar somente estrutura necessária para esta história. Não criar todas as tabelas/entidades agora.
- Se forem criados serviços iniciais, usar padrão de módulos de domínio em `modules/`; rotas e server actions devem chamar serviços, não provedores diretamente.
- Internacionalização deve usar chaves/valores normalizados; idioma de exibição não pode alterar preço, prazo, carrinho ou pedido.

### Estrutura inicial esperada

Criar ou preparar a estrutura abaixo, ajustando ao starter real sem inventar camadas extras:

```text
app/
  layout.tsx
  page.tsx
  globals.css
  [locale]/                 # se a estratégia de i18n por rota for adotada nesta história
components/
  shell/
    SiteHeader.tsx
    MobileMenu.tsx
    SkipLink.tsx
  ui/
    Button.tsx
    IconButton.tsx
modules/
  i18n/
    locales.ts
    dictionary.ts
  catalog/                  # somente tipos/stubs mínimos se necessários para navegação
providers/
db/
tests/
  e2e/
```

Não é obrigatório usar exatamente estes nomes se o starter impuser convenção diferente, mas qualquer variação deve preservar as fronteiras da arquitetura.

### UX obrigatório nesta história

- Mobile-first: a navegação deve funcionar integralmente em 320 px.
- Cabeçalho mobile: logo JS Designs, busca, Área da Cliente, carrinho e menu.
- Alvos de toque: mínimo 44 x 44 px.
- Foco visível com contraste AA.
- Ícones com nomes acessíveis.
- Menu/drawer com foco preso, Escape e restauração de foco.
- Ordem de foco deve seguir ordem visual e de leitura.
- Redução de movimento deve remover animações não essenciais.
- Desktop pode expandir navegação, mas não pode remover funções mobile.

### Regras de conteúdo e tom

- Usar português do Brasil como idioma base.
- Preparar inglês e espanhol como locales suportados.
- A marca deve parecer premium, autoral, acolhedora e atemporal.
- Não copiar identidade visual, paleta coral, textos promocionais ou composição exata da referência Gio.
- Não usar “100% seguro”.
- Não adicionar anúncios de terceiros.

### Latest technical information

- Next.js App Router é o router recomendado para novos recursos React e aplicações full-stack; usar a documentação de App Router, não Pages Router.
- Next.js possui suporte integrado a TypeScript quando o projeto é criado/configurado pelo fluxo oficial.
- React 19 oferece Actions e hooks como `useActionState` para estados de ação/formulário; usar quando reduzir boilerplate em formulários interativos futuros.
- Tailwind CSS v4 usa configuração CSS-first e `@theme` para tokens que geram utilities; tokens JS Designs devem ser declarados de forma compatível com esse modelo.

### Testes mínimos esperados

- `build` deve passar.
- `typecheck` deve passar.
- `lint` deve passar se configurado.
- Teste e2e mínimo deve validar:
  - home carrega;
  - header mostra logo, busca, conta, carrinho e menu;
  - menu abre/fecha por clique e Escape;
  - foco retorna ao botão que abriu o menu;
  - controles principais são acessíveis por teclado;
  - viewport 320 px não quebra layout.

### Riscos de implementação a evitar

- Não criar uma loja “só desktop”.
- Não colocar lógica de preço/pedido dentro do cabeçalho ou componentes visuais.
- Não criar autenticação real completa nesta história; apenas entrada/acesso visual seguro para história futura.
- Não adicionar WhatsApp flutuante permanente.
- Não copiar o protótipo literalmente se ele contradisser PRD/UX/arquitetura.
- Não criar tabelas de pedido, pagamento, briefing ou catálogo completo antes das histórias que precisam delas.

### Referências

- `J:\JSDESIGN\_bmad-output\planning-artifacts\epics.md` — Epic 1, Story 1.1, FR-1 a FR-6, NFR-1, NFR-2, NFR-3, NFR-10, NFR-12, UX-DR1, UX-DR2, UX-DR3, UX-DR4, UX-DR7, UX-DR10, UX-DR32, UX-DR33.
- `J:\JSDESIGN\_bmad-output\planning-artifacts\architecture\architecture-JSDESIGN-2026-07-27\ARCHITECTURE-SPINE.md` — AD-1, AD-2, AD-3, AD-14, Stack, Estrutura Inicial.
- `J:\JSDESIGN\_bmad-output\planning-artifacts\ux-designs\ux-JSDESIGN-2026-07-26\DESIGN.md` — Brand & Style, Colors, Typography, Layout & Spacing, Components.
- `J:\JSDESIGN\_bmad-output\planning-artifacts\ux-designs\ux-JSDESIGN-2026-07-26\EXPERIENCE.md` — Component Patterns, State Patterns, Interaction Primitives, Accessibility Floor, Responsive & Platform.
- `J:\JSDESIGN\prototype\index.html`, `prototype\styles.css`, `prototype\app.js` — referência estática existente, não aplicação de produção.
- Next.js Docs: https://nextjs.org/docs
- Next.js Installation: https://nextjs.org/docs/app/getting-started/installation
- Next.js TypeScript: https://nextjs.org/docs/app/api-reference/config/typescript
- Tailwind CSS Theme Variables: https://tailwindcss.com/docs/theme
- React `useActionState`: https://react.dev/reference/react/useActionState

## Project Structure Notes

- Não há arquivos existentes de app para atualizar; esta história é criação inicial.
- A pasta `prototype/` deve permanecer intacta como referência.
- Se o dev scaffoldar arquivos na raiz, deve preservar `_bmad/`, `_bmad-output/`, `.agents/` e `prototype/`.
- Qualquer configuração nova deve ficar explícita em arquivos versionáveis; segredos e credenciais ficam fora do código.

## Dev Agent Record

### Agent Model Used

TBD pelo agente de desenvolvimento.

### Debug Log References

### Completion Notes List

- Ultimate context engine analysis completed - comprehensive developer guide created.

### File List
