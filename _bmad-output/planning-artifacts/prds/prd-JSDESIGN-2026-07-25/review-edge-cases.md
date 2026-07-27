[
  {
    "location": "prd.md:148-149",
    "trigger_condition": "E-mail de compra como visitante já pertence a outra conta",
    "guard_snippet": "Definir: associar pedido somente após verificação segura de identidade",
    "potential_consequence": "Pedido vinculado à conta errada ou acesso indevido"
  },
  {
    "location": "prd.md:148-149",
    "trigger_condition": "Cliente informa e-mail incorreto e perde acesso ao pedido",
    "guard_snippet": "Definir: fluxo seguro de correção de e-mail pós-compra",
    "potential_consequence": "Briefing e pedido tornam-se inacessíveis à compradora"
  },
  {
    "location": "prd.md:243-260",
    "trigger_condition": "Tradução obrigatória está ausente ao publicar uma página",
    "guard_snippet": "Definir: bloquear publicação ou aplicar fallback explicitamente sinalizado",
    "potential_consequence": "Jornada mistura idiomas ou omite informação contratual"
  },
  {
    "location": "prd.md:253-260",
    "trigger_condition": "Idioma muda durante Checkout com formulário parcialmente preenchido",
    "guard_snippet": "Definir: preservar campos, consentimentos e validações na troca",
    "potential_consequence": "Dados somem ou consentimento muda sem confirmação"
  },
  {
    "location": "prd.md:253-260",
    "trigger_condition": "Idioma da conta diverge do idioma usado na compra",
    "guard_snippet": "Definir: registrar idioma transacional por pedido e evento",
    "potential_consequence": "Notificações críticas chegam em idioma inesperado"
  },
  {
    "location": "prd.md:266-283",
    "trigger_condition": "Busca recebe termo vazio, enorme ou composto só por símbolos",
    "guard_snippet": "Definir: normalizar, limitar e tratar consulta sem termos pesquisáveis",
    "potential_consequence": "Erro, abuso de recursos ou página sem orientação"
  },
  {
    "location": "prd.md:266-283",
    "trigger_condition": "Mesmo tema possui acentos e grafias diferentes entre idiomas",
    "guard_snippet": "Definir: aliases localizados com normalização de acentos e caixa",
    "potential_consequence": "Resultados exatos desaparecem para grafias equivalentes"
  },
  {
    "location": "prd.md:266-283",
    "trigger_condition": "Produto encontrado é retirado por propriedade intelectual",
    "guard_snippet": "Definir: despublicar busca, URLs e recomendações com destino alternativo",
    "potential_consequence": "Página inválida continua vendendo ativo não autorizado"
  },
  {
    "location": "prd.md:285-312",
    "trigger_condition": "Produto fica indisponível após visualização e antes do pagamento",
    "guard_snippet": "Definir: revalidar disponibilidade imediatamente antes da cobrança",
    "potential_consequence": "Venda aceita sem capacidade ou item disponível"
  },
  {
    "location": "prd.md:285-303",
    "trigger_condition": "Quantidade solicitada supera cinquenta unidades ou capacidade atual",
    "guard_snippet": "Definir: limite, cotação humana ou bloqueio antes do Carrinho",
    "potential_consequence": "Preço ou prazo é prometido sem regra operacional"
  },
  {
    "location": "prd.md:295-303",
    "trigger_condition": "Recurso escolhido é incompatível com o Tipo de Convite",
    "guard_snippet": "Definir: matriz de recursos válida por formato e complexidade",
    "potential_consequence": "Pedido pago não pode ser produzido como configurado"
  },
  {
    "location": "prd.md:325-351",
    "trigger_condition": "Mesma Miniatura atende vários itens compatíveis do pedido",
    "guard_snippet": "Definir: cobrar uma vez e vincular aos itens selecionados",
    "potential_consequence": "Cliente paga múltiplas vezes pelo mesmo serviço"
  },
  {
    "location": "prd.md:334-351",
    "trigger_condition": "Pré-formulário contém dados conflitantes com o Briefing posterior",
    "guard_snippet": "Definir: solicitar confirmação e manter histórico da substituição",
    "potential_consequence": "Arte usa dados antigos sem decisão explícita"
  },
  {
    "location": "prd.md:344-369",
    "trigger_condition": "Complemento torna-se indisponível sem afetar o produto principal",
    "guard_snippet": "Definir: remover apenas complemento e recalcular benefício com confirmação",
    "potential_consequence": "Carrinho inteiro bloqueia ou total fica incorreto"
  },
  {
    "location": "prd.md:353-369",
    "trigger_condition": "Cupom expira entre cálculo do Carrinho e pagamento",
    "guard_snippet": "Definir: revalidar benefício e exigir aceite do novo total",
    "potential_consequence": "Cobrança diverge do total previamente apresentado"
  },
  {
    "location": "prd.md:353-369",
    "trigger_condition": "Visitante usa e-mails diferentes para repetir primeira compra",
    "guard_snippet": "Definir: regra antifraude proporcional sem bloquear cliente legítima",
    "potential_consequence": "Benefício de primeira compra é reutilizado indefinidamente"
  },
  {
    "location": "prd.md:373-427",
    "trigger_condition": "Pedido combina itens físicos, convites e arquivos prontos",
    "guard_snippet": "Definir: totais, frete, prazos e confirmações separados por modalidade",
    "potential_consequence": "Entrega ou próxima ação fica ambígua no pedido misto"
  },
  {
    "location": "prd.md:373-427",
    "trigger_condition": "Pedido contém itens físicos com endereços de entrega diferentes",
    "guard_snippet": "Definir: proibir múltiplos endereços ou dividir remessas explicitamente",
    "potential_consequence": "Itens são enviados ao destino incorreto"
  },
  {
    "location": "prd.md:373-427",
    "trigger_condition": "Sessão do Checkout expira antes da confirmação do pagamento",
    "guard_snippet": "Definir: preservar rascunho e revalidar preço antes de retomar",
    "potential_consequence": "Cliente perde dados ou paga configuração obsoleta"
  },
  {
    "location": "prd.md:383-409",
    "trigger_condition": "Cartão exige autenticação e cliente abandona o desafio",
    "guard_snippet": "Definir: estado pagamento incompleto com retomada ou expiração",
    "potential_consequence": "Pedido fica órfão ou aparenta pagamento confirmado"
  },
  {
    "location": "prd.md:383-409",
    "trigger_condition": "Provedor retorna pagamento pendente por tempo indeterminado",
    "guard_snippet": "Definir: timeout, reconciliação e estado visível de análise",
    "potential_consequence": "Pedido nunca avança nem pode ser recuperado"
  },
  {
    "location": "prd.md:392-409",
    "trigger_condition": "Pagamento é confirmado após cancelamento automático da transferência",
    "guard_snippet": "Definir: reconciliar confirmação tardia sem reativação silenciosa",
    "potential_consequence": "Dinheiro recebido para pedido cancelado ou sem capacidade"
  },
  {
    "location": "prd.md:392-409",
    "trigger_condition": "Confirmação financeira chega simultaneamente à rotina de expiração",
    "guard_snippet": "Definir: transição atômica entre reservado, pago e cancelado",
    "potential_consequence": "Pedido alterna estados ou libera fluxo duas vezes"
  },
  {
    "location": "prd.md:392-427",
    "trigger_condition": "Pagamento confirmado falha ao gerar Fatura ou liberar Briefing",
    "guard_snippet": "Definir: compensação, reprocessamento e fila operacional acionável",
    "potential_consequence": "Cliente paga mas não recebe próxima etapa"
  },
  {
    "location": "prd.md:392-427",
    "trigger_condition": "Reembolso parcial afeta somente um item do pedido",
    "guard_snippet": "Definir: estados e valores financeiros por item e pedido",
    "potential_consequence": "Fatura, benefício e entrega ficam inconsistentes"
  },
  {
    "location": "prd.md:392-427",
    "trigger_condition": "Chargeback ocorre após entrega digital ou início da Produção",
    "guard_snippet": "Definir: estado, evidências, suspensão e decisão operacional",
    "potential_consequence": "Acesso permanece ativo enquanto pagamento é contestado"
  },
  {
    "location": "prd.md:411-427",
    "trigger_condition": "Cotação de frete muda após revisão e antes da cobrança",
    "guard_snippet": "Definir: validade da cotação e reconfirmação do total",
    "potential_consequence": "Margem absorve diferença ou cliente paga valor inesperado"
  },
  {
    "location": "prd.md:441-469",
    "trigger_condition": "Pedido possui vários itens com Briefings em completudes diferentes",
    "guard_snippet": "Definir: prontidão e relógio por item, arte ou pedido",
    "potential_consequence": "Todos os itens iniciam cedo ou ficam bloqueados"
  },
  {
    "location": "prd.md:441-469",
    "trigger_condition": "Cliente edita resposta depois de marcar Briefing como completo",
    "guard_snippet": "Definir: bloquear, versionar ou reabrir com impacto no prazo",
    "potential_consequence": "Arte e Briefing deixam de representar os mesmos dados"
  },
  {
    "location": "prd.md:441-469",
    "trigger_condition": "Upload obrigatório falha na análise antimalware ou excede limite",
    "guard_snippet": "Definir: erro recuperável sem concluir o Briefing",
    "potential_consequence": "Prazo inicia sem arquivo utilizável ou cliente trava"
  },
  {
    "location": "prd.md:441-469",
    "trigger_condition": "Cliente nunca conclui Briefing de pedido já pago",
    "guard_snippet": "Definir: escalonamento, encerramento e política após inatividade prolongada",
    "potential_consequence": "Pedido pago permanece pausado indefinidamente"
  },
  {
    "location": "prd.md:451-469",
    "trigger_condition": "Prazo vence em fim de semana, feriado ou mudança de fuso",
    "guard_snippet": "Definir: calendário, fuso e arredondamento para horas úteis",
    "potential_consequence": "Promessas de 24 ou 48 horas divergem entre interfaces"
  },
  {
    "location": "prd.md:461-507",
    "trigger_condition": "Cliente solicita alteração enquanto Sharom publica nova Prévia",
    "guard_snippet": "Definir: controle de concorrência e versão-base da solicitação",
    "potential_consequence": "Comentário é aplicado à versão errada"
  },
  {
    "location": "prd.md:461-507",
    "trigger_condition": "Cliente aprova enquanto outra alteração está sendo processada",
    "guard_snippet": "Definir: aprovação atômica somente da versão corrente estável",
    "potential_consequence": "Produção começa com versão diferente da intenção"
  },
  {
    "location": "prd.md:461-507",
    "trigger_condition": "Uma solicitação contém múltiplas mudanças em mensagens separadas",
    "guard_snippet": "Definir: janela e confirmação que consolidam uma Rodada",
    "potential_consequence": "Contador gratuito é consumido de forma inconsistente"
  },
  {
    "location": "prd.md:461-507",
    "trigger_condition": "Alteração gratuita muda complexidade e prazo do convite",
    "guard_snippet": "Definir: recalcular prazo e obter aceite antes de continuar",
    "potential_consequence": "Prazo prometido deixa de ser executável"
  },
  {
    "location": "prd.md:481-507",
    "trigger_condition": "Cliente tenta revogar aprovação antes do início físico",
    "guard_snippet": "Definir: janela de revogação ou exceção comercial registrada",
    "potential_consequence": "Operação produz arte que cliente tentou corrigir"
  },
  {
    "location": "prd.md:481-507",
    "trigger_condition": "Pedido possui várias Artes e somente algumas são aprovadas",
    "guard_snippet": "Definir: aprovação e liberação de Produção por Arte",
    "potential_consequence": "Itens não aprovados entram em Produção"
  },
  {
    "location": "prd.md:500-507",
    "trigger_condition": "Pausa ocorre depois da Aprovação durante os sete dias",
    "guard_snippet": "Definir: causas válidas, relógio e nova data prometida",
    "potential_consequence": "Prazo restante é recalculado de forma arbitrária"
  },
  {
    "location": "prd.md:511-536",
    "trigger_condition": "Integração envia transição de estado atrasada ou fora de ordem",
    "guard_snippet": "Definir: matriz de transições válidas e rejeição auditada",
    "potential_consequence": "Pedido retrocede ou pula etapa obrigatória"
  },
  {
    "location": "prd.md:511-536",
    "trigger_condition": "Pedido termina por entrega, reembolso, devolução ou chargeback",
    "guard_snippet": "Definir: estados terminais distintos e transições permitidas",
    "potential_consequence": "Concluído oculta obrigação financeira ou logística pendente"
  },
  {
    "location": "prd.md:529-545",
    "trigger_condition": "Fotografia revela item incorreto ou defeito antes da postagem",
    "guard_snippet": "Definir: bloquear envio, abrir retrabalho e atualizar prazo",
    "potential_consequence": "Produto defeituoso é enviado apesar da evidência"
  },
  {
    "location": "prd.md:538-545",
    "trigger_condition": "Transportadora perde, devolve, atrasa ou avaria a encomenda",
    "guard_snippet": "Definir: estados, comunicação e resolução por exceção logística",
    "potential_consequence": "Cliente vê Enviado sem caminho de resolução"
  },
  {
    "location": "prd.md:547-573",
    "trigger_condition": "Canal de entrega do convite está inválido ou sem consentimento",
    "guard_snippet": "Definir: validar canal e oferecer alternativa segura",
    "potential_consequence": "Convite aprovado não chega à cliente"
  },
  {
    "location": "prd.md:556-573",
    "trigger_condition": "Arquivo digital está corrompido, ausente ou incompatível",
    "guard_snippet": "Definir: validar artefato antes do envio e permitir substituição",
    "potential_consequence": "Entrega automática conclui com arquivo inutilizável"
  },
  {
    "location": "prd.md:566-573",
    "trigger_condition": "Notificação chega duplicada, atrasada ou fora da ordem",
    "guard_snippet": "Definir: idempotência e sequência por evento e canal",
    "potential_consequence": "Cliente executa ação antiga ou recebe mensagens conflitantes"
  },
  {
    "location": "prd.md:566-573",
    "trigger_condition": "E-mail e WhatsApp falham para notificação obrigatória",
    "guard_snippet": "Definir: retentativas, alerta operacional e fallback na Área",
    "potential_consequence": "Cliente não descobre prazo ou ação pendente"
  },
  {
    "location": "prd.md:566-573",
    "trigger_condition": "Cliente revoga WhatsApp durante um pedido em andamento",
    "guard_snippet": "Definir: trocar canal sem perder notificações obrigatórias",
    "potential_consequence": "Comunicação continua sem consentimento ou deixa de ocorrer"
  },
  {
    "location": "prd.md:577-612",
    "trigger_condition": "Cliente não autenticada pergunta dados específicos do pedido",
    "guard_snippet": "Definir: autenticar e autorizar antes de consultar dados",
    "potential_consequence": "Chat revela status ou arquivos de outra cliente"
  },
  {
    "location": "prd.md:586-612",
    "trigger_condition": "Resposta cadastrada está expirada mas permanece ativa",
    "guard_snippet": "Definir: validade, revisão periódica e expiração automática",
    "potential_consequence": "Chat comunica preço, prazo ou política desatualizada"
  },
  {
    "location": "prd.md:595-612",
    "trigger_condition": "Cliente alterna simultaneamente entre Chat e WhatsApp",
    "guard_snippet": "Definir: identidade da conversa e prevenção de atendimento duplicado",
    "potential_consequence": "Respostas conflitantes ou trabalho humano duplicado"
  },
  {
    "location": "prd.md:595-612",
    "trigger_condition": "Cliente fecha o site após escalonamento para atendimento humano",
    "guard_snippet": "Definir: persistir conversa e canal de retorno consentido",
    "potential_consequence": "Mensagem é perdida ou fica sem resposta"
  },
  {
    "location": "prd.md:577-612",
    "trigger_condition": "Robô recebe spam, abuso ou conteúdo malicioso repetido",
    "guard_snippet": "Definir: limites, bloqueio e preservação segura de evidências",
    "potential_consequence": "Suporte fica indisponível ou expõe operação"
  },
  {
    "location": "prd.md:616-687",
    "trigger_condition": "Duas administradoras atualizam o mesmo pedido simultaneamente",
    "guard_snippet": "Definir: versão concorrente, bloqueio ou detecção de conflito",
    "potential_consequence": "Próxima Ação ou estado é sobrescrito"
  },
  {
    "location": "prd.md:616-687",
    "trigger_condition": "Conta administrativa perde papel durante sessão ativa",
    "guard_snippet": "Definir: revogar sessão e revalidar autorização por ação",
    "potential_consequence": "Acesso removido continua executando operações sensíveis"
  },
  {
    "location": "prd.md:652-678",
    "trigger_condition": "Capacidade produtiva é excedida antes da venda ser bloqueada",
    "guard_snippet": "Definir: capacidade por período e regra de indisponibilidade",
    "potential_consequence": "Pedidos aceitos superam os prazos prometidos"
  },
  {
    "location": "prd.md:661-678",
    "trigger_condition": "Preço muda enquanto existe Carrinho persistido em outro idioma",
    "guard_snippet": "Definir: snapshot, moeda e reconfirmação localizada do preço",
    "potential_consequence": "Cliente paga total diferente do contexto exibido"
  },
  {
    "location": "prd.md:679-687",
    "trigger_condition": "Exclusão RGPD conflita com Fatura ou disputa financeira ativa",
    "guard_snippet": "Definir: retenção legal seletiva e anonimização do restante",
    "potential_consequence": "Dados são apagados ilegalmente ou retidos em excesso"
  },
  {
    "location": "prd.md:689-707",
    "trigger_condition": "Consentimento de analytics é negado por parte das visitantes",
    "guard_snippet": "Definir: métricas agregadas válidas e documentar cobertura incompleta",
    "potential_consequence": "Baseline compara idiomas e jornadas com viés oculto"
  },
  {
    "location": "prd.md:754-800",
    "trigger_condition": "Serviço antimalware fica indisponível durante envio do Briefing",
    "guard_snippet": "Definir: quarentena segura, retentativa e aviso sem liberar arquivo",
    "potential_consequence": "Arquivo perigoso avança ou Briefing trava indefinidamente"
  },
  {
    "location": "prd.md:754-800",
    "trigger_condition": "Link privado expira durante ação ativa da cliente",
    "guard_snippet": "Definir: renovação autenticada sem tornar o arquivo público",
    "potential_consequence": "Cliente perde acesso ou recorre a canal inseguro"
  },
  {
    "location": "prd.md:754-800",
    "trigger_condition": "Restauração recupera Pedido mas perde arquivo ou versão aprovada",
    "guard_snippet": "Definir: teste de consistência referencial após restauração",
    "potential_consequence": "Produção usa dados incompletos após recuperação"
  },
  {
    "location": "prd.md:754-800",
    "trigger_condition": "Fotografia infantil perde consentimento de conservação para recompra",
    "guard_snippet": "Definir: eliminar cópias ativas e tratar backups conforme política",
    "potential_consequence": "Imagem permanece retida após revogação"
  },
  {
    "location": "prd.md:873-887",
    "trigger_condition": "Pentest encontra vulnerabilidade crítica ou alta antes do lançamento",
    "guard_snippet": "Definir: bloquear lançamento salvo tratamento formalmente aceito",
    "potential_consequence": "Loja entra em produção com risco conhecido"
  },
  {
    "location": "addendum.md:106-129",
    "trigger_condition": "Webhook possui assinatura inválida, repetida ou chave rotacionada",
    "guard_snippet": "Definir: validar assinatura, tolerância temporal e rotação",
    "potential_consequence": "Evento financeiro falso ou legítimo é processado incorretamente"
  },
  {
    "location": "addendum.md:120-129",
    "trigger_condition": "Fatura já emitida precisa correção após mudança autorizada",
    "guard_snippet": "Definir: nota de crédito, reemissão e trilha fiscal",
    "potential_consequence": "Documento fiscal diverge do pedido corrigido"
  },
  {
    "location": "addendum.md:126-137",
    "trigger_condition": "Etiqueta é criada mas transportadora rejeita a coleta",
    "guard_snippet": "Definir: exceção logística sem marcar pedido como Enviado",
    "potential_consequence": "Cliente recebe rastreamento de remessa inexistente"
  },
  {
    "location": "addendum.md:139-154",
    "trigger_condition": "Arquivo passa antimalware mas contém metadados pessoais desnecessários",
    "guard_snippet": "Definir: remover metadados aplicáveis antes de uso ou entrega",
    "potential_consequence": "Dados pessoais vazam em Prévia ou arquivo final"
  },
  {
    "location": "addendum.md:156-178",
    "trigger_condition": "Fallback de tradução não existe para resposta crítica do suporte",
    "guard_snippet": "Definir: bloquear resposta automática e escalar no idioma disponível",
    "potential_consequence": "Robô mistura idiomas ou comunica conteúdo incompleto"
  },
  {
    "location": "addendum.md:197-226",
    "trigger_condition": "Pedido inclui dados de várias crianças com consentimentos distintos",
    "guard_snippet": "Definir: consentimento, finalidade e retenção por arquivo ou pessoa",
    "potential_consequence": "Uma autorização é aplicada indevidamente a todas as imagens"
  }
]
