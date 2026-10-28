<?php
$nomeUser = $_SESSION['nome'] ?? 'Usuário';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Fornecedores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
  <link rel="stylesheet" href="/loja_roupas/public/assets/css/style.css">
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div class="brand">
        <div class="badge"></div>
        <div>
          <h1>Fornecedores</h1>
       <small>CRUD (admin)</small>
        </div>
      </div>
      <div class="pill">
        Olá, <strong><?= htmlspecialchars($nomeUser) ?></strong>
        • <a href="/loja_roupas/index.php?controller=auth&action=logout">Sair</a>
      </div>
    </div>
 
    <div class="grid" style="grid-template-columns: 1fr 2fr;">
      <div class="card">
        <h2><?= $editar ? "Editar Fornecedor #".(int)$editar['id'] : "Novo Fornecedor" ?></h2>
 
        <form method="post" action="index.php?controller=fornecedor&action=salvar">
          <input type="hidden" name="id" value="<?= $editar ? (int)$editar['id'] : 0 ?>">
 
          <label>Nome</label>
          <input class="input" type="text" name="nome" required
                 value="<?= $editar ? htmlspecialchars($editar['nome']) : '' ?>">
 
          <label>CNPJ</label>
          <input class="input" type="text" name="cnpj"
                 value="<?= $editar ? htmlspecialchars($editar['cnpj'] ?? '') : '' ?>">
 
          <label>Telefone</label>
          <input class="input" type="text" name="telefone"
                 value="<?= $editar ? htmlspecialchars($editar['telefone'] ?? '') : '' ?>">
 
          <label>E-mail</label>
          <input class="input" type="email" name="email"
                 value="<?= $editar ? htmlspecialchars($editar['email'] ?? '') : '' ?>">
 <label>CEP</label>
<input class="input" type="text" id="campo-cep" name="cep" maxlength="9"
       placeholder="00000-000"
       value="<?= $editar ? htmlspecialchars($editar['cep'] ?? '') : '' ?>">
<small class="muted" id="status-cep"></small>

          <label>Endereço</label>
          <textarea class="input" name="endereco" rows="2"><?= $editar ? htmlspecialchars($editar['endereco'] ?? '') : '' ?></textarea>
 
          <div class="actions" style="margin-top:14px; display:flex; gap:10px;">
            <button class="btn btn-primary" type="submit">Salvar</button>
            <a class="btn" href="index.php?controller=fornecedor&action=index">Limpar</a>
          </div>
        </form>
      </div>
 
      <div class="card">

   <!-- LISTA DE FORNECEDORES -->
<h2>Lista de Fornecedores</h2>

<!-- Envolvemos a tabela para permitir rolagem caso a tela seja pequena -->
<div style="overflow-x: auto;">
  <table class="table" id="tabela-fornecedores">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>CNPJ</th>
        <th>Telefone</th>
        <th>Endereço</th>
        <th>Status</th>
        <!-- Removido o width fixo de 260px que estava esmagando o endereço -->
        <th>Ações</th> 
      </tr>
    </thead>
    <tbody>
      <?php foreach ($fornecedores as $f): ?>
      <tr>
        <td>#<?= (int)$f['id'] ?></td>
        <td><?= htmlspecialchars($f['nome']) ?></td>
        <td><?= htmlspecialchars($f['cnpj'] ?? '-') ?></td>
        <td style="white-space: nowrap;"><?= htmlspecialchars($f['telefone'] ?? '-') ?></td>
        <td><?= htmlspecialchars($f['endereco'] ?? '-') ?></td>
        <td>
          <?= ((int)$f['ativo'] === 1)
              ? '<span class="tag ok">Ativo</span>'
              : '<span class="tag off">Inativo</span>' ?>
        </td>
        <!-- Adicionada a classe td-acoes para alinhar os botões -->
        <td class="td-acoes">
          <a class="btn" href="index.php?controller=fornecedor&action=index&id=<?= (int)$f['id'] ?>">Editar</a>
          
          <?php if ((int)$f['ativo'] === 1): ?>
            <a class="btn btn-danger"
               href="index.php?controller=fornecedor&action=toggle&id=<?= (int)$f['id'] ?>&ativo=0"
               onclick="return confirm('Inativar este fornecedor?')">Inativar</a>
          <?php else: ?>
            <a class="btn btn-success"
               href="index.php?controller=fornecedor&action=toggle&id=<?= (int)$f['id'] ?>&ativo=1">Ativar</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>





        <div style="margin-top:14px;">
          <a class="btn" href="/loja_roupas/index.php?controller=auth&action=dashboard">Voltar ao Dashboard</a>
        </div>
      </div>
    </div>
  </div>


<script>
document.getElementById('campo-cep').addEventListener('blur', function () {
    const cepDigitado = this.value.replace(/\D/g, ''); // remove tudo que não é número
    const status = document.getElementById('status-cep');
    const campoEndereco = document.querySelector('textarea[name="endereco"]');
 
    if (cepDigitado.length !== 8) {
        status.textContent = '';
        return;
    }
 
    status.textContent = 'Buscando endereço...';
 
    fetch('index.php?controller=cep&action=buscar&cep=' + cepDigitado)
        .then(function (resposta) {
            return resposta.json();
        })
        .then(function (dados) {
            if (dados.erro) {
                status.textContent = 'Erro: ' + dados.erro;
                return;
            }
    const enderecoCompleto =
                dados.logradouro + ', ' +
                dados.bairro + ' - ' +
                dados.cidade + '/' + dados.estado;
 
            campoEndereco.value = enderecoCompleto;
            status.textContent = 'Endereço preenchido automaticamente. ✅';
        })
        .catch(function () {
            status.textContent = 'Não foi possível consultar o CEP agora.';
        });
});
</script>







</body>
</html>
