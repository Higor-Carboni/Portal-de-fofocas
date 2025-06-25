<?php
require_once 'conexao.php';
session_start();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            header("Location: dashboard.php");
            exit;
        } else {
            $mensagem = "Email ou senha inválidos.";
        }
    } else {
        $mensagem = "Preencha todos os campos corretamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="conteudo-page">

        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
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
        </main>

        <?php include 'includes/footer.php'; ?>

    </div>

    <!-- Botão voltar ao topo -->
    <button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">↑</button>

    <script>
        window.addEventListener('scroll', function () {
            const btn = document.getElementById('topo');
            btn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
    </script>

</body>

</html>