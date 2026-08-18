# Contratos compartilhados

Espaço reservado para contratos entre o Next.js BFF e a Laravel API.

Uso previsto:

- OpenAPI/JSON Schema quando os endpoints forem formalizados.
- Tipos derivados de contratos, não regras de negócio.
- Versionamento explícito quando houver mudanças incompatíveis.

Contratos atuais:

- `catalog-admin-v1.openapi.yaml`: escritas administrativas do catálogo, concorrência por `version` e erros estáveis.

Os contratos descrevem transporte e projeções. Invariantes comerciais continuam sendo autoridade do domínio Laravel.
