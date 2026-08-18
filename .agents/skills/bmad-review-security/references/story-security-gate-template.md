# Template de Gate de Seguranca para Story

Adicione ou verifique este bloco antes de marcar uma story como `ready-for-dev`.

## Security Gate - bmad-review-security

- Revisao executada em: `{date}`
- Relatorio: `{review_report_path}`
- Resultado: `{Aprovado | Bloqueado | Aprovado com ressalvas}`
- Alto risco aberto: `{sim | nao}`
- Medio risco aberto com owner: `{sim | nao | n/a}`
- Responsavel por aceite de risco: `{nome | n/a}`

### Condicoes para Desenvolvimento

- `{condicao 1}`
- `{condicao 2}`

### Evidencia Exigida na Implementacao

- Testes negativos de autenticacao/autorizacao.
- Testes de validacao de payload e path traversal quando houver arquivo/path.
- Verificacao de erro sanitizado.
- Verificacao de ausencia de PII/segredos em logs.
- Execucao de SAST/SCA/secret scan disponivel no projeto.
