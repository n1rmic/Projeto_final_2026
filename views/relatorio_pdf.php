<?php
// Este arquivo NÃO é acessado diretamente pelo navegador — ele só gera o HTML
// que a Dompdf converte em PDF.
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 13px; color: #222; }
    h1 { font-size: 18px; border-bottom: 2px solid #333; padding-bottom: 6px; }
    h2 { font-size: 15px; margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #999; padding: 5px 8px; font-size: 12px; }
    th { background: #eee; }
  </style>
  <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
</head>
<body>
  <h1>Relatório Gerencial — Loja Cosplay</h1>
  <p>Gerado em: <?= date('d/m/Y H:i') ?></p>
 
  <h2>Produtos mais vendidos</h2>
  <table>
    <thead><tr><th>Produto</th><th>Qtde. vendida</th></tr></thead>
    <tbody>
      <?php foreach ($topProdutos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nome']) ?></td>
        <td><?= (int)$p['total_vendido'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
 
  <h2>Fornecedores</h2>
  <table>
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
</body>
</html>
