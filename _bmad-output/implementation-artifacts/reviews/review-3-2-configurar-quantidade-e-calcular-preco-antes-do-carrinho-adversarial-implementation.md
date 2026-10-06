# Revisão adversarial — Story 3.2 (implementação)

Data: 2026-10-06  
Artefato: implementação de cotação pré-carrinho em Laravel/PostgreSQL e Next.js/BFF.

## Achados e tratamento

- O BFF lia todo o corpo HTTP antes de validar o limite de 4 KiB, deixando a rota vulnerável a consumo evitável de memória. Corrigido com leitura limitada por stream, cancelamento no excesso e rejeição de UTF-8 inválido; testes verificam esses casos.
- O transporte aceitava qualquer tamanho de corpo JSON vindo do serviço interno antes de parsear. Corrigido com teto de 16 KiB e teste de resposta upstream excedente.
- UI e BFF tinham validadores diferentes e o guard local da UI aceitava cotações incompletas. Corrigido com reutilização de `isQuote`/`isPublicError` e verificação de produto, modelo e quantidade.
- O guard da cotação não provava que subtotal, desconto e total correspondiam aos valores unitários. Corrigido com invariantes inteiras usando `BigInt` e teste para total incoerente.
- Erros públicos aceitavam qualquer string como correlation ID, inclusive conteúdo excessivo ou não canônico. Corrigido exigindo formato UUID e adicionando caso negativo.
- Uma faixa movida para outra regra incrementava a versão nova, mas não invalidava a versão anterior. Corrigido no gatilho PostgreSQL; teste de feature verifica ambas as versões.
- O campo de quantidade não impunha o teto absoluto antes da primeira resposta de cotação. Corrigido com limite visual e validação local de 10000.
- O BFF aceitava media types iniciados por `application/json`, inclusive valores não JSON como `application/jsonx`. Corrigido comparando o media type normalizado antes dos parâmetros.
- A regra `digital_ready = 1` estava coberta no domínio, mas não no endpoint integrado. Adicionado teste HTTP provando quantidade 1 aceita e 2 rejeitada.
- O mock Playwright fixava `model_key: null`, divergia do request da página com modelo selecionado e escondia a verificação de identidade da resposta. Corrigido para refletir o modelo e produto enviados no request; o cenário real de resposta fora de ordem passou.

## Resultado

Todos os achados desta revisão foram corrigidos e verificados. `composer test` passou com 162 testes e 810 assertions (1 benchmark ignorado), Pint passou, lint/typecheck/BFF/SEO/build passaram, Playwright passou com 3 cenários, audits npm/Composer não encontraram advisories e os scanners versionados não encontraram achados.
