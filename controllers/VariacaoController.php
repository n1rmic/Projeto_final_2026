<?php
require_once __DIR__ . '/../models/Variacao.php';
require_once __DIR__ . '/../models/Produto.php';

class VariacaoController
{
    public function index(): void
    {
        $this->check();

        $produtoId = (int)($_GET['produto_id'] ?? 0);
        if ($produtoId <= 0) {
            header("Location: index.php?controller=produto&action=index");
            exit;
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($produtoId);
        if (!$produto) {
            die("Produto não encontrado.");
        }

        $variacaoModel = new Variacao();
        $variacoes = $variacaoModel->listarPorProduto($produtoId);

        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $variacaoModel->buscarPorId((int)$_GET['id']);
        }

        require_once __DIR__ . '/../views/variacoes.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int)($_POST['id'] ?? 0);
        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $tamanho = trim($_POST['tamanho'] ?? '');
        $tamanho = $tamanho === '' ? null : $tamanho;

        $cor = trim($_POST['cor'] ?? '');
        $cor = $cor === '' ? null : $cor;

        $sku = trim($_POST['sku'] ?? '');
        
        $precoRaw = str_replace(['R$', ' ', '.'], '', $_POST['preco'] ?? '0');
        $precoRaw = str_replace(',', '.', $precoRaw);
        $preco = (float)$precoRaw;

        if ($produtoId <= 0 || $sku === '' || $preco <= 0) {
            die("Preencha os campos obrigatórios (SKU e Preço válido).");
        }

        $variacaoModel = new Variacao();

        try {
            if ($id > 0) {
                $variacaoModel->atualizar($id, $tamanho, $cor, $sku, $preco);
            } else {
                $variacaoModel->inseri($produtoId, $tamanho, $cor, $sku, $preco);
            }
        } catch (\PDOException $e) {
            die("Erro ao salvar SKU. Certifique-se de que o código SKU é único e não duplicado.");
        }

        header("Location: index.php?controller=variacao&action=index&produto_id=" . $produtoId);
        exit;
    }

    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $produtoId = (int)($_GET['produto_id'] ?? 0);

        if ($id <= 0 || $produtoId <= 0) {
            die("Parâmetros inválidos.");
        }

        $variacaoModel = new Variacao();

        try {
            $variacaoModel->deletar($id);
       } catch (\PDOException $e) {
    die("Erro real do MySQL: " . $e->getMessage());
}

        header("Location: index.php?controller=variacao&action=index&produto_id=" . $produtoId);
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