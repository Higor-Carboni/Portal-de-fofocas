<!-- <footer>
    <div class="redes">
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
    <small>© Fofoquei News — Todos os direitos reservados</small>
</footer> -->

<footer>
    <div class="redes">
    <a href="https://www.facebook.com/facebook" target="_blank" aria-label="Facebook" rel="noopener">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://www.instagram.com/instagram" target="_blank" aria-label="Instagram" rel="noopener">
      <i class="fab fa-instagram"></i>
    </a>
    <a href="https://twitter.com/X" target="_blank" aria-label="Twitter/X" rel="noopener">
      <i class="fab fa-x-twitter"></i>
    </a>
    <a href="https://www.youtube.com/user/YouTube" target="_blank" aria-label="YouTube" rel="noopener">
      <i class="fab fa-youtube"></i>
    </a>
    <a href="https://www.linkedin.com/company/linkedin" target="_blank" aria-label="LinkedIn" rel="noopener">
      <i class="fab fa-linkedin-in"></i>
    </a>
  </div>
  <p>&copy; <?= date('Y') ?> Fofoquei News. Todos os direitos reservados.</p>
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