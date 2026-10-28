<h1 class="titulo-pagina">Meu carrinho</h1>

<?php if ($carrinho->vazio()): ?>
  <div class="vazio">
    <p>Seu carrinho está vazio.</p>
    <p>Escolha um produto na vitrine e ele aparece aqui.</p>
    <a class="botao botao--primario" href="<?= e(url('loja', 'index')) ?>">Ver produtos</a>
  </div>
<?php else: ?>
  <div class="carrinho">
    <ul class="carrinho__itens">
      <?php foreach ($carrinho->itens() as $item): ?>
        <li class="item">
          <a class="item__foto" href="<?= e(url('loja', 'produto', ['id' => $item['produto_id']])) ?>">
            <img src="<?= e($item['imagem']) ?>" alt="" width="96" height="96" loading="lazy">
          </a>

          <div class="item__dados">
            <a class="item__nome" href="<?= e(url('loja', 'produto', ['id' => $item['produto_id']])) ?>"><?= e($item['produto_nome']) ?></a>
            <p class="item__variacao">Tamanho <?= e($item['tamanho']) ?>, cor <?= e($item['cor']) ?></p>
            <p class="item__unitario"><?= e(moeda($item['preco_unitario'])) ?> cada</p>

            <div class="item__acoes">
              <form action="<?= e(url('carrinho', 'atualizar')) ?>" method="post" class="item__qtd">
                <?= csrfCampo() ?>
                <input type="hidden" name="variacao_id" value="<?= (int) $item['variacao_id'] ?>">
                <label for="qtd-<?= (int) $item['variacao_id'] ?>">Qtd.</label>
                <input id="qtd-<?= (int) $item['variacao_id'] ?>" type="number" name="quantidade"
                       value="<?= (int) $item['quantidade'] ?>" min="1" inputmode="numeric" data-autoenviar>
                <button type="submit" class="link">Atualizar</button>
              </form>
              <form action="<?= e(url('carrinho', 'remover')) ?>" method="post">
                <?= csrfCampo() ?>
                <input type="hidden" name="variacao_id" value="<?= (int) $item['variacao_id'] ?>">
                <button type="submit" class="link link--perigo">Remover</button>
              </form>
            </div>
          </div>

          <p class="item__subtotal"><?= e(moeda($carrinho->subtotal($item))) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>

    <aside class="resumo" aria-label="Resumo do pedido">
      <h2>Resumo</h2>
      <dl>
        <div><dt>Produtos (<?= (int) $carrinho->contar() ?>)</dt><dd><?= e(moeda($carrinho->total())) ?></dd></div>
        <div class="resumo__total"><dt>Total</dt><dd><?= e(moeda($carrinho->total())) ?></dd></div>
      </dl>
      <a class="botao botao--primario botao--cheio" href="<?= e(url('pedido', 'checkout')) ?>">Finalizar compra</a>
      <a class="botao botao--secundario botao--cheio" href="<?= e(url('loja', 'index')) ?>">Continuar comprando</a>
    </aside>
  </div>
<?php endif; ?>
