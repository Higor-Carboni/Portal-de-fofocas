<?php
require 'verifica_login.php';
require 'conexao.php';

// Buscar categorias do banco
$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $noticia = $_POST['noticia'] ?? '';
    $categoria_id = $_POST['categoria'] ?? '';
    $imagem = '';

    // Cria a pasta uploads se não existir
    $pastaUploads = __DIR__ . '/uploads';
    if (!is_dir($pastaUploads)) {
        mkdir($pastaUploads, 0777, true);
    }

    // Upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $permitidas)) {
            $nome_arquivo = uniqid('img_') . '.' . $ext;
            $destino = $pastaUploads . '/' . $nome_arquivo;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $imagem = 'uploads/' . $nome_arquivo;
            } else {
                $msg = "Erro ao salvar a imagem.";
            }
        } else {
            $msg = "Formato de imagem não permitido.";
        }
    }

    if ($titulo && $noticia && $categoria_id && !$msg) {
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, noticia, imagem, categoria_id, autor) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $noticia, $imagem, $categoria_id, $_SESSION['usuario_id']]);
        header("Location: index.php");
        exit;
    } elseif (!$msg) {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Notícia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="dashboard.php" class="btn-voltar">← Voltar</a>
<div class="container">
    <h2>Cadastrar Notícia</h2>
    <form method="POST" class="form-box" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="noticia" placeholder="Notícia" required></textarea>
        <select name="categoria" required>
            <option value="">Selecione a categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="imagem" accept="image/*" required>
        <button type="submit">Salvar</button>
    </form>
    <p class="msg-erro"><?= $msg ?></p>
</div>
</body>
</html>