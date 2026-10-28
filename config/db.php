<?php
class Database
{
private static $conn = null;
public static function getConnection()
{
if (self::$conn === null) {
try {
self::$conn = new PDO(
"mysql:host=" . (getenv('DB_HOST') ?: 'localhost') . ";dbname=" . (getenv('DB_NAME') ?: 'loja_roupas') . ";charset=utf8mb4",
getenv('DB_USER') ?: "root",
getenv('DB_PASS') !== false ? getenv('DB_PASS') : "",
[
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]
);
} catch (PDOException $e) {
// Em produção isso vira log
die("Erro ao conectar ao banco de dados.");
}
}
return self::$conn;
}
}