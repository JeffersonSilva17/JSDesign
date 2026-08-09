# Revisão adversarial — Story 1.4 ready-for-dev

Data: 2026-08-08

Conteúdo revisado: `_bmad-output/implementation-artifacts/1-4-criar-a-home-publica-com-entrada-para-os-produtos-mais-procurados.md`

- A story ainda promete “cards específicos”, mas os exemplos recomendados continuam sendo modalidades/categorias amplas, como “lembrancinhas físicas personalizadas” e “convites digitais personalizados”; isso não atende bem à intenção anterior de exibir produtos específicos logo de cara.
- A posição “imediatamente após a hero” reduz ambiguidade, mas não impede uma hero alta demais; sem orçamento de altura, quantidade máxima de conteúdo ou teste de visibilidade real no viewport mobile, “Mais procurados” ainda pode ficar baixo na prática.
- A story mistura duas camadas de descoberta na home: `modalityCards` dentro da hero e `mostWanted` após a hero; sem orientação clara para remover, reduzir ou reaproveitar os cards atuais, a implementação pode duplicar mensagens e deixar a primeira dobra pesada.
- O CTA “Comprar agora” para Produto Digital Pronto continua arriscado mesmo com aviso próximo, porque direciona para rota placeholder e pode parecer ação de compra real; a story deveria decidir entre manter honestidade total com CTA não transacional ou implementar uma página/fluxo real em outra story.
- A exigência “Download imediato após pagamento confirmado” colide com a exigência de não simular compra/pagamento; a microcopy precisa ser mais precisa para explicar que esse é o comportamento futuro, não uma capacidade atual disponível.
- A quarta entrada opcional “kits/topos ou outra entrada” é permissiva demais e contradiz a busca por produtos específicos; sem escolha fechada, o dev pode criar um card genérico, desalinhado ou comercialmente fraco.
- A story cobre FR-3, mas os testes mínimos de SEO só falam genericamente em metadata coerente; faltam critérios verificáveis de title, description, conteúdo indexável honesto e ausência de `noindex` acidental na home.
- O critério de área visual acessível não foi convertido em teste; sem assert de `aria-hidden`, `alt`, `role` ou nome acessível, a implementação pode passar sem garantir acessibilidade dos elementos visuais.
- A orientação para usar “curadoria editorial inicial” aparece nos Dev Notes e tarefas, mas não exige texto visível explicando a natureza editorial; se não aparecer para a cliente, “Mais procurados” ainda pode soar como ranking real.
- Os destinos permitidos `/produtos`, `/categorias` e `/buscar` são todos placeholders; a story não exige que o texto do destino preserve o contexto do card clicado, então o clique pode parecer beco sem saída.
- A story não define ordem dos cards; para a prioridade do negócio, lembrancinhas físicas personalizadas provavelmente deveriam aparecer primeiro, mas isso está apenas implícito.
- A validação de “sem preço real” não cobre símbolos e padrões numéricos comuns, como `€`, `R$`, `USD`, `CHF`, `BRL`, `0,00`, `10x`, porcentagem de desconto ou palavras como “grátis”.
- A story não exige comprovar que `package.json` e lockfile não foram alterados, apesar de proibir dependências visuais novas; isso deixa brecha para adicionar biblioteca sem teste ou revisão específica.
- A exigência de manter Server Component depende de inspeção manual; não há critério claro para detectar introdução indevida de `use client` na home ou na nova seção.
- A story ainda usa o termo “produto digital personalizado” no enunciado original dos épicos/FRs em alguns contextos, mas a microcopy atual trabalha com “convite digital personalizado” e “Produto Digital Pronto”; falta uma regra explícita para evitar reintroduzir a expressão ambígua.
- A seção “Mais procurados” deve aparecer cedo, mas a story não exige prova visual, screenshot ou inspeção manual antes da revisão; só o teste automatizado pode não capturar densidade, hierarquia visual e sensação de primeira dobra.

## Resolução aplicada

Os achados claros foram incorporados à story em 2026-08-08:

- conjunto fechado e ordenado de três produtos editoriais baseado no mockup UX;
- hero compacta, sem duplicação dos atuais `modalityCards`;
- CTA não transacional `Ver arquivo` para Produto Digital Pronto enquanto o destino for placeholder;
- download descrito como comportamento futuro condicionado a pagamento confirmado;
- frase visível explicando que a seleção é editorial, não ranking medido;
- critérios objetivos para primeira viewport 320 x 800 px, screenshot, SEO indexável e acessibilidade das áreas visuais;
- cobertura ampliada contra preço/promoção simulados;
- preservação explícita de Server Component, `package.json` e lockfile;
- uso consistente de “convite digital personalizado” e “Produto Digital Pronto”.

Não há achado remanescente que exija decisão de produto ou arquitetura para liberar `ready-for-dev`.
