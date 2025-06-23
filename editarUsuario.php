<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'verificaAdmin.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0 || $id == $_SESSION['usuario_id']) {
    header("Location: usuarios.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "<p class='msg-erro'>Usuário não encontrado.</p>";
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $perfil = $_POST['perfil'] ?? 'comum';

    if ($nome && $email && in_array($perfil, ['admin', 'comum', 'normal'])) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, perfil = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $perfil, $id]);

        $msg = "Usuário atualizado com sucesso!";
        $usuario['nome'] = $nome;
        $usuario['email'] = $email;
        $usuario['perfil'] = $perfil;
    } else {
        $msg = "Preencha todos os campos corretamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/usuarios.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <div class="container">
                <form method="POST" class="form-box">
                    <h2 class="titulo-pagina">✏️ Editar Usuário</h2>

                    <?php if ($msg): ?>
                        <p class="<?= strpos($msg, 'sucesso') !== false ? 'msg-sucesso' : 'msg-erro' ?>">
                            <?= htmlspecialchars($msg) ?>
                        </p>
                    <?php endif; ?>

                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

                    <label for="perfil">Perfil</label>
                    <select name="perfil" id="perfil" required>
                        <option value="comum" <?= $usuario['perfil'] === 'comum' ? 'selected' : '' ?>>Comum</option>
                        <option value="admin" <?= $usuario['perfil'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>

                    <div class="form-botoes">
                        <button type="submit">Salvar</button>
                        <button type="button" onclick="window.location.href='usuarios.php'">Voltar</button>
                    </div>
                </form>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>