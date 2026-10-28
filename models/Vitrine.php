<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Consultas somente-leitura do catálogo, usadas pela vitrine do cliente.
 * Reaproveita as tabelas categoria, produto, variacao e estoque.
 */
class Vitrine
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function categoriasAtivas(): array
    {
        return $this->db
            ->query("SELECT id, nome FROM categoria WHERE ativo = 1 ORDER BY nome")
            ->fetchAll();
    }

    /**
     * Produtos ativos (de categorias ativas) que tenham ao menos uma variação.
     * $filtros: categoria_id (int), busca (string)
     */
    public function listar(array $filtros = [], ?int $limite = null, int $offset = 0): array
    {
        [$where, $params] = $this->montarFiltro($filtros);

        $sql = "SELECT p.id, p.nome, p.descricao, p.categoria_id, c.nome AS categoria_nome,
                       MIN(v.preco) AS preco_min,
                       COALESCE(SUM(e.quantidade), 0) AS estoque_total
                FROM produto p
                JOIN categoria c ON c.id = p.categoria_id
                JOIN variacao v ON v.produto_id = p.id
                LEFT JOIN estoque e ON e.variacao_id = v.id
                WHERE $where
                GROUP BY p.id, p.nome, p.descricao, p.categoria_id, c.nome
                ORDER BY p.nome";
        if ($limite !== null) {
            $sql .= " LIMIT :limite OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $nome => $valor) {
            $stmt->bindValue($nome, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        if ($limite !== null) {
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar(array $filtros = []): int
    {
        [$where, $params] = $this->montarFiltro($filtros);
        $sql = "SELECT COUNT(DISTINCT p.id)
                FROM produto p
                JOIN categoria c ON c.id = p.categoria_id
                JOIN variacao v ON v.produto_id = p.id
                WHERE $where";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $nome => $valor) {
            $stmt->bindValue($nome, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function montarFiltro(array $filtros): array
    {
        $where = ["p.ativo = 1", "c.ativo = 1"];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = "p.categoria_id = :categoria_id";
            $params[':categoria_id'] = (int) $filtros['categoria_id'];
        }
        if (!empty($filtros['busca'])) {
            $where[] = "p.nome LIKE :busca";
            // escapa % e _ digitados pelo usuário
            $busca = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filtros['busca']);
            $params[':busca'] = '%' . $busca . '%';
        }
        return [implode(' AND ', $where), $params];
    }

    /** Um produto ativo (categoria ativa) ou null. */
    public function buscarProduto(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.nome, p.descricao, p.categoria_id, c.nome AS categoria_nome
             FROM produto p
             JOIN categoria c ON c.id = p.categoria_id
             WHERE p.id = :id AND p.ativo = 1 AND c.ativo = 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Variações de um produto com o estoque disponível. */
    public function variacoesDoProduto(int $produtoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.id, v.tamanho, v.cor, v.sku, v.preco,
                    COALESCE(e.quantidade, 0) AS estoque
             FROM variacao v
             LEFT JOIN estoque e ON e.variacao_id = v.id
             WHERE v.produto_id = :id
             ORDER BY v.preco, v.tamanho, v.cor"
        );
        $stmt->execute([':id' => $produtoId]);
        return $stmt->fetchAll();
    }

    /** Variações de vários produtos de uma vez (agrupadas por produto_id). */
    public function variacoesDeProdutos(array $produtoIds): array
    {
        if (!$produtoIds) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($produtoIds), '?'));
        $stmt = $this->db->prepare(
            "SELECT v.id, v.produto_id, v.tamanho, v.cor, v.sku, v.preco,
                    COALESCE(e.quantidade, 0) AS estoque
             FROM variacao v
             LEFT JOIN estoque e ON e.variacao_id = v.id
             WHERE v.produto_id IN ($marcadores)
             ORDER BY v.produto_id, v.preco, v.tamanho, v.cor"
        );
        $stmt->execute(array_map('intval', array_values($produtoIds)));

        $agrupado = [];
        foreach ($stmt->fetchAll() as $linha) {
            $agrupado[$linha['produto_id']][] = $linha;
        }
        return $agrupado;
    }

    /**
     * Variação comprável (produto e categoria ativos) com nome do produto e estoque.
     * Usada para validar o carrinho.
     */
    public function buscarVariacao(int $variacaoId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT v.id, v.produto_id, v.tamanho, v.cor, v.sku, v.preco,
                    COALESCE(e.quantidade, 0) AS estoque,
                    p.nome AS produto_nome
             FROM variacao v
             JOIN produto p ON p.id = v.produto_id
             JOIN categoria c ON c.id = p.categoria_id
             LEFT JOIN estoque e ON e.variacao_id = v.id
             WHERE v.id = :id AND p.ativo = 1 AND c.ativo = 1"
        );
        $stmt->execute([':id' => $variacaoId]);
        return $stmt->fetch() ?: null;
    }
}
