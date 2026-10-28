<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?> — Loja de Cosplay</title>
 <link rel="icon" type="image/png" href="public/assets/img/site.png">
  <link rel="stylesheet" href="public/assets/css/loja.css">
</head>
<body>
<a class="pular" href="#conteudo">Ir para o conteúdo</a>

<header class="topo">
  <div class="topo__interno">
    <a class="marca" href="<?= e(url('loja', 'index')) ?>" aria-label="Loja de Cosplay — página inicial">
      <svg class="marca__icone" viewBox="0 0 32 32" width="30" height="30" aria-hidden="true">
        <path d="M4 9c0-2 2-3 4-3h16c2 0 4 1 4 3v7c0 6-5 10-12 10S4 22 4 16V9z" fill="#ff3d81"/>
        <ellipse cx="11" cy="14" rx="3" ry="2.2" fill="#35206b"/>
        <ellipse cx="21" cy="14" rx="3" ry="2.2" fill="#35206b"/>
        <path d="M12 20c2.5 2 5.5 2 8 0" stroke="#35206b" stroke-width="1.8" fill="none" stroke-linecap="round"/>
      </svg>
      <span>Loja de Cosplay</span>
    </a>

    <form class="busca" action="index.php" method="get" role="search">
      <input type="hidden" name="controller" value="loja">
      <input type="hidden" name="action" value="index">
      <?php if ($categoriaAtual): ?>
        <input type="hidden" name="categoria_id" value="<?= (int) $categoriaAtual ?>">
      <?php endif; ?>
      <label class="sr-only" for="busca-q">Buscar produtos</label>
      <input id="busca-q" type="search" name="q" value="<?= e($buscaAtual) ?>" placeholder="Buscar fantasias, perucas, acessórios…" maxlength="100">
      <button type="submit">Buscar</button>
    </form>

    <nav class="conta" aria-label="Minha conta">
      <?php if ($clienteNome): ?>
        <a href="<?= e(url('cliente', 'meusDados')) ?>">Olá, <?= e(explode(' ', trim($clienteNome))[0]) ?></a>
        <a href="<?= e(url('pedido', 'meusPedidos')) ?>">Meus pedidos</a>
        <form class="conta__sair" action="<?= e(url('cliente', 'logout')) ?>" method="post">
          <?= csrfCampo() ?>
          <button type="submit" class="link">Sair</button>
        </form>
      <?php else: ?>
        <a href="<?= e(url('cliente', 'login')) ?>">Entrar</a>
        <a href="<?= e(url('cliente', 'cadastro')) ?>">Criar conta</a>
      <?php endif; ?>
      <a class="carrinho-link" href="<?= e(url('carrinho', 'index')) ?>" aria-label="Carrinho, <?= (int) $qtdCarrinho ?> item(ns)">
        <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L20 8H6.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.5" cy="19" r="1.4" fill="currentColor"/><circle cx="17" cy="19" r="1.4" fill="currentColor"/></svg>
        <?php if ($qtdCarrinho > 0): ?><span class="carrinho-link__qtd" data-carrinho-qtd><?= (int) $qtdCarrinho ?></span><?php endif; ?>
      </a>
    </nav>
  </div>

  <nav class="categorias" aria-label="Categorias">
    <div class="categorias__interno">
      <a href="<?= e(url('loja', 'index')) ?>" class="<?= !$categoriaAtual ? 'ativa' : '' ?>">Todas</a>
      <?php foreach ($categorias as $cat): ?>
        <a href="<?= e(url('loja', 'index', ['categoria_id' => $cat['id']])) ?>"
           class="<?= (int) $cat['id'] === (int) $categoriaAtual ? 'ativa' : '' ?>"
           <?= (int) $cat['id'] === (int) $categoriaAtual ? 'aria-current="page"' : '' ?>><?= e($cat['nome']) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>

<main id="conteudo" class="pagina">
  <?php foreach ($flashes as $f): ?>
    <div class="aviso aviso--<?= e($f['tipo']) ?>" role="<?= $f['tipo'] === 'erro' ? 'alert' : 'status' ?>"><?= e($f['msg']) ?></div>
  <?php endforeach; ?>
