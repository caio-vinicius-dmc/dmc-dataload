<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Controladores\ConexoesController;
use App\Servicos\ServicoExecucao;

// Definir chave estável para este run
$key = base64_encode(random_bytes(32));
putenv('ENCRYPTION_KEY=' . $key);
Database::loadEnv(__DIR__ . '/..');

echo "ENCRYPTION_KEY temporária para este run: $key\n";

$c = new ConexoesController();

$data = [
    'nome_conexao' => 'run_once_local',
    'tipo_banco' => 'postgres',
    'host' => getenv('DB_HOST') ?: 'localhost',
    'porta' => getenv('DB_PORT') ?: 5433,
    'nome_banco' => getenv('DB_DATABASE') ?: 'db_dmc_dataload',
    'usuario' => getenv('DB_USERNAME') ?: 'postgres',
    'senha' => getenv('DB_PASSWORD') ?: 'dmc2023@',
    'parametros_extras' => new stdClass()
];

$res = $c->salvar($data);
print_r($res);

// Criar rotina e bloco e executar imediatamente
$pdo = Database::getConexao();
$perfilId = $res['id'];
$stmt = $pdo->prepare('INSERT INTO tb_rotinas (nome, descricao, id_conexao, id_usuario_criador) VALUES (?, ?, ?, ?) RETURNING id');
$stmt->execute(['rotina_exec_run_once', 'Rotina exec run once', $perfilId, null]);
$rotinaId = $stmt->fetchColumn();
echo "Rotina criada: $rotinaId\n";

$b = $pdo->prepare('INSERT INTO tb_blocos_rotina (id_rotina, codigo_bloco, ordem, script_sql, tipo_bloco) VALUES (?, ?, ?, ?, ?) RETURNING id');
$b->execute([$rotinaId, 'B_RUN', 1, "SELECT now() as agora, 'teste-run' as texto", 'SELECT']);
$blId = $b->fetchColumn();
echo "Bloco criado: $blId\n";

$svc = new ServicoExecucao();
$out = $svc->executarRotina((int)$rotinaId);
print_r($out);
