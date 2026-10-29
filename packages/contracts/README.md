# Contratos compartilhados

Espaço reservado para contratos entre o Next.js BFF e a Laravel API.

O contrato público versionado do catálogo está em `catalog-public-v1.openapi.yaml`. Ele cobre listagem, detalhe mínimo, facetas e busca por intenção somente de produtos publicados, com paginação limitada, allowlist explícita e erros sanitizados. A busca possui envelope fechado para grupos exatos, semelhantes, sugestões editoriais seguras, intenção e totais coerentes. O contrato administrativo continua separado em `catalog-admin-v1.openapi.yaml`.

O contrato de inclusão server-owned de itens configurados está em `cart-v1.openapi.yaml`. O navegador acessa essa superfície somente pelo BFF; a API exige contexto interno autenticado, `Idempotency-Key`, schema fechado, limite de corpo de 16 KiB e erros sanitizados.

Uso previsto:

- OpenAPI/JSON Schema quando os endpoints forem formalizados.
- Tipos derivados de contratos, não regras de negócio.
- Versionamento explícito quando houver mudanças incompatíveis.

Contratos atuais:

- `catalog-admin-v1.openapi.yaml`: escritas administrativas do catálogo, concorrência por `version` e erros estáveis.
- `pricing-v1.openapi.yaml`: cotação pública sem reserva de disponibilidade.
- `cart-v1.openapi.yaml`: inclusão idempotente de itens personalizados no carrinho convidado.

Os contratos descrevem transporte e projeções. Invariantes comerciais continuam sendo autoridade do domínio Laravel.
