<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/estoque.php';
 
class Entrada
{
    private PDO $conn;
    private Estoque $estoqueModel;
 
    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->estoqueModel = new Estoque();
    }
 
 public function listarComFornecedor(): array
{
    $sql = "
        SELECT 
            em.id,
            em.data,
            em.status,
            em.valor_total,
            f.nome AS fornecedor_nome,
            COALESCE(SUM(me.quantidade), 0) AS total_quantidade,
            GROUP_CONCAT(
                CONCAT(p.nome, ' (', v.sku, ' / ', v.tamanho, ' / ', v.cor, ')')
                SEPARATOR ', '
            ) AS produtos_nomes
        FROM entrada_mercadoria em
        INNER JOIN fornecedor f ON f.id = em.fornecedor_id
        LEFT JOIN movimento_estoque me ON me.origem_id = em.id AND me.origem = 'entrada'
        LEFT JOIN variacao v ON v.id = me.variacao_id
        LEFT JOIN produto p ON p.id = v.produto_id
        GROUP BY em.id, em.data, em.status, em.valor_total, f.nome
        ORDER BY em.id DESC
    ";
    
    return $this->conn->query($sql)->fetchAll();
}

    public function buscarItensDaVariacao(int $variacaoId): array
    {
        // usado em relatórios; não obrigatório na tela de cadastro
        $stmt = $this->conn->prepare("
            SELECT * FROM entrada_item WHERE variacao_id = :vid
        ");
        $stmt->execute([':vid' => $variacaoId]);
        return $stmt->fetchAll();
    }
 
    /**
     * Registra uma entrada completa (cabeçalho + itens) em uma única transação
     * e credita o estoque de cada variação recebida.
     *
     * @param array $itens Cada item: ['variacao_id' => int, 'quantidade' => int, 'custo_unitario' => float]
     */
    public function registrar(int $fornecedorId, array $itens): int
    {
        if (empty($itens)) {
            throw new InvalidArgumentException("A entrada precisa ter ao menos um item.");
        }
 
        $valorTotal = 0.0;
        foreach ($itens as $item) {
            $valorTotal += $item['quantidade'] * $item['custo_unitario'];
        }
 
        $this->conn->beginTransaction();
 
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO entrada_mercadoria (fornecedor_id, data, status, valor_total)
                VALUES (:fornecedor_id, CURDATE(), 'confirmada', :valor_total)
            ");
            $stmt->execute([
                ':fornecedor_id' => $fornecedorId,
                ':valor_total'   => $valorTotal,
            ]);
            $entradaId = (int) $this->conn->lastInsertId();
 
            $stmtItem = $this->conn->prepare("
                INSERT INTO entrada_item (entrada_id, variacao_id, quantidade, custo_unitario)
                VALUES (:entrada_id, :variacao_id, :quantidade, :custo_unitario)
            ");
 
            $stmtMov = $this->conn->prepare("
                INSERT INTO movimento_estoque (variacao_id, tipo, quantidade, origem, origem_id, data)
                VALUES (:variacao_id, 'entrada', :quantidade, 'entrada', :origem_id, NOW())
            ");
 
            foreach ($itens as $item) {
                $stmtItem->execute([
                    ':entrada_id'     => $entradaId,
                    ':variacao_id'    => $item['variacao_id'],
                    ':quantidade'     => $item['quantidade'],
                    ':custo_unitario' => $item['custo_unitario'],
                ]);
 
                // Credita o estoque (delta positivo)
                $this->estoqueModel->ajustar((int)$item['variacao_id'], (int)$item['quantidade']);
 
                $stmtMov->execute([
                    ':variacao_id' => $item['variacao_id'],
                    ':quantidade'  => $item['quantidade'],
                    ':origem_id'   => $entradaId,
                ]);
            }
 
            $this->conn->commit();
            return $entradaId;
 
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}