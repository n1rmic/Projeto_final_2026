<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Variações (SKU) - <?= htmlspecialchars($produto['nome']) ?></title>
  <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>

<body>
    <div class="header">
        <div class="container header-inner">
            <div>
                <strong>Loja Cosplay</strong>
                <span class="badge">Variações (SKU)</span>
            </div>
            <div class="user">
                Olá, <strong><?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?></strong>
                <a class="btn" href="index.php?controller=produto&action=index">Voltar aos Produtos</a>
                <a class="btn btn-ghost" href="index.php?controller=auth&action=logout">Sair</a>
            </div>
        </div>
    </div>

    <div class="container grid">
        <div class="card">
            <h2><?= $editar ? "Editar Variação #" . (int)$editar['id'] : "Cadastrar SKU / Variação" ?></h2>
            <form method="post" action="index.php?controller=variacao&action=salvar">
                <input type="hidden" name="id" value="<?= $editar ? (int)$editar['id'] : 0 ?>">
                <input type="hidden" name="produto_id" value="<?= (int)$produto['id'] ?>">

                <div class="form-group">
                    <label>Produto Pai</label>
                    <input class="input" type="text" value="<?= htmlspecialchars($produto['nome']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Código SKU (Único)</label>
                    <input class="input" type="text" name="sku" placeholder="Ex: COS-NARUTO-M-PR" required
                        value="<?= $editar ? htmlspecialchars($editar['sku']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Tamanho (opcional)</label>
                    <input class="input" type="text" name="tamanho" placeholder="Ex: P, M, G, Único"
                        value="<?= $editar ? htmlspecialchars($editar['tamanho'] ?? '') : '' ?>">
                </div>

                <div class="form-group">
                    <label>Cor (opcional)</label>
                    <input class="input" type="text" name="cor" placeholder="Ex: Preto, Vermelho"
                        value="<?= $editar ? htmlspecialchars($editar['cor'] ?? '') : '' ?>">
                </div>

                <div class="form-group">
                    <label>Preço de Venda (R$)</label>
                    <input class="input" type="text" name="preco" required placeholder="0,00"
                        value="<?= $editar ? number_format($editar['preco'], 2, ',', '.') : '' ?>">
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Salvar Variação</button>
                    <a class="btn" href="index.php?controller=variacao&action=index&produto_id=<?= (int)$produto['id'] ?>">Limpar</a>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>SKUs de "<?= htmlspecialchars($produto['nome']) ?>"</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Tamanho</th>
                        <th>Cor</th>
                        <th>Preço</th>
                        <th style="width:180px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($variacoes)): ?>
                        <tr>
                            <td colspan="6" class="muted">Nenhum SKU cadastrado para este produto.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($variacoes as $v): ?>
                            <tr>
                                <td>#<?= (int)$v['id'] ?></td>
                                <td><strong><?= htmlspecialchars($v['sku']) ?></strong></td>
                                <td><?= htmlspecialchars($v['tamanho'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($v['cor'] ?? '-') ?></td>
                                <td>R$ <?= number_format($v['preco'], 2, ',', '.') ?></td>
                                <td>
                                    <a class="btn" href="index.php?controller=variacao&action=index&produto_id=<?= (int)$produto['id'] ?>&id=<?= (int)$v['id'] ?>">Editar</a>
                                    <a class="btn btn-danger" href="index.php?controller=variacao&action=deletar&produto_id=<?= (int)$produto['id'] ?>&id=<?= (int)$v['id'] ?>" onclick="return confirm('Excluir este SKU?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>