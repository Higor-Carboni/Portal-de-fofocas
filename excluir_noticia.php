<?php
require 'verifica_login.php';
require 'conexao.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$noticia = $stmt->fetch();
if (!$noticia) die('Acesso negado.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Excluir Notícia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="dashboard.php" class="btn-voltar">← Voltar</a>
<div class="container">
    <h2>Excluir Notícia</h2>
    <p>Tem certeza que deseja excluir a notícia <strong><?= htmlspecialchars($noticia['titulo']) ?></strong>?</p>
    <form method="POST" class="form-box">
        <button type="submit" style="background:#c00;">Sim, excluir</button>
        <a href="dashboard.php" class="btn-voltar" style="width:auto;">Cancelar</a>
    </form>
</div>
</body>
</html>