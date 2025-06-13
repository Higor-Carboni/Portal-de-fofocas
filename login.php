<?php
require_once 'conexao.php';
session_start();
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    if ($email && $senha) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            header("Location: index.php");
            exit;
        } else {
            $mensagem = "Email ou senha inválidos.";
        }
    } else {
        $mensagem = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #f4f4f4;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }
        .topo {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #1a252f;
        }
        .topo h1 {
            margin: 0;
            font-size: 2.2em;
            font-weight: bold;
        }
        .menu-superior {
            margin-top: 10px;
        }
        .menu-superior a {
            margin: 0 8px;
            color: #ecf0f1;
            font-weight: bold;
            text-decoration: none;
        }
        .menu-superior a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 400px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0001;
            padding: 32px 28px 24px 28px;
        }
        .container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 18px;
        }
        .form-box {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .form-box input[type="text"],
        .form-box input[type="email"],
        .form-box input[type="password"] {
            padding: 12px;
            border: 1.5px solid #bfc9d1;
            border-radius: 8px;
            font-size: 1em;
            background: #f8fafc;
            transition: border 0.2s;
        }
        .form-box input:focus {
            border-color: #34495e;
            outline: none;
            background: #fff;
        }
        .form-box button {
            background: #34495e;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .form-box button:hover {
            background: #2c3e50;
        }
        .msg-erro {
            color: #c0392b;
            margin-top: 10px;
            text-align: center;
        }
        .form-box a {
            color: #34495e;
            text-decoration: underline;
            font-size: 0.98em;
        }
        .form-box a:hover {
            color: #2c3e50;
        }
        .container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        footer {
            margin-top: 40px;
            background: #2c3e50;
            color: #eee;
            text-align: center;
            padding: 30px 10px;
        }
        .redes {
            margin-bottom: 12px;
        }
        .redes img {
            width: 24px;
            margin: 0 6px;
            vertical-align: middle;
            filter: brightness(0) invert(1);
        }
        .form-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0001;
            padding: 32px 28px 24px 28px;
            margin: 30px auto 0 auto;
            max-width: 370px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .form-box input[type="email"],
        .form-box input[type="password"] {
            padding: 14px;
            border: 1.5px solid #bfc9d1;
            border-radius: 8px;
            font-size: 1.07em;
            background: #f8fafc;
            transition: border 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-box input[type="email"]:focus,
        .form-box input[type="password"]:focus {
            border-color: #34495e;
            background: #fff;
            box-shadow: 0 0 0 2px #34495e22;
        }
        .form-box button {
            background: #34495e;
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }
        .form-box button:hover {
            background: #2c3e50;
        }
        .form-box a {
            color: #34495e;
            text-decoration: underline;
            font-size: 0.98em;
            text-align: center;
            margin-top: 6px;
        }
        .form-box a:hover {
            color: #2c3e50;
        }
        .msg-erro {
            color: #c0392b;
            margin-top: 10px;
            text-align: center;
        }
        .container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <header class="topo">
        <h1>Fofocas Brasil💬</h1>
        <div class="menu-superior">
            <a href="index.php">Início</a>
            <a href="cadastroUsuario.php">Cadastrar</a>
        </div>
    </header>
    <main>
        <div class="container">
            <h2>Login</h2>
            <form method="POST" class="form-box">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
                <a href="cadastroUsuario.php">Ainda não tem cadastro? Cadastre-se</a>
            </form>
            <p class="msg-erro"><?= $mensagem ?></p>
        </div>
    </main>
    <footer>
        <div class="redes">
            <a href="#"><img src="icone-instagram.png" alt="Instagram"></a>
            <a href="#"><img src="icone-facebook.png" alt="Facebook"></a>
            <a href="#"><img src="icone-twitter.png" alt="Twitter"></a>
        </div>
        <small>© Fofocas Brasil — Todos os direitos reservados</small>
    </footer>
</body>
</html>