# Revisão Adversarial Terminal — Story 2.2

- O bloqueio de Docker/PostgreSQL impedia validar Feature tests e E2E integrado, então a story não podia sair de `in-progress` sem recuperar infraestrutura local real.
- A evidência de CWV/NFR-2 estava declarada como pendente e precisava virar teste executável por viewport, não apenas uma observação manual sobre aspect ratio.
- A suíte Playwright precisava cobrir a medição local de LCP, CLS e latência de interação em 320, 420, 760 e 1100 px para não deixar uma lacuna exatamente no critério quantitativo.
- A validação de query bruta precisava rejeitar formatos que Laravel poderia normalizar ou colapsar antes da camada de domínio, incluindo chaves duplicadas, array-style e percent-encoding inválido.
- A API pública não podia mascarar exceções internas de programação como indisponibilidade sanitizada, porque isso esconderia regressões reais atrás de `503`.
- A projeção pública não podia manter ramo legado capaz de reaproveitar payload administrativo ou devolver campos não previstos na allowlist.
- O rate limit público não podia usar IP bruto como chave persistente de cache/log, mesmo quando resolvido pelo mecanismo confiável do framework.
- O BFF não podia aceitar path de imagem apenas por formato; o path também precisava existir sob o `public` servido pelo Next.js para evitar card quebrado validado como seguro.
- A navegação do detalhe precisava preservar retorno filtrado e paginado sem aceitar URL externa, hash, query duplicada ou parâmetro fora do contrato público.
- A página de listagem não podia cair inteira quando somente facetas falhassem; a listagem principal precisava continuar renderizando com estado sanitizado e facetas vazias.
- O OpenAPI público precisava fechar `additionalProperties` e allowlists também nos objetos aninhados, não apenas no envelope principal.
- O teste E2E do catálogo precisava ser estável contra a captura promocional de primeira compra para não transformar um fluxo alheio em falso negativo da story.
- A documentação precisava deixar claro que fotos reais continuam fail-closed e que o fixture E2E não aprova storage/CDN ou adapter de mídia em produção.
- Os gates finais precisavam ser reexecutados depois das correções adversariais, porque resultados anteriores ao patch não comprovam o estado atual.
