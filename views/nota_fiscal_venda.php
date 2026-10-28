<?php
// Trata e formata a data e hora exatas registradas na venda
$timestampVenda = !empty($venda['data']) ? strtotime($venda['data']) : time();
$dataVendaFormatada = date('d/m/Y', $timestampVenda);
$horaVendaFormatada = date('H:i:s', $timestampVenda);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Nota Fiscal de Venda - Nº <?= htmlspecialchars($nota['numero'] ?? $venda['id']) ?></title>
    <link rel="stylesheet" href="/loja_roupas/public/assets/css/nota.css">
    <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
    <link rel="stylesheet" href="nota_fiscal.css">
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
                    <p class="nf-dado-linha"><strong>NÚMERO:</strong> <?= htmlspecialchars($nota['numero'] ?? str_pad($venda['id'], 6, '0', STR_PAD_LEFT)) ?></p>
                    <p class="nf-dado-linha"><strong>SÉRIE:</strong> <?= htmlspecialchars($nota['serie'] ?? '1') ?></p>
                    <p class="nf-dado-linha"><strong>MODELO:</strong> <?= htmlspecialchars($nota['modelo'] ?? '55') ?></p>
                </div>
            </div>
        </header>

        <!-- DADOS DE EMISSÃO (DATA E HORA EXATAS DA VENDA) -->
        <section class="nf-emissao">
            <div class="nf-emissao-bloco">
                <span class="nf-emissao-label">DATA DA VENDA</span>
                <span class="nf-emissao-valor"><?= $dataVendaFormatada ?></span>
            </div>
            <div class="nf-emissao-bloco">
                <span class="nf-emissao-label">HORA DA VENDA</span>
                <span class="nf-emissao-valor"><?= $horaVendaFormatada ?></span>
            </div>
            <div class="nf-emissao-bloco">
                <span class="nf-emissao-label">REFERÊNCIA VENDA</span>
                <span class="nf-emissao-valor">#<?= (int) $venda['id'] ?></span>
            </div>
        </section>

        <!-- CORPO / PRODUTOS VENDIDOS -->
        <main class="nf-conteudo">
            <table class="nf-tabela-produtos">
                <thead>
                    <tr>
                        <th class="nf-th-codigo">CÓDIGO / SKU</th>
                        <th class="nf-th-descricao">DESCRIÇÃO DO PRODUTO VENDIDO</th>
                        <th class="nf-th-qtd">QTD</th>
                        <th class="nf-th-valor-uni">V. UNITÁRIO</th>
                        <th class="nf-th-valor-tot">V. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($venda['itens']) && is_array($venda['itens'])): ?>
                        <?php foreach ($venda['itens'] as $item): ?>
                            <?php 
                                $quantidade = (int)($item['quantidade'] ?? 1);
                                $precoUnitario = (float)($item['preco_unitario'] ?? $item['valor_unitario'] ?? 0);
                                $subtotal = (float)($item['valor_total'] ?? ($quantidade * $precoUnitario));
                                
                                // Monta o nome do produto com variação (tamanho/cor) se disponível
                                $nomeProduto = $item['produto_nome'] ?? $item['nome'] ?? $item['descricao'] ?? 'Produto sem Descrição';
                                $variacoesInfo = [];
                                if (!empty($item['tamanho'])) $variacoesInfo[] = "Tam: " . $item['tamanho'];
                                if (!empty($item['cor'])) $variacoesInfo[] = "Cor: " . $item['cor'];
                                $detalhes = !empty($variacoesInfo) ? " (" . implode(' / ', $variacoesInfo) . ")" : "";
                            ?>
                            <tr>
                                <td class="nf-td-codigo"><?= htmlspecialchars($item['sku'] ?? $item['variacao_id'] ?? '#' . $item['id']) ?></td>
                                <td class="nf-td-descricao"><?= htmlspecialchars($nomeProduto . $detalhes) ?></td>
                                <td class="nf-td-qtd"><?= $quantidade ?></td>
                                <td class="nf-td-valor-uni">R$ <?= number_format($precoUnitario, 2, ',', '.') ?></td>
                                <td class="nf-td-valor-tot">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback para venda simples de item único -->
                        <?php 
                            $quantidade = (int)($venda['quantidade'] ?? 1);
                            $valorTotal = (float)($venda['valor_total'] ?? $nota['valor_total'] ?? 0);
                            $precoUnitario = (float)($venda['preco_unitario'] ?? ($quantidade > 0 ? $valorTotal / $quantidade : $valorTotal));
                        ?>
                        <tr>
                            <td class="nf-td-codigo">#<?= (int)$venda['id'] ?></td>
                            <td class="nf-td-descricao"><?= htmlspecialchars($venda['produto_nome'] ?? 'Produto Geral / Ref. Venda #' . $venda['id']) ?></td>
                            <td class="nf-td-qtd"><?= $quantidade ?></td>
                            <td class="nf-td-valor-uni">R$ <?= number_format($precoUnitario, 2, ',', '.') ?></td>
                            <td class="nf-td-valor-tot">R$ <?= number_format($valorTotal, 2, ',', '.') ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>

        <!-- TOTAIS DA NOTA -->
        <footer class="nf-rodape">
            <div class="nf-total-bloco">
                <span class="nf-total-label">VALOR TOTAL DA NOTA</span>
                <span class="nf-total-valor">R$ <?= number_format((float)($nota['valor_total'] ?? $venda['valor_total'] ?? 0), 2, ',', '.') ?></span>
            </div>
        </footer>

        <!-- BOTÕES DE AÇÃO -->
        <div class="nf-botoes no-print">
            <button class="nf-btn-imprimir" onclick="window.print()">Imprimir Nota Fiscal</button>
        </div>

    </div>

</body>

</html>