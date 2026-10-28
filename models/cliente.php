<?php
require_once __DIR__ . '/../config/db.php';

/** Cliente de BALCÃO (tabela `cliente`) usado nas vendas manuais do PDV. */
class Cliente
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM cliente WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Busca ignorando pontuação do cpf_cnpj (que pode estar salvo com máscara). */
    public function buscarPorCpf(string $cpfSomenteDigitos): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM cliente
            WHERE REPLACE(REPLACE(REPLACE(cpf_cnpj, '.', ''), '-', ''), '/', '') = :cpf
            LIMIT 1
        ");
        $stmt->execute([':cpf' => $cpfSomenteDigitos]);
        return $stmt->fetch() ?: null;
    }

    /** Cria um cliente de balcão a partir de um cadastro já existente no site. */
    public function criarAPartirDoSite(array $clienteSite): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO cliente (nome, cpf_cnpj, telefone, email, endereco)
            VALUES (:nome, :cpf, :telefone, :email, :endereco)
        ");
        $stmt->execute([
            ':nome'     => $clienteSite['nome'],
            ':cpf'      => $clienteSite['cpf'],
            ':telefone' => $clienteSite['telefone'],
            ':email'    => $clienteSite['email'],
            ':endereco' => $clienteSite['endereco'],
        ]);
        return (int) $this->conn->lastInsertId();
    }
}