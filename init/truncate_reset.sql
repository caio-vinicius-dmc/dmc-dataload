--BEGIN;

-- ============================================================
-- 1. LOGS, AUDITORIA E HISTÓRICO
-- ============================================================
TRUNCATE TABLE tb_auditoria                CASCADE;
TRUNCATE TABLE tb_auditoria_rotina         CASCADE;
TRUNCATE TABLE tb_logs_execucao            CASCADE;
TRUNCATE TABLE tb_logs_sistema             CASCADE;
TRUNCATE TABLE tb_metricas_sistema         CASCADE;

-- ============================================================
-- 2. WORKFLOWS (nós e execuções antes das definições)
-- ============================================================
TRUNCATE TABLE tb_workflow_node_execucoes  CASCADE;
TRUNCATE TABLE tb_workflow_execucoes       CASCADE;
TRUNCATE TABLE tb_workflow_edges           CASCADE;
TRUNCATE TABLE tb_workflow_nodes           CASCADE;
TRUNCATE TABLE tb_workflows                CASCADE;

-- ============================================================
-- 3. PIPELINES
-- ============================================================
TRUNCATE TABLE tb_pipeline_execucoes       CASCADE;
TRUNCATE TABLE tb_pipelines                CASCADE;

select * from tb_pipelines;


-- ============================================================
-- 4. ROTINAS (blocos e filas antes das rotinas)
-- ============================================================
TRUNCATE TABLE tb_fila_execucao            CASCADE;
TRUNCATE TABLE tb_blocos_rotina            CASCADE;
TRUNCATE TABLE tb_rotinas                  CASCADE;

-- ============================================================
-- 5. APIs EXTERNAS E MONITORAMENTO
-- ============================================================
TRUNCATE TABLE tb_valores_capturados       CASCADE;
TRUNCATE TABLE tb_eventos_api              CASCADE;
TRUNCATE TABLE tb_api_externas             CASCADE;

-- ============================================================
-- 6. NOTIFICAÇÕES E WEBHOOKS
-- ============================================================
TRUNCATE TABLE tb_notificacoes             CASCADE;
TRUNCATE TABLE tb_webhooks                 CASCADE;
TRUNCATE TABLE tb_canais_notificacao       CASCADE;

-- ============================================================
-- 7. ARQUIVOS E BACKUPS
-- ============================================================
TRUNCATE TABLE tb_arquivos                 CASCADE;
TRUNCATE TABLE tb_backups                  CASCADE;

-- ============================================================
-- 8. CONEXÕES
-- ============================================================
TRUNCATE TABLE tb_perfis_conexao           CASCADE;

-- Tabela legada (se existir)
DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'connections') THEN
        EXECUTE 'TRUNCATE TABLE connections CASCADE';
    END IF;
END $$;

-- ============================================================
-- 9. COMPARTILHAMENTOS E VÍNCULOS DE RECURSOS
-- ============================================================
TRUNCATE TABLE tb_compartilhamentos        CASCADE;
TRUNCATE TABLE tb_recurso_empresas         CASCADE;
TRUNCATE TABLE tb_recurso_projetos         CASCADE;

-- ============================================================
-- 10. VÍNCULOS USUÁRIO <-> EMPRESA/PROJETO
--     (remove todos, depois recria só o do super_admin)
-- ============================================================
TRUNCATE TABLE tb_usuario_empresas         CASCADE;
TRUNCATE TABLE tb_usuario_projetos         CASCADE;

-- ============================================================
-- 11. PROJETOS E EMPRESAS
-- ============================================================
TRUNCATE TABLE tb_projetos                 CASCADE;
TRUNCATE TABLE tb_empresas                 CASCADE;

-- ============================================================
-- 12. USUÁRIOS (remove todos exceto o super_admin)
-- ============================================================
TRUNCATE TABLE tb_password_resets          CASCADE;
TRUNCATE TABLE tb_rate_limits              CASCADE;

DELETE FROM tb_usuarios WHERE id != 1;

-- ============================================================
-- 13. TABELAS LEGADAS (se existirem)
-- ============================================================
DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'schedules') THEN
        EXECUTE 'TRUNCATE TABLE schedules CASCADE';
    END IF;
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'logs_sistema') THEN
        EXECUTE 'TRUNCATE TABLE logs_sistema CASCADE';
    END IF;
END $$;

-- ============================================================
-- 14. WORKER HEARTBEAT
-- ============================================================
TRUNCATE TABLE tb_worker_heartbeat         CASCADE;

-- ============================================================
-- 15. RESET DE SEQUENCES (IDs voltam a 1)
-- ============================================================
DO $$
DECLARE
    seq RECORD;
BEGIN
    FOR seq IN
        SELECT c.relname AS seqname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relkind = 'S'
          AND n.nspname = 'public'
          AND c.relname NOT LIKE 'tb_usuarios_id_seq'
    LOOP
        EXECUTE format('ALTER SEQUENCE %I RESTART WITH 1', seq.seqname);
    END LOOP;
END $$;

-- Sequence do usuário: próximo id = 2 (pois id=1 é o super_admin)
DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM pg_class WHERE relname = 'tb_usuarios_id_seq' AND relkind = 'S') THEN
        PERFORM setval('tb_usuarios_id_seq', (SELECT MAX(id) FROM tb_usuarios));
    END IF;
END $$;

-- ============================================================
-- 16. GARANTIR SUPER ADMIN ÍNTEGRO
-- ============================================================
UPDATE tb_usuarios
SET    nivel_acesso = 'super_admin',
       ativo        = true
WHERE  id = 1;

--COMMIT;

-- ============================================================
-- VERIFICAÇÃO PÓS-RESET
-- ============================================================
-- Execute após o script para confirmar:
--
--   SELECT 'tb_usuarios' AS tabela, count(*) FROM tb_usuarios
--   UNION ALL SELECT 'tb_rotinas',           count(*) FROM tb_rotinas
--   UNION ALL SELECT 'tb_perfis_conexao',    count(*) FROM tb_perfis_conexao
--   UNION ALL SELECT 'tb_pipelines',         count(*) FROM tb_pipelines
--   UNION ALL SELECT 'tb_workflows',         count(*) FROM tb_workflows
--   UNION ALL SELECT 'tb_api_externas',      count(*) FROM tb_api_externas
--   UNION ALL SELECT 'tb_auditoria',         count(*) FROM tb_auditoria
--   UNION ALL SELECT 'tb_logs_execucao',     count(*) FROM tb_logs_execucao
--   UNION ALL SELECT 'tb_empresas',          count(*) FROM tb_empresas
--   UNION ALL SELECT 'tb_projetos',          count(*) FROM tb_projetos
--   ORDER BY 1;
--
-- Resultado esperado: tb_usuarios = 1, todo o resto = 0
-- ============================================================
