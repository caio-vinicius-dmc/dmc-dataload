<?php
namespace App\Core;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $instance = null;

    public static function loadEnv(string $path = __DIR__ . '/../../')
    {
        if (file_exists($path . '.env')) {
            // Carregar manualmente o .env para garantir que $_ENV seja populado
            // (algumas configurações PHP não populam $_ENV automaticamente)
            $lines = file($path . '.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line[0] === '#') continue; // Pular comentários
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    // Remover aspas se existirem
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                        $value = substr($value, 1, -1);
                    }
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    public static function getConexao(): PDO
    {
        if (self::$instance === null) {
            self::loadEnv();

            $driver = getenv('DB_DRIVER') ?: 'pgsql';
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '5433';
            $db   = getenv('DB_DATABASE') ?: 'db_dmc_dataload';
            $user = getenv('DB_USERNAME') ?: 'postgres';
            $pass = getenv('DB_PASSWORD') ?: 'dmc2023@';

            try {
                if ($driver === 'pgsql') {
                    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $db);
                } else {
                    $dsn = sprintf('%s:host=%s;port=%s;dbname=%s', $driver, $host, $port, $db);
                }

                $opts = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$instance = new PDO($dsn, $user, $pass, $opts);
            } catch (PDOException $e) {
                throw new \RuntimeException('Falha ao conectar ao banco interno: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
