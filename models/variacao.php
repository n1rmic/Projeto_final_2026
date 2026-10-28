<?php
require_once __DIR__ . '/../config/db.php';

class Variacao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarPorProduto(int $produtoId): array
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM variacao 
            WHERE produto_id = :produto_id 
            ORDER BY id DESC
        ");
        $stmt->execute([':produto_id' => $produtoId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM variacao WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function inseri(int $produtoId, ?string $tamanho, ?string $cor, string $sku, float $preco): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO variacao (produto_id, tamanho, cor, sku, preco)
            VALUES (:produto_id, :tamanho, :cor, :sku, :preco)
        ");
        $stmt->execute([
            ':produto_id' => $produtoId,
            ':tamanho' => $tamanho,
            ':cor' => $cor,
            ':sku' => $sku,
            ':preco' => $preco
        ]);
        return (int)$this->conn->lastInsertId();
    }

    public function atualizar(int $id, ?string $tamanho, ?string $cor, string $sku, float $preco): void
    {
        $stmt = $this->conn->prepare("
            UPDATE variacao 
            SET tamanho = :tamanho, cor = :cor, sku = :sku, preco = :preco
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':tamanho' => $tamanho,
            ':cor' => $cor,
            ':sku' => $sku,
            ':preco' => $preco
        ]);
    }

    public function deletar(int $id): void
    {
        $stmt = $this->conn->prepare("DELETE FROM variacao WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}