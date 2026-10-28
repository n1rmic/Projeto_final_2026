<?php
require_once __DIR__ . '/../models/Fornecedor.php';
 
class FornecedorController
{
    public function index(): void
    {
        $this->check();
        $this->onlyAdmin();
 
        $fornecedorModel = new Fornecedor();
        $fornecedores = $fornecedorModel->listarTodos();
 
        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $fornecedorModel->buscarPorId((int) $_GET['id']);
        }
 
        require_once __DIR__ . '/../views/fornecedores.php';
    }
 
    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();
 
        $id       = (int) ($_POST['id'] ?? 0);
        $nome     = trim($_POST['nome'] ?? '');
        $cnpj     = trim($_POST['cnpj'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
 
        $cnpj     = $cnpj === '' ? null : $cnpj;
        $telefone = $telefone === '' ? null : $telefone;
        $email    = $email === '' ? null : $email;
        $endereco = $endereco === '' ? null : $endereco;
 
        if ($nome === '') {
            die("Nome do fornecedor é obrigatório.");
        }
   $fornecedorModel = new Fornecedor();
 
        if ($id > 0) {
            $fornecedorModel->atualizar($id, $nome, $cnpj, $telefone, $email, $endereco);
        } else {
            $fornecedorModel->inserir($nome, $cnpj, $telefone, $email, $endereco);
        }
 
        header("Location: index.php?controller=fornecedor&action=index");
        exit;
    }
 
    public function toggle(): void
    {
        $this->check();
        $this->onlyAdmin();
 
        $id    = (int) ($_GET['id'] ?? 0);
        $ativo = (int) ($_GET['ativo'] ?? 1);
 
        if ($id <= 0) die("ID inválido.");
 
        $fornecedorModel = new Fornecedor();
        $fornecedorModel->setAtivo($id, $ativo === 1);
 
        header("Location: index.php?controller=fornecedor&action=index");
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
