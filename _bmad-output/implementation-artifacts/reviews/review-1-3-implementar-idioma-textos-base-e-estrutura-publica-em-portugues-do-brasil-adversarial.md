# Revisão adversarial — Story 1.3

Data: 2026-08-05

Artefatos revisados:

- `_bmad-output/implementation-artifacts/1-3-implementar-idioma-textos-base-e-estrutura-publica-em-portugues-do-brasil.md`
- `apps/web/src/i18n/`
- `apps/web/src/features/public-store/publicLayoutContent.ts`
- `apps/web/src/app/(public)/`
- `apps/web/src/components/layout/`
- `apps/web/tests/e2e/foundation.spec.ts`

## Achados

- A suíte E2E completa ainda falha no teste BFF porque a Laravel API não está ativa em `127.0.0.1:8000`, PostgreSQL não responde em `5432` e Redis não responde em `6379`; isso impede declarar a Definition of Done completa.
- A execução paralela de Playwright com ESLint pode causar condição de corrida em `test-results`; validações devem ser rodadas sequencialmente quando envolverem Playwright e lint.
- O cache gerado `.next/dev/types/validator.ts` estava corrompido antes da validação e precisou ser regenerado/corrigido localmente; esse tipo de estado não deve ser confundido com erro do código versionado.
- Os novos cards da home passaram a renderizar parágrafos dentro de `.product-preview`; sem CSS específico, havia risco de regressão visual e de espaçamento no mobile.
- A primeira versão dos testes usava `getByText` em strict mode para frases que aparecem em mais de um lugar, criando falso negativo não relacionado ao comportamento da aplicação.
- A cobertura inicial de UTF-8 focava na home e poderia deixar mojibake passar em placeholders; a checagem foi ampliada no loop de rotas placeholder.
- A cobertura inicial não validava metadata pública de placeholder, embora a story peça centralização de metadata; foi adicionado teste específico para `/produtos`.
- O termo `Silhouette Studio` estava previsto na story como termo aprovado, mas não aparecia em texto visível; foi incluído no contexto de Produto Digital Pronto.
- A implementação precisa continuar sem rotas localizadas, seletor de idioma, cookies, `proxy.ts` ou biblioteca i18n; a revisão confirmou que esses itens não foram adicionados.
- A implementação precisa continuar sem backend Laravel, carrinho, checkout, preço, cupom, frete ou pagamento; a revisão confirmou que esses itens não foram implementados no frontend.

## Resultado

Achados de código/teste tratados. Bloqueio restante é ambiental: ativar Laravel API + PostgreSQL + Redis para permitir que `npm run test:e2e` passe integralmente.

