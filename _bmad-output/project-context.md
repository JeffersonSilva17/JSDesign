---
project_name: 'JSDESIGN'
user_name: 'Sharom'
date: '2026-08-18'
sections_completed: ['technology_stack', 'language_rules', 'framework_rules', 'testing_rules', 'quality_rules', 'workflow_rules', 'anti_patterns']
status: 'complete'
rule_count: 90
optimized_for_llm: true
---

# Contexto do Projeto para Agentes de IA

_Este arquivo contém regras e padrões críticos que os agentes de IA devem seguir ao implementar código neste projeto. O foco são detalhes não óbvios que, de outro modo, poderiam passar despercebidos._

---

## Stack de Tecnologia e Versões

- Frontend/BFF: Next.js 16.3.0, React 19.2.0 e React DOM 19.2.0.
- Linguagem frontend: TypeScript 5.9.x em modo `strict`, com alvo ES2022.
- Runtime frontend: Node.js 24.x; instalar com `npm ci`.
- Backend/API: PHP 8.5.x e Laravel 13.x.
- Banco transacional: PostgreSQL 18.x.
- Cache e filas: Redis 8.x; cliente Laravel `predis/predis` 3.x.
- Testes: PHPUnit 12.5.x para Laravel e Playwright 1.60.0 com Chromium.
- Qualidade: ESLint 9.x com `eslint-config-next` 16.3.0 e Laravel Pint 1.x.
- Contratos: REST JSON versionado em `/api/v1`; contratos OpenAPI ficam em `packages/contracts`.
- `package.json` e `composer.json` definem faixas de compatibilidade; `package-lock.json` e `composer.lock` definem os patches efetivamente instalados.
- Não atualizar dependências, alterar faixas ou regenerar lockfiles fora do escopo explícito da story.
- Quando artefatos de planejamento divergirem do código, manifests, lockfiles, configurações e CI vigentes prevalecem; registrar a divergência na story.
- As tags atuais de PostgreSQL e Redis atendem ao ambiente local/CI, mas não constituem política aprovada de pin por digest para produção.
- Python não integra a aplicação, o build ou os testes do produto; não o adicionar como dependência da solução.

## Regras Críticas de Implementação

### Regras Específicas de Linguagem

- TypeScript deve permanecer em modo `strict`; não contornar erros com `any`, casts amplos ou desativação de regras.
- Tratar respostas HTTP, JSON e outros dados externos inicialmente como `unknown`; validar sua forma com type guards antes do uso.
- Usar `import type` para símbolos exclusivamente de tipo e o alias `@/*` para imports internos de `apps/web/src`.
- Modelar props e estruturas imutáveis com `Readonly` quando não houver mutação intencional.
- Manter tipos de contratos BFF alinhados ao JSON Laravel, incluindo estados discriminados e campos opcionais/proibidos explicitamente.
- Valores monetários atravessam contratos como inteiros em unidade mínima acompanhados da moeda; não usar ponto flutuante para regras financeiras.
- Controllers, middleware e casos de uso sem estado devem seguir o padrão atual de classes `final readonly` quando suas dependências permitirem.
- Preservar a convenção JSON `snake_case` da API e nomes PHP/TypeScript idiomáticos dentro de cada aplicação.

### Regras Específicas de Framework

- Usar o Next.js App Router em `apps/web/src/app`; páginas e layouts são Server Components por padrão.
- Adicionar `'use client'` somente no menor componente que realmente precise de estado, efeitos ou APIs do navegador.
- O navegador deve consumir páginas ou rotas same-origin do Next.js; não chamar diretamente a API Laravel interna.
- `API_INTERNAL_URL` é configuração exclusivamente server-side e nunca deve ser exposta ao bundle do cliente.
- O BFF pode adaptar payloads, compor dados de tela, validar respostas, controlar cache e traduzir falhas; não pode reproduzir regras comerciais.
- Validar runtime payloads recebidos do Laravel antes de renderizá-los ou devolvê-los ao navegador.
- Definir `Metadata` nas páginas públicas e manter textos visíveis centralizados na estrutura de conteúdo/i18n existente.
- Não introduzir biblioteca de estado global: usar Server Components e estado React local até que uma necessidade aprovada justifique outra solução.
- No Laravel, organizar cada módulo em `Domain`, `Application`, `Infrastructure` e `Interfaces/Http`.
- Controllers, Form Requests, Resources e middleware ficam em `Interfaces/Http`; controllers delegam a casos de uso.
- Leituras complexas ou públicas usam portas de query em `Application` e implementações em `Infrastructure`; não consultar tabelas diretamente em controllers, BFF ou outros módulos.
- Eloquent ou Query Builder ficam restritos a `Infrastructure`, repositories, queries e migrations.
- Rotas da API devem permanecer sob `/api/v1` e seguir nomes estáveis.
- Resources públicos são allowlists explícitas; adicionar um campo administrativo não pode fazê-lo aparecer automaticamente em respostas públicas.
- PostgreSQL é a fonte durável de verdade; Redis serve apenas para cache e filas.
- Jobs e integrações externas devem ser idempotentes e entrar por portas/adapters do Laravel.

### Regras de Testes

- Implementar cada comportamento pelo ciclo red-green-refactor: teste falhando primeiro, implementação mínima e refatoração com a suíte verde.
- Colocar testes PHP puros de domínio e casos de uso em `apps/api/tests/Unit`; substituir portas externas por fakes explícitos.
- Colocar testes HTTP, persistência, bindings e integração Laravel/PostgreSQL em `apps/api/tests/Feature`.
- Testes que acessam o banco devem usar `RefreshDatabase` e PostgreSQL real; não substituir por SQLite quando tipos, constraints ou semântica PostgreSQL fizerem parte do comportamento.
- Substituir adapters fail-closed por fakes apenas dentro do teste; produção deve continuar negando operações sem integração real aprovada.
- Testar caminhos positivos, validação, limites, conflitos, concorrência/versionamento e falhas de dependências conforme o risco da story.
- Para contratos públicos, verificar allowlist e ausência de campos administrativos ou sensíveis; usar comparações exatas quando a estabilidade do payload for requisito.
- Testes Playwright ficam em `apps/web/tests/e2e/*.spec.ts` e devem preferir roles, nomes acessíveis e comportamento observável a seletores de implementação.
- Fluxos públicos devem verificar teclado/foco, semântica, responsividade relevante e ausência de texto corrompido.
- Isolar UI com interceptação de rede quando apropriado, mas manter smoke tests críticos atravessando Next.js BFF e Laravel reais.
- Não inventar meta numérica de cobertura: o repositório ainda não configura threshold; cobrir integralmente os critérios de aceitação e regressões próximas.
- Antes de concluir uma story, executar `composer test` e `vendor/bin/pint --test` no backend; `npm run lint`, `npm run typecheck`, `npm run build` e os testes Playwright aplicáveis no frontend.

### Regras de Qualidade e Estilo

- O backend deve passar por Laravel Pint; o frontend deve obedecer ao ESLint com `core-web-vitals` e regras TypeScript do Next.js.
- Não introduzir Prettier ou outro formatador sem decisão explícita; o projeto não possui essa configuração.
- Em PHP, usar classes e arquivos em `PascalCase`, namespaces alinhados às pastas e uma responsabilidade principal por arquivo.
- Em React, componentes exportados usam `PascalCase`; features e rotas seguem a organização e os nomes já existentes.
- Manter imports externos antes dos internos, separar grupos com linha em branco e usar `@/*` para código sob `apps/web/src`.
- Colocar comportamento no módulo dono da capacidade; não criar diretórios genéricos `Helpers`, `Services` ou `Utils` para esconder responsabilidade de domínio.
- Reutilizar tipos, Resources, portas e objetos de valor existentes; não duplicar contratos entre controller, BFF e UI.
- Manter conteúdo público em português do Brasil na estrutura centralizada de i18n; preservar UTF-8 e impedir mojibake.
- Usar comentários para explicar decisões, invariantes ou riscos não evidentes; não narrar código óbvio.
- Usar PHPDoc quando ele acrescentar informação que o tipo nativo não expressa, especialmente shapes de arrays e coleções.
- Não adicionar dependências, abstrações ou configurações globais sem necessidade explícita da story.
- Preservar a estrutura modular e manter diffs restritos aos arquivos necessários; não misturar refatorações não relacionadas.

### Regras do Fluxo de Desenvolvimento

- Usar a story contextualizada como fonte autoritativa e executar tarefas/subtarefas na ordem documentada.
- Manter `sprint-status.yaml` sincronizado com o ciclo `backlog` → `ready-for-dev` → `in-progress` → `review` → `done`.
- O padrão observado de branch é `story_<épico>_<story>`; preservar esse formato enquanto não houver convenção substituta.
- Mensagens de commit devem ser curtas, em português e centradas no resultado da story; não impor formato semântico ainda não adotado pelo projeto.
- Não marcar tarefas ou critérios como concluídos sem testes existentes e executados com sucesso.
- Toda implementação de story deve passar por revisão adversarial conforme a customização BMad do projeto; achados críticos/altos precisam ser tratados ou explicitamente aceitos.
- Alterações de contrato REST devem atualizar testes HTTP e o OpenAPI aplicável em `packages/contracts`.
- Novas variáveis de ambiente devem ser documentadas no `.env.example` apropriado; nunca versionar segredos ou valores reais.
- Migrations novas devem ser incrementais; não reescrever migration já compartilhada para simular estado final.
- Não escolher hospedagem, autenticação, storage ou provedores externos enquanto essas decisões arquiteturais permanecerem adiadas.
- O deploy final ainda não está definido; não adicionar configuração específica de fornecedor sem decisão aprovada.

### Regras Críticas que Não Podem Ser Esquecidas

- Nunca mover regras de preço, disponibilidade, publicação, pedido, pagamento ou transição de estado para o Next.js/BFF.
- Nunca consultar tabelas Laravel diretamente a partir de controllers, BFF ou outros módulos; usar casos de uso e portas de query.
- Catálogo público expõe somente produtos publicados e campos da allowlist de `PublicCatalogProductResource`.
- Estado, notas e evidências de verificação de direitos são estritamente administrativos; personagens ou ativos protegidos não são categorias públicas.
- Manter `AdminIdentityResolver` e `FileReferenceValidator` fail-closed até existirem adapters reais aprovados; não usar headers confiados, tokens fixos, paths, URLs ou base64 como atalhos.
- Autenticação e autorização pertencem ao Laravel; o BFF não pode conceder acesso nem expor credenciais server-to-server ao navegador.
- Arquivos privados exigem autorização backend e entrega controlada ou URL temporária; nunca criar links públicos permanentes.
- Validar parâmetros, enums, tamanhos, paginação e filtros em toda fronteira HTTP; usar queries parametrizadas e limites máximos explícitos.
- Listagens devem evitar N+1, usar ordenação determinística e paginação limitada; índices devem acompanhar filtros recorrentes comprovados.
- Cache não substitui PostgreSQL e não pode tornar visível conteúdo retirado ou ainda não publicado.
- Operações que podem ser repetidas por retry, webhook ou concorrência devem ser idempotentes e preservar transações/versionamento otimista.
- Não liberar briefing, produção ou download por retorno do navegador; somente pagamento confirmado pelo backend pode autorizar essas transições.
- Minimizar PII em payloads, logs e erros; nunca registrar segredos, cupons em claro, evidências privadas ou dados pessoais desnecessários.
- Respostas de erro públicas usam códigos e mensagens sanitizadas; nunca expor SQL, stack traces, caminhos internos ou detalhes de providers.
- Toda chamada externa ou interna por rede deve ter timeout e comportamento de falha explícito.
- Preservar acessibilidade por teclado, foco, nomes acessíveis, HTML semântico e contraste nos fluxos públicos.
- Não inventar decisões arquiteturais adiadas nem implementar escopo pertencente a stories futuras.

---

## Diretrizes de Uso

**Para agentes de IA:**

- Ler este arquivo antes de implementar ou revisar código.
- Cumprir todas as regras aplicáveis; em caso de dúvida, preferir a opção mais restritiva e registrar a incerteza.
- Propor atualização deste arquivo quando uma mudança aprovada estabelecer um novo padrão durável.

**Para pessoas:**

- Manter o arquivo enxuto e focado em regras que agentes poderiam ignorar.
- Atualizar versões e padrões quando o código ou a arquitetura vigente mudar.
- Revisar periodicamente e remover orientações obsoletas ou que tenham se tornado autoevidentes.

Última atualização: 2026-08-18
