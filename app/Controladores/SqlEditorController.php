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
                
                // Query de teste varia por tipo de banco
                $testQuery = match($conexao['tipo_banco']) {
                    'oracle' => 'SELECT 1 FROM DUAL',
                    'sqlserver' => 'SELECT 1',
                    default => 'SELECT 1'
                };
                
                $testDb->query($testQuery);
                
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
     * Listar contadores de objetos do banco (para hierarquia com lazy loading)
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
            
            $counts = [
                'schemas' => []
            ];
            $counts = [
                'schemas' => []
            ];
            
            if ($tipo === 'postgres') {
                // Contar objetos por schema
                $result = $targetDb->query("
                    SELECT 
                        s.schema_name,
                        COUNT(DISTINCT CASE WHEN t.table_type = 'BASE TABLE' THEN t.table_name END) as tables_count,
                        COUNT(DISTINCT CASE WHEN v.table_name IS NOT NULL THEN v.table_name END) as views_count,
                        COUNT(DISTINCT r.routine_name) as functions_count
                    FROM information_schema.schemata s
                    LEFT JOIN information_schema.tables t ON s.schema_name = t.table_schema AND t.table_type = 'BASE TABLE'
                    LEFT JOIN information_schema.views v ON s.schema_name = v.table_schema
                    LEFT JOIN information_schema.routines r ON s.schema_name = r.routine_schema
                    WHERE s.schema_name NOT IN ('pg_catalog', 'information_schema')
                    GROUP BY s.schema_name
                    ORDER BY s.schema_name
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $counts['schemas'][] = [
                        'name' => $row['schema_name'],
                        'tables' => (int)$row['tables_count'],
                        'views' => (int)$row['views_count'],
                        'functions' => (int)$row['functions_count']
                    ];
                }
                
            } elseif ($tipo === 'mysql') {
                // MySQL não tem schemas separados, usar database atual
                $dbName = $targetDb->query("SELECT DATABASE()")->fetchColumn();
                
                $tables = $targetDb->query("
                    SELECT COUNT(*) FROM information_schema.tables 
                    WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'
                ")->fetchColumn();
                
                $views = $targetDb->query("
                    SELECT COUNT(*) FROM information_schema.views 
                    WHERE table_schema = DATABASE()
                ")->fetchColumn();
                
                $functions = $targetDb->query("
                    SELECT COUNT(*) FROM information_schema.routines 
                    WHERE routine_schema = DATABASE() AND routine_type = 'FUNCTION'
                ")->fetchColumn();
                
                $procedures = $targetDb->query("
                    SELECT COUNT(*) FROM information_schema.routines 
                    WHERE routine_schema = DATABASE() AND routine_type = 'PROCEDURE'
                ")->fetchColumn();
                
                $counts['schemas'][] = [
                    'name' => $dbName,
                    'tables' => (int)$tables,
                    'views' => (int)$views,
                    'functions' => (int)$functions,
                    'procedures' => (int)$procedures
                ];
                
            } elseif ($tipo === 'oracle') {
                // Oracle - Versão rápida: apenas contar tabelas e views
                // Functions, procedures e packages mostram "..." e são carregados sob demanda
                $excludeOwners = "'SYS','SYSTEM','OUTLN','DBSNMP','APPQOSSYS','WMSYS','EXFSYS','CTXSYS','XDB','ANONYMOUS','ORDSYS','ORDDATA','MDSYS','OLAPSYS'";
                
                // 1. Buscar owners com tables
                $tables = $targetDb->query("
                    SELECT owner, COUNT(*) as cnt
                    FROM all_tables 
                    WHERE owner NOT IN ({$excludeOwners})
                    GROUP BY owner
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                // 2. Buscar owners com views
                $views = $targetDb->query("
                    SELECT owner, COUNT(*) as cnt
                    FROM all_views 
                    WHERE owner NOT IN ({$excludeOwners})
                    GROUP BY owner
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                // Montar array indexed por owner
                $ownerData = [];
                foreach ($tables as $row) {
                    $owner = $row['OWNER'] ?? $row['owner'] ?? '';
                    $ownerData[$owner] = [
                        'name' => $owner,
                        'tables' => (int)($row['CNT'] ?? $row['cnt'] ?? 0),
                        'views' => 0,
                        'functions' => '?',
                        'procedures' => '?',
                        'packages' => '?'
                    ];
                }
                
                foreach ($views as $row) {
                    $owner = $row['OWNER'] ?? $row['owner'] ?? '';
                    if (isset($ownerData[$owner])) {
                        $ownerData[$owner]['views'] = (int)($row['CNT'] ?? $row['cnt'] ?? 0);
                    } else {
                        $ownerData[$owner] = [
                            'name' => $owner,
                            'tables' => 0,
                            'views' => (int)($row['CNT'] ?? $row['cnt'] ?? 0),
                            'functions' => '?',
                            'procedures' => '?',
                            'packages' => '?'
                        ];
                    }
                }
                
                // Ordenar por nome
                ksort($ownerData);
                $counts['schemas'] = array_values($ownerData);
                
            } elseif ($tipo === 'sqlserver') {
                // SQL Server - Contar objetos por schema
                $result = $targetDb->query("
                    SELECT 
                        s.name as schema_name,
                        SUM(CASE WHEN o.type = 'U' THEN 1 ELSE 0 END) as tables_count,
                        SUM(CASE WHEN o.type = 'V' THEN 1 ELSE 0 END) as views_count,
                        SUM(CASE WHEN o.type IN ('FN', 'IF', 'TF') THEN 1 ELSE 0 END) as functions_count,
                        SUM(CASE WHEN o.type = 'P' THEN 1 ELSE 0 END) as procedures_count
                    FROM sys.schemas s
                    LEFT JOIN sys.objects o ON s.schema_id = o.schema_id
                    WHERE s.name NOT IN ('sys', 'INFORMATION_SCHEMA')
                    GROUP BY s.name
                    ORDER BY s.name
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $counts['schemas'][] = [
                        'name' => $row['schema_name'],
                        'tables' => (int)$row['tables_count'],
                        'views' => (int)$row['views_count'],
                        'functions' => (int)$row['functions_count'],
                        'procedures' => (int)$row['procedures_count']
                    ];
                }
            }
            
            return ['sucesso' => true, 'counts' => $counts];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar objetos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Buscar tabelas de um schema específico (lazy loading)
     */
    public function getTables(int $conexaoId, string $schema): array
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
            $tabelas = [];
            
            if ($tipo === 'postgres') {
                $stmt = $targetDb->prepare("
                    SELECT table_name
                    FROM information_schema.tables 
                    WHERE table_schema = ? AND table_type = 'BASE TABLE'
                    ORDER BY table_name
                ");
                $stmt->execute([$schema]);
                $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'mysql') {
                $tabelas = $targetDb->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'oracle') {
                $stmt = $targetDb->prepare("
                    SELECT table_name
                    FROM all_tables 
                    WHERE owner = ?
                    ORDER BY table_name
                ");
                $stmt->execute([$schema]);
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
                // Oracle retorna em maiúsculas
                $tabelas = $result;
                
            } elseif ($tipo === 'sqlserver') {
                $stmt = $targetDb->prepare("
                    SELECT name
                    FROM sys.tables 
                    WHERE SCHEMA_NAME(schema_id) = ?
                    ORDER BY name
                ");
                $stmt->execute([$schema]);
                $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            return ['sucesso' => true, 'tabelas' => $tabelas];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar tabelas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Buscar views de um schema específico (lazy loading)
     */
    public function getViews(int $conexaoId, string $schema): array
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
            $views = [];
            
            if ($tipo === 'postgres') {
                $stmt = $targetDb->prepare("
                    SELECT table_name
                    FROM information_schema.views 
                    WHERE table_schema = ?
                    ORDER BY table_name
                ");
                $stmt->execute([$schema]);
                $views = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'mysql') {
                $views = $targetDb->query("
                    SELECT table_name 
                    FROM information_schema.views 
                    WHERE table_schema = DATABASE()
                    ORDER BY table_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'oracle') {
                $stmt = $targetDb->prepare("
                    SELECT view_name
                    FROM all_views 
                    WHERE owner = ?
                    ORDER BY view_name
                ");
                $stmt->execute([$schema]);
                $views = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'sqlserver') {
                $stmt = $targetDb->prepare("
                    SELECT name
                    FROM sys.views 
                    WHERE SCHEMA_NAME(schema_id) = ?
                    ORDER BY name
                ");
                $stmt->execute([$schema]);
                $views = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            return ['sucesso' => true, 'views' => $views];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar views: ' . $e->getMessage()];
        }
    }
    
    /**
     * Buscar functions de um schema específico (lazy loading)
     */
    public function getFunctions(int $conexaoId, string $schema): array
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
            $functions = [];
            
            if ($tipo === 'postgres') {
                $stmt = $targetDb->prepare("
                    SELECT routine_name
                    FROM information_schema.routines 
                    WHERE routine_schema = ?
                    ORDER BY routine_name
                ");
                $stmt->execute([$schema]);
                $functions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'mysql') {
                $functions = $targetDb->query("
                    SELECT routine_name 
                    FROM information_schema.routines 
                    WHERE routine_schema = DATABASE() AND routine_type = 'FUNCTION'
                    ORDER BY routine_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'oracle') {
                $stmt = $targetDb->prepare("
                    SELECT object_name
                    FROM all_objects 
                    WHERE owner = ? AND object_type = 'FUNCTION'
                    ORDER BY object_name
                ");
                $stmt->execute([$schema]);
                $functions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'sqlserver') {
                $stmt = $targetDb->prepare("
                    SELECT name
                    FROM sys.objects 
                    WHERE SCHEMA_NAME(schema_id) = ? AND type IN ('FN', 'IF', 'TF')
                    ORDER BY name
                ");
                $stmt->execute([$schema]);
                $functions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            return ['sucesso' => true, 'functions' => $functions];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar functions: ' . $e->getMessage()];
        }
    }
    
    /**
     * Buscar procedures de um schema específico (lazy loading)
     */
    public function getProcedures(int $conexaoId, string $schema): array
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
            $procedures = [];
            
            if ($tipo === 'postgres') {
                // PostgreSQL não distingue procedures de functions antes da v11
                $procedures = [];
                
            } elseif ($tipo === 'mysql') {
                $procedures = $targetDb->query("
                    SELECT routine_name 
                    FROM information_schema.routines 
                    WHERE routine_schema = DATABASE() AND routine_type = 'PROCEDURE'
                    ORDER BY routine_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'oracle') {
                $stmt = $targetDb->prepare("
                    SELECT object_name
                    FROM all_objects 
                    WHERE owner = ? AND object_type = 'PROCEDURE'
                    ORDER BY object_name
                ");
                $stmt->execute([$schema]);
                $procedures = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } elseif ($tipo === 'sqlserver') {
                $stmt = $targetDb->prepare("
                    SELECT name
                    FROM sys.objects 
                    WHERE SCHEMA_NAME(schema_id) = ? AND type = 'P'
                    ORDER BY name
                ");
                $stmt->execute([$schema]);
                $procedures = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            return ['sucesso' => true, 'procedures' => $procedures];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar procedures: ' . $e->getMessage()];
        }
    }
    
    /**
     * Buscar packages de um schema específico (lazy loading) - Oracle only
     */
    public function getPackages(int $conexaoId, string $schema): array
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
            $packages = [];
            
            if ($tipo === 'oracle') {
                $stmt = $targetDb->prepare("
                    SELECT object_name
                    FROM all_objects 
                    WHERE owner = ? AND object_type = 'PACKAGE'
                    ORDER BY object_name
                ");
                $stmt->execute([$schema]);
                $packages = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            return ['sucesso' => true, 'packages' => $packages];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar packages: ' . $e->getMessage()];
        }
    }
    
    /**
     * DEPRECATED - Manter para compatibilidade, mas agora retorna apenas contadores
     * Use getTables, getViews, getFunctions, etc. para lazy loading
     */
    public function getObjectsOld(int $conexaoId): array
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
                
            } elseif ($tipo === 'oracle') {
                // Oracle - Buscar Owners/Schemas
                $owners = $targetDb->query("
                    SELECT DISTINCT owner 
                    FROM all_tables 
                    WHERE owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS', 
                                       'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY owner
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['schemas'] = $owners;
                
                // Tabelas de todos os owners acessíveis
                $tabelasRaw = $targetDb->query("
                    SELECT 
                        owner || '.' || table_name as name,
                        owner as schema,
                        table_name
                    FROM all_tables 
                    WHERE owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS',
                                       'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY owner, table_name
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                // Normalizar chaves para minúsculas (Oracle retorna MAIÚSCULAS)
                $objetos['tabelas'] = array_map(function($row) {
                    return [
                        'name' => $row['NAME'] ?? $row['name'] ?? '',
                        'schema' => $row['SCHEMA'] ?? $row['schema'] ?? '',
                        'table_name' => $row['TABLE_NAME'] ?? $row['table_name'] ?? ''
                    ];
                }, $tabelasRaw);
                
                // Views
                $views = $targetDb->query("
                    SELECT owner || '.' || view_name as name
                    FROM all_views 
                    WHERE owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS',
                                       'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY owner, view_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['views'] = $views;
                
                // Functions
                $funcoes = $targetDb->query("
                    SELECT owner || '.' || object_name as name
                    FROM all_objects 
                    WHERE object_type = 'FUNCTION'
                    AND owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS',
                                     'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY owner, object_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['funcoes'] = $funcoes;
                
                // Procedures
                $procedures = $targetDb->query("
                    SELECT owner || '.' || object_name as name
                    FROM all_objects 
                    WHERE object_type = 'PROCEDURE'
                    AND owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS',
                                     'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY owner, object_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['procedures'] = $procedures;
                
                // Packages
                $packages = $targetDb->query("
                    SELECT owner || '.' || object_name as name
                    FROM all_objects 
                    WHERE object_type = 'PACKAGE'
                    AND owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS',
                                     'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY owner, object_name
                ")->fetchAll(PDO::FETCH_COLUMN);
                $objetos['packages'] = $packages;
                
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
     * Obter metadados para autocomplete (tabelas + colunas)
     */
    public function getMetadata(int $conexaoId): array
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
            
            $metadata = [
                'tables' => [],
                'keywords' => []
            ];
            
            if ($tipo === 'postgres') {
                // Buscar tabelas e suas colunas
                $query = "
                    SELECT 
                        t.table_schema,
                        t.table_name,
                        COALESCE(
                            json_agg(
                                json_build_object(
                                    'name', c.column_name,
                                    'type', c.data_type
                                ) ORDER BY c.ordinal_position
                            ) FILTER (WHERE c.column_name IS NOT NULL),
                            '[]'
                        ) as columns
                    FROM information_schema.tables t
                    LEFT JOIN information_schema.columns c 
                        ON t.table_schema = c.table_schema 
                        AND t.table_name = c.table_name
                    WHERE t.table_schema NOT IN ('pg_catalog', 'information_schema')
                        AND t.table_type = 'BASE TABLE'
                    GROUP BY t.table_schema, t.table_name
                    ORDER BY t.table_schema, t.table_name
                ";
                
                $result = $targetDb->query($query)->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $tableName = $row['table_schema'] . '.' . $row['table_name'];
                    $columns = json_decode($row['columns'], true);
                    
                    $metadata['tables'][$tableName] = array_column($columns, 'name');
                    $metadata['tables'][$row['table_name']] = array_column($columns, 'name');
                }
                
            } elseif ($tipo === 'mysql') {
                // Buscar tabelas
                $tables = $targetDb->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($tables as $table) {
                    // Buscar colunas de cada tabela
                    $columns = $targetDb->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
                    $metadata['tables'][$table] = array_column($columns, 'Field');
                }
                
            } elseif ($tipo === 'oracle') {
                // Oracle - Buscar tabelas e colunas
                $query = "
                    SELECT 
                        t.owner,
                        t.table_name,
                        c.column_name,
                        c.data_type,
                        c.column_id
                    FROM all_tables t
                    LEFT JOIN all_tab_columns c 
                        ON t.owner = c.owner 
                        AND t.table_name = c.table_name
                    WHERE t.owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP', 'APPQOSSYS', 'WMSYS', 'EXFSYS',
                                         'CTXSYS', 'XDB', 'ANONYMOUS', 'ORDSYS', 'ORDDATA', 'MDSYS', 'OLAPSYS')
                    ORDER BY t.owner, t.table_name, c.column_id
                ";
                
                $result = $targetDb->query($query)->fetchAll(PDO::FETCH_ASSOC);
                
                $currentTable = null;
                $currentCols = [];
                
                foreach ($result as $row) {
                    // Oracle retorna colunas em MAIÚSCULAS
                    $owner = $row['OWNER'] ?? $row['owner'] ?? '';
                    $tableName = $row['TABLE_NAME'] ?? $row['table_name'] ?? '';
                    $columnName = $row['COLUMN_NAME'] ?? $row['column_name'] ?? null;
                    
                    $fullName = $owner . '.' . $tableName;
                    
                    if ($currentTable !== $fullName) {
                        if ($currentTable !== null) {
                            $metadata['tables'][$currentTable] = $currentCols;
                        }
                        $currentTable = $fullName;
                        $currentCols = [];
                    }
                    
                    if ($columnName) {
                        $currentCols[] = $columnName;
                    }
                }
                
                if ($currentTable !== null) {
                    $metadata['tables'][$currentTable] = $currentCols;
                }
                
            } elseif ($tipo === 'sqlserver') {
                // Buscar tabelas e colunas
                $query = "
                    SELECT 
                        SCHEMA_NAME(t.schema_id) as table_schema,
                        t.name as table_name,
                        c.name as column_name
                    FROM sys.tables t
                    LEFT JOIN sys.columns c ON t.object_id = c.object_id
                    WHERE SCHEMA_NAME(t.schema_id) NOT IN ('sys', 'INFORMATION_SCHEMA')
                    ORDER BY t.schema_id, t.name, c.column_id
                ";
                
                $result = $targetDb->query($query)->fetchAll(PDO::FETCH_ASSOC);
                
                $currentTable = null;
                $currentCols = [];
                
                foreach ($result as $row) {
                    $fullName = $row['table_schema'] . '.' . $row['table_name'];
                    
                    if ($currentTable !== $fullName) {
                        if ($currentTable !== null) {
                            $metadata['tables'][$currentTable] = $currentCols;
                        }
                        $currentTable = $fullName;
                        $currentCols = [];
                    }
                    
                    if ($row['column_name']) {
                        $currentCols[] = $row['column_name'];
                    }
                }
                
                if ($currentTable !== null) {
                    $metadata['tables'][$currentTable] = $currentCols;
                }
            }
            
            // Adicionar keywords SQL comuns
            $metadata['keywords'] = [
                'SELECT', 'FROM', 'WHERE', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP',
                'TABLE', 'VIEW', 'INDEX', 'DATABASE', 'SCHEMA', 'JOIN', 'INNER', 'LEFT', 'RIGHT',
                'OUTER', 'ON', 'AND', 'OR', 'NOT', 'IN', 'LIKE', 'BETWEEN', 'IS', 'NULL',
                'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT', 'OFFSET', 'DISTINCT', 'AS',
                'COUNT', 'SUM', 'AVG', 'MIN', 'MAX', 'CAST', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END'
            ];
            
            return ['sucesso' => true, 'metadata' => $metadata];
            
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
            
            // Limpar SQL: remover ponto e vírgula no final (Oracle PDO não aceita)
            $sql = rtrim($sql);
            if (substr($sql, -1) === ';') {
                $sql = substr($sql, 0, -1);
            }
            
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
        $parametrosExtras = [];
        
        // Parse parametros_extras se existir
        if (!empty($config['parametros_extras'])) {
            $parametrosExtras = json_decode($config['parametros_extras'], true) ?? [];
        }
        
        // Descriptografar senha usando a mesma classe Crypto do sistema
        $senha = '';
        if ($senhaEncriptada) {
            $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
            if (!$key) {
                throw new Exception('ENCRYPTION_KEY não configurada no sistema');
            }
            
            try {
                $senha = Crypto::decrypt($senhaEncriptada, $key);
                // Senha pode ser vazia legitimamente (ex: root do MySQL sem senha)
                // Não validar se está vazia, apenas verificar se descriptografia funcionou
            } catch (Exception $e) {
                throw new Exception('Erro ao descriptografar senha: ' . $e->getMessage());
            }
        }
        // Senha vazia é permitida (alguns bancos não usam senha)
        
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
                // Oracle precisa de SID ou Service Name dos parametros_extras
                $tipoConexaoOracle = $parametrosExtras['tipo_conexao_oracle'] ?? 'sid';
                $serviceName = $parametrosExtras['service_name'] ?? '';
                $sid = $parametrosExtras['sid'] ?? '';
                
                if ($tipoConexaoOracle === 'service_name' && !empty($serviceName)) {
                    $dsn = "oci:dbname=//{$host}:{$porta}/{$serviceName};charset=UTF8";
                } elseif (!empty($sid)) {
                    $dsn = "oci:dbname=//{$host}:{$porta}/{$sid};charset=UTF8";
                } elseif (!empty($database)) {
                    // Fallback: usar nome_banco se existir
                    $dsn = "oci:dbname=//{$host}:{$porta}/{$database};charset=UTF8";
                } else {
                    throw new Exception("Conexão Oracle requer SID ou Service Name configurado");
                }
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
