<?php
namespace App\Utils;

class DriverChecker
{
    /**
     * Retorna lista de drivers PDO disponíveis
     */
    public static function getAvailableDrivers(): array
    {
        return \PDO::getAvailableDrivers();
    }

    /**
     * Verifica se um driver específico está disponível
     */
    public static function isDriverAvailable(string $tipoBanco): bool
    {
        $driverMap = self::getDriverMap();
        $driverNecessario = $driverMap[$tipoBanco] ?? null;
        
        if (!$driverNecessario) {
            return false;
        }

        $available = self::getAvailableDrivers();
        return in_array($driverNecessario, $available, true);
    }

    /**
     * Mapeia tipo de banco para driver PDO necessário
     */
    public static function getDriverMap(): array
    {
        return [
            'postgres' => 'pgsql',
            'mysql' => 'mysql',
            'mariadb' => 'mysql',
            'sqlserver' => 'sqlsrv',
            'oracle' => 'oci',
            'sqlite' => 'sqlite',
            'odbc' => 'odbc'
        ];
    }

    /**
     * Retorna informações sobre driver faltante e como instalar
     */
    public static function getDriverInstallInfo(string $tipoBanco): array
    {
        $phpIni = php_ini_loaded_file();
        $extDir = PHP_EXTENSION_DIR;

        $info = [
            'postgres' => [
                'driver' => 'pgsql',
                'nome' => 'PostgreSQL PDO',
                'disponivel' => false,
                'auto_install' => true,
                'instrucoes' => [
                    '1. A extensão geralmente já vem com o PHP/XAMPP',
                    '2. Abra ' . $phpIni,
                    '3. Remova o ";" de: ;extension=pgsql',
                    '4. Remova o ";" de: ;extension=pdo_pgsql',
                    '5. Reinicie o Apache/XAMPP',
                ],
                'links' => [
                    'Documentação PHP PostgreSQL' => 'https://www.php.net/manual/pt_BR/book.pgsql.php'
                ]
            ],
            'mysql' => [
                'driver' => 'mysql',
                'nome' => 'MySQL/MariaDB PDO',
                'disponivel' => false,
                'auto_install' => true,
                'instrucoes' => [
                    '1. A extensão geralmente já vem com o PHP/XAMPP',
                    '2. Abra ' . $phpIni,
                    '3. Remova o ";" de: ;extension=mysqli',
                    '4. Remova o ";" de: ;extension=pdo_mysql',
                    '5. Reinicie o Apache/XAMPP',
                ],
                'links' => [
                    'Documentação PHP MySQL' => 'https://www.php.net/manual/pt_BR/book.mysqli.php'
                ]
            ],
            'mariadb' => [
                'driver' => 'mysql',
                'nome' => 'MariaDB (via MySQL PDO)',
                'disponivel' => false,
                'auto_install' => true,
                'instrucoes' => [
                    '1. MariaDB usa o mesmo driver que MySQL',
                    '2. Abra ' . $phpIni,
                    '3. Remova o ";" de: ;extension=mysqli',
                    '4. Remova o ";" de: ;extension=pdo_mysql',
                    '5. Reinicie o Apache/XAMPP',
                ],
                'links' => [
                    'Documentação PHP MySQL' => 'https://www.php.net/manual/pt_BR/book.mysqli.php'
                ]
            ],
            'oracle' => [
                'driver' => 'oci',
                'nome' => 'Oracle OCI8',
                'disponivel' => false,
                'auto_install' => true,
                'instrucoes' => [
                    '1. Baixe o Oracle Instant Client:',
                    '   https://www.oracle.com/database/technologies/instant-client/downloads.html',
                    '   (Escolha a versão compatível com seu PHP - 64-bit recomendado)',
                    '',
                    '2. Extraia o ZIP em uma pasta (ex: C:\oracle\instantclient_19_x)',
                    '',
                    '3. Adicione a pasta ao PATH do Windows:',
                    '   - Painel de Controle > Sistema > Configurações avançadas',
                    '   - Variáveis de Ambiente > PATH > Adicionar',
                    '',
                    '4. Habilite a extensão no php.ini:',
                    '   - Abra ' . $phpIni,
                    '   - Remova o ";" de: ;extension=oci8_12c (ou oci8_19)',
                    '   - Remova o ";" de: ;extension=pdo_oci',
                    '',
                    '5. Reinicie o Apache/XAMPP',
                    '',
                    '6. Verifique com: php -m | findstr oci'
                ],
                'links' => [
                    'Instant Client' => 'https://www.oracle.com/database/technologies/instant-client/downloads.html',
                    'Documentação PHP OCI8' => 'https://www.php.net/manual/pt_BR/book.oci8.php'
                ]
            ],
            'sqlserver' => [
                'driver' => 'sqlsrv',
                'nome' => 'SQL Server PDO',
                'disponivel' => false,
                'auto_install' => true,
                'instrucoes' => [
                    '1. Baixe os Microsoft Drivers for PHP for SQL Server:',
                    '   https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server',
                    '',
                    '2. Extraia os arquivos .dll compatíveis com sua versão do PHP',
                    '   (verifique com: php -v)',
                    '',
                    '3. Copie os arquivos para a pasta ext do PHP:',
                    '   - php_sqlsrv_xx_ts.dll',
                    '   - php_pdo_sqlsrv_xx_ts.dll',
                    '   Para: ' . $extDir,
                    '',
                    '4. Habilite as extensões no php.ini:',
                    '   - Abra ' . $phpIni,
                    '   - Adicione: extension=php_sqlsrv_xx_ts.dll',
                    '   - Adicione: extension=php_pdo_sqlsrv_xx_ts.dll',
                    '',
                    '5. Instale o Microsoft ODBC Driver for SQL Server:',
                    '   https://docs.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server',
                    '',
                    '6. Reinicie o Apache/XAMPP',
                    '',
                    '7. Verifique com: php -m | findstr sqlsrv'
                ],
                'links' => [
                    'Microsoft Drivers for PHP' => 'https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server',
                    'ODBC Driver' => 'https://docs.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server'
                ]
            ]
        ];

        if (!isset($info[$tipoBanco])) {
            return [
                'driver' => 'desconhecido',
                'nome' => 'Driver Desconhecido',
                'disponivel' => false,
                'instrucoes' => ['Driver não suportado ou não documentado'],
                'links' => []
            ];
        }

        $driverInfo = $info[$tipoBanco];
        $driverInfo['disponivel'] = self::isDriverAvailable($tipoBanco);
        
        return $driverInfo;
    }

    /**
     * Retorna status de todos os drivers suportados
     */
    public static function getAllDriversStatus(): array
    {
        $tiposBanco = ['postgres', 'mysql', 'mariadb', 'sqlserver', 'oracle', 'sqlite'];
        $status = [];

        foreach ($tiposBanco as $tipo) {
            $driverMap = self::getDriverMap();
            $driverNecessario = $driverMap[$tipo] ?? 'unknown';
            
            $status[] = [
                'tipo_banco' => $tipo,
                'driver' => $driverNecessario,
                'disponivel' => self::isDriverAvailable($tipo),
                'nome_exibicao' => ucfirst($tipo)
            ];
        }

        return $status;
    }

    /**
     * Retorna informações do ambiente PHP
     */
    public static function getPhpInfo(): array
    {
        return [
            'versao_php' => PHP_VERSION,
            'php_ini' => php_ini_loaded_file(),
            'extension_dir' => PHP_EXTENSION_DIR,
            'os' => PHP_OS,
            'architecture' => PHP_INT_SIZE * 8 . '-bit',
            'thread_safe' => ZEND_THREAD_SAFE ? 'Yes (TS)' : 'No (NTS)',
            'drivers_disponiveis' => self::getAvailableDrivers()
        ];
    }
}
