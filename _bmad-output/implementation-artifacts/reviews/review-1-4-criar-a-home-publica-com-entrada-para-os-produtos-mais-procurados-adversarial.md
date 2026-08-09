# Revisão adversarial — Story 1.4: Criar a home pública com entrada para os produtos mais procurados

Data: 2026-08-06

## Conteúdo revisado

`_bmad-output/implementation-artifacts/1-4-criar-a-home-publica-com-entrada-para-os-produtos-mais-procurados.md`

## Achados

- A expressão “Mais procurados” pode ser lida como métrica real de vendas/busca, mas o projeto ainda não tem catálogo, analytics ou métricas operacionais implementadas.
- “Aparecer cedo” é uma exigência fraca se não definir posição relativa na página ou um critério verificável no mobile.
- A aceitação permite “área visual ou imagem acessível”, mas não define quando a área visual é decorativa ou quando precisa de texto alternativo/nome acessível.
- O CTA “Comprar agora” para Produto Digital Pronto é arriscado porque a compra real ainda não existe; sem microcopy obrigatória próxima, a cliente pode entender que há checkout funcional.
- A story menciona “entrada de busca”, mas o app atual tem rota/link de busca placeholder, não um campo de busca real; isso pode induzir implementação fora de escopo.
- A preparação para BFF/Laravel está correta, mas precisa deixar explícito que o contrato criado agora é editorial/UI, não contrato transacional de catálogo.
- A lista sugerida de cards usa exemplos amplos; faltava orientar que “mais procurados” é curadoria editorial inicial até existirem métricas reais.
- A proibição de preço real não cobre variações comuns como “a partir de”, “desde”, “subtotal”, “em promoção” ou desconto visual.
- A story não exigia atualizar `qualityCopy` ou fixture equivalente para que os testes possam validar novos textos de forma centralizada como na Story 1.3.
- Os destinos dos cards estão permissivos demais; sem regra, alguém pode criar slugs futuros ou links quebrados em vez de usar rotas públicas existentes.
- A seção “Mais procurados” poderia ser implementada depois do bloco “Próximos caminhos”, contrariando a intenção de aparecer logo de cara.
- Se imagens forem adicionadas, faltava proibir asset externo/remoto e reforçar que não se deve copiar a referência Gio nem usar foto sem fonte/licença.
- A Definition of Done não mencionava explicitamente que a copy antiga dizendo que a seção pertence à Story 1.4 deve desaparecer da home.
- O teste de “sem overflow horizontal crítico” precisa ser amarrado ao viewport 320 px e ao body/documentElement, caso contrário pode passar sem realmente cobrir o problema.
