<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$titulo = '';
$noticia = '';
$categoria_id = '';
$imagem = '';

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $noticia = trim($_POST['noticia'] ?? '');
    $categoria_id = $_POST['categoria_id'] ?? '';
    $imagem = $_FILES['imagem'] ?? null;

    if (empty($titulo) || empty($noticia) || empty($categoria_id)) {
        $mensagem = 'Preencha todos os campos obrigatórios.';
    } else {
        $imagem_nome = null;
        if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $permitidas)) {
                // Garante que o nome não venha com duplas extensões
                $nomeOriginal = pathinfo($imagem['name'], PATHINFO_FILENAME);
                $imagem_nome = 'img/' . uniqid('img_') . '.' . $ext;

                move_uploaded_file($imagem['tmp_name'], $imagem_nome);
            } else {
                $mensagem = 'Formato de imagem não permitido.';
            }
        }

        if (!$mensagem) {
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, noticia, categoria_id, autor, imagem, data) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $titulo,
                $noticia,
                $categoria_id,
                $_SESSION['usuario_id'],
                $imagem_nome
            ]);
            // Após salvar a notícia, envia solicitação de revisão ao admin
            $noticia_id = $pdo->lastInsertId();
            $solicitacao = $pdo->prepare("INSERT INTO solicitacoes (usuario_id, noticia_id, tipo, status, data_solicitacao) VALUES (?, ?, 'nova', 'pendente', NOW())");
            $solicitacao->execute([$_SESSION['usuario_id'], $noticia_id]);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Notícia</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main>
            <form method="post" enctype="multipart/form-data" class="form-box-noticia">
                <h2>Cadastrar Notícia</h2>
                <?php if (!empty($mensagem)): ?>
                    <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
                <?php endif; ?>

                <label for="titulo">Título*</label>
                <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>" required>

                <label for="noticia">Texto*</label>
                <textarea name="noticia" id="noticia" rows="6" required><?= htmlspecialchars($noticia) ?></textarea>

                <label for="categoria_id">Categoria*</label>
                <select name="categoria_id" id="categoria_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoria_id == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="imagem" class="custom-file-upload">
                    <i class="fa fa-cloud-upload"></i> Escolher imagem
                </label>
                <input type="file" id="imagem" name="imagem" style="display: none;">
                <span id="file-chosen">Nenhum arquivo selecionado</span>

                <div class="form-botoes">
                    <button type="submit">Salvar</button>
                    <button type="button" onclick="window.location.replace('index.php')">Voltar</button>
                </div>
            </form>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>

    <script>
        document.getElementById('imagem').addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : 'Nenhum arquivo selecionado';
            document.getElementById('file-chosen').textContent = fileName;
        });
    </script>
</body>

</html>