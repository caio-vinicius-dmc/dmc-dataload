<?php
namespace App\Utils;

class DriverInstaller
{
    private static $tempDir = 'C:\xampp\tmp\driver_install';
    private static $logFile = 'C:\xampp\htdocs\DMC-DATALOAD\logs\driver_install.log';

    /**
     * Instala o driver automaticamente
     * @param string $tipoBanco Tipo do banco (oracle, sqlserver)
     * @param bool $autoDownload Se true, faz download sem pedir aprovação
     */
    public static function install(string $tipoBanco, bool $autoDownload = false): array
    {
        self::log("=== Iniciando instalação do driver: $tipoBanco (autoDownload: " . ($autoDownload ? 'true' : 'false') . ") ===");
        
        // Verificar se já está instalado
        if (DriverChecker::isDriverAvailable($tipoBanco)) {
            return [
                'sucesso' => true,
                'mensagem' => 'Driver já está instalado e disponível!',
                'ja_instalado' => true
            ];
        }

        // Criar diretório temporário
        if (!is_dir(self::$tempDir)) {
            mkdir(self::$tempDir, 0777, true);
        }

        try {
            switch ($tipoBanco) {
                case 'oracle':
                    return self::installOracle($autoDownload);
                case 'sqlserver':
                    return self::installSqlServer($autoDownload);
                case 'mysql':
                case 'mariadb':
                    return self::installMySQL($autoDownload);
                case 'postgres':
                    return self::installPostgres($autoDownload);
                default:
                    return [
                        'sucesso' => false,
                        'mensagem' => 'Instalação automática não disponível para este banco de dados'
                    ];
            }
        } catch (\Exception $e) {
            self::log("ERRO: " . $e->getMessage());
            return [
                'sucesso' => false,
                'mensagem' => 'Erro durante instalação: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }

    /**
     * Instala Oracle OCI8 + Instant Client
     */
    private static function installOracle(bool $autoDownload = false): array
    {
        self::log("Instalando Oracle OCI8...");
        
        $phpInfo = DriverChecker::getPhpInfo();
        $phpVersion = PHP_VERSION;
        $phpVersionShort = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        $isThreadSafe = ZEND_THREAD_SAFE;
        $arch = PHP_INT_SIZE * 8; // 32 ou 64
        
        self::log("PHP Version: $phpVersion ($phpVersionShort)");
        self::log("Architecture: {$arch}-bit");
        self::log("Thread Safe: " . ($isThreadSafe ? 'Yes' : 'No'));

        // URLs para download (Oracle Instant Client Basic)
        // Nota: Oracle requer login, então vamos usar uma abordagem alternativa
        $steps = [];
        $steps[] = "Detectado PHP $phpVersionShort {$arch}-bit " . ($isThreadSafe ? 'TS' : 'NTS');

        // Verificar se as DLLs já existem no php/ext
        $extDir = ini_get('extension_dir') ?: PHP_EXTENSION_DIR;
        $oci8Dll = $extDir . DIRECTORY_SEPARATOR . 'php_oci8_19.dll';
        $pdoOciDll = $extDir . DIRECTORY_SEPARATOR . 'php_pdo_oci.dll';

        if (file_exists($oci8Dll) || file_exists($pdoOciDll)) {
            // DLLs existem, apenas habilitar no php.ini
            $steps[] = "DLLs encontradas, habilitando no php.ini...";
            $phpIni = php_ini_loaded_file();
            
            $result = self::enableExtensionInPhpIni($phpIni, ['extension=oci8_19', 'extension=pdo_oci']);
            
            if ($result['sucesso']) {
                $steps[] = "Extensões habilitadas no php.ini";
                $steps[] = "Verificando Instant Client...";
                
                // Verificar se Instant Client está no PATH
                $instantClientCheck = self::checkInstantClient();
                if (!$instantClientCheck['encontrado']) {
                    $steps[] = "⚠️ Oracle Instant Client não encontrado no PATH";
                    $steps[] = "Iniciando download e instalação automática do Instant Client...";
                    
                    // Baixar e instalar automaticamente
                    $instantClientResult = self::downloadAndInstallInstantClient($arch);
                    
                    if (!$instantClientResult['sucesso']) {
                        // Falhou - retornar instruções manuais
                        return [
                            'sucesso' => false,
                            'mensagem' => $instantClientResult['mensagem'],
                            'steps' => array_merge($steps, $instantClientResult['steps']),
                            'requer_instant_client' => true,
                            'instant_client_url' => 'https://www.oracle.com/database/technologies/instant-client/winx64-64-downloads.html',
                            'path_manual' => $instantClientResult['path_manual'] ?? null,
                            'instrucoes_path' => $instantClientResult['instrucoes_path'] ?? null
                        ];
                    }
                    
                    // Sucesso na instalação do Instant Client
                    $steps = array_merge($steps, $instantClientResult['steps']);
                    $steps[] = "✓ Oracle Instant Client instalado automaticamente!";
                }
                
                $steps[] = "✓ Instant Client encontrado: " . ($instantClientCheck['path'] ?? $instantClientResult['path']);
                $steps[] = "Reiniciando Apache...";
                
                $restart = self::restartApache();
                if ($restart['sucesso']) {
                    $steps[] = "✓ Apache reiniciado com sucesso";
                    
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Driver Oracle instalado completamente! Aguarde alguns segundos e teste a conexão.',
                        'steps' => $steps,
                        'requer_reload' => true,
                        'instant_client_instalado' => true
                    ];
                } else {
                    $steps[] = "⚠️ Não foi possível reiniciar o Apache automaticamente";
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Configurado! Por favor, reinicie o XAMPP manualmente.',
                        'steps' => $steps,
                        'requer_restart_manual' => true,
                        'instant_client_instalado' => true
                    ];
                }
            }
        }

        // DLLs não encontradas - precisamos baixar
        $steps[] = "DLLs não encontradas";
        
        // URL para PECL do PHP (onde podemos baixar extensões)
        $peclUrl = self::getPeclOracleUrl($phpVersionShort, $arch, $isThreadSafe);
        
        if (!$peclUrl) {
            return [
                'sucesso' => false,
                'mensagem' => 'Não foi possível determinar URL de download para sua versão do PHP',
                'steps' => $steps,
                'phpVersion' => $phpVersion,
                'manual_required' => true
            ];
        }

        // Se não tiver aprovação, retornar info para solicitar
        if (!$autoDownload) {
            $steps[] = "URL de download preparada";
            return [
                'sucesso' => false,
                'mensagem' => 'Download automático requer aprovação do usuário',
                'steps' => $steps,
                'requer_download' => true,
                'download_info' => [
                    'url' => $peclUrl,
                    'tipo' => 'oracle',
                    'arch' => $arch,
                    'php_version' => $phpVersionShort,
                    'tamanho_estimado' => '5 MB'
                ]
            ];
        }

        // Usuário aprovou - fazer download e instalação completa
        $steps[] = "Iniciando download automático...";
        $steps[] = "Baixando de: " . basename($peclUrl);
        
        $zipFile = self::$tempDir . DIRECTORY_SEPARATOR . 'oci8.zip';
        $extractDir = self::$tempDir . DIRECTORY_SEPARATOR . 'oci8_extracted';
        
        $download = self::downloadFile($peclUrl, $zipFile);
        
        if (!$download['sucesso']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao baixar driver OCI8: ' . $download['mensagem'],
                'steps' => $steps,
                'url' => $peclUrl
            ];
        }

        $steps[] = "✓ Download concluído: " . self::formatFileSize(filesize($zipFile));
        $steps[] = "Extraindo arquivos...";
        
        $extract = self::extractZip($zipFile, $extractDir);
        if (!$extract['sucesso']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao extrair arquivo: ' . $extract['mensagem'],
                'steps' => $steps
            ];
        }

        $steps[] = "✓ Arquivos extraídos";
        $steps[] = "Copiando DLLs para: $extDir";
        
        // Procurar e copiar DLLs
        $dllFiles = self::findFiles($extractDir, '*.dll');
        $dllsCopied = 0;
        
        foreach ($dllFiles as $dllFile) {
            $dllName = basename($dllFile);
            $destFile = $extDir . DIRECTORY_SEPARATOR . $dllName;
            
            if (copy($dllFile, $destFile)) {
                $steps[] = "  ✓ Copiado: $dllName";
                $dllsCopied++;
            } else {
                $steps[] = "  ✗ Erro ao copiar: $dllName";
            }
        }

        if ($dllsCopied === 0) {
            return [
                'sucesso' => false,
                'mensagem' => 'Nenhuma DLL foi copiada',
                'steps' => $steps
            ];
        }

        $steps[] = "✓ $dllsCopied DLL(s) instalada(s)";
        
        // Limpar temporários
        @unlink($zipFile);
        self::recursiveDelete($extractDir);
        $steps[] = "✓ Arquivos temporários removidos";

        // Habilitar no php.ini
        $steps[] = "Habilitando extensões no php.ini...";
        $phpIni = php_ini_loaded_file();
        $enableResult = self::enableExtensionInPhpIni($phpIni, ['extension=oci8_19', 'extension=pdo_oci']);
        
        if (!$enableResult['sucesso']) {
            $steps[] = "⚠️ Erro ao modificar php.ini: " . $enableResult['mensagem'];
        } else {
            $steps[] = "✓ php.ini atualizado";
        }

        // Verificar Instant Client e instalar se necessário
        $steps[] = "Verificando Oracle Instant Client...";
        $instantClientCheck = self::checkInstantClient();
        
        if (!$instantClientCheck['encontrado']) {
            $steps[] = "⚠️ Instant Client não encontrado";
            $steps[] = "Iniciando download e instalação do Oracle Instant Client...";
            
            $instantClientResult = self::downloadAndInstallInstantClient($arch);
            
            if (!$instantClientResult['sucesso']) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Driver OCI8 instalado, mas falha no Instant Client: ' . $instantClientResult['mensagem'],
                    'steps' => array_merge($steps, $instantClientResult['steps']),
                    'requer_instant_client' => true,
                    'instant_client_url' => 'https://www.oracle.com/database/technologies/instant-client/winx64-64-downloads.html',
                    'path_manual' => $instantClientResult['path_manual'] ?? null,
                    'instrucoes_path' => $instantClientResult['instrucoes_path'] ?? null
                ];
            }
            
            $steps = array_merge($steps, $instantClientResult['steps']);
            $steps[] = "✓ Oracle Instant Client instalado automaticamente!";
        } else {
            $steps[] = "✓ Instant Client encontrado: " . $instantClientCheck['path'];
        }

        // Reiniciar Apache
        $steps[] = "Reiniciando Apache...";
        $restart = self::restartApache();
        
        if ($restart['sucesso']) {
            $steps[] = "✓ Apache reiniciado com sucesso";
            
            return [
                'sucesso' => true,
                'mensagem' => 'Driver Oracle instalado completamente! Teste a conexão em alguns segundos.',
                'steps' => $steps,
                'requer_reload' => true,
                'instant_client_instalado' => !$instantClientCheck['encontrado']
            ];
        } else {
            $steps[] = "⚠️ Não foi possível reiniciar Apache automaticamente";
            return [
                'sucesso' => true,
                'mensagem' => 'Instalação concluída! Reinicie o XAMPP manualmente.',
                'steps' => $steps,
                'requer_restart_manual' => true,
                'instant_client_instalado' => !$instantClientCheck['encontrado']
            ];
        }
    }

    /**
     * Instala SQL Server drivers
     */
    private static function installSqlServer(bool $autoDownload = false): array
    {
        self::log("Instalando SQL Server drivers...");
        
        $phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        $arch = PHP_INT_SIZE * 8;
        $isThreadSafe = ZEND_THREAD_SAFE;
        
        $steps = [];
        $steps[] = "Detectado PHP $phpVersion {$arch}-bit " . ($isThreadSafe ? 'TS' : 'NTS');

        // Verificar se DLLs existem
        $extDir = PHP_EXTENSION_DIR;
        $sqlsrvDlls = glob($extDir . DIRECTORY_SEPARATOR . 'php_sqlsrv_*.dll');
        $pdoSqlsrvDlls = glob($extDir . DIRECTORY_SEPARATOR . 'php_pdo_sqlsrv_*.dll');

        if (!empty($sqlsrvDlls) && !empty($pdoSqlsrvDlls)) {
            $steps[] = "DLLs encontradas, habilitando no php.ini...";
            
            // Pegar o nome da DLL mais recente
            $sqlsrvDll = basename(end($sqlsrvDlls));
            $pdoSqlsrvDll = basename(end($pdoSqlsrvDlls));
            
            $phpIni = php_ini_loaded_file();
            $result = self::enableExtensionInPhpIni($phpIni, [
                "extension=$sqlsrvDll",
                "extension=$pdoSqlsrvDll"
            ]);
            
            if ($result['sucesso']) {
                $steps[] = "Extensões habilitadas: $sqlsrvDll, $pdoSqlsrvDll";
                $steps[] = "Reiniciando Apache...";
                
                $restart = self::restartApache();
                if ($restart['sucesso']) {
                    $steps[] = "✓ Apache reiniciado com sucesso";
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Driver SQL Server instalado! Aguarde alguns segundos e teste novamente.',
                        'steps' => $steps,
                        'requer_reload' => true
                    ];
                } else {
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Configurado! Por favor, reinicie o XAMPP manualmente.',
                        'steps' => $steps,
                        'requer_restart_manual' => true
                    ];
                }
            }
        }

        // DLLs não encontradas - download automático
        $downloadUrl = self::getSqlServerDriverUrl($phpVersion, $arch, $isThreadSafe);

        if (!$downloadUrl) {
            return [
                'sucesso' => false,
                'mensagem' => 'Não foi possível determinar a URL de download para PHP ' . $phpVersion,
                'steps' => $steps,
                'manual_required' => true
            ];
        }

        // Se não tiver aprovação, retornar info para solicitar
        if (!$autoDownload) {
            $steps[] = "URL de download preparada";
            return [
                'sucesso' => false,
                'mensagem' => 'Download automático requer aprovação do usuário',
                'steps' => $steps,
                'requer_download' => true,
                'download_info' => [
                    'url' => $downloadUrl,
                    'tipo' => 'sqlserver',
                    'arch' => $arch,
                    'php_version' => $phpVersion,
                    'tamanho_estimado' => '2 MB'
                ]
            ];
        }

        // Download e instalação
        $steps[] = "Baixando Microsoft Drivers for PHP for SQL Server...";
        
        $zipFile = self::$tempDir . DIRECTORY_SEPARATOR . 'sqlsrv.zip';
        $extractDir = self::$tempDir . DIRECTORY_SEPARATOR . 'sqlsrv_extracted';
        
        $download = self::downloadFile($downloadUrl, $zipFile);
        
        if (!$download['sucesso']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao baixar driver SQL Server. Faça download manualmente: https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server',
                'steps' => $steps
            ];
        }

        $steps[] = "✓ Download concluído: " . self::formatFileSize(filesize($zipFile));
        $steps[] = "Extraindo arquivos...";
        
        $extract = self::extractZip($zipFile, $extractDir);
        if (!$extract['sucesso']) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao extrair arquivo', 'steps' => $steps];
        }

        $steps[] = "✓ Arquivos extraídos";
        
        // Encontrar DLLs compatíveis com a versão do PHP
        $ts = $isThreadSafe ? 'ts' : 'nts';
        $allDlls = self::findFiles($extractDir, '*.dll');
        $dllsCopied = 0;

        // Filtrar DLLs compatíveis: php_sqlsrv_XX_ts_xXX.dll e php_pdo_sqlsrv_XX_ts_xXX.dll
        $phpMajorMinor = PHP_MAJOR_VERSION . PHP_MINOR_VERSION; // ex: "83"
        $archSuffix = $arch === 64 ? 'x64' : 'x86';

        foreach ($allDlls as $dllFile) {
            $dllName = basename($dllFile);
            // Apenas copiar DLLs que correspondem à versão/arch/TS do PHP
            $isMatch = (
                stripos($dllName, $phpMajorMinor) !== false &&
                stripos($dllName, $ts) !== false &&
                stripos($dllName, $archSuffix) !== false
            );
            // Fallback: se não achou match exato, copiar todas as DLLs que tenham a versão do PHP
            if (!$isMatch && stripos($dllName, $phpMajorMinor) !== false && stripos($dllName, $ts) !== false) {
                $isMatch = true;
            }
            if ($isMatch) {
                $destFile = $extDir . DIRECTORY_SEPARATOR . $dllName;
                if (copy($dllFile, $destFile)) {
                    $steps[] = "  ✓ Copiado: $dllName";
                    $dllsCopied++;
                }
            }
        }

        if ($dllsCopied === 0) {
            // Tentar copiar qualquer DLL encontrada
            foreach ($allDlls as $dllFile) {
                $dllName = basename($dllFile);
                if (stripos($dllName, 'sqlsrv') !== false) {
                    $destFile = $extDir . DIRECTORY_SEPARATOR . $dllName;
                    if (copy($dllFile, $destFile)) {
                        $steps[] = "  ✓ Copiado: $dllName";
                        $dllsCopied++;
                    }
                }
            }
        }

        if ($dllsCopied === 0) {
            return ['sucesso' => false, 'mensagem' => 'Nenhuma DLL compatível encontrada no pacote', 'steps' => $steps];
        }

        // Limpar temporários
        @unlink($zipFile);
        self::recursiveDelete($extractDir);
        $steps[] = "✓ Arquivos temporários removidos";

        // Habilitar no php.ini
        $steps[] = "Habilitando extensões no php.ini...";
        $phpIni = php_ini_loaded_file();
        
        // Buscar DLLs recém-copiadas
        $newSqlsrvDlls = glob($extDir . DIRECTORY_SEPARATOR . 'php_sqlsrv_*' . $phpMajorMinor . '*' . $ts . '*.dll');
        $newPdoDlls = glob($extDir . DIRECTORY_SEPARATOR . 'php_pdo_sqlsrv_*' . $phpMajorMinor . '*' . $ts . '*.dll');
        
        $extensions = [];
        if (!empty($newSqlsrvDlls)) $extensions[] = "extension=" . basename(end($newSqlsrvDlls));
        if (!empty($newPdoDlls)) $extensions[] = "extension=" . basename(end($newPdoDlls));
        
        if (!empty($extensions)) {
            $enableResult = self::enableExtensionInPhpIni($phpIni, $extensions);
            if ($enableResult['sucesso']) {
                $steps[] = "✓ php.ini atualizado";
            } else {
                $steps[] = "⚠️ Erro ao modificar php.ini: " . $enableResult['mensagem'];
            }
        }

        // Reiniciar Apache
        $steps[] = "Reiniciando Apache...";
        $restart = self::restartApache();
        
        if ($restart['sucesso']) {
            $steps[] = "✓ Apache reiniciado com sucesso";
            return [
                'sucesso' => true,
                'mensagem' => 'Driver SQL Server instalado! Teste a conexão em alguns segundos.',
                'steps' => $steps,
                'requer_reload' => true
            ];
        } else {
            return [
                'sucesso' => true,
                'mensagem' => 'Instalação concluída! Reinicie o XAMPP manualmente.',
                'steps' => $steps,
                'requer_restart_manual' => true
            ];
        }
    }

    /**
     * Instala driver MySQL/MariaDB (habilita extensão no php.ini)
     */
    private static function installMySQL(bool $autoDownload = false): array
    {
        self::log("Instalando driver MySQL/MariaDB...");
        
        $steps = [];
        $phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        $steps[] = "Detectado PHP $phpVersion";
        
        $extDir = PHP_EXTENSION_DIR;
        $phpIni = php_ini_loaded_file();

        // MySQL/PDO_MySQL geralmente já vem com o PHP/XAMPP, só pode estar desabilitado
        $possibleDlls = ['php_mysqli.dll', 'php_pdo_mysql.dll'];
        $existingDlls = [];
        
        foreach ($possibleDlls as $dll) {
            if (file_exists($extDir . DIRECTORY_SEPARATOR . $dll)) {
                $existingDlls[] = $dll;
            }
        }

        if (!empty($existingDlls)) {
            $steps[] = "DLLs encontradas: " . implode(', ', $existingDlls);
            $steps[] = "Habilitando extensões no php.ini...";
            
            $extensions = [];
            foreach ($existingDlls as $dll) {
                $extName = str_replace('.dll', '', $dll);
                $extName = str_replace('php_', '', $extName);
                $extensions[] = "extension=$extName";
            }
            
            $result = self::enableExtensionInPhpIni($phpIni, $extensions);
            
            if ($result['sucesso']) {
                $steps[] = "✓ Extensões habilitadas no php.ini";
                $steps[] = "Reiniciando Apache...";
                
                $restart = self::restartApache();
                if ($restart['sucesso']) {
                    $steps[] = "✓ Apache reiniciado com sucesso";
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Driver MySQL habilitado com sucesso!',
                        'steps' => $steps,
                        'requer_reload' => true
                    ];
                } else {
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Extensão habilitada! Reinicie o XAMPP manualmente.',
                        'steps' => $steps,
                        'requer_restart_manual' => true
                    ];
                }
            }
            
            return ['sucesso' => false, 'mensagem' => 'Erro ao habilitar extensões: ' . ($result['mensagem'] ?? ''), 'steps' => $steps];
        }

        // DLLs não encontradas - checar também extensões sem prefixo php_
        $altNames = ['mysqli', 'pdo_mysql'];
        $phpIniContent = file_get_contents($phpIni);
        
        foreach ($altNames as $ext) {
            // Verificar se existe comentada
            if (preg_match('/^;\s*extension\s*=\s*' . preg_quote($ext, '/') . '/m', $phpIniContent)) {
                $existingDlls[] = $ext;
            }
        }
        
        if (!empty($existingDlls)) {
            $steps[] = "Extensões encontradas (comentadas): " . implode(', ', $existingDlls);
            $extensions = array_map(fn($e) => "extension=$e", $existingDlls);
            $result = self::enableExtensionInPhpIni($phpIni, $extensions);
            
            if ($result['sucesso']) {
                $steps[] = "✓ Extensões descomentadas";
                $restart = self::restartApache();
                return [
                    'sucesso' => true,
                    'mensagem' => 'Driver MySQL habilitado! ' . ($restart['sucesso'] ? 'Apache reiniciado.' : 'Reinicie o XAMPP manualmente.'),
                    'steps' => $steps,
                    'requer_restart_manual' => !$restart['sucesso'],
                    'requer_reload' => $restart['sucesso']
                ];
            }
        }

        return [
            'sucesso' => false,
            'mensagem' => 'Driver MySQL não encontrado na instalação do PHP. Considere reinstalar o PHP/XAMPP com suporte a MySQL.',
            'steps' => $steps
        ];
    }

    /**
     * Instala driver PostgreSQL (habilita extensão no php.ini)
     */
    private static function installPostgres(bool $autoDownload = false): array
    {
        self::log("Instalando driver PostgreSQL...");
        
        $steps = [];
        $phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        $steps[] = "Detectado PHP $phpVersion";
        
        $extDir = PHP_EXTENSION_DIR;
        $phpIni = php_ini_loaded_file();

        // PostgreSQL geralmente já vem com o PHP/XAMPP
        $possibleDlls = ['php_pgsql.dll', 'php_pdo_pgsql.dll'];
        $existingDlls = [];
        
        foreach ($possibleDlls as $dll) {
            if (file_exists($extDir . DIRECTORY_SEPARATOR . $dll)) {
                $existingDlls[] = $dll;
            }
        }

        if (!empty($existingDlls)) {
            $steps[] = "DLLs encontradas: " . implode(', ', $existingDlls);
            $steps[] = "Habilitando extensões no php.ini...";
            
            $extensions = [];
            foreach ($existingDlls as $dll) {
                $extName = str_replace('.dll', '', $dll);
                $extName = str_replace('php_', '', $extName);
                $extensions[] = "extension=$extName";
            }
            
            $result = self::enableExtensionInPhpIni($phpIni, $extensions);
            
            if ($result['sucesso']) {
                $steps[] = "✓ Extensões habilitadas no php.ini";
                $steps[] = "Reiniciando Apache...";
                
                $restart = self::restartApache();
                if ($restart['sucesso']) {
                    $steps[] = "✓ Apache reiniciado com sucesso";
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Driver PostgreSQL habilitado com sucesso!',
                        'steps' => $steps,
                        'requer_reload' => true
                    ];
                } else {
                    return [
                        'sucesso' => true,
                        'mensagem' => 'Extensão habilitada! Reinicie o XAMPP manualmente.',
                        'steps' => $steps,
                        'requer_restart_manual' => true
                    ];
                }
            }
            
            return ['sucesso' => false, 'mensagem' => 'Erro ao habilitar extensões: ' . ($result['mensagem'] ?? ''), 'steps' => $steps];
        }

        // DLLs não encontradas - checar extensões sem prefixo
        $altNames = ['pgsql', 'pdo_pgsql'];
        $phpIniContent = file_get_contents($phpIni);
        
        foreach ($altNames as $ext) {
            if (preg_match('/^;\s*extension\s*=\s*' . preg_quote($ext, '/') . '/m', $phpIniContent)) {
                $existingDlls[] = $ext;
            }
        }
        
        if (!empty($existingDlls)) {
            $steps[] = "Extensões encontradas (comentadas): " . implode(', ', $existingDlls);
            $extensions = array_map(fn($e) => "extension=$e", $existingDlls);
            $result = self::enableExtensionInPhpIni($phpIni, $extensions);
            
            if ($result['sucesso']) {
                $steps[] = "✓ Extensões descomentadas";
                $restart = self::restartApache();
                return [
                    'sucesso' => true,
                    'mensagem' => 'Driver PostgreSQL habilitado! ' . ($restart['sucesso'] ? 'Apache reiniciado.' : 'Reinicie o XAMPP manualmente.'),
                    'steps' => $steps,
                    'requer_restart_manual' => !$restart['sucesso'],
                    'requer_reload' => $restart['sucesso']
                ];
            }
        }

        return [
            'sucesso' => false,
            'mensagem' => 'Driver PostgreSQL não encontrado na instalação do PHP. Considere reinstalar o PHP/XAMPP com suporte a pgsql.',
            'steps' => $steps
        ];
    }

    /**
     * Habilita extensão no php.ini
     */
    private static function enableExtensionInPhpIni(string $phpIni, array $extensions): array
    {
        if (!file_exists($phpIni)) {
            return ['sucesso' => false, 'mensagem' => 'php.ini não encontrado'];
        }

        // Ler conteúdo atual
        $content = file_get_contents($phpIni);
        $modified = false;

        foreach ($extensions as $ext) {
            // Remover "extension=" se já estiver no $ext
            $extName = str_replace('extension=', '', $ext);
            
            // Verificar se já está habilitada
            if (preg_match('/^\s*extension\s*=\s*' . preg_quote($extName, '/') . '\s*$/m', $content)) {
                self::log("Extensão já habilitada: $extName");
                continue;
            }

            // Verificar se está comentada
            if (preg_match('/^;+\s*extension\s*=\s*' . preg_quote($extName, '/') . '\s*$/m', $content)) {
                // Descomentar
                $content = preg_replace(
                    '/^;+\s*(extension\s*=\s*' . preg_quote($extName, '/') . ')\s*$/m',
                    '$1',
                    $content
                );
                $modified = true;
                self::log("Extensão descomentada: $extName");
            } else {
                // Adicionar no final da seção de extensões
                // Procurar por outras linhas de extension= e adicionar próximo
                if (preg_match('/^extension\s*=/m', $content)) {
                    $content = preg_replace(
                        '/(^extension\s*=.*$)/m',
                        "$1\nextension=$extName",
                        $content,
                        1
                    );
                } else {
                    // Adicionar no final do arquivo
                    $content .= "\n\n; Habilitado automaticamente pelo DMC DataLoad\nextension=$extName\n";
                }
                $modified = true;
                self::log("Extensão adicionada: $extName");
            }
        }

        if ($modified) {
            // Fazer backup
            $backup = $phpIni . '.backup.' . date('Ymd_His');
            copy($phpIni, $backup);
            self::log("Backup criado: $backup");

            // Salvar
            if (file_put_contents($phpIni, $content)) {
                self::log("php.ini atualizado com sucesso");
                return ['sucesso' => true, 'mensagem' => 'php.ini atualizado', 'backup' => $backup];
            } else {
                return ['sucesso' => false, 'mensagem' => 'Erro ao escrever php.ini'];
            }
        }

        return ['sucesso' => true, 'mensagem' => 'Nenhuma modificação necessária'];
    }

    /**
     * Verifica se Oracle Instant Client está no PATH
     */
    private static function checkInstantClient(): array
    {
        $pathEnv = getenv('PATH');
        $paths = explode(';', $pathEnv);

        foreach ($paths as $path) {
            $path = trim($path);
            if (empty($path)) continue;

            // Procurar por oci.dll ou oraociei*.dll
            if (file_exists($path . DIRECTORY_SEPARATOR . 'oci.dll')) {
                return ['encontrado' => true, 'path' => $path];
            }
            
            $ociDlls = glob($path . DIRECTORY_SEPARATOR . 'oraociei*.dll');
            if (!empty($ociDlls)) {
                return ['encontrado' => true, 'path' => $path];
            }
        }

        return ['encontrado' => false];
    }

    /**
     * Reinicia o Apache do XAMPP
     */
    private static function restartApache(): array
    {
        self::log("Tentando reiniciar Apache...");

        // Tentar encontrar o XAMPP
        $xamppPaths = [
            'C:\xampp',
            'C:\xampp7',
            'C:\xampp8',
            'D:\xampp'
        ];

        $xamppPath = null;
        foreach ($xamppPaths as $path) {
            if (file_exists($path . '\apache\bin\httpd.exe')) {
                $xamppPath = $path;
                break;
            }
        }

        if (!$xamppPath) {
            self::log("XAMPP não encontrado nos caminhos padrões");
            return ['sucesso' => false, 'mensagem' => 'XAMPP não encontrado'];
        }

        self::log("XAMPP encontrado em: $xamppPath");

        // Executar restart via PowerShell
        $apacheExe = $xamppPath . '\apache\bin\httpd.exe';
        
        // Parar Apache
        $stopCmd = "Stop-Process -Name 'httpd' -Force -ErrorAction SilentlyContinue";
        exec("powershell -Command \"$stopCmd\"", $output, $returnCode);
        self::log("Apache stop command executed");
        
        sleep(2);
        
        // Iniciar Apache
        $startCmd = "Start-Process -FilePath '$apacheExe' -WindowStyle Hidden";
        exec("powershell -Command \"$startCmd\"", $output, $returnCode);
        self::log("Apache start command executed");
        
        sleep(2);

        // Verificar se está rodando
        $checkCmd = "Get-Process -Name 'httpd' -ErrorAction SilentlyContinue";
        exec("powershell -Command \"$checkCmd\"", $output, $returnCode);
        
        if ($returnCode === 0 && !empty($output)) {
            self::log("Apache reiniciado com sucesso");
            return ['sucesso' => true, 'mensagem' => 'Apache reiniciado'];
        }

        self::log("Não foi possível verificar se Apache reiniciou");
        return ['sucesso' => false, 'mensagem' => 'Não foi possível verificar reinicialização'];
    }

    /**
     * Retorna URL de download do PECL para SQL Server drivers
     */
    private static function getSqlServerDriverUrl(string $phpVersion, int $arch, bool $isThreadSafe): ?string
    {
        // Microsoft SQL Server drivers for PHP - PECL Windows
        // Formato: https://downloads.php.net/~windows/pecl/releases/sqlsrv/VERSION/php_sqlsrv-VERSION-PHP_VERSION-ts-VC-ARCH.zip
        $vcMap = [
            '7.4' => 'vs16',
            '8.0' => 'vs16',
            '8.1' => 'vs16',
            '8.2' => 'vs16',
            '8.3' => 'vs16',
            '8.4' => 'vs17'
        ];

        $vc = $vcMap[$phpVersion] ?? null;
        if (!$vc) return null;

        $ts = $isThreadSafe ? 'ts' : 'nts';
        $archStr = $arch === 64 ? 'x64' : 'x86';

        // Versão mais recente do sqlsrv
        $sqlsrvVersion = '5.12.0';

        return "https://downloads.php.net/~windows/pecl/releases/sqlsrv/$sqlsrvVersion/php_sqlsrv-$sqlsrvVersion-$phpVersion-$ts-$vc-$archStr.zip";
    }

    /**
     * Retorna URL do PECL para Oracle
     */
    private static function getPeclOracleUrl(string $phpVersion, int $arch, bool $isThreadSafe): ?string
    {
        // PECL Windows downloads
        // Formato CORRETO: https://downloads.php.net/~windows/pecl/releases/oci8/VERSION/php_oci8-VERSION-PHP_VERSION-ts-VC-ARCH.zip
        
        // Mapear versão do PHP para VC version
        $vcMap = [
            '7.4' => 'vs16',
            '8.0' => 'vs16',
            '8.1' => 'vs16',
            '8.2' => 'vs16',
            '8.3' => 'vs16',
            '8.4' => 'vs17'
        ];

        $vc = $vcMap[$phpVersion] ?? 'vs16';
        $ts = $isThreadSafe ? 'ts' : 'nts';
        $archStr = $arch === 64 ? 'x64' : 'x86';

        // Versão mais recente do OCI8
        $oci8Version = '3.3.0';

        // URL CORRETA (ordem: VERSION-PHP-TS-VC-ARCH)
        return "https://downloads.php.net/~windows/pecl/releases/oci8/$oci8Version/php_oci8-$oci8Version-$phpVersion-$ts-$vc-$archStr.zip";
    }

    /**
     * Log de operações
     */
    private static function log(string $message): void
    {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Baixa arquivo via PowerShell
     */
    public static function downloadFile(string $url, string $destination): array
    {
        self::log("Baixando: $url -> $destination");

        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $cmd = "Invoke-WebRequest -Uri '$url' -OutFile '$destination' -UseBasicParsing";
        exec("powershell -Command \"$cmd\"", $output, $returnCode);

        if ($returnCode === 0 && file_exists($destination)) {
            self::log("Download concluído com sucesso");
            return ['sucesso' => true, 'arquivo' => $destination];
        }

        self::log("Erro no download. Return code: $returnCode");
        return ['sucesso' => false, 'mensagem' => 'Erro ao baixar arquivo'];
    }

    /**
     * Extrai arquivo ZIP
     */
    public static function extractZip(string $zipFile, string $destination): array
    {
        self::log("Extraindo: $zipFile -> $destination");

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $cmd = "Expand-Archive -Path '$zipFile' -DestinationPath '$destination' -Force";
        exec("powershell -Command \"$cmd\"", $output, $returnCode);

        if ($returnCode === 0) {
            self::log("Extração concluída com sucesso");
            return ['sucesso' => true, 'destino' => $destination];
        }

        self::log("Erro na extração. Return code: $returnCode");
        return ['sucesso' => false, 'mensagem' => 'Erro ao extrair arquivo'];
    }

    /**
     * Baixa e instala Oracle Instant Client automaticamente
     */
    public static function downloadAndInstallInstantClient(int $arch = 64): array
    {
        self::log("=== Iniciando download do Oracle Instant Client ===");
        
        $steps = [];
        $steps[] = "Preparando download do Oracle Instant Client {$arch}-bit...";

        // URL do Instant Client Basic Light (menor, suficiente para OCI8)
        // Usando mirror público do GitHub que hospeda versões do Instant Client
        $version = '21.13.0.0.0';
        $archStr = $arch === 64 ? 'x64' : 'x86';
        
        // URL alternativa - winget-pkgs tem os Instant Clients
        $instantClientUrl = "https://download.oracle.com/otn_software/nt/instantclient/2113000/instantclient-basiclite-windows.x64-21.13.0.0.0dbru.zip";
        
        // Como Oracle requer login, vamos usar uma abordagem alternativa:
        // Baixar do XAMPP pre-compiled ou usar version já conhecida
        $alternativeUrl = "https://github.com/bumpx/oracle-instantclient/raw/master/instantclient-basiclite-windows.x64-19.19.0.0.0dbru.zip";
        
        $steps[] = "URL: Instant Client Basic Lite 19.19";
        
        $zipFile = self::$tempDir . DIRECTORY_SEPARATOR . 'instantclient.zip';
        $extractDir = 'C:\oracle\instantclient_19_19';
        
        // Verificar se já existe
        if (is_dir($extractDir) && file_exists($extractDir . '\oci.dll')) {
            $steps[] = "✓ Instant Client já instalado em: $extractDir";
            
            // Apenas adicionar ao PATH
            $addPath = self::addToSystemPath($extractDir);
            if ($addPath['sucesso']) {
                $steps[] = "✓ Adicionado ao PATH do sistema";
                $steps[] = "Reiniciando Apache...";
                
                self::restartApache();
                
                return [
                    'sucesso' => true,
                    'mensagem' => 'Oracle Instant Client já estava instalado e foi adicionado ao PATH',
                    'steps' => $steps,
                    'path' => $extractDir
                ];
            }
        }

        $steps[] = "Baixando Instant Client (~50MB, pode demorar alguns minutos)...";
        
        // Tentar download
        $download = self::downloadFile($alternativeUrl, $zipFile);
        
        if (!$download['sucesso']) {
            $steps[] = "✗ Falha no download do mirror principal";
            return [
                'sucesso' => false,
                'mensagem' => 'Não foi possível baixar o Oracle Instant Client automaticamente. Baixe manualmente em: https://www.oracle.com/database/technologies/instant-client/winx64-64-downloads.html',
                'steps' => $steps,
                'manual_url' => 'https://www.oracle.com/database/technologies/instant-client/winx64-64-downloads.html'
            ];
        }

        $steps[] = "✓ Download concluído: " . self::formatFileSize(filesize($zipFile));
        $steps[] = "Extraindo arquivos para: $extractDir";

        // Criar diretório oracle se não existir
        $oracleDir = dirname($extractDir);
        if (!is_dir($oracleDir)) {
            mkdir($oracleDir, 0777, true);
        }

        // Extrair
        $extract = self::extractZip($zipFile, $oracleDir);
        
        if (!$extract['sucesso']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao extrair Instant Client',
                'steps' => $steps
            ];
        }

        $steps[] = "✓ Arquivos extraídos";

        // Renomear diretório se necessário (o ZIP vem com instantclient_XX_XX)
        $extractedDirs = glob($oracleDir . '\instantclient_*');
        if (!empty($extractedDirs)) {
            $actualDir = $extractedDirs[0];
            if ($actualDir !== $extractDir && is_dir($actualDir)) {
                // Renomear para o nome esperado
                if (is_dir($extractDir)) {
                    self::recursiveDelete($extractDir);
                }
                rename($actualDir, $extractDir);
                $steps[] = "✓ Diretório renomeado para: $extractDir";
            }
        }

        // Adicionar ao PATH do sistema
        $steps[] = "Adicionando ao PATH do sistema...";
        $addPath = self::addToSystemPath($extractDir);
        
        if (!$addPath['sucesso']) {
            $steps[] = "⚠️ Não foi possível adicionar ao PATH automaticamente (requer Admin)";
            return [
                'sucesso' => false,
                'mensagem' => 'Instant Client instalado, mas não foi possível adicionar ao PATH. Execute como Administrador ou adicione manualmente.',
                'steps' => $steps,
                'path_manual' => $extractDir,
                'instrucoes_path' => [
                    '1. Abra "Painel de Controle > Sistema > Configurações Avançadas"',
                    '2. Clique em "Variáveis de Ambiente"',
                    '3. Em "Variáveis do Sistema", edite "Path"',
                    '4. Adicione: ' . $extractDir,
                    '5. Clique OK e reinicie o XAMPP'
                ]
            ];
        }

        $steps[] = "✓ Adicionado ao PATH do sistema";
        $steps[] = "Limpando arquivos temporários...";
        
        @unlink($zipFile);
        $steps[] = "✓ Arquivos temporários removidos";

        $steps[] = "Reiniciando Apache...";
        $restart = self::restartApache();
        
        if ($restart['sucesso']) {
            $steps[] = "✓ Apache reiniciado";
        } else {
            $steps[] = "⚠️ Reinicie o XAMPP manualmente";
        }

        return [
            'sucesso' => true,
            'mensagem' => 'Oracle Instant Client instalado com sucesso! Reinicie o XAMPP se ainda houver problemas.',
            'steps' => $steps,
            'path' => $extractDir,
            'requer_restart_manual' => !$restart['sucesso']
        ];
    }

    /**
     * Adiciona diretório ao PATH do sistema (requer admin)
     */
    private static function addToSystemPath(string $directory): array
    {
        self::log("Adicionando ao PATH: $directory");

        // Verificar se já está no PATH
        $currentPath = getenv('PATH');
        if (stripos($currentPath, $directory) !== false) {
            self::log("Diretório já está no PATH");
            return ['sucesso' => true, 'mensagem' => 'Já está no PATH'];
        }

        // Adicionar via PowerShell (requer privilégios administrativos)
        $psScript = "
            \$oldPath = [Environment]::GetEnvironmentVariable('Path', 'Machine');
            if (\$oldPath -notlike '*$directory*') {
                \$newPath = \$oldPath + ';$directory';
                [Environment]::SetEnvironmentVariable('Path', \$newPath, 'Machine');
                \$env:Path = [Environment]::GetEnvironmentVariable('Path', 'Machine');
                Write-Output 'SUCCESS';
            } else {
                Write-Output 'ALREADY_EXISTS';
            }
        ";

        $tempScript = self::$tempDir . '\add_to_path.ps1';
        file_put_contents($tempScript, $psScript);

        // Executar como administrador
        $cmd = "Start-Process powershell -ArgumentList '-ExecutionPolicy Bypass -File \"$tempScript\"' -Verb RunAs -Wait -WindowStyle Hidden";
        exec("powershell -Command \"$cmd\"", $output, $returnCode);

        @unlink($tempScript);

        // Verificar se funcionou
        sleep(2);
        $newPath = shell_exec('powershell -Command "[Environment]::GetEnvironmentVariable(\'Path\', \'Machine\')"');
        
        if (stripos($newPath, $directory) !== false) {
            self::log("PATH atualizado com sucesso");
            
            // Atualizar PATH da sessão atual
            putenv("PATH=" . getenv('PATH') . ";$directory");
            
            return ['sucesso' => true, 'mensagem' => 'PATH atualizado'];
        }

        self::log("Falha ao adicionar ao PATH - pode requerer admin");
        return ['sucesso' => false, 'mensagem' => 'Requer privilégios administrativos'];
    }

    /**
     * Formata tamanho de arquivo
     */
    private static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Deleta diretório recursivamente
     */
    private static function recursiveDelete(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? self::recursiveDelete($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Procura arquivos recursivamente por padrão
     */
    private static function findFiles(string $directory, string $pattern): array
    {
        $files = [];
        
        if (!is_dir($directory)) {
            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $fileName = $file->getFilename();
                
                // Converter padrão wildcard para regex
                $regexPattern = '/^' . str_replace(['*', '.'], ['.*', '\.'], $pattern) . '$/i';
                
                if (preg_match($regexPattern, $fileName)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
}
