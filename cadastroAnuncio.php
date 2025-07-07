<?php
require_once 'verifica_login.php';
require_once 'verificaAdmin.php';
require_once 'conexao.php';

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
        $imagem_nome = null;
        if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $permitidas)) {
                $imagem_nome = 'img/anuncio_' . uniqid() . '.' . $ext;
                move_uploaded_file($imagem['tmp_name'], $imagem_nome);
            } else {
                $mensagem = 'Formato de imagem não permitido.';
            }
        } else {
            $mensagem = 'Selecione uma imagem para o anúncio.';
        }

        if (!$mensagem) {
            $stmt = $pdo->prepare("INSERT INTO anuncios (nome, imagem, link, texto, ativo, destaque, valorAnuncio) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $imagem_nome, $link, $texto, $ativo, $destaque, $valorAnuncio]);

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
                        title: 'Anúncio Cadastrado!',
                        text: 'O anúncio foi cadastrado com sucesso.',
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
    <title>Cadastrar Anúncio</title>
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
                    <h2 class="titulo-pagina">📢 Cadastrar Anúncio</h2>
                    
                    <?php if (!empty($mensagem)): ?>
                        <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
                    <?php endif; ?>

                    <label for="nome">Nome da Empresa/Anunciante*</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>

                    <label for="link">URL de Destino*</label>
                    <input type="url" id="link" name="link" value="<?= htmlspecialchars($_POST['link'] ?? '') ?>" 
                           placeholder="https://www.exemplo.com" required>

                    <label for="texto">Slogan/Mensagem*</label>
                    <input type="text" id="texto" name="texto" value="<?= htmlspecialchars($_POST['texto'] ?? '') ?>" 
                           maxlength="255" required>

                    <label for="valorAnuncio">Valor do Anúncio (R$)</label>
                    <input type="number" id="valorAnuncio" name="valorAnuncio" 
                           value="<?= htmlspecialchars($_POST['valorAnuncio'] ?? '0.00') ?>" 
                           step="0.01" min="0">

                    <label for="imagem" class="custom-file-upload">
                        <i class="fa fa-cloud-upload"></i> Escolher Imagem do Anúncio*
                    </label>
                    <input type="file" id="imagem" name="imagem" style="display: none;" required>
                    <span id="file-chosen">Nenhum arquivo selecionado</span>

                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="ativo" <?= isset($_POST['ativo']) ? 'checked' : 'checked' ?>>
                            <span class="checkmark"></span>
                            Anúncio Ativo
                        </label>

                        <label class="checkbox-label">
                            <input type="checkbox" name="destaque" <?= isset($_POST['destaque']) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Anúncio em Destaque
                        </label>
                    </div>

                    <div class="form-botoes">
                        <button type="submit">Cadastrar Anúncio</button>
                        <button type="button" onclick="window.location.href='anuncios.php'">Voltar</button>
                    </div>
                </form>
            </div>
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