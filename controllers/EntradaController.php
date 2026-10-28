<?php
require_once __DIR__ . '/../models/entrada.php';
require_once __DIR__ . '/../models/fornecedor.php';
 
class EntradaController
{
    public function index(): void
    {
        $this->check();
 
        $entradaModel     = new Entrada();
        $fornecedorModel  = new Fornecedor();
   $entradas     = $entradaModel->listarComFornecedor();
        $fornecedores = $fornecedorModel->listarAtivos();
 
        // Para simplificar a tela, buscamos as variações direto aqui (SQL cru é aceitável
        // em telas de apoio simples; em um projeto maior, isso viraria um Model Variacao).
        $conn = Database::getConnection();
        $variacoes = $conn->query("
            SELECT v.id, v.sku, v.tamanho, v.cor, p.nome AS produto_nome
            FROM variacao v
            INNER JOIN produto p ON p.id = v.produto_id
            WHERE p.ativo = 1
            ORDER BY p.nome
        ")->fetchAll();
 
        require_once __DIR__ . '/../views/entradas.php';
    }
 
    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();
 
        $fornecedorId = (int) ($_POST['fornecedor_id'] ?? 0);
        $variacaoIds  = $_POST['variacao_id'] ?? [];
        $quantidades  = $_POST['quantidade'] ?? [];
        $custos       = $_POST['custo_unitario'] ?? [];
 
        if ($fornecedorId <= 0) {
            die("Selecione um fornecedor.");
        }
 
        $itens = [];
        foreach ($variacaoIds as $i => $variacaoId) {
            $variacaoId = (int) $variacaoId;
            $quantidade = (int) ($quantidades[$i] ?? 0);
            $custo      = (float) str_replace(',', '.', $custos[$i] ?? '0');
 
            if ($variacaoId > 0 && $quantidade > 0) {
                $itens[] = [
                    'variacao_id'    => $variacaoId,
                    'quantidade'     => $quantidade,
                    'custo_unitario' => $custo,
                ];
            }
        }
 
        if (empty($itens)) {
            die("Informe ao menos um item válido para a entrada.");
        }
 
        $entradaModel = new Entrada();
 
        try {
            $entradaModel->registrar($fornecedorId, $itens);
        } catch (Throwable $e) {
            die("Erro ao registrar entrada: " . htmlspecialchars($e->getMessage()));
        }
 
        header("Location: index.php?controller=entrada&action=index");
        exit;
    }
 
    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
      }
    }
 
    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            die("Acesso negado.");
        }
    }
}
