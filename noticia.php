<?php
require_once 'conexao.php';
require_once 'funcoes.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT noticias.*, usuarios.nome AS autor_nome FROM noticias JOIN usuarios ON noticias.autor = usuarios.id WHERE noticias.id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();
if (!$noticia) die('Notícia não encontrada.');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Portal de Notícias</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="index.php" class="btn-voltar">← Voltar</a>
<div class="container" style="max-width:700px;">
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
    <?php if ($noticia['imagem']): ?>
        <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem" style="max-width:400px;display:block;margin:0 auto 20px auto;">
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($noticia['noticia'])) ?></p>
    <p><small>Por <strong><?= htmlspecialchars($noticia['autor_nome']) ?></strong> em <?= date("d/m/Y H:i", strtotime($noticia['data'])) ?></small></p>
</div>
</body>
</html>