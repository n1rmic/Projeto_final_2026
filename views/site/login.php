<section class="formulario formulario--estreito">
  <h1>Entrar</h1>

  <?php if ($erro): ?><div class="aviso aviso--erro" role="alert"><?= e($erro) ?></div><?php endif; ?>

  <form action="<?= e(url('cliente', 'login')) ?>" method="post" novalidate>
    <?= csrfCampo() ?>
    <div class="campo">
      <label for="email">E-mail</label>
      <input id="email" type="email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus>
    </div>
    <div class="campo">
      <label for="senha">Senha</label>
      <input id="senha" type="password" name="senha" autocomplete="current-password" required>
    </div>
    <button type="submit" class="botao botao--primario botao--cheio">Entrar</button>
  </form>

  <p class="formulario__rodape">Ainda não tem conta? <a href="<?= e(url('cliente', 'cadastro')) ?>">Criar conta</a></p>
</section>
