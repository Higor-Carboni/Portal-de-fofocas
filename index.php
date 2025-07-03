<?php
require_once 'conexao.php';
require_once 'funcoes.php';
session_start();

// Buscar categorias
$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$categoria_id = $_GET['categoria'] ?? '';
$pesquisa = trim($_GET['pesquisa'] ?? '');
$params = [];
$sql = "SELECT noticias.*, usuarios.nome AS autor_nome, categorias.nome AS categoria_nome
        FROM noticias 
        JOIN usuarios ON noticias.autor = usuarios.id
        JOIN categorias ON noticias.categoria_id = categorias.id";
$where = [];
if ($categoria_id && ctype_digit($categoria_id)) {
    $where[] = "noticias.categoria_id = ?";
    $params[] = $categoria_id;
}
if ($pesquisa !== '') {
    $where[] = "noticias.titulo LIKE ?";
    $params[] = '%' . $pesquisa . '%';
}
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY data DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

// Buscar anúncio em destaque ou o mais recente ativo
$anuncio_destaque = $pdo->query("SELECT * FROM anuncio WHERE ativo = 1 ORDER BY destaque DESC, id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Fofoquei News</title>
    <!-- Ordem corrigida -->
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="conteudo-page">

    <?php include 'includes/header.php'; ?>

    <main class="conteudo">

        <?php if ($anuncio_destaque): ?>
        <div class="anuncio destaque-fixo" style="margin: 0 auto 24px auto; max-width: 380px;">
            <a href="<?= htmlspecialchars($anuncio_destaque['link']) ?>" target="_blank" rel="noopener">
                <img src="<?= htmlspecialchars($anuncio_destaque['imagem']) ?>" alt="<?= htmlspecialchars($anuncio_destaque['nome']) ?>" style="max-width:220px;max-height:80px;display:block;margin:auto;">
                <div class="anuncio-texto"><?= htmlspecialchars($anuncio_destaque['texto']) ?></div>
            </a>
        </div>
        <?php endif; ?>

        <div class="filtros-categorias">
            <button onclick="window.location.href='index.php'">Todas</button>
            <?php foreach ($categorias as $cat): ?>
                <button onclick="window.location.href='?categoria=<?= $cat['id'] ?>'">
                    <?= htmlspecialchars($cat['nome']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="pesquisa-centralizada">
            <input type="text" id="pesquisa-titulo" name="pesquisa" placeholder="Pesquisar por título..." value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
        </div>

        <?php if ($noticias): ?>
            <div class="grade-cards">
                <?php foreach ($noticias as $n): ?>
                    <a href="noticia.php?id=<?= $n['id'] ?>" class="card-noticia">
                        <?php if ($n['imagem']): ?>
                            <img src="<?= htmlspecialchars($n['imagem']) ?>" alt="Imagem da notícia">
                        <?php endif; ?>
                        <div class="texto">
                            <h2><?= htmlspecialchars($n['titulo']) ?></h2>
                            <p><?= resumo($n['noticia'], 150) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="sem-noticia">😢 Nenhuma fofoca publicada ainda.</p>
        <?php endif; ?>

        <div id="anuncios"></div>

    </main>

</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('pesquisa-titulo');
    const cards = document.querySelectorAll('.card-noticia');

    input.addEventListener('input', function() {
        const termo = input.value.trim().toLowerCase();
        cards.forEach(card => {
            const titulo = card.querySelector('h2').textContent.toLowerCase();
            if (titulo.includes(termo)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

function renderAnuncios(anuncios) {
    const container = document.getElementById('anuncios');
    if (!container) return;
    container.innerHTML = '';
    anuncios.forEach(anuncio => {
        if (!anuncio.ativo) return;
        const destaque = anuncio.destaque ? 'anuncio-destaque' : '';
        container.innerHTML += `
            <div class="anuncio ${destaque}">
                <a href="${anuncio.link}" target="_blank" rel="noopener">
                    <img src="${anuncio.imagem}" alt="${anuncio.nome}" style="max-width:220px;max-height:80px;display:block;margin:auto;">
                    <div class="anuncio-texto">${anuncio.texto}</div>
                </a>
            </div>
        `;
    });
}
fetch('anuncios.json.php')
    .then(r => r.json())
    .then(anuncios => {
        const ativos = anuncios.filter(a => a.ativo);
        if (ativos.length === 0) return;
        const sorteado = ativos[Math.floor(Math.random() * ativos.length)];
        setTimeout(() => {
            const modal = document.createElement('div');
            modal.style.position = 'fixed';
            modal.style.top = '0'; modal.style.left = '0';
            modal.style.width = '100vw'; modal.style.height = '100vh';
            modal.style.background = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex'; modal.style.alignItems = 'center'; modal.style.justifyContent = 'center';
            modal.innerHTML = `
                <div style="background:#fff;padding:24px 32px;border-radius:12px;max-width:350px;text-align:center;position:relative;">
                    <button style="position:absolute;top:8px;right:12px;font-size:18px;background:none;border:none;cursor:pointer;" onclick="this.closest('div').parentNode.remove()">×</button>
                    <a href="${sorteado.link}" target="_blank" rel="noopener">
                        <img src="${sorteado.imagem}" alt="${sorteado.nome}" style="max-width:220px;max-height:80px;display:block;margin:auto;">
                        <div class="anuncio-texto">${sorteado.texto}</div>
                    </a>
                </div>
            `;
            document.body.appendChild(modal);
        }, 2000);
    });
</script>
</body>
</html>