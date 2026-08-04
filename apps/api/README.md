# JS Designs API

Aplicação Laravel API da loja online JS Designs.

Esta pasta contém somente o backend REST da plataforma. A loja pública e o BFF ficam em `../web`.

## Comandos principais

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan serve --host=127.0.0.1 --port=8000
```

## Testes

```powershell
composer test
vendor/bin/pint --test
```

## Escopo da Story 1.1

Esta API expõe apenas a fundação técnica e o endpoint `GET /api/v1/health`.

Catálogo, carrinho, checkout, pedidos, briefing, produção, entrega, suporte funcional e painel administrativo serão implementados nas stories correspondentes.
