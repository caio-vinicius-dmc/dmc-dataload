-- =====================================================
-- MIGRAÇÃO 005: Sistema de Pipelines Visuais
-- Data: 2026-03-18
-- Descrição: Cria tabelas para pipelines drag-and-drop
-- =====================================================

-- =========================
-- TABELA: PIPELINES
-- =========================
CREATE TABLE IF NOT EXISTS tb_pipelines (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    modo VARCHAR(20) DEFAULT 'nocode',            -- nocode, lowcode, code
    ativo BOOLEAN DEFAULT false,
    dados_flow JSONB DEFAULT '{"drawflow":{"Home":{"data":{}}}}',
    dados_code TEXT DEFAULT '',
    variaveis JSONB DEFAULT '{}',
    agendamento_cron TEXT,
    trigger_tipo VARCHAR(50) DEFAULT 'manual',     -- manual, cron, evento, webhook
    trigger_config JSONB DEFAULT '{}',
    versao INTEGER DEFAULT 1,
    tags JSONB DEFAULT '[]',
    criado_por INTEGER,
    data_criacao TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    data_atualizacao TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =========================
-- TABELA: EXECUÇÕES DE PIPELINES
-- =========================
CREATE TABLE IF NOT EXISTS tb_pipeline_execucoes (
    id SERIAL PRIMARY KEY,
    id_pipeline INTEGER NOT NULL REFERENCES tb_pipelines(id) ON DELETE CASCADE,
    status VARCHAR(50) DEFAULT 'pending',          -- pending, running, success, error, cancelled
    data_inicio TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    data_fim TIMESTAMP WITH TIME ZONE,
    duracao_ms INTEGER,
    nodes_total INTEGER DEFAULT 0,
    nodes_executados INTEGER DEFAULT 0,
    nodes_sucesso INTEGER DEFAULT 0,
    nodes_falha INTEGER DEFAULT 0,
    resultado JSONB DEFAULT '{}',
    log_execucao JSONB DEFAULT '[]',
    erro TEXT,
    executado_por INTEGER
);

-- =========================
-- ÍNDICES
-- =========================
CREATE INDEX IF NOT EXISTS idx_pipelines_ativo ON tb_pipelines(ativo);
CREATE INDEX IF NOT EXISTS idx_pipelines_modo ON tb_pipelines(modo);
CREATE INDEX IF NOT EXISTS idx_pipelines_trigger_tipo ON tb_pipelines(trigger_tipo);
CREATE INDEX IF NOT EXISTS idx_pipeline_exec_pipeline ON tb_pipeline_execucoes(id_pipeline);
CREATE INDEX IF NOT EXISTS idx_pipeline_exec_status ON tb_pipeline_execucoes(status);
CREATE INDEX IF NOT EXISTS idx_pipeline_exec_data ON tb_pipeline_execucoes(data_inicio);
