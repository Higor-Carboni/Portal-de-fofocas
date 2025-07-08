<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes.php';

// Ativar modo de erro para debugging
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ativar modo de erro para debugging
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Buscar categorias
$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Buscar anúncios ativos
$anuncios = $pdo->query("SELECT * FROM anuncios WHERE ativo = 1 ORDER BY destaque DESC, data_cadastro DESC")->fetchAll(PDO::FETCH_ASSOC);

// Selecionar anúncio aleatório para pop-up
$anuncios_ativos = $pdo->query("SELECT * FROM anuncios WHERE ativo = 1")->fetchAll(PDO::FETCH_ASSOC);
$anuncio_popup = !empty($anuncios_ativos) ? $anuncios_ativos[array_rand($anuncios_ativos)] : null;

// Filtros
$categoria_id = $_GET['categoria'] ?? '';
$pesquisa = trim($_GET['pesquisa'] ?? '');
$params = [];
$sql = "SELECT noticias.*, usuarios.nome AS autor_nome, categorias.nome AS categoria_nome
        FROM noticias 
        LEFT JOIN usuarios ON noticias.autor = usuarios.id
        LEFT JOIN categorias ON noticias.categoria_id = categorias.id";

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

// Debug opcional
// echo '<pre>'; print_r($noticias); echo '</pre>';

$noticia_destaque = !empty($noticias) ? array_shift($noticias) : null;
$noticias_sidebar = array_slice($noticias, 0, 3);
$noticias_secundarias = array_slice($noticias, 3);

// Montar array de notícias para o grid mosaico
$noticias_mosaico = [];
if (!empty($noticia_destaque)) {
    $noticias_mosaico[] = ['class' => 'noticia-destaque', 'dados' => $noticia_destaque, 'tipo' => 'featured'];
}
if (!empty($noticias)) {
    if (isset($noticias[0])) $noticias_mosaico[] = ['class' => 'noticia-pequena1', 'dados' => $noticias[0], 'tipo' => 'secondary'];
    if (isset($noticias[1])) $noticias_mosaico[] = ['class' => 'noticia-media1', 'dados' => $noticias[1], 'tipo' => 'secondary'];
    if (isset($noticias[2])) $noticias_mosaico[] = ['class' => 'noticia-media2', 'dados' => $noticias[2], 'tipo' => 'secondary'];
    if (isset($noticias[3])) $noticias_mosaico[] = ['class' => 'noticia-media3', 'dados' => $noticias[3], 'tipo' => 'secondary'];
    if (isset($noticias[4])) $noticias_mosaico[] = ['class' => 'noticia-media4', 'dados' => $noticias[4], 'tipo' => 'secondary'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fofoquei News - Portal de Notícias</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/usuarios.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .banner-anuncio {
        display: flex;
        justify-content: center;
        margin: 32px 0 24px 0;
    }
    .banner-anuncio img {
        max-width: 100%;
        max-height: 120px;
        border-radius: 10px;
        box-shadow: 0 4px 24px #0001;
        transition: box-shadow 0.2s;
        background: #fff;
    }
    .banner-anuncio img:hover {
        box-shadow: 0 8px 32px #0002;
    }
    </style>
</head>
<body>

<div class="conteudo-page">
    <?php include 'includes/header.php'; ?>

    <div class="portal-container">
        <!-- Filtros de categoria -->
        <div class="filtros-categorias">
            <button onclick="window.location.href='index.php'" <?= empty($categoria_id) ? 'class="active"' : '' ?>>Todas</button>
            <?php foreach ($categorias as $cat): ?>
                <button onclick="window.location.href='?categoria=<?= $cat['id'] ?>'" <?= $categoria_id == $cat['id'] ? 'class="active"' : '' ?>>
                    <?= htmlspecialchars($cat['nome']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Pesquisa -->
        <div class="pesquisa-centralizada">
            <form method="get" action="index.php" class="form-pesquisa-titulo">
                <input type="text" id="pesquisa-titulo" name="pesquisa" placeholder="Pesquisar por título..." value="<?= htmlspecialchars($pesquisa) ?>">
            </form>
        </div>

        <?php if (!empty($noticias_mosaico)): ?>
        <div class="news-grid-mosaico">
            <?php foreach ($noticias_mosaico as $item): ?>
                <a href="noticia.php?id=<?= $item['dados']['id'] ?>" class="news-link <?= $item['class'] ?>">
                    <div class="<?= $item['tipo'] === 'featured' ? 'news-featured' : 'news-secondary-item' ?> <?= $item['class'] ?>">
                        <?php if ($item['dados']['imagem']): ?>
                            <img src="<?= htmlspecialchars($item['dados']['imagem']) ?>" alt="<?= htmlspecialchars($item['dados']['titulo']) ?>">
                        <?php endif; ?>
                        <div class="news-overlay">
                            <span class="news-category"><?= htmlspecialchars($item['dados']['categoria_nome']) ?></span>
                            <h2><?= htmlspecialchars($item['dados']['titulo']) ?></h2>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($noticia_destaque) && empty($noticias)): ?>
        <div class="news-loading">
            <i class="fas fa-newspaper"></i>
            <h3>Nenhuma notícia encontrada</h3>
            <p>😢 Nenhuma fofoca publicada ainda ou nenhuma notícia corresponde aos filtros aplicados.</p>
        </div>
        <?php endif; ?>

        <!-- Anúncios -->
        <?php if (!empty($anuncios)): ?>
            <?php foreach ($anuncios as $anuncio): ?>
                <div class="banner-anuncio">
                    <a href="<?= htmlspecialchars($anuncio['link']) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($anuncio['imagem']) ?>" alt="Anúncio" loading="lazy">
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Modal de Anúncio -->
<?php if ($anuncio_popup): ?>
<div id="modal-anuncio" class="modal-anuncio">
    <div class="modal-conteudo">
        <span class="modal-fechar" onclick="fecharModal()">&times;</span>
        <img src="<?= htmlspecialchars($anuncio_popup['imagem']) ?>" alt="<?= htmlspecialchars($anuncio_popup['nome']) ?>" class="modal-imagem">
        <div class="modal-titulo"><?= htmlspecialchars($anuncio_popup['nome']) ?></div>
        <div class="modal-texto"><?= htmlspecialchars($anuncio_popup['texto']) ?></div>
        <a href="<?= htmlspecialchars($anuncio_popup['link']) ?>" target="_blank" class="modal-botao">Ver Mais</a>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('pesquisa-titulo');
    const newsItems = document.querySelectorAll('.news-featured, .news-sidebar-item, .news-secondary-item');
    const newsGrid = document.querySelector('.news-grid');
    const newsSecondary = document.querySelector('.news-secondary');

    // Função para mostrar todas as notícias
    function mostrarTodasNoticias() {
        newsItems.forEach(item => {
            item.style.display = '';
            item.style.visibility = 'visible';
            item.style.opacity = '1';
        });
        
        // Garantir que os containers estejam visíveis
        if (newsGrid) newsGrid.style.display = '';
        if (newsSecondary) newsSecondary.style.display = '';
    }

    // Função para filtrar notícias
    function filtrarNoticias(termo) {
        if (!termo || termo.trim() === '') {
            mostrarTodasNoticias();
            return;
        }

        let encontrouAlguma = false;
        newsItems.forEach(item => {
            const tituloElement = item.querySelector('h2, h3');
            if (tituloElement) {
                const titulo = tituloElement.textContent.toLowerCase();
                const corresponde = titulo.includes(termo.toLowerCase());
                
                if (corresponde) {
                    item.style.display = '';
                    item.style.visibility = 'visible';
                    item.style.opacity = '1';
                    encontrouAlguma = true;
                } else {
                    item.style.display = 'none';
                }
            }
        });

        // Se não encontrou nenhuma, mostrar mensagem
        if (!encontrouAlguma) {
            const loadingDiv = document.querySelector('.news-loading');
            if (!loadingDiv) {
                const novoLoading = document.createElement('div');
                novoLoading.className = 'news-loading';
                novoLoading.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>Nenhuma notícia encontrada</h3>
                    <p>😢 Nenhuma notícia corresponde ao termo "${termo}".</p>
                `;
                if (newsGrid) {
                    newsGrid.parentNode.insertBefore(novoLoading, newsGrid.nextSibling);
                }
            }
        } else {
            // Remover mensagem de "não encontrado" se existir
            const loadingDiv = document.querySelector('.news-loading');
            if (loadingDiv && loadingDiv.querySelector('p').textContent.includes('corresponde ao termo')) {
                loadingDiv.remove();
            }
        }
    }

    // Event listener para pesquisa
    if (input) {
        input.addEventListener('input', function() {
            const termo = this.value.trim();
            filtrarNoticias(termo);
        });

        // Limpar pesquisa quando o campo for limpo
        input.addEventListener('change', function() {
            if (this.value.trim() === '') {
                mostrarTodasNoticias();
            }
        });
    }

    // Garantir que todas as notícias estejam visíveis ao carregar a página
    setTimeout(mostrarTodasNoticias, 100);

    <?php if ($anuncio_popup): ?>
    setTimeout(function() {
        const modal = document.getElementById('modal-anuncio');
        if (modal) modal.classList.add('show');
    }, 3000);
    <?php endif; ?>
});

function fecharModal() {
    const modal = document.getElementById('modal-anuncio');
    if (modal) modal.classList.remove('show');
}

window.onclick = function(event) {
    const modal = document.getElementById('modal-anuncio');
    if (event.target === modal) modal.classList.remove('show');
}
</script>
</body>
</html>
