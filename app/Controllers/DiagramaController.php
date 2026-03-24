<?php
namespace App\Controllers;

use App\Core\Database;
use App\Servicos\ServicoPermissao;
use App\Utils\Crypto;
use PDO;
use Exception;

class DiagramaController
{
    private function verificarAcessoConexao(array $conexao): void
    {
        if (!ServicoPermissao::podeVerRecurso('conexao', (int)$conexao['id'], isset($conexao['criado_por']) ? (int)$conexao['criado_por'] : null)) {
            throw new Exception('Acesso negado a esta conexão');
        }
    }

    /**
     * Obter estrutura completa do banco para diagrama
     */
    public function getEstrutura(int $conexaoId): array
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conexao) {
                return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
            }
            $this->verificarAcessoConexao($conexao);
            
            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];
            
            $estrutura = [
                'conexao' => [
                    'id' => $conexao['id'],
                    'nome' => $conexao['nome_conexao'],
                    'tipo' => $tipo,
                    'banco' => $conexao['nome_banco']
                ],
                'tabelas' => [],
                'relacionamentos' => []
            ];
            
            // Obter tabelas e colunas
            $tabelas = $this->obterTabelas($targetDb, $tipo, $conexao);
            $estrutura['tabelas'] = $tabelas;
            
            // Obter relacionamentos (foreign keys)
            $relacionamentos = $this->obterRelacionamentos($targetDb, $tipo, $conexao);
            $estrutura['relacionamentos'] = $relacionamentos;
            
            return ['sucesso' => true, 'estrutura' => $estrutura];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    /**
     * Obter estrutura de uma tabela específica e suas relações
     */
    public function getEstruturaTabela(int $conexaoId, string $schema, string $tabela): array
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conexao) {
                return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
            }
            $this->verificarAcessoConexao($conexao);
            
            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];
            
            $estrutura = [
                'conexao' => [
                    'id' => $conexao['id'],
                    'nome' => $conexao['nome_conexao'],
                    'tipo' => $tipo,
                    'banco' => $conexao['nome_banco']
                ],
                'tabelas' => [],
                'relacionamentos' => []
            ];
            
            // Obter tabela principal e relacionadas
            $tabelasRelacionadas = $this->obterTabelasRelacionadas($targetDb, $tipo, $conexao, $schema, $tabela);
            $estrutura['tabelas'] = $tabelasRelacionadas['tabelas'];
            $estrutura['relacionamentos'] = $tabelasRelacionadas['relacionamentos'];
            $estrutura['tabelaPrincipal'] = $tabela;
            
            return ['sucesso' => true, 'estrutura' => $estrutura];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    /**
     * Salvar posições do diagrama
     */
    public function salvarPosicoes(int $conexaoId, array $posicoes): array
    {
        try {
            $db = Database::getConexao();
            
            // Criar tabela se não existir
            $db->exec("
                CREATE TABLE IF NOT EXISTS tb_diagrama_posicoes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    conexao_id INT NOT NULL,
                    tabela_nome VARCHAR(255) NOT NULL,
                    pos_x FLOAT NOT NULL,
                    pos_y FLOAT NOT NULL,
                    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uk_conexao_tabela (conexao_id, tabela_nome)
                )
            ");
            
            foreach ($posicoes as $pos) {
                $stmt = $db->prepare("
                    INSERT INTO tb_diagrama_posicoes (conexao_id, tabela_nome, pos_x, pos_y)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE pos_x = VALUES(pos_x), pos_y = VALUES(pos_y)
                ");
                $stmt->execute([$conexaoId, $pos['tabela'], $pos['x'], $pos['y']]);
            }
            
            return ['sucesso' => true, 'mensagem' => 'Posições salvas com sucesso'];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    /**
     * Carregar posições salvas do diagrama
     */
    public function carregarPosicoes(int $conexaoId): array
    {
        try {
            $db = Database::getConexao();
            
            // Verificar se tabela existe
            $tables = $db->query("SHOW TABLES LIKE 'tb_diagrama_posicoes'")->fetchAll();
            if (empty($tables)) {
                return ['sucesso' => true, 'posicoes' => []];
            }
            
            $stmt = $db->prepare("SELECT tabela_nome, pos_x, pos_y FROM tb_diagrama_posicoes WHERE conexao_id = ?");
            $stmt->execute([$conexaoId]);
            $posicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($posicoes as $pos) {
                $result[$pos['tabela_nome']] = [
                    'x' => (float)$pos['pos_x'],
                    'y' => (float)$pos['pos_y']
                ];
            }
            
            return ['sucesso' => true, 'posicoes' => $result];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage(), 'posicoes' => []];
        }
    }
    
    /**
     * Listar tabelas disponíveis para seleção
     */
    public function listarTabelas(int $conexaoId): array
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$conexao) {
                return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
            }
            $this->verificarAcessoConexao($conexao);
            
            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];
            
            $tabelas = [];
            
            if ($tipo === 'postgres') {
                $result = $targetDb->query("
                    SELECT table_schema, table_name 
                    FROM information_schema.tables 
                    WHERE table_type = 'BASE TABLE' 
                    AND table_schema NOT IN ('pg_catalog', 'information_schema')
                    ORDER BY table_schema, table_name
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $tabelas[] = [
                        'schema' => $row['table_schema'],
                        'nome' => $row['table_name'],
                        'completo' => $row['table_schema'] . '.' . $row['table_name']
                    ];
                }
            } elseif ($tipo === 'mysql') {
                $database = $conexao['nome_banco'];
                $result = $targetDb->query("
                    SELECT table_schema, table_name 
                    FROM information_schema.tables 
                    WHERE table_type = 'BASE TABLE' 
                    AND table_schema = '{$database}'
                    ORDER BY table_name
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $tabelas[] = [
                        'schema' => $row['table_schema'],
                        'nome' => $row['table_name'],
                        'completo' => $row['table_name']
                    ];
                }
            } elseif ($tipo === 'oracle') {
                $result = $targetDb->query("
                    SELECT owner, table_name 
                    FROM all_tables 
                    WHERE owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP')
                    ORDER BY owner, table_name
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $tabelas[] = [
                        'schema' => $row['OWNER'],
                        'nome' => $row['TABLE_NAME'],
                        'completo' => $row['OWNER'] . '.' . $row['TABLE_NAME']
                    ];
                }
            } elseif ($tipo === 'sqlserver') {
                $result = $targetDb->query("
                    SELECT TABLE_SCHEMA, TABLE_NAME 
                    FROM INFORMATION_SCHEMA.TABLES 
                    WHERE TABLE_TYPE = 'BASE TABLE'
                    ORDER BY TABLE_SCHEMA, TABLE_NAME
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $tabelas[] = [
                        'schema' => $row['TABLE_SCHEMA'],
                        'nome' => $row['TABLE_NAME'],
                        'completo' => $row['TABLE_SCHEMA'] . '.' . $row['TABLE_NAME']
                    ];
                }
            } elseif ($tipo === 'sqlite') {
                $result = $targetDb->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($result as $name) {
                    $tabelas[] = [
                        'schema' => 'main',
                        'nome' => $name,
                        'completo' => $name
                    ];
                }
            }
            
            return ['sucesso' => true, 'tabelas' => $tabelas];
            
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    /**
     * Obter tabelas com suas colunas
     */
    private function obterTabelas($db, string $tipo, array $conexao): array
    {
        $tabelas = [];
        
        if ($tipo === 'postgres') {
            // Obter tabelas
            $result = $db->query("
                SELECT t.table_schema, t.table_name,
                       obj_description((t.table_schema || '.' || t.table_name)::regclass) as table_comment
                FROM information_schema.tables t
                WHERE t.table_type = 'BASE TABLE' 
                AND t.table_schema NOT IN ('pg_catalog', 'information_schema')
                ORDER BY t.table_schema, t.table_name
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $schemaTable = $row['table_schema'] . '.' . $row['table_name'];
                
                // Obter colunas
                $cols = $db->prepare("
                    SELECT 
                        c.column_name,
                        c.data_type,
                        c.character_maximum_length,
                        c.numeric_precision,
                        c.is_nullable,
                        c.column_default,
                        CASE WHEN pk.column_name IS NOT NULL THEN true ELSE false END as is_primary_key,
                        col_description((c.table_schema || '.' || c.table_name)::regclass, c.ordinal_position) as column_comment
                    FROM information_schema.columns c
                    LEFT JOIN (
                        SELECT ku.column_name, ku.table_schema, ku.table_name
                        FROM information_schema.table_constraints tc
                        JOIN information_schema.key_column_usage ku ON tc.constraint_name = ku.constraint_name
                        WHERE tc.constraint_type = 'PRIMARY KEY'
                    ) pk ON c.table_schema = pk.table_schema AND c.table_name = pk.table_name AND c.column_name = pk.column_name
                    WHERE c.table_schema = ? AND c.table_name = ?
                    ORDER BY c.ordinal_position
                ");
                $cols->execute([$row['table_schema'], $row['table_name']]);
                $colunas = $cols->fetchAll(PDO::FETCH_ASSOC);
                
                $tabelas[] = [
                    'schema' => $row['table_schema'],
                    'nome' => $row['table_name'],
                    'completo' => $schemaTable,
                    'comentario' => $row['table_comment'],
                    'colunas' => array_map(function($col) {
                        return [
                            'nome' => $col['column_name'],
                            'tipo' => $this->formatarTipo($col),
                            'nulo' => $col['is_nullable'] === 'YES',
                            'pk' => (bool)$col['is_primary_key'],
                            'default' => $col['column_default'],
                            'comentario' => $col['column_comment']
                        ];
                    }, $colunas)
                ];
            }
        } elseif ($tipo === 'mysql') {
            $database = $conexao['nome_banco'];
            
            $result = $db->query("
                SELECT table_name, table_comment
                FROM information_schema.tables 
                WHERE table_type = 'BASE TABLE' 
                AND table_schema = '{$database}'
                ORDER BY table_name
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $cols = $db->prepare("
                    SELECT 
                        column_name,
                        data_type,
                        character_maximum_length,
                        numeric_precision,
                        is_nullable,
                        column_default,
                        column_key,
                        column_comment
                    FROM information_schema.columns
                    WHERE table_schema = ? AND table_name = ?
                    ORDER BY ordinal_position
                ");
                $cols->execute([$database, $row['table_name']]);
                $colunas = $cols->fetchAll(PDO::FETCH_ASSOC);
                
                $tabelas[] = [
                    'schema' => $database,
                    'nome' => $row['table_name'],
                    'completo' => $row['table_name'],
                    'comentario' => $row['table_comment'],
                    'colunas' => array_map(function($col) {
                        return [
                            'nome' => $col['column_name'],
                            'tipo' => $this->formatarTipo($col),
                            'nulo' => $col['is_nullable'] === 'YES',
                            'pk' => $col['column_key'] === 'PRI',
                            'default' => $col['column_default'],
                            'comentario' => $col['column_comment']
                        ];
                    }, $colunas)
                ];
            }
        } elseif ($tipo === 'oracle') {
            $result = $db->query("
                SELECT owner, table_name, comments
                FROM all_tab_comments
                WHERE owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP')
                AND table_type = 'TABLE'
                ORDER BY owner, table_name
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $cols = $db->prepare("
                    SELECT 
                        c.column_name,
                        c.data_type,
                        c.data_length,
                        c.data_precision,
                        c.nullable,
                        c.data_default,
                        cc.comments as column_comment,
                        CASE WHEN pk.column_name IS NOT NULL THEN 'Y' ELSE 'N' END as is_pk
                    FROM all_tab_columns c
                    LEFT JOIN all_col_comments cc ON c.owner = cc.owner AND c.table_name = cc.table_name AND c.column_name = cc.column_name
                    LEFT JOIN (
                        SELECT acc.owner, acc.table_name, acc.column_name
                        FROM all_cons_columns acc
                        JOIN all_constraints ac ON acc.constraint_name = ac.constraint_name
                        WHERE ac.constraint_type = 'P'
                    ) pk ON c.owner = pk.owner AND c.table_name = pk.table_name AND c.column_name = pk.column_name
                    WHERE c.owner = :owner AND c.table_name = :table_name
                    ORDER BY c.column_id
                ");
                $cols->execute(['owner' => $row['OWNER'], 'table_name' => $row['TABLE_NAME']]);
                $colunas = $cols->fetchAll(PDO::FETCH_ASSOC);
                
                $tabelas[] = [
                    'schema' => $row['OWNER'],
                    'nome' => $row['TABLE_NAME'],
                    'completo' => $row['OWNER'] . '.' . $row['TABLE_NAME'],
                    'comentario' => $row['COMMENTS'],
                    'colunas' => array_map(function($col) {
                        return [
                            'nome' => $col['COLUMN_NAME'],
                            'tipo' => $col['DATA_TYPE'] . ($col['DATA_LENGTH'] ? "({$col['DATA_LENGTH']})" : ''),
                            'nulo' => $col['NULLABLE'] === 'Y',
                            'pk' => $col['IS_PK'] === 'Y',
                            'default' => $col['DATA_DEFAULT'],
                            'comentario' => $col['COLUMN_COMMENT']
                        ];
                    }, $colunas)
                ];
            }
        } elseif ($tipo === 'sqlserver') {
            $result = $db->query("
                SELECT 
                    s.name as table_schema,
                    t.name as table_name,
                    ep.value as table_comment
                FROM sys.tables t
                JOIN sys.schemas s ON t.schema_id = s.schema_id
                LEFT JOIN sys.extended_properties ep ON t.object_id = ep.major_id AND ep.minor_id = 0 AND ep.name = 'MS_Description'
                ORDER BY s.name, t.name
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $cols = $db->prepare("
                    SELECT 
                        c.name as column_name,
                        ty.name as data_type,
                        c.max_length,
                        c.precision,
                        c.is_nullable,
                        dc.definition as column_default,
                        CASE WHEN ic.object_id IS NOT NULL THEN 1 ELSE 0 END as is_pk,
                        ep.value as column_comment
                    FROM sys.columns c
                    JOIN sys.types ty ON c.user_type_id = ty.user_type_id
                    JOIN sys.tables t ON c.object_id = t.object_id
                    JOIN sys.schemas s ON t.schema_id = s.schema_id
                    LEFT JOIN sys.default_constraints dc ON c.default_object_id = dc.object_id
                    LEFT JOIN sys.index_columns ic ON t.object_id = ic.object_id AND c.column_id = ic.column_id
                        AND ic.index_id = (SELECT i.index_id FROM sys.indexes i WHERE i.object_id = t.object_id AND i.is_primary_key = 1)
                    LEFT JOIN sys.extended_properties ep ON t.object_id = ep.major_id AND c.column_id = ep.minor_id AND ep.name = 'MS_Description'
                    WHERE s.name = ? AND t.name = ?
                    ORDER BY c.column_id
                ");
                $cols->execute([$row['table_schema'], $row['table_name']]);
                $colunas = $cols->fetchAll(PDO::FETCH_ASSOC);
                
                $tabelas[] = [
                    'schema' => $row['table_schema'],
                    'nome' => $row['table_name'],
                    'completo' => $row['table_schema'] . '.' . $row['table_name'],
                    'comentario' => $row['table_comment'],
                    'colunas' => array_map(function($col) {
                        return [
                            'nome' => $col['column_name'],
                            'tipo' => $col['data_type'] . ($col['max_length'] > 0 ? "({$col['max_length']})" : ''),
                            'nulo' => (bool)$col['is_nullable'],
                            'pk' => (bool)$col['is_pk'],
                            'default' => $col['column_default'],
                            'comentario' => $col['column_comment']
                        ];
                    }, $colunas)
                ];
            }
        } elseif ($tipo === 'sqlite') {
            $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $tableName) {
                $cols = $db->query("PRAGMA table_info(" . $db->quote($tableName) . ")")->fetchAll(PDO::FETCH_ASSOC);
                $tabelas[] = [
                    'schema' => 'main',
                    'nome' => $tableName,
                    'completo' => $tableName,
                    'comentario' => null,
                    'colunas' => array_map(function($col) {
                        return [
                            'nome' => $col['name'],
                            'tipo' => $col['type'] ?: 'TEXT',
                            'nulo' => !$col['notnull'],
                            'pk' => (bool)$col['pk'],
                            'default' => $col['dflt_value'],
                            'comentario' => null
                        ];
                    }, $cols)
                ];
            }
        }
        
        return $tabelas;
    }
    
    /**
     * Obter relacionamentos (foreign keys)
     */
    private function obterRelacionamentos($db, string $tipo, array $conexao): array
    {
        $relacionamentos = [];
        
        if ($tipo === 'postgres') {
            $result = $db->query("
                SELECT
                    tc.table_schema as from_schema,
                    tc.table_name as from_table,
                    kcu.column_name as from_column,
                    ccu.table_schema as to_schema,
                    ccu.table_name as to_table,
                    ccu.column_name as to_column,
                    tc.constraint_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage ccu ON ccu.constraint_name = tc.constraint_name
                WHERE tc.constraint_type = 'FOREIGN KEY'
                AND tc.table_schema NOT IN ('pg_catalog', 'information_schema')
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $relacionamentos[] = [
                    'from_table' => $row['from_schema'] . '.' . $row['from_table'],
                    'from_column' => $row['from_column'],
                    'to_table' => $row['to_schema'] . '.' . $row['to_table'],
                    'to_column' => $row['to_column'],
                    'nome' => $row['constraint_name']
                ];
            }
        } elseif ($tipo === 'mysql') {
            $database = $conexao['nome_banco'];
            
            $result = $db->query("
                SELECT
                    kcu.table_name as from_table,
                    kcu.column_name as from_column,
                    kcu.referenced_table_name as to_table,
                    kcu.referenced_column_name as to_column,
                    kcu.constraint_name
                FROM information_schema.key_column_usage kcu
                WHERE kcu.table_schema = '{$database}'
                AND kcu.referenced_table_name IS NOT NULL
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $relacionamentos[] = [
                    'from_table' => $row['from_table'],
                    'from_column' => $row['from_column'],
                    'to_table' => $row['to_table'],
                    'to_column' => $row['to_column'],
                    'nome' => $row['constraint_name']
                ];
            }
        } elseif ($tipo === 'oracle') {
            $result = $db->query("
                SELECT
                    a.owner as from_schema,
                    a.table_name as from_table,
                    a.column_name as from_column,
                    c_pk.owner as to_schema,
                    c_pk.table_name as to_table,
                    b.column_name as to_column,
                    a.constraint_name
                FROM all_cons_columns a
                JOIN all_constraints c ON a.constraint_name = c.constraint_name
                JOIN all_constraints c_pk ON c.r_constraint_name = c_pk.constraint_name
                JOIN all_cons_columns b ON c_pk.constraint_name = b.constraint_name
                WHERE c.constraint_type = 'R'
                AND a.owner NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'DBSNMP')
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $relacionamentos[] = [
                    'from_table' => $row['FROM_SCHEMA'] . '.' . $row['FROM_TABLE'],
                    'from_column' => $row['FROM_COLUMN'],
                    'to_table' => $row['TO_SCHEMA'] . '.' . $row['TO_TABLE'],
                    'to_column' => $row['TO_COLUMN'],
                    'nome' => $row['CONSTRAINT_NAME']
                ];
            }
        } elseif ($tipo === 'sqlserver') {
            $result = $db->query("
                SELECT
                    s1.name as from_schema,
                    t1.name as from_table,
                    c1.name as from_column,
                    s2.name as to_schema,
                    t2.name as to_table,
                    c2.name as to_column,
                    fk.name as constraint_name
                FROM sys.foreign_keys fk
                JOIN sys.foreign_key_columns fkc ON fk.object_id = fkc.constraint_object_id
                JOIN sys.tables t1 ON fkc.parent_object_id = t1.object_id
                JOIN sys.schemas s1 ON t1.schema_id = s1.schema_id
                JOIN sys.columns c1 ON fkc.parent_object_id = c1.object_id AND fkc.parent_column_id = c1.column_id
                JOIN sys.tables t2 ON fkc.referenced_object_id = t2.object_id
                JOIN sys.schemas s2 ON t2.schema_id = s2.schema_id
                JOIN sys.columns c2 ON fkc.referenced_object_id = c2.object_id AND fkc.referenced_column_id = c2.column_id
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $relacionamentos[] = [
                    'from_table' => $row['from_schema'] . '.' . $row['from_table'],
                    'from_column' => $row['from_column'],
                    'to_table' => $row['to_schema'] . '.' . $row['to_table'],
                    'to_column' => $row['to_column'],
                    'nome' => $row['constraint_name']
                ];
            }
        } elseif ($tipo === 'sqlite') {
            $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $tableName) {
                $fks = $db->query("PRAGMA foreign_key_list(" . $db->quote($tableName) . ")")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fks as $fk) {
                    $relacionamentos[] = [
                        'from_table' => $tableName,
                        'from_column' => $fk['from'],
                        'to_table' => $fk['table'],
                        'to_column' => $fk['to'],
                        'nome' => 'fk_' . $tableName . '_' . $fk['from']
                    ];
                }
            }
        }
        
        return $relacionamentos;
    }
    
    /**
     * Obter tabelas relacionadas a uma tabela específica
     */
    private function obterTabelasRelacionadas($db, string $tipo, array $conexao, string $schema, string $tabela): array
    {
        // Primeiro, obter todos os relacionamentos
        $todosRelacionamentos = $this->obterRelacionamentos($db, $tipo, $conexao);
        
        // Nome completo da tabela
        $nomeCompleto = ($tipo === 'mysql') ? $tabela : $schema . '.' . $tabela;
        
        // Filtrar relacionamentos que envolvem a tabela
        $relacionamentosFiltrados = array_filter($todosRelacionamentos, function($rel) use ($nomeCompleto, $tabela) {
            return $rel['from_table'] === $nomeCompleto || 
                   $rel['to_table'] === $nomeCompleto ||
                   $rel['from_table'] === $tabela || 
                   $rel['to_table'] === $tabela;
        });
        
        // Coletar nomes das tabelas relacionadas
        $tabelasNomes = [$nomeCompleto];
        foreach ($relacionamentosFiltrados as $rel) {
            if (!in_array($rel['from_table'], $tabelasNomes)) {
                $tabelasNomes[] = $rel['from_table'];
            }
            if (!in_array($rel['to_table'], $tabelasNomes)) {
                $tabelasNomes[] = $rel['to_table'];
            }
        }
        
        // Obter todas as tabelas e filtrar
        $todasTabelas = $this->obterTabelas($db, $tipo, $conexao);
        $tabelasFiltradas = array_filter($todasTabelas, function($tab) use ($tabelasNomes) {
            return in_array($tab['completo'], $tabelasNomes) || in_array($tab['nome'], $tabelasNomes);
        });
        
        return [
            'tabelas' => array_values($tabelasFiltradas),
            'relacionamentos' => array_values($relacionamentosFiltrados)
        ];
    }
    
    /**
     * Formatar tipo de dado
     */
    private function formatarTipo(array $col): string
    {
        $tipo = $col['data_type'] ?? $col['DATA_TYPE'] ?? '';
        $tamanho = $col['character_maximum_length'] ?? $col['data_length'] ?? $col['max_length'] ?? null;
        $precisao = $col['numeric_precision'] ?? $col['data_precision'] ?? $col['precision'] ?? null;
        
        if ($tamanho && $tamanho > 0 && $tamanho < 10000) {
            return strtoupper($tipo) . "({$tamanho})";
        } elseif ($precisao) {
            return strtoupper($tipo) . "({$precisao})";
        }
        
        return strtoupper($tipo);
    }
    
    /**
     * Criar conexão PDO
     */
    private function criarConexao(array $conexao): PDO
    {
        $tipo = $conexao['tipo_banco'];
        $host = $conexao['host'];
        $porta = $conexao['porta'];
        $banco = $conexao['nome_banco'];
        $usuario = $conexao['usuario'];
        
        // Descriptografar senha
        $senha = '';
        if (!empty($conexao['senha_encriptada'])) {
            $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
            if (!$key) {
                throw new Exception('ENCRYPTION_KEY não configurada no sistema');
            }
            $senha = Crypto::decrypt($conexao['senha_encriptada'], $key);
        }
        
        switch ($tipo) {
            case 'postgres':
                $dsn = "pgsql:host={$host};port={$porta};dbname={$banco}";
                break;
            case 'mysql':
                $dsn = "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4";
                break;
            case 'oracle':
                $dsn = "oci:dbname=//{$host}:{$porta}/{$banco};charset=AL32UTF8";
                break;
            case 'sqlserver':
                $dsn = "sqlsrv:Server={$host},{$porta};Database={$banco}";
                break;
            case 'sqlite':
                $extras = json_decode($conexao['parametros_extras'] ?? '{}', true) ?: [];
                $sqlitePath = $extras['sqlite_path'] ?? $banco ?? '';
                if (empty($sqlitePath)) {
                    throw new Exception('Caminho do banco SQLite é obrigatório');
                }
                if (!file_exists($sqlitePath)) {
                    throw new Exception("Arquivo SQLite não encontrado: {$sqlitePath}");
                }
                $dsn = "sqlite:{$sqlitePath}";
                $usuario = null;
                $senha = null;
                break;
            default:
                throw new Exception("Tipo de banco não suportado: {$tipo}");
        }
        
        return new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
}
