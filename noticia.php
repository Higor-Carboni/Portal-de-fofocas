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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .noticia-main-layout {
            display: flex;
            gap: 32px;
            max-width: 1400px;
            margin: 0 auto;
            margin-top: 32px;
            margin-bottom: 32px;
        }
        .noticia-container {
            flex: 2 1 0%;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            padding: 48px 56px 36px 56px;
            min-width: 0;
            transition: background 0.3s, color 0.3s;
        }
        .noticia-header {
            text-align: left;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .noticia-titulo {
            font-size: 3.2rem;
            font-weight: 900;
            color: #2c3e50;
            line-height: 1.12;
            margin-bottom: 18px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .noticia-imagem-container {
            margin: 44px 0 32px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.13);
        }
        .noticia-imagem {
            max-width: 100%;
            max-height: 700px;
            width: auto;
            height: auto;
            object-fit: cover;
            border-radius: 18px;
            transition: transform 0.3s;
        }
        .noticia-imagem:hover {
            transform: scale(1.04);
        }
        .noticia-conteudo {
            font-size: 1.28rem;
            line-height: 2.1;
            color: #2c3e50;
            text-align: justify;
            margin: 44px 0 28px 0;
            padding: 0 10px;
        }
        .noticia-acoes {
            display: flex;
            justify-content: flex-start;
            gap: 22px;
            margin-top: 44px;
            padding-top: 32px;
            border-top: 2px solid #f0f0f0;
            flex-wrap: wrap;
        }
        .btn-noticia {
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            box-shadow: 0 2px 12px #0001;
            cursor: pointer;
            transition: all 0.25s;
            letter-spacing: 0.5px;
        }
        .btn-voltar-noticia {
            background: linear-gradient(135deg, #7b5cff 0%, #3e8ef7 100%);
            color: #fff;
            border: 2px solid #7b5cff;
        }
        .btn-voltar-noticia:hover {
            background: linear-gradient(135deg, #3e8ef7 0%, #7b5cff 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 20px rgba(62, 142, 247, 0.18);
        }
        .btn-compartilhar {
            background: linear-gradient(135deg, #00c896 0%, #00b4d8 100%);
            color: #fff;
            border: 2px solid #00c896;
        }
        .btn-compartilhar:hover {
            background: linear-gradient(135deg, #00b4d8 0%, #00c896 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 20px rgba(0, 200, 150, 0.18);
        }
        .noticia-tags {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .sidebar-outras-noticias {
            flex: 1 1 320px;
            min-width: 260px;
            max-width: 370px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 18px #0001;
            padding: 24px 18px 18px 18px;
            height: fit-content;
            align-self: flex-start;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar-outras-noticias h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #3a4666;
        }
        .card-outra-noticia {
            display: flex;
            gap: 14px;
            margin-bottom: 18px;
            background: #f7f8fa;
            border-radius: 10px;
            box-shadow: 0 2px 8px #0001;
            overflow: hidden;
            transition: box-shadow 0.2s, background 0.3s;
        }
        .card-outra-noticia:hover {
            box-shadow: 0 6px 18px #0002;
            background: #f0f3fa;
        }
        .card-outra-noticia-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px 0 0 10px;
            background: #e0e0e0;
        }
        .card-outra-noticia-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 6px 0;
        }
        .card-outra-noticia-titulo {
            font-size: 1rem;
            font-weight: 600;
            color: #232b3f;
            margin-bottom: 4px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
        .card-outra-noticia-data {
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        /* DARK MODE */
        body.dark-mode {
            background: #181c24;
        }
        body.dark-mode .noticia-container {
            background: #232b3f;
            color: #f2f2f2;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }
        body.dark-mode .noticia-header {
            border-bottom: 2px solid #232b3f;
        }
        body.dark-mode .noticia-titulo {
            color: #fff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.18);
        }
        body.dark-mode .noticia-conteudo {
            color: #e0e0e0;
        }
        body.dark-mode .noticia-acoes {
            border-top: 2px solid #232b3f;
        }
        body.dark-mode .btn-voltar-noticia {
            background: linear-gradient(135deg, #3e8ef7 0%, #7b5cff 100%);
            color: #fff;
            border: 2px solid #7b5cff;
        }
        body.dark-mode .btn-voltar-noticia:hover {
            background: linear-gradient(135deg, #7b5cff 0%, #3e8ef7 100%);
            color: #fff;
        }
        body.dark-mode .btn-compartilhar {
            background: linear-gradient(135deg, #00b4d8 0%, #00c896 100%);
            color: #fff;
            border: 2px solid #00c896;
        }
        body.dark-mode .btn-compartilhar:hover {
            background: linear-gradient(135deg, #00c896 0%, #00b4d8 100%);
            color: #fff;
        }
        body.dark-mode .sidebar-outras-noticias {
            background: #232b3f;
            color: #f2f2f2;
            box-shadow: 0 4px 18px #0004;
        }
        body.dark-mode .sidebar-outras-noticias h3 {
            color: #b3b8c5;
        }
        body.dark-mode .card-outra-noticia {
            background: #232b3f;
            color: #f2f2f2;
            box-shadow: 0 2px 8px #0004;
        }
        body.dark-mode .card-outra-noticia:hover {
            background: #181c24;
        }
        body.dark-mode .card-outra-noticia-titulo {
            color: #fff;
        }
        body.dark-mode .card-outra-noticia-data {
            color: #b3b8c5;
        }
        @media (max-width: 1100px) {
            .noticia-main-layout {
                flex-direction: column;
                gap: 0;
            }
            .sidebar-outras-noticias {
                max-width: 100%;
                margin: 32px 0 0 0;
                align-self: stretch;
            }
        }
        @media (max-width: 700px) {
            .noticia-container {
                padding: 16px 4vw;
            }
            .noticia-titulo {
                font-size: 1.7rem;
            }
            .noticia-imagem {
                max-height: 220px;
            }
            .sidebar-outras-noticias {
                padding: 12px 2vw;
            }
        }
    </style>
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
                    <a href="index.php" class="btn-noticia btn-voltar-noticia">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao Portal
                    </a>
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



        // Função para compartilhar notícia
        function compartilharNoticia() {
            const titulo = '<?= addslashes($noticia['titulo']) ?>';
            const url = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: titulo,
                    url: url
                });
            } else {
                // Fallback para navegadores que não suportam Web Share API
                const textToShare = `${titulo}\n\nLeia mais: ${url}`;
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(textToShare).then(() => {
                        alert('Link da notícia copiado para a área de transferência!');
                    });
                } else {
                    // Fallback para navegadores mais antigos
                    const textArea = document.createElement('textarea');
                    textArea.value = textToShare;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    alert('Link da notícia copiado para a área de transferência!');
                }
            }
        }

        // Adicionar efeito de fade-in na imagem
        document.addEventListener('DOMContentLoaded', function() {
            const imagem = document.querySelector('.noticia-imagem');
            if (imagem) {
                imagem.style.opacity = '0';
                imagem.style.transition = 'opacity 0.5s ease-in-out';
                
                imagem.onload = function() {
                    imagem.style.opacity = '1';
                };
                
                // Se a imagem já foi carregada
                if (imagem.complete) {
                    imagem.style.opacity = '1';
                }
            }
        });
    </script>
</body>
</html>