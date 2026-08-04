# Revisão adversarial — Story 1.1: Inicializar a fundação técnica da plataforma

Data inicial: 2026-08-01  
Última atualização: 2026-08-04  
Alvo: `1-1-inicializar-a-fundacao-tecnica-da-plataforma.md`  
Status revisado: `ready-for-dev` antes do desenvolvimento; implementação local depois movida para `review`  
Tipo de conteúdo: revisão adversarial pré-desenvolvimento e pós-implementação

## Findings

- A story exige Laravel 13.x, Next.js 16.2.x, React 19.2.x, PHP 8.5.x, PostgreSQL 18.x e Playwright 1.60.x, mas não define uma política de travamento de versões em `composer.json`, `package.json`, lockfiles ou imagens Docker. Sem isso, o dev pode instalar versões incompatíveis ou patches diferentes e ainda alegar cumprimento.

- A story manda criar scaffold Laravel e Next.js, mas não define qual package manager JavaScript será usado. Sem escolher npm, pnpm ou yarn, o repositório pode nascer com lockfiles inconsistentes e CI instável.

- A story não define a versão de Node.js necessária para Next.js 16.2.x. Isso deixa o CI e o ambiente local vulneráveis a falhas por runtime incompatível.

- A story exige PHP 8.5.x, mas não define como isso será garantido localmente e no CI. Se a máquina ou o runner não tiver PHP 8.5 disponível, o dev pode baixar a exigência sem perceber ou quebrar o pipeline.

- A story manda criar PostgreSQL 18.x e Redis em `infra/docker/`, mas não especifica se o arquivo deve ser `docker-compose.yml`, `compose.yaml` ou outro padrão. Isso abre margem para documentação e comandos divergentes.

- A story pede “Laravel apontando para PostgreSQL e Redis”, mas não exige teste que prove que Laravel realmente consegue conectar ao banco e ao Redis. Um healthcheck que retorna JSON fixo pode passar mesmo com banco/cache quebrados.

- O endpoint `GET /api/v1/health` não tem contrato de resposta definido. Sem payload esperado, o frontend/BFF e os testes podem validar qualquer coisa como “saudável”.

- A story pede rota BFF para consultar o health da API, mas não define o caminho dessa rota no Next.js. O dev pode criar `/api/health`, `/bff/health`, `/health` ou outra rota, dificultando padronização futura.

- A story não define comportamento esperado quando a Laravel API estiver fora do ar. O BFF precisa responder com erro controlado e não vazar URL interna, stack trace, token, DSN ou detalhes de infraestrutura.

- A story diz para criar esqueleto de muitos módulos Laravel, mas isso pode gerar ruído arquitetural prematuro. Criar pastas vazias para 15 módulos dá falsa sensação de arquitetura implementada e pode levar futuros devs a colocar código em módulos sem padrões claros.

- A story exige Domain/Application/Infrastructure/Interfaces, mas não define namespace PHP, autoload PSR-4 ou convenção real para módulos. Sem isso, a estrutura pode existir no filesystem e ainda ser inútil para Laravel/Composer.

- A story menciona Eloquent restrito à Infrastructure, mas não exige uma regra automática ou documentação objetiva que impeça Models Eloquent em `Domain`. Sem enforcement, a regra depende apenas de disciplina.

- A story exige CI/CD com análise estática “quando configurada”, mas não decide se PHPStan/Larastan, Pint, ESLint ou TypeScript strict serão obrigatórios desde a fundação. Isso enfraquece o gate de qualidade que a própria story tenta criar.

- A story pede Playwright 1.60.x, mas não define se o e2e deve rodar contra app local com API real, API mockada ou apenas página estática. Um smoke test superficial pode passar sem validar a comunicação Frontend/BFF → Laravel API.

- A story não define estratégia de variáveis de ambiente para o BFF com clareza suficiente. Precisa distinguir variáveis server-only de variáveis públicas `NEXT_PUBLIC_*`, porque esse é exatamente o ponto onde segredos acabam vazando para o navegador.

- A story não especifica portas locais padrão para web, API, PostgreSQL e Redis. O README pode acabar documentando comandos que conflitam ou não batem com Docker/CI.

- A story não exige `.gitignore`/exclusões para `.env`, caches, vendors, `node_modules`, builds e artefatos locais. Em um scaffold inicial, isso é uma falha básica que pode levar a commit de lixo ou segredo.

- A story não define se o monorepo terá comandos raiz para orquestrar backend/frontend/testes. Sem comandos raiz, cada dev pode criar uma experiência operacional diferente e o README vira colagem de comandos manuais.

- A story não decide se `packages/contracts/` deve ficar vazio, conter README placeholder ou conter um contrato mínimo do healthcheck. Criar pasta vazia pode não ser versionado pelo Git e causar confusão.

- A story não exige evidência de que o CI consegue subir PostgreSQL e Redis durante testes. O workflow pode rodar testes em memória ou sem serviços, deixando a integração real sem cobertura.

- A story não define como a Laravel API será chamada pelo Next.js em ambiente local: URL interna Docker, localhost, nome de serviço ou variável externa. Esse detalhe é onde setups Docker frequentemente quebram.

- A story não define se os scaffolds devem ser criados por ferramentas oficiais ou manualmente. Isso pode resultar em estrutura não idiomática, especialmente para Laravel 13 e Next.js 16.

- A story não orienta como lidar com a pasta `prototype/`: ela diz para preservar, mas não diz se algum asset, token ou CSS deve ser copiado ou explicitamente ignorado na Story 1.1. Isso abre espaço para importar protótipo estático cedo demais.

- A story não tem critério explícito de “sem deploy”. Ela menciona em subtarefa, mas a Definition of Done não reforça isso. Um dev pode gastar tempo criando deploy/hosting antes da base estar estável.

- A story diz que “falhas desses checks devem bloquear aprovação técnica”, mas não há definição de branch protection ou de como isso será verificado se o repositório não tiver permissões GitHub configuradas. O máximo que a story pode exigir localmente é workflow presente e falhando corretamente.

- A story não exige um `README` curto de troubleshooting para erros previsíveis: porta ocupada, banco indisponível, Redis indisponível, migrations falhando, API URL incorreta e erro de CORS/BFF. Sem isso, a fundação pode subir para quem criou e falhar para qualquer outro dev.

- A story não define política de CORS. Mesmo com BFF, a API Laravel precisa ter postura clara: aceitar somente origens esperadas no desenvolvimento ou nem expor rotas diretas ao navegador. Deixar padrão pode criar bloqueios locais ou abertura excessiva.

- A story não define se a API health deve ficar pública, interna ou protegida. Healthcheck público pode vazar informações; healthcheck protegido pode quebrar o BFF e CI. A decisão precisa estar explícita.

- A story não define se o healthcheck deve verificar apenas processo vivo ou dependências. Se verificar tudo, uma falha de Redis derruba health geral; se não verificar nada, não prova a stack. Precisa separar `live` e `ready` ou definir escopo do health.

- A story não exige registro no `sprint-status.yaml` após implementação além do status atual de ready-for-dev. O fluxo metodológico novo pede revisão adversarial após implementação, mas a story não contém instrução para o dev não marcar `done` antes dessa revisão.

## Revisão adversarial pós-implementação

Data: 2026-08-04  
Alvo: implementação local não commitada da Story 1.1  
Tipo de conteúdo: diff/artefatos de scaffold Laravel API, Next.js BFF, Docker, CI/CD, README e story

## Findings pós-implementação

- O README ainda declarava Next.js 16.2 depois da decisão explícita de atualizar para 16.3.0, criando documentação operacional falsa para o próximo agente ou dev.

- O `package.json` usava `^16.3.0` para `next` e `eslint-config-next`, permitindo upgrades automáticos dentro da série 16 que poderiam alterar comportamento sem revisão arquitetural.

- O workflow `smoke.yml` iniciava a Laravel API em background e seguia imediatamente para instalar/rodar o frontend, deixando uma condição de corrida real onde o BFF poderia consultar a API antes de ela estar pronta.

- A story original e as tarefas continuam mencionando Next.js 16.2.x, enquanto a implementação usa 16.3.0 por decisão de segurança aprovada; isso precisa ficar explicitamente documentado no Dev Agent Record para não parecer desvio acidental.

- O healthcheck da Laravel API valida apenas que a aplicação responde, não que PostgreSQL e Redis estejam acessíveis; isso é aceitável para liveness mínimo, mas não deve ser vendido como readiness completo da stack.

- O BFF retorna a mensagem de erro controlada para falha de API, mas ainda inclui o texto da exceção; hoje a mensagem não vaza segredo, porém futuras alterações no client HTTP devem preservar essa contenção.

- As credenciais `jsdesign/jsdesign` aparecem em `.env.example` e no compose como padrão local; isso é aceitável para desenvolvimento, mas precisa permanecer fora de qualquer documentação de produção.

- O scaffold Laravel ainda carrega scripts e arquivos herdados do template que podem confundir a separação entre API e frontend se alguém usar comandos genéricos do Laravel sem ler o README da raiz.

- O Docker usa tags móveis `postgres:18` e `redis:8-alpine`; isso atende à exigência 18.x/Redis atual, mas não garante reprodutibilidade bit a bit em longo prazo.

- O smoke e2e cobre a comunicação BFF → Laravel API, mas não cobre falha deliberada da API nem resposta degradada 503; essa cobertura deve entrar quando a camada de observabilidade/erros for expandida.

- O CI valida backend, frontend e smoke em workflows separados, mas ainda não há branch protection configurado no GitHub; a story só entrega os workflows, não garante enforcement remoto.

- O diretório `packages/contracts` contém apenas README placeholder; isso evita pasta vazia, mas o contrato formal OpenAPI/JSON Schema ainda precisa nascer quando os endpoints deixarem de ser apenas healthcheck.
