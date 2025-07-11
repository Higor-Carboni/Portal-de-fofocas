<?php
require 'verifica_login.php';
require 'conexao.php';
$id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    if ($nome && $email) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
        $stmt->execute([$nome, $email, $id]);
        $msg = "Perfil atualizado!";
        // Atualiza sessão
        $_SESSION['usuario_nome'] = $nome;
    } else {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Alterar Perfil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <div class="container">
        <form method="POST" class="form-box">
            <h2>Alterar Perfil</h2>
            <?php if ($msg): ?>
                <p class="<?= strpos($msg, 'atualizad') !== false ? 'msg-sucesso' : 'msg-erro' ?>">
                    <?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            <div class="form-botoes">
                <button type="submit">Salvar</button>
                <button type="button" onclick="window.location.replace('index.php')">Voltar</button>
            </div>
        </form>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>