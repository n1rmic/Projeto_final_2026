<?php
// Trata e formata a data e hora exatas registradas no pedido
$timestampPedido = !empty($pedido['data']) ? strtotime($pedido['data']) : time();
$dataPedidoFormatada = date('d/m/Y', $timestampPedido);
$horaPedidoFormatada = date('H:i:s', $timestampPedido);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Nota Fiscal do Pedido - Nº <?= htmlspecialchars(str_pad((string) $pedido['id'], 6, '0', STR_PAD_LEFT)) ?></title>
   <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
    <link rel="stylesheet" href="/loja_roupas/public/assets/css/nota.css">
</head>

<body>

    <div class="nf-container">

        <!-- CABEÇALHO DA NOTA -->
        <header class="nf-cabecalho">
            <div class="nf-emitente">
                <h1 class="nf-emitente-nome">LOJA COSPLAY LTDA</h1>
                <p class="nf-emitente-documento">CNPJ: 00.000.000/0001-00</p>
                <p class="nf-emitente-endereco">Rua Principal, 123 - Centro - Cidade/UF</p>
            </div>

            <div class="nf-documento-info">
                <h2 class="nf-documento-titulo">NOTA FISCAL</h2>
                <div class="nf-documento-dados">
                    <p class="nf-dado-linha"><strong>NÚMERO:</strong> <?= str_pad((string) $pedido['id'], 6, '0', STR_PAD_LEFT) ?></p>
                    <p class="nf-dado-linha"><strong>SÉRIE:</strong> 1</p>
                    <p class="nf-dado-linha"><strong>MODELO:</strong> 55</p>
                </div>
            </div>
        </header>

        <!-- DADOS DE EMISSÃO (DATA E HORA EXATAS DO PEDIDO) -->
        <section class="nf-emissao">
            <div class="nf-emissao-bloco">
                <span class="nf-emissao-label">DATA DO PEDIDO</span>
                <span class="nf-emissao-valor"><?= $dataPedidoFormatada ?></span>
            </div>
            <div class="nf-emissao-bloco">
                <span class="nf-emissao-label">HORA DO PEDIDO</span>
                <span class="nf-emissao-valor"><?= $horaPedidoFormatada ?></span>
            </div>
            <div class="nf-emissao-bloco">
                <span class="nf-emissao-label">REFERÊNCIA PEDIDO</span>
                <span class="nf-emissao-valor">#<?= (int) $pedido['id'] ?></span>
            </div>
        </section>

        <!-- DESTINATÁRIO (CLIENTE E ENDEREÇO DE ENTREGA) -->
        <section class="nf-emissao">
            <div class="nf-emissao-bloco" style="width:100%">
                <span class="nf-emissao-label">DESTINATÁRIO</span>
                <span class="nf-emissao-valor"><?= htmlspecialchars($cliente['nome'] ?? '') ?> — CPF <?= htmlspecialchars(formatarCpf($cliente['cpf'] ?? '')) ?></span>
                <p class="nf-emitente-endereco">Entrega em: <?= htmlspecialchars($pedido['endereco_entrega']) ?></p>
            </div>
        </section>

        <!-- CORPO / PRODUTOS DO PEDIDO -->
        <main class="nf-conteudo">
            <table class="nf-tabela-produtos">
                <thead>
                    <tr>
                        <th class="nf-th-codigo">CÓDIGO</th>
                        <th class="nf-th-descricao">DESCRIÇÃO DO PRODUTO</th>
                        <th class="nf-th-qtd">QTD</th>
                        <th class="nf-th-valor-uni">V. UNITÁRIO</th>
                        <th class="nf-th-valor-tot">V. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedido['itens'] as $item): ?>
                        <?php
                            $quantidade = (int) $item['quantidade'];
                            $precoUnitario = (float) $item['preco_unitario'];
                            $detalhes = trim(($item['tamanho'] ?? '') . '/' . ($item['cor'] ?? ''), '/');
                        ?>
                        <tr>
                            <td class="nf-td-codigo">#<?= (int) $item['produto_id'] ?></td>
                            <td class="nf-td-descricao"><?= htmlspecialchars($item['produto_nome']) ?><?= $detalhes !== '' ? ' (' . htmlspecialchars($detalhes) . ')' : '' ?></td>
                            <td class="nf-td-qtd"><?= $quantidade ?></td>
                            <td class="nf-td-valor-uni">R$ <?= number_format($precoUnitario, 2, ',', '.') ?></td>
                            <td class="nf-td-valor-tot">R$ <?= number_format($precoUnitario * $quantidade, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>

        <!-- TOTAIS DA NOTA -->
        <footer class="nf-rodape">
            <div class="nf-total-bloco">
                <span class="nf-total-label">VALOR TOTAL DA NOTA</span>
                <span class="nf-total-valor">R$ <?= number_format((float) $pedido['valor_total'], 2, ',', '.') ?></span>
            </div>
        </footer>

        <!-- BOTÕES DE AÇÃO -->
        <div class="nf-botoes no-print">
            <button class="nf-btn-imprimir" onclick="window.print()">Imprimir Nota Fiscal</button>
        </div>

    </div>

</body>
</html>