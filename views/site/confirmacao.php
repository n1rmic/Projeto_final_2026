<section class="confirmacao">
  <h1>Pedido realizado com sucesso</h1>
  <p>Recebemos o pedido <strong>#<?= (int) $pedido['id'] ?></strong>. Ele está com status <strong><?= e($pedido['status']) ?></strong> e será atualizado pela loja conforme o pagamento e o envio.</p>

  <dl class="confirmacao__dados">
    <div><dt>Entrega em</dt><dd><?= e($pedido['endereco_entrega']) ?></dd></div>
    <div><dt>Total</dt><dd><?= e(moeda($pedido['valor_total'])) ?></dd></div>
  </dl>

  <ul class="lista-itens">
    <?php foreach ($pedido['itens'] as $i): ?>
      <li>
        <span><?= (int) $i['quantidade'] ?> × <?= e($i['produto_nome']) ?> <small>(<?= e($i['tamanho']) ?>, <?= e($i['cor']) ?>)</small></span>
        <span><?= e(moeda($i['preco_unitario'] * $i['quantidade'])) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="confirmacao__botoes">
    <a class="botao botao--primario" href="<?= e(url('pedido', 'meusPedidos')) ?>">Ver meus pedidos</a>
    <a class="botao botao--secundario" href="<?= e(url('loja', 'index')) ?>">Continuar comprando</a>
    <a class="botao botao--secundario" href="<?= e(url('pedido', 'verNota', ['id' => $pedido['id']])) ?>">Ver Nota Fiscal</a>
  </div>
</section>