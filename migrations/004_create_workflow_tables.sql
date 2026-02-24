-- =====================================================
-- MIGRAÇÃO 004: Sistema de Workflows e APIs Externas
-- Data: 2026-02-03
-- Descrição: Cria tabelas para automação de workflows
-- =====================================================

-- =========================
-- TABELA: APIs EXTERNAS
-- =========================
CREATE TABLE IF NOT EXISTS tb_api_externas (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    url TEXT NOT NULL,
    metodo VARCHAR(10) DEFAULT 'GET',
    headers JSONB DEFAULT '{}',
    auth_tipo VARCHAR(50) DEFAULT 'none', -- none, bearer, basic, api_key
    credenciais JSONB DEFAULT '{}', -- {token: xxx} ou {username: x, password: y} ou {api_key: x, api_key_header: x}
    body_template TEXT, -- Template de body para POST/PUT
    tipo_resposta VARCHAR(20) DEFAULT 'json', -- json, xml, text
    intervalo_polling INT DEFAULT 60, -- segundos
    timeout INT DEFAULT 30,
    ativo BOOLEAN DEFAULT true,
    ultima_verificacao TIMESTAMP WITH TIME ZONE,
    ultimo_status VARCHAR(50), -- success, error, timeout
    ultimo_erro TEXT,
    criado_por INT,
    data_criacao TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    data_atualizacao TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_api_externas_ativo ON tb_api_externas(ativo);
CREATE INDEX IF NOT EXISTS idx_api_externas_nome ON tb_api_externas(nome);

-- =========================
-- TABELA: EVENTOS DE API
-- =========================
CREATE TABLE IF NOT EXISTS tb_eventos_api (
    id SERIAL PRIMARY KEY,
    id_api INT NOT NULL REFERENCES tb_api_externas(id) ON DELETE CASCADE,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    jsonpath VARCHAR(500), -- Ex: "$.data.status" ou "$.results[0].value"
    xpath VARCHAR(500), -- Para XML
    tipo_valor VARCHAR(20) DEFAULT 'string', -- boolean, string, number, json, array
    operador VARCHAR(30) NOT NULL, -- equals, not_equals, contains, not_contains, greater_than, less_than, greater_or_equal, less_or_equal, in_array, not_in_array, is_null, is_not_null, regex
    valor_esperado TEXT,
    acao VARCHAR(50) DEFAULT 'trigger_workflow', -- trigger_workflow, store_value, notify, store_and_trigger
    id_workflow INT, -- Workflow a ser disparado
    armazenar_valor BOOLEAN DEFAULT true,
    ativo BOOLEAN DEFAULT true,
    ultima_verificacao TIMESTAMP WITH TIME ZONE,
    ultimo_valor_capturado TEXT,
    ultimo_match BOOLEAN,
    total_matches INT DEFAULT 0,
    data_criacao TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    data_atualizacao TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_eventos_api_id_api ON tb_eventos_api(id_api);
CREATE INDEX IF NOT EXISTS idx_eventos_api_ativo ON tb_eventos_api(ativo);
CREATE INDEX IF NOT EXISTS idx_eventos_api_workflow ON tb_eventos_api(id_workflow);

-- =========================
-- TABELA: VALORES CAPTURADOS
-- =========================
CREATE TABLE IF NOT EXISTS tb_valores_capturados (
    id SERIAL PRIMARY KEY,
    id_evento INT NOT NULL REFERENCES tb_eventos_api(id) ON DELETE CASCADE,
    id_api INT NOT NULL REFERENCES tb_api_externas(id) ON DELETE CASCADE,
    valor TEXT,
    valor_json JSONB, -- Para valores complexos
    response_completo JSONB, -- Resposta completa da API
    condição_match BOOLEAN DEFAULT false,
    processado BOOLEAN DEFAULT false,
    id_workflow_execucao INT, -- Referência ao workflow disparado
    data_captura TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_valores_capturados_evento ON tb_valores_capturados(id_evento);
CREATE INDEX IF NOT EXISTS idx_valores_capturados_processado ON tb_valores_capturados(processado);
CREATE INDEX IF NOT EXISTS idx_valores_capturados_data ON tb_valores_capturados(data_captura);

-- =========================
-- TABELA: WORKFLOWS
-- =========================
CREATE TABLE IF NOT EXISTS tb_workflows (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT false,
    dados_json JSONB NOT NULL DEFAULT '{"nodes":[],"edges":[]}', -- Estrutura completa do workflow
    versao INT DEFAULT 1,
    trigger_tipo VARCHAR(50) DEFAULT 'manual', -- manual, api_event, cron, rotina_finished
    trigger_config JSONB DEFAULT '{}', -- Configurações do trigger
    criado_por INT,
    data_criacao TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    data_atualizacao TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_workflows_ativo ON tb_workflows(ativo);
CREATE INDEX IF NOT EXISTS idx_workflows_trigger ON tb_workflows(trigger_tipo);

-- =========================
-- TABELA: NÓS DO WORKFLOW
-- =========================
CREATE TABLE IF NOT EXISTS tb_workflow_nodes (
    id SERIAL PRIMARY KEY,
    id_workflow INT NOT NULL REFERENCES tb_workflows(id) ON DELETE CASCADE,
    node_id VARCHAR(50) NOT NULL, -- ID único no canvas
    tipo_node VARCHAR(50) NOT NULL, -- trigger, rotina, condition, delay, notification, loop, parallel, end
    label VARCHAR(255),
    id_referencia INT, -- ID da rotina, API, evento, etc
    posicao_x INT DEFAULT 0,
    posicao_y INT DEFAULT 0,
    config_json JSONB DEFAULT '{}', -- Configurações específicas do nó
    ordem_execucao INT DEFAULT 0,
    data_criacao TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(id_workflow, node_id)
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_workflow_nodes_workflow ON tb_workflow_nodes(id_workflow);
CREATE INDEX IF NOT EXISTS idx_workflow_nodes_tipo ON tb_workflow_nodes(tipo_node);

-- =========================
-- TABELA: CONEXÕES (EDGES)
-- =========================
CREATE TABLE IF NOT EXISTS tb_workflow_edges (
    id SERIAL PRIMARY KEY,
    id_workflow INT NOT NULL REFERENCES tb_workflows(id) ON DELETE CASCADE,
    edge_id VARCHAR(50) NOT NULL,
    node_origem VARCHAR(50) NOT NULL,
    node_destino VARCHAR(50) NOT NULL,
    condicao VARCHAR(50) DEFAULT 'always', -- always, when_success, when_error, when_true, when_false, custom
    expressao_condicional TEXT, -- Ex: "rotina_1.registros > 100"
    label VARCHAR(100),
    estilo JSONB DEFAULT '{}', -- Estilos visuais (cor, tipo de linha, etc)
    data_criacao TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(id_workflow, edge_id)
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_workflow_edges_workflow ON tb_workflow_edges(id_workflow);
CREATE INDEX IF NOT EXISTS idx_workflow_edges_origem ON tb_workflow_edges(node_origem);
CREATE INDEX IF NOT EXISTS idx_workflow_edges_destino ON tb_workflow_edges(node_destino);

-- =========================
-- TABELA: EXECUÇÕES DE WORKFLOW
-- =========================
CREATE TABLE IF NOT EXISTS tb_workflow_execucoes (
    id SERIAL PRIMARY KEY,
    id_workflow INT NOT NULL REFERENCES tb_workflows(id) ON DELETE CASCADE,
    versao_workflow INT DEFAULT 1,
    status VARCHAR(50) DEFAULT 'pending', -- pending, running, completed, failed, cancelled, paused
    triggered_by VARCHAR(50), -- api_event, manual, cron, rotina_finished
    trigger_data JSONB DEFAULT '{}', -- Dados do trigger (valor da API, etc)
    contexto JSONB DEFAULT '{}', -- Variáveis de contexto durante execução
    data_inicio TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    data_fim TIMESTAMP WITH TIME ZONE,
    duracao_ms INT,
    nodes_total INT DEFAULT 0,
    nodes_executados INT DEFAULT 0,
    nodes_sucesso INT DEFAULT 0,
    nodes_falha INT DEFAULT 0,
    nodes_pulados INT DEFAULT 0,
    resultado_json JSONB DEFAULT '{}', -- Status final de cada nó
    erro TEXT,
    criado_por INT
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_workflow_exec_workflow ON tb_workflow_execucoes(id_workflow);
CREATE INDEX IF NOT EXISTS idx_workflow_exec_status ON tb_workflow_execucoes(status);
CREATE INDEX IF NOT EXISTS idx_workflow_exec_data ON tb_workflow_execucoes(data_inicio);

-- =========================
-- TABELA: EXECUÇÃO DE NÓS
-- =========================
CREATE TABLE IF NOT EXISTS tb_workflow_node_execucoes (
    id SERIAL PRIMARY KEY,
    id_workflow_execucao INT NOT NULL REFERENCES tb_workflow_execucoes(id) ON DELETE CASCADE,
    node_id VARCHAR(50) NOT NULL,
    tipo_node VARCHAR(50),
    label VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending', -- pending, running, completed, failed, skipped, waiting
    data_inicio TIMESTAMP WITH TIME ZONE,
    data_fim TIMESTAMP WITH TIME ZONE,
    duracao_ms INT,
    input_data JSONB DEFAULT '{}', -- Dados de entrada do nó
    output_data JSONB DEFAULT '{}', -- Resultado do nó
    erro TEXT,
    ordem INT DEFAULT 0,
    tentativas INT DEFAULT 0
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_node_exec_workflow_exec ON tb_workflow_node_execucoes(id_workflow_execucao);
CREATE INDEX IF NOT EXISTS idx_node_exec_status ON tb_workflow_node_execucoes(status);
CREATE INDEX IF NOT EXISTS idx_node_exec_node_id ON tb_workflow_node_execucoes(node_id);

-- =========================
-- COMENTÁRIOS NAS TABELAS
-- =========================
COMMENT ON TABLE tb_api_externas IS 'APIs externas cadastradas para monitoramento';
COMMENT ON TABLE tb_eventos_api IS 'Eventos/condições monitoradas em cada API';
COMMENT ON TABLE tb_valores_capturados IS 'Histórico de valores capturados das APIs';
COMMENT ON TABLE tb_workflows IS 'Workflows de automação (fluxos)';
COMMENT ON TABLE tb_workflow_nodes IS 'Nós individuais de cada workflow';
COMMENT ON TABLE tb_workflow_edges IS 'Conexões entre nós do workflow';
COMMENT ON TABLE tb_workflow_execucoes IS 'Histórico de execuções de workflows';
COMMENT ON TABLE tb_workflow_node_execucoes IS 'Histórico de execução de cada nó';

-- =========================
-- INSERTS DE EXEMPLO
-- =========================

-- API de exemplo (JSONPlaceholder para testes)
INSERT INTO tb_api_externas (nome, descricao, url, metodo, auth_tipo, tipo_resposta, intervalo_polling, ativo)
VALUES 
    ('JSONPlaceholder - Posts', 'API de teste para verificar posts', 'https://jsonplaceholder.typicode.com/posts/1', 'GET', 'none', 'json', 30, true),
    ('JSONPlaceholder - Users', 'API de teste para verificar usuários', 'https://jsonplaceholder.typicode.com/users', 'GET', 'none', 'json', 60, true)
ON CONFLICT DO NOTHING;
