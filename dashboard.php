<?php
declare(strict_types=1);
require 'verifica_login.php';
require 'conexao.php';

// Dados do dashboard
$stmt = $pdo->prepare("SELECT n.*, u.nome AS autor_nome FROM noticias n JOIN usuarios u ON u.id = n.autor WHERE n.autor = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$noticias = $stmt->fetchAll();

$totalNoticias = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$noticiasHoje = $pdo->query("SELECT COUNT(*) FROM noticias WHERE DATE(data) = CURDATE()")->fetchColumn();
$noticiasSemana = $pdo->query("SELECT COUNT(*) FROM noticias WHERE YEARWEEK(data, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();

$categorias = $pdo->query("SELECT c.nome, COUNT(n.id) AS total FROM categorias c LEFT JOIN noticias n ON n.categoria_id = c.id GROUP BY c.id")->fetchAll();
$usuarios = $pdo->query("SELECT u.nome, COUNT(n.id) AS total FROM usuarios u LEFT JOIN noticias n ON n.autor = u.id GROUP BY u.nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Portal de Notícias</title>

  <!-- Estilos base -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="css/footer.css">

  <!-- Estilo do header exclusivo para ADMIN -->
  <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
    <link rel="stylesheet" href="css/headerAdmin.css">
    <?php else: ?>
    <!-- Estilo do header exclusivo para Isuario Comum -->
     <link rel="Stylesheet" href="css/header.css">
    <?php endif; ?>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Chart.js (se necessário) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo-dashboard">
            <h1 class="titulo-dashboard">📊 Painel de Controle</h1>
            <p style="text-align: center;">
                Bem-vindo(a), <?= $_SESSION['usuario_nome'] ?>! Acompanhe suas estatísticas e gerencie suas notícias.
            </p>

            <div class="botoes-navegacao">
                <a href="#categoriasChart-container">📂 Notícias por Categoria</a>
                <a href="#usuariosChart-container">👥 Usuários / Redatores</a>
                <a href="#noticiasPeriodoChart-container">📅 Hoje x Esta Semana</a>
                <a href="#suas-noticias-container">📰 Notícias</a>
            </div>

            <div class="cards-info-inline">
                <div class="card-info-inline">
                    <h3>Total de Notícias</h3>
                    <p><?= intval($totalNoticias) ?></p>
                </div>
                <div class="card-info-inline">
                    <h3>Publicadas Hoje</h3>
                    <p><?= intval($noticiasHoje) ?></p>
                </div>
                <div class="card-info-inline">
                    <h3>Esta Semana</h3>
                    <p><?= intval($noticiasSemana) ?></p>
                </div>
            </div>

            <div class="graficos-dashboard">
                <div class="grafico-box" id="categoriasChart-container">
                    <h3>Notícias por Categoria</h3>
                    <canvas id="categoriasChart"></canvas>
                </div>
                <div class="grafico-box" id="usuariosChart-container">
                    <h3>Usuários / Redatores</h3>
                    <canvas id="usuariosChart"></canvas>
                </div>
                <div class="grafico-box" id="noticiasPeriodoChart-container">
                    <h3>Hoje x Esta Semana</h3>
                    <canvas id="noticiasPeriodoChart"></canvas>
                </div>
            </div>

            <div id="suas-noticias-container">
                <h2>Notícias</h2>
                <table class="tabela-noticias">
                    <thead>
                        <tr>
                            <th>Autor</th>
                            <th>Título</th>
                            <th>Resumo</th>
                            <th>Data</th>
                            <th>Imagem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($noticias as $n): ?>
                            <tr>
                                <td><?= htmlspecialchars($n['autor_nome']) ?></td>
                                <td><?= htmlspecialchars($n['titulo']) ?></td>
                                <td><?= substr(strip_tags($n['noticia']), 0, 30) ?>...</td>
                                <td><?= $n['data'] ?></td>
                                <td>
                                    <?php if ($n['imagem']): ?>
                                        <img src="<?= htmlspecialchars($n['imagem']) ?>" alt="imagem">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['usuario_perfil'] === 'admin'): ?>
                                        <a href="alterarNoticia.php?id=<?= $n['id'] ?>" class="btn-editar" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="excluir_noticia.php?id=<?= $n['id'] ?>" class="btn-excluir" title="Excluir"
                                           onclick="return confirm('Excluir esta notícia?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="solicitar.php?id=<?= $n['id'] ?>&tipo=editar" class="btn-editar" title="Solicitar edição">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="solicitar.php?id=<?= $n['id'] ?>&tipo=excluir" class="btn-excluir" title="Solicitar exclusão"
                                           onclick="return confirm('Deseja solicitar a exclusão desta notícia?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: right; margin-bottom: -12px;">
                <a href="cadastroNoticia.php" class="btn-cadastrar-noticia" title="Nova Notícia">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="form-botoes">
                <button onclick="window.location.href='index.php'">Voltar</button>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- Botão para voltar ao topo -->
    <button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        new Chart(document.getElementById('categoriasChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($categorias, 'nome')) ?>,
                datasets: [{
                    label: 'Notícias por Categoria',
                    data: <?= json_encode(array_column($categorias, 'total')) ?>,
                    backgroundColor: '#36A2EB'
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
        new Chart(document.getElementById('usuariosChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($usuarios, 'nome')) ?>,
                datasets: [{
                    label: 'Notícias Publicadas',
                    data: <?= json_encode(array_column($usuarios, 'total')) ?>,
                    backgroundColor: '#3A5EFF'
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        new Chart(document.getElementById('noticiasPeriodoChart'), {
            type: 'bar',
            data: {
                labels: ['Hoje', 'Esta Semana'],
                datasets: [{
                    label: 'Notícias',
                    data: [<?= intval($noticiasHoje) ?>, <?= intval($noticiasSemana) ?>],
                    backgroundColor: ['#FF6384', '#36A2EB']
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Botão para exibir topo
        window.addEventListener('scroll', function () {
            const btn = document.getElementById('topo');
            btn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
    </script>
</body>

</html>
