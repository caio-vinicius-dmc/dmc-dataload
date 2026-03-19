-- Migration 008: Audit Logs, Configurações, Webhooks
-- DMC DataLoad - 19/03/2026

-- =====================================================
-- 1. Tabela de Auditoria (ações administrativas)
-- =====================================================
CREATE TABLE IF NOT EXISTS tb_auditoria (
    id SERIAL PRIMARY KEY,
    acao VARCHAR(50) NOT NULL,           -- criar, editar, excluir, login, logout, compartilhar, etc.
    entidade VARCHAR(50) NOT NULL,       -- rotina, pipeline, workflow, conexao, usuario, empresa, projeto, etc.
    entidade_id INTEGER,                 -- ID do recurso afetado
    entidade_nome VARCHAR(255),          -- Nome do recurso (para referência mesmo após exclusão)
    id_usuario INTEGER NOT NULL,         -- Quem fez a ação
    nome_usuario VARCHAR(255),           -- Nome (snapshot para referência)
    nivel_acesso VARCHAR(30),            -- Nível do usuário no momento
    dados_anteriores JSONB DEFAULT '{}', -- Estado antes da alteração
    dados_novos JSONB DEFAULT '{}',      -- Estado após a alteração
    ip_address VARCHAR(45),
    user_agent TEXT,
    criado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_auditoria_acao ON tb_auditoria(acao);
CREATE INDEX IF NOT EXISTS idx_auditoria_entidade ON tb_auditoria(entidade);
CREATE INDEX IF NOT EXISTS idx_auditoria_usuario ON tb_auditoria(id_usuario);
CREATE INDEX IF NOT EXISTS idx_auditoria_criado_em ON tb_auditoria(criado_em);
CREATE INDEX IF NOT EXISTS idx_auditoria_entidade_id ON tb_auditoria(entidade, entidade_id);

-- =====================================================
-- 2. Tabela de Configurações do Sistema (key-value)
-- =====================================================
CREATE TABLE IF NOT EXISTS tb_configuracoes (
    chave VARCHAR(100) PRIMARY KEY,
    valor TEXT,
    grupo VARCHAR(50) NOT NULL DEFAULT 'geral',  -- geral, email, ldap, scheduler, seguranca, notificacoes
    descricao VARCHAR(255),
    atualizado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    atualizado_por INTEGER
);

CREATE INDEX IF NOT EXISTS idx_configuracoes_grupo ON tb_configuracoes(grupo);

-- Valores padrão
INSERT INTO tb_configuracoes (chave, valor, grupo, descricao) VALUES
    ('app_nome', 'DMC DataLoad', 'geral', 'Nome da aplicação'),
    ('app_timezone', 'America/Sao_Paulo', 'geral', 'Timezone padrão'),
    ('app_idioma', 'pt-BR', 'geral', 'Idioma padrão'),
    ('app_manutencao', '0', 'geral', 'Modo manutenção'),
    ('smtp_host', '', 'email', 'Servidor SMTP'),
    ('smtp_port', '587', 'email', 'Porta SMTP'),
    ('smtp_encryption', 'tls', 'email', 'Criptografia SMTP'),
    ('smtp_user', '', 'email', 'Usuário SMTP'),
    ('smtp_password', '', 'email', 'Senha SMTP'),
    ('smtp_from_email', '', 'email', 'E-mail remetente'),
    ('smtp_from_name', 'DMC DataLoad', 'email', 'Nome remetente'),
    ('notif_email_falha', '1', 'notificacoes', 'Enviar e-mail em falha de execução'),
    ('notif_webhook_ativo', '0', 'notificacoes', 'Ativar webhooks de notificação'),
    ('notif_webhook_url', '', 'notificacoes', 'URL do webhook padrão'),
    ('seguranca_timeout_sessao', '3600', 'seguranca', 'Timeout de sessão em segundos'),
    ('seguranca_tentativas_login', '5', 'seguranca', 'Tentativas de login antes de bloqueio'),
    ('seguranca_tempo_bloqueio', '900', 'seguranca', 'Tempo de bloqueio em segundos'),
    ('scheduler_intervalo', '60', 'scheduler', 'Intervalo de verificação em segundos'),
    ('scheduler_max_paralelo', '5', 'scheduler', 'Máximo de execuções paralelas'),
    ('scheduler_timeout', '3600', 'scheduler', 'Timeout de execução em segundos')
ON CONFLICT (chave) DO NOTHING;

-- =====================================================
-- 3. Tabela de Webhooks
-- =====================================================
CREATE TABLE IF NOT EXISTS tb_webhooks (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    eventos TEXT[] NOT NULL DEFAULT '{}',        -- Array de eventos: falha_execucao, sucesso_execucao, etc.
    headers JSONB DEFAULT '{}',                  -- Headers customizados
    ativo BOOLEAN DEFAULT true,
    secret VARCHAR(255),                         -- Secret para assinatura HMAC
    criado_por INTEGER,
    criado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
