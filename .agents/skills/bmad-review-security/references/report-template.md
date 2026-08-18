# Template de Relatorio - Revisao Adversarial de Seguranca

Use este template para toda execucao de `bmad-review-security`.

## Metadados

- Artefato auditado: `{artifact}`
- Escopo: `{scope}`
- Data: `{date}`
- Auditor: Vex - Security Auditor
- Resultado do gate: `{Aprovado | Bloqueado | Aprovado com ressalvas}`

## Evidencias Coletadas

- Arquivos lidos: `{files_read}`
- Comandos executados: `{commands_run}`
- Ferramentas nao executadas: `{tools_not_run_with_reason}`
- Referencias consultadas: `{references}`

## Threat Model STRIDE

| Categoria | Superficie | Risco | Mitigacao existente | Gap |
| --- | --- | --- | --- | --- |
| Spoofing | `{entrypoint}` | `{risk}` | `{control}` | `{gap}` |
| Tampering | `{entrypoint}` | `{risk}` | `{control}` | `{gap}` |
| Repudiation | `{entrypoint}` | `{risk}` | `{control}` | `{gap}` |
| Information Disclosure | `{entrypoint}` | `{risk}` | `{control}` | `{gap}` |
| Denial of Service | `{entrypoint}` | `{risk}` | `{control}` | `{gap}` |
| Elevation of Privilege | `{entrypoint}` | `{risk}` | `{control}` | `{gap}` |

## Achados

### Alto Risco

#### `{ID}`

- Item / Componente Afetado: `{arquivo:linha | spec/secao}`
- Risco Detectado: `{descricao tecnica}`
- Impacto para o Projeto: `{exploracao possivel e consequencia de negocio}`
- Solucao Recomendada: `{correcao precisa, teste ou configuracao}`
- Evidencia: `{trecho, comando, teste, ausencia de controle}`
- Status: `{Aberto | Corrigido | Aceito pelo Responsavel | Nao Reproduzido}`

### Medio Risco

#### `{ID}`

- Item / Componente Afetado: `{arquivo:linha | spec/secao}`
- Risco Detectado: `{descricao tecnica}`
- Impacto para o Projeto: `{exploracao possivel e consequencia de negocio}`
- Solucao Recomendada: `{correcao precisa, teste ou configuracao}`
- Evidencia: `{trecho, comando, teste, ausencia de controle}`
- Status: `{Aberto | Corrigido | Aceito pelo Responsavel | Nao Reproduzido}`

### Baixo Risco

#### `{ID}`

- Item / Componente Afetado: `{arquivo:linha | spec/secao}`
- Risco Detectado: `{descricao tecnica}`
- Impacto para o Projeto: `{exploracao possivel e consequencia de negocio}`
- Solucao Recomendada: `{correcao precisa, teste ou configuracao}`
- Evidencia: `{trecho, comando, teste, ausencia de controle}`
- Status: `{Aberto | Corrigido | Aceito pelo Responsavel | Nao Reproduzido}`

## Decisao do Gate

- Decisao: `{Aprovado | Bloqueado | Aprovado com ressalvas}`
- Condicoes antes de avancar: `{conditions}`
- Owner: `{owner}`
- Prazo / proximo checkpoint: `{deadline_or_checkpoint}`
