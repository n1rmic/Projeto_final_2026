?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <style>
        body{
            font-family: Arial;
            background:#f2f2f2;
        }

        .login{
            width:300px;
            margin:100px auto;
            background:#fff;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        input{
            width:100%;
            padding:10px;
            margin:10px 0;
        }

        button{
            width:100%;
            padding:10px;
            background:#007bff;
            color:white;
            border:none;
            cursor:pointer;
        }

        .erro{
            color:red;
        }
    </style>
</head>
<body>

<div class="login">
    <h2>Login</h2>

    <?php if(isset($erro)) : ?>
        <p class="erro"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuário" required>

        <input type="password" name="senha" placeholder="Senha" required>

        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>