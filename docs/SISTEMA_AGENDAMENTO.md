# 📅 Sistema de Agendamento Completo - DMC DataLoad

## 🎯 Visão Geral

O sistema de agendamento foi completamente reformulado para oferecer controle total sobre quando e como as rotinas são executadas.

## ✨ Funcionalidades

### 1. **Configuração Visual de Frequência**
Não precisa conhecer CRON! Configure de forma visual:

- **A cada X minutos**: Ideal para processos que precisam rodar frequentemente
  - Ex: A cada 5 minutos, a cada 15 minutos, etc.
  
- **A cada X horas**: Para rotinas que rodam algumas vezes por dia
  - Ex: A cada 2 horas, a cada 6 horas, etc.
  
- **Diariamente**: Executa uma vez por dia em horário específico
  - Ex: Todos os dias às 08:00
  
- **Semanalmente**: Escolha os dias da semana
  - Ex: Segunda, Quarta e Sexta às 09:00
  
- **Mensalmente**: Escolha o dia do mês
  - Ex: Todo dia 1 às 00:00

### 2. **Período de Execução**

#### Data de Início
- Define quando o agendamento começa a valer
- Se não informado, começa imediatamente
- Formato: DD/MM/YYYY HH:MM

#### Data de Fim
- Define quando o agendamento para de executar
- Útil para rotinas temporárias ou campanhas com prazo
- Formato: DD/MM/YYYY HH:MM

**Exemplo**: 
- Início: 01/02/2025 08:00
- Fim: 28/02/2025 18:00
- Resultado: Rotina só executará durante Fevereiro/2025, das 8h às 18h

### 3. **Exceções e Dias Ignorados**

#### Datas Específicas para Ignorar
Adicione datas em que a rotina NÃO deve executar:
```
25/12/2024
01/01/2025
21/04/2025
```

**Casos de uso**:
- Feriados específicos da empresa
- Dias de manutenção programada
- Eventos especiais

#### Ignorar Feriados Nacionais
- ☑️ Marque esta opção para pular feriados brasileiros automaticamente
- Inclui: Ano Novo, Carnaval, Páscoa, Tiradentes, Trabalho, Corpus Christi, Independência, N. Sra. Aparecida, Finados, Proclamação da República, Natal

### 4. **Configurações Avançadas**

#### Máximo de Tentativas
- Quantas vezes tentar executar antes de desistir
- Padrão: 3 tentativas
- Se todas falharem, a rotina é marcada como erro

#### Intervalo Entre Tentativas (minutos)
- Quanto tempo esperar entre cada tentativa
- Padrão: 5 minutos
- Evita sobrecarregar o sistema

#### Timeout (segundos)
- Tempo máximo de execução antes de cancelar
- Padrão: 300 segundos (5 minutos)
- Previne rotinas travadas

#### Notificar em Caso de Falha
- ☑️ Receber notificação quando a rotina falhar
- Notificações por email/sistema (a implementar)

### 5. **Modo CRON Manual** (Para Usuários Avançados)

Se você conhece expressões CRON, pode usar diretamente:

**Presets Disponíveis**:
- A cada minuto: `* * * * *`
- A cada 5 minutos: `*/5 * * * *`
- A cada hora: `0 * * * *`
- Diariamente à meia-noite: `0 0 * * *`
- Diariamente às 8h: `0 8 * * *`
- Seg-Sex às 9h: `0 9 * * 1-5`
- Todo Domingo às 1h: `0 1 * * 0`
- Primeiro dia do mês: `0 0 1 * *`

**Formato CRON**: `minuto hora dia mês dia-da-semana`

## 📖 Como Usar

### Passo 1: Acessar o Scheduler
```
http://localhost/DMC-DATALOAD/public/scheduler
```

### Passo 2: Criar Novo Agendamento
1. Clique em **"+ Novo Agendamento"**
2. Selecione a **Rotina** que deseja agendar

### Passo 3: Escolher o Modo

#### Modo Visual (Recomendado)
1. Selecione a **Frequência** (minutos, horas, diário, semanal, mensal)
2. Configure o **intervalo** ou **horário**
3. Para semanal: marque os dias da semana
4. Para mensal: informe o dia do mês

#### Modo CRON Manual
1. Marque "CRON Manual"
2. Use um preset ou digite a expressão CRON
3. Veja o preview da descrição

### Passo 4: Definir Período (Opcional)
- **Data de Início**: Quando começar
- **Data de Fim**: Quando terminar (deixe vazio para nunca parar)

### Passo 5: Configurar Exceções (Opcional)
- **Datas para Ignorar**: Digite uma data por linha
- **Ignorar Feriados**: Marque se quiser pular feriados brasileiros

### Passo 6: Ajustar Configurações Avançadas (Opcional)
- **Máx. Tentativas**: Quantas vezes tentar em caso de erro
- **Intervalo Tentativas**: Minutos entre tentativas
- **Timeout**: Segundos máximos de execução
- **Notificar Falha**: Receber alertas

### Passo 7: Salvar
1. Veja o **Preview** (CRON, descrição, próxima execução)
2. Clique em **"Salvar Agendamento"**
3. A rotina aparecerá na tabela "Rotinas Agendadas"

## 🎨 Preview em Tempo Real

O modal mostra um preview instantâneo:
- **Expressão CRON**: Como será armazenado
- **Descrição**: Em linguagem natural
- **Próxima Execução**: Quando executará pela primeira vez

## 💾 Dados Salvos

Todas as configurações são salvas na tabela `tb_rotinas`:

| Campo | Descrição |
|-------|-----------|
| `agendamento_cron` | Expressão CRON |
| `data_inicio` | Data/hora de início |
| `data_fim` | Data/hora de término |
| `datas_ignorar_json` | Array JSON com datas a ignorar |
| `ignorar_feriados` | Se ignora feriados (boolean) |
| `max_tentativas` | Máximo de tentativas |
| `intervalo_tentativas` | Minutos entre tentativas |
| `timeout` | Timeout em segundos |
| `notificar_falha` | Se notifica falhas (boolean) |
| `ativa` | Se está ativa (boolean) |

## 🔍 Exemplos Práticos

### Exemplo 1: Sincronização Diária
**Cenário**: Sincronizar dados com sistema externo todos os dias às 2h da manhã.

**Configuração**:
- Frequência: Diariamente
- Horário: 02:00
- Ignorar Feriados: Sim
- Período: Sem data de fim

### Exemplo 2: Processamento em Horário Comercial
**Cenário**: Processar pedidos a cada 15 minutos, apenas em dias úteis, das 8h às 18h.

**Configuração**:
- Frequência: A cada 15 minutos
- Dias da Semana: Segunda a Sexta
- Data Início: 01/02/2025 08:00
- Exceções: Feriados nacionais
- Ignorar Feriados: Sim

### Exemplo 3: Relatório Mensal
**Cenário**: Gerar relatório mensal no primeiro dia útil de cada mês.

**Configuração**:
- Frequência: Mensalmente
- Dia do Mês: 1
- Horário: 06:00
- Ignorar Feriados: Sim

### Exemplo 4: Campanha Temporária
**Cenário**: Rotina especial durante campanha de Black Friday.

**Configuração**:
- Frequência: A cada 5 minutos
- Data Início: 20/11/2025 00:00
- Data Fim: 30/11/2025 23:59
- Datas Ignorar: -
- Max Tentativas: 5

## 🛠️ Manutenção

### Editar Agendamento
1. Clique no botão de editar (✏️) na tabela
2. Modifique as configurações
3. Salve novamente

### Ativar/Desativar
- Use o switch na coluna "Status"
- Desativada: não executará, mas mantém a configuração

### Ver Logs
- Clique em "Ver Logs" para histórico de execuções
- Veja quando executou, quanto tempo levou, se houve erros

## ⚠️ Observações Importantes

1. **Worker Deve Estar Rodando**: O scheduler worker precisa estar ativo para executar as rotinas
2. **Horário do Servidor**: Todas as datas/horas são baseadas no horário do servidor
3. **CRON vs Período**: A expressão CRON define a frequência, mas o período (data_inicio/data_fim) limita quando pode executar
4. **Exceções Têm Prioridade**: Se uma data está nas exceções, não executará mesmo que caia na frequência CRON
5. **Feriados Móveis**: Páscoa, Carnaval e Corpus Christi são calculados automaticamente

## 🚀 Próximos Passos

- [ ] Sistema de notificações por email
- [ ] Logs detalhados de por que uma execução foi pulada
- [ ] Calendário visual mostrando próximas execuções
- [ ] Feriados estaduais e municipais
- [ ] Grupos de rotinas (executar várias juntas)
- [ ] Dependências entre rotinas

## 📞 Suporte

Em caso de dúvidas ou problemas:
1. Verifique se o worker está rodando
2. Consulte os logs em "Log do Scheduler"
3. Valide a expressão CRON no preview
4. Verifique se as datas de exceção estão no formato correto (DD/MM/YYYY)
