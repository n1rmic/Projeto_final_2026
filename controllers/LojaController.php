<?php
require_once __DIR__ . '/SiteBaseController.php';

class LojaController extends SiteBaseController
{
    private const POR_PAGINA = 12;

    /** index.php?controller=loja&action=index[&categoria_id=5][&q=peruca][&pagina=2] */
    public function index(): void
    {
        $vitrine = new Vitrine();
        $filtros = [
            'categoria_id' => (int) ($_GET['categoria_id'] ?? 0),
            'busca'        => trim($_GET['q'] ?? ''),
        ];

        $total = $vitrine->contar($filtros);
        $paginas = max(1, (int) ceil($total / self::POR_PAGINA));
        $pagina = min($paginas, max(1, (int) ($_GET['pagina'] ?? 1)));

        $produtos = $vitrine->listar($filtros, self::POR_PAGINA, ($pagina - 1) * self::POR_PAGINA);
        foreach ($produtos as &$p) {
            $p['imagem'] = imagemProduto((int) $p['id']);
        }
        unset($p);

        $categoriaAtual = null;
        foreach ($vitrine->categoriasAtivas() as $c) {
            if ((int) $c['id'] === $filtros['categoria_id']) {
                $categoriaAtual = $c;
            }
        }

        $this->render('home', [
            'produtos'       => $produtos,
            'total'          => $total,
            'pagina'         => $pagina,
            'paginas'        => $paginas,
            'filtros'        => $filtros,
            'categoriaNome'  => $categoriaAtual['nome'] ?? null,
        ], 'Loja de Cosplay');
    }

    /** index.php?controller=loja&action=produto&id=X */
    public function produto(): void
    {
        $vitrine = new Vitrine();
        $produto = $vitrine->buscarProduto((int) ($_GET['id'] ?? 0));
        if (!$produto) {
            http_response_code(404);
            $this->render('nao_encontrado', ['mensagem' => 'Esse produto não existe ou não está mais à venda.'], 'Produto não encontrado');
            return;
        }

        $variacoes = $vitrine->variacoesDoProduto((int) $produto['id']);
        if (!$variacoes) {
            http_response_code(404);
            $this->render('nao_encontrado', ['mensagem' => 'Esse produto ainda não tem opções disponíveis para compra.'], 'Produto indisponível');
            return;
        }

        // só o necessário vai para o navegador
        $variacoesJs = array_map(fn($v) => [
            'id'      => (int) $v['id'],
            'tamanho' => $v['tamanho'],
            'cor'     => $v['cor'],
            'sku'     => $v['sku'],
            'preco'   => (float) $v['preco'],
            'estoque' => (int) $v['estoque'],
        ], $variacoes);

        $this->render('produto', [
            'produto'     => $produto,
            'imagem'      => imagemProduto((int) $produto['id']),
            'variacoes'   => $variacoesJs,
            'precoMin'    => min(array_column($variacoesJs, 'preco')),
        ], $produto['nome']);
    }

    /**
     * Endpoint JSON da vitrine (alternativa ao produto&action=listarJson, que referencia p.imagem).
     * index.php?controller=loja&action=produtosJson[&categoria_id=5][&q=texto]
     */
    public function produtosJson(): void
    {
        $vitrine = new Vitrine();
        $produtos = $vitrine->listar([
            'categoria_id' => (int) ($_GET['categoria_id'] ?? 0),
            'busca'        => trim($_GET['q'] ?? ''),
        ]);
        $variacoes = $vitrine->variacoesDeProdutos(array_column($produtos, 'id'));

        $saida = [];
        foreach ($produtos as $p) {
            $saida[] = [
                'id'             => (int) $p['id'],
                'nome'           => $p['nome'],
                'descricao'      => $p['descricao'],
                'categoria_id'   => (int) $p['categoria_id'],
                'categoria_nome' => $p['categoria_nome'],
                'imagem'         => imagemProduto((int) $p['id']),
                'preco_min'      => (float) $p['preco_min'],
                'variacoes'      => array_map(fn($v) => [
                    'id'      => (int) $v['id'],
                    'tamanho' => $v['tamanho'],
                    'cor'     => $v['cor'],
                    'sku'     => $v['sku'],
                    'preco'   => (float) $v['preco'],
                    'estoque' => (int) $v['estoque'],
                ], $variacoes[$p['id']] ?? []),
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($saida, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
