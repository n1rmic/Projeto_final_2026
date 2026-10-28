<?php
require_once __DIR__ . '/../config/db.php';
 
class Estoque
{
    private PDO $conn;
 
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }
 
    /**
     * Garante que a variação tenha uma linha de estoque.
     * Chamado sempre que uma nova variação (SKU) é cadastrada.
     */
    public function garantirRegistro(int $variacaoId, int $minimo = 0): void

     {
        $stmt = $this->conn->prepare(
            "SELECT id FROM estoque WHERE variacao_id = :vid"
        );
        $stmt->execute([':vid' => $variacaoId]);
 
        if (!$stmt->fetch()) {
            $ins = $this->conn->prepare("
                INSERT INTO estoque (variacao_id, quantidade, minimo)
                VALUES (:vid, 0, :minimo)
            ");
            $ins->execute([':vid' => $variacaoId, ':minimo' => $minimo]);
        }
    }
 
    public function buscarPorVariacao(int $variacaoId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM estoque WHERE variacao_id = :vid"
        );
        $stmt->execute([':vid' => $variacaoId]);
        $r = $stmt->fetch();
        return $r ?: null;
    }
 
    /**
     * Ajusta a quantidade em estoque (positivo = entrada, negativo = saída).
     * Lança exceção se a operação resultar em estoque negativo.
     */
    public function ajustar(int $variacaoId, int $delta): void
    {
        $this->garantirRegistro($variacaoId);
 
        $stmt = $this->conn->prepare(
            "SELECT quantidade FROM estoque WHERE variacao_id = :vid FOR UPDATE"
        );
        $stmt->execute([':vid' => $variacaoId]);
        $atual = (int) $stmt->fetchColumn();
 
        $novaQuantidade = $atual + $delta;
 
        if ($novaQuantidade < 0) {
            throw new RuntimeException("Estoque insuficiente para esta operação.");
        }
 
        $up = $this->conn->prepare(
            "UPDATE estoque SET quantidade = :q WHERE variacao_id = :vid"
        );
        $up->execute([':q' => $novaQuantidade, ':vid' => $variacaoId]);
    }
 
public function listarBaixoEstoque(int $limiteEstoque = 5): array
{
    // Considera qualquer item com quantidade <= $limiteEstoque (padrão: 5)
    $sql = "
        SELECT 
            p.nome AS produto_nome,
            v.sku,
            e.quantidade,
            COALESCE(e.minimo, :limite) AS minimo
        FROM estoque e
        INNER JOIN variacao v ON v.id = e.variacao_id
        INNER JOIN produto p ON p.id = v.produto_id
        WHERE e.quantidade <= :limite
        ORDER BY e.quantidade ASC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':limite', $limiteEstoque, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}
 
    public function contarBaixoEstoque(): int
    {
  $sql = "SELECT COUNT(*) FROM estoque WHERE quantidade <= minimo";
        return (int) $this->conn->query($sql)->fetchColumn();
    }

    
}
