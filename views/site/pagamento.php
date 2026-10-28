<h1 class="titulo-pagina">Forma de pagamento</h1>

<div class="carrinho">
  <form class="formulario checkout__form" action="<?= e(url('pedido', 'finalizar')) ?>" method="post" novalidate>
    <?= csrfCampo() ?>
    <h2>Selecione o método de pagamento</h2>

    <?php if ($erro): ?><div class="aviso aviso--erro" role="alert"><?= e($erro) ?></div><?php endif; ?>

    <div class="pgto-metodos">
      <label class="pgto-metodo">
        <input type="radio" name="metodo" value="cartao" checked>
        <span class="pgto-metodo__card">
          <span class="pgto-metodo__icone">💳</span>
          <span>
            <span class="pgto-metodo__titulo">Cartão de crédito</span>
            <span class="pgto-metodo__sub">Visa, Master, Elo...</span>
          </span>
        </span>
      </label>
      <label class="pgto-metodo">
        <input type="radio" name="metodo" value="pix">
        <span class="pgto-metodo__card">
          <span class="pgto-metodo__icone">⚡</span>
          <span>
            <span class="pgto-metodo__titulo">Pix</span>
            <span class="pgto-metodo__sub">Aprovação imediata</span>
          </span>
        </span>
      </label>
    </div>

    <div id="bloco-cartao" class="pgto-painel">
      <h3>Dados do cartão</h3>
      <div class="pgto-grid">
        <div class="campo campo--full">
          <label for="numero_cartao">Número do cartão</label>
          <input id="numero_cartao" type="text" name="numero_cartao" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000">
        </div>
        <div class="campo campo--full">
          <label for="nome_cartao">Nome impresso no cartão</label>
          <input id="nome_cartao" type="text" name="nome_cartao" maxlength="100" placeholder="Como está no cartão">
        </div>
        <div class="campo">
          <label for="validade_cartao">Validade</label>
          <input id="validade_cartao" type="text" name="validade_cartao" maxlength="5" placeholder="MM/AA">
        </div>
        <div class="campo">
          <label for="cvv_cartao">CVV</label>
          <input id="cvv_cartao" type="text" name="cvv_cartao" inputmode="numeric" maxlength="4" placeholder="000">
        </div>
      </div>
      <p class="pgto-selo-seguranca">🔒 Ambiente seguro — seus dados de pagamento são protegidos.</p>
    </div>

    <div id="bloco-pix" class="pgto-painel pgto-pix" style="display:none">
      <h3>Pague com Pix</h3>
      <img src="public/assets/img/qrcode-pix.svg" alt="QR Code Pix" width="200" height="200">
      <p>Abra o app do seu banco, escolha pagar com Pix e escaneie o código acima. A confirmação é automática.</p>
    </div>

    <button type="submit" class="botao botao--primario botao--cheio" style="margin-top:1.3rem">Finalizar compra</button>
  </form>

  <aside class="resumo" aria-label="Itens do pedido">
    <h2>Seu pedido</h2>
    <ul class="resumo__itens">
      <?php foreach ($carrinho->itens() as $item): ?>
        <li>
          <span><?= (int) $item['quantidade'] ?> × <?= e($item['produto_nome']) ?>
            <small><?= e($item['tamanho']) ?>, <?= e($item['cor']) ?></small></span>
          <span><?= e(moeda($carrinho->subtotal($item))) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="pgto-total-fixo">
      <span>Total</span>
      <strong><?= e(moeda($carrinho->total())) ?></strong>
    </div>
    <a class="link" href="<?= e(url('pedido', 'checkout')) ?>">Voltar ao endereço</a>
  </aside>
</div>

<script>
(function () {
  var radios = document.querySelectorAll('input[name="metodo"]');
  var cartao = document.getElementById('bloco-cartao');
  var pix = document.getElementById('bloco-pix');
  function atualizar() {
    var escolhido = document.querySelector('input[name="metodo"]:checked').value;
    cartao.style.display = escolhido === 'cartao' ? '' : 'none';
    pix.style.display = escolhido === 'pix' ? '' : 'none';
  }
  radios.forEach(function (r) { r.addEventListener('change', atualizar); });
  atualizar();
})();
</script>