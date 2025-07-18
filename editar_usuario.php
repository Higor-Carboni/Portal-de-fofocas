<?php
require 'verifica_login.php';
require 'conexao.php';

$id = $_GET['id'] ?? $_SESSION['usuario_id'];
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
        $msg = "Usuário atualizado!";
        // Atualiza sessão se for o próprio usuário
        if ($id == $_SESSION['usuario_id']) {
            $_SESSION['usuario_nome'] = $nome;
        }
    } else {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="usuarios.php" class="btn-voltar">← Voltar</a>
<div class="container">
    <h2>Editar Usuário</h2>
    <form method="POST" class="form-box">
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        <button type="submit">Salvar</button>
    </form>
    <p class="msg-erro"><?= $msg ?></p>
</div>
</body>
</html>