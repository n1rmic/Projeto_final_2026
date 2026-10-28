<?php
require_once __DIR__ . '/SiteBaseController.php';
require_once __DIR__ . '/../models/ClienteSite.php';
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController extends SiteBaseController
{
    /** index.php?controller=pedido&action=checkout  (exige login) */
    public function checkout(): void
    {
        $this->exigirLogin();

        $carrinho = new Carrinho();
        foreach ($carrinho->sincronizar() as $aviso) {
            flash('aviso', $aviso);
        }
        if ($carrinho->vazio()) {
            flash('aviso', 'Seu carrinho está vazio.');
            redirecionar(url('carrinho', 'index'));
        }

        $cliente = (new ClienteSite())->buscarPorCpf($this->clienteCpf());

        $this->render('checkout', [
            'carrinho' => $carrinho,
            'cliente'  => $cliente,
            'old'      => [],
            'erro'     => '',
        ], 'Finalizar compra');
    }

    /** POST: recebe o endereço, valida e mostra a escolha de pagamento */
    public function pagamento(): void
    {
        $this->exigirLogin();
        $this->exigirPost(url('pedido', 'checkout'));

        $campos = ['cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];
        $old = ['endereco_opcao' => $_POST['endereco_opcao'] ?? 'cadastrado'];
        foreach ($campos as $c) {
            $old[$c] = trim((string) ($_POST[$c] ?? ''));
        }

        $carrinho = new Carrinho();
        $avisos = $carrinho->sincronizar();
        if ($avisos) {
            foreach ($avisos as $a) {
                flash('aviso', $a);
            }
        }
        if ($carrinho->vazio()) {
            redirecionar(url('carrinho', 'index'));
        }

        $cliente = (new ClienteSite())->buscarPorCpf($this->clienteCpf());

        if ($old['endereco_opcao'] === 'cadastrado' && !empty($cliente['endereco'])) {
            $endereco = $cliente['endereco'];
        } else {
            $obrigatorios = ['cep', 'rua', 'numero', 'bairro', 'cidade', 'uf'];
            $faltando = array_filter($obrigatorios, fn($c) => $old[$c] === '');
            if ($faltando || strlen(somenteDigitos($old['cep'])) !== 8) {
                $this->render('checkout', [
                    'carrinho' => $carrinho,
                    'cliente'  => $cliente,
                    'old'      => $old,
                    'erro'     => 'Preencha todos os campos obrigatórios do endereço (CEP com 8 dígitos).',
                ], 'Finalizar compra');
                return;
            }

            $endereco = "{$old['rua']}, {$old['numero']}"
                . ($old['complemento'] !== '' ? " - {$old['complemento']}" : '')
                . " - {$old['bairro']}, {$old['cidade']}/{$old['uf']} - CEP " . formatarCep($old['cep']);
        }

        $_SESSION['checkout_endereco'] = $endereco;

        $this->render('pagamento', [
            'carrinho' => $carrinho,
            'erro'     => '',
        ], 'Forma de pagamento');
    }

    /** POST: confirma a forma de pagamento e cria o pedido */
    public function finalizar(): void
    {
        $this->exigirLogin();
        $this->exigirPost(url('pedido', 'checkout'));

        $endereco = $_SESSION['checkout_endereco'] ?? '';
        $carrinho = new Carrinho();
        if ($endereco === '' || $carrinho->vazio()) {
            redirecionar(url('pedido', 'checkout'));
        }

        $metodo = $_POST['metodo'] ?? '';
        if ($metodo === 'cartao') {
            $obrigatorios = ['numero_cartao', 'nome_cartao', 'validade_cartao', 'cvv_cartao'];
            foreach ($obrigatorios as $c) {
                if (trim((string) ($_POST[$c] ?? '')) === '') {
                    $this->render('pagamento', [
                        'carrinho' => $carrinho,
                        'erro'     => 'Preencha todos os dados do cartão.',
                    ], 'Forma de pagamento');
                    return;
                }
            }
        } elseif ($metodo !== 'pix') {
            redirecionar(url('pedido', 'checkout'));
        }

        try {
            $pedidoId = (new Pedido())->criar($this->clienteCpf(), $endereco, $carrinho->itens());
        } catch (RegraNegocioException $ex) {
            flash('erro', $ex->getMessage());
            redirecionar(url('carrinho', 'index'));
        } catch (Throwable $ex) {
            error_log('[loja] falha ao criar pedido: ' . $ex->getMessage());
            flash('erro', 'Não foi possível concluir o pedido agora. Seu carrinho foi mantido; tente novamente.');
            redirecionar(url('carrinho', 'index'));
        }

        $carrinho->limpar();
        unset($_SESSION['checkout_endereco']);
        redirecionar(url('pedido', 'confirmacao', ['id' => $pedidoId]));
    }

    /** index.php?controller=pedido&action=confirmacao&id=N */
    public function confirmacao(): void
    {
        $this->exigirLogin();
        $pedido = (new Pedido())->buscarDoCliente((int) ($_GET['id'] ?? 0), $this->clienteCpf());
        if (!$pedido) {
            http_response_code(404);
            $this->render('nao_encontrado', ['mensagem' => 'Pedido não encontrado.'], 'Pedido não encontrado');
            return;
        }
        $this->render('confirmacao', ['pedido' => $pedido], 'Pedido realizado');
    }

    /** index.php?controller=pedido&action=verNota&id=N  (mesma tela da NF-e, com botão Imprimir) */
    public function verNota(): void
    {
        $this->exigirLogin();

        $pedido = (new Pedido())->buscarDoCliente((int) ($_GET['id'] ?? 0), $this->clienteCpf());
        if (!$pedido) {
            http_response_code(404);
            $this->render('nao_encontrado', ['mensagem' => 'Pedido não encontrado.'], 'Pedido não encontrado');
            return;
        }

        $cliente = (new ClienteSite())->buscarPorCpf($this->clienteCpf());

        require __DIR__ . '/../views/nota_fiscal_pedido.php';
    }

    /** index.php?controller=pedido&action=meusPedidos */
    public function meusPedidos(): void
    {
        $this->exigirLogin();
        $pedidos = (new Pedido())->listarDoCliente($this->clienteCpf());
        $this->render('meus_pedidos', ['pedidos' => $pedidos], 'Meus pedidos');
    }
}