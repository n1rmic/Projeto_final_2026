<?php
require_once __DIR__ . '/../models/Vitrine.php';
require_once __DIR__ . '/../models/Carrinho.php';

/**
 * Base dos controllers do CLIENTE (loja, carrinho, cliente, pedido).
 * A sessão do cliente ($_SESSION['cliente_id'], ['cliente_nome']) é separada
 * da do vendedor/admin ($_SESSION['usuario_id'], ['perfil']).
 */
abstract class SiteBaseController
{
    /** Renderiza views/site/{view}.php dentro do cabeçalho/rodapé da loja. */
    protected function render(string $view, array $dados = [], string $titulo = 'Loja de Cosplay'): void
    {
        $carrinho = new Carrinho();
        $layout = [
            'titulo'        => $titulo,
            'categorias'    => (new Vitrine())->categoriasAtivas(),
            'qtdCarrinho'   => $carrinho->contar(),
            'clienteNome'   => $_SESSION['cliente_nome'] ?? null,
            'flashes'       => pegarFlashes(),
            'buscaAtual'    => trim($_GET['q'] ?? ''),
            'categoriaAtual' => (int) ($_GET['categoria_id'] ?? 0),
        ];
        extract($layout);
        extract($dados);

        require BASE_PATH . '/views/site/_header.php';
        require BASE_PATH . '/views/site/' . $view . '.php';
        require BASE_PATH . '/views/site/_footer.php';
    }

    protected function clienteLogado(): bool
    {
        return !empty($_SESSION['cliente_id']);
    }

    /** CPF do cliente logado (é o "id" do cliente do site). */
    protected function clienteCpf(): string
    {
        return (string) $_SESSION['cliente_id'];
    }

    /** Exige login; guarda para onde voltar depois de entrar. */
    protected function exigirLogin(): void
    {
        if ($this->clienteLogado()) {
            return;
        }
        $_SESSION['redirect_apos_login'] = $_SERVER['REQUEST_METHOD'] === 'GET'
            ? 'index.php?' . ($_SERVER['QUERY_STRING'] ?? '')
            : url('carrinho', 'index');
        flash('aviso', 'Entre na sua conta para continuar a compra.');
        redirecionar(url('cliente', 'login'));
    }

    protected function iniciarSessaoCliente(array $cliente): void
    {
        session_regenerate_id(true);
        $_SESSION['cliente_id'] = $cliente['cpf'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
    }

    /** Aceita só POST com token CSRF válido; senão volta para $voltarPara. */
    protected function exigirPost(string $voltarPara): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar($voltarPara);
        }
        if (!csrfValido()) {
            flash('erro', 'Sua sessão expirou. Tente de novo.');
            redirecionar($voltarPara);
        }
    }
}
