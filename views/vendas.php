<?php
$nomeUser = $_SESSION['nome'] ?? 'Vendedor';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Caixa PDV - Finalizar Venda</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
  <link rel="stylesheet" href="/loja_roupas/public/assets/css/style.css">
  <style>
    /* Estilos do Terminal de Caixa (PDV) */
    .pdv-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 20px;
      align-items: start;
    }

    .visor-caixa {
      background: #05050e;
      border: 2px solid var(--neon2);
      border-radius: var(--radius);
      padding: 20px;
      text-align: right;
      box-shadow: 0 0 20px rgba(0, 229, 255, 0.2);
    }

    .visor-label {
      color: var(--muted);
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .visor-valor {
      font-size: 38px;
      font-weight: 900;
      color: var(--neon3);
      font-family: monospace;
      margin: 8px 0;
      text-shadow: 0 0 10px rgba(124, 255, 0, 0.4);
    }

    .pdv-info-box {
      background: rgba(255, 255, 255, 0.04);
      border-radius: 12px;
      padding: 12px;
      margin-top: 14px;
      font-size: 13px;
    }

    .pdv-info-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 6px;
      color: var(--muted);
    }

    .pdv-info-item strong {
      color: var(--txt);
    }

    .alert-box {
      padding: 14px 18px;
      border-radius: 14px;
      margin-bottom: 18px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .alert-erro {
      background: rgba(255, 43, 80, 0.15);
      border: 1px solid #ff2b50;
      color: #ff8095;
    }

    .alert-sucesso {
      background: rgba(0, 255, 170, 0.15);
      border: 1px solid #00ffaa;
      color: #70ffcd;
    }

    .badge-estoque {
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: bold;
    }

    .badge-ok { background: rgba(0,229,255,0.15); color: var(--neon2); }
    .badge-empty { background: rgba(255,43,80,0.2); color: #ff5050; }

    @media (max-width: 950px) {
      .pdv-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="topbar">
    <div class="brand">
      <div class="badge" style="background: linear-gradient(135deg, var(--neon2), var(--neon1));"></div>
      <div>
        <h1>Terminal de Caixa (PDV)</h1>
        <small>Atendimento de Balcão e Fechamento de Venda</small>
      </div>
    </div>
    <div class="pill">
      Vendedor: <strong><?= htmlspecialchars($nomeUser) ?></strong>
      • <a href="/loja_roupas/index.php?controller=auth&action=logout">Sair</a>
    </div>
  </div>

  <!-- BANNERS DE ALERTA -->
  <?php if (isset($_SESSION['erro'])): ?>
    <div class="alert-box alert-erro">
      ⚠️ <?= htmlspecialchars($_SESSION['erro']) ?>
    </div>
    <?php unset($_SESSION['erro']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['sucesso'])): ?>
    <div class="alert-box alert-sucesso">
      ✅ <?= htmlspecialchars($_SESSION['sucesso']) ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
  <?php endif; ?>

  <form method="post" action="index.php?controller=venda&action=salvar" id="form-caixa">
    <div class="pdv-grid">
      
      <!-- COLUNA DA ESQUERDA: REGISTRO DE ITENS DA COMPRA -->
      <div class="card">
        <h2>1. Identificação & Itens</h2>
        
                <div class="form-group">
          <label for="busca-cpf-cliente">Buscar cliente pelo CPF</label>
          <div style="display:flex; gap:8px;">
            <input class="input" type="text" id="busca-cpf-cliente" placeholder="Somente números" maxlength="14" inputmode="numeric">
            <button type="button" class="btn" id="btn-buscar-cpf">Buscar</button>
          </div>
          <small id="msg-busca-cpf" style="display:block; margin-top:4px;"></small>
        </div>

        <div class="form-group">
          <label>Cliente Comprador</label>
          <select class="input" name="cliente_id" required>
            <option value="">-- Selecione o Cliente --</option>
            <?php foreach ($clientes as $cl): ?>
              <option value="<?= (int)$cl['id'] ?>"><?= htmlspecialchars($cl['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <h3 style="margin-top:22px; font-size: 16px;">Passagem de Produtos</h3>
        
        <table class="table" id="tabela-itens">
          <thead>
            <tr>
              <th>Produto / Variação (SKU)</th>
              <th style="width:90px; text-align:center;">Estoque</th>
              <th style="width:100px;">Qtd</th>
              <th style="width:120px;">Preço (R$)</th>
              <th style="width:120px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($linha = 0; $linha < 5; $linha++): ?>
            <tr>
              <td>
                <select class="input select-variacao" name="variacao_id[]">
                  <option value="" data-preco="0" data-estoque="0">-- Nenhum Item --</option>
                  <?php foreach ($variacoes as $v): ?>
                    <option value="<?= (int)$v['id'] ?>" 
                            data-preco="<?= (float)$v['preco'] ?>"
                            data-estoque="<?= (int)$v['estoque_qtd'] ?>">
                      <?= htmlspecialchars($v['produto_nome']) ?> —
                      <?= htmlspecialchars($v['sku']) ?>
                      (<?= htmlspecialchars($v['tamanho'] ?? '-') ?>/<?= htmlspecialchars($v['cor'] ?? '-') ?>) 
                      | Stock: <?= (int)$v['estoque_qtd'] ?> un.
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td style="text-align:center;">
                <span class="badge-estoque badge-empty cell-estoque">0</span>
              </td>
              <td>
                <input class="input input-qtd" type="number" min="0" name="quantidade[]" value="0">
              </td>
              <td>
                <input class="input input-preco" type="text" name="preco_unitario[]" value="0,00">
              </td>
              <td style="font-weight: bold; color: var(--neon2);" class="cell-subtotal">
                R$ 0,00
              </td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>

        <p class="muted" style="margin-top:12px;">
          💡 Os preços são sugeridos automaticamente. Você pode ajustar o preço unitário para dar descontos ou aplicar promoções de balcão.
        </p>
      </div>

      <!-- COLUNA DA DIREITA: VISOR E BOTÃO DE FINALIZAR DO CAIXA -->
      <div>
        <div class="visor-caixa">
          <div class="visor-label">TOTAL A PAGAR</div>
          <div class="visor-valor" id="total-geral-display">R$ 0,00</div>
          
          <div class="pdv-info-box">
            <div class="pdv-info-item">
              <span>Operador:</span>
              <strong><?= htmlspecialchars($nomeUser) ?></strong>
            </div>
            <div class="pdv-info-item">
              <span>Itens Selecionados:</span>
              <strong id="total-itens-count">0 un.</strong>
            </div>
            <div class="pdv-info-item">
              <span>Status Caixa:</span>
              <strong style="color:var(--neon3);">Pronto para Fechar</strong>
            </div>
          </div>

          <button class="btn btn-primary" type="submit" style="width:100%; margin-top:20px; padding: 16px; font-size: 16px;">
            🛒 FINALIZAR VENDA
          </button>
        </div>

        <div style="margin-top: 14px; text-align: center;">
          <a class="btn btn-secondary" href="/loja_roupas/index.php?controller=auth&action=dashboard" style="width:100%;">
            Voltar ao Dashboard
          </a>
        </div>
      </div>

    </div>
  </form>

  <br><br>

  <!-- LISTA DE VENDAS RECENTES -->
  <div class="card">
    <h2>Histórico do Caixa (Vendas Recentes)</h2>

    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Vendedor</th>
          <th>Data</th>
          <th>Valor Total</th>
          <th style="width:200px;">Ações de Nota Fiscal</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($vendas)): ?>
          <tr><td colspan="6" class="muted">Nenhuma venda realizada recentemente.</td></tr>
        <?php else: ?>
          <?php foreach ($vendas as $v): ?>
          <tr>
            <td>#<?= (int)$v['id'] ?></td>
            <td><?= htmlspecialchars($v['cliente_nome']) ?></td>
            <td><?= htmlspecialchars($v['vendedor_nome']) ?></td>
            <td><?= date('d/m/Y', strtotime($v['data'])) ?></td>
            <td><strong>R$ <?= number_format((float)$v['valor_total'], 2, ',', '.') ?></strong></td>
            <td>
              <a class="btn" href="index.php?controller=venda&action=emitirNota&venda_id=<?= (int)$v['id'] ?>">Emitir NF</a>
              <a class="btn btn-secondary" href="index.php?controller=venda&action=verNota&venda_id=<?= (int)$v['id'] ?>">Ver NF</a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabela = id('tabela-itens');

  function id(el) { return document.getElementById(el); }

  function recalcularTotais() {
    let totalGeral = 0;
    let totalUnidades = 0;

    document.querySelectorAll('#tabela-itens tbody tr').forEach(function (tr) {
      const select = tr.querySelector('.select-variacao');
      const inputQtd = tr.querySelector('.input-qtd');
      const inputPreco = tr.querySelector('.input-preco');
      const cellEstoque = tr.querySelector('.cell-estoque');
      const cellSubtotal = tr.querySelector('.cell-subtotal');

      const option = select.options[select.selectedIndex];
      const precoSugerido = parseFloat(option.getAttribute('data-preco') || 0);
      const estoqueDisponivel = parseInt(option.getAttribute('data-estoque') || 0);

      // Atualiza badge de estoque da linha
      cellEstoque.textContent = estoqueDisponivel;
      if (estoqueDisponivel > 0) {
        cellEstoque.className = 'badge-estoque badge-ok cell-estoque';
      } else {
        cellEstoque.className = 'badge-estoque badge-empty cell-estoque';
      }

      const qtd = parseInt(inputQtd.value) || 0;
      
      // Converte preço formatado
      let precoVal = inputPreco.value.replace('R$', '').replace(/\s/g, '').replace('.', '').replace(',', '.');
      let preco = parseFloat(precoVal) || 0;

      // Validação visual se colocar quantidade acima do estoque
      if (qtd > estoqueDisponivel && select.value !== "") {
        inputQtd.style.borderColor = '#ff4d4d';
        inputQtd.style.backgroundColor = 'rgba(255,77,77,0.1)';
      } else {
        inputQtd.style.borderColor = '';
        inputQtd.style.backgroundColor = '';
      }

      const subtotal = qtd * preco;
      cellSubtotal.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');

      if (select.value !== "" && qtd > 0) {
        totalGeral += subtotal;
        totalUnidades += qtd;
      }
    });

    // Atualiza o Visor Digital do Caixa
    id('total-geral-display').textContent = 'R$ ' + totalGeral.toFixed(2).replace('.', ',');
    id('total-itens-count').textContent = totalUnidades + ' un.';
  }

  // Monitora alterações nos selects de variação
  document.querySelectorAll('.select-variacao').forEach(function (select) {
    select.addEventListener('change', function () {
      const tr = this.closest('tr');
      const option = this.options[this.selectedIndex];
      const preco = parseFloat(option.getAttribute('data-preco') || 0);
      const inputPreco = tr.querySelector('.input-preco');
      const inputQtd = tr.querySelector('.input-qtd');

      if (this.value !== "") {
        inputPreco.value = preco.toFixed(2).replace('.', ',');
        if (parseInt(inputQtd.value) === 0) {
          inputQtd.value = 1; // Coloca 1 por padrão ao escolher o produto
        }
      } else {
        inputPreco.value = '0,00';
        inputQtd.value = 0;
      }

      recalcularTotais();
    });
  });

  // Monitora digitação de quantidade e preços
  document.querySelectorAll('.input-qtd, .input-preco').forEach(function (input) {
    input.addEventListener('input', recalcularTotais);
  });

  // Cálculo inicial
  recalcularTotais();
});
</script>
<script>
document.getElementById('btn-buscar-cpf').addEventListener('click', async () => {
  const cpfInput = document.getElementById('busca-cpf-cliente');
  const msg = document.getElementById('msg-busca-cpf');
  const cpf = cpfInput.value.replace(/\D/g, '');

  if (cpf.length !== 11) {
    msg.textContent = 'Informe um CPF com 11 dígitos.';
    msg.style.color = '#ff5c5c';
    return;
  }

  msg.textContent = 'Buscando...';
  msg.style.color = 'inherit';

  try {
    const resp = await fetch(`index.php?controller=venda&action=buscarClientePorCpf&cpf=${cpf}`);
    const dados = await resp.json();
    const select = document.querySelector('select[name="cliente_id"]');

    if (!dados.encontrado) {
      msg.textContent = dados.mensagem || 'Cliente não encontrado. Use Cliente Balcao.';
      msg.style.color = '#ff5c5c';
      return;
    }

    let opcao = select.querySelector(`option[value="${dados.id}"]`);
    if (!opcao) {
      opcao = document.createElement('option');
      opcao.value = dados.id;
      select.appendChild(opcao);
    }
    opcao.textContent = dados.nome;
    select.value = dados.id;

    msg.textContent = `Cliente encontrado: ${dados.nome}`;
    msg.style.color = '#7cff00';
  } catch (e) {
    msg.textContent = 'Erro ao buscar cliente.';
    msg.style.color = '#ff5c5c';
  }
});
</script>

</body>
</html>