// Função para compartilhar notícia
function compartilharNoticia() {
    const titulo = document.querySelector('.noticia-titulo')?.textContent || 'Notícia do Fofoquei News';
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: titulo,
            url: url
        }).catch(err => {
            console.log('Erro ao compartilhar:', err);
            copiarParaAreaTransferencia(titulo, url);
        });
    } else {
        copiarParaAreaTransferencia(titulo, url);
    }
}

// Função auxiliar para copiar para área de transferência
function copiarParaAreaTransferencia(titulo, url) {
    const textToShare = `${titulo}\n\nLeia mais: ${url}`;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(textToShare).then(() => {
            mostrarNotificacao('Link da notícia copiado para a área de transferência!', 'sucesso');
        }).catch(err => {
            console.log('Erro ao copiar:', err);
            copiarFallback(textToShare);
        });
    } else {
        copiarFallback(textToShare);
    }
}

// Fallback para navegadores mais antigos
function copiarFallback(textToShare) {
    const textArea = document.createElement('textarea');
    textArea.value = textToShare;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        mostrarNotificacao('Link da notícia copiado para a área de transferência!', 'sucesso');
    } catch (err) {
        console.log('Erro no fallback:', err);
        mostrarNotificacao('Erro ao copiar link. Tente copiar manualmente.', 'erro');
    }
    
    document.body.removeChild(textArea);
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
            <i class="fas ${getIconeNotificacao(tipo)}"></i>
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
        background: ${getCorNotificacao(tipo)};
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

    // Adicionar animação CSS se não existir
    if (!document.querySelector('#notificacao-styles')) {
        const style = document.createElement('style');
        style.id = 'notificacao-styles';
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
    }

    document.body.appendChild(notificacao);

    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if (notificacao.parentElement) {
            notificacao.remove();
        }
    }, 5000);
}

// Função auxiliar para obter ícone da notificação
function getIconeNotificacao(tipo) {
    const icones = {
        'sucesso': 'fa-check-circle',
        'erro': 'fa-exclamation-circle',
        'aviso': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return icones[tipo] || icones.info;
}

// Função auxiliar para obter cor da notificação
function getCorNotificacao(tipo) {
    const cores = {
        'sucesso': '#00c896',
        'erro': '#ff4757',
        'aviso': '#ffa502',
        'info': '#3e8ef7'
    };
    return cores[tipo] || cores.info;
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    inicializarEfeitosVisuais();
    inicializarAcessibilidade();
    inicializarLazyLoading();
    inicializarOtimizacoesMobile();
    inicializarGestosToque();
});

// Função para inicializar efeitos visuais
function inicializarEfeitosVisuais() {
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
}

// Função para inicializar acessibilidade
function inicializarAcessibilidade() {
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
}

// Função para inicializar lazy loading
function inicializarLazyLoading() {
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
}

// Função para inicializar otimizações mobile
function inicializarOtimizacoesMobile() {
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        
        scrollTimeout = setTimeout(() => {
            const btn = document.getElementById('topo');
            if (btn) {
                btn.style.display = window.scrollY > 300 ? 'block' : 'none';
            }
        }, 10);
    }, { passive: true });
}

// Função para inicializar gestos de toque
function inicializarGestosToque() {
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
} 