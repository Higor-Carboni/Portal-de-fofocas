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

            $usuario_id = $pdo->lastInsertId();
            $_SESSION['usuario_id'] = $usuario_id;
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
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0001;
            padding: 32px 28px 24px 28px;
            margin: 30px auto 0 auto;
            max-width: 370px;
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
            text-align: center;
            margin-top: 6px;
        }
        .form-box a:hover {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <header class="topo">
        <h1>Criar Conta ✍️</h1>
    </header>

    <div class="container">
        <?php if (!empty($mensagem)): ?>
            <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="post" class="form-box">
            <input type="text" name="nome" placeholder="Seu nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            <input type="email" name="email" placeholder="Seu e-mail" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <input type="password" name="senha" placeholder="Crie uma senha" required>
            <button type="submit">Cadastrar e Entrar</button>
            <a href="login.php">Já tem uma conta? Faça login</a>
        </form>
    </div>
</body>
</html>
