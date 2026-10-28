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
    // Nota Fiscal de ENTRADA
    // -------------------------
 
    public function buscarPorEntrada(int $entradaId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM nota_fiscal_entrada WHERE entrada_id = :entrada_id"
        );
        $stmt->execute([':entrada_id' => $entradaId]);
        $r = $stmt->fetch();
        return $r ?: null;
    }
 
    /**
     * Registra a NF de entrada vinculada a uma compra já confirmada.
     * A chave de acesso é única — normalmente vem do fornecedor (XML da NF-e),
     * mas aqui simulamos sua geração para fins didáticos.
     */
    public function registrarParaEntrada(int $entradaId, string $modelo, string $serie, string $numero, string $chaveAcesso): int
    {
        $jaExiste = $this->buscarPorEntrada($entradaId);
        if ($jaExiste) {
            throw new RuntimeException("Esta entrada já possui nota fiscal registrada.");
        }
 
        $stmtEntrada = $this->conn->prepare("SELECT valor_total FROM entrada_mercadoria WHERE id = :id");
        $stmtEntrada->execute([':id' => $entradaId]);
        $entrada = $stmtEntrada->fetch();
 
        if (!$entrada) {
            throw new InvalidArgumentException("Entrada não encontrada.");
        }
 
        $stmt = $this->conn->prepare("
            INSERT INTO nota_fiscal_entrada
                (entrada_id, modelo, serie, numero, chave_acesso, data_emissao, valor_total)
            VALUES
                (:entrada_id, :modelo, :serie, :numero, :chave_acesso, CURDATE(), :valor_total)
        ");
        $stmt->execute([
            ':entrada_id'  => $entradaId,
            ':modelo'      => $modelo,
            ':serie'       => $serie,
            ':numero'      => $numero,
            ':chave_acesso'=> $chaveAcesso,
       ':valor_total' => $entrada['valor_total'],
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
