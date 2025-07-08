<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'verificaAdmin.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: anuncios.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM anuncios WHERE id = ?");
$stmt->execute([$id]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>
        <link rel='stylesheet' href='css/style.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Anúncio não encontrado',
                text: 'O anúncio solicitado não foi encontrado.',
                confirmButtonText: 'Voltar'
            }).then(() => {
                window.location.href = 'anuncios.php';
            });
        </script>
    </body>
    </html>";
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $texto = trim($_POST['texto'] ?? '');
    $valorAnuncio = floatval($_POST['valorAnuncio'] ?? 0);
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $imagem = $_FILES['imagem'] ?? null;

    if (empty($nome) || empty($link) || empty($texto)) {
        $mensagem = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($link, FILTER_VALIDATE_URL)) {
        $mensagem = 'URL inválida.';
    } else {
        $imagem_nome = $anuncio['imagem']; // Mantém a imagem atual por padrão

        if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $permitidas)) {
                $imagem_nome = 'img/anuncio_' . uniqid() . '.' . $ext;
                move_uploaded_file($imagem['tmp_name'], $imagem_nome);
            } else {
                $mensagem = 'Formato de imagem não permitido.';
            }
        }

        if (!$mensagem) {
            $stmt = $pdo->prepare("UPDATE anuncios SET nome = ?, imagem = ?, link = ?, texto = ?, ativo = ?, destaque = ?, valorAnuncio = ? WHERE id = ?");
            $stmt->execute([$nome, $imagem_nome, $link, $texto, $ativo, $destaque, $valorAnuncio, $id]);

            echo "<!DOCTYPE html>
            <html lang='pt-br'>
            <head>
                <meta charset='UTF-8'>
                <title>Sucesso</title>
                <link rel='stylesheet' href='css/style.css'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Anúncio Atualizado!',
                        text: 'O anúncio foi atualizado com sucesso.',
                        confirmButtonText: 'Ver Anúncios'
                    }).then(() => {
                        window.location.href = 'anuncios.php';
                    });
                </script>
            </body>
            </html>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Anúncio</title>
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
                <form method="POST" enctype="multipart/form-data" class="form-box">
                    <h2 class="titulo-pagina">✏️ Editar Anúncio</h2>
                    
                    <?php if (!empty($mensagem)): ?>
                        <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
                    <?php endif; ?>

                    <label for="nome">Nome da Empresa/Anunciante*</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($anuncio['nome']) ?>" required>

                    <label for="link">URL de Destino*</label>
                    <input type="url" id="link" name="link" value="<?= htmlspecialchars($anuncio['link']) ?>" required>

                    <label for="texto">Slogan/Mensagem*</label>
                    <input type="text" id="texto" name="texto" value="<?= htmlspecialchars($anuncio['texto']) ?>" maxlength="255" required>

                    <label for="valorAnuncio">Valor do Anúncio (R$)</label>
                    <input type="number" id="valorAnuncio" name="valorAnuncio" value="<?= htmlspecialchars($anuncio['valorAnuncio']) ?>" step="0.01" min="0">

                    <label for="imagem" class="custom-file-upload">
                        <i class="fa fa-cloud-upload"></i> Escolher Nova Imagem (opcional)
                    </label>
                    <input type="file" id="imagem" name="imagem" style="display: none;">
                    <span id="file-chosen"><?= basename($anuncio['imagem']) ?></span>
                    
                    <div class="imagem-atual">
                        <label>Imagem Atual:</label>
                        <img src="<?= htmlspecialchars($anuncio['imagem']) ?>" 
                             alt="<?= htmlspecialchars($anuncio['nome']) ?>" 
                             style="max-width: 200px; max-height: 100px; object-fit: cover; border-radius: 4px; margin-top: 10px;">
                    </div>

                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="ativo" <?= $anuncio['ativo'] ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Anúncio Ativo
                        </label>

                        <label class="checkbox-label">
                            <input type="checkbox" name="destaque" <?= $anuncio['destaque'] ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Anúncio em Destaque
                        </label>
                    </div>

                    <div class="form-botoes">
                        <button type="submit">Atualizar Anúncio</button>
                        <button type="button" onclick="window.location.href='anuncios.php'">Voltar</button>
                    </div>
                </form>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <script>
        document.getElementById('imagem').addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : '<?= basename($anuncio['imagem']) ?>';
            document.getElementById('file-chosen').textContent = fileName;
        });
    </script>
</body>

</html> 