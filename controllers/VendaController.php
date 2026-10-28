<?php
require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../models/cliente.php';
class VendaController
{
    public function index(): void
    {
        $this->check();

        $vendaModel = new Venda();
        $vendas = $vendaModel->listarRecentes();

        $conn = Database::getConnection();

        $clientes = $conn->query("SELECT id, nome FROM cliente ORDER BY nome")->fetchAll();

        // Traz as variações com o saldo atual em estoque (LEFT JOIN no estoque)
        $variacoes = $conn->query("
            SELECT v.id, v.sku, v.tamanho, v.cor, v.preco, p.nome AS produto_nome,
                   COALESCE(e.quantidade, 0) AS estoque_qtd
            FROM variacao v
            INNER JOIN produto p ON p.id = v.produto_id
            LEFT JOIN estoque e ON e.variacao_id = v.id
            WHERE p.ativo = 1
            ORDER BY p.nome ASC, v.sku ASC
        ")->fetchAll();

        require_once __DIR__ . '/../views/vendas.php';
    }

    public function salvar(): void
    {
        $this->check();

        $clienteId   = (int) ($_POST['cliente_id'] ?? 0);
        $variacaoIds = $_POST['variacao_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];
        $precos      = $_POST['preco_unitario'] ?? [];

        if ($clienteId <= 0) {
            $_SESSION['erro'] = "Selecione o cliente para abrir a venda no caixa.";
            header("Location: index.php?controller=venda&action=index");
            exit;
        }

        $itens = [];
        foreach ($variacaoIds as $i => $variacaoId) {
            $variacaoId = (int) $variacaoId;
            $quantidade = (int) ($quantidades[$i] ?? 0);
            
            // Trata formatação de preço (br -> float)
            $precoRaw   = str_replace(['R$', ' ', '.'], '', $precos[$i] ?? '0');
            $precoRaw   = str_replace(',', '.', $precoRaw);
            $preco      = (float) $precoRaw;

            if ($variacaoId > 0 && $quantidade > 0) {
                $itens[] = [
                    'variacao_id'    => $variacaoId,
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $preco,
                ];
            }
        }

        if (empty($itens)) {
            $_SESSION['erro'] = "Informe ao menos um item com quantidade maior que zero para registrar a venda.";
            header("Location: index.php?controller=venda&action=index");
            exit;
        }

        $vendaModel = new Venda();
        $usuarioId  = (int) $_SESSION['usuario_id'];

        try {
            $vendaId = $vendaModel->registrar($usuarioId, $clienteId, $itens);
            $_SESSION['sucesso'] = "Venda #{$vendaId} finalizada com sucesso no caixa!";
        } catch (RuntimeException $e) {
            $_SESSION['erro'] = "Não foi possível concluir a venda: " . $e->getMessage();
        } catch (Throwable $e) {
            $_SESSION['erro'] = "Erro no caixa ao registrar a venda: " . $e->getMessage();
        }

        header("Location: index.php?controller=venda&action=index");
        exit;
    }

    public function emitirNota(): void
    {
        $this->check();

        $vendaId = (int) ($_GET['venda_id'] ?? 0);
        if ($vendaId <= 0) {
            $_SESSION['erro'] = "Venda inválida.";
            header("Location: index.php?controller=venda&action=index");
            exit;
        }

        require_once __DIR__ . '/../models/NotaFiscal.php';
        $notaModel = new NotaFiscal();

        try {
            $notaModel->emitirParaVenda($vendaId);
            $_SESSION['sucesso'] = "Nota Fiscal emitida com sucesso para a venda #{$vendaId}!";
        } catch (Throwable $e) {
            $_SESSION['erro'] = "Não foi possível emitir a nota fiscal: " . $e->getMessage();
        }

        header("Location: index.php?controller=venda&action=index");
        exit;
    }

    public function verNota(): void
    {
        $this->check();

        $vendaId = (int) ($_GET['venda_id'] ?? 0);
        if ($vendaId <= 0) die("Venda inválida.");

        require_once __DIR__ . '/../models/NotaFiscal.php';
        $notaModel = new NotaFiscal();
        $nota = $notaModel->buscarPorVenda($vendaId);

        if (!$nota) {
            $_SESSION['erro'] = 'Esta venda ainda não possui nota fiscal emitida. Clique em "Emitir NF" primeiro.';
            header("Location: index.php?controller=venda&action=index");
            exit;
        }

        $vendaModel = new Venda();
        $venda = $vendaModel->buscarPorId($vendaId);

        if (!$venda) {
            die("Venda não encontrada.");
        }

        require_once __DIR__ . '/../views/nota_fiscal_venda.php';
    }

    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

/** GET (AJAX): cpf -> JSON. Busca em `cliente`; se não achar, tenta puxar de `cliente_site`. */
public function buscarClientePorCpf(): void
{
    $this->check();
    header('Content-Type: application/json; charset=utf-8');

    $cpf = preg_replace('/\D+/', '', (string) ($_GET['cpf'] ?? ''));
    if (strlen($cpf) !== 11) {
        echo json_encode(['encontrado' => false, 'mensagem' => 'Informe um CPF com 11 dígitos.']);
        exit;
    }

    $clienteModel = new Cliente();
    $cliente = $clienteModel->buscarPorCpf($cpf);

    if (!$cliente) {
        require_once __DIR__ . '/../models/ClienteSite.php';
        $clienteSite = (new ClienteSite())->buscarPorCpf($cpf);
        if ($clienteSite) {
            $novoId = $clienteModel->criarAPartirDoSite($clienteSite);
            $cliente = $clienteModel->buscarPorId($novoId);
        }
    }

    if (!$cliente) {
        echo json_encode(['encontrado' => false, 'mensagem' => 'CPF não encontrado. Use "Cliente Balcao" ou cadastre um cliente novo.']);
        exit;
    }

    echo json_encode(['encontrado' => true, 'id' => (int) $cliente['id'], 'nome' => $cliente['nome']]);
    exit;
}
}