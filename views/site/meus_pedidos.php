<h1 class="titulo-pagina">Meus pedidos</h1>

<?php if (!$pedidos): ?>
  <div class="vazio">
    <p>Você ainda não fez nenhum pedido.</p>
    <a class="botao botao--primario" href="<?= e(url('loja', 'index')) ?>">Ver produtos</a>
  </div>
<?php else: ?>
  <ul class="pedidos">
    <?php foreach ($pedidos as $p): ?>
      <li class="pedido">
        <header class="pedido__topo">
          <div>
            <h2>Pedido #<?= (int) $p['id'] ?></h2>
            <p><?= e(date('d/m/Y H:i', strtotime($p['data']))) ?></p>
          </div>
          <span class="status status--<?= e($p['status']) ?>"><?= e($p['status']) ?></span>
        </header>

        <ul class="lista-itens">
          <?php foreach ($p['itens'] as $i): ?>
            <li>
              <span><?= (int) $i['quantidade'] ?> × <a href="<?= e(url('loja', 'produto', ['id' => $i['produto_id']])) ?>"><?= e($i['produto_nome']) ?></a>
                <small>(<?= e($i['tamanho']) ?>, <?= e($i['cor']) ?>)</small></span>
              <span><?= e(moeda($i['preco_unitario'] * $i['quantidade'])) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <footer class="pedido__rodape">
          <p>Entrega em: <?= e($p['endereco_entrega']) ?></p>
          <p class="pedido__total">Total <?= e(moeda($p['valor_total'])) ?></p>
        </footer>
        <a class="link" href="<?= e(url('pedido', 'verNota', ['id' => $p['id']])) ?>">Ver Nota Fiscal</a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>