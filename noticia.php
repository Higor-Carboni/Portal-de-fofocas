<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT noticias.*, usuarios.nome AS autor_nome FROM noticias JOIN usuarios ON noticias.autor = usuarios.id WHERE noticias.id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    echo "<p class='msg-erro'>Notícia não encontrada.</p>";
    echo "<div class='form-botoes'><button class='btn-voltar' onclick=\"window.location.href='index.php'\">Voltar</button></div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Fofoquei News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main>
            <div class="container form-box-noticia">
                <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>

                <?php if ($noticia['imagem']): ?>
                    <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem da notícia">
                <?php endif; ?>

                <p><?= nl2br(htmlspecialchars($noticia['noticia'])) ?></p>

                <p><small>Por <strong><?= htmlspecialchars($noticia['autor_nome']) ?></strong> em <?= date("d/m/Y H:i", strtotime($noticia['data'])) ?></small></p>

                <div class="form-botoes">
                    <button class="btn-voltar" type="button" onclick="window.location.href='index.php'">Voltar</button>
                </div>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });" title="Voltar ao topo">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        window.addEventListener('scroll', function () {
            const btn = document.getElementById('topo');
            btn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
    </script>
</body>
</html>