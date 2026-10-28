<nav class="migalhas" aria-label="Você está em">
  <a href="<?= e(url('loja', 'index')) ?>">Início</a>
  <a href="<?= e(url('loja', 'index', ['categoria_id' => $produto['categoria_id']])) ?>"><?= e($produto['categoria_nome']) ?></a>
  <span><?= e($produto['nome']) ?></span>
</nav>

<article class="produto" data-produto>
  <div class="produto__foto">
    <img src="<?= e($imagem) ?>" alt="<?= e($produto['nome']) ?>" width="640" height="640">
  </div>

  <div class="produto__info">
    <p class="produto__categoria"><?= e($produto['categoria_nome']) ?></p>
    <h1><?= e($produto['nome']) ?></h1>

    <p class="produto__preco" data-preco>
      <small>a partir de</small> <?= e(moeda($precoMin)) ?>
    </p>

    <form class="compra" action="<?= e(url('carrinho', 'adicionar')) ?>" method="post" data-form-compra>
      <?= csrfCampo() ?>
      <input type="hidden" name="variacao_id" value="" data-variacao-id>

      <fieldset class="opcoes" data-grupo="tamanho">
        <legend>Tamanho: <strong data-rotulo-tamanho></strong></legend>
        <div class="opcoes__lista" data-lista-tamanho></div>
      </fieldset>

      <fieldset class="opcoes" data-grupo="cor">
        <legend>Cor: <strong data-rotulo-cor></strong></legend>
        <div class="opcoes__lista" data-lista-cor></div>
      </fieldset>

      <p class="disponibilidade" data-disponibilidade aria-live="polite"></p>

      <div class="quantidade">
        <label for="quantidade">Quantidade</label>
        <input id="quantidade" name="quantidade" type="number" value="1" min="1" max="1" inputmode="numeric" data-quantidade>
      </div>

      <div class="compra__botoes">
        <button type="submit" name="comprar_agora" value="1" class="botao botao--primario" data-botao-compra>Comprar agora</button>
        <button type="submit" class="botao botao--secundario" data-botao-compra>Adicionar ao carrinho</button>
      </div>

      <noscript><p class="aviso aviso--erro">Ative o JavaScript do navegador para escolher tamanho e cor.</p></noscript>
    </form>
  </div>

  <?php if (trim((string) $produto['descricao']) !== ''): ?>
    <section class="produto__descricao">
      <h2>Descrição</h2>
      <p><?= nl2br(e($produto['descricao'])) ?></p>
    </section>
  <?php endif; ?>
</article>

<script type="application/json" id="variacoes-data"><?= json_encode($variacoes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
