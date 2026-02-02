<?php
namespace App\Controladores;

use App\Core\Database;
use App\Utils\Crypto;
use PDO;
use Exception;

class SqlEditorController
{
    /**
     * Conectar a um banco de dados
     */
    public function connect(int $conexaoId): array
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conexao) {
                return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
            }
            
            // Testar conexão
            try {
                $testDb = $this->criarConexao($conexao);
                $testDb->query('SELECT 1');
                
                return [
                    'sucesso' => true,
                    'conexao' => [
                        'id' => $conexao['id'],
                        'nome_conexao' => $conexao['nome_conexao'],
                        'tipo_banco' => $conexao['tipo_banco'],
                        'host' => $conexao['host'],
                        'porta' => $conexao['porta'],
                        'nome_banco' => $conexao['nome_banco']
                    ]
                ];
            } catch (Exception $e) {
                return ['sucesso' => false, 'mensagem' => 'Erro ao conectar: ' . $e->getMessage()];
            }
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    /**
     * Listar objetos do banco (schemas, tabelas, views, functions)
     */
    public function getObjects(int $conexaoId): array
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conexao) {
                return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
            }
            
            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];
            
            $objetos = [
                'schemas' => [],
                'tabelas' => [],
                'views' => [],
                'funcoes' => []
            ];
            
            if ($tipo === 'postgres') {
                // Schemas
                $schemas = $targetDb->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('pg_catalog', 'information_schema') ORDER BY schema_name")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['schemas'] = $schemas;
                
                // Tabelas
                $tabelas = $targetDb->query("
                    SELECT 
                        table_schema || '.' || table_name as name,
                        table_schema as schema,
                        table_name
                    FROM information_schema.tables 
                    WHERE table_schema NOT IN ('pg_catalog', 'information_schema')
                    AND table_type = 'BASE TABLE'
                    ORDER BY table_schema, table_name
                ")->fetchAll(PDO::FETCH_ASSOC);
                $objetos['tabelas'] = $tabelas;
                
                // Views
                $views = $targetDb->query("
                    SELECT table_schema || '.' || table_name as name
                    FROM information_schema.views 
                    WHERE table_schema NOT IN ('pg_catalog', 'information_schema')
                    ORDER BY table_schema, table_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['views'] = $views;
                
                // Functions
                $funcoes = $targetDb->query("
                    SELECT routine_schema || '.' || routine_name as name
                    FROM information_schema.routines 
                    WHERE routine_schema NOT IN ('pg_catalog', 'information_schema')
                    ORDER BY routine_schema, routine_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['funcoes'] = $funcoes;
                
            } elseif ($tipo === 'mysql') {
                // MySQL
                $tabelas = $targetDb->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['tabelas'] = array_map(function($t) { return ['name' => $t]; }, $tabelas);
                
                // Views
                $views = $targetDb->query("
                    SELECT table_name 
                    FROM information_schema.views 
                    WHERE table_schema = DATABASE()
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['views'] = $views;
                
                // Functions
                $funcoes = $targetDb->query("
                    SELECT routine_name 
                    FROM information_schema.routines 
                    WHERE routine_schema = DATABASE()
                    AND routine_type = 'FUNCTION'
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['funcoes'] = $funcoes;
                
            } elseif ($tipo === 'sqlserver') {
                // SQL Server
                $schemas = $targetDb->query("SELECT name FROM sys.schemas WHERE name NOT IN ('sys', 'INFORMATION_SCHEMA') ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['schemas'] = $schemas;
                
                $tabelas = $targetDb->query("
                    SELECT 
                        SCHEMA_NAME(schema_id) + '.' + name as name,
                        SCHEMA_NAME(schema_id) as schema,
                        name as table_name
                    FROM sys.tables 
                    ORDER BY schema_id, name
                ")->fetchAll(PDO::FETCH_ASSOC);
                $objetos['tabelas'] = $tabelas;
                
                $views = $targetDb->query("
                    SELECT SCHEMA_NAME(schema_id) + '.' + name as name
                    FROM sys.views 
                    ORDER BY schema_id, name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['views'] = $views;
            }
            
            return ['sucesso' => true, 'objetos' => $objetos];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    /**
     * Executar query SQL
     */
    public function execute(array $data): array
    {
        try {
            $conexaoId = $data['conexao_id'] ?? null;
            $sql = $data['sql'] ?? '';
            
            if (!$conexaoId || !$sql) {
                return ['sucesso' => false, 'erro' => 'Parâmetros inválidos'];
            }
            
            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conexao) {
                return ['sucesso' => false, 'erro' => 'Conexão não encontrada'];
            }
            
            $targetDb = $this->criarConexao($conexao);
            
            // Detectar tipo de query
            $sqlUpper = strtoupper(trim($sql));
            $isSelect = strpos($sqlUpper, 'SELECT') === 0 
                     || strpos($sqlUpper, 'SHOW') === 0
                     || strpos($sqlUpper, 'DESCRIBE') === 0
                     || strpos($sqlUpper, 'EXPLAIN') === 0;
            
            if ($isSelect) {
                // Query de seleção
                $stmt = $targetDb->query($sql);
                $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $colunas = [];
                if (count($dados) > 0) {
                    $colunas = array_keys($dados[0]);
                }
                
                return [
                    'sucesso' => true,
                    'dados' => $dados,
                    'colunas' => $colunas,
                    'linhas' => count($dados)
                ];
            } else {
                // Query de modificação
                $stmt = $targetDb->prepare($sql);
                $stmt->execute();
                $linhasAfetadas = $stmt->rowCount();
                
                return [
                    'sucesso' => true,
                    'linhas_afetadas' => $linhasAfetadas,
                    'mensagem' => "Query executada com sucesso. Linhas afetadas: $linhasAfetadas"
                ];
            }
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Criar conexão PDO com banco alvo
     */
    private function criarConexao(array $config): PDO
    {
        $tipo = $config['tipo_banco'];
        $host = $config['host'];
        $porta = $config['porta'];
        $database = $config['nome_banco'];
        $usuario = $config['usuario'];
        $senhaEncriptada = $config['senha_encriptada'] ?? '';
        
        // Descriptografar senha usando a mesma classe Crypto do sistema
        $senha = '';
        if ($senhaEncriptada) {
            $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
            if (!$key) {
                throw new Exception('ENCRYPTION_KEY não configurada no sistema');
            }
            
            try {
                $senha = Crypto::decrypt($senhaEncriptada, $key);
                if (empty($senha)) {
                    throw new Exception('Falha ao descriptografar senha');
                }
            } catch (Exception $e) {
                throw new Exception('Erro ao descriptografar senha: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Senha não configurada para esta conexão');
        }
        
        switch ($tipo) {
            case 'postgres':
                $dsn = "pgsql:host=$host;port=$porta;dbname=$database";
                break;
            case 'mysql':
                $dsn = "mysql:host=$host;port=$porta;dbname=$database;charset=utf8mb4";
                break;
            case 'sqlserver':
                $dsn = "sqlsrv:Server=$host,$porta;Database=$database";
                break;
            case 'oracle':
                $dsn = "oci:dbname=//$host:$porta/$database";
                break;
            default:
                throw new Exception("Tipo de banco não suportado: $tipo");
        }
        
        return new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
}
