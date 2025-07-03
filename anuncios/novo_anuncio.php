<?php
require '../verifica_login.php';
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO anuncio (nome, imagem, link, texto, ativo, destaque, valorAnuncio) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nome'],
        $_POST['imagem'],
        $_POST['link'],
        $_POST['texto'],
        isset($_POST['ativo']) ? 1 : 0,
        isset($_POST['destaque']) ? 1 : 0,
        str_replace(',', '.', $_POST['valorAnuncio'])
    ]);
    header('Location: anuncio.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Anunciante</title>
    <!-- CSS carregado no header específico -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-anunciante {
            max-width: 720px;
            margin: 30px auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }

        .form-anunciante h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            color: #232b3f;
        }

        .form-check-label {
            margin-left: 6px;
        }

        .btn-salvar {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-salvar:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="conteudo-page">
    <?php include 'header_anuncios.php'; ?>

    <main class="conteudo">
        <form method="post" class="form-anunciante">
            <h1><i class="fas fa-plus"></i> Novo Anúncio</h1>

            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagem (URL):</label>
                <input type="text" name="imagem" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Link:</label>
                <input type="text" name="link" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Texto:</label>
                <input type="text" name="texto" class="form-control">
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ativo" id="ativo" checked>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="destaque" id="destaque">
                        <label class="form-check-label" for="destaque">Destaque</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Valor:</label>
                    <input type="text" name="valorAnuncio" value="0,00" class="form-control">
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn-salvar"><i class="fas fa-save"></i> Salvar</button>
                <a href="anuncio.php" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </main>

    <?php include 'footer_anuncios.php'; ?>
</div>

<!-- Scripts carregados no footer específico -->
</body>
</html>
