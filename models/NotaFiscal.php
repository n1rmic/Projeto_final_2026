<?php
require_once __DIR__ . '/../config/db.php';
 
class NotaFiscal
{
    private PDO $conn;
 
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }
 
    // -------------------------
    // Nota Fiscal de VENDA
    // -------------------------
 
    public function buscarPorVenda(int $vendaId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM nota_fiscal_venda WHERE venda_id = :venda_id"
        );
        $stmt->execute([':venda_id' => $vendaId]);
        $r = $stmt->fetch();
        return $r ?: null;
    }
 
    /**
     * Emite a NF de uma venda já finalizada. Gera um número sequencial
     * único por série/modelo, conforme a regra de negócio da Etapa 5.
     */
    public function emitirParaVenda(int $vendaId, string $modelo = 'NFCe', string $serie = '1'): int
    {
        $jaEmitida = $this->buscarPorVenda($vendaId);
        if ($jaEmitida) {
            throw new RuntimeException("Esta venda já possui nota fiscal emitida (#{$jaEmitida['numero']}).");
        }
 
        $stmtVenda = $this->conn->prepare("SELECT valor_total FROM venda WHERE id = :id");
        $stmtVenda->execute([':id' => $vendaId]);
        $venda = $stmtVenda->fetch();
 
        if (!$venda) {
            throw new InvalidArgumentException("Venda não encontrada.");
        }


        $numero = $this->proximoNumero('nota_fiscal_venda', $modelo, $serie);
 
        $stmt = $this->conn->prepare("
            INSERT INTO nota_fiscal_venda (venda_id, modelo, serie, numero, data_emissao, valor_total)
            VALUES (:venda_id, :modelo, :serie, :numero, CURDATE(), :valor_total)
        ");
        $stmt->execute([
            ':venda_id'   => $vendaId,
            ':modelo'     => $modelo,
            ':serie'      => $serie,
            ':numero'     => $numero,
            ':valor_total'=> $venda['valor_total'],
        ]);
 
        return (int) $this->conn->lastInsertId();
    }
 
 
 
 
    // -------------------------
    // Auxiliar
    // -------------------------
 
    /**
     * Gera o próximo número sequencial de NF, único por série/modelo,
     * exatamente como pede a regra de negócio da Etapa 5.
     */
    private function proximoNumero(string $tabela, string $modelo, string $serie): string
    {
        $stmt = $this->conn->prepare("
            SELECT MAX(CAST(numero AS UNSIGNED)) AS ultimo
            FROM {$tabela}
            WHERE modelo = :modelo AND serie = :serie
        ");
        $stmt->execute([':modelo' => $modelo, ':serie' => $serie]);
        $ultimo = (int) ($stmt->fetch()['ultimo'] ?? 0);
 
        return (string) ($ultimo + 1);
    }
}
