<?php
require_once __DIR__ . '/SiteBaseController.php';

class CarrinhoController extends SiteBaseController
{
    /** index.php?controller=carrinho&action=index */
    public function index(): void
    {
        $carrinho = new Carrinho();
        foreach ($carrinho->sincronizar() as $aviso) {
            flash('aviso', $aviso);
        }
        $this->render('carrinho', ['carrinho' => $carrinho], 'Meu carrinho');
    }

    /** POST: variacao_id, quantidade, [comprar_agora=1] */
    public function adicionar(): void
    {
        $variacaoId = (int) ($_POST['variacao_id'] ?? 0);
        $volta = url('carrinho', 'index');
        $this->exigirPost($volta);

        // se falhar, volta para a página do produto (quando dá para descobrir qual é)
        $paginaProduto = url('loja', 'index');
        if ($variacaoId > 0) {
            $variacao = (new Vitrine())->buscarVariacao($variacaoId);
            if ($variacao) {
                $paginaProduto = url('loja', 'produto', ['id' => $variacao['produto_id']]);
            }
        }

        try {
            (new Carrinho())->adicionar($variacaoId, (int) ($_POST['quantidade'] ?? 1));
        } catch (RegraNegocioException $ex) {
            flash('erro', $ex->getMessage());
            redirecionar($paginaProduto);
        }

        if (!empty($_POST['comprar_agora'])) {
            redirecionar(url('pedido', 'checkout'));
        }
        flash('ok', 'Produto adicionado ao carrinho.');
        redirecionar($volta);
    }

    /** POST: variacao_id, quantidade */
    public function atualizar(): void
    {
        $volta = url('carrinho', 'index');
        $this->exigirPost($volta);

        try {
            (new Carrinho())->alterar((int) ($_POST['variacao_id'] ?? 0), (int) ($_POST['quantidade'] ?? 0));
        } catch (RegraNegocioException $ex) {
            flash('erro', $ex->getMessage());
        }
        redirecionar($volta);
    }

    /** POST: variacao_id */
    public function remover(): void
    {
        $volta = url('carrinho', 'index');
        $this->exigirPost($volta);

        (new Carrinho())->remover((int) ($_POST['variacao_id'] ?? 0));
        flash('ok', 'Item removido do carrinho.');
        redirecionar($volta);
    }
}
