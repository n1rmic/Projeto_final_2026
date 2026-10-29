<?php
class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {

            // Padrão para XAMPP/WAMP (computador local)
            $cfg = [
                'host' => 'localhost',
                'name' => 'loja_roupas',
                'user' => 'root',
                'pass' => '',
            ];

            // No servidor online, crie config/db.local.php (veja db.local.example.php).
            // Ele sobrescreve os valores acima e não vai para o GitHub nem para o zip.
            $local = __DIR__ . '/db.local.php';
            if (is_file($local)) {
                $extra = require $local;
                if (is_array($extra)) {
                    $cfg = array_merge($cfg, $extra);
                }
            }

            try {
                self::$conn = new PDO(
                    "mysql:host={$cfg['host']};dbname={$cfg['name']};charset=utf8mb4",
                    $cfg['user'],
                    $cfg['pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                error_log('[db] ' . $e->getMessage());
                die("Erro ao conectar ao banco de dados.");
            }
        }

        return self::$conn;
    }
}