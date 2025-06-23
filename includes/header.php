<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<header class="topo">
  <div class="cabecalho-container">
    <img src="img/logoFofoca500.png" alt="Logo" class="logo">

    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
      <button id="menu-btn" class="menu-icon">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
    <?php endif; ?>

    <nav class="header-links" id="nav-links">
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <span class="nome-usuario">
          <i class="fas fa-user-circle"></i> <?= $_SESSION['usuario_nome'] ?>
        </span>

        <div class="menu-links-container">
          <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>

            <!-- 👑 Painel do Administrador com submenu -->
            <div class="dropdown">
              <a href="admin.php" class="link-header"><i class="fas fa-crown"></i> Painel do Administrador</a>
              <div class="dropdown-content">
                <a href="usuarios.php"><i class="fas fa-users"></i> Usuários</a>
                <a href="painelSolicitacoes.php"><i class="fas fa-tasks"></i> Solicitações</a>
              </div>
            </div>

            <!-- 📊 Dashboard -->
            <a href="dashboard.php" class="link-header"><i class="fas fa-chart-line"></i> Dashboard</a>

          <?php else: ?>
            <a href="dashboard.php" class="link-header"><i class="fas fa-home"></i> Dashboard</a>
            <a href="cadastroNoticia.php" class="link-header"><i class="fas fa-plus"></i> Nova Notícia</a>
          <?php endif; ?>

          <!-- 🔚 Sair -->
          <a href="logout.php" class="link-header"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
      <?php elseif ($paginaAtual === 'index.php'): ?>
        <a href="login.php" class="link-header"><i class="fas fa-sign-in-alt"></i> Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Script do menu responsivo -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.getElementById('menu-btn');
    const navLinks = document.getElementById('nav-links');

    if (menuBtn && navLinks) {
      menuBtn.addEventListener('click', function () {
        navLinks.classList.toggle('ativo');
      });

      document.addEventListener('click', function (e) {
        if (!menuBtn.contains(e.target) && !navLinks.contains(e.target)) {
          navLinks.classList.remove('ativo');
        }
      });
    }
  });
</script>