-- DMC DataLoad - Migration 006: Notificações e Rate Limiting
-- Tabela de notificações do sistema

CREATE TABLE IF NOT EXISTS tb_notificacoes (
    id SERIAL PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL DEFAULT 'system',
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT,
    dados JSONB DEFAULT '{}',
    lida BOOLEAN DEFAULT FALSE,
    id_usuario INTEGER,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_notificacoes_lida ON tb_notificacoes (lida);
CREATE INDEX IF NOT EXISTS idx_notificacoes_tipo ON tb_notificacoes (tipo);
CREATE INDEX IF NOT EXISTS idx_notificacoes_created ON tb_notificacoes (created_at DESC);

-- Tabela de rate limiting
CREATE TABLE IF NOT EXISTS tb_rate_limits (
    id SERIAL PRIMARY KEY,
    chave VARCHAR(255) NOT NULL,
    tentativas INTEGER DEFAULT 1,
    primeira_tentativa TIMESTAMP DEFAULT NOW(),
    ultima_tentativa TIMESTAMP DEFAULT NOW()
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_rate_limits_chave ON tb_rate_limits (chave);
