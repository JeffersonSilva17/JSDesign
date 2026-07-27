# Reconciliação de entrada — `implementation-roadmap.md`

## Escopo

Esta análise reconcilia:

- entrada: `_bmad-output/brainstorming/brainstorm-website-js-designs-2026-07-23/implementation-roadmap.md`;
- artefatos de destino: `prd.md` e `addendum.md`;
- precedência: decisões posteriores registradas em `.memlog.md` prevalecem sobre a entrada.

Nenhuma ausência foi tratada automaticamente como requisito: itens superados por decisão posterior foram classificados como resolvidos, não como lacunas.

## Veredito

O PRD e o addendum preservam a maior parte da intenção do roadmap: jornada completa no lançamento, modalidades distintas, descoberta e busca, compra como visitante, pagamento e faturação, pós-compra personalizado, operação sem planilhas, segurança, privacidade, administração, gates M1/M2 e riscos principais.

Persistem cinco pontos relevantes de reconciliação. Dois podem bloquear o início seguro de UX/arquitetura/épicos — a ausência de um gate F0 explícito e a indefinição da taxonomia comercial de convites. Os demais devem ser resolvidos ou encaminhados ao documento downstream adequado.

## Cobertura confirmada

### Produto e experiência

- A jornada completa, e não apenas a vitrine, está refletida nas jornadas UJ-1 a UJ-4, nos FRs e nos gates M1/M2.
- A separação entre produto físico personalizado, convite personalizado e Produto Digital Pronto está explícita.
- Busca por tema, personagem, ocasião e grafias alternativas, com exatos antes de semelhantes e Projeto Exclusivo no estado sem resultado, está coberta em FR-5 e FR-6.
- Divulgação progressiva, direção premium, prova de acabamento, apoio humano e mensagens de confiança sem promessa absoluta estão cobertos.
- CTAs aprovados — “Comprar já” para lembrancinhas e “Finalizar e comprar” no Checkout — estão preservados.

### Comércio e pós-compra

- Checkout em página única, compra sem cadastro, conta pós-compra, transferência por 48 horas, NIF, Fatura e frete europeu estão cobertos.
- Briefing adaptado, reaproveitamento de dados, salvamento automático, Prévias numeradas, três Rodadas de Alteração, Aprovação Final e bloqueio da versão aprovada estão cobertos.
- Linha do tempo, pausas, Próxima Ação, fotografia privada, rastreamento, entrega digital e notificações estão cobertos.
- Administração, Ficha de Produção, fila de exceções, papéis, auditoria e operação sem planilha paralela estão cobertos.

### Segurança, privacidade e lançamento

- RGPD por design, dados privados, consentimento separado para marketing, isolamento de arquivos, idempotência, MFA administrativo, recuperação e testes estão distribuídos entre PRD e addendum.
- Dependências de pagamento, transferência, faturação, frete, e-mail, WhatsApp, Chat, armazenamento e políticas estão identificadas.
- Riscos centrais do roadmap foram preservados ou ampliados no PRD.

## Decisões mais recentes que resolvem conflitos com a entrada

Os seguintes desvios são intencionais e não constituem lacunas:

1. **Início da Produção física:** o roadmap antigo ligava o contador ao Briefing completo; a decisão posterior determina sete dias corridos somente após a Aprovação Final. O PRD está correto em UJ-1, UJ-4 e FR-29.
2. **Prazo para o Briefing:** o roadmap usa três dias corridos em um trecho; a decisão posterior confirma três dias úteis. O PRD está correto.
3. **Prazos de convites:** as faixas antigas de até 72 horas e três a cinco dias foram superadas por até 24 horas para Convite Padrão e até 48 horas para Convite Complexo. O PRD e o addendum registram o override.
4. **Suporte automático:** o roadmap posicionava o Chat automático após o núcleo; a proprietária o tornou obrigatório no MVP. UJ-5 e FR-37 a FR-40 refletem a decisão atual.
5. **SEO:** o roadmap posicionava SEO depois do núcleo; a descoberta posterior confirmou Google como entrada principal. FR-3 e o escopo do MVP estão corretos.
6. **Idiomas:** o roadmap adiava espanhol e não exigia inglês; a decisão posterior torna português do Brasil, inglês e espanhol obrigatórios no lançamento. FR-4 e NFR-10 estão corretos.

## Lacunas e conflitos remanescentes

### 1. Gate F0 não está formalizado nos artefatos de destino

**Severidade:** alta — potencial bloqueador de UX, arquitetura e épicos.

O roadmap exige um gate anterior à construção do núcleo para aprovar modalidades, regras comerciais, taxonomia, estados, protótipos, integrações críticas, inventário do catálogo, mapa de dados, retenção, ameaças e responsáveis. O PRD contém dependências e 16 perguntas abertas, mas apresenta apenas M1 e M2.

Sem um F0 explícito, decisões que o próprio roadmap considera bloqueadoras podem ser interpretadas como simples pendências não bloqueantes. Isso é especialmente relevante para países/moeda, fiscalidade, pagamentos, frete, retenção, catálogo final, política de alterações, pedidos mistos e capacidade operacional.

**Recomendação:** criar no PRD um Gate F0 enxuto, orientado a resultados, ou classificar individualmente as perguntas abertas entre bloqueadoras de fase e deferíveis. Detalhes técnicos e evidências podem permanecer no addendum ou em arquitetura.

### 2. Famílias e formatos de convite não foram reconciliados explicitamente

**Severidade:** alta — afeta catálogo, UX, precificação, busca, migração e aceite.

O roadmap pede preservar as famílias Essenciais, Interativos, Infinito e Cinemágicos e os oito formatos existentes. As decisões posteriores redefiniram apenas os prazos em dois grupos operacionais — Convite Padrão e Convite Complexo — mas não registraram que as famílias e os oito formatos foram descartados.

O PRD usa Padrão/Complexo e deixa “classificação final dos Tipos de Convite” em aberto, enquanto o addendum registra os prazos antigos como superados. Falta declarar se:

- Padrão/Complexo são modalidades operacionais acima das famílias existentes;
- as quatro famílias e os oito formatos continuam como taxonomia comercial;
- ou a taxonomia antiga foi substituída integralmente.

**Recomendação:** manter a questão como decisão comercial bloqueadora e registrar a relação entre classificação comercial, complexidade, prazo, demonstrações, recursos e preço antes de fechar catálogo e migração.

### 3. Plano e aceite da migração dos dois catálogos perderam profundidade

**Severidade:** média-alta — risco operacional de lançamento.

O PRD cita catálogo final, fotografias reais e migração no M2; o addendum inclui uma ordem geral. Porém, não preserva de forma suficiente:

- inventário separado das duas fontes;
- identificador estável e status de publicação por produto;
- carga repetível em homologação com relatório de erros;
- revisão da totalidade pela JS Designs;
- estratégia de delta e congelamento antes da publicação;
- conciliação fonte × site publicado;
- reversão da carga;
- critérios de unicidade, completude, mídia correta e não publicação acidental de fotos privadas ou sem consentimento.

**Recomendação:** adicionar ao addendum uma seção “Migração do catálogo” ou criar um plano de migração downstream. No PRD, basta manter o resultado verificável no Gate M2: catálogo final completo, único, conciliado, pesquisável e livre de publicação indevida.

### 4. Reuso e cobrança única da Miniatura em itens compatíveis não estão resolvidos

**Severidade:** média — afeta total do Carrinho e testes.

O roadmap exige que uma mesma Miniatura seja reutilizável em vários produtos compatíveis do mesmo Pedido e que sua criação seja cobrada uma única vez. O PRD confirma €5 para criação de Miniatura em convites, mas não define:

- quais itens são compatíveis;
- se a mesma Miniatura pode ser compartilhada entre convites ou entre convite e produto físico;
- se a cobrança única vale apenas para convites;
- como o Carrinho explica o reuso.

O índice de pressupostos reconhece que aplicar €5 a produtos físicos ainda não foi confirmado, mas não captura toda a regra de compatibilidade e não duplicação.

**Recomendação:** acrescentar essa decisão às perguntas comerciais abertas. Após confirmação, refletir o resultado em FR-11/FR-12/FR-14 e nos testes de cálculo, sem generalizar o preço de €5 para físicos.

### 5. Preferência global de canal pós-compra está parcialmente especificada

**Severidade:** média.

O roadmap pede preferência entre e-mail e WhatsApp para comunicações pós-compra e produtos digitais, conforme aplicável. O PRD:

- define e-mail ou WhatsApp para entrega do convite;
- exige canais consentidos para notificações;
- exige escalonamento de suporte por Chat ou WhatsApp.

Não define, entretanto, onde a cliente escolhe ou altera a preferência geral, quais comunicações são obrigatoriamente mantidas na Área da Cliente/e-mail, nem como indisponibilidade de WhatsApp degrada sem bloquear a jornada.

**Recomendação:** decidir o escopo da preferência por tipo de comunicação — operacional, entrega de convite, lembrete e marketing — e manter a Área da Cliente como registro principal. Mecanismos de integração e fallback pertencem ao addendum/arquitetura.

## Detalhes da entrada corretamente deslocados para documentos downstream

Os seguintes pontos não precisam ser repetidos no corpo do PRD, desde que sejam preservados em arquitetura, plano de testes, migração ou addendum:

- tokenização/campos hospedados, autenticação de eventos, ordem de webhooks e conciliação;
- WAF/antibot, links assinados, antimalware, gestão de segredos, HSTS e CSP;
- algoritmo de migração, formato da carga e procedimento de reversão;
- matriz de testes por integração: falha, indisponibilidade, timeout, duplicação, evento fora de ordem, reprocessamento e recuperação manual;
- camadas de testes unitários, integração, E2E, segurança, privacidade e recuperação;
- implementação interna da máquina de estados e dos relógios.

O PRD deve continuar expressando os resultados verificáveis correspondentes: não duplicação, privacidade, isolamento, recuperação, operação segura sob falha e jornada completa.

## Síntese para triagem

| Item | Destino sugerido | Ação |
|---|---|---|
| Gate F0 | PRD | Resolver antes de considerar o PRD seguro para handoff |
| Taxonomia de convites | PRD + addendum | Decisão comercial bloqueadora |
| Migração dos catálogos | Addendum ou plano próprio; resultado no Gate M2 | Preservar processo e critérios de aceite |
| Miniatura compartilhada | Perguntas abertas + FRs após decisão | Confirmar compatibilidade e cobrança |
| Preferência de canal | PRD; mecanismo no addendum | Definir regra de produto e fallback |

