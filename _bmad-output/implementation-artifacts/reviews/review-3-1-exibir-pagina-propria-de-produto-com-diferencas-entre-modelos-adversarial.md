# Revisao Adversarial Geral - Story 3.1

Artefato revisado: `_bmad-output/implementation-artifacts/3-1-exibir-pagina-propria-de-produto-com-diferencas-entre-modelos.md`

Data: 2026-09-22

## Achados

- A story precisava limitar explicitamente a quantidade de modelos; sem limite, o dev poderia expor payload grande, UI longa e consulta sem budget claro. Corrigido com limite de 12 modelos por produto.
- A galeria nao tinha teto de imagens; isso deixava margem para N+1, CLS e pagina pesada. Corrigido com limite de 8 imagens por produto.
- O uso potencial de `variants_reference` estava perigoso por permitir interpretacao livre de string/JSON. Corrigido exigindo parser estruturado, schema e `additionalProperties: false`.
- A selecao de modelo poderia depender demais de JavaScript. Corrigido exigindo que todas as diferencas continuem visiveis sem JS e que o JS seja apenas melhoria local.
- A extensao de contrato nao exigia limites de tamanho para campos novos. Corrigido com limites para modelos e exigencia de limites explicitos no OpenAPI/BFF.
- O criterio de contrato nao citava `additionalProperties: false`, apesar de o padrao do projeto depender disso para impedir vazamento de campo extra. Corrigido no AC 4.
- A story nao deixava claro como tratar CTA quando carrinho/configuracao ainda forem placeholders. Corrigido exigindo comunicacao transparente sem simular compra.
- Faltava exigir teste de query budget para detalhe enriquecido. Corrigido em "Testes esperados".
- A story citava textos longos novos sem regra de apresentacao/truncamento. Corrigido com regra para limites e metadata curta/sanitizada.
- A comparacao de modelos precisava especificar identificador publico estavel e ordem deterministica. Corrigido no AC 3.
- A story corria o risco de permitir que preco por modelo virasse regra no frontend. Mitigado com nota clara: preco especifico so aparece se vier do Laravel como campo publico validado; calculo final fica para 3.2+.
- A story precisava reforcar que as imagens de modelos seguem as mesmas regras de imagem publica da galeria. Mitigado pelas regras de imagem segura e payload allowlist.

## Resultado

Todos os achados documentais foram aplicados diretamente na story antes da promocao para ready-for-dev. Nenhuma pendencia de julgamento humano foi identificada nesta revisao documental.
