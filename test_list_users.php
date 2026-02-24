<?php
try {
    $db = new PDO('pgsql:host=localhost;port=5433;dbname=db_dmc_dataload', 'postgres', 'dmc2023@');
    $stmt = $db->query('SELECT COUNT(*) FROM tb_usuarios');
    echo 'Usuários: ' . $stmt->fetchColumn() . PHP_EOL;
    
    $stmt = $db->query('SELECT id, nome_usuario, email FROM tb_usuarios LIMIT 5');
    echo "\nListagem:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '  - ID: ' . $row['id'] . ', User: ' . $row['nome_usuario'] . ', Email: ' . $row['email'] . PHP_EOL;
    }
    
    // Verificar se existe admin
    $stmt = $db->query("SELECT id, nome_usuario FROM tb_usuarios WHERE nome_usuario = 'admin'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        echo "\n✅ Usuário 'admin' existe (ID: {$admin['id']})\n";
    } else {
        echo "\n⚠️  Usuário 'admin' não encontrado. Criando...\n";
        // Criar usuário admin
        $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO tb_usuarios (nome_usuario, email, senha) VALUES (?, ?, ?)');
        $stmt->execute(['admin', 'admin@dmc.local', $senhaHash]);
        echo "✅ Usuário 'admin' criado com senha 'admin123'\n";
    }
} catch(Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}

