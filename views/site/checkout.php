<h1 class="titulo-pagina">Finalizar compra</h1>

<div class="carrinho">
  <form class="formulario checkout__form" action="<?= e(url('pedido', 'pagamento')) ?>" method="post" novalidate>
    <?= csrfCampo() ?>
    <h2>Endereço de entrega</h2>

    <?php if ($erro): ?><div class="aviso aviso--erro" role="alert"><?= e($erro) ?></div><?php endif; ?>

    <?php if (!empty($cliente['endereco'])): ?>
    <div class="opcoes-endereco">
      <label class="opcoes-endereco__item"><input type="radio" name="endereco_opcao" value="cadastrado" checked> Usar endereço cadastrado</label>
      <label class="opcoes-endereco__item"><input type="radio" name="endereco_opcao" value="novo"<?= ($old['endereco_opcao'] ?? '') === 'novo' ? ' checked' : '' ?>> Usar outro endereço para este pedido</label>
    </div>
    <p id="endereco-cadastrado-resumo" class="checkout__cliente"><?= nl2br(e($cliente['endereco'])) ?></p>
    <?php endif; ?>

    <div id="bloco-endereco-novo">
      <p class="formulario__intro">Informe o CEP para preencher o endereço automaticamente (ViaCEP).</p>

      <div class="campo">
        <label for="cep">CEP</label>
        <input id="cep" type="text" name="cep" value="<?= e($old['cep'] ?? '') ?>" inputmode="numeric" maxlength="9" placeholder="00000-000">
        <span class="campo__ajuda" id="cep-status"></span>
      </div>

      <div class="campo">
        <label for="rua">Rua / Logradouro</label>
        <input id="rua" type="text" name="rua" value="<?= e($old['rua'] ?? '') ?>" maxlength="150" autocomplete="address-line1">
      </div>

      <div class="campo">
        <label for="numero">Número</label>
        <input id="numero" type="text" name="numero" value="<?= e($old['numero'] ?? '') ?>" maxlength="10">
      </div>

      <div class="campo">
        <label for="complemento">Complemento <span class="campo__opcional">(opcional)</span></label>
        <input id="complemento" type="text" name="complemento" value="<?= e($old['complemento'] ?? '') ?>" maxlength="100" autocomplete="address-line2">
      </div>

      <div class="campo">
        <label for="bairro">Bairro</label>
        <input id="bairro" type="text" name="bairro" value="<?= e($old['bairro'] ?? '') ?>" maxlength="100">
      </div>

      <div class="campo">
        <label for="cidade">Cidade</label>
        <input id="cidade" type="text" name="cidade" value="<?= e($old['cidade'] ?? '') ?>" maxlength="100">
      </div>

      <div class="campo">
        <label for="uf">Estado (UF)</label>
        <input id="uf" type="text" name="uf" value="<?= e($old['uf'] ?? '') ?>" maxlength="2">
      </div>
    </div>

    <p class="checkout__cliente">Pedido em nome de <strong><?= e($cliente['nome']) ?></strong> (CPF <?= e(formatarCpf($cliente['cpf'])) ?>).</p>

    <button type="submit" class="botao botao--primario botao--cheio">Ir para pagamento</button>
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
    <dl>
      <div class="resumo__total"><dt>Total</dt><dd><?= e(moeda($carrinho->total())) ?></dd></div>
    </dl>
    <a class="link" href="<?= e(url('carrinho', 'index')) ?>">Voltar ao carrinho</a>
  </aside>
</div>

<script>
(function () {
  var radios = document.querySelectorAll('input[name="endereco_opcao"]');
  var blocoNovo = document.getElementById('bloco-endereco-novo');
  var resumo = document.getElementById('endereco-cadastrado-resumo');

  function atualizar() {
    var opcao = document.querySelector('input[name="endereco_opcao"]:checked');
    var usarNovo = !opcao || opcao.value === 'novo';
    blocoNovo.style.display = usarNovo ? '' : 'none';
    if (resumo) resumo.style.display = usarNovo ? 'none' : '';
  }
  if (radios.length) {
    radios.forEach(function (r) { r.addEventListener('change', atualizar); });
    atualizar();
  }

  var cep = document.getElementById('cep');
  var status = document.getElementById('cep-status');
  var mapa = { rua: 'logradouro', bairro: 'bairro', cidade: 'cidade', uf: 'estado' };

  cep.addEventListener('input', function () {
    cep.value = cep.value.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d)/, '$1-$2');
  });

  cep.addEventListener('blur', function () {
    var digitos = cep.value.replace(/\D/g, '');
    if (digitos.length !== 8) return;
    status.textContent = 'Buscando endereço...';
    fetch('index.php?controller=cep&action=buscar&cep=' + digitos)
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.erro) { status.textContent = d.erro; return; }
        Object.keys(mapa).forEach(function (campo) {
          var el = document.getElementById(campo);
          if (el && d[mapa[campo]]) el.value = d[mapa[campo]];
        });
        status.textContent = '';
        document.getElementById('numero').focus();
      })
      .catch(function () { status.textContent = 'Não foi possível consultar o CEP agora.'; });
  });
})();
</script>