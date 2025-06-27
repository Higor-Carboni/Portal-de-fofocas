<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$paginaAtual = basename($_SERVER['PHP_SELF']);
$exibeOffcanvas = isset($_SESSION['usuario_id']);
?>

<!-- BOOTSTRAP CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="header.css">
<link rel="stylesheet" href="headerAdmin.css">

<header class="topo">
  <div class="cabecalho-container">
    <a href="index.php">
      <img src="img/logoFofoca500.png" alt="Logo" class="logo">
    </a>

    <?php if ($exibeOffcanvas): ?>
      <!-- BOTÃO MENU HAMBURGUER -->
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral"
        aria-controls="menuLateral" aria-label="Abrir menu">
        <i class="fas fa-bars" style="color:#3e4354; font-size:36px;"></i>
      </button>

      <!-- MENU OFFCANVAS -->
      <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="menuLateralLabel">Menu</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>

        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <?php if (isset($_SESSION['usuario_id'])): ?>
              <li class="nav-item text-white mb-2">
                <i class="fas fa-user-circle"></i> <?= $_SESSION['usuario_nome'] ?>
              </li>

              <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
                <li class="nav-item">
                  <a class="nav-link text-white" href="dashboard.php">
                    <i class="fas fa-chart-line"></i> Dashboard
                  </a>
                </li>

                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-white" href="#" id="adminDropdown" role="button"
                     data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-crown"></i> Administração
                  </a>
                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item" href="usuarios.php"><i class="fas fa-users"></i> Usuários</a></li>
                    <li><a class="dropdown-item" href="painelSolicitacoes.php"><i class="fas fa-tasks"></i> Solicitações</a></li>
                  </ul>
                </li>
              <?php else: ?>
                <li class="nav-item"><a class="nav-link text-white" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="cadastroNoticia.php"><i class="fas fa-plus"></i> Nova Notícia</a></li>
              <?php endif; ?>

              <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    <?php else: ?>
      <!-- VISITANTE -->
      <nav class="menu-superior">
        <?php if (!isset($_SESSION['usuario_id'])): ?>
          <a href="login.php" class="link-header"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php else: ?>
          <span class="nome-usuario">
            <i class="fas fa-user-circle"></i> <?= $_SESSION['usuario_nome'] ?>
          </span>
          <a href="logout.php" class="link-header text-danger"><i class="fas fa-sign-out-alt"></i> Sair</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
</header>