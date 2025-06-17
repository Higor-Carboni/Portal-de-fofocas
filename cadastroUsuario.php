<?php
require_once 'conexao.php';
require_once 'funcoes.php';
session_start();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'E-mail inválido.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensagem = 'E-mail já cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'normal')");
            $stmt->execute([$nome, $email, $senhaHash]);

            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_perfil'] = 'normal';

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta</title>
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
            padding: 20px 30px; /* Aumentado */
            border-bottom: 4px solid #1A237E;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 120px; /* Adicionado */
        }
       .logo {
            height: 100px;
            transform-origin: center;
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
        .form-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
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
       
    </header>

    <div class="container">
        <form method="post" class="form-box">
            <h2>Criar Conta</h2>
            <?php if (!empty($mensagem)): ?>
                <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <input type="text" name="nome" placeholder="Seu nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            <input type="email" name="email" placeholder="Seu e-mail" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <input type="password" name="senha" placeholder="Crie uma senha" required>
            
        
        <a href="login.php">Já tem uma conta? Faça login</a>
        
        <div class="form-buttons">
                <button type="submit">Salvar</button>
               <button type="button" onclick="window.location.href='index.php'">Voltar</button>
            </div>
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
</body>
</html>
