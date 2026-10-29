# Revisão adversarial — Story 3.3

## Escopo

- Artefato: `3-3-coletar-personalizacao-essencial-no-adicionar-ao-carrinho.md`
- Contexto comparado: Epic 3 Story 3.3 e Story 4.1, FR-11/FR-12, UX Experience/Design, architecture spine, Stories 3.1/3.2, código atual da página de produto e configurador de preço.
- Data: 2026-10-27
- Método: leitura integral da story e comparação de requisitos, arquitetura, limites de escopo e caminhos operacionais.

## Achados

1. **“Comprar agora” não tem pós-condição definida.** A story manda o mesmo CTA executar inclusão, mas só diz que a cliente pode seguir ao carrinho. Isso faz “Comprar agora” significar “Adicionar ao carrinho” e pode criar implementação incompatível quando checkout surgir. Definir se ambos os CTAs adicionam e vão ao carrinho ou se “Comprar agora” prossegue ao checkout quando disponível.
2. **O convite não declara quais campos são obrigatórios.** AC2 enumera tema, cores, nome, data, horário e local; AC3 apenas alude a “campo obrigatório” sem regra por tipo Padrão/Complexo. A API/UI não conseguem decidir quando bloquear inclusão nem como aplicar “Preencher depois”. Mapear campo obrigatório/opcional por modalidade/tipo com base na tabela comercial aprovada.
3. **“Dados essenciais do evento” e “nome/texto principal” não têm shape.** Não se esclarece se nome é uma pessoa, anfitrião, texto literal do convite ou vários nomes; endereço pode significar local livre ou endereço postal. Definir campos e semântica sem coletar endereço postal completo antes de necessário.
4. **Formato e semântica temporal estão ausentes.** Não define data passada inválida, data local do evento, formato de transporte, fuso para horário ou apresentação em outros idiomas. Erros de conversão podem deslocar horário do convite. Normalizar `event_date` como data sem fuso e horário como hora local sem conversão, com formato canônico documentado.
5. **Não há limites de tamanho e caracteres para dados livres.** Tema, cores, nome, endereço e observações podem gerar payload excessivo, armazenamento abusivo ou conteúdo perigoso em renderização. O contrato precisa definir máximos por campo, bytes máximos e tratamento de texto; a UI deve escapar conteúdo e nunca interpretá-lo como HTML.
6. **“Preencher depois” depende de uma capacidade que não existe.** A story exige permissão explícita do catálogo, mas nenhum campo/contrato público correspondente foi encontrado, e nenhum task implementa ou identifica quem configura a permissão. Ou define os produtos/modalidades que podem adiar agora, ou adiciona a capacidade ao catálogo com owner/admin e teste de publicação; a ausência não pode deixar decisões para o desenvolvedor.
7. **A condição de que dados alteram escopo/preço não está operacionalizada.** AC4 proíbe adiar dados que alteram preço/escopo, sem enumerá-los nem apontar uma regra Laravel. Não fica claro se a regra é estática por modalidade ou configurável por produto. Ligar o bloqueio ao schema aprovado do catálogo/caso de uso e validar no Laravel.
8. **Idempotência está descrita como resultado, sem contrato de replay.** “Repetir a mesma requisição” não define chave, escopo, prazo, resposta para o mesmo payload ou conflito de chave com payload diferente. Sem isso, retries de timeout podem duplicar itens ou retornar configuração alheia. Definir idempotência no endpoint/API com testes para replay e colisão.
9. **A fronteira com Story 4.1 é insuficiente.** A story precisa persistir/adicionar um item real, enquanto a story futura gerencia itens e carrinho salvo. Não define se 3.3 abre um carrinho mínimo convidado, reutiliza um existente, ou apenas cria um item, nem o que 4.1 pode assumir sobre shape/status do item. Declarar o agregado e o contrato produzido por 3.3 como pré-requisito de 4.1.
10. **Falta comportamento para duas abas e atualização concorrente.** Produto pode ficar indisponível ou configuração mudar entre cotação, preenchimento e inclusão; duas submissões paralelas podem criar itens diferentes. AC5 menciona configuração desatualizada sem definir conflito, reconfirmação ou associação ao item. Definir revalidação atômica no Laravel e resposta recuperável que preserve formulário e não use cotação antiga.
11. **Catálogo e modalidade do convite são insuficientes para validar o fluxo.** Não há seed/fixtures definidos para Convite Padrão e Complexo, nem critério de resposta que prove prazo/briefing diferente sem prometer prazo incorreto. Referenciar classificações públicas do catálogo e garantir que dados do pré-formulário sejam reutilizados no Briefing sem redigitação.
12. **O requisito de acesso a dados não tem modelo de autorização/retention operacional.** A mitigação deixa sessão/autenticação deferida e retenção em política externa; a story só registra achados de segurança. Implementadores ainda podem armazenar PII de criança num carrinho sem owner, prazo ou deleção. Os dois achados SEC-3.3-01/02 permanecem gates antes de `ready-for-dev`.
13. **A exceção de Produto Digital Pronto é descrita, mas não cobre a rota real de compra.** Story 3.1 ainda encaminha para o placeholder de carrinho e 3.2 não inclui item. A regra “compra segue a modalidade já definida” pode ser interpretada como uma compra disponível apesar do carrinho ainda não existir. Descrever explicitamente como o fluxo de inclusão desta story diferencia digital pronto, ou definir que sua compra fica fora desta story.
14. **Conteúdo e estado do formulário não especificam idioma atual.** A página de produto usa `defaultLocale`; a story exige pt-BR/en/es, mas não diz se vai adotar preferência manual, mudar as strings atuais ou apenas adicionar chaves. Definir a fonte única do locale e provar que erros/respostas não alternam silenciosamente o idioma.
15. **A estratégia de segurança para mutação está genérica.** “CSRF conforme estratégia aprovada”, “rate limit” e “sem cache” não apontam mecanismos/limites verificáveis nem escopo por sessão/IP. Quando a estratégia de sessão for decidida, fixar controles e contratos de erro, incluindo corpo inválido, 413, 419/CSRF, 422, 429 e timeout, sem detalhes internos.

## Ajustes objetivos aplicados

- Mantida a story em `draft`/`backlog`; a falta de decisões arquiteturais e de privacidade impede promovê-la a `ready-for-dev`.
- Tornada explícita a normalização de data e horário para evitar conversão de fuso inadvertida.
- Adicionada tarefa para definir requiredness e limites por campo, erros públicos e provas específicas de modalidade antes da implementação.
- Reforçados teste de conflitos de configuração, retries/replay, colisão de idempotência e inclusão concorrente.
- Tornado explícito que Produto Digital Pronto só pode usar inclusão se a modalidade tiver fluxo suportado; não deve receber formulário de personalização.

## Tratamento aplicado em 2026-10-13

- “Adicionar ao carrinho” e “Comprar agora” foram definidos como a mesma inclusão server-side seguida de navegação ao carrinho; checkout permanece fora desta story. Os rótulos seguem a modalidade: “Comprar já” para físicos e “Comprar agora” para convites.
- Convite Padrão e Convite Complexo agora têm a mesma matriz obrigatória (`theme`, `colors`, `event_name`, `event_date`, `event_time`, `event_location`); o Complexo acrescenta Briefing completo depois do pagamento. `event_name` é texto exibido no convite e `event_location` é local livre, sem endereço postal.
- A data usa `YYYY-MM-DD` sem fuso, a hora usa `HH:mm` local e datas anteriores à data operacional configurada no Laravel são rejeitadas. Os campos livres têm limites explícitos e o corpo máximo é 16 KiB.
- “Preencher depois” depende de `allows_deferred_briefing=true` publicado pelo catálogo; ausência equivale a falso e nenhum produto atual é habilitado por padrão. A regra não permite adiar campos que mudem escopo, modalidade ou preço.
- A escolha de Miniatura (`none`, `client_file`, `create_by_js_designs`) foi incluída no pré-formulário, sem upload ou preço local; cobrança, compartilhamento e arquivo continuam na Story 3.4.
- O item mínimo e a criação/reutilização do carrinho convidado foram descritos para a Story 4.1. Concorrência, cotação desatualizada, replay idempotente, escaping, locale manual e códigos de erro foram adicionados aos critérios e testes.
- Fixtures de Convite Padrão/Complexo, reutilização do pré-formulário no Briefing e a resposta `unsupported_mode` para `digital_ready` foram adicionadas às tarefas. Rate limit de 10 submissões por minuto por sessão e timeout upstream de 3 segundos tornaram os controles verificáveis.
- A especificação agora vincula carrinho/pré-formulário abandonado à retenção de 30 dias do NFR-7 e exige eliminação verificável.

## Veredito

Os quinze achados funcionais e de contrato foram tratados na story. AD-10 foi atualizado em 2026-10-14 com o mecanismo concreto de sessão/CSRF, o contrato OpenAPI foi publicado e os gates SAST/Semgrep, Gitleaks, npm audit e composer audit passaram em 2026-10-27. Veredito: story pronta para `ready-for-dev`.
