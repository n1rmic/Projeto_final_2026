<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Categoria.php';

class ProdutoController
{
    public function index(): void
    {
        $this->check();
        $produtoModel = new Produto();
        $categoriaModel = new Categoria();
        
        $produtos = $produtoModel->listarComCategoria(false);
        $categorias = $categoriaModel->listarAtivas();
        
        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $produtoModel->buscarPorId((int)$_GET['id']);
        }
        
        require_once __DIR__ . '/../views/produtos.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();
        
        $id = (int)($_POST['id'] ?? 0);
        $categoriaId = (int)($_POST['categoria_id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $descricao = $descricao === '' ? null : $descricao;
        
        if ($categoriaId <= 0 || $nome === '') {
            die("Dados inválidos.");
        }
        
        $produtoModel = new Produto();
        
        if ($id > 0) {
            // Atualiza produto
            $produtoModel->atualizar($id, $categoriaId, $nome, $descricao);
            $this->salvarImagemDoProduto($id); // upload opcional
        } else {
            // Insere produto e pega ID para nomear a imagem
            $novoId = $produtoModel->inserir($categoriaId, $nome, $descricao);
            $this->salvarImagemDoProduto($novoId); // upload opcional
        }
        
        header("Location: index.php?controller=produto&action=index");
        exit;
    }

    public function toggle(): void
    {
        $this->check();
        $this->onlyAdmin();
        
        $id = (int)($_GET['id'] ?? 0);
        $ativo = (int)($_GET['ativo'] ?? 1);
        
        if ($id <= 0) die("ID inválido.");
        
        $produtoModel = new Produto();
        $produtoModel->setAtivo($id, $ativo === 1);
        
        header("Location: index.php?controller=produto&action=index");
        exit;
    }

    // MÉTODO ADICIONADO PARA EXCLUIR O PRODUTO E A IMAGEM
    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) die("ID inválido.");

        // 1. Apaga as imagens do produto na pasta uploads
        $destDir = __DIR__ . '/../public/uploads/produtos/';
        foreach (['jpg', 'png', 'webp'] as $ext) {
            $arquivo = $destDir . $id . '.' . $ext;
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }
        }

        // 2. Apaga o registro do banco de dados
        $produtoModel = new Produto();
        $produtoModel->deletar($id);

        header("Location: index.php?controller=produto&action=index");
        exit;
    }

    // -------------------------
    // Upload (POO + seguro)
    // -------------------------
    private function salvarImagemDoProduto(int $produtoId): void
    {
        if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
            return; // sem imagem
        }
        
        // limita tamanho (2MB)
        if (($_FILES['imagem']['size'] ?? 0) > 2 * 1024 * 1024) {
            return; // em produção: mostrar mensagem
        }
        
        $tmp = $_FILES['imagem']['tmp_name'];
        $mime = mime_content_type($tmp);
        
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null
        };
        
        if ($ext === null) return;
        
        $destDir = __DIR__ . '/../public/uploads/produtos/';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        
        // remove versões antigas (se trocar)
        foreach (['jpg','png','webp'] as $e) {
            $old = $destDir . $produtoId . '.' . $e;
            if (file_exists($old)) unlink($old);
        }
        
        $dest = $destDir . $produtoId . '.' . $ext;
        move_uploaded_file($tmp, $dest);
    }

    // -------------------------
    // Segurança básica de sessão
    // -------------------------
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

public function listarJson(): void
{
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $conn = Database::getConnection();
        $stmt = $conn->query("
            SELECT p.id, p.nome, p.imagem, COALESCE(MIN(v.preco), 0) AS preco, v.sku
            FROM produto p
            LEFT JOIN variacao v ON v.produto_id = p.id
            WHERE p.ativo = 1
            GROUP BY p.id, p.nome, p.imagem
            ORDER BY p.nome ASC
        ");

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['erro' => $e->getMessage()]);
    }
    exit;
}
}