<?php
require_once __DIR__ . '/../config/db.php';
 
class Relatorio
{
    private PDO $conn;
 
    public function __construct()
    {
   $this->conn = Database::getConnection();
    }
 
    public function kpiVendasMes(): int
    {
        $sql = "
            SELECT COUNT(*) FROM venda
            WHERE MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
        ";
        return (int) $this->conn->query($sql)->fetchColumn();
    }
 
    public function kpiEntradasMes(): int
    {
        $sql = "
            SELECT COUNT(*) FROM entrada_mercadoria
            WHERE MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
        ";
        return (int) $this->conn->query($sql)->fetchColumn();
    }
 
    public function kpiEstoqueBaixo(): int
    {
        $sql = "SELECT COUNT(*) FROM estoque WHERE quantidade <= minimo";
        return (int) $this->conn->query($sql)->fetchColumn();
    }
 
    public function kpiTotalProdutos(): int
    {
        $sql = "SELECT COUNT(*) FROM produto WHERE ativo = 1";
        return (int) $this->conn->query($sql)->fetchColumn();
    }
 
    public function topProdutosMaisVendidos(int $limite = 5): array
    {
        $sql = "
            SELECT p.nome, SUM(vi.quantidade) AS total_vendido
            FROM venda_item vi
            INNER JOIN variacao v ON v.id = vi.variacao_id
            INNER JOIN produto p ON p.id = v.produto_id
            INNER JOIN venda ve ON ve.id = vi.venda_id
            WHERE ve.status = 'finalizada'
            GROUP BY p.id, p.nome
            ORDER BY total_vendido DESC
            LIMIT :limite
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
 
    public function fornecedoresComMaisEntregas(int $limite = 5): array
    {
        $sql = "
            SELECT f.nome, COUNT(em.id) AS total_entradas,
                   COALESCE(SUM(em.valor_total), 0) AS valor_total
            FROM fornecedor f
            LEFT JOIN entrada_mercadoria em ON em.fornecedor_id = f.id
            GROUP BY f.id, f.nome
            ORDER BY total_entradas DESC
            LIMIT :limite
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
   }
 
    public function entradasRecentes(int $limite = 5): array
    {
        $sql = "
            SELECT em.id, em.data, em.valor_total, f.nome AS fornecedor_nome
            FROM entrada_mercadoria em
            INNER JOIN fornecedor f ON f.id = em.fornecedor_id
            ORDER BY em.id DESC
            LIMIT :limite
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
public function kpiTotalEstoque(): int
{
    $sql = "SELECT COALESCE(SUM(quantidade), 0) FROM estoque";
    return (int) $this->conn->query($sql)->fetchColumn();
}
    
}
