<?php
require_once 'conexao.php';
require_once 'funcoes.php';
session_start();

// Buscar categorias
$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$categoria_id = $_GET['categoria'] ?? '';
$params = [];
$sql = "SELECT noticias.*, usuarios.nome AS autor_nome, categorias.nome AS categoria_nome
        FROM noticias 
        JOIN usuarios ON noticias.autor = usuarios.id
        JOIN categorias ON noticias.categoria_id = categorias.id";
if ($categoria_id && ctype_digit($categoria_id)) {
    $sql .= " WHERE noticias.categoria_id = ?";
    $params[] = $categoria_id;
}
$sql .= " ORDER BY data DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Fofoquei News</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="conteudo-page">

        <?php include 'includes/header.php'; ?>

        <!-- TOPO -->
        <header class="topo">
            <div class="cabecalho-container">
                <a href="index.php"><img src="img/logoFofoca500.png" alt="Logo" class="logo"></a>

               

                <button class="menu-hamburguer" id="btnMenu" aria-label="Abrir menu"
                    onclick="abrirMenuMobile()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </header>

        <!-- CATEGORIAS -->
        <section class="filtros-categorias">
            <button onclick="window.location.href='index.php'">Todas</button>
            <?php foreach ($categorias as $cat): ?>
                <button onclick="window.location.href='?categoria=<?= $cat['id'] ?>'">
                    <?= htmlspecialchars($cat['nome']) ?>
                </button>
            <?php endforeach; ?>
        </section>

        <!-- NOTÍCIAS -->
        <main class="conteudo">
            <?php if ($noticias): ?>
                <div class="grade-cards">
                    <?php foreach ($noticias as $n): ?>
                        <a href="noticia.php?id=<?= $n['id'] ?>" class="link-noticia">
                            <article class="card-noticia">
                                <?php if ($n['imagem']): ?>
                                    <img src="<?= htmlspecialchars($n['imagem']) ?>" alt="Imagem da notícia">
                                <?php endif; ?>
                                <div class="texto">
                                    <h2><?= htmlspecialchars($n['titulo']) ?></h2>
                                    <p><?= resumo($n['noticia'], 150) ?></p>
                                </div>
                            </article>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="sem-noticia">😢 Nenhuma fofoca publicada ainda.</p>
            <?php endif; ?>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- MENU MOBILE JS -->
    <script>
        function abrirMenuMobile() {
            document.getElementById('menuMobile').style.display = 'flex';
            document.getElementById('menuOverlay').style.display = 'block';
        }
        function fecharMenuMobile() {
            document.getElementById('menuMobile').style.display = 'none';
            document.getElementById('menuOverlay').style.display = 'none';
        }

        document.querySelectorAll('.menu-mobile-list a').forEach(function (link) {
            link.addEventListener('click', fecharMenuMobile);
        });
    </script>
</body>

</html>