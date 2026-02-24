# 🚀 DMC DataLoad

<div align="center">

![DMC DataLoad](https://img.shields.io/badge/DMC-DataLoad-blue?style=for-the-badge&logo=postgresql)
![Version](https://img.shields.io/badge/version-1.0.0-green?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15%2B-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![License](https://img.shields.io/badge/license-Apache%202.0-orange?style=for-the-badge)

**Sistema de Orquestração ETL (Extract, Transform, Load) desenvolvido em PHP com PostgreSQL**

[Documentação](docs/documentacao.html) • [Reportar Bug](https://github.com/caio-vinicius-dmc/dmc-dataload/issues) • [Solicitar Feature](https://github.com/caio-vinicius-dmc/dmc-dataload/issues)

</div>

---

## 📋 Sobre o Projeto

**DMC DataLoad** é uma plataforma completa de orquestração ETL que permite criar, gerenciar e executar rotinas de processamento de dados de forma visual e intuitiva. O sistema foi projetado para automatizar pipelines de dados, facilitando a integração entre diferentes fontes de dados e processos de transformação.

### ✨ Principais Características

- 🎨 **Interface Visual Moderna** - UI responsiva e intuitiva desenvolvida com Bootstrap 5
- 🔐 **Segurança Robusta** - Criptografia AES-256-CBC para senhas, sessões seguras, prepared statements
- ⚡ **Alta Performance** - Processamento assíncrono e workers dedicados
- 📊 **Monitoramento em Tempo Real** - Dashboard com métricas, gráficos e alertas
- 🔄 **Agendamento Inteligente** - Suporte completo a expressões CRON
- 📝 **SQL Editor Profissional** - Editor com syntax highlighting e múltiplas abas
- 📅 **Visualização em Calendário** - Visão temporal das execuções
- 📈 **Histórico Detalhado** - Logs completos de todas as operações
- 💾 **Exportação de Dados** - Geração automática de CSV
- 👥 **Gestão de Usuários** - Múltiplos níveis de acesso

---

## 🎯 Funcionalidades

### 🗄️ Gestão de Conexões
- Cadastro de múltiplas conexões PostgreSQL
- Teste de conectividade antes de salvar
- Senhas criptografadas com AES-256-CBC
- Suporte a diferentes schemas

### 📋 Editor de Rotinas
- Interface drag-and-drop para criar fluxos ETL
- Múltiplos blocos SQL (SELECT, INSERT, UPDATE, DELETE)
- Validação de sintaxe em tempo real
- Reutilização de blocos entre rotinas
- Documentação inline

### ⏰ Agendamento Automático
- Expressões CRON com validação
- Presets pré-configurados (diário, semanal, mensal)
- Cálculo de próximas execuções
- Histórico de agendamentos
- Controle de execuções paralelas

### 💻 SQL Editor
- Editor profissional com CodeMirror
- Syntax highlighting para SQL
- Múltiplas abas e sessões
- Explorador de objetos do banco
- **Autocomplete Inteligente**: Sugestões dinâmicas de schemas, tabelas, views, colunas e keywords SQL baseadas no banco conectado
- Modo fullscreen para máxima produtividade
- **Layout configurável**: Vertical (editor acima, resultados abaixo) ou Horizontal (3 colunas: Database Explorer | Editor | Resultados)
- Painéis redimensionáveis (sidebar, editor e resultados)
- **Atalhos de teclado**: Comentar/descomentar, identar, autocomplete, zoom com Ctrl+Scroll e mais
- Autocomplete SQL com Ctrl+Space (carrega tabelas, colunas e keywords do banco atual)
- Zoom dinâmico (Ctrl + Scroll do mouse)
- Histórico de queries
- Execução de múltiplas queries

### 📊 Dashboard
- Métricas em tempo real
- Gráficos de execuções (sucessos/falhas)
- Rotinas em execução
- Próximos agendamentos
- Estatísticas de performance

### 📅 Calendário
- Visualização mensal/semanal/diária
- Código de cores por status
- Detalhes ao clicar no evento
- Filtros por rotina e status

### 📜 Histórico
- Lista completa de execuções
- Filtros avançados (data, status, rotina)
- Visualização de logs detalhados
- Exportação para CSV
- Download de resultados

### 👥 Administração
- Gestão de usuários
- Níveis de acesso (Admin, Operador, Visualizador)
- Logs do sistema
- Configurações gerais

---

## 🛠️ Tecnologias

### Backend
- **PHP 8.2+** - Linguagem principal
- **PostgreSQL 15+** - Banco de dados
- **Composer** - Gerenciador de dependências
- **PHPDotEnv** - Gerenciamento de variáveis de ambiente
- **PHPSpreadsheet** - Manipulação de planilhas
- **Cron Expression Parser** - Processamento de expressões CRON

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **jQuery 3.7** - Manipulação do DOM
- **CodeMirror 5.65** - Editor de código
- **FullCalendar** - Componente de calendário
- **SweetAlert2** - Notificações elegantes
- **Chart.js** - Gráficos interativos
- **Bootstrap Icons** - Ícones

### Infraestrutura
- **Apache/XAMPP** - Servidor web
- **PSR-4 Autoload** - Carregamento automático de classes
- **RESTful API** - Arquitetura de comunicação

---

## 📦 Instalação

### Requisitos

- PHP 8.2 ou superior
- PostgreSQL 15 ou superior
- Composer 2.x
- Extensões PHP: `pdo_pgsql`, `openssl`, `mbstring`, `json`

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/caio-vinicius-dmc/dmc-dataload.git
cd dmc-dataload
```

2. **Instale as dependências**
```bash
composer install --no-dev
```

3. **Configure o ambiente**
```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=sua_chave_32_bytes_base64

DB_HOST=localhost
DB_PORT=5433
DB_DATABASE=db_dmc_dataload
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
DB_SCHEMA=public

ENCRYPTION_KEY=sua_chave_criptografia_base64
```

4. **Crie o banco de dados**
```sql
CREATE DATABASE db_dmc_dataload;
```

5. **Execute o script SQL inicial**
```bash
psql -U postgres -d db_dmc_dataload -f sql/02-02-2026.sql
```

6. **Gere a chave de criptografia**
```bash
php scripts/encrypt_password.php
```

7. **Configure o servidor web**

Aponte o DocumentRoot para a pasta `public/`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/DMC-DATALOAD/public"
    ServerName dataload.local
    
    <Directory "C:/xampp/htdocs/DMC-DATALOAD/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

8. **Inicie o servidor**
```bash
# Opção 1: Servidor embutido do PHP (desenvolvimento)
php -S localhost:8080 -t public

# Opção 2: Apache/XAMPP (recomendado)
# Acesse: http://localhost/DMC-DATALOAD/public
```

9. **Acesse a aplicação**

Abra o navegador em: `http://localhost:8080` ou `http://dataload.local`

**Credenciais padrão:**
- Usuário: `admin`
- Senha: `admin123` (altere após primeiro login)

---

## 🚀 Como Usar

### 1. Criar uma Conexão

1. Acesse **Conexões** no menu
2. Clique em **Nova Conexão**
3. Preencha os dados:
   - Nome da conexão
   - Host e porta
   - Nome do banco
   - Usuário e senha
4. Clique em **Testar Conexão**
5. Se bem-sucedido, clique em **Salvar**

### 2. Criar uma Rotina

1. Acesse **Rotinas** > **Editor**
2. Preencha as informações básicas
3. Adicione blocos SQL:
   - **SELECT** - Consulta dados (gera CSV)
   - **INSERT** - Insere registros
   - **UPDATE** - Atualiza registros
   - **DELETE** - Remove registros
4. Configure a ordem de execução
5. Salve a rotina

### 3. Executar Manualmente

1. Na lista de rotinas, clique em **▶️ Executar**
2. Acompanhe o progresso em tempo real
3. Visualize os resultados e logs
4. Faça download do CSV (se aplicável)

### 4. Agendar Execução

1. Acesse **Scheduler**
2. Selecione a rotina
3. Configure a expressão CRON:
   - Use presets ou digite manualmente
   - Valide a expressão
4. Ative o agendamento
5. Monitore pelo dashboard

### 5. Usar o SQL Editor

1. Acesse **SQL Editor**
2. Selecione uma conexão
3. Escreva suas queries no editor
4. Execute com **F5** ou botão Executar
5. Visualize resultados em tabela
6. **Alterar layout**: Clique no botão de layout para alternar entre:
   - **Vertical**: Editor acima, resultados abaixo
   - **Horizontal**: 3 colunas lado a lado (Database Explorer | Editor | Resultados)
7. Redimensione os painéis arrastando as bordas
8. Exporte resultados para CSV

---

## 📁 Estrutura do Projeto

```
DMC-DATALOAD/
├── 📂 app/
│   ├── Controladores/          # Controllers principais (API, Conexões, Rotinas, SQL Editor)
│   ├── Controllers/            # Controllers adicionais (Calendário, Logs, Scheduler, Users)
│   ├── Core/                   # Classes fundamentais (Auth, Database, Crypto, Logger)
│   ├── Servicos/               # Serviços de negócio (Autenticação, Execução)
│   └── Utils/                  # Utilitários (Validator, Crypto)
├── 📂 bin/
│   └── scheduler-worker.php    # Worker do agendador
├── 📂 docs/
│   ├── documentacao.html       # Documentação completa
│   ├── openapi.yaml            # Especificação da API
│   └── SISTEMA_AGENDAMENTO.md  # Documentação do scheduler
├── 📂 public/
│   ├── index.php               # Front controller
│   └── .htaccess               # Configuração Apache
├── 📂 scripts/
│   ├── encrypt_password.php    # Utilitário de criptografia
│   ├── migrate.php             # Script de migração
│   ├── run_rotina_once.php     # Executar rotina via CLI
│   └── worker.php              # Worker de processamento
├── 📂 sql/
│   └── 02-02-2026.sql          # Script SQL inicial
├── 📂 storage/
│   ├── csv/                    # CSVs gerados pelas rotinas
│   └── logs/                   # Arquivos de log
├── 📂 views/
│   ├── layouts/                # Templates base
│   ├── dashboard_new.php       # Dashboard
│   ├── conexoes_new.php        # Gestão de conexões
│   ├── rotinas_new.php         # Lista de rotinas
│   ├── historico_new.php       # Histórico de execuções
│   ├── calendario.php          # Calendário visual
│   ├── scheduler.php           # Agendamentos
│   ├── sql_editor.php          # Editor SQL
│   ├── logs.php                # Logs do sistema
│   └── ...
├── .env                        # Configurações (não versionado)
├── .env.example                # Exemplo de configurações
├── composer.json               # Dependências PHP
├── LICENSE                     # Licença Apache-2.0
└── README.md                   # Este arquivo
```

---

## 🔌 API Endpoints

A aplicação possui uma API RESTful completa. Principais endpoints:

### Autenticação
- `POST /login` - Autenticar usuário
- `POST /logout` - Encerrar sessão
- `GET /api/sessao` - Verificar sessão

### Conexões
- `GET /conexoes/list` - Listar conexões
- `POST /conexoes/salvar` - Criar/atualizar
- `POST /conexoes/test` - Testar conexão
- `POST /conexoes/delete/{id}` - Excluir

### Rotinas
- `GET /rotinas/list` - Listar rotinas
- `GET /rotinas/get/{id}` - Buscar rotina com blocos
- `POST /rotinas/salvar` - Criar/atualizar
- `POST /rotinas/run/{id}` - Executar
- `POST /rotinas/toggle/{id}` - Ativar/desativar

### Histórico
- `GET /api/historico` - Listar execuções
- `GET /api/historico/{id}` - Detalhes da execução
- `GET /api/historico/exportar` - Exportar CSV

### SQL Editor
- `GET /sql-editor/connect/{id}` - Conectar ao banco
- `GET /sql-editor/objects/{id}` - Listar objetos
- `POST /sql-editor/execute` - Executar query

### Dashboard
- `GET /api/dashboard/metricas` - Métricas e estatísticas

📖 **Documentação completa:** [OpenAPI Specification](docs/openapi.yaml)

---

## 🗄️ Banco de Dados

### Principais Tabelas

| Tabela | Descrição |
|--------|-----------|
| `tb_usuarios` | Usuários do sistema |
| `tb_perfis_conexao` | Perfis de conexão com bancos |
| `tb_rotinas` | Rotinas ETL |
| `tb_blocos_rotina` | Blocos SQL das rotinas |
| `tb_logs_execucao` | Histórico de execuções |
| `tb_logs_sistema` | Logs gerais do sistema |

### Schema

```sql
-- Tabela de rotinas
CREATE TABLE tb_rotinas (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    id_conexao INTEGER REFERENCES tb_perfis_conexao(id),
    ativa BOOLEAN DEFAULT true,
    agendamento_cron VARCHAR(255),
    proxima_execucao TIMESTAMP,
    esta_executando BOOLEAN DEFAULT false,
    criado_em TIMESTAMP DEFAULT NOW(),
    atualizado_em TIMESTAMP DEFAULT NOW()
);

-- Tabela de blocos
CREATE TABLE tb_blocos_rotina (
    id SERIAL PRIMARY KEY,
    id_rotina INTEGER REFERENCES tb_rotinas(id) ON DELETE CASCADE,
    codigo_bloco VARCHAR(100) NOT NULL,
    ordem INTEGER NOT NULL,
    tipo_bloco VARCHAR(50) NOT NULL,
    script_sql TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT NOW()
);

-- ... (ver sql/02-02-2026.sql para schema completo)
```

---

## 🔒 Segurança

O DMC DataLoad implementa múltiplas camadas de segurança:

- ✅ **Criptografia AES-256-CBC** para senhas de conexão
- ✅ **Prepared Statements** em todas as queries
- ✅ **Sessões seguras** com httponly e samesite
- ✅ **Validação de entrada** em todos os endpoints
- ✅ **Autenticação obrigatória** em rotas protegidas
- ✅ **Hash bcrypt** para senhas de usuários
- ✅ **CSRF protection** em formulários
- ✅ **Logs de auditoria** de todas as ações

---

## 🧪 Testes

```bash
# Executar testes unitários (em desenvolvimento)
composer test

# Gerar relatório de cobertura
composer test-coverage
```

---

## 📈 Roadmap

### v1.1.0 (Q2 2026)
- [ ] Suporte a MySQL, SQL Server, Oracle
- [ ] Notificações por email/Slack
- [ ] Dependências entre rotinas
- [ ] Variáveis de ambiente nas queries

### v1.2.0 (Q3 2026)
- [ ] Versionamento de rotinas
- [ ] Dashboard customizável
- [ ] Temas (modo escuro)
- [ ] Integração LDAP/Active Directory

### v2.0.0 (Q4 2026)
- [ ] Containerização com Docker
- [ ] CI/CD completo
- [ ] API GraphQL
- [ ] Suporte a MongoDB

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Siga os passos:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

### Diretrizes

- Siga o padrão PSR-12 para código PHP
- Escreva testes para novas funcionalidades
- Documente mudanças no README
- Use mensagens de commit descritivas

---

## 📄 Licença

Este projeto está licenciado sob a **Apache License 2.0** - veja o arquivo [LICENSE](LICENSE) para detalhes.

```
Copyright 2026 Dynamic Motion Century

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```

---

## 👥 Autores

**Dynamic Motion Century**
- Website: [www.dynamicmotioncentury.com.br](https://www.dynamicmotioncentury.com.br)
- Email: [contato@dynamicmotioncentury.com.br](mailto:contato@dynamicmotioncentury.com.br)
- GitHub: [@caio-vinicius-dmc](https://github.com/caio-vinicius-dmc)

---

## 💬 Suporte

Encontrou um bug ou tem uma sugestão?

- 🐛 [Reportar Bug](https://github.com/caio-vinicius-dmc/dmc-dataload/issues/new?labels=bug)
- 💡 [Solicitar Feature](https://github.com/caio-vinicius-dmc/dmc-dataload/issues/new?labels=enhancement)
- 📧 [Contato Direto](mailto:contato@dynamicmotioncentury.com.br)

---

## 🌟 Star History

Se este projeto foi útil para você, considere dar uma ⭐!

---

## 📸 Screenshots

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Editor de Rotinas
![Editor](docs/screenshots/editor.png)

### SQL Editor
![SQL Editor](docs/screenshots/sql-editor.png)

### Calendário
![Calendário](docs/screenshots/calendario.png)

*Screenshots em breve*

---

<div align="center">

**Feito com ❤️ por [Dynamic Motion Century](https://www.dynamicmotioncentury.com.br)**

[⬆ Voltar ao topo](#-dmc-dataload)

</div>
