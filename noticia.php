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
                        mostrarNotificacao('Link da notícia copiado para a área de transferência!', 'sucesso');
                    });
                } else {
                    // Fallback para navegadores mais antigos
                    const textArea = document.createElement('textarea');
                    textArea.value = textToShare;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    mostrarNotificacao('Link da notícia copiado para a área de transferência!', 'sucesso');
                }
            }
        }

        // Função para mostrar notificações
        function mostrarNotificacao(mensagem, tipo = 'info') {
            // Remover notificação anterior se existir
            const notificacaoExistente = document.querySelector('.notificacao');
            if (notificacaoExistente) {
                notificacaoExistente.remove();
            }

            const notificacao = document.createElement('div');
            notificacao.className = `notificacao notificacao-${tipo}`;
            notificacao.innerHTML = `
                <div class="notificacao-conteudo">
                    <i class="fas ${tipo === 'sucesso' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                    <span>${mensagem}</span>
                </div>
                <button class="notificacao-fechar" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Adicionar estilos inline para a notificação
            notificacao.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${tipo === 'sucesso' ? '#00c896' : '#3e8ef7'};
                color: white;
                padding: 12px 16px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                display: flex;
                align-items: center;
                gap: 12px;
                max-width: 300px;
                animation: slideIn 0.3s ease-out;
                font-size: 0.9rem;
            `;

            // Adicionar estilos para os elementos internos
            const conteudo = notificacao.querySelector('.notificacao-conteudo');
            conteudo.style.cssText = `
                display: flex;
                align-items: center;
                gap: 8px;
                flex: 1;
            `;

            const fechar = notificacao.querySelector('.notificacao-fechar');
            fechar.style.cssText = `
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                transition: background 0.2s;
            `;

            // Adicionar animação CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                .notificacao-fechar:hover {
                    background: rgba(255,255,255,0.2) !important;
                }
                
                @media (max-width: 480px) {
                    .notificacao {
                        top: 10px !important;
                        right: 10px !important;
                        left: 10px !important;
                        max-width: none !important;
                    }
                }
            `;
            document.head.appendChild(style);

            document.body.appendChild(notificacao);

            // Remover automaticamente após 5 segundos
            setTimeout(() => {
                if (notificacao.parentElement) {
                    notificacao.remove();
                }
            }, 5000);
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

            // Melhorar acessibilidade dos cards de outras notícias
            const cardsNoticias = document.querySelectorAll('.card-outra-noticia');
            cardsNoticias.forEach(card => {
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
                
                // Adicionar role para acessibilidade
                card.setAttribute('role', 'button');
                card.setAttribute('tabindex', '0');
            });

            // Lazy loading para imagens dos cards
            const imagensCards = document.querySelectorAll('.card-outra-noticia-img');
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                imageObserver.unobserve(img);
                            }
                        }
                    });
                });

                imagensCards.forEach(img => {
                    if (img.src) {
                        img.dataset.src = img.src;
                        img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjcwIiBoZWlnaHQ9IjcwIiBmaWxsPSIjRjBGMEYwIi8+CjxwYXRoIGQ9Ik0zNSAzNUw0MCA0MEwzMCA0MFYzNUgzNVoiIGZpbGw9IiNDQ0NDQ0MiLz4KPC9zdmc+';
                        imageObserver.observe(img);
                    }
                });
            }
        });

        // Melhorar performance em dispositivos móveis
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            
            scrollTimeout = setTimeout(() => {
                const btn = document.getElementById('topo');
                btn.style.display = window.scrollY > 300 ? 'block' : 'none';
            }, 10);
        }, { passive: true });

        // Adicionar suporte para gestos de toque
        let touchStartY = 0;
        let touchEndY = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });

        document.addEventListener('touchend', function(e) {
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartY - touchEndY;
            
            // Swipe para cima (voltar ao topo)
            if (diff > swipeThreshold && window.scrollY > 500) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>