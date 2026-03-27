<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Core\Database;
use App\Utils\Crypto;
use PDO;

Database::loadEnv(__DIR__ . '/');
$db = Database::getConexao();
$stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
$stmt->execute([5]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

$key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
$senha = Crypto::decrypt($config['senha_encriptada'], $key);

$pdo = new PDO("pgsql:host={$config['host']};port={$config['porta']};dbname={$config['nome_banco']}", $config['usuario'], $senha);
echo "Conectado PG 8.4 como {$config['usuario']}\n";
echo "Executando SELECT pg_sleep(3)...\n";
$pdo->query("SELECT pg_sleep(3)");
echo "Finalizado\n";
