<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'verificaAdmin.php';
// Excluir usuário
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    if ($idExcluir != $_SESSION['usuario_id']) { // Não deixa excluir a si mesmo
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$idExcluir]);
    }
    header("Location: usuarios.php");
    exit;
}

// Buscar todos os usuários
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="dashboard.php" class="btn-voltar">← Voltar</a>
<div class="container" style="max-width:700px;">
    <h2>Usuários Cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn-voltar" style="width:auto;">✏️ Editar</a>
                <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                    <a href="usuarios.php?excluir=<?= $u['id'] ?>" class="btn-voltar" style="width:auto;background:#c00;" onclick="return confirm('Excluir este usuário?')">❌ Excluir</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>