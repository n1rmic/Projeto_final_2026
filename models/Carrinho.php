<?php
require_once __DIR__ . '/Vitrine.php';

/**
 * Carrinho guardado na sessão PHP: $_SESSION['carrinho'][variacao_id] = item.
 * Item: variacao_id, produto_id, produto_nome, tamanho, cor, preco_unitario, quantidade, imagem
 *
 * Preço e estoque da sessão são só "cache" de exibição: sincronizar() e o
 * Pedido::criar() sempre conferem de novo no banco.
 */
class Carrinho
{
    private Vitrine $vitrine;

    public function __construct()
    {
        $this->vitrine = new Vitrine();
        if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
    }

    public function itens(): array
    {
        return array_values($_SESSION['carrinho']);
    }

    public function vazio(): bool
    {
        return count($_SESSION['carrinho']) === 0;
    }

    /** Quantidade total de unidades (contador do ícone do carrinho). */
    public function contar(): int
    {
        $total = 0;
        foreach ($_SESSION['carrinho'] as $item) {
            $total += $item['quantidade'];
        }
        return $total;
    }

    public function subtotal(array $item): float
    {
        return round($item['preco_unitario'] * $item['quantidade'], 2);
    }

    public function total(): float
    {
        $total = 0.0;
        foreach ($_SESSION['carrinho'] as $item) {
            $total += $this->subtotal($item);
        }
        return round($total, 2);
    }

    public function limpar(): void
    {
        $_SESSION['carrinho'] = [];
    }

    /**
     * Adiciona (soma) uma quantidade de uma variação.
     * @throws RegraNegocioException se a variação não existe ou não há estoque
     */
    public function adicionar(int $variacaoId, int $quantidade): void
    {
        if ($quantidade < 1) {
            throw new RegraNegocioException('Informe uma quantidade válida.');
        }
        $variacao = $this->vitrine->buscarVariacao($variacaoId);
        if (!$variacao) {
            throw new RegraNegocioException('Essa opção do produto não está mais disponível.');
        }
        $estoque = (int) $variacao['estoque'];
        if ($estoque <= 0) {
            throw new RegraNegocioException('Essa opção está esgotada.');
        }

        $jaNoCarrinho = $_SESSION['carrinho'][$variacaoId]['quantidade'] ?? 0;
        $novaQtd = $jaNoCarrinho + $quantidade;
        if ($novaQtd > $estoque) {
            $restante = max(0, $estoque - $jaNoCarrinho);
            throw new RegraNegocioException(
                $restante > 0
                    ? "Só há mais {$restante} unidade(s) disponível(is) dessa opção."
                    : 'Você já tem todo o estoque dessa opção no carrinho.'
            );
        }

        $_SESSION['carrinho'][$variacaoId] = [
            'variacao_id'    => (int) $variacao['id'],
            'produto_id'     => (int) $variacao['produto_id'],
            'produto_nome'   => $variacao['produto_nome'],
            'tamanho'        => $variacao['tamanho'],
            'cor'            => $variacao['cor'],
            'preco_unitario' => (float) $variacao['preco'],
            'quantidade'     => $novaQtd,
            'imagem'         => imagemProduto((int) $variacao['produto_id']),
        ];
    }

    /**
     * Define a quantidade exata de um item (0 remove).
     * @throws RegraNegocioException se passar do estoque
     */
    public function alterar(int $variacaoId, int $quantidade): void
    {
        if (!isset($_SESSION['carrinho'][$variacaoId])) {
            return;
        }
        if ($quantidade <= 0) {
            $this->remover($variacaoId);
            return;
        }
        $variacao = $this->vitrine->buscarVariacao($variacaoId);
        if (!$variacao) {
            $this->remover($variacaoId);
            throw new RegraNegocioException('Um item do carrinho não está mais disponível e foi removido.');
        }
        $estoque = (int) $variacao['estoque'];
        if ($quantidade > $estoque) {
            $_SESSION['carrinho'][$variacaoId]['quantidade'] = max(1, $estoque);
            throw new RegraNegocioException("Só há {$estoque} unidade(s) disponível(is) dessa opção.");
        }
        $_SESSION['carrinho'][$variacaoId]['quantidade'] = $quantidade;
    }

    public function remover(int $variacaoId): void
    {
        unset($_SESSION['carrinho'][$variacaoId]);
    }

    /**
     * Confere cada item com o banco: atualiza preço, remove o que saiu de linha
     * ou esgotou e reduz quantidades acima do estoque.
     * @return string[] avisos para mostrar ao cliente
     */
    public function sincronizar(): array
    {
        $avisos = [];
        foreach ($_SESSION['carrinho'] as $variacaoId => $item) {
            $variacao = $this->vitrine->buscarVariacao((int) $variacaoId);
            $nome = $item['produto_nome'];

            if (!$variacao || (int) $variacao['estoque'] <= 0) {
                unset($_SESSION['carrinho'][$variacaoId]);
                $avisos[] = "“{$nome}” não está mais disponível e foi removido do carrinho.";
                continue;
            }
            $estoque = (int) $variacao['estoque'];
            if ($item['quantidade'] > $estoque) {
                $_SESSION['carrinho'][$variacaoId]['quantidade'] = $estoque;
                $avisos[] = "A quantidade de “{$nome}” foi ajustada para {$estoque} (estoque disponível).";
            }
            $precoAtual = (float) $variacao['preco'];
            if (abs($precoAtual - $item['preco_unitario']) > 0.001) {
                $_SESSION['carrinho'][$variacaoId]['preco_unitario'] = $precoAtual;
                $avisos[] = "O preço de “{$nome}” foi atualizado para " . moeda($precoAtual) . '.';
            }
        }
        return $avisos;
    }
}
