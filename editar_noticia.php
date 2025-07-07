<?php
require 'verifica_login.php';
require 'conexao.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$noticia = $stmt->fetch();
if (!$noticia) die('Acesso negado');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $noticia_txt = $_POST['noticia'];
    $imagem = $_POST['imagem'];
    $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, noticia=?, imagem=? WHERE id=?");
    $stmt->execute([$titulo, $noticia_txt, $imagem, $id]);
    echo "Notícia atualizada.";
}
?>
<form method="POST">
    <input type="text" name="titulo" value="<?= htmlspecialchars($noticia['titulo']) ?>"><br>
    <textarea name="noticia"><?= htmlspecialchars($noticia['noticia']) ?></textarea><br>
    <input type="text" name="imagem" value="<?= htmlspecialchars($noticia['imagem']) ?>"><br>
    <button type="submit">Alterar</button>
</form>