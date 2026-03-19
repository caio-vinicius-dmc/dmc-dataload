-- ============================================================
-- Migration 007: RBAC - Empresas, Projetos e Permissões
-- DMC DataLoad - Sistema de Controle de Acesso por Papéis
-- ============================================================

BEGIN;

-- ============================================================
-- 1. Atualizar nivel_acesso dos usuarios existentes
-- ============================================================

-- Converter 'admin' do usuario id=1 para 'super_admin' (único super admin)
UPDATE tb_usuarios SET nivel_acesso = 'super_admin' WHERE id = 1;

-- Converter 'user' existentes para 'desenvolvedor'
UPDATE tb_usuarios SET nivel_acesso = 'desenvolvedor' WHERE nivel_acesso = 'user';

-- Garantir que 'operador' permanece como 'operador' (já está correto)

-- ============================================================
-- 2. Tabela de Empresas
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_empresas (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    descricao TEXT,
    ativa BOOLEAN DEFAULT TRUE NOT NULL,
    criado_por INTEGER REFERENCES tb_usuarios(id) ON DELETE SET NULL,
    data_criacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    data_atualizacao TIMESTAMPTZ DEFAULT NOW() NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_empresas_ativa ON tb_empresas(ativa);

-- ============================================================
-- 3. Tabela de Projetos (dentro de empresas)
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_projetos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    id_empresa INTEGER NOT NULL REFERENCES tb_empresas(id) ON DELETE CASCADE,
    ativo BOOLEAN DEFAULT TRUE NOT NULL,
    criado_por INTEGER REFERENCES tb_usuarios(id) ON DELETE SET NULL,
    data_criacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    data_atualizacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    UNIQUE(nome, id_empresa)
);

CREATE INDEX IF NOT EXISTS idx_projetos_empresa ON tb_projetos(id_empresa);
CREATE INDEX IF NOT EXISTS idx_projetos_criador ON tb_projetos(criado_por);

-- ============================================================
-- 4. Associação Usuário <-> Empresa (M:N)
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_usuario_empresas (
    id SERIAL PRIMARY KEY,
    id_usuario INTEGER NOT NULL REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    id_empresa INTEGER NOT NULL REFERENCES tb_empresas(id) ON DELETE CASCADE,
    data_associacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    UNIQUE(id_usuario, id_empresa)
);

CREATE INDEX IF NOT EXISTS idx_ue_usuario ON tb_usuario_empresas(id_usuario);
CREATE INDEX IF NOT EXISTS idx_ue_empresa ON tb_usuario_empresas(id_empresa);

-- ============================================================
-- 5. Associação Usuário <-> Projeto (M:N)
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_usuario_projetos (
    id SERIAL PRIMARY KEY,
    id_usuario INTEGER NOT NULL REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    id_projeto INTEGER NOT NULL REFERENCES tb_projetos(id) ON DELETE CASCADE,
    data_associacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    UNIQUE(id_usuario, id_projeto)
);

CREATE INDEX IF NOT EXISTS idx_up_usuario ON tb_usuario_projetos(id_usuario);
CREATE INDEX IF NOT EXISTS idx_up_projeto ON tb_usuario_projetos(id_projeto);

-- ============================================================
-- 6. Associação Recurso <-> Empresa (M:N polimórfica)
-- Tipos: conexao, rotina, api, workflow, pipeline, agendamento, evento_api
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_recurso_empresas (
    id SERIAL PRIMARY KEY,
    tipo_recurso VARCHAR(50) NOT NULL,
    id_recurso INTEGER NOT NULL,
    id_empresa INTEGER NOT NULL REFERENCES tb_empresas(id) ON DELETE CASCADE,
    data_associacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    UNIQUE(tipo_recurso, id_recurso, id_empresa)
);

CREATE INDEX IF NOT EXISTS idx_re_tipo_recurso ON tb_recurso_empresas(tipo_recurso, id_recurso);
CREATE INDEX IF NOT EXISTS idx_re_empresa ON tb_recurso_empresas(id_empresa);

-- ============================================================
-- 7. Associação Recurso <-> Projeto (M:N polimórfica)
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_recurso_projetos (
    id SERIAL PRIMARY KEY,
    tipo_recurso VARCHAR(50) NOT NULL,
    id_recurso INTEGER NOT NULL,
    id_projeto INTEGER NOT NULL REFERENCES tb_projetos(id) ON DELETE CASCADE,
    data_associacao TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    UNIQUE(tipo_recurso, id_recurso, id_projeto)
);

CREATE INDEX IF NOT EXISTS idx_rp_tipo_recurso ON tb_recurso_projetos(tipo_recurso, id_recurso);
CREATE INDEX IF NOT EXISTS idx_rp_projeto ON tb_recurso_projetos(id_projeto);

-- ============================================================
-- 8. Compartilhamento de Recursos entre Desenvolvedores
-- Permite compartilhar recursos específicos ou todos de um tipo
-- ============================================================
CREATE TABLE IF NOT EXISTS tb_compartilhamentos (
    id SERIAL PRIMARY KEY,
    tipo_recurso VARCHAR(50) NOT NULL,
    id_recurso INTEGER,  -- NULL = compartilha TODOS daquele tipo
    id_usuario_dono INTEGER NOT NULL REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    id_usuario_destino INTEGER NOT NULL REFERENCES tb_usuarios(id) ON DELETE CASCADE,
    permissao VARCHAR(20) NOT NULL DEFAULT 'ver',  -- 'ver' (específico) ou 'ver_tudo' (todos do tipo)
    data_compartilhamento TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    UNIQUE(tipo_recurso, id_recurso, id_usuario_dono, id_usuario_destino)
);

CREATE INDEX IF NOT EXISTS idx_comp_dono ON tb_compartilhamentos(id_usuario_dono);
CREATE INDEX IF NOT EXISTS idx_comp_destino ON tb_compartilhamentos(id_usuario_destino);
CREATE INDEX IF NOT EXISTS idx_comp_recurso ON tb_compartilhamentos(tipo_recurso, id_recurso);

-- ============================================================
-- 9. Adicionar coluna criado_por nas tabelas que não possuem
-- ============================================================

-- tb_perfis_conexao
DO $$ BEGIN
    ALTER TABLE tb_perfis_conexao ADD COLUMN criado_por INTEGER REFERENCES tb_usuarios(id) ON DELETE SET NULL;
EXCEPTION WHEN duplicate_column THEN NULL;
END $$;

-- tb_eventos_api  
DO $$ BEGIN
    ALTER TABLE tb_eventos_api ADD COLUMN criado_por INTEGER REFERENCES tb_usuarios(id) ON DELETE SET NULL;
EXCEPTION WHEN duplicate_column THEN NULL;
END $$;

-- schedules (tabela legada)
DO $$ BEGIN
    ALTER TABLE schedules ADD COLUMN criado_por INTEGER REFERENCES tb_usuarios(id) ON DELETE SET NULL;
EXCEPTION WHEN duplicate_column THEN NULL;
END $$;

-- ============================================================
-- 10. Atribuir dados existentes ao super_admin (id=1)
-- ============================================================

-- Atribuir conexões sem dono ao admin
UPDATE tb_perfis_conexao SET criado_por = 1 WHERE criado_por IS NULL;

-- Atribuir rotinas sem dono ao admin
UPDATE tb_rotinas SET id_usuario_criador = 1 WHERE id_usuario_criador IS NULL;

-- Atribuir workflows sem dono ao admin
UPDATE tb_workflows SET criado_por = 1 WHERE criado_por IS NULL;

-- Atribuir pipelines sem dono ao admin
UPDATE tb_pipelines SET criado_por = 1 WHERE criado_por IS NULL;

-- Atribuir APIs externas sem dono ao admin
UPDATE tb_api_externas SET criado_por = 1 WHERE criado_por IS NULL;

-- Atribuir eventos API sem dono ao admin
UPDATE tb_eventos_api SET criado_por = 1 WHERE criado_por IS NULL;

-- Atribuir schedules sem dono ao admin
UPDATE schedules SET criado_por = 1 WHERE criado_por IS NULL;

COMMIT;
