<?php
require '../verifica_login.php';
require '../conexao.php';
$id = $_GET['id'] ?? '';
if (!$id) { header('Location: anuncio.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM anuncio WHERE id = ?");
$stmt->execute([$id]);
$anunciante = $stmt->fetch();
if (!$anunciante) { header('Location: anuncio.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE anuncio SET nome=?, imagem=?, link=?, texto=?, ativo=?, destaque=?, valorAnuncio=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nome'],
        $_POST['imagem'],
        $_POST['link'],
        $_POST['texto'],
        isset($_POST['ativo']) ? 1 : 0,
        isset($_POST['destaque']) ? 1 : 0,
        str_replace(',', '.', $_POST['valorAnuncio']),
        $id
    ]);
    header('Location: anuncio.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Anunciante</title>
    <!-- CSS carregado no header específico -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-box-noticia {
            max-width: 720px;
            margin: 30px auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }

        .titulo-dashboard {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            color: #232b3f;
        }

        .btn-cancelar {
            background-color: #6c757d;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.3s;
            display: inline-block;
            margin-left: 10px;
        }

        .btn-cancelar:hover {
            background-color: #5a6268;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="conteudo-page">
    <?php include 'header_anuncios.php'; ?>
    <main class="conteudo">
        <h2 class="titulo-dashboard" style="margin-bottom: 18px;">
            <i class="fas fa-bullhorn"></i> Editar Anunciante
        </h2>
        <form method="post" class="form-box-noticia">
            <label>Nome:
                <input type="text" name="nome" value="<?= htmlspecialchars($anunciante['nome']) ?>" required>
            </label>
            <label>Imagem (URL):
                <input type="text" name="imagem" value="<?= htmlspecialchars($anunciante['imagem']) ?>">
            </label>
            <label>Link:
                <input type="text" name="link" value="<?= htmlspecialchars($anunciante['link']) ?>">
            </label>
            <label>Texto:
                <input type="text" name="texto" value="<?= htmlspecialchars($anunciante['texto']) ?>">
            </label>
            <div style="display: flex; gap: 18px;">
                <label style="display: flex; align-items: center; gap: 6px;">
                    <input type="checkbox" name="ativo" <?= $anunciante['ativo'] ? 'checked' : '' ?>> Ativo
                </label>
                <label style="display: flex; align-items: center; gap: 6px;">
                    <input type="checkbox" name="destaque" <?= $anunciante['destaque'] ? 'checked' : '' ?>> Destaque
                </label>
            </div>
            <label>Valor:
                <input type="text" name="valorAnuncio" value="<?= number_format($anunciante['valorAnuncio'],2,',','.') ?>">
            </label>
            <div class="form-botoes">
                <button type="submit"><i class="fas fa-save"></i> Salvar</button>
                <a href="anuncio.php" class="btn-cancelar">Cancelar</a>
            </div>
        </form>
    </main>
    <?php include 'footer_anuncios.php'; ?>
</div>
</body>
</html>