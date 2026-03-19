<?php
require_once __DIR__ . '/vendor/autoload.php';

App\Core\Database::loadEnv(__DIR__ . '/');
$db = App\Core\Database::getConexao();

$sql = file_get_contents(__DIR__ . '/migrations/007_rbac_empresas_projetos.sql');

try {
    $db->exec($sql);
    echo "Migration 007 executada com sucesso!\n";
    
    // Verificar tabelas criadas
    $tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename LIKE 'tb_%' ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas: " . implode(', ', $tables) . "\n";
    
    // Verificar nivel_acesso atualizado
    $users = $db->query('SELECT id, nome_usuario, nivel_acesso FROM tb_usuarios ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "User {$u['id']}: {$u['nome_usuario']} -> {$u['nivel_acesso']}\n";
    }
    
    // Verificar novas tabelas
    $newTables = ['tb_empresas', 'tb_projetos', 'tb_usuario_empresas', 'tb_usuario_projetos', 'tb_recurso_empresas', 'tb_recurso_projetos', 'tb_compartilhamentos'];
    foreach ($newTables as $t) {
        $exists = $db->query("SELECT EXISTS (SELECT 1 FROM pg_tables WHERE tablename = '$t')")->fetchColumn();
        echo "$t: " . ($exists ? 'OK' : 'MISSING') . "\n";
    }
    
    // Verificar coluna criado_por em tb_perfis_conexao
    $cols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tb_perfis_conexao' AND column_name = 'criado_por'")->fetchColumn();
    echo "tb_perfis_conexao.criado_por: " . ($cols ? 'OK' : 'MISSING') . "\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
