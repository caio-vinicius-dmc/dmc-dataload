<?php
try {
    $db = new PDO('pgsql:host=localhost;port=5433;dbname=db_dmc_dataload', 'postgres', 'dmc2023@');
    
    // Listar colunas da tabela
    $stmt = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'tb_usuarios' ORDER BY ordinal_position");
    echo "Colunas da tabela tb_usuarios:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['column_name']} ({$row['data_type']})\n";
    }
    
    // Listar usuários
    $stmt = $db->query('SELECT * FROM tb_usuarios LIMIT 3');
    echo "\nUsuários cadastrados:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch(Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
