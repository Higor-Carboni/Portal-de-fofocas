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
<button id="btn-topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });" style="display:none;position:fixed;right:24px;bottom:24px;z-index:99;font-size:1.3em;background:#232b3f;color:#fff;border:none;border-radius:50%;width:44px;height:44px;align-items:center;justify-content:center;box-shadow:0 2px 8px #0003;">
  <i class="fas fa-arrow-up"></i>
</button>

<script>
  // Exibir botão de topo ao rolar a página
  window.addEventListener('scroll', function () {
    const btn = document.getElementById('btn-topo');
    if (btn) {
      btn.style.display = window.scrollY > 300 ? 'block' : 'none';
    }
  });
</script>

<!-- Bootstrap Bundle JS (obrigatório para dropdown/offcanvas funcionar) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<!-- Exportar para pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- autoTable plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
