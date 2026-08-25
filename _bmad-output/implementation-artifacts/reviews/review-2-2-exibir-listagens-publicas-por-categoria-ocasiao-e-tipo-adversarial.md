# Revisão Adversarial — Story 2.2

- A regra `page > last_page` conflita com a regra absoluta `current_page = 1` quando `total = 0`; uma requisição explícita de `page=5` teria dois metadados possíveis.
- O contrato não diz se filtros bem formados, mas inexistentes, são erro de validação ou conjunto vazio; API, BFF e UI poderiam implementar respostas incompatíveis.
- O “path relativo same-origin” de imagem ainda não identifica quem serve esse path no domínio público do Next.js; aceitar `/media/...` emitido pelo Laravel sem rota equivalente no Next produziria cards quebrados apesar da validação de segurança.
- `description` persistida aceita até 10.000 caracteres, mas a listagem promete “descrição curta” e até 48 itens; proibir truncamento no BFF sem definir uma projeção de resumo no Laravel permite payload excessivo e layout instável.
- `compatibility` aceita até 5.000 caracteres livres e não existe marcador estruturado para “Silhouette Studio”; o card não consegue decidir com segurança quando mostrar o badge sem uma regra explícita ou inferência textual frágil.
- O shape público continua impreciso para nulabilidade e enums de preço, entrega, disponibilidade, prazo, compatibilidade e taxonomia; dizer que o OpenAPI será exato não impede implementações divergentes antes dele existir.
- Facetas não têm ordenação determinística nem regra para rótulos/chaves repetidos, portanto a navegação pode mudar de ordem entre requests e testes podem passar por acidente.
- A configuração de throttle não define como obter identidade através do BFF/proxy confiável; usar header arbitrário permite spoofing, enquanto usar apenas o IP do Next pode bloquear todas as clientes no mesmo bucket.
- A UX inválida no Next permanece indefinida: a API responde `422`, mas filtros web são validados antes do upstream e a story não fixa se a página mostra erro, vazio, `404` ou indisponibilidade.
- O critério de responsividade declara quatro faixas, mas os testes requerem somente 320 px e desktop; regressões exatamente em 420, 760 e 1100 px poderiam escapar.
- “Performance” está reduzida a ausência de N+1; a story não liga a listagem ao NFR-2 de Core Web Vitals nem exige dimensões/reserva de espaço da imagem, permitindo CLS ou mídia bloqueante.
- O texto limita implementação a pt-BR sem registrar a tensão com o NFR-10 de três idiomas no lançamento; cópias e mapeamentos podem ficar impossíveis de internacionalizar ou o agente pode expandir indevidamente o escopo.
- O contrato de taxonomia no card não define campos, tipos permitidos, deduplicação ou ordem; uma Resource poderia devolver mais metadados do que a UI precisa ou gerar snapshots instáveis.
- A regra “ativar o card ou CTA” contradiz a tarefa de “um único link semântico”; envolver o card inteiro e adicionar outro CTA cria links aninhados/duplicados e nomes acessíveis ambíguos.

