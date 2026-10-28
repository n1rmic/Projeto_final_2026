<?php
$nomeUser = $_SESSION['nome'] ?? 'Usuário';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Relatórios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Cache busting mantido -->
  <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
  <link rel="stylesheet" href="/loja_roupas/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
  <div class="topbar">
    <div class="brand">
      <div class="badge"></div>
      <div>
        <h1>Relatórios</h1>
        <small>Indicadores da loja</small>
      </div>
    </div>
    <div class="pill">
      Olá, <strong><?= htmlspecialchars($nomeUser) ?></strong>
      • <a href="/loja_roupas/index.php?controller=auth&action=logout">Sair</a>
    </div>
  </div>
 <div class="actions" style="margin: 14px 0;">
  <a class="btn" href="index.php?controller=relatorio&action=exportarExcel">
    📊 Exportar para Excel
  </a>
  <a class="btn" href="index.php?controller=relatorio&action=exportarPdf">
    📄 Exportar para PDF
  </a>
</div>

  <!-- Grid principal -->
  <div class="grid" style="grid-template-columns: 1fr 1fr;">
    <div class="card">
      <h2>Produtos mais vendidos</h2>
      <div style="overflow-x: auto;">
        <table class="table">
          <thead><tr><th>Produto</th><th>Qtde. vendida</th></tr></thead>
          <tbody>
            <?php foreach ($topProdutos as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= (int)$p['total_vendido'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($topProdutos)): ?>
            <tr><td colspan="2" class="muted">Nenhuma venda registrada ainda.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
 
    <div class="card">
      <h2>Fornecedores</h2>
      <div style="overflow-x: auto;">
        <table class="table">
          <thead><tr><th>Fornecedor</th><th>Entradas</th><th>Valor total</th></tr></thead>
          <tbody>
            <?php foreach ($fornecedores as $f): ?>
            <tr>
              <td><?= htmlspecialchars($f['nome']) ?></td>
              <td><?= (int)$f['total_entradas'] ?></td>
              <td>R$ <?= number_format((float)$f['valor_total'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
 
  <!-- Grid secundário -->
  <div class="grid" style="grid-template-columns: 1fr 1fr; margin-top:16px;">
    <div class="card">
      <h2>Entradas recentes</h2>
      <div style="overflow-x: auto;">
        <table class="table">
          <thead><tr><th>ID</th><th>Fornecedor</th><th>Data</th><th>Valor</th></tr></thead>
          <tbody>
            <?php foreach ($entradas as $e): ?>
            <tr>
              <td>#<?= (int)$e['id'] ?></td>
              <td><?= htmlspecialchars($e['fornecedor_nome']) ?></td>
              <td><?= htmlspecialchars($e['data']) ?></td>
              <td>R$ <?= number_format((float)$e['valor_total'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
 
    <div class="card">
      <h2>Estoque baixo (⚠ atenção)</h2>
      <div style="overflow-x: auto;">
        <table class="table">
          <thead><tr><th>Produto</th><th>SKU</th><th>Qtde.</th><th>Mínimo</th></tr></thead>
          <tbody>
            <?php foreach ($baixoEstoque as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['produto_nome']) ?></td>
              <td><?= htmlspecialchars($b['sku']) ?></td>
              <td><?= (int)$b['quantidade'] ?></td>
              <td><?= (int)$b['minimo'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($baixoEstoque)): ?>
            <tr><td colspan="4" class="muted">Nenhum item abaixo do estoque mínimo. 🎉</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
 
  <div style="margin-top:14px;">
    <a class="btn" href="/loja_roupas/index.php?controller=auth&action=dashboard">Voltar ao Dashboard</a>
  </div>
</div>
</body>
</html>