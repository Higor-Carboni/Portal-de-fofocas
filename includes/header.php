<?php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
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

  <!-- Título do Portal -->
    <h1 class="titulo-portal">Portal de Notícias</h1>

    <!-- Previsão do tempo OpenWeather -->
    <div id="weather-widget" style="display:flex;align-items:center;gap:8px;margin-left:16px;"></div>

    <script>
      // Chave e configs da API do clima
      const WEATHER_API_KEY = '968f7d1cc54d497422288815e7cebb49';
      const WEATHER_CITY = 'Sapucaia do Sul'; // Altere para a cidade desejada
      const WEATHER_UNITS = 'metric';
      const WEATHER_LANG = 'pt_br';

      // Função que busca o clima na OpenWeather e mostra no topo
      async function getWeather() {
        try {
          const url = `https://api.openweathermap.org/data/2.5/weather?q=${WEATHER_CITY}&appid=${WEATHER_API_KEY}&units=${WEATHER_UNITS}&lang=${WEATHER_LANG}`;
          const res = await fetch(url);
          if (!res.ok) return;
          const data = await res.json();
          const icon = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;


          // Mostra só ícone, temperatura e cidade (descrição removida)
          document.getElementById('weather-widget').innerHTML = `
       <img src="${icon}" alt="${data.weather[0].description}">
       <div class="weather-info">
         <div class="weather-temp">${Math.round(data.main.temp)}°C</div>
         <div class="weather-city">${data.name}</div>
       </div>
      `;
        } catch (e) { }
      }
      getWeather();
    </script>

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
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            aria-label="Fechar"></button>
        </div>

        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <?php if (isset($_SESSION['usuario_id'])): ?>
              <li class="nav-item text-white mb-2">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
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
                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown" tabindex="0">
                    <li><a class="dropdown-item" href="usuarios.php"><i class="fas fa-users"></i> Usuários</a></li>
                    <li><a class="dropdown-item" href="anuncios.php"><i class="fas fa-ad"></i> Anúncios</a></li>
                    <li><a class="dropdown-item" href="painelSolicitacoes.php"><i class="fas fa-tasks"></i> Solicitações</a></li>
                  </ul>
                </li>
              <?php else: ?>
                <li class="nav-item"><a class="nav-link text-white" href="dashboard.php"><i class="fas fa-home"></i>
                    Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="cadastroNoticia.php"><i class="fas fa-plus"></i>
                    Nova Notícia</a></li>
              <?php endif; ?>

              <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
              </li>
            <?php endif; ?>

              <!-- ABA ACESSIBILIDADE COMO DROPDOWN -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="acessibilidadeDropdown" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-universal-access"></i> Acessibilidade
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="acessibilidadeDropdown">
                  <li>
                    <button class="dropdown-item" id="toggle-darkmode-btn" type="button">
                      <i class="fa fa-moon"></i> <span id="darkmode-status">Ativar Dark Mode</span>
                    </button>
                  </li>
                </ul>
              </li>
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
        
        <!-- BOTÃO DARK MODE PARA VISITANTES -->
        <button class="btn-darkmode-visitante" id="toggle-darkmode-btn-visitante" type="button" title="Alternar Dark Mode">
          <i class="fa fa-moon"></i>
        </button>
      </nav>
    <?php endif; ?>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function atualizaBotaoDarkMode() {
        const status = document.getElementById('darkmode-status');
        const icon = document.querySelector('#toggle-darkmode-btn i');
        const iconVisitante = document.querySelector('#toggle-darkmode-btn-visitante i');
        
        if (status && icon) {
            if(document.body.classList.contains('dark-mode')) {
                status.textContent = 'Desativar Dark Mode';
                icon.className = 'fa fa-sun';
            } else {
                status.textContent = 'Ativar Dark Mode';
                icon.className = 'fa fa-moon';
            }
        }
        
        if (iconVisitante) {
            if(document.body.classList.contains('dark-mode')) {
                iconVisitante.className = 'fa fa-sun';
            } else {
                iconVisitante.className = 'fa fa-moon';
            }
        }
    }

    // Aplica dark mode se já estiver salvo
    if(localStorage.getItem('darkmode')) {
        document.body.classList.add('dark-mode');
    }
    atualizaBotaoDarkMode();

    // Evento do botão para usuários logados
    const btn = document.getElementById('toggle-darkmode-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            if(document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkmode', '1');
            } else {
                localStorage.removeItem('darkmode');
            }
            atualizaBotaoDarkMode();
        });
    }

    // Evento do botão para visitantes
    const btnVisitante = document.getElementById('toggle-darkmode-btn-visitante');
    if (btnVisitante) {
        btnVisitante.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            if(document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkmode', '1');
            } else {
                localStorage.removeItem('darkmode');
            }
            atualizaBotaoDarkMode();
        });
    }
});
</script>