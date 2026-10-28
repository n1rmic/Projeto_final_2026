<?php
/** Mostra a mensagem de erro de um campo, se houver. */
$erroCampo = fn(string $c) => isset($erros[$c])
    ? '<span class="campo__erro" id="erro-' . $c . '">' . e($erros[$c]) . '</span>' : '';
$descrito = fn(string $c) => isset($erros[$c]) ? ' aria-invalid="true" aria-describedby="erro-' . $c . '"' : '';
?>
<section class="formulario">
  <h1>Criar conta</h1>
  <p class="formulario__intro">Com a conta você finaliza compras e acompanha seus pedidos.</p>

  <?php if (isset($erros['geral'])): ?><div class="aviso aviso--erro" role="alert"><?= e($erros['geral']) ?></div><?php endif; ?>

  <form action="<?= e(url('cliente', 'cadastro')) ?>" method="post" novalidate>
    <?= csrfCampo() ?>

    <div class="campo">
      <label for="nome">Nome completo</label>
      <input id="nome" type="text" name="nome" value="<?= e($old['nome']) ?>" maxlength="100" autocomplete="name" required<?= $descrito('nome') ?>>
      <?= $erroCampo('nome') ?>
    </div>

    <div class="campo">
      <label for="email">E-mail</label>
      <input id="email" type="email" name="email" value="<?= e($old['email']) ?>" maxlength="100" autocomplete="email" required<?= $descrito('email') ?>>
      <?= $erroCampo('email') ?>
    </div>

    <div class="campo">
      <label for="cpf">CPF</label>
      <input id="cpf" type="text" name="cpf" value="<?= e(formatarCpf($old['cpf'])) ?>" inputmode="numeric" maxlength="14" placeholder="000.000.000-00" data-mascara="cpf" required<?= $descrito('cpf') ?>>
      <?= $erroCampo('cpf') ?>
    </div>

    <div class="campo">
      <label for="telefone">Telefone <span class="campo__opcional">(opcional)</span></label>
      <input id="telefone" type="text" name="telefone" value="<?= e(formatarTelefone($old['telefone'])) ?>" inputmode="tel" maxlength="15" placeholder="(21) 99999-9999" data-mascara="telefone" autocomplete="tel"<?= $descrito('telefone') ?>>
      <?= $erroCampo('telefone') ?>
    </div>

    <div class="campo">
      <label for="endereco">Endereço</label>
      <textarea id="endereco" name="endereco" rows="3" maxlength="255" autocomplete="street-address" placeholder="Rua, número, bairro, cidade/UF e CEP" required<?= $descrito('endereco') ?>><?= e($old['endereco']) ?></textarea>
      <?= $erroCampo('endereco') ?>
    </div>

    <div class="campo">
      <label for="senha">Senha</label>
      <input id="senha" type="password" name="senha" minlength="8" maxlength="72" autocomplete="new-password" required<?= $descrito('senha') ?>>
      <span class="campo__ajuda">Mínimo de 8 caracteres.</span>
      <?= $erroCampo('senha') ?>
    </div>

    <div class="campo">
      <label for="senha_confirmacao">Confirmar senha</label>
      <input id="senha_confirmacao" type="password" name="senha_confirmacao" maxlength="72" autocomplete="new-password" required<?= $descrito('senha_confirmacao') ?>>
      <?= $erroCampo('senha_confirmacao') ?>
    </div>

    <button type="submit" class="botao botao--primario botao--cheio">Criar conta</button>
  </form>

  <p class="formulario__rodape">Já tem conta? <a href="<?= e(url('cliente', 'login')) ?>">Entrar</a></p>
</section>
