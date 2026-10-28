<?php
$titulo_lista = $filtros['busca'] !== ''
    ? 'Resultados para “' . $filtros['busca'] . '”'
    : ($categoriaNome ?? 'Todos os produtos');

// mantém categoria e busca ao trocar de página
$paramsPagina = array_filter([
    'categoria_id' => $filtros['categoria_id'] ?: null,
    'q'            => $filtros['busca'] !== '' ? $filtros['busca'] : null,
]);
?>
<section class="lista-cabecalho">
  <h1><?= e($titulo_lista) ?></h1>
  <p class="lista-cabecalho__total"><?= (int) $total ?> <?= $total === 1 ? 'produto' : 'produtos' ?></p>
</section>

<?php if (!$produtos): ?>
  <div class="vazio">
    <p>Nenhum produto encontrado.</p>
    <p>Tente outro termo de busca ou volte para a vitrine completa.</p>
    <a class="botao botao--secundario" href="<?= e(url('loja', 'index')) ?>">Ver todos os produtos</a>
  </div>
<?php else: ?>
  <ul class="grade">
    <?php foreach ($produtos as $p): $esgotado = (int) $p['estoque_total'] <= 0; ?>
      <li class="card">
        <a class="card__link" href="<?= e(url('loja', 'produto', ['id' => $p['id']])) ?>">
          <div class="card__foto">
            <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" loading="lazy" width="400" height="400">
            <?php if ($esgotado): ?><span class="selo">Esgotado</span><?php endif; ?>
          </div>
          <div class="card__corpo">
            <span class="card__categoria"><?= e($p['categoria_nome']) ?></span>
            <h2 class="card__nome"><?= e($p['nome']) ?></h2>
            <p class="card__preco"><small>a partir de</small> <?= e(moeda($p['preco_min'])) ?></p>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($paginas > 1): ?>
    <nav class="paginacao" aria-label="Páginas de produtos">
      <?php if ($pagina > 1): ?>
        <a href="<?= e(url('loja', 'index', $paramsPagina + ['pagina' => $pagina - 1])) ?>">Anterior</a>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $paginas; $i++): ?>
        <a href="<?= e(url('loja', 'index', $paramsPagina + ['pagina' => $i])) ?>"
           class="<?= $i === $pagina ? 'ativa' : '' ?>" <?= $i === $pagina ? 'aria-current="page"' : '' ?>><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($pagina < $paginas): ?>
        <a href="<?= e(url('loja', 'index', $paramsPagina + ['pagina' => $pagina + 1])) ?>">Próxima</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>
