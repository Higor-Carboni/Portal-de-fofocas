<?php
require_once 'conexao.php';
require_once 'funcoes.php';
session_start();

// Buscar categorias do banco
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
?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Portal de Notícias</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:800px;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <h1>📰 Portal de Notícias</h1>
        <div>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <strong>Bem-vindo, <?= $_SESSION['usuario_nome'] ?>!</strong>
                | <a href="cadastroNoticia.php">Nova Notícia</a>
                | <a href="dashboard.php">Dashboard</a>
                | <a href="logout.php">Sair</a>
                <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
                    | <a href="usuarios.php" class="nav-link">Gerenciar Usuários</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="cadastroUsuario.php">Cadastrar</a>
                | <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Botões de filtro de categorias -->
    <div class="filtros-categorias" style="display:flex;gap:16px;justify-content:center;margin:24px 0 12px 0;">
        <button class="btn-filtro" onclick="window.location.href='index.php'">Todas</button>
        <?php foreach ($categorias as $cat): ?>
            <button class="btn-filtro" onclick="window.location.href='?categoria=<?= $cat['id'] ?>'"><?= htmlspecialchars($cat['nome']) ?></button>
        <?php endforeach; ?>
    </div>
    <hr>
    <?php if ($noticias): ?>
        <?php foreach ($noticias as $n): ?>
            <div style="border-bottom: 1px solid #eee; padding: 18px 0; display:flex;align-items:center;gap:24px;">
                <div style="flex:1;">
                    <h2><?= htmlspecialchars($n['titulo']) ?></h2>
                    <p><?= resumo($n['noticia'], 150) ?></p>
                    <p>
                        <small>
                            Por <strong><?= htmlspecialchars($n['autor_nome']) ?></strong>
                            em <?= date("d/m/Y H:i", strtotime($n['data'])) ?>
                            | Categoria: <?= htmlspecialchars($n['categoria_nome']) ?>
                        </small>
                    </p>
                    <a href="noticia.php?id=<?= $n['id'] ?>" class="btn-voltar" style="width:auto;display:inline-block;margin:0;">Ler mais</a>
                </div>
                <?php if ($n['imagem']): ?>
                    <img src="<?= htmlspecialchars($n['imagem']) ?>" alt="Imagem" style="max-width:120px;max-height:80px;">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center;">Sem notícias cadastradas ainda.</p>
    <?php endif; ?>
</div>
<footer style="margin-top:40px;text-align:center;">
    <hr>
    <div>
        <a href="#"><img src="icone-instagram.png" width="24" alt="Instagram"></a>
        <a href="#"><img src="icone-facebook.png" width="24" alt="Facebook"></a>
        <a href="#"><img src="icone-twitter.png" width="24" alt="Twitter"></a>
    </div>
    <small>Todos os direitos reservados</small>
</footer>
</body>
</html>