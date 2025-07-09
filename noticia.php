<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Buscar notícia principal
$stmt = $pdo->prepare("SELECT noticias.*, usuarios.nome AS autor_nome, categorias.nome AS categoria_nome 
                       FROM noticias 
                       JOIN usuarios ON noticias.autor = usuarios.id 
                       LEFT JOIN categorias ON noticias.categoria_id = categorias.id 
                       WHERE noticias.id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    echo "<p class='msg-erro'>Notícia não encontrada.</p>";
    echo "<div class='form-botoes'><button class='btn-voltar' onclick=\"window.location.href='index.php'\">Voltar</button></div>";
    exit;
}

// Buscar outras notícias recentes (exceto a atual)
$stmtOutras = $pdo->prepare("SELECT id, titulo, imagem, categoria_id, data FROM noticias WHERE id != ? ORDER BY data DESC LIMIT 5");
$stmtOutras->execute([$id]);
$outras_noticias = $stmtOutras->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Fofoquei News</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/noticia.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="js/noticia.js" defer></script>
</head>
<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <div class="noticia-main-layout">
            <div class="noticia-container">
                <div class="noticia-header">
                    <?php if ($noticia['categoria_nome']): ?>
                        <div class="noticia-categoria">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($noticia['categoria_nome']) ?>
                        </div>
                    <?php endif; ?>
                    <h1 class="noticia-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h1>
                    <div class="noticia-meta">
                        <div class="noticia-meta-item">
                            <i class="fas fa-user"></i>
                            <span>Por <strong><?= htmlspecialchars($noticia['autor_nome']) ?></strong></span>
                        </div>
                        <div class="noticia-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?= date("d/m/Y", strtotime($noticia['data'])) ?></span>
                        </div>
                        <div class="noticia-meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?= date("H:i", strtotime($noticia['data'])) ?></span>
                        </div>
                    </div>
                </div>
                <?php if ($noticia['imagem']): ?>
                    <div class="noticia-imagem-container">
                        <img src="<?= htmlspecialchars($noticia['imagem']) ?>" 
                             alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                             class="noticia-imagem"
                             loading="lazy">
                    </div>
                <?php endif; ?>
                <div class="noticia-conteudo">
                    <?= nl2br(htmlspecialchars($noticia['noticia'])) ?>
                </div>
                <div class="noticia-acoes">
                    <button class="btn-noticia btn-voltar-noticia" onclick="window.location.replace('index.php')">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao Portal
                    </button>
                    <button class="btn-noticia btn-compartilhar" onclick="compartilharNoticia()">
                        <i class="fas fa-share-alt"></i>
                        Compartilhar
                    </button>
                </div>
                <div class="noticia-tags">
                    <span class="tag">Fofoquei News</span>
                    <span class="tag">Notícias</span>
                    <?php if ($noticia['categoria_nome']): ?>
                        <span class="tag"><?= htmlspecialchars($noticia['categoria_nome']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <aside class="sidebar-outras-noticias">
                <h3>Outras notícias</h3>
                <?php foreach ($outras_noticias as $outra): ?>
                    <a href="noticia.php?id=<?= $outra['id'] ?>" class="card-outra-noticia">
                        <?php if ($outra['imagem']): ?>
                            <img src="<?= htmlspecialchars($outra['imagem']) ?>" alt="<?= htmlspecialchars($outra['titulo']) ?>" class="card-outra-noticia-img">
                        <?php else: ?>
                            <div class="card-outra-noticia-img" style="display:flex;align-items:center;justify-content:center;color:#aaa;font-size:2rem;">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-outra-noticia-info">
                            <div class="card-outra-noticia-titulo" title="<?= htmlspecialchars($outra['titulo']) ?>">
                                <?= htmlspecialchars($outra['titulo']) ?>
                            </div>
                            <div class="card-outra-noticia-data">
                                <?= date('d/m/Y', strtotime($outra['data'])) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </aside>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>