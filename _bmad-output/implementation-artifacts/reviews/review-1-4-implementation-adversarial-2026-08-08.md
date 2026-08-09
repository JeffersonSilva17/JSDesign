# Revisão adversarial da implementação — Story 1.4

- A captura anexada pelo teste usa `fullPage: true`, embora o nome declare 320 x 800; o arquivo não comprova visualmente a primeira viewport exata.
- A evidência manual foi capturada no servidor de desenvolvimento e contém o indicador visual do Next.js, contaminando a revisão da interface entregue.
- A navegação mobile reduz os links para `0.6875rem` (11 px), abaixo do papel tipográfico mínimo aprovado e com legibilidade fraca em 320 px.
- A copy pública do Produto Digital Pronto expõe o jargão interno “story futura”, inadequado para a cliente.
- Os elementos `article` dos produtos não possuem nome acessível programático associado ao respectivo heading.
- O teste de alvo de toque verifica somente altura mínima, deixando a largura de 44 px sem cobertura.
- O teste de posição compara “Mais procurados” com “Continue explorando”, mas não prova que a seção é irmã imediatamente posterior à hero.
- O teste de Server Component não aceita espaços antes da diretiva e pode deixar passar `use client` indentado.
- Os campos `cta.description` dos três destaques são preenchidos, mas nunca renderizados ou consumidos; isso cria conteúdo morto e um falso contrato de acessibilidade.
- A remoção dos antigos `modalityCards` não está protegida por assert explícito, permitindo reintrodução futura da duplicação na hero.
- A classe `hero--compact` é adicionada ao JSX sem regra correspondente, criando sinalização de comportamento inexistente.
- A captura persistente não é gerada pelo teste e a configuração atual do reporter não preservou o attachment após uma execução verde, deixando a evidência dependente de ação manual não documentada.
