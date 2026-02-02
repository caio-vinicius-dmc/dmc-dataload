<?php
/**
 * Migração para adicionar campos de agendamento e melhorar logs
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

Database::loadEnv(__DIR__ . '/../');

try {
    $db = Database::getConexao();
    
    echo "=== Migração de Scheduler e Logs ===\n\n";
    
    // 1. Adicionar campos na tb_rotinas
    echo "1. Adicionando campos de agendamento em tb_rotinas...\n";
    
    $campos = [
        "agendamento_cron" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS agendamento_cron TEXT",
        "ativa" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS ativa BOOLEAN DEFAULT false",
        "proxima_execucao" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS proxima_execucao TIMESTAMPTZ",
        "ultima_execucao" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS ultima_execucao TIMESTAMPTZ",
        "tentativas_falha" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS tentativas_falha INTEGER DEFAULT 0",
        "max_tentativas" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS max_tentativas INTEGER DEFAULT 3",
        "ultima_verificacao" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS ultima_verificacao TIMESTAMPTZ",
        "webhook_sucesso" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS webhook_sucesso TEXT",
        "webhook_falha" => "ALTER TABLE tb_rotinas ADD COLUMN IF NOT EXISTS webhook_falha TEXT"
    ];
    
    foreach ($campos as $nome => $sql) {
        try {
            $db->exec($sql);
            echo "   ✓ Campo '{$nome}' adicionado/verificado\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "   ✗ Erro no campo '{$nome}': " . $e->getMessage() . "\n";
            } else {
                echo "   - Campo '{$nome}' já existe\n";
            }
        }
    }
    
    // 2. Adicionar campos na tb_logs_execucao
    echo "\n2. Adicionando campos em tb_logs_execucao...\n";
    
    $camposLog = [
        "id_usuario" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS id_usuario INTEGER",
        "duracao_ms" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS duracao_ms INTEGER",
        "status" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'pendente'",
        "blocos_executados" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS blocos_executados INTEGER DEFAULT 0",
        "blocos_sucesso" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS blocos_sucesso INTEGER DEFAULT 0",
        "blocos_falha" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS blocos_falha INTEGER DEFAULT 0",
        "caminho_csv" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS caminho_csv TEXT",
        "detalhes_json" => "ALTER TABLE tb_logs_execucao ADD COLUMN IF NOT EXISTS detalhes_json JSONB"
    ];
    
    foreach ($camposLog as $nome => $sql) {
        try {
            $db->exec($sql);
            echo "   ✓ Campo '{$nome}' adicionado/verificado\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "   ✗ Erro no campo '{$nome}': " . $e->getMessage() . "\n";
            } else {
                echo "   - Campo '{$nome}' já existe\n";
            }
        }
    }
    
    // 3. Criar índices
    echo "\n3. Criando índices de performance...\n";
    
    $indices = [
        "idx_rotinas_ativa_proxima" => "CREATE INDEX IF NOT EXISTS idx_rotinas_ativa_proxima ON tb_rotinas(ativa, proxima_execucao) WHERE ativa = true",
        "idx_logs_status" => "CREATE INDEX IF NOT EXISTS idx_logs_status ON tb_logs_execucao(status)",
        "idx_logs_data_inicio" => "CREATE INDEX IF NOT EXISTS idx_logs_data_inicio ON tb_logs_execucao(data_inicio DESC)",
        "idx_logs_rotina_data" => "CREATE INDEX IF NOT EXISTS idx_logs_rotina_data ON tb_logs_execucao(id_rotina, data_inicio DESC)"
    ];
    
    foreach ($indices as $nome => $sql) {
        try {
            $db->exec($sql);
            echo "   ✓ Índice '{$nome}' criado/verificado\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "   ✗ Erro no índice '{$nome}': " . $e->getMessage() . "\n";
            } else {
                echo "   - Índice '{$nome}' já existe\n";
            }
        }
    }
    
    // 4. Criar diretórios necessários
    echo "\n4. Criando diretórios necessários...\n";
    
    $dirs = [
        __DIR__ . '/../storage',
        __DIR__ . '/../storage/logs',
        __DIR__ . '/../storage/csv'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
            echo "   ✓ Diretório criado: " . basename($dir) . "\n";
        } else {
            echo "   - Diretório já existe: " . basename($dir) . "\n";
        }
    }
    
    echo "\n=== Migração concluída com sucesso! ===\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
