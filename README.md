# JS Designs — fundação técnica

Monorepo inicial da loja online JS Designs.

## Catálogo público

`/produtos` e `/categorias` são Server Components que consultam o Laravel somente pelo BFF server-side. A API oferece listagem, facetas, detalhe, busca e projeções de sitemap sob `/api/v1/catalog`, com `Cache-Control: no-store`, throttle e statement timeout configuráveis. Listagem/busca limitam páginas a 48 itens; sitemap usa lotes fixos de 500 slugs.

Fotos reais continuam fail-closed: `storage_reference` nunca sai do backend e só um adapter aprovado poderá convertê-la em path same-origin. Categorias precisam ser provisionadas operacionalmente. Busca e SEO/canonical foram implementados nas stories 2.3 e 2.4; detalhe completo/compra pertencem aos Epics 3 e 4.

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

Depois de subir a infraestrutura, configure `BOOTSTRAP_DB_USERNAME` e `BOOTSTRAP_DB_PASSWORD` no ambiente com a conta administrativa **local** do PostgreSQL e execute `php apps/api/scripts/provision-local-security.php`. O script preserva dados, provisiona `jsdesign_runtime` (DML, sem DDL/superuser) e `jsdesign_migrator` (migrações, sem superuser), gera senhas aleatórias e a chave compartilhada do sitemap, e grava somente arquivos `.env` ignorados pelo Git. Remova as variáveis de bootstrap da sessão depois. O bootstrap do Compose usa credenciais descartáveis restritas ao loopback; elas nunca são as credenciais da aplicação.

Migrações locais usam `php artisan migrate --env=migrations`; testes PHP usam `.env.testing` e exclusivamente `jsdesign_test`. A API e workers usam `.env` com o papel de runtime. Playwright prepara a base com o migrator e atende HTTP com runtime. Não execute migrações com a conta da aplicação. O provisionamento rotaciona credenciais e exige reiniciar os processos locais já abertos.

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

Para o smoke/e2e, deixe as portas 3000 e 8000 livres: Playwright inicia os servidores. Garanta que `apps/web/.env.local` contenha:

```env
API_INTERNAL_URL=http://127.0.0.1:8000
```

## Limites atuais

O backend implementa o cadastro administrativo e a publicação do catálogo. As escritas permanecem fail-closed até a integração de uma identidade administrativa e de um mecanismo aprovado de arquivos.

A listagem, a busca e o SEO públicos estão implementados. Carrinho, checkout, autenticação completa, briefing, aprovação de arte, pagamento, entrega, suporte funcional e painel administrativo visual continuam fora desta etapa.

### Origem pública e indexação

O web exige `SITE_URL` explícita no build e no runtime: somente uma origem HTTPS, sem credenciais, caminho, query ou fragmento. HTTP é permitido apenas em loopback para desenvolvimento/CI. Não há inferência por Host ou pela URL interna da API.

Configure `SEO_INDEXING_ENABLED=false` em local, preview e homologação (padrão). Use `true` somente no ambiente público autorizado. Valores diferentes de `true`/`false` falham com erro sanitizado. O modo desabilitado aplica `X-Robots-Tag: noindex, nofollow`, omite canonical/JSON-LD e retorna 404 nos documentos XML sem consultar a API; robots permite ler o noindex e não anuncia sitemap. CI constrói e testa nos dois modos. Para comandos locais, carregue as variáveis de `apps/web/.env.example` no ambiente.

O índice `/sitemap.xml` anuncia `/sitemap-editorial.xml` e `/sitemap-catalogo.xml?page=N`. Cada filho lê somente um lote de 500 slugs publicados; o editorial aceita até 4998 categorias, além das duas páginas de entrada. Não há snapshot entre requisições nem cache persistente. A API usa uma cota dedicada (`CATALOG_SITEMAP_RATE_LIMIT_PER_MINUTE`, padrão 60), separada da navegação. Timeout total do BFF: 5 s; limites JSON: 256 KiB por lote e 4 MiB editorial; XML: 50 MiB. Falhas retornam JSON sanitizado e HTTP 503, ou 429 com Retry-After validado. Queries inválidas retornam 422; lote inexistente, 404.

Execute `npm run test:seo` para a política SEO e transporte limitado. O benchmark PostgreSQL é executável com `CATALOG_SITEMAP_BENCHMARK=1 php artisan test --filter=CatalogSitemapBenchmarkTest` (banco de testes). Volume medido: 100 mil produtos; o limite contratual de cinco milhões não representa capacidade operacional validada. Repetir a medição antes de ampliar o volume. As tags e imagens são verificadas por HTTP; isso não equivale a uma prévia validada em redes sociais ou elegibilidade a rich results.

O runtime web deve usar `npm run start` (servidor Node próprio), com `SITEMAP_CLIENT_KEY` igual à configuração privada da API: 32 bytes aleatórios codificados como 64 caracteres hexadecimais. O servidor deriva a identidade do socket e sobrescreve `X-Catalog-Client`; o BFF encaminha um identificador HMAC sem IP em claro, com assinatura válida por 30 segundos. A API verifica a assinatura antes de aplicar a cota. Acesso direto à API usa o endereço do socket e ignora headers de identidade não assinados. O índice consome duas leituras da cota. Sem a chave, o runtime falha no startup. `next start` direto, runtimes edge/serverless e `output: standalone` não substituem esse servidor.

Atrás de um proxy, configure `CATALOG_TRUSTED_PROXY_IPS` com os IPs exatos dos sockets autorizados e faça o proxy **sobrescrever** `X-Real-IP` com um único IP do visitante. Restrinja o acesso ao origin; nunca confie em headers fornecidos pelo visitante. Sem proxy, deixe a lista vazia. A variável `HOSTNAME_BIND` controla a interface (padrão loopback); `PORT`, a porta. O fallback `/catalog-social.png` é prerenderizado no build, sem renderização de imagem por acesso.

Segurança: `node scripts/scan-secrets.mjs` na raiz verifica arquivos atuais, inclusive não commitados, sem ler `.env` ignorados; o workflow `security` também verifica o histórico, SAST e dependências. As exceções do Gitleaks combinam caminho e conteúdo específicos de exemplos/checksums BMAD. `AGENTS.md` registra as políticas de revisão, comandos e navegação; configurações do cliente/IDE são externas ao repositório.
