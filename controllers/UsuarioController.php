<?php
require_once __DIR__ . '/../models/usuario.php';


class UsuarioController
{
    // Exibe a tela de formulário
    public function create()
    {
        $this->exigirAdmin();
       require_once __DIR__ . '/../views/cadastro.php';
    }


    // Processa os dados recebidos do formulário
    public function store()
    {
        $this->exigirAdmin();
        csrfExigir();

        // 1. Receber os dados do $_POST
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha_plana = $_POST['senha'] ?? '';


        // Validação básica
        if (empty($nome) || empty($email) || empty($senha_plana)) {
            die("Por favor, preencha todos os campos.");
        }


        // 2. Criptografar a senha
        // Como o AuthController usa password_verify, o banco precisa receber o hash
        $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);


        // 3. Chamar o Model para salvar
        $usuarioModel = new Usuario();
       
        // Passamos os dados. O perfil é vai ser venededor e o ativo é 1 (ativo)
        $sucesso = $usuarioModel->cadastrar($nome, $email, $senha_hash, 'vendedor', 1);


        if ($sucesso) {
            // Se deu certo, manda de volta para o login ou dashboard
            header("Location: index.php?controller=auth&action=dashboard");
            exit;
        } else {
            die("Erro ao cadastrar o vendedor no banco de dados.");
        }
    }

    // Só o administrador logado pode cadastrar vendedores
    private function exigirAdmin(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            http_response_code(403);
            die("Acesso negado.");
        }
    }
}
