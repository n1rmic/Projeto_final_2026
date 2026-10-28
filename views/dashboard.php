<?php
$nome = $_SESSION['nome'] ?? 'Usuário';
$perfil = $_SESSION['perfil'] ?? 'vendedor';
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Dashboard - Loja Cosplay</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
<link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>


<div class="container">
<div class="topbar">
<div class="brand">
<div class="badge"></div>
<div>
<h1>Dashboard</h1>
<small>Painel do sistema</small>
</div>
</div>
<div class="pill">
Logado como <strong><?php echo htmlspecialchars($nome); ?></strong>
(<?php echo htmlspecialchars($perfil); ?>)
• <a href="/loja_roupas/index.php?controller=auth&action=logout">Sair</a>
</div>
</div>
<div class="card">
<h2 style="margin-top:0;">Bem-vindo(a), <?php echo htmlspecialchars($nome); ?>!</h2>
<p style="color:var(--muted); margin-top:6px;">
Escolha um módulo para continuar.
</p>

<div class="nav">
  <a href="/loja_roupas/index.php?controller=produto&action=index">Produtos</a>
 
  <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
    <a href="/loja_roupas/index.php?controller=categoria&action=index">Categorias</a>
    <a href="/loja_roupas/index.php?controller=fornecedor&action=index">Fornecedores</a>
  <?php endif; ?>
 
  <a href="/loja_roupas/index.php?controller=entrada&action=index">Entradas</a>
  <a href="/loja_roupas/index.php?controller=venda&action=index">Vendas</a>
  <a href="/loja_roupas/index.php?controller=relatorio&action=index">Relatórios</a>
  <a href="/loja_roupas/index.php?controller=loja&action=index" target="_blank" rel="noopener">🛒 Visitar o site</a>
</div>







<div class="kpis">

<div class="kpi">
  <div class="label">Vendas (mês)</div>
  <div class="value"><?= (int) $kpiVendasMes ?></div>
</div>
<div class="kpi">
  <div class="label">Entradas (mês)</div>
  <div class="value"><?= (int) $kpiEntradasMes ?></div>
</div>
<div class="kpi">
  <div class="label">Estoque baixo</div>
  <div class="value"><?= (int) $kpiEstoqueBaixo ?></div>
</div>
<div class="kpi">
  <div class="label">Produtos</div>
  <div class="value"><?= (int) $kpiTotalProdutos ?></div>
</div>

<div class="kpi">
  <div class="label">Estoque</div>
  <div class="value"><?= (int) $kpiTotalEstoque ?></div>
</div>










</div>
</div>
<p style="margin-top:16px; color:var(--muted); font-size:13px;">
Observação: esses números serão alimentados quando implementarmos os módulos de
entradas, vendas e estoque.
</p>
</div>
</div>
</body>
</html>
