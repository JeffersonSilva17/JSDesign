# Revisão adversarial da implementação — Story 2.1

Conteúdo revisado: story, alterações não commitadas do módulo `Catalog`, migration PostgreSQL, testes, composição Laravel, documentação e contrato OpenAPI.

## Achados

- A porta `AdminIdentityResolver` em `Application` importava `Illuminate\Http\Request`, violando a direção das dependências; a assinatura foi tornada independente de HTTP.
- Uma edição de produto já publicado podia remover campos obrigatórios e manter o status público; o caso de uso agora reaplica todas as invariantes de publicação antes de persistir.
- A revalidação de uma edição publicada inicialmente não consultava novamente o estado da referência de arquivo; o caso de uso agora usa a porta de arquivos também nesse fluxo.
- Uma evidência já verificada podia ter status, observações, autoria ou timestamp reescritos silenciosamente; o caso de uso agora preserva o registro verificado e rejeita downgrade/mutação retroativa.
- A porta de arquivos retornava apenas booleano e não permitia provar separadamente referência ausente e rejeitada; o contrato agora usa estados `accepted`, `missing` e `rejected`, cobertos por testes.
- O objeto `Money` aceitava qualquer sequência de três letras como se fosse moeda ISO; a versão atual restringe a moeda-base suportada a `EUR` e documenta o deferimento de multimoeda.
- O agregado validava modalidade, mas aceitava disponibilidade, entrega, status e verificação de direitos inválidos quando chamado fora do HTTP; os enums passaram a ser aplicados no domínio.
- O schema PostgreSQL não possuía checks para disponibilidade, entrega e moeda suportada; constraints explícitas foram adicionadas.
- O schema permitia gravar direitos como `verified` sem evidência, autora ou timestamp; uma constraint impede esse estado incompleto.
- Produto inexistente era reportado como conflito `409`; a API agora retorna `404` somente depois de autenticação e autorização, mantendo `409` para unicidade/versão.
- A projeção pública carregava nomes de campos administrativos de direitos com valores nulos dentro da taxonomia; a Resource agora projeta uma allowlist mínima.
- O OpenAPI não descrevia integralmente envelopes de erro nem metadados administrativos de direitos na resposta; schemas e respostas foram completados.
- Mensagens e fixtures novas continham sequências UTF-8 corrompidas; os textos foram normalizados e os testes de acentuação preservados.
- O pré-requisito operacional de categorias existentes não estava documentado; o README do módulo agora registra o provisionamento e o deferimento do CRUD visual para a Story 8.2.

## Resultado

Todos os achados que afetavam critérios de aceitação, arquitetura, integridade ou contrato foram corrigidos nesta execução. Não restam achados críticos ou altos pendentes de aceite.
