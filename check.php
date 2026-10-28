<?php
// Verificação rápida do banco: conta as tabelas e lista as encontradas.
require_once __DIR__ . '/config/db.php';

try {
    $db = Database::getConnection();

    $query = $db->query("SHOW TABLES");
    $tabelasEncontradas = $query->fetchAll(PDO::FETCH_COLUMN);

    $total = count($tabelasEncontradas);
    $meta = 17; // tabelas do loja_roupas.sql

    echo "<h1>🔍 Verificação do Sistema</h1>";

    if ($total == $meta) {
        echo "<p style='color: green;'>Sucesso! O banco de dados está completo com as $meta tabelas.</p>";
    } else {
        echo "<p style='color: red;'>Atenção: o banco possui $total tabelas, mas o esperado são $meta.</p>";
    }

    echo "<h3>Tabelas encontradas no banco:</h3>";
    echo "<ul>";
    foreach ($tabelasEncontradas as $tabela) {
        echo "<li>" . htmlspecialchars($tabela) . "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Erro ao carregar o teste: " . htmlspecialchars($e->getMessage());
}
