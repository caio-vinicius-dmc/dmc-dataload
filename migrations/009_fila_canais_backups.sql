-- Migration 009: Background Queue (Fila de Execução)
-- DMC DataLoad - Background execution queue

CREATE TABLE IF NOT EXISTS tb_fila_execucao (
    id SERIAL PRIMARY KEY,
    tipo VARCHAR(30) NOT NULL,              -- rotina, pipeline, workflow
    id_recurso INTEGER NOT NULL,            -- ID da rotina/pipeline/workflow
    nome_recurso VARCHAR(255),              -- Nome para referência rápida
    status VARCHAR(20) NOT NULL DEFAULT 'pendente', -- pendente, processando, concluido, falha, cancelado
    prioridade SMALLINT NOT NULL DEFAULT 5, -- 1 (máxima) a 10 (mínima)
    tentativas INTEGER NOT NULL DEFAULT 0,
    max_tentativas INTEGER NOT NULL DEFAULT 3,
    agendado_para TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    iniciado_em TIMESTAMP WITH TIME ZONE,
    concluido_em TIMESTAMP WITH TIME ZONE,
    resultado JSONB,                        -- Resultado da execução (logs, métricas)
    erro TEXT,                              -- Mensagem de erro se falhou
    id_usuario INTEGER,                     -- Quem enfileirou
    nome_usuario VARCHAR(255),
    worker_id VARCHAR(100),                 -- ID do worker que processou
    criado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_fila_status ON tb_fila_execucao(status);
CREATE INDEX IF NOT EXISTS idx_fila_prioridade ON tb_fila_execucao(prioridade, criado_em);
CREATE INDEX IF NOT EXISTS idx_fila_tipo ON tb_fila_execucao(tipo, id_recurso);
CREATE INDEX IF NOT EXISTS idx_fila_agendado ON tb_fila_execucao(agendado_para) WHERE status = 'pendente';
CREATE INDEX IF NOT EXISTS idx_fila_usuario ON tb_fila_execucao(id_usuario);

-- Tabela de notificações Slack/Teams
CREATE TABLE IF NOT EXISTS tb_canais_notificacao (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo VARCHAR(20) NOT NULL,              -- slack, teams, discord
    webhook_url TEXT NOT NULL,
    canal VARCHAR(100),                     -- #canal ou nome do canal
    ativo BOOLEAN NOT NULL DEFAULT true,
    notificar_sucesso BOOLEAN NOT NULL DEFAULT false,
    notificar_falha BOOLEAN NOT NULL DEFAULT true,
    notificar_inicio BOOLEAN NOT NULL DEFAULT false,
    tipos_recurso TEXT[] DEFAULT ARRAY['rotina','pipeline','workflow'],
    criado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    criado_por INTEGER
);

CREATE INDEX IF NOT EXISTS idx_canais_tipo ON tb_canais_notificacao(tipo);
CREATE INDEX IF NOT EXISTS idx_canais_ativo ON tb_canais_notificacao(ativo);

-- Tabela de backups
CREATE TABLE IF NOT EXISTS tb_backups (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo VARCHAR(30) NOT NULL DEFAULT 'completo', -- completo, rotinas, configuracoes
    tamanho_bytes BIGINT,
    caminho_arquivo TEXT,
    checksum VARCHAR(64),                   -- SHA-256
    status VARCHAR(20) NOT NULL DEFAULT 'pendente', -- pendente, gerando, concluido, falha
    erro TEXT,
    id_usuario INTEGER,
    nome_usuario VARCHAR(255),
    criado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    concluido_em TIMESTAMP WITH TIME ZONE
);

CREATE INDEX IF NOT EXISTS idx_backups_status ON tb_backups(status);
CREATE INDEX IF NOT EXISTS idx_backups_tipo ON tb_backups(tipo);
