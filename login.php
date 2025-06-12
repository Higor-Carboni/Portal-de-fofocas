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
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="index.php" class="btn-voltar">← Voltar</a>
<div class="container">
    <h2>Login</h2>
    <form method="POST" class="form-box">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
    <p class="msg-erro"><?= $mensagem ?></p>
    <p><a href="cadastroUsuario.php">Ainda não tem cadastro? Cadastre-se</a></p>
</div>
</body>
</html>