<?php
/**
 * DMC DataLoad - Serviço de Permissões (RBAC)
 * 
 * Hierarquia de papéis:
 *   super_admin (4) → Tudo. Único. Cria empresas, associa admins.
 *   admin (3)       → Gerencia dentro das suas empresas. Cria projetos e usuários subordinados.
 *   desenvolvedor (2) → Cria recursos. Vê apenas os seus. Pode compartilhar.
 *   operador (1)     → Somente leitura em dashboard, historico, diagrama, scheduler, calendario.
 */

namespace App\Servicos;

use App\Core\Database;
use App\Core\AuthMiddleware;

class ServicoPermissao
{
    private static array $niveisHierarquia = [
        'operador' => 1,
        'desenvolvedor' => 2,
        'admin' => 3,
        'super_admin' => 4,
    ];

    // Páginas que o operador pode acessar (somente leitura)
    private static array $paginasOperador = [
        '/dashboard', '/',
        '/historico',
        '/diagrama',
        '/monitoramento',
        '/scheduler',
        '/calendario',
        '/meu-perfil',
        '/notificacoes',
    ];

    // Tipos de recurso válidos
    private static array $tiposRecurso = [
        'conexao', 'rotina', 'api', 'workflow', 'pipeline', 'agendamento', 'evento_api'
    ];

    // ================================================================
    // Métodos de verificação de papel
    // ================================================================

    public static function obterNivel(?string $papel = null): int
    {
        if ($papel === null) {
            $usuario = AuthMiddleware::obterUsuario();
            $papel = $usuario['nivel_acesso'] ?? 'operador';
        }
        return self::$niveisHierarquia[$papel] ?? 0;
    }

    public static function ehSuperAdmin(?array $usuario = null): bool
    {
        $usuario = $usuario ?? AuthMiddleware::obterUsuario();
        return ($usuario['nivel_acesso'] ?? '') === 'super_admin';
    }

    public static function ehAdmin(?array $usuario = null): bool
    {
        $usuario = $usuario ?? AuthMiddleware::obterUsuario();
        return in_array($usuario['nivel_acesso'] ?? '', ['admin', 'super_admin']);
    }

    public static function ehDesenvolvedor(?array $usuario = null): bool
    {
        $usuario = $usuario ?? AuthMiddleware::obterUsuario();
        return ($usuario['nivel_acesso'] ?? '') === 'desenvolvedor';
    }

    public static function ehOperador(?array $usuario = null): bool
    {
        $usuario = $usuario ?? AuthMiddleware::obterUsuario();
        return ($usuario['nivel_acesso'] ?? '') === 'operador';
    }

    /**
     * Verifica se o nível do usuário atinge o mínimo requerido
     */
    public static function temNivelMinimo(string $nivelRequerido, ?array $usuario = null): bool
    {
        $usuario = $usuario ?? AuthMiddleware::obterUsuario();
        $nivelAtual = self::obterNivel($usuario['nivel_acesso'] ?? 'operador');
        $nivelMin = self::obterNivel($nivelRequerido);
        return $nivelAtual >= $nivelMin;
    }

    /**
     * Exige nível mínimo de acesso — retorna 403 JSON se insuficiente
     */
    public static function exigirNivel(string $nivelRequerido): void
    {
        AuthMiddleware::exigirAutenticacao();
        if (!self::temNivelMinimo($nivelRequerido)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Acesso negado. Nível requerido: ' . $nivelRequerido]);
            exit;
        }
    }

    // ================================================================
    // Verificação de acesso a páginas (Operador)
    // ================================================================

    /**
     * Verifica se o operador pode acessar determinada página
     */
    public static function operadorPodeAcessarPagina(string $path): bool
    {
        // Remove query string e trailing slash
        $path = parse_url($path, PHP_URL_PATH) ?: $path;
        $path = rtrim($path, '/') ?: '/';

        foreach (self::$paginasOperador as $pagina) {
            if ($path === $pagina) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se a rota API é permitida para operador (somente GET/leitura)
     */
    public static function operadorPodeAcessarApi(string $path, string $method): bool
    {
        // APIs de perfil do próprio usuário: permitidas para todos (GET e POST)
        $apisPerfil = ['/api/perfil', '/api/perfil/atualizar', '/api/perfil/alterar-senha'];
        if (in_array($path, $apisPerfil, true)) {
            return true;
        }

        // APIs de notificação do próprio usuário: permitidas para todos
        if (str_starts_with($path, '/api/notificacoes/')) {
            return true;
        }

        // Operador só pode fazer GET nas demais rotas
        if ($method !== 'GET') {
            return false;
        }

        // APIs de leitura permitidas para operador
        $apisPermitidas = [
            '/api/dashboard/',
            '/api/health',
            '/api/versao',
            '/api/sessao',
            '/api/historico',
            '/api/calendario/',
            '/api/scheduler/',
            '/api/diagrama/',
            '/api/notificacoes',
            '/historico/',
            '/calendario/',
            '/scheduler/',
            '/diagrama/',
            '/conexoes/list',
            '/conexoes/get/',
            '/rotinas/list',
            '/rotinas/get/',
            '/rotinas/stats/',
            '/pipelines/list',
            '/pipelines/get/',
            '/pipelines/stats',
            '/pipelines/historico/',
            '/pipelines/execucao/',
            '/api/workflows/list',
            '/api/workflows/get/',
            '/api/workflows/stats',
            '/api/workflow-execucoes/',
            '/api/apis-externas/list',
            '/api/apis-externas/get/',
            '/api/eventos-api/list',
            '/api/eventos-api/get/',
        ];

        foreach ($apisPermitidas as $api) {
            if (strpos($path, $api) === 0 || $path === rtrim($api, '/')) {
                return true;
            }
        }

        return false;
    }

    // ================================================================
    // Empresas do usuário
    // ================================================================

    /**
     * Retorna IDs das empresas a que o usuário pertence
     */
    public static function obterEmpresasDoUsuario(?int $idUsuario = null): array
    {
        $idUsuario = $idUsuario ?? AuthMiddleware::obterUsuarioId();
        if (!$idUsuario) return [];

        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT id_empresa FROM tb_usuario_empresas WHERE id_usuario = :id");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Retorna IDs dos projetos a que o usuário pertence
     */
    public static function obterProjetosDoUsuario(?int $idUsuario = null): array
    {
        $idUsuario = $idUsuario ?? AuthMiddleware::obterUsuarioId();
        if (!$idUsuario) return [];

        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT id_projeto FROM tb_usuario_projetos WHERE id_usuario = :id");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Verifica se o usuário pertence a uma empresa específica
     */
    public static function usuarioPertenceEmpresa(int $idEmpresa, ?int $idUsuario = null): bool
    {
        $idUsuario = $idUsuario ?? AuthMiddleware::obterUsuarioId();
        if (!$idUsuario) return false;

        // Super admin pertence a todas
        if (self::ehSuperAdmin()) return true;

        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT 1 FROM tb_usuario_empresas WHERE id_usuario = :uid AND id_empresa = :eid");
        $stmt->execute([':uid' => $idUsuario, ':eid' => $idEmpresa]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Verifica se o usuário pertence a um projeto específico
     */
    public static function usuarioPertenceProjeto(int $idProjeto, ?int $idUsuario = null): bool
    {
        $idUsuario = $idUsuario ?? AuthMiddleware::obterUsuarioId();
        if (!$idUsuario) return false;

        if (self::ehSuperAdmin()) return true;

        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT 1 FROM tb_usuario_projetos WHERE id_usuario = :uid AND id_projeto = :pid");
        $stmt->execute([':uid' => $idUsuario, ':pid' => $idProjeto]);
        return (bool)$stmt->fetchColumn();
    }

    // ================================================================
    // Visibilidade de recursos
    // ================================================================

    /**
     * Retorna cláusula SQL WHERE para filtrar recursos visíveis ao usuário.
     * 
     * Para super_admin: sem filtro (vê tudo)
     * Para admin: recursos das empresas dele
     * Para desenvolvedor: recursos que ele criou OU que foram compartilhados com ele
     * Para operador: recursos das empresas/projetos dele (somente leitura)
     * 
     * @param string $tipoRecurso  Tipo: conexao, rotina, api, workflow, pipeline, etc.
     * @param string $aliasTabela  Alias da tabela principal na query (ex: 'r' para tb_rotinas r)
     * @param string $colunaCriador Nome da coluna de criador na tabela (ex: 'criado_por', 'id_usuario_criador')
     * @return array ['where' => string, 'params' => array]
     */
    public static function filtroVisibilidade(string $tipoRecurso, string $aliasTabela, string $colunaCriador = 'criado_por'): array
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();

        if (!$usuario || !$idUsuario) {
            return ['where' => 'FALSE', 'params' => []];
        }

        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        // Super admin vê tudo
        if ($nivel === 'super_admin') {
            return ['where' => 'TRUE', 'params' => []];
        }

        // Admin: vê tudo das empresas dele
        if ($nivel === 'admin') {
            return [
                'where' => "({$aliasTabela}.{$colunaCriador} = :filtro_uid 
                    OR {$aliasTabela}.id IN (
                        SELECT re.id_recurso FROM tb_recurso_empresas re
                        WHERE re.tipo_recurso = :filtro_tipo
                        AND re.id_empresa IN (SELECT ue.id_empresa FROM tb_usuario_empresas ue WHERE ue.id_usuario = :filtro_uid2)
                    ))",
                'params' => [
                    ':filtro_uid' => $idUsuario,
                    ':filtro_uid2' => $idUsuario,
                    ':filtro_tipo' => $tipoRecurso,
                ]
            ];
        }

        // Desenvolvedor: vê o que criou + o que compartilharam com ele + recursos dos projetos dele
        if ($nivel === 'desenvolvedor') {
            return [
                'where' => "({$aliasTabela}.{$colunaCriador} = :filtro_uid 
                    OR {$aliasTabela}.id IN (
                        SELECT c.id_recurso FROM tb_compartilhamentos c
                        WHERE c.tipo_recurso = :filtro_tipo
                        AND c.id_usuario_destino = :filtro_uid2
                        AND (c.id_recurso = {$aliasTabela}.id OR c.id_recurso IS NULL)
                    )
                    OR {$aliasTabela}.id IN (
                        SELECT rp.id_recurso FROM tb_recurso_projetos rp
                        WHERE rp.tipo_recurso = :filtro_tipo2
                        AND rp.id_projeto IN (SELECT up.id_projeto FROM tb_usuario_projetos up WHERE up.id_usuario = :filtro_uid3)
                    ))",
                'params' => [
                    ':filtro_uid' => $idUsuario,
                    ':filtro_uid2' => $idUsuario,
                    ':filtro_uid3' => $idUsuario,
                    ':filtro_tipo' => $tipoRecurso,
                    ':filtro_tipo2' => $tipoRecurso,
                ]
            ];
        }

        // Operador: vê recursos dos projetos dele (somente leitura)
        if ($nivel === 'operador') {
            return [
                'where' => "({$aliasTabela}.{$colunaCriador} = :filtro_uid 
                    OR {$aliasTabela}.id IN (
                        SELECT rp.id_recurso FROM tb_recurso_projetos rp
                        WHERE rp.tipo_recurso = :filtro_tipo
                        AND rp.id_projeto IN (SELECT up.id_projeto FROM tb_usuario_projetos up WHERE up.id_usuario = :filtro_uid2)
                    ))",
                'params' => [
                    ':filtro_uid' => $idUsuario,
                    ':filtro_uid2' => $idUsuario,
                    ':filtro_tipo' => $tipoRecurso,
                ]
            ];
        }

        return ['where' => 'FALSE', 'params' => []];
    }

    /**
     * Gera filtro de visibilidade usando parâmetros posicionais (?) 
     * Útil para UNION ALL queries que já usam ? placeholders
     * @return array ['where' => string, 'params' => array (indexed)]
     */
    public static function filtroVisibilidadePosicional(string $tipoRecurso, string $aliasTabela, string $colunaCriador = 'criado_por'): array
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();
        if (!$usuario || !$idUsuario) return ['where' => 'FALSE', 'params' => []];
        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        if ($nivel === 'super_admin') return ['where' => 'TRUE', 'params' => []];

        if ($nivel === 'admin') {
            return [
                'where' => "({$aliasTabela}.{$colunaCriador} = ? 
                    OR {$aliasTabela}.id IN (
                        SELECT re.id_recurso FROM tb_recurso_empresas re
                        WHERE re.tipo_recurso = ?
                        AND re.id_empresa IN (SELECT ue.id_empresa FROM tb_usuario_empresas ue WHERE ue.id_usuario = ?)
                    ))",
                'params' => [$idUsuario, $tipoRecurso, $idUsuario]
            ];
        }

        if ($nivel === 'desenvolvedor') {
            return [
                'where' => "({$aliasTabela}.{$colunaCriador} = ? 
                    OR {$aliasTabela}.id IN (
                        SELECT c.id_recurso FROM tb_compartilhamentos c
                        WHERE c.tipo_recurso = ? AND c.id_usuario_destino = ?
                    )
                    OR {$aliasTabela}.id IN (
                        SELECT rp.id_recurso FROM tb_recurso_projetos rp
                        WHERE rp.tipo_recurso = ?
                        AND rp.id_projeto IN (SELECT up.id_projeto FROM tb_usuario_projetos up WHERE up.id_usuario = ?)
                    ))",
                'params' => [$idUsuario, $tipoRecurso, $idUsuario, $tipoRecurso, $idUsuario]
            ];
        }

        // operador: vê recursos dos projetos dele (somente leitura)
        return [
            'where' => "({$aliasTabela}.{$colunaCriador} = ? 
                OR {$aliasTabela}.id IN (
                    SELECT rp.id_recurso FROM tb_recurso_projetos rp
                    WHERE rp.tipo_recurso = ?
                    AND rp.id_projeto IN (SELECT up.id_projeto FROM tb_usuario_projetos up WHERE up.id_usuario = ?)
                ))",
            'params' => [$idUsuario, $tipoRecurso, $idUsuario]
        ];
    }

    // ================================================================
    // Verificações de permissão sobre recurso específico
    // ================================================================

    /**
     * Busca o ID do criador de um recurso no banco de dados
     */
    private static function obterCriadorRecurso(string $tipoRecurso, int $idRecurso): ?int
    {
        $mapa = [
            'conexao'     => ['tabela' => 'tb_perfis_conexao',  'coluna' => 'criado_por'],
            'rotina'      => ['tabela' => 'tb_rotinas',         'coluna' => 'id_usuario_criador'],
            'pipeline'    => ['tabela' => 'tb_pipelines',       'coluna' => 'criado_por'],
            'api'         => ['tabela' => 'tb_api_externas',    'coluna' => 'criado_por'],
            'api_externa' => ['tabela' => 'tb_api_externas',    'coluna' => 'criado_por'],
            'workflow'    => ['tabela' => 'tb_workflows',       'coluna' => 'criado_por'],
        ];

        if (!isset($mapa[$tipoRecurso])) return null;

        $cfg = $mapa[$tipoRecurso];
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT {$cfg['coluna']} FROM {$cfg['tabela']} WHERE id = ?");
        $stmt->execute([$idRecurso]);
        $val = $stmt->fetchColumn();

        return $val !== false ? (int)$val : null;
    }

    /**
     * Verifica se o usuário pode VER um recurso específico
     */
    public static function podeVerRecurso(string $tipoRecurso, int $idRecurso, ?int $idCriador = null): bool
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();

        if (!$usuario || !$idUsuario) return false;

        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        // Super admin vê tudo
        if ($nivel === 'super_admin') return true;

        // Buscar criador do banco se não informado
        if ($idCriador === null) {
            $idCriador = self::obterCriadorRecurso($tipoRecurso, $idRecurso);
        }

        // É o criador
        if ($idCriador !== null && $idCriador === $idUsuario) return true;

        // Admin: verifica se o recurso pertence a alguma empresa dele
        if ($nivel === 'admin') {
            $db = Database::getConexao();
            $stmt = $db->prepare("
                SELECT 1 FROM tb_recurso_empresas re
                JOIN tb_usuario_empresas ue ON ue.id_empresa = re.id_empresa AND ue.id_usuario = :uid
                WHERE re.tipo_recurso = :tipo AND re.id_recurso = :rid
            ");
            $stmt->execute([':uid' => $idUsuario, ':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
            return (bool)$stmt->fetchColumn();
        }

        // Desenvolvedor: verifica se foi compartilhado com ele ou pertence a projetos dele
        if ($nivel === 'desenvolvedor') {
            $db = Database::getConexao();
            // Verifica compartilhamento específico ou total
            $stmt = $db->prepare("
                SELECT 1 FROM tb_compartilhamentos
                WHERE tipo_recurso = :tipo
                AND id_usuario_destino = :uid
                AND (id_recurso = :rid OR id_recurso IS NULL)
            ");
            $stmt->execute([':tipo' => $tipoRecurso, ':uid' => $idUsuario, ':rid' => $idRecurso]);
            if ($stmt->fetchColumn()) return true;

            // Verifica se pertence a algum projeto do desenvolvedor
            $stmt2 = $db->prepare("
                SELECT 1 FROM tb_recurso_projetos rp
                JOIN tb_usuario_projetos up ON up.id_projeto = rp.id_projeto AND up.id_usuario = :uid
                WHERE rp.tipo_recurso = :tipo AND rp.id_recurso = :rid
            ");
            $stmt2->execute([':uid' => $idUsuario, ':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
            return (bool)$stmt2->fetchColumn();
        }

        // Operador: verifica se pertence a algum projeto dele
        if ($nivel === 'operador') {
            $db = Database::getConexao();
            $stmt = $db->prepare("
                SELECT 1 FROM tb_recurso_projetos rp
                JOIN tb_usuario_projetos up ON up.id_projeto = rp.id_projeto AND up.id_usuario = :uid
                WHERE rp.tipo_recurso = :tipo AND rp.id_recurso = :rid
            ");
            $stmt->execute([':uid' => $idUsuario, ':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
            return (bool)$stmt->fetchColumn();
        }

        return false;
    }

    /**
     * Verifica se o usuário pode MODIFICAR (editar/deletar) um recurso
     */
    public static function podeModificarRecurso(string $tipoRecurso, int $idRecurso, ?int $idCriador = null): bool
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();

        if (!$usuario || !$idUsuario) return false;

        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        // Operador NUNCA pode modificar
        if ($nivel === 'operador') return false;

        // Super admin pode tudo
        if ($nivel === 'super_admin') return true;

        // Buscar criador do banco se não informado
        if ($idCriador === null) {
            $idCriador = self::obterCriadorRecurso($tipoRecurso, $idRecurso);
        }

        // Admin: pode modificar recursos das empresas dele
        if ($nivel === 'admin') {
            if ($idCriador !== null && $idCriador === $idUsuario) return true;
            $db = Database::getConexao();
            $stmt = $db->prepare("
                SELECT 1 FROM tb_recurso_empresas re
                JOIN tb_usuario_empresas ue ON ue.id_empresa = re.id_empresa AND ue.id_usuario = :uid
                WHERE re.tipo_recurso = :tipo AND re.id_recurso = :rid
            ");
            $stmt->execute([':uid' => $idUsuario, ':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
            return (bool)$stmt->fetchColumn();
        }

        // Desenvolvedor: pode modificar o que ele criou, compartilhado com 'editar', ou pertence a projetos dele
        if ($nivel === 'desenvolvedor') {
            if ($idCriador !== null && $idCriador === $idUsuario) return true;
            $db = Database::getConexao();
            // Compartilhamento com permissão editar
            $stmt = $db->prepare("
                SELECT 1 FROM tb_compartilhamentos
                WHERE tipo_recurso = :tipo
                AND id_usuario_destino = :uid
                AND (id_recurso = :rid OR id_recurso IS NULL)
                AND permissao = 'editar'
            ");
            $stmt->execute([':tipo' => $tipoRecurso, ':uid' => $idUsuario, ':rid' => $idRecurso]);
            if ($stmt->fetchColumn()) return true;

            // Pertence a algum projeto do desenvolvedor
            $stmt2 = $db->prepare("
                SELECT 1 FROM tb_recurso_projetos rp
                JOIN tb_usuario_projetos up ON up.id_projeto = rp.id_projeto AND up.id_usuario = :uid
                WHERE rp.tipo_recurso = :tipo AND rp.id_recurso = :rid
            ");
            $stmt2->execute([':uid' => $idUsuario, ':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
            return (bool)$stmt2->fetchColumn();
        }

        return false;
    }

    /**
     * Verifica se o usuário pode EXECUTAR um recurso (rotina, workflow, pipeline)
     */
    public static function podeExecutarRecurso(string $tipoRecurso, int $idRecurso, ?int $idCriador = null): bool
    {
        $usuario = AuthMiddleware::obterUsuario();
        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        // Operador NUNCA pode executar
        if ($nivel === 'operador') return false;

        // Se pode ver, pode executar (para admin, dev, super_admin)
        return self::podeVerRecurso($tipoRecurso, $idRecurso, $idCriador);
    }

    // ================================================================
    // Gerenciamento de compartilhamentos
    // ================================================================

    /**
     * Compartilha um recurso com outro usuário
     */
    public static function compartilharRecurso(
        string $tipoRecurso,
        ?int $idRecurso,
        int $idUsuarioDestino,
        string $permissao = 'ver'
    ): bool {
        $idUsuarioDono = AuthMiddleware::obterUsuarioId();
        if (!$idUsuarioDono) return false;

        $db = Database::getConexao();
        $stmt = $db->prepare("
            INSERT INTO tb_compartilhamentos (tipo_recurso, id_recurso, id_usuario_dono, id_usuario_destino, permissao)
            VALUES (:tipo, :rid, :dono, :destino, :perm)
            ON CONFLICT (tipo_recurso, id_recurso, id_usuario_dono, id_usuario_destino)
            DO UPDATE SET permissao = :perm2, data_compartilhamento = NOW()
        ");

        return $stmt->execute([
            ':tipo' => $tipoRecurso,
            ':rid' => $idRecurso,
            ':dono' => $idUsuarioDono,
            ':destino' => $idUsuarioDestino,
            ':perm' => $permissao,
            ':perm2' => $permissao,
        ]);
    }

    /**
     * Remove compartilhamento
     */
    public static function removerCompartilhamento(string $tipoRecurso, ?int $idRecurso, int $idUsuarioDestino): bool
    {
        $idUsuarioDono = AuthMiddleware::obterUsuarioId();
        if (!$idUsuarioDono) return false;

        $db = Database::getConexao();

        if ($idRecurso === null) {
            $stmt = $db->prepare("
                DELETE FROM tb_compartilhamentos 
                WHERE tipo_recurso = :tipo AND id_recurso IS NULL
                AND id_usuario_dono = :dono AND id_usuario_destino = :destino
            ");
        } else {
            $stmt = $db->prepare("
                DELETE FROM tb_compartilhamentos 
                WHERE tipo_recurso = :tipo AND id_recurso = :rid
                AND id_usuario_dono = :dono AND id_usuario_destino = :destino
            ");
        }

        $params = [
            ':tipo' => $tipoRecurso,
            ':dono' => $idUsuarioDono,
            ':destino' => $idUsuarioDestino,
        ];
        if ($idRecurso !== null) {
            $params[':rid'] = $idRecurso;
        }

        return $stmt->execute($params);
    }

    /**
     * Lista compartilhamentos de um recurso
     */
    public static function listarCompartilhamentos(string $tipoRecurso, ?int $idRecurso = null): array
    {
        $idUsuario = AuthMiddleware::obterUsuarioId();
        if (!$idUsuario) return [];

        $db = Database::getConexao();

        $sql = "SELECT c.*, u.nome_usuario as usuario_destino_nome
                FROM tb_compartilhamentos c
                JOIN tb_usuarios u ON u.id = c.id_usuario_destino
                WHERE c.tipo_recurso = :tipo AND c.id_usuario_dono = :dono";
        $params = [':tipo' => $tipoRecurso, ':dono' => $idUsuario];

        if ($idRecurso !== null) {
            $sql .= " AND (c.id_recurso = :rid OR c.id_recurso IS NULL)";
            $params[':rid'] = $idRecurso;
        }

        $sql .= " ORDER BY c.data_compartilhamento DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ================================================================
    // Associação de recursos a empresas/projetos
    // ================================================================

    /**
     * Associa um recurso a empresas
     */
    public static function associarRecursoEmpresas(string $tipoRecurso, int $idRecurso, array $idsEmpresas): void
    {
        $db = Database::getConexao();

        // Remove associações anteriores
        $stmt = $db->prepare("DELETE FROM tb_recurso_empresas WHERE tipo_recurso = :tipo AND id_recurso = :rid");
        $stmt->execute([':tipo' => $tipoRecurso, ':rid' => $idRecurso]);

        // Insere novas
        $stmt = $db->prepare("INSERT INTO tb_recurso_empresas (tipo_recurso, id_recurso, id_empresa) VALUES (:tipo, :rid, :eid)");
        foreach ($idsEmpresas as $idEmpresa) {
            $stmt->execute([':tipo' => $tipoRecurso, ':rid' => $idRecurso, ':eid' => (int)$idEmpresa]);
        }
    }

    /**
     * Associa um recurso a projetos
     */
    public static function associarRecursoProjetos(string $tipoRecurso, int $idRecurso, array $idsProjetos): void
    {
        $db = Database::getConexao();

        $stmt = $db->prepare("DELETE FROM tb_recurso_projetos WHERE tipo_recurso = :tipo AND id_recurso = :rid");
        $stmt->execute([':tipo' => $tipoRecurso, ':rid' => $idRecurso]);

        $stmt = $db->prepare("INSERT INTO tb_recurso_projetos (tipo_recurso, id_recurso, id_projeto) VALUES (:tipo, :rid, :pid)");
        foreach ($idsProjetos as $idProjeto) {
            $stmt->execute([':tipo' => $tipoRecurso, ':rid' => $idRecurso, ':pid' => (int)$idProjeto]);
        }
    }

    /**
     * Obtém empresas de um recurso
     */
    public static function obterEmpresasDoRecurso(string $tipoRecurso, int $idRecurso): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT id_empresa FROM tb_recurso_empresas WHERE tipo_recurso = :tipo AND id_recurso = :rid");
        $stmt->execute([':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Obtém projetos de um recurso
     */
    public static function obterProjetosDoRecurso(string $tipoRecurso, int $idRecurso): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT id_projeto FROM tb_recurso_projetos WHERE tipo_recurso = :tipo AND id_recurso = :rid");
        $stmt->execute([':tipo' => $tipoRecurso, ':rid' => $idRecurso]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    // ================================================================
    // Utilitários para administração de usuários
    // ================================================================

    /**
     * Verifica se o admin logado pode gerenciar determinado usuário
     */
    public static function adminPodeGerenciarUsuario(int $idUsuarioAlvo): bool
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();

        if (!$usuario || !$idUsuario) return false;
        if (self::ehSuperAdmin()) return true;

        // Admin não pode gerenciar super_admin ou outros admins de empresas diferentes
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT nivel_acesso FROM tb_usuarios WHERE id = :id");
        $stmt->execute([':id' => $idUsuarioAlvo]);
        $nivelAlvo = $stmt->fetchColumn();

        // Não pode gerenciar super_admin
        if ($nivelAlvo === 'super_admin') return false;

        // Não pode gerenciar outro admin (somente super_admin pode)
        if ($nivelAlvo === 'admin') return false;

        // Admin pode gerenciar dev/operador se compartilham empresa
        if (($usuario['nivel_acesso'] ?? '') === 'admin') {
            $empresasAdmin = self::obterEmpresasDoUsuario($idUsuario);
            $empresasAlvo = self::obterEmpresasDoUsuario($idUsuarioAlvo);
            return !empty(array_intersect($empresasAdmin, $empresasAlvo));
        }

        return false;
    }

    /**
     * Retorna os papéis que o usuário logado pode atribuir a outros
     */
    public static function papeisDisponiveis(): array
    {
        $usuario = AuthMiddleware::obterUsuario();
        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        if ($nivel === 'super_admin') {
            return ['admin', 'desenvolvedor', 'operador'];
        }
        if ($nivel === 'admin') {
            return ['desenvolvedor', 'operador'];
        }
        return [];
    }

    /**
     * Retorna empresas disponíveis para o admin atribuir a um novo usuário
     */
    public static function empresasDisponiveisParaAdmin(): array
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();

        if (!$usuario || !$idUsuario) return [];

        $db = Database::getConexao();

        // Super admin: todas as empresas ativas
        if (self::ehSuperAdmin()) {
            $stmt = $db->query("SELECT id, nome FROM tb_empresas WHERE ativa = true ORDER BY nome");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Admin e desenvolvedor: apenas as empresas que ele faz parte
        $stmt = $db->prepare("
            SELECT e.id, e.nome FROM tb_empresas e
            JOIN tb_usuario_empresas ue ON ue.id_empresa = e.id AND ue.id_usuario = :uid
            WHERE e.ativa = true ORDER BY e.nome
        ");
        $stmt->execute([':uid' => $idUsuario]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retorna projetos disponíveis para o admin atribuir
     */
    public static function projetosDisponiveisParaAdmin(?array $idsEmpresas = null): array
    {
        $usuario = AuthMiddleware::obterUsuario();
        $idUsuario = AuthMiddleware::obterUsuarioId();

        if (!$usuario || !$idUsuario) return [];

        $db = Database::getConexao();

        if (self::ehSuperAdmin()) {
            if ($idsEmpresas) {
                $placeholders = implode(',', array_fill(0, count($idsEmpresas), '?'));
                $stmt = $db->prepare("SELECT p.id, p.nome, p.id_empresa, e.nome as empresa_nome FROM tb_projetos p JOIN tb_empresas e ON e.id = p.id_empresa WHERE p.ativo = true AND p.id_empresa IN ($placeholders) ORDER BY e.nome, p.nome");
                $stmt->execute($idsEmpresas);
            } else {
                $stmt = $db->query("SELECT p.id, p.nome, p.id_empresa, e.nome as empresa_nome FROM tb_projetos p JOIN tb_empresas e ON e.id = p.id_empresa WHERE p.ativo = true ORDER BY e.nome, p.nome");
            }
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        // Desenvolvedor: apenas seus projetos específicos (via tb_usuario_projetos)
        if ($nivel === 'desenvolvedor') {
            $sql = "
                SELECT p.id, p.nome, p.id_empresa, e.nome as empresa_nome 
                FROM tb_projetos p
                JOIN tb_empresas e ON e.id = p.id_empresa
                JOIN tb_usuario_projetos up ON up.id_projeto = p.id AND up.id_usuario = :uid
                WHERE p.ativo = true";
            $params = [':uid' => $idUsuario];
            if ($idsEmpresas) {
                $placeholders = implode(',', array_fill(0, count($idsEmpresas), '?'));
                // Rebuild with positional for empresa filter
                $sql = "
                    SELECT p.id, p.nome, p.id_empresa, e.nome as empresa_nome 
                    FROM tb_projetos p
                    JOIN tb_empresas e ON e.id = p.id_empresa
                    JOIN tb_usuario_projetos up ON up.id_projeto = p.id AND up.id_usuario = ?
                    WHERE p.ativo = true AND p.id_empresa IN ($placeholders)";
                $params = array_merge([$idUsuario], $idsEmpresas);
                $sql .= " ORDER BY e.nome, p.nome";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            } else {
                $sql .= " ORDER BY e.nome, p.nome";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            }
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Admin: todos os projetos das empresas dele
        $stmt = $db->prepare("
            SELECT p.id, p.nome, p.id_empresa, e.nome as empresa_nome 
            FROM tb_projetos p
            JOIN tb_empresas e ON e.id = p.id_empresa
            JOIN tb_usuario_empresas ue ON ue.id_empresa = p.id_empresa AND ue.id_usuario = :uid
            WHERE p.ativo = true ORDER BY e.nome, p.nome
        ");
        $stmt->execute([':uid' => $idUsuario]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
