# Sistema de Workflows e Automação - DMC DataLoad

## 📋 Resumo da Implementação

Este documento descreve o módulo de **Workflows e Automação** implementado no DMC DataLoad, que permite conectar APIs externas, capturar eventos baseados em respostas e disparar automações.

---

## 🗂️ Arquivos Criados/Modificados

### Banco de Dados
- **migrations/004_create_workflow_tables.sql** - Schema com 8 tabelas

### Controllers
- **src/Controladores/ApiExternaController.php** (~560 linhas)
  - CRUD de APIs externas
  - CRUD de eventos
  - Teste de conexão
  - Parser JSONPath
  
- **src/Controladores/WorkflowController.php** (~350 linhas)
  - CRUD de workflows
  - Execução manual
  - Duplicação
  - Gestão de execuções

### Engine
- **src/Lib/WorkflowEngine.php** (~490 linhas)
  - Interpretação de grafo de workflow
  - Execução de nós (trigger, rotina, condition, delay, notification, etc.)
  - Avaliação de condições de edges
  - Log detalhado de execução por nó

### Views (UI)
- **views/apis-externas.php** - Gerenciamento de APIs externas
- **views/eventos-api.php** - Configuração de eventos
- **views/workflows.php** - Lista de workflows
- **views/workflow-builder.php** - Canvas drag-and-drop
- **views/workflow-execucoes.php** - Histórico de execuções

### Worker
- **bin/api-monitor-worker.php** - Worker para polling de APIs
  - Execução: `php bin/api-monitor-worker.php [--interval=30] [--once] [--quiet]`

### Rotas (public/index.php)
- 25+ novas rotas adicionadas para APIs, eventos e workflows

### Menu (views/layouts/base.php)
- Nova seção "Automação" com 4 links

---

## 🗃️ Estrutura do Banco de Dados

### Tabelas Principais

| Tabela | Descrição |
|--------|-----------|
| `tb_api_externas` | APIs externas configuradas |
| `tb_eventos_api` | Eventos de captura baseados em JSONPath |
| `tb_valores_capturados` | Histórico de valores capturados |
| `tb_workflows` | Definição dos workflows |
| `tb_workflow_nodes` | Nós individuais (sincronizados do JSON) |
| `tb_workflow_edges` | Conexões entre nós |
| `tb_workflow_execucoes` | Log de execuções de workflows |
| `tb_workflow_node_execucoes` | Log de execução por nó |

---

## ✅ Funcionalidades Implementadas

### 1. APIs Externas
- [x] Cadastro com URL, método (GET/POST), headers customizados
- [x] Autenticação: None, Bearer Token, Basic Auth, API Key
- [x] Teste de conexão com visualização de resposta
- [x] Ativar/desativar APIs
- [x] Intervalo de polling configurável

### 2. Eventos de API
- [x] Captura de valores via JSONPath (ex: `$.data[0].status`)
- [x] Operadores: equals, not_equals, greater_than, less_than, contains, regex, etc.
- [x] Ações: trigger_workflow, store_value, notify, store_and_trigger
- [x] Teste de JSONPath com dados simulados
- [x] Associação com workflow

### 3. Workflow Builder
- [x] Canvas drag-and-drop interativo
- [x] Tipos de nó: Trigger, Rotina, Condition, Delay, Notification, Variable, End
- [x] Conexão visual entre nós
- [x] Propriedades editáveis por nó
- [x] Condições: Verdadeiro/Falso para nós de condição
- [x] Salvar/carregar workflow
- [x] Executar manualmente

### 4. Execução de Workflows
- [x] Engine de interpretação de grafo
- [x] Execução de rotinas do sistema
- [x] Condições com expressões
- [x] Delays configuráveis
- [x] Notificações (log/email/webhook)
- [x] Variáveis de contexto
- [x] Log detalhado por nó

### 5. API Monitor Worker
- [x] Polling automático de APIs
- [x] Extração de valores via JSONPath
- [x] Avaliação de condições
- [x] Disparo automático de workflows
- [x] Registro de valores capturados
- [x] Atualização de estatísticas

---

## 🧪 Testes Realizados

### Testes de Banco
- [x] Migration 004 executada com sucesso (38 comandos)
- [x] Todas as 8 tabelas criadas
- [x] Índices criados corretamente

### Testes do Worker
- [x] Conexão com banco OK
- [x] Requisição à API JSONPlaceholder OK (HTTP 200)
- [x] Extração JSONPath OK (`$[0].id` = 1)
- [x] Avaliação de condição OK (1 > 50 = false)
- [x] Fluxo completo sem erros

### Testes de Interface
- [x] Servidor PHP rodando na porta 8042
- [x] Páginas carregando com layout correto
- [x] Redirecionamento para login funcionando

---

## 📝 Pendências e Sugestões

### Melhorias Futuras
1. **Autenticação OAuth 2.0** - Adicionar suporte para OAuth
2. **Retry automático** - Retentar requisições falhas
3. **Histórico de alterações** - Versionar edições de workflows
4. **Logs em tempo real** - WebSocket para acompanhar execuções
5. **Templates de workflow** - Biblioteca de workflows prontos
6. **Integração Slack/Discord** - Notificações em canais
7. **Dashboard de métricas** - Gráficos de execuções

### Bugs Conhecidos
- Nenhum bug crítico identificado

---

## 🚀 Como Usar

### 1. Cadastrar API Externa
```
Acessar: /apis-externas
→ Clicar "Nova API"
→ Configurar URL, método, autenticação
→ Testar conexão
→ Salvar
```

### 2. Configurar Evento
```
Acessar: /eventos-api
→ Clicar "Novo Evento"
→ Selecionar API
→ Definir JSONPath (ex: $.status)
→ Configurar operador e valor esperado
→ Associar workflow (opcional)
→ Salvar
```

### 3. Criar Workflow
```
Acessar: /workflow-builder
→ Arrastar nós da paleta para o canvas
→ Conectar nós clicando nos conectores
→ Configurar propriedades de cada nó
→ Salvar
```

### 4. Iniciar Monitor
```bash
# Executar uma vez
php bin/api-monitor-worker.php --once

# Executar continuamente (produção)
php bin/api-monitor-worker.php --interval=60
```

---

## 📊 Dados de Teste

Foram inseridos dados de teste:
- 2 APIs do JSONPlaceholder (Posts e Users)
- 1 Workflow de teste com 3 nós
- 1 Evento configurado para disparar quando ID > 50

---

**Implementação concluída em: Fevereiro/2026**
**Desenvolvido por: GitHub Copilot + Usuário**
