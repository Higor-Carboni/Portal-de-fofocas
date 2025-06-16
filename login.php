<?php
require_once 'conexao.php';
session_start();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
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
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            background: #f0f4ff;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header.topo {
            background: #3A5EFF;
            color: white;
            padding: 10px 20px;
            border-bottom: 4px solid #1A237E;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            height: 50px;
            animation: girarLogo 20s linear infinite;
        }
        @keyframes girarLogo {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-box {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0001;
            display: flex;
            flex-direction: column;
            gap: 18px;
            width: 100%;
            max-width: 400px;
        }
        .form-box input {
            padding: 12px;
            border: 1.5px solid #bfc9d1;
            border-radius: 8px;
            font-size: 1em;
            background: #f8fafc;
        }
        .form-box input:focus {
            border-color: #3A5EFF;
            outline: none;
            background: #fff;
        }
        .form-box button {
            background: #3A5EFF;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
        }
        .form-box button:hover {
            background: #1A237E;
        }
        .form-box a {
            text-align: center;
            color: #3A5EFF;
            text-decoration: underline;
            font-size: 0.95em;
        }
        .msg-erro {
            color: #c0392b;
            text-align: center;
            font-size: 0.95em;
        }
        footer {
            background: #3A5EFF;
            color: white;
            text-align: center;
            padding: 16px 10px;
        }
        .redes img {
            width: 24px;
            margin: 0 8px;
            vertical-align: middle;
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
    <header class="topo">
        <img src="img/logoFofoca500.png" alt="Logo" class="logo">
        <span><strong>Fofocas Brasil 💬</strong></span>
    </header>

    <div class="container">
        <form method="POST" class="form-box">
            <h2>Login</h2>
            <?php if (!empty($mensagem)): ?>
                <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
            <button type="button" onclick="window.location.href='index.php'">Voltar</button>
        </form>
    </div>

    <footer>
        <div class="redes">
             <a href="#"><i class="fab fa-instagram"></i></a>
             <a href="#"><i class="fab fa-facebook-f"></i></a>
             <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
        <small>© Fofocas Brasil — Todos os direitos reservados</small>
    </footer>

    <button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
    ↑
</button>
</body>
</html>