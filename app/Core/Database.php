<?php
/**
 * app/Core/Database.php
 * Conexión PDO Singleton
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require CONFIG_PATH . '/database.php';
            
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                if (APP_ENV === 'development') {
                    throw new Exception("Error de conexión a MySQL: " . $e->getMessage());
                } else {
                    error_log("DB Connection Error: " . $e->getMessage());
                    throw new Exception("Error al conectar con la base de datos.");
                }
            }
        }

        return self::$instance;
    }
}