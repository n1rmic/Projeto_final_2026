/* Loja de Cosplay — scripts do cliente (sem dependências) */
(function () {
  'use strict';

  var moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

  /* ---------------------------------------------------------------
     Máscaras de CPF e telefone
     --------------------------------------------------------------- */
  function mascaraCpf(v) {
    v = v.replace(/\D/g, '').slice(0, 11);
    return v
      .replace(/^(\d{3})(\d)/, '$1.$2')
      .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
      .replace(/\.(\d{3})(\d)/, '.$1-$2');
  }

  function mascaraTelefone(v) {
    v = v.replace(/\D/g, '').slice(0, 11);
    if (v.length > 10) return v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    if (v.length > 6) return v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    if (v.length > 2) return v.replace(/^(\d{2})(\d*)/, '($1) $2');
    return v;
  }

  document.querySelectorAll('[data-mascara]').forEach(function (campo) {
    var fn = campo.dataset.mascara === 'cpf' ? mascaraCpf : mascaraTelefone;
    campo.addEventListener('input', function () { campo.value = fn(campo.value); });
  });

  /* ---------------------------------------------------------------
     Carrinho: envia o formulário ao mudar a quantidade
     --------------------------------------------------------------- */
  document.querySelectorAll('[data-autoenviar]').forEach(function (input) {
    input.addEventListener('change', function () {
      if (input.value !== '' && Number(input.value) >= 1) input.form.submit();
    });
  });

  /* ---------------------------------------------------------------
     Página do produto: escolha de tamanho/cor -> preço, estoque, botão
     --------------------------------------------------------------- */
  var dadosEl = document.getElementById('variacoes-data');
  var raiz = document.querySelector('[data-produto]');
  if (!dadosEl || !raiz) return;

  var variacoes = JSON.parse(dadosEl.textContent);
  var precoEl = raiz.querySelector('[data-preco]');
  var idInput = raiz.querySelector('[data-variacao-id]');
  var qtdInput = raiz.querySelector('[data-quantidade]');
  var dispEl = raiz.querySelector('[data-disponibilidade]');
  var botoes = raiz.querySelectorAll('[data-botao-compra]');
  var listaTamanho = raiz.querySelector('[data-lista-tamanho]');
  var listaCor = raiz.querySelector('[data-lista-cor]');
  var rotuloTamanho = raiz.querySelector('[data-rotulo-tamanho]');
  var rotuloCor = raiz.querySelector('[data-rotulo-cor]');

  function unicos(campo) {
    var vistos = [];
    variacoes.forEach(function (v) { if (vistos.indexOf(v[campo]) === -1) vistos.push(v[campo]); });
    return vistos;
  }

  var tamanhos = unicos('tamanho');
  var cores = unicos('cor');

  function achar(tamanho, cor) {
    for (var i = 0; i < variacoes.length; i++) {
      if (variacoes[i].tamanho === tamanho && variacoes[i].cor === cor) return variacoes[i];
    }
    return null;
  }

  // começa na primeira variação que tenha estoque (senão, na primeira)
  var inicial = variacoes.filter(function (v) { return v.estoque > 0; })[0] || variacoes[0];
  var escolha = { tamanho: inicial.tamanho, cor: inicial.cor };

  function criarChips(lista, valores, campo) {
    valores.forEach(function (valor) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'chip';
      b.textContent = valor;
      b.dataset.valor = valor;
      b.addEventListener('click', function () {
        escolha[campo] = valor;
        atualizar();
      });
      lista.appendChild(b);
    });
  }

  criarChips(listaTamanho, tamanhos, 'tamanho');
  criarChips(listaCor, cores, 'cor');

  function marcarChips(lista, campo, outroCampo) {
    lista.querySelectorAll('.chip').forEach(function (chip) {
      var ativo = chip.dataset.valor === escolha[campo];
      chip.setAttribute('aria-pressed', ativo ? 'true' : 'false');

      // "sem estoque" = combinação com a outra escolha atual inexistente ou esgotada
      var v = campo === 'tamanho' ? achar(chip.dataset.valor, escolha.cor) : achar(escolha.tamanho, chip.dataset.valor);
      chip.classList.toggle('sem-estoque', !v || v.estoque <= 0);
    });
  }

  function bloquear(motivo, classe) {
    idInput.value = '';
    dispEl.textContent = motivo;
    dispEl.className = 'disponibilidade ' + (classe || '');
    qtdInput.disabled = true;
    botoes.forEach(function (b) { b.disabled = true; });
  }

  function atualizar() {
    rotuloTamanho.textContent = escolha.tamanho;
    rotuloCor.textContent = escolha.cor;
    marcarChips(listaTamanho, 'tamanho');
    marcarChips(listaCor, 'cor');

    var v = achar(escolha.tamanho, escolha.cor);

    if (!v) {
      bloquear('Essa combinação de tamanho e cor não é vendida. Escolha outra opção.', 'esgotado');
      return;
    }

    precoEl.textContent = moeda.format(v.preco);

    if (v.estoque <= 0) {
      bloquear('Esgotado.', 'esgotado');
      return;
    }

    idInput.value = v.id;
    qtdInput.disabled = false;
    qtdInput.max = v.estoque;
    if (Number(qtdInput.value) > v.estoque || Number(qtdInput.value) < 1) qtdInput.value = 1;
    botoes.forEach(function (b) { b.disabled = false; });

    if (v.estoque <= 5) {
      dispEl.textContent = v.estoque === 1 ? 'Última unidade disponível.' : 'Últimas ' + v.estoque + ' unidades.';
      dispEl.className = 'disponibilidade ultimas';
    } else {
      dispEl.textContent = v.estoque + ' unidades disponíveis.';
      dispEl.className = 'disponibilidade';
    }
  }

  atualizar();

  // último cuidado antes de enviar (o servidor valida de novo)
  raiz.querySelector('[data-form-compra]').addEventListener('submit', function (ev) {
    if (!idInput.value) { ev.preventDefault(); return; }
    var q = Number(qtdInput.value);
    if (!Number.isInteger(q) || q < 1 || q > Number(qtdInput.max)) {
      ev.preventDefault();
      qtdInput.focus();
      dispEl.textContent = 'Informe uma quantidade entre 1 e ' + qtdInput.max + '.';
      dispEl.className = 'disponibilidade esgotado';
    }
  });
})();
