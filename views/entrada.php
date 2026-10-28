<?php
$nomeUser = $_SESSION['nome'] ?? 'Usuário';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Entradas de Mercadoria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- O truque do time() mantido para evitar o cache! -->
  <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
  <link rel="stylesheet" href="/loja_roupas/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<div class="container">
  <div class="topbar">
    <div class="brand">
      <div class="badge"></div>
      <div>
        <h1>Entradas de Mercadoria</h1>
        <small>Compra e reposição de estoque</small>
      </div>
    </div>
    <div class="pill">
      Olá, <strong><?= htmlspecialchars($nomeUser) ?></strong>
      • <a href="/loja_roupas/index.php?controller=auth&action=logout">Sair</a>
    </div>
  </div>
 
  <div class="card">
    <h2>Registrar Nova Entrada</h2>
 
    <form method="post" action="index.php?controller=entrada&action=salvar">
      <label>Fornecedor</label>
      <select class="input" name="fornecedor_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($fornecedores as $f): ?>
          <option value="<?= (int)$f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
        <?php endforeach; ?>
      </select>
 
      <h3 style="margin-top:18px;">Itens da entrada</h3>
      <p class="muted" style="font-size:13px;">
        Preencha uma linha para cada variação (SKU) recebida. Deixe quantidade "0" para
        ignorar uma linha.
      </p>
 
      <!-- Envolvendo a tabela de itens para responsividade -->
      <div style="overflow-x: auto;">
        <table class="table" id="tabela-itens">
          <thead>
            <tr>
              <th>Variação (SKU)</th>
              <th>Quantidade</th>
              <th>Custo Unitário (R$)</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($linha = 0; $linha < 5; $linha++): ?>
            <tr>
              <td>
                <select class="input" name="variacao_id[]">
                  <option value="">-- não usar esta linha --</option>
                  <?php foreach ($variacoes as $v): ?>
                    <option value="<?= (int)$v['id'] ?>">
                      <?= htmlspecialchars($v['produto_nome']) ?> —
                      <?= htmlspecialchars($v['sku']) ?>
                      (<?= htmlspecialchars($v['tamanho']) ?>/<?= htmlspecialchars($v['cor']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><input class="input" type="number" min="0" name="quantidade[]" value="0"></td>
              <td><input class="input" type="text" name="custo_unitario[]" value="0,00"></td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
 
      <div class="actions" style="margin-top:14px;">
        <button class="btn btn-primary" type="submit">Confirmar Entrada</button>
      </div>
    </form>
  </div>
 
  <br><br><br>
 
<div class="card">
    <h2>Últimas Entradas</h2>
    
    <div style="overflow-x: auto;">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Fornecedor</th>
            <th>Produto(s)</th>
            <th>Quantidade</th>
            <th>Data</th>
            <th>Status</th>
            <th>Valor Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($entradas as $e): ?>
          <tr>
            <td>#<?= (int)$e['id'] ?></td>
            <td><?= htmlspecialchars($e['fornecedor_nome'] ?? '') ?></td>
            <td><?= htmlspecialchars($e['produtos_nomes'] ?? 'Sem itens') ?></td>
            <td><?= (int)($e['total_quantidade'] ?? 0) ?></td>
            <td><?= htmlspecialchars($e['data'] ?? '') ?></td>
            <td><?= htmlspecialchars($e['status'] ?? '') ?></td>
            <td>R$ <?= number_format((float)($e['valor_total'] ?? 0), 2, ',', '.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <div style="margin-top:14px;">
      <a class="btn" href="/loja_roupas/index.php?controller=auth&action=dashboard">Voltar ao Dashboard</a>
    </div>
  </div>
</div>
</body>
</html>