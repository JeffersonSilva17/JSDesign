# Estrutura modular inicial

Esta pasta reserva a separação arquitetural aprovada para os domínios futuros da JS Designs.

Cada módulo deve manter as camadas:

- `Domain`: regras e conceitos de negócio puros, sem dependência de HTTP, Eloquent, filas, storage ou gateways.
- `Application`: casos de uso e orquestração.
- `Infrastructure`: Eloquent, repositories, queries, migrations técnicas e adapters.
- `Interfaces/Http`: controllers, requests/resources e rotas HTTP.

Módulos implementados:

- `Promotions`: captura e emissão do cupom de primeira compra.
- `Catalog`: cadastro administrativo, persistência e publicação de produtos; consulte `Catalog/README.md`.

Novas regras de e-commerce continuam restritas às stories correspondentes.
