<?php
require_once __DIR__ . '/../models/usuario.php';
class AuthController
{
public function form()
{
require_once __DIR__ . '/../views/login.php';
}
public function login()
{
csrfExigir();
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$usuarioModel = new Usuario();
$user = $usuarioModel->buscarPorEmail($email);
// Sempre roda password_verify (mesmo sem usuário) e usa uma única mensagem de erro,
// para não revelar se o e-mail existe nem se a conta está inativa.
$hash = $user['senha'] ?? '$2y$10$abcdefghijklmnopqrstuuAbCdEfGhIjKlMnOpQrStUvWxYzAbCdE';
$ok = password_verify($senha, $hash);
if (!$user || (int)$user['ativo'] !== 1 || !$ok) {
die("E-mail ou senha inválidos.");
}
// novo id de sessão após autenticar (evita fixação de sessão)
session_regenerate_id(true);
$_SESSION['usuario_id'] = $user['id'];
$_SESSION['perfil'] = $user['perfil'];
$_SESSION['nome'] = $user['nome'];
header("Location: index.php?controller=auth&action=dashboard");
exit;
}

public function dashboard()
{
    $this->check();
 
    require_once __DIR__ . '/../models/Relatorio.php';
    $relatorioModel = new Relatorio();
 
    $kpiVendasMes    = $relatorioModel->kpiVendasMes();
    $kpiEntradasMes  = $relatorioModel->kpiEntradasMes();
    $kpiEstoqueBaixo = $relatorioModel->kpiEstoqueBaixo();
    $kpiTotalProdutos = $relatorioModel->kpiTotalProdutos();
    $kpiTotalEstoque  = $relatorioModel->kpiTotalEstoque();
 
    require_once __DIR__ . '/../views/dashboard.php';
}



public function logout()
{
session_destroy();
header("Location: index.php?controller=auth&action=form");
exit;
}
public function check()
{
if (!isset($_SESSION['usuario_id'])) {
header("Location: index.php?controller=auth&action=form");
exit;
} }
public function onlyAdmin()
{
$this->check();
if (($_SESSION['perfil'] ?? '') !== 'admin') {
die("Acesso negado.");
} } }
