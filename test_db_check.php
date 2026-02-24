<?php
/**
 * Script para verificar tabelas do banco de dados
 */

try {
    $db = new PDO('pgsql:host=localhost;port=5433;dbname=db_dmc_dataload', 'postgres', 'dmc2023@');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco com sucesso!\n\n";
    
    // Listar tabelas workflow e API
    $stmt = $db->query("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        AND (tablename LIKE 'tb_workflow%' OR tablename LIKE 'tb_api%' OR tablename LIKE 'tb_evento%' OR tablename LIKE 'tb_valor%')
        ORDER BY tablename
    ");
    
    echo "📋 Tabelas existentes:\n";
    $tabelas = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tabelas[] = $row['tablename'];
        echo "  ✓ " . $row['tablename'] . "\n";
    }
    
    if (count($tabelas) === 0) {
        echo "\n⚠️  Nenhuma tabela encontrada. Execute a migration 004!\n";
    } else {
        echo "\n📊 Total: " . count($tabelas) . " tabelas\n";
        
        // Verificar se tem dados
        echo "\n📈 Dados existentes:\n";
        
        if (in_array('tb_api_externas', $tabelas)) {
            $stmt = $db->query("SELECT COUNT(*) as total FROM tb_api_externas");
            $count = $stmt->fetchColumn();
            echo "  • APIs Externas: $count\n";
        }
        
        if (in_array('tb_eventos_api', $tabelas)) {
            $stmt = $db->query("SELECT COUNT(*) as total FROM tb_eventos_api");
            $count = $stmt->fetchColumn();
            echo "  • Eventos API: $count\n";
        }
        
        if (in_array('tb_workflows', $tabelas)) {
            $stmt = $db->query("SELECT COUNT(*) as total FROM tb_workflows");
            $count = $stmt->fetchColumn();
            echo "  • Workflows: $count\n";
        }
        
        if (in_array('tb_workflow_execucoes', $tabelas)) {
            $stmt = $db->query("SELECT COUNT(*) as total FROM tb_workflow_execucoes");
            $count = $stmt->fetchColumn();
            echo "  • Execuções: $count\n";
        }
    }
    
} catch(Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
