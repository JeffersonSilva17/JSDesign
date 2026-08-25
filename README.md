# JS Designs — fundação técnica

Monorepo inicial da loja online JS Designs.

## Catálogo público

`/produtos` e `/categorias` são Server Components que consultam o Laravel somente pelo BFF server-side. A API oferece os três GETs sob `/api/v1/catalog`, sempre com `Cache-Control: no-store`, paginação máxima de 48, throttle e statement timeout configuráveis.

Fotos reais continuam fail-closed: `storage_reference` nunca sai do backend e só um adapter aprovado poderá convertê-la em path same-origin. Categorias precisam ser provisionadas operacionalmente. Busca entra na Story 2.3, SEO/canonical na 2.4 e detalhe completo/compra nos Epics 3 e 4.

## Stack

- Frontend/BFF: Next.js 16.3, React 19.2 e TypeScript em `apps/web`.
- Backend/API: PHP 8.5 e Laravel 13 em `apps/api`.
- Runtime Node esperado para o frontend: Node 24.x.
- Banco principal: PostgreSQL 18.
- Cache/fila: Redis.
- CI/CD: GitHub Actions para backend, frontend e smoke/e2e.

Python não faz parte da stack do produto, dos scripts, do build ou dos testes.

## Portas locais

- Next.js Frontend/BFF: `http://127.0.0.1:3000`
- Laravel API: `http://127.0.0.1:8000`
- PostgreSQL: `127.0.0.1:5432`
- Redis: `127.0.0.1:6379`

## Preparar ambiente

Execute cada bloco abaixo a partir da raiz do repositório `J:\JSDESIGN`.

Backend:

```powershell
cd apps/api
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Frontend:

```powershell
cd apps/web
npm ci
Copy-Item .env.example .env.local
```

Infra local:

```powershell
docker compose -f infra/docker/compose.yaml up -d --wait
```

## Rodar localmente

Execute cada bloco abaixo a partir da raiz do repositório `J:\JSDESIGN`.

Em um terminal:

```powershell
cd apps/api
php artisan serve --host=127.0.0.1 --port=8000
```

Em outro terminal:

```powershell
cd apps/web
npm run dev
```

Worker de fila Laravel, quando houver jobs pendentes:

```powershell
cd apps/api
php artisan queue:work
```

O Redis deve estar ativo antes de iniciar o worker de fila.

Validações manuais:

- Laravel API: `http://127.0.0.1:8000/api/v1/health`
- Next/BFF: `http://127.0.0.1:3000/api/health`
- Página web: `http://127.0.0.1:3000`

## Testes e checks

Execute cada bloco abaixo a partir da raiz do repositório `J:\JSDESIGN`.

Backend:

```powershell
cd apps/api
composer test
vendor/bin/pint --test
```

Frontend:

```powershell
cd apps/web
npm run lint
npm run typecheck
npm run build
```

Smoke/e2e:

```powershell
cd apps/web
npx playwright install chromium
npm run test:e2e
```

Para o smoke/e2e, mantenha a Laravel API ativa em `127.0.0.1:8000` e garanta que `apps/web/.env.local` contenha:

```env
API_INTERNAL_URL=http://127.0.0.1:8000
```

## Limites atuais

O backend implementa o cadastro administrativo e a publicação do catálogo. As escritas permanecem fail-closed até a integração de uma identidade administrativa e de um mecanismo aprovado de arquivos.

Ainda não existem listagem/busca pública, carrinho, checkout, autenticação completa, briefing, aprovação de arte, pagamento, entrega, suporte funcional ou painel administrativo visual.
