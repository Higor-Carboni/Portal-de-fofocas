<?php
require 'verifica_login.php';
require 'conexao.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $noticia = $_POST['noticia'] ?? '';
    $imagem = $_POST['imagem'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    if ($titulo && $noticia && $categoria) {
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, noticia, imagem, categoria, autor) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $noticia, $imagem, $categoria, $_SESSION['usuario_id']]);
        $msg = "Notícia salva!";
    } else {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<form method="POST">
    <input type="text" name="titulo" placeholder="Título" required><br>
    <textarea name="noticia" placeholder="Notícia" required></textarea><br>
    <input type="text" name="imagem" placeholder="URL da imagem"><br>
    <select name="categoria" required>
        <option value="">Selecione a categoria</option>
        <option value="Famosos">Famosos</option>
        <option value="TV">TV</option>
        <option value="Politica">Política</option>
        <option value="Curiosidades">Curiosidades</option>
    </select><br>
    <button type="submit">Salvar</button>
</form>
<p><?= $msg ?></p>