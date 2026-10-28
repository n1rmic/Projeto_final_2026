<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Login - Loja Cosplay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="icon" type="image/png" href="public/assets/img/dashboard.png">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="card" style="max-width:520px; margin:70px auto;">
        <div class="brand">
            <div class="badge"></div>
            <div>
                <h1>Loja Cosplay</h1>
                <small>Acesso ao sistema</small>
            </div>
        </div>
       
        <form method="post" action="/loja_roupas/index.php?controller=auth&action=login">
            <label>E-mail</label>
            <input type="email" name="email" required autocomplete="username">
           
            <label>Senha</label>
            <input type="password" name="senha" required autocomplete="current-password">
           
            <div style="margin-top:16px; display:flex; justify-content:flex-end; align-items: center;">
                <button class="btn" type="submit">Entrar</button>
               
                <a href="index.php?controller=usuario&action=create" class="btn" style="margin: 3px; text-decoration: none; text-align: center; display: inline-block;">
                    Cadastrar
                </a>
            </div>
        </form>


        <p style="margin-top:14px; color:var(--muted); font-size:13px;">
            Dica: se der erro, confirme se o usuário está <strong>ativo</strong> e se a senha está
            salva com <strong>hash</strong>.
        </p>
    </div>
</div>
</body>
</html>

