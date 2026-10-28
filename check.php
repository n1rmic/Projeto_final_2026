<?php
// 1. Inclui o seu arquivo de conexão (ajuste o nome se for diferente)
require_once 'config/db.php';

try {
    // 2. Tenta obter a conexão
    $db = $conexao; // Usando a variável de conexão definida em db.php

    // 3. Executa o comando para listar as tabelas
    $query = $db->query("SHOW TABLES");
    $tabelasEncontradas = $query->fetchAll(PDO::FETCH_COLUMN);

    $total = count($tabelasEncontradas);
    $meta = 14;

    echo "<h1>🔍 Verificação do Sistema</h1>";

    // 4. Lógica de comparação
    if ($total == $meta) {
        echo "<p style='color: green;'>Sucesso! O banco de dados está completo com as 14 tabelas.</p>";
    } else {
        $faltando = $meta - $total;
        echo "<p style='color: red;'>Atenção: O banco possui $total tabelas. Estão <strong>faltando $faltando</strong> para chegar às 14.</p>";
    }

    // 5. Listagem das tabelas existentes
    echo "<h3>Tabelas encontradas no banco:</h3>";
    echo "<ul>";
    foreach ($tabelasEncontradas as $tabela) {
        echo "<li>$tabela</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Erro ao carregar o teste: " . $e->getMessage();
}
?>