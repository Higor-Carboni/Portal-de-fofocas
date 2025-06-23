<footer>
    <div class="redes">
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
    <small>© Fofoquei News — Todos os direitos reservados</small>
</footer>

<!-- Botão de voltar ao topo -->
<button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    // Exibir botão de topo ao rolar a página
    window.addEventListener('scroll', function () {
        const btn = document.getElementById('topo');
        btn.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
</script>