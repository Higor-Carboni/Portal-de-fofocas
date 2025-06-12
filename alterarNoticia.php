<?php
require 'verifica_login.php';
require 'conexao.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$noticia = $stmt->fetch();

if (!$noticia) die('Acesso negado');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $noticia_txt = $_POST['noticia'] ?? '';
    $imagem = $_POST['imagem'] ?? '';
    $categoria_id = $_POST['categoria'] ?? '';

    if ($titulo && $noticia_txt && $categoria_id) {
        $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, noticia=?, imagem=?, categoria_id=? WHERE id=?");
        $stmt->execute([$titulo, $noticia_txt, $imagem, $categoria_id, $id]);
        $msg = "Notícia atualizada!";
        // Atualiza os dados da notícia para exibir corretamente após alteração
        $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
        $stmt->execute([$id, $_SESSION['usuario_id']]);
        $noticia = $stmt->fetch();
    } else {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}

// Buscar categorias do banco
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alterar Notícia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="dashboard.php" class="btn-voltar">← Voltar</a>
<div class="container">
    <h2>Alterar Notícia</h2>
    <form method="POST" class="form-box">
        <input type="text" name="titulo" value="<?= htmlspecialchars($noticia['titulo']) ?>" required>
        <textarea name="noticia" required><?= htmlspecialchars($noticia['noticia']) ?></textarea>
        <input type="text" name="imagem" value="<?= htmlspecialchars($noticia['imagem']) ?>" placeholder="URL da imagem">
        
        <select name="categoria" required>
            <option value="">Selecione a categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id'] ?>" <?= $categoria['id'] == $noticia['categoria_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Alterar</button>
    </form>
    <p class="msg-erro"><?= $msg ?></p>
</div>
</body>
</html>
