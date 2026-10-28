<?php
$erroCampo = fn(array $lista, string $c) => isset($lista[$c])
    ? '<span class="campo__erro" id="erro-' . $c . '">' . e($lista[$c]) . '</span>' : '';
$descrito = fn(array $lista, string $c) => isset($lista[$c]) ? ' aria-invalid="true" aria-describedby="erro-' . $c . '"' : '';
?>
<h1 class="titulo-pagina">Meus dados</h1>

<div class="duas-colunas">
  <section class="formulario">
    <h2>Dados do cadastro</h2>

    <?php if (isset($erros['geral'])): ?><div class="aviso aviso--erro" role="alert"><?= e($erros['geral']) ?></div><?php endif; ?>

    <form action="<?= e(url('cliente', 'meusDados')) ?>" method="post" novalidate>
      <?= csrfCampo() ?>

      <div class="campo">
        <label for="cpf">CPF</label>
        <input id="cpf" type="text" value="<?= e(formatarCpf($old['cpf'])) ?>" readonly aria-describedby="ajuda-cpf">
        <span class="campo__ajuda" id="ajuda-cpf">O CPF identifica seu cadastro e não pode ser alterado.</span>
      </div>

      <div class="campo">
        <label for="nome">Nome completo</label>
        <input id="nome" type="text" name="nome" value="<?= e($old['nome']) ?>" maxlength="100" required<?= $descrito($erros, 'nome') ?>>
        <?= $erroCampo($erros, 'nome') ?>
      </div>

      <div class="campo">
        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="<?= e($old['email']) ?>" maxlength="100" required<?= $descrito($erros, 'email') ?>>
        <?= $erroCampo($erros, 'email') ?>
      </div>

      <div class="campo">
        <label for="telefone">Telefone <span class="campo__opcional">(opcional)</span></label>
        <input id="telefone" type="text" name="telefone" value="<?= e(formatarTelefone($old['telefone'])) ?>" inputmode="tel" maxlength="15" data-mascara="telefone"<?= $descrito($erros, 'telefone') ?>>
        <?= $erroCampo($erros, 'telefone') ?>
      </div>

      <div class="campo">
        <label for="cep_dados">CEP <span class="campo__opcional">(preenche o endereço abaixo)</span></label>
        <input id="cep_dados" type="text" inputmode="numeric" maxlength="9" placeholder="00000-000">
        <span class="campo__ajuda" id="cep_dados-status"></span>
      </div>

      <div class="campo">
        <label for="endereco">Endereço</label>
        <textarea id="endereco" name="endereco" rows="3" maxlength="255" required<?= $descrito($erros, 'endereco') ?>><?= e($old['endereco']) ?></textarea>
        <?= $erroCampo($erros, 'endereco') ?>
      </div>

      <script>
      (function () {
        var cep = document.getElementById('cep_dados');
        var status = document.getElementById('cep_dados-status');
        var endereco = document.getElementById('endereco');
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
              endereco.value = [d.logradouro, d.bairro, d.cidade + '/' + d.estado, 'CEP ' + d.cep]
                .filter(Boolean).join(', ');
              status.textContent = 'Endereço preenchido. Complete com número e complemento.';
            })
            .catch(function () { status.textContent = 'Não foi possível consultar o CEP agora.'; });
        });
      })();
      </script>

      <button type="submit" class="botao botao--primario">Salvar alterações</button>
    </form>
  </section>

  <section class="formulario">
    <h2>Alterar senha</h2>
    <form action="<?= e(url('cliente', 'alterarSenha')) ?>" method="post" novalidate>
      <?= csrfCampo() ?>

      <div class="campo">
        <label for="senha_atual">Senha atual</label>
        <input id="senha_atual" type="password" name="senha_atual" autocomplete="current-password" required<?= $descrito($errosSenha, 'senha_atual') ?>>
        <?= $erroCampo($errosSenha, 'senha_atual') ?>
      </div>

      <div class="campo">
        <label for="senha">Nova senha</label>
        <input id="senha" type="password" name="senha" minlength="8" maxlength="72" autocomplete="new-password" required<?= $descrito($errosSenha, 'senha') ?>>
        <span class="campo__ajuda">Mínimo de 8 caracteres.</span>
        <?= $erroCampo($errosSenha, 'senha') ?>
      </div>

      <div class="campo">
        <label for="senha_confirmacao">Confirmar nova senha</label>
        <input id="senha_confirmacao" type="password" name="senha_confirmacao" maxlength="72" autocomplete="new-password" required<?= $descrito($errosSenha, 'senha_confirmacao') ?>>
        <?= $erroCampo($errosSenha, 'senha_confirmacao') ?>
      </div>

      <button type="submit" class="botao botao--secundario">Alterar senha</button>
    </form>
  </section>


  <form method="POST" action="<?= url('cliente', 'excluirConta') ?>" onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Essa ação não pode ser desfeita.');">
    <?= csrfCampo() ?>
    <label for="senha_exclusao">Confirme sua senha para excluir a conta</label>
    <input type="password" id="senha_exclusao" name="senha" required>
    <button type="submit">Excluir minha conta</button>
</form>
</div>