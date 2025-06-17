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
    <title>Fofoquinha News 💬</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <header class="topo">
        <div class="cabecalho-container">
            <img src="img/logoFofoca500.png" alt="Logo" class="logo">
            <div class="menu-superior">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span>👤 <?= $_SESSION['usuario_nome'] ?></span>
                    <a href="cadastroNoticia.php">+ Nova</a>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="cadastroUsuario.php">Cadastrar</a>
                    <a href="logout.php">Sair</a>
                    <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
                        <a href="usuarios.php">Usuários</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="filtros-categorias">
        <button onclick="window.location.href='index.php'">Todas</button>
        <?php foreach ($categorias as $cat): ?>
            <button
                onclick="window.location.href='?categoria=<?= $cat['id'] ?>'"><?= htmlspecialchars($cat['nome']) ?></button>
        <?php endforeach; ?>
    </div>

    <main class="conteudo">
        <?php if ($noticias): ?>
            <div class="grade-cards">
                <?php foreach ($noticias as $n): ?>
                    <article class="card-noticia">
                        <?php if ($n['imagem']): ?>
                            <img src="<?= htmlspecialchars($n['imagem']) ?>" alt="Imagem da notícia">
                        <?php endif; ?>
                        <div class="texto">
                            <h2><?= htmlspecialchars($n['titulo']) ?></h2>
                            <p><?= resumo($n['noticia'], 150) ?></p>
                            <!-- Removido o <small> com autor, data e categoria -->
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="sem-noticia">😢 Nenhuma fofoca publicada ainda.</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="redes">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
        <small>© Fofocas Brasil — Todos os direitos reservados</small>
    </footer>

    <button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        window.addEventListener('scroll', function () {
            const btn = document.getElementById('topo');
            if (window.scrollY > 300) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        });
    </script>

</body>

</html>