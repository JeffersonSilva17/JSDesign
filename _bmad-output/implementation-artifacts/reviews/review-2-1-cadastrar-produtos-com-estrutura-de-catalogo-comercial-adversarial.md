# Revisão adversarial - Story 2.1

Data: 2026-08-13

Alvo: `_bmad-output/implementation-artifacts/2-1-cadastrar-produtos-com-estrutura-de-catalogo-comercial.md`

## Achados

- A story tratava autenticação fail-closed como solução implementável, mas poderia ser lida como endpoint operacional mesmo sem estratégia administrativa aprovada. Isso criava risco de handoff incorreto para produção.
- A fronteira de arquivos citava um "mecanismo de arquivos aprovado" sem explicar como o dev deve validar referências enquanto o storage final segue deferido pela arquitetura.
- A story exigia imagem principal na publicação, mas não dizia explicitamente que a referência precisa ser validada por uma porta/adaptador, não apenas por formato de payload.
- O escopo inclui proteção contra URL/path/base64/SVG, mas sem fake de teste explícito o dev poderia deixar apenas validação negativa superficial no Form Request.
- A redação original não separava com força suficiente "desenvolvimento testável" de "operação real", especialmente para auth e arquivos, que são duas decisões arquiteturais adiadas.
- A story expande a AC original de Story 2.1 para cobrir quase todo o FR-5. Isso é defensável pelo PRD, mas aumenta a carga de implementação e exige disciplina para não invadir preço dinâmico, configuradores e UI administrativa.
- O requisito de personagens/ativos protegidos permite rascunho com estado `pending`, enquanto o PRD diz que ativos protegidos somente podem ser cadastrados após verificação. A story precisa ser interpretada como "rascunho administrativo não publicável", não como produto vendável no catálogo.
- A normalização por acentuação equivalente exige comportamento Unicode determinístico. A story não nomeia extensão ou fallback, então o dev deve provar isso em testes antes de assumir que o ambiente PHP já cobre o caso.
- A modelagem de variante é propositalmente mínima, mas "quantidade mínima ou referência de variante" ainda pode virar uma pseudo-variante sem constraints se o dev não limitar a fatia ao necessário para publicação.
- A exigência de "não revelar existência" em escritas administrativas precisa ser conciliada com contratos `401`, `403`, `404` e `409`; a story não define a matriz completa de respostas para produto inexistente versus produto sem permissão.
- A prova de que campos administrativos nunca entram no contrato público é parcialmente prospectiva, porque Story 2.2 ainda não existe. O melhor que a Story 2.1 pode exigir é separar Resource/DTO administrativo de uma projeção pública futura.
- O contrato de erros tipados menciona `message_key`, `field_path`, `recoverable` e `next_action?`, mas não fixa um envelope comum. Sem isso, endpoints podem ficar coerentes isoladamente e inconsistentes entre módulos.

## Correções aplicadas

- A seção de autenticação foi ajustada para declarar que a story pode ser desenvolvida e testada com fronteira fail-closed, mas não deve ser considerada operacional em ambiente real sem estratégia administrativa aprovada.
- Foi adicionada uma seção de fronteira de arquivos para exigir porta de validação, adapter de produção fail-closed e fake explícito em testes para referência válida, inexistente, rejeitada e entradas inseguras.

## Risco residual

A story está pronta para desenvolvimento técnico, mas o endpoint administrativo só deve ser promovido a uso operacional depois de uma decisão explícita de autenticação administrativa e mecanismo de arquivos/storage. Isso é coerente com a arquitetura atual, que deferiu essas escolhas atrás de portas/adaptadores.
