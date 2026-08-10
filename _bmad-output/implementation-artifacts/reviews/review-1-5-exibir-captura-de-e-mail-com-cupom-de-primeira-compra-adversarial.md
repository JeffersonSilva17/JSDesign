# Revisão adversarial — Story 1.5

- O modo `display` dizia entregar o código “uma única vez”, mas a mesma story exigia retry idempotente. Uma resposta perdida deixaria a visitante sem código; o contrato precisa devolver o mesmo código associado sem criar outro benefício.
- Um único `202 Accepted` para `display` e `email` escondia a diferença entre resultado síncrono e trabalho assíncrono. Os status precisam ser explícitos: `200` para exibição concluída e `202` para entrega aceita.
- A unicidade “campanha ativa/e-mail” estava ambígua e não definia uma constraint implementável. É necessário fixar `(campaign_id, email_fingerprint)`, digest único e identificador único de campanha.
- “CSPRNG/alta entropia” não dava critério verificável e permitia códigos fracos ou impraticáveis. A story precisa definir entropia mínima e formato humano digitável.
- A normalização de e-mail não estava definida. Diferentes camadas poderiam tratar maiúsculas, espaços, aliases `+` e pontos de forma incompatível, quebrando idempotência ou fundindo pessoas distintas.
- O rate limit era apenas “proporcional”, sem números ou janelas testáveis. Isso não permite implementação nem teste consistente e deixa o endpoint público vulnerável a abuso.
- O gatilho dizia “por exemplo, scroll de 22%” e “atraso curto”; essa formulação permite implementações e testes divergentes. O sinal, atraso e condições de cancelamento precisam ser determinísticos.
- A copy afirmava benefício de primeira compra sem explicitar que a emissão não comprova elegibilidade final. Um e-mail já comprador poderia interpretar o código emitido como garantia de desconto.
- Os defaults de feature flag e delivery não estavam separados por ambiente. Um deploy poderia ativar coleta real por engano ou deixar os testes sem forma estável de exercer o fluxo.
- O fingerprint do e-mail era mencionado apenas implicitamente. Hash simples de e-mail é enumerável; lookup/rate limit precisam de HMAC com chave server-side e logs sem PII.
- A cópia recuperável do código estava “protegida” sem dizer como. Como `display` e reenvio exigem recuperar o valor, digest isolado não basta; a cópia deve ser criptografada e o digest usado para validação.
- O modo `email` poderia ser confundido com envio concluído mesmo com mailer `log` e CI sem worker. A feature precisa ficar desabilitada ou responder indisponibilidade honesta quando canal/worker não estiver operacional.
- A story podia ser lida como autorização para newsletter porque usa linguagem de consentimento promocional. Deve permanecer explícito que a finalidade é somente emissão/entrega do cupom, sem marketing implícito.
- A ativação com dados reais dependia de um placeholder de privacidade que atualmente declara não coletar dados. O gate de produção precisa ser impeditivo até política, retenção, texto/versionamento e canal estarem aprovados.
- O arquivo terminava com nota de conclusão em inglês e revisão “aguardando”, contrariando o idioma do documento e o estado real após o gate. O registro deve refletir a revisão concluída.

