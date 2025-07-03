<?php
require_once 'conexao.php';
require_once 'funcoes.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;

if ($_SESSION['usuario_perfil'] === 'admin') {
    // Admin pode editar qualquer notícia
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
} else {
    // Redator: precisa ser o autor E ter solicitação aprovada
    $stmt = $pdo->prepare("
        SELECT n.* FROM noticias n
        JOIN solicitacoes s ON s.noticia_id = n.id
        WHERE n.id = ? 
          AND n.autor = ? 
          AND s.tipo = 'editar' 
          AND s.status = 'aprovada'
        ORDER BY s.data_solicitacao DESC
        LIMIT 1
    ");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
}

$noticia = $stmt->fetch();

if (!$noticia) {
    echo "<p style='text-align:center; color:red;'>Você não tem permissão para editar esta notícia ou ela ainda não foi aprovada.</p>";
    exit;
}

$mensagem = '';
$titulo = $noticia['titulo'];
$texto = $noticia['noticia'];
$categoria_id = $noticia['categoria_id'] ?? '';
$imagem_atual = $noticia['imagem'];

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $texto = trim($_POST['noticia'] ?? '');
    $categoria_id = $_POST['categoria_id'] ?? '';
    $imagem = $_FILES['imagem'] ?? null;

    if (empty($titulo) || empty($texto) || empty($categoria_id)) {
        $mensagem = 'Pzreencha todos os campos obrigatórios.';
    } else {
        $imagem_nome = $imagem_atual;

        if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($ext, $permitidas)) {
                $imagem_nome = 'img/' . uniqid('img_') . '.' . $ext;
                move_uploaded_file($imagem['tmp_name'], $imagem_nome);
            } else {
                $mensagem = 'Formato de imagem não permitido.';
            }
        }

        if (!$mensagem) {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, noticia = ?, categoria_id = ?, imagem = ? WHERE id = ?");
            $stmt->execute([$titulo, $texto, $categoria_id, $imagem_nome, $id]);

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Notícia</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* =================== ANUNCIO - PADRÃO INDEX =================== */

        .anuncio-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin: 24px 0 10px 0;
          padding: 0 12px;
        }

        .anuncio-header h2 {
          font-size: 2rem;
          color: #232b3f;
          font-weight: 700;
          margin: 0;
          display: flex;
          align-items: center;
          gap: 10px;
        }

        .anuncio-header .btn-cadastrar-noticia {
          background: #b51717;
          color: #fff;
          padding: 10px 16px;
          border-radius: 6px;
          font-weight: 500;
          text-decoration: none;
          font-size: 0.95rem;
          box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
          transition: background 0.2s ease;
          display: flex;
          align-items: center;
          gap: 8px;
        }

        .anuncio-header .btn-cadastrar-noticia:hover {
          background: #3f4557;
        }

        .grade-cards-anuncio {
          display: flex;
          flex-wrap: wrap;
          gap: 32px;
          justify-content: center;
          margin-top: 10px;
        }

        .card-anuncio {
          background: #fff;
          border-radius: 18px;
          box-shadow: 0 6px 24px #0002;
          overflow: hidden;
          width: 340px;
          min-height: 340px;
          display: flex;
          flex-direction: column;
          transition: transform 0.18s, box-shadow 0.18s;
          position: relative;
          align-items: center;
        }

        .card-anuncio:hover {
          transform: translateY(-6px) scale(1.03);
          box-shadow: 0 12px 32px #0003;
        }

        .card-anuncio img {
          width: 100%;
          height: 180px;
          object-fit: contain;
          display: block;
          background: #f7f7f7;
          border-top-left-radius: 18px;
          border-top-right-radius: 18px;
        }

        .card-anuncio .texto {
          width: 100%;
          padding: 18px;
          background: linear-gradient(0deg, #232b3f 92%, #232b3f99 100%, transparent 100%);
          color: #fff;
          border-bottom-left-radius: 18px;
          border-bottom-right-radius: 18px;
          min-height: 120px;
          display: flex;
          flex-direction: column;
          gap: 8px;
        }

        .card-anuncio h3 {
          margin: 0 0 6px 0;
          font-size: 1.18em;
          font-weight: bold;
          color: #fff;
          text-shadow: 0 2px 8px #000a;
        }

        .card-anuncio p {
          margin: 0;
          font-size: 1em;
          color: #e0e0e0;
          text-shadow: 0 2px 8px #000a;
        }

        .card-anuncio .info-extra {
          font-size: 0.98em;
          color: #ffe082;
          margin-bottom: 4px;
        }

        .card-anuncio .acoes {
          display: flex;
          gap: 10px;
          margin-top: 10px;
        }

        .card-anuncio .btn-editar,
        .card-anuncio .btn-excluir {
          padding: 6px 10px;
          border-radius: 6px;
          font-size: 1rem;
          margin: 0 2px;
          display: inline-block;
          text-decoration: none;
          color: #fff;
          background: #3a4666;
          transition: background 0.2s;
        }

        .card-anuncio .btn-editar:hover {
          background: #2979cc;
        }

        .card-anuncio .btn-excluir {
          background: #b51717;
        }

        .card-anuncio .btn-excluir:hover {
          background: #cc2e2e;
        }

        @media (max-width: 1100px) {
          .card-anuncio {
            width: 46vw;
            min-width: 260px;
            max-width: 98vw;
          }
        }

        @media (max-width: 700px) {
          .grade-cards-anuncio {
            flex-direction: column;
            align-items: center;
          }
          .card-anuncio {
            width: 98vw;
            border-radius: 12px;
          }
          .card-anuncio img {
            border-radius: 12px 12px 0 0;
          }
          .card-anuncio .texto {
            border-radius: 0 0 12px 12px;
          }
        }
    </style>
</head>
<body>
<div class="conteudo-page">
    <?php include 'includes/header.php'; ?>

    <main>
        <form method="post" enctype="multipart/form-data" class="form-box-noticia">
            <h2><i class="fas fa-pen"></i> Alterar Notícia</h2>
            <?php if (!empty($mensagem)): ?>
                <p class="msg-erro"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <label for="titulo">Título*</label>
            <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>" required>

            <label for="noticia">Texto*</label>
            <textarea name="noticia" id="noticia" rows="6" required><?= htmlspecialchars($texto) ?></textarea>

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
                <i class="fa fa-cloud-upload"></i> Escolher nova imagem
            </label>
            <input type="file" id="imagem" name="imagem" style="display: none;">
            <span id="file-chosen"><?= $imagem_atual ? basename($imagem_atual) : 'Nenhum arquivo selecionado' ?></span>

            <div class="form-botoes">
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="window.location.href='dashboard.php'">Voltar</button>
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