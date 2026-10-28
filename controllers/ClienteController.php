<?php
require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../models/ClienteSite.php';

class ClienteController extends SiteBaseController
{
    private const SENHA_MIN = 8;
    private const SENHA_MAX = 72; // limite do bcrypt

    /* ------------------------------ cadastro ------------------------------ */

    /** index.php?controller=cliente&action=cadastro  (GET mostra, POST grava) */
    public function cadastro(): void
    {
        if ($this->clienteLogado()) {
            redirecionar(url('cliente', 'meusDados'));
        }

        $old = ['nome' => '', 'email' => '', 'cpf' => '', 'endereco' => '', 'telefone' => ''];
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrfValido()) {
                $erros['geral'] = 'Sua sessão expirou. Envie o formulário de novo.';
            } else {
                $old = $this->lerDadosCadastro();
                $senha = (string) ($_POST['senha'] ?? '');
                $confirma = (string) ($_POST['senha_confirmacao'] ?? '');
                $model = new ClienteSite();

                $erros = $this->validarDados($old, null, $model);
                $erros += $this->validarNovaSenha($senha, $confirma, 'senha', 'senha_confirmacao');

                if (!isset($erros['cpf']) && $model->buscarPorCpf($old['cpf'])) {
                    $erros['cpf'] = 'Já existe um cadastro com esse CPF.';
                }

                if (!$erros) {
                    try {
                        $model->criar($old + ['senha' => $senha]);
                        $this->iniciarSessaoCliente(['cpf' => $old['cpf'], 'nome' => $old['nome']]);
                        flash('ok', 'Cadastro concluído. Bem-vindo(a), ' . $old['nome'] . '!');
                        $this->voltarParaOndeParou();
                    } catch (RegraNegocioException $ex) {
                        $erros['geral'] = $ex->getMessage();
                    }
                }
            }
        }

        $this->render('cadastro', ['old' => $old, 'erros' => $erros], 'Criar conta');
    }

    /* ------------------------------- login -------------------------------- */

    /** index.php?controller=cliente&action=login */
    public function login(): void
    {
        if ($this->clienteLogado()) {
            $this->voltarParaOndeParou();
        }

        $email = '';
        $erro = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = mb_strtolower(trim($_POST['email'] ?? ''));
            if (!csrfValido()) {
                $erro = 'Sua sessão expirou. Tente entrar de novo.';
            } else {
                $cliente = (new ClienteSite())->autenticar($email, (string) ($_POST['senha'] ?? ''));
                if ($cliente) {
                    $this->iniciarSessaoCliente($cliente);
                    $this->voltarParaOndeParou();
                }
                $erro = 'E-mail ou senha incorretos.';
            }
        }

        $this->render('login', ['email' => $email, 'erro' => $erro], 'Entrar');
    }

    public function logout(): void
    {
        // só encerra a sessão do CLIENTE; a do vendedor/admin (usuario_id) não é tocada
        $this->exigirPost(url('loja', 'index'));
        unset($_SESSION['cliente_id'], $_SESSION['cliente_nome'], $_SESSION['redirect_apos_login']);
        session_regenerate_id(true);
        flash('ok', 'Você saiu da sua conta.');
        redirecionar(url('loja', 'index'));
    }

    /* ---------------------------- meus dados ------------------------------ */

    /** index.php?controller=cliente&action=meusDados  (GET mostra, POST salva) */
    public function meusDados(): void
    {
        $this->exigirLogin();
        $model = new ClienteSite();
        $cliente = $model->buscarPorCpf($this->clienteCpf());
        if (!$cliente) { // cadastro removido com a sessão aberta
            unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);
            redirecionar(url('cliente', 'login'));
        }

        $old = [
            'nome' => $cliente['nome'], 'email' => $cliente['email'], 'cpf' => $cliente['cpf'],
            'endereco' => $cliente['endereco'], 'telefone' => (string) $cliente['telefone'],
        ];
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrfValido()) {
                $erros['geral'] = 'Sua sessão expirou. Envie o formulário de novo.';
            } else {
                $old = $this->lerDadosCadastro();
                $old['cpf'] = $cliente['cpf']; // CPF é a chave: não muda por aqui
                $erros = $this->validarDados($old, $cliente['cpf'], $model);

                if (!$erros) {
                    try {
                        $model->atualizar($cliente['cpf'], $old);
                        $_SESSION['cliente_nome'] = $old['nome'];
                        flash('ok', 'Dados atualizados.');
                        redirecionar(url('cliente', 'meusDados'));
                    } catch (RegraNegocioException $ex) {
                        $erros['geral'] = $ex->getMessage();
                    }
                }
            }
        }

        $this->render('meus_dados', ['old' => $old, 'erros' => $erros, 'errosSenha' => []], 'Meus dados');
    }

    /** POST: senha_atual, senha, senha_confirmacao */
    public function alterarSenha(): void
    {
        $this->exigirLogin();
        $this->exigirPost(url('cliente', 'meusDados'));

        $model = new ClienteSite();
        $cliente = $model->buscarPorCpf($this->clienteCpf());
        if (!$cliente) {
            unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);
            redirecionar(url('cliente', 'login'));
        }
        $erros = [];

        if (!password_verify((string) ($_POST['senha_atual'] ?? ''), $cliente['senha_hash'])) {
            $erros['senha_atual'] = 'A senha atual não confere.';
        }
        $erros += $this->validarNovaSenha(
            (string) ($_POST['senha'] ?? ''), (string) ($_POST['senha_confirmacao'] ?? ''), 'senha', 'senha_confirmacao'
        );

        if (!$erros) {
            $model->atualizarSenha($cliente['cpf'], (string) $_POST['senha']);
            session_regenerate_id(true);
            flash('ok', 'Senha alterada.');
            redirecionar(url('cliente', 'meusDados'));
        }

        $old = [
            'nome' => $cliente['nome'], 'email' => $cliente['email'], 'cpf' => $cliente['cpf'],
            'endereco' => $cliente['endereco'], 'telefone' => (string) $cliente['telefone'],
        ];
        $this->render('meus_dados', ['old' => $old, 'erros' => [], 'errosSenha' => $erros], 'Meus dados');
    }

    /* ------------------------------ apoio --------------------------------- */

    private function lerDadosCadastro(): array
    {
        return [
            'nome'     => trim((string) ($_POST['nome'] ?? '')),
            'email'    => mb_strtolower(trim((string) ($_POST['email'] ?? ''))),
            'cpf'      => somenteDigitos((string) ($_POST['cpf'] ?? '')),
            'endereco' => trim((string) ($_POST['endereco'] ?? '')),
            'telefone' => somenteDigitos((string) ($_POST['telefone'] ?? '')),
        ];
    }

    /** Valida nome, e-mail, CPF, endereço e telefone. $cpfAtual != null = edição. */
    private function validarDados(array $d, ?string $cpfAtual, ClienteSite $model): array
    {
        $erros = [];

        if (mb_strlen($d['nome']) < 3 || mb_strlen($d['nome']) > 100) {
            $erros['nome'] = 'Informe seu nome completo (3 a 100 caracteres).';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 100) {
            $erros['email'] = 'Informe um e-mail válido.';
        } elseif ($model->emailEmUso($d['email'], $cpfAtual)) {
            $erros['email'] = 'Esse e-mail já está cadastrado.';
        }
       
        if (mb_strlen($d['endereco']) < 10 || mb_strlen($d['endereco']) > 255) {
            $erros['endereco'] = 'Informe rua, número, bairro, cidade/UF e CEP (até 255 caracteres).';
        }
        if ($d['telefone'] !== '' && !in_array(strlen($d['telefone']), [10, 11], true)) {
            $erros['telefone'] = 'Telefone deve ter DDD + 8 ou 9 dígitos.';
        }
        return $erros;
    }

    private function validarNovaSenha(string $senha, string $confirma, string $campo, string $campoConfirma): array
    {
        $erros = [];
        if (strlen($senha) < self::SENHA_MIN) {
            $erros[$campo] = 'A senha precisa ter pelo menos ' . self::SENHA_MIN . ' caracteres.';
        } elseif (strlen($senha) > self::SENHA_MAX) {
            $erros[$campo] = 'A senha pode ter no máximo ' . self::SENHA_MAX . ' caracteres.';
        } elseif ($senha !== $confirma) {
            $erros[$campoConfirma] = 'A confirmação não é igual à senha.';
        }
        return $erros;
    }

    /** Depois de entrar/cadastrar: volta para onde o cliente estava (ex.: carrinho/checkout). */
    private function voltarParaOndeParou(): void
    {
        $destino = destinoInternoSeguro($_SESSION['redirect_apos_login'] ?? null, url('loja', 'index'));
        unset($_SESSION['redirect_apos_login']);
        redirecionar($destino);
    }

    /** POST: senha (confirmação) */
public function excluirConta(): void
{
    $this->exigirLogin();
    $this->exigirPost(url('cliente', 'meusDados'));

    $model = new ClienteSite();
    $cliente = $model->buscarPorCpf($this->clienteCpf());
    if (!$cliente) {
        redirecionar(url('cliente', 'login'));
    }

    if (!password_verify((string) ($_POST['senha'] ?? ''), $cliente['senha_hash'])) {
        flash('erro', 'Senha incorreta. A conta não foi excluída.');
        redirecionar(url('cliente', 'meusDados'));
    }

    require_once __DIR__ . '/../models/Pedido.php';
    $temPedidos = (bool) (new Pedido())->listarDoCliente($cliente['cpf']);

    if ($temPedidos) {
        $model->anonimizar($cliente['cpf']);
        $mensagem = 'Sua conta foi encerrada. Como você possui pedidos, o histórico de compras foi mantido de forma anônima.';
    } else {
        $model->excluir($cliente['cpf']);
        $mensagem = 'Sua conta foi excluída com sucesso.';
    }

    unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);
    session_regenerate_id(true);
    flash('ok', $mensagem);
    redirecionar(url('loja', 'index'));
}
}
