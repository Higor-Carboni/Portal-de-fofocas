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
</head>

<body>
    <header class="topo">
        <img src="img/logoFofoca500.png" alt="Logo" class="logo">
        <div class="menu-superior">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <span>👤 <?= $_SESSION['usuario_nome'] ?></span>
                <a href="cadastroNoticia.php">+ Nova</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Sair</a>
                <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
                    <a href="usuarios.php">Usuários</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="cadastroUsuario.php">Cadastrar</a>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="filtros-categorias">
        <button onclick="window.location.href='index.php'">Todas</button>
        <?php foreach ($categorias as $cat): ?>
            <button onclick="window.location.href='?categoria=<?= $cat['id'] ?>'"><?= htmlspecialchars($cat['nome']) ?></button>
        <?php endforeach; ?>
    </div>

    <main class="conteudo">
        <?php if ($noticias): ?>
            <?php foreach ($noticias as $n): ?>
                <article class="card-noticia">
                    <?php if ($n['imagem']): ?>
                        <img src="<?= htmlspecialchars($n['imagem']) ?>" alt="Imagem da notícia">
                    <?php endif; ?>
                    <div class="texto">
                        <h2><?= htmlspecialchars($n['titulo']) ?></h2>
                        <p><?= resumo($n['noticia'], 150) ?></p>
                        <small>
                            Por <strong><?= htmlspecialchars($n['autor_nome']) ?></strong> em
                            <?= date("d/m/Y H:i", strtotime($n['data'])) ?> |
                            Categoria: <?= htmlspecialchars($n['categoria_nome']) ?>
                        </small>
                        <a href="noticia.php?id=<?= $n['id'] ?>" class="ler-mais">Ler mais</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="sem-noticia">😢 Nenhuma fofoca publicada ainda.</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="redes">
            <a href="#"><img src="icone-instagram.png" alt="Instagram"></a>
            <a href="#"><img src="icone-facebook.png" alt="Facebook"></a>
            <a href="#"><img src="icone-twitter.png" alt="Twitter"></a>
        </div>
        <small>© Fofocas Brasil — Todos os direitos reservados</small>
    </footer>
</body>

</html>