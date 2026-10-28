<?php
require_once __DIR__ . '/../config/db.php';
 
class Fornecedor
{
    private PDO $conn;
 
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }
 
    public function listarTodos(): array
    {
        $sql = "SELECT id, nome, cnpj, telefone, email, endereco, ativo
                FROM fornecedor
                ORDER BY nome";
        return $this->conn->query($sql)->fetchAll();
    }
 
    public function listarAtivos(): array
    {
        $sql = "SELECT id, nome FROM fornecedor WHERE ativo = 1 ORDER BY nome";
        return $this->conn->query($sql)->fetchAll();
    }
 
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM fornecedor WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }
 
    public function inserir(string $nome, ?string $cnpj, ?string $telefone, ?string $email, ?string $endereco): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO fornecedor (nome, cnpj, telefone, email, endereco, ativo)
            VALUES (:nome, :cnpj, :telefone, :email, :endereco, 1)
        ");
        $stmt->execute([
            ':nome'     => $nome,
            ':cnpj'     => $cnpj,
            ':telefone' => $telefone,
            ':email'    => $email,
            ':endereco' => $endereco,
        ]);
        return (int) $this->conn->lastInsertId();
    }
 
    public function atualizar(int $id, string $nome, ?string $cnpj, ?string $telefone, ?string $email, ?string $endereco): void
    {
        $stmt = $this->conn->prepare("
            UPDATE fornecedor
            SET nome = :nome, cnpj = :cnpj, telefone = :telefone,
                email = :email, endereco = :endereco
            WHERE id = :id
  ");
        $stmt->execute([
            ':id'       => $id,
            ':nome'     => $nome,
            ':cnpj'     => $cnpj,
            ':telefone' => $telefone,
            ':email'    => $email,
            ':endereco' => $endereco,
        ]);
    }
 
    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->conn->prepare(
            "UPDATE fornecedor SET ativo = :ativo WHERE id = :id"
        );
        $stmt->execute([
            ':id'    => $id,
            ':ativo' => $ativo ? 1 : 0,
        ]);
    }
}
