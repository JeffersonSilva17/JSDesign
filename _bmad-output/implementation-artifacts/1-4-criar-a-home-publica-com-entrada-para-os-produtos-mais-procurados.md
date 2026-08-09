---
baseline_commit: 78e8dcd052a2698f353b2366db0b9a4f02e0cbe8
---

# Story 1.4: Criar a home pública com entrada para os produtos mais procurados

Status: done

## Story

Como cliente da JS Designs,  
quero ver logo na entrada da loja os produtos mais procurados e caminhos claros de compra,  
para encontrar rapidamente lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto.

## Requisitos cobertos

- FR-1
- FR-3
- UX-DR4, UX-DR8, UX-DR11, UX-DR12

## Acceptance Criteria

1. **Home com proposta clara e destaque cedo para mais procurados**
   - **Given** a cliente acessando a home pública da JS Designs  
     **When** a página carregar em celular ou computador  
     **Then** deve existir uma primeira seção com proposta de valor clara da JS Designs  
     **And** a seção “Mais procurados” deve aparecer imediatamente após a hero, antes de qualquer bloco secundário como “Próximos caminhos”, rodapé ou explicações longas  
     **And** em viewport de 320 x 800 px o heading “Mais procurados” deve estar dentro da primeira viewport no carregamento, sem interação nem rolagem  
     **And** a hero deve ser compactada: os atuais `modalityCards` devem ser removidos da hero ou reaproveitados como os próprios cards de “Mais procurados”, sem duplicar as mesmas três modalidades  
     **And** deve ficar evidente que a loja trabalha com lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto.

2. **Cards específicos de produtos/entradas procuradas**
   - **Given** a cliente avaliando os destaques da home  
     **When** visualizar “Mais procurados”  
     **Then** deve ver exatamente três cards específicos de curadoria editorial inicial, nesta ordem: “Kit Festa Fazendinha”, “Convite Jardim Dourado” e “Topo Céu Encantado”  
     **And** a seção não deve afirmar ou insinuar ranking medido, vendas reais ou analytics ainda inexistentes  
     **And** deve exibir uma explicação curta e visível de que os destaques são uma seleção editorial para apresentar as modalidades da loja  
     **And** cada card deve exibir nome, modalidade, descrição curta, CTA e caminho público coerente  
     **And** cada card deve ter uma área visual leve: se for decorativa, deve ser ignorada por leitor de tela; se comunicar conteúdo, deve ter nome acessível ou texto alternativo equivalente  
     **And** os cards devem diferenciar produto físico personalizado, convite digital personalizado e Produto Digital Pronto.

3. **CTAs coerentes com a modalidade e com o escopo atual**
   - **Given** um card de produto físico personalizado ou convite digital personalizado  
     **When** a cliente acionar o CTA  
     **Then** o destino deve levar para descoberta/detalhes futuros, sem adicionar direto ao carrinho  
     **And** o texto deve evitar promessa de compra imediata antes de configuração, briefing, preço ou catálogo real existirem.
   - **Given** o card de Produto Digital Pronto  
     **When** a cliente visualizar o CTA  
     **Then** deve usar um CTA não transacional como “Ver arquivo”, e não “Comprar agora”, porque o destino ainda é placeholder  
     **And** o card deve informar “Produto digital”, “Silhouette Studio” e que, quando a compra estiver disponível, o download será liberado após o pagamento confirmado, sem apresentar essa capacidade futura como ativa agora.

4. **Busca placeholder e caminhos públicos preservados**
   - **Given** a cliente querendo procurar por tema, produto ou ocasião  
     **When** usar os CTAs da home, o link/entrada atual de busca placeholder ou os links de destaque  
     **Then** deve conseguir seguir para rotas públicas já existentes, como `/produtos`, `/categorias` ou `/buscar`  
     **And** nenhuma ação deve depender de cadastro, login, carrinho funcional ou suporte humano.

5. **Conteúdo centralizado, tipado e preparado para dados reais**
   - **Given** a estrutura criada na Story 1.3  
     **When** os textos e cards da home forem implementados  
     **Then** a cópia editorial, labels, CTAs e metadados devem ficar no conteúdo público tipado, não hardcoded na página React  
     **And** o contrato criado nesta story deve ser explicitamente editorial/UI, não contrato transacional de catálogo  
     **And** deve preparar substituição futura por dados vindos do BFF/Laravel sem colocar regra de catálogo, preço, disponibilidade, desconto, frete ou carrinho no frontend.

6. **SEO e semântica da home**
   - **Given** a home pública é superfície indexável  
     **When** o HTML for renderizado  
     **Then** deve manter o title `Loja online de personalizados físicos e digitais | JS Designs` e a description definida em `publicContent.metadata.home`, atualizando-a somente se continuar em português do Brasil e mencionar as três modalidades sem promessas transacionais  
     **And** a home não deve renderizar `noindex` nem `nofollow`  
     **And** deve ter heading hierarchy correta, com um único `h1` e a seção “Mais procurados” identificada por `h2`  
     **And** cards devem ser navegáveis por teclado e ter nomes acessíveis.

7. **Responsividade mobile-first e acessibilidade**
   - **Given** a loja prioriza 80% de uso em celular  
     **When** a home for visualizada em 320 px  
     **Then** hero, CTAs e “Mais procurados” devem permanecer legíveis, operáveis e sem overflow horizontal  
     **And** botões/links devem respeitar alvo mínimo de toque de 44 x 44 px  
     **And** foco visível, ordem de leitura e redução de movimento devem ser preservados.

8. **Validação técnica**
   - **Given** o frontend em Next.js  
     **When** os testes forem executados  
     **Then** deve haver cobertura para:
       - presença da seção “Mais procurados” cedo na home;
       - cards específicos das três modalidades;
       - CTAs e `hrefs` esperados;
       - ausência de compra/carrinho/preço real simulado;
       - heading “Mais procurados” dentro da primeira viewport de 320 x 800 px e ausência de overflow horizontal;
       - metadata indexável, sem `noindex`/`nofollow`;
       - tratamento acessível da área visual de cada card;
       - textos em português do Brasil, UTF-8 e sem mojibake;
       - preservação dos testes existentes de layout, placeholders, `noindex`, `/health` e `/api/health`.

## Tasks / Subtasks

- [x] Preparar a implementação lendo contexto obrigatório (AC: 1, 5, 6)
  - [x] Antes de editar código Next.js, ler `apps/web/AGENTS.md`.
  - [x] Consultar os guias locais relevantes do Next.js 16 em `apps/web/node_modules/next/dist/docs/`, principalmente App Router, Server Components, linking/navigating e metadata.
  - [x] Ler `apps/web/src/app/(public)/page.tsx`, `apps/web/src/i18n/publicContent.ts`, `apps/web/src/i18n/publicContent.types.ts`, `apps/web/src/features/public-store/publicLayoutContent.ts`, `apps/web/src/app/globals.css` e `apps/web/tests/e2e/foundation.spec.ts`.
  - [x] Confirmar que a implementação não altera `apps/api` nesta story.

- [x] Modelar conteúdo dos “Mais procurados” no dicionário público (AC: 2, 3, 5)
  - [x] Estender `PublicContent` com uma estrutura tipada para `home.mostWanted` ou equivalente.
  - [x] Cada item deve conter, no mínimo: `title`, `modality`, `description`, `cta.label`, `cta.href`, `visualLabel` ou `imageAlt`, e indicadores necessários para diferenciar físico personalizado, convite personalizado e Produto Digital Pronto.
  - [x] Documentar pelo nome do campo, comentário ou estrutura que essa lista é curadoria editorial inicial, não dado transacional nem ranking medido.
  - [x] Implementar o conjunto editorial fechado do mockup UX, sem criar um quarto card:
    - `Kit Festa Fazendinha` — produto físico personalizado, CTA `Ver opções`, destino `/produtos`;
    - `Convite Jardim Dourado` — convite digital personalizado, CTA `Ver modelos`, destino `/buscar`;
    - `Topo Céu Encantado` — Produto Digital Pronto para Silhouette Studio, CTA `Ver arquivo`, destino `/produtos`.
  - [x] Não usar personagens, marcas, preço, desconto, disponibilidade, prazo fechado ou promessa de entrega que ainda dependam de catálogo real.
  - [x] Não usar “a partir de”, “desde”, “subtotal”, “promoção”, “mais vendido” ou qualquer variação que pareça preço/ranking real.
  - [x] Remover ou reescrever a mensagem atual de `nextPaths` que diz que “Mais procurados” entra na Story 1.4.
  - [x] Atualizar `qualityCopy` ou estrutura equivalente para incluir textos usados pelos testes da nova seção sem duplicar literais em excesso.

- [x] Renderizar a seção na home pública (AC: 1, 2, 3, 4, 6, 7)
  - [x] Atualizar `apps/web/src/app/(public)/page.tsx` para renderizar a seção “Mais procurados” a partir do conteúdo tipado.
  - [x] Preservar a home como Server Component; não adicionar `use client` se não houver interação real.
  - [x] Remover os `modalityCards` da hero ou reutilizar seu conteúdo dentro de `mostWanted`; não renderizar dois conjuntos concorrentes com as mesmas modalidades.
  - [x] Manter um único `h1` na página.
  - [x] Usar `h2` para “Mais procurados” e nomes acessíveis nos cards.
  - [x] Usar `next/link` para CTAs internos.
  - [x] Direcionar cards somente para rotas públicas já existentes até páginas reais de produto existirem: `/produtos`, `/categorias` ou `/buscar`.
  - [x] Não criar slugs de produto, rotas novas ou links futuros não implementados.
  - [x] Para personalizados, evitar CTA de carrinho; usar textos como “Ver detalhes”, “Ver opções” ou “Personalizar depois”, desde que o destino não simule a ação.
  - [x] Para Produto Digital Pronto, usar `Ver arquivo`; reservar “Comprar agora” para a story que entregar página de produto e fluxo real de compra.
  - [x] Manter o contexto do clique no nome acessível do link (por exemplo, “Ver arquivo: Topo Céu Encantado”), mesmo que o destino ainda seja uma rota placeholder genérica.

- [x] Ajustar estilos mobile-first sem degradar a base visual (AC: 1, 2, 7)
  - [x] Atualizar `apps/web/src/app/globals.css` com estilos para grid/lista de “Mais procurados”.
  - [x] Garantir leitura em coluna única a partir de 320 px.
  - [x] Compactar hero e espaçamentos mobile para que o `h2` “Mais procurados” esteja dentro da viewport de 320 x 800 px; não esconder conteúdo nem usar posicionamento artificial apenas para satisfazer o teste.
  - [x] Permitir layout mais confortável em desktop sem esconder informações existentes no mobile.
  - [x] Usar tokens visuais aprovados: branco, marfim, champagne, taupe, preto suave, verde calmo e vermelho apenas para erro/risco.
  - [x] Evitar sombras fortes, cards genéricos de marketplace e elementos que copiem a referência Gio.
  - [x] Não adicionar imagens pesadas ou dependência visual nova; se não houver fotos reais no app, usar área visual editorial leve e acessível até a story de catálogo/mídia.
  - [x] Se alguma imagem for adicionada, usar apenas asset local autorizado no projeto, sem hotlink remoto, sem copiar a referência Gio e sem foto sem fonte/licença clara.

- [x] Preservar limites de arquitetura e escopo (AC: 3, 4, 5, 8)
  - [x] Não criar catálogo real no frontend.
  - [x] Não criar endpoint Laravel de catálogo nesta story.
  - [x] Não implementar busca real, preço, disponibilidade, desconto progressivo, cupom, frete, miniatura, carrinho, checkout, pagamento, briefing, login ou suporte funcional.
  - [x] Não mover regra de negócio para Next.js.
  - [x] Não alterar `apps/web/package.json` nem `apps/web/package-lock.json`; esta story não adiciona dependências.
  - [x] Manter `/health` fora do `PublicShell` e preservar `/api/health`.
  - [x] Manter `/carrinho` e `/entrar` como placeholders `noindex`.

- [x] Atualizar testes E2E de frontend (AC: 2, 3, 4, 6, 7, 8)
  - [x] Atualizar `apps/web/tests/e2e/foundation.spec.ts` ou criar spec dedicada para a home.
  - [x] Validar que existe seção “Mais procurados” com `h2`.
  - [x] Validar os três nomes e sua ordem: “Kit Festa Fazendinha”, “Convite Jardim Dourado” e “Topo Céu Encantado”.
  - [x] Validar que Produto Digital Pronto exibe “Produto digital”, “Silhouette Studio” e a explicação futura condicionada ao pagamento confirmado.
  - [x] Validar que personalizados não têm CTA direto de carrinho.
  - [x] Validar `hrefs` dos CTAs para rotas públicas existentes.
  - [x] Validar que a página não exibe preço real, subtotal, disponibilidade, checkout funcional, formulário de pagamento ou simulação de carrinho.
  - [x] Cobrir também padrões comerciais proibidos: `€`, `R$`, `USD`, `CHF`, `BRL`, valores como `0,00`, parcelamento (`10x`), percentuais, “grátis”, “a partir de”, “desde”, “promoção” e “mais vendido”.
  - [x] Validar viewport de 320 px sem overflow horizontal crítico medindo `documentElement.scrollWidth <= documentElement.clientWidth` ou critério equivalente.
  - [x] Em 320 x 800 px, validar que o `h2` “Mais procurados” está em viewport sem rolagem e registrar screenshot de evidência para revisão visual.
  - [x] Validar que a seção “Mais procurados” aparece antes de “Próximos caminhos” ou qualquer bloco secundário.
  - [x] Validar `title`, `meta[name="description"]` e ausência de `meta[name="robots"]` com `noindex`/`nofollow` na home.
  - [x] Para cada área visual, validar `aria-hidden="true"` quando decorativa ou texto alternativo/nome acessível quando informativa.
  - [x] Confirmar por inspeção/teste estático que `page.tsx` e a nova seção não introduzem a diretiva `use client`.
  - [x] Preservar testes da Story 1.3 para `lang="pt-BR"`, acentos, ausência de mojibake, header, footer, placeholders, metadata, `/health` e BFF `/api/health`.

- [x] Rodar validações proporcionais ao escopo (AC: 8)
  - [x] Em `apps/web`: `npm run lint`.
  - [x] Em `apps/web`: `npm run typecheck`.
  - [x] Em `apps/web`: `npm run build`.
  - [x] Em `apps/web`: `npm run test:e2e`, com Laravel API ativa para preservar o smoke BFF existente.
  - [x] Rodar backend somente se algum arquivo backend for alterado por engano; o esperado é não alterar `apps/api`.

### Review Findings

- [x] [Review][Patch] Restaurar quebra de linha da navegação no breakpoint desktop para evitar colisão ou overflow em larguras intermediárias [apps/web/src/app/globals.css:505]
- [x] [Review][Patch] Cobrir explicitamente a ausência de CTA direto de carrinho nos cards personalizados [apps/web/tests/e2e/foundation.spec.ts:77]
- [x] [Review][Patch] Validar a modalidade visível dentro de cada card, preservando a associação correta entre produto e modalidade [apps/web/tests/e2e/foundation.spec.ts:80]

## Dev Notes

### Escopo exato desta story

Esta story transforma a home pública base em uma home de descoberta inicial com uma seção real de “Mais procurados”. Neste momento, “Mais procurados” significa curadoria editorial inicial da JS Designs, não ranking calculado por vendas, busca ou analytics. A implementação continua sendo de fundação pública/frontend; ela não cria catálogo transacional nem páginas finais de produto.

Resultado esperado:

- A home deixa de dizer que “Mais procurados” é futuro e passa a exibir uma seção navegável de destaques.
- A copy antiga dizendo que “A listagem de ‘Mais procurados’ entra na Story 1.4” deixa de aparecer na home.
- Os destaques usam os três exemplos específicos do mockup UX aprovado: “Kit Festa Fazendinha”, “Convite Jardim Dourado” e “Topo Céu Encantado”.
- A seção diferencia produto físico personalizado, convite digital personalizado e Produto Digital Pronto, nessa ordem, sem quarto card opcional.
- Os `modalityCards` atuais deixam de competir com “Mais procurados”: devem ser removidos da hero ou reaproveitados como os próprios itens da nova seção.
- O conteúdo fica centralizado e tipado no dicionário público criado na Story 1.3.
- A UI fica preparada para trocar a fonte editorial por dados vindos do BFF/Laravel quando o catálogo real existir.

Fora do escopo:

- Catálogo real.
- Busca real.
- Página real de produto.
- Imagens finais/fotos reais do catálogo.
- Integração BFF/Laravel para catálogo.
- Preços, disponibilidade, subtotal, quantidade, miniatura, carrinho, checkout, pagamento, cupom, desconto, frete, briefing, login, suporte funcional ou admin.
- Qualquer alteração em `apps/api`.

### Fundamento do produto

O PRD define que a página inicial deve apresentar proposta de valor, busca destacada, categorias essenciais, produtos mais pedidos, prova de acabamento, funcionamento da personalização e acesso discreto a Projeto Exclusivo. Para esta story, o recorte é a entrada de “produtos mais pedidos/mais procurados” na home pública, mantendo placeholders honestos para o que ainda não foi implementado.

O UX reforça que a home deve ser mobile-first, porque cerca de 80% da descoberta/compra deve ocorrer por celular. A primeira dobra mobile deve ser curta e mostrar marca, busca/acesso rápido e “Mais procurados” com produtos específicos visíveis cedo.

Fontes:

- `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/prd.md`
- `_bmad-output/planning-artifacts/prds/prd-JSDESIGN-2026-07-25/addendum.md`
- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/DESIGN.md`
- `_bmad-output/planning-artifacts/ux-designs/ux-JSDESIGN-2026-07-26/EXPERIENCE.md`

### Arquitetura obrigatória

- Browser → Next.js Frontend/BFF → Laravel API.
- Next.js é dono da renderização pública, SEO, navegação e composição da home.
- Laravel continua sendo autoridade de domínio, catálogo real, disponibilidade, preço, carrinho, pagamento, briefing, pedidos e regras comerciais.
- O BFF pode compor dados para tela, mas não deve conter regra de negócio principal.
- Como o catálogo real só entra em épicos/stories futuras, esta story deve usar conteúdo editorial tipado no frontend e deixar explícito o ponto de substituição futura por BFF/Laravel.
- Não criar endpoint temporário falso de catálogo nem JSON solto que pareça fonte transacional.

Fonte:

- `_bmad-output/planning-artifacts/architecture/architecture-JSDESIGN-2026-07-27-laravel-bff/ARCHITECTURE-SPINE.md`

### Stack e versões que devem ser preservadas

- Frontend/BFF: Next.js `16.3.0`, React `19.2.0`, TypeScript.
- Node local/CI: `24.x`.
- E2E: Playwright `1.60.0`.
- Backend/API preservado: PHP `8.5.x`, Laravel `13.x`.
- Banco/cache preservados: PostgreSQL `18.x`, Redis.

Não alterar versões nem lockfile nesta story. O código instalado (`package.json`) é a fonte de verdade para a implementação. A documentação pública consultada em 2026-08-08 ainda descreve Next.js 16.3 como Preview e recomenda 16.2.11 como Active LTS com correções de segurança de julho de 2026; portanto, qualquer decisão de upgrade/downgrade precisa de avaliação técnica separada e não deve ser misturada ao escopo da home.

Fonte: `apps/web/package.json`.

### Informação técnica atual do Next.js

Usar App Router. Pages e layouts são Server Components por padrão; a home deve continuar server-side enquanto a seção não exigir estado client-side. Metadata estática deve continuar exportada como `Metadata` a partir da page/layout. Links internos devem usar `next/link`.

A documentação local do Next.js 16 sobre internationalization recomenda rotas por locale e dicionários quando a localização real for implementada. Esta story não deve criar rotas localizadas nem `proxy.ts`; deve apenas respeitar a estrutura tipada `pt-BR` já criada na Story 1.3.

Fontes locais lidas:

- `apps/web/node_modules/next/dist/docs/01-app/03-api-reference/04-functions/generate-metadata.md`
- `apps/web/node_modules/next/dist/docs/01-app/02-guides/internationalization.md`

Fontes oficiais verificadas em 2026-08-08:

- `https://nextjs.org/docs/app/getting-started/server-and-client-components`
- `https://nextjs.org/docs/app/getting-started/metadata-and-og-images`
- `https://nextjs.org/docs/app/api-reference/components/link`
- `https://nextjs.org/blog` — estado de releases e atualização de segurança de julho de 2026.
- `https://playwright.dev/docs/release-notes` — versão 1.60 e versões posteriores; manter 1.60.0 nesta story.

### Estado atual dos arquivos que provavelmente serão alterados

- `apps/web/src/app/(public)/page.tsx`
  - Estado atual: home pública consome `publicContent.home.hero`, `modalityCards` e `nextPaths`.
  - Mudança esperada: renderizar “Mais procurados” a partir de novo conteúdo tipado; remover texto de que a seção entra na Story 1.4.
  - Preservar: `h1` único, Server Component, links internos com `next/link`, ausência de catálogo/preço/carrinho real.

- `apps/web/src/i18n/publicContent.types.ts`
  - Estado atual: contrato tipado cobre `brand`, `metadata`, `navigation`, `cta`, `home.hero`, `home.modalityCards`, `home.nextPaths`, `footer`, `placeholders` e `qualityCopy`.
  - Mudança esperada: adicionar contrato para `home.mostWanted` ou equivalente.

- `apps/web/src/i18n/publicContent.ts`
  - Estado atual: conteúdo `pt-BR` centralizado, incluindo a mensagem “A listagem de ‘Mais procurados’ entra na Story 1.4”.
  - Mudança esperada: incluir textos/cards da nova seção; atualizar metadata da home se necessário.

- `apps/web/src/features/public-store/publicLayoutContent.ts`
  - Estado atual: façade para conteúdo público.
  - Mudança esperada: nenhuma ou export opcional se a página precisar de alias específico.

- `apps/web/src/app/globals.css`
  - Estado atual: tokens visuais, hero, cards conceituais, entry section, header/footer, placeholders e responsividade base.
  - Mudança esperada: estilos para seção/cards de “Mais procurados”.

- `apps/web/tests/e2e/foundation.spec.ts`
  - Estado atual: valida fundação pública, conteúdo PT-BR/UTF-8, modalidades, header/footer, mobile 320 px, placeholders, metadata, `/health` e `/api/health`.
  - Mudança esperada: acrescentar cobertura da seção “Mais procurados” sem afrouxar asserts existentes.

### Conteúdo recomendado para os cards

Usar exatamente três cards, na ordem abaixo, alinhados ao mockup `key-home-mobile.html`:

1. **Kit Festa Fazendinha**
   - Modalidade: `Produto físico personalizado`
   - Mensagem: exige escolha de quantidade, tema, nome/data e personalização antes do carrinho em stories futuras.
   - CTA: `Ver opções`
   - Destino: `/produtos`

2. **Convite Jardim Dourado**
   - Modalidade: `Convite digital personalizado`
   - Mensagem: não é entrega imediata; exige edição/criação, prévia e aprovação.
   - CTA: `Ver modelos`
   - Destino: `/buscar`

3. **Topo Céu Encantado**
   - Modalidade: `Produto digital`
   - Mensagem: sem personalização; compatível com Silhouette Studio; quando a compra estiver disponível, download após pagamento confirmado.
   - CTA: `Ver arquivo`
   - Destino: `/produtos`
   - Observação: não usar “Comprar agora” enquanto o destino for placeholder.

Não usar nomes de personagens famosos, marcas, propriedades protegidas, preço, promoção, desconto, prazo fechado ou “mais vendido” como fato medido. Antes de métricas reais, preferir “mais procurados” como curadoria editorial de entrada.

### UX/microcopy obrigatória

- Priorizar primeira dobra curta: marca, busca/acesso rápido e “Mais procurados” aparecem cedo.
- Em 320 x 800 px, o heading “Mais procurados” deve estar visível sem rolagem; anexar screenshot ao handoff da implementação.
- Exibir uma frase curta explicando que os cards são uma seleção editorial, não um ranking por vendas.
- Textos de botão curtos para 320 px.
- Produto personalizado não entra direto no carrinho.
- Produto Digital Pronto deve ser visualmente diferenciado e rotulado como digital.
- Convite digital personalizado deve informar que não é entregue imediatamente.
- Não usar “100% seguro”.
- Não usar WhatsApp como botão flutuante ou início obrigatório da compra.
- Não copiar a composição, paleta, claims ou imagens da referência Gio.

### Inteligência das stories anteriores

- Story 1.1 criou a fundação Next.js BFF + Laravel API + PostgreSQL + Redis + CI/CD. O repositório está fixado em Next.js `16.3.0`; não alterar a versão nesta story, e tratar a divergência com o canal Active LTS em avaliação técnica separada.
- Story 1.2 criou shell público, header, footer, placeholders, route group `(public)`, tokens visuais e testes de layout/mobile.
- Story 1.3 centralizou conteúdo público em `apps/web/src/i18n/`, definiu `pt-BR` como locale ativo, preparou `en`/`es` apenas como contrato futuro e endureceu testes de UTF-8/mojibake.
- A revisão/code review da 1.3 fortaleceu uso de `activeLocale`, centralização do skip link e regex de mojibake. Não desfazer esses ajustes.
- O E2E completo depende do BFF consultar a Laravel API em `127.0.0.1:8000`; se o teste completo falhar por API inativa, registrar evidência e rodar subset frontend, mas não marcar story concluída sem validação completa quando o ambiente estiver disponível.

### Anti-patterns proibidos

- Não usar Python em scripts, build, testes, tooling ou documentação do produto.
- Não alterar backend Laravel para esta story.
- Não implementar API fake de catálogo.
- Não criar dados de catálogo transacionais hardcoded no componente.
- Não exibir preço, subtotal, desconto, frete, disponibilidade, contagem de estoque ou prazo fechado; proibir também símbolos/moedas (`€`, `R$`, `USD`, `CHF`, `BRL`), valores como `0,00`, parcelamento, percentuais, “grátis”, “a partir de”, “desde”, “promoção” e “mais vendido”.
- Não simular carrinho, checkout, pagamento, login, briefing ou suporte.
- Não criar botão permanente de WhatsApp.
- Não adicionar biblioteca de UI, carrossel, animação ou analytics.
- Não alterar `package.json` nem lockfile.
- Não adicionar imagem pesada ou asset copiado da referência Gio.
- Não quebrar `lang="pt-BR"` nem introduzir mojibake.
- Não mover textos editoriais de volta para componentes React.

### Testing Requirements

Mínimo obrigatório:

- `npm run lint`
- `npm run typecheck`
- `npm run build`
- `npm run test:e2e` com Laravel API ativa

Cobertura mínima:

- Home renderiza heading principal e seção `h2` “Mais procurados”.
- Os três cards aparecem na ordem “Kit Festa Fazendinha”, “Convite Jardim Dourado” e “Topo Céu Encantado”.
- Cards diferenciam modalidade por texto visível, não apenas por cor.
- Produto Digital Pronto mostra “Produto digital”, “Silhouette Studio” e descreve download após pagamento confirmado como comportamento futuro, não ativo.
- Convite digital personalizado informa que exige edição/criação, prévia e aprovação.
- CTAs têm `href` para rotas públicas existentes.
- Produtos personalizados não exibem CTA de carrinho.
- Home não exibe preço real, subtotal, checkout, pagamento ou formulário transacional.
- Viewport 320 px não tem overflow horizontal crítico.
- Em 320 x 800 px, o `h2` “Mais procurados” está dentro da primeira viewport e há screenshot de evidência.
- Home tem title/description verificáveis, é indexável e não contém `noindex`/`nofollow`.
- Áreas visuais decorativas usam `aria-hidden="true"`; áreas informativas possuem texto alternativo ou nome acessível.
- A home continua Server Component, sem `use client` na page ou na nova seção.
- Conteúdo visível e head continuam sem mojibake.
- Testes existentes de `/health` e `/api/health` continuam passando.

### Definition of Done

- Story implementada somente no escopo de home pública/frontend.
- Seção “Mais procurados” criada e visível cedo na home.
- Três cards específicos e ordenados renderizados com conteúdo centralizado e tipado, sem duplicação dos atuais `modalityCards` na hero.
- CTAs levam a rotas públicas existentes sem simular fluxo transacional.
- Produto físico personalizado, convite digital personalizado e Produto Digital Pronto são diferenciados com copy adequada.
- Home continua em português do Brasil, UTF-8 e sem mojibake.
- Metadata da home segue coerente com descoberta pública e não contém `noindex`/`nofollow`.
- Layout mobile-first preservado em 320 px.
- Acessibilidade mínima preservada: headings, links, foco visível, nomes acessíveis e alvo de toque.
- Nenhuma regra de catálogo, preço, carrinho ou compra foi colocada no frontend.
- Backend Laravel não foi alterado.
- `/health`, `/api/health`, placeholders e `noindex` de `/carrinho` e `/entrar` preservados.
- Checks locais relevantes passam.
- Nenhum Python adicionado.

### Revisão adversarial da criação da story

Revisão registrada em `_bmad-output/implementation-artifacts/reviews/review-1-4-criar-a-home-publica-com-entrada-para-os-produtos-mais-procurados-adversarial.md`.

Revisão complementar registrada em `_bmad-output/implementation-artifacts/reviews/review-1-4-ready-for-dev-adversarial-2026-08-08.md`.

Ajustes aplicados após a revisão:

- “Mais procurados” tratado como curadoria editorial inicial, não ranking medido.
- Critério de posição da seção tornado verificável.
- CTA “Comprar agora” removido desta story; todos os destinos atuais permanecem não transacionais e honestos.
- Busca descrita como link/entrada placeholder, não busca funcional.
- Contrato de conteúdo definido como editorial/UI, não transacional.
- Testes exigidos para posição da seção, overflow 320 px, CTAs, ausência de preço/ranking real e atualização de `qualityCopy`.
- Cards, ordem, primeira viewport, metadata indexável, acessibilidade visual, Server Component e lockfile foram fechados como critérios verificáveis.

## Dev Agent Record

### Agent Model Used

Codex GPT-5.

### Implementation Plan

- Estender o contrato de conteúdo público com uma curadoria editorial tipada para os três destaques.
- Substituir os cards conceituais duplicados da hero pela seção semântica “Mais procurados”, mantendo a home como Server Component.
- Compactar o layout mobile com tokens existentes e áreas visuais editoriais leves, sem assets ou dependências novas.
- Cobrir ordem, CTAs, SEO, acessibilidade, primeira viewport, ausência de conteúdo transacional e regressões por Playwright.
- Validar contra Laravel, PostgreSQL e Redis reais e executar revisão adversarial antes do handoff.

### Debug Log References

- 2026-08-08 — Customização `bmad-dev-story` resolvida manualmente por leitura dos TOMLs; `baseline_commit` registrado como `78e8dcd052a2698f353b2366db0b9a4f02e0cbe8`.
- 2026-08-08 — RED confirmado: 3/4 novos testes falharam pela ausência de “Mais procurados”; o teste de Server Component passou sobre a base existente.
- 2026-08-08 — GREEN/REFACTOR: contrato `home.mostWanted`, seção editorial, estilos mobile-first e testes E2E implementados; CSS legado dos `modalityCards` removido.
- 2026-08-08 — Primeira regressão completa: 13/14 passaram; wrapper interno `<header>` violava o smoke de um único header global e foi substituído por `<div>`.
- 2026-08-08 — Docker Desktop iniciado; PostgreSQL e Redis ficaram saudáveis; Laravel respondeu `ok` para app, database e redis em `/api/v1/health`.
- 2026-08-08 — Revisão adversarial registrada em `_bmad-output/implementation-artifacts/reviews/review-1-4-implementation-adversarial-2026-08-08.md`; 12 achados claros tratados.
- 2026-08-08 — Validação final aprovada: `npm run lint`, `npm run typecheck`, `npm run build` e `npm run test:e2e` com 14/14 testes.
- 2026-08-08 — Evidência visual de produção gerada em 320 x 800 px, sem indicador de desenvolvimento.

### Completion Notes List

- Home agora exibe exatamente “Kit Festa Fazendinha”, “Convite Jardim Dourado” e “Topo Céu Encantado”, nessa ordem, como seleção editorial explicitamente não baseada em ranking.
- Conteúdo, modalidades, CTAs e destinos estão centralizados e tipados em `publicContent`; nenhum catálogo, preço, disponibilidade ou regra comercial foi criado no frontend.
- Hero foi compactada e os antigos `modalityCards` foram removidos, evitando duplicação das modalidades.
- CTAs usam apenas rotas públicas existentes e nomes acessíveis contextualizados; Produto Digital Pronto usa “Ver arquivo”, sem simular compra.
- Cards possuem headings associados aos `article`, áreas visuais decorativas ocultas de leitores de tela e alvos de toque mínimos de 44 x 44 px.
- Home continua estática/Server Component, indexável, com metadata PT-BR, um único `h1` e `h2` “Mais procurados” dentro da primeira viewport 320 x 800 px.
- Header mobile foi compactado com fonte mínima de 12 px e os cards usam somente tokens visuais existentes, sem imagens, bibliotecas ou animações novas.
- `apps/api`, `apps/web/package.json` e `apps/web/package-lock.json` permaneceram inalterados.
- Revisão adversarial concluída; os 12 achados registrados foram corrigidos ou cobertos por evidência/teste.

### File List

- `_bmad-output/implementation-artifacts/1-4-criar-a-home-publica-com-entrada-para-os-produtos-mais-procurados.md`
- `_bmad-output/implementation-artifacts/evidence/story-1-4-home-mobile-320x800.png`
- `_bmad-output/implementation-artifacts/reviews/review-1-4-criar-a-home-publica-com-entrada-para-os-produtos-mais-procurados-adversarial.md`
- `_bmad-output/implementation-artifacts/reviews/review-1-4-implementation-adversarial-2026-08-08.md`
- `_bmad-output/implementation-artifacts/reviews/review-1-4-ready-for-dev-adversarial-2026-08-08.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `apps/web/src/app/(public)/page.tsx`
- `apps/web/src/app/globals.css`
- `apps/web/src/i18n/publicContent.ts`
- `apps/web/src/i18n/publicContent.types.ts`
- `apps/web/tests/e2e/foundation.spec.ts`

### Change Log

- 2026-08-06 — Story criada via `bmad-create-story` com escopo de home pública e entrada para “Mais procurados”.
- 2026-08-08 — Revisão adversarial complementar aplicada; cards, primeira dobra, CTAs, SEO, acessibilidade e limites de dependência fechados como critérios verificáveis.
- 2026-08-08 — Story 1.4 implementada com curadoria editorial tipada, layout mobile-first, cobertura E2E completa e 12 achados da revisão adversarial tratados.
- 2026-08-09 — Code review concluído; três patches aplicados e story promovida para `done`.
