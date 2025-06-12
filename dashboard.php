<?php
require 'verifica_login.php';
require 'conexao.php';

// Notícias do usuário logado
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE autor = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$noticias = $stmt->fetchAll();

// Estatísticas gerais
$totalNoticias = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$noticiasHoje = $pdo->query("SELECT COUNT(*) FROM noticias WHERE DATE(data) = CURDATE()")->fetchColumn();
$noticiasSemana = $pdo->query("SELECT COUNT(*) FROM noticias WHERE YEARWEEK(data, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();

// Categorias e contagem de notícias
$categorias = $pdo->query("
    SELECT c.nome, COUNT(n.id) AS total
    FROM categorias c
    LEFT JOIN noticias n ON n.categoria_id = c.id
    GROUP BY c.id
")->fetchAll();

// Notícias publicadas hoje e nesta semana
$hoje = date('Y-m-d');
$noticias_hoje = $pdo->query("SELECT COUNT(*) FROM noticias WHERE DATE(data) = '$hoje'")->fetchColumn();

$seteDiasAtras = date('Y-m-d', strtotime('-7 days'));
$noticias_semana = $pdo->query("SELECT COUNT(*) FROM noticias WHERE DATE(data) >= '$seteDiasAtras'")->fetchColumn();

// Lista de usuários/redatores
$usuarios = $pdo->query("
    SELECT u.id, u.nome, COUNT(n.id) AS total
    FROM usuarios u
    LEFT JOIN noticias n ON n.autor = u.id
    GROUP BY u.id
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Portal de Notícias</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }
        .box {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
            margin-bottom: 30px;
        }
        .box h3 {
            margin-bottom: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn-voltar">← Voltar</a>

    <div class="container" style="max-width:1000px;margin:0 auto">
        <h1>Portal de Notícias - Dashboard</h1>

        <!-- Botões de navegação -->
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:20px">
            <a href="cadastroNoticia.php" class="btn-voltar" style="width:auto;">[+] Nova Notícia</a>
            <a href="alterarUsuario.php" class="btn-voltar" style="width:auto;">Editar Perfil</a>
            <a href="usuarios.php" class="btn-voltar" style="width:auto;">Gerenciar Usuários</a>
        </div>

        <hr>

        <h2>📊 Estatísticas</h2>
        <ul>
            <li><strong>Total de Notícias:</strong> <?= $totalNoticias ?> </li>
            <li><strong>Notícias Hoje:</strong> <?= $noticiasHoje ?> </li>
            <li><strong>Notícias Esta Semana:</strong> <?= $noticiasSemana ?> </li>
        </ul>

        <h3>🗂️ Notícias por Categoria</h3>
        <div class="chart-container">
            <canvas id="categoriasChart"></canvas>
        </div>

        <h3>🧑‍💻 Usuários / Redatores</h3>
        <div class="chart-container">
            <canvas id="usuariosChart"></canvas>
        </div>

        <h3>📅 Notícias Hoje vs Esta Semana</h3>
        <div class="chart-container">
            <canvas id="noticiasPeriodoChart"></canvas>
        </div>

        <hr>

        <h2>📝 Suas Notícias</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Notícia</th>
                    <th>Data</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($noticias as $n): ?>
                <tr>
                    <td><?= $n['id'] ?> </td>
                    <td><?= htmlspecialchars($n['titulo']) ?> </td>
                    <td><?= substr(strip_tags($n['noticia']), 0, 30) ?>...</td>
                    <td><?= $n['data'] ?> </td>
                    <td>
                        <?php if ($n['imagem']): ?>
                            <img src="<?= htmlspecialchars($n['imagem']) ?>" style="max-width:60px;max-height:40px">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="alterarNoticia.php?id=<?= $n['id'] ?>" class="btn-voltar">✏️</a>
                        <a href="excluir_noticia.php?id=<?= $n['id'] ?>" class="btn-voltar" style="background:#c00;color:#fff">❌</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Gráfico de Notícias por Categoria
    new Chart(document.getElementById('categoriasChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($categorias, 'nome')) ?>,
            datasets: [{
                label: 'Notícias por Categoria',
                data: <?= json_encode(array_column($categorias, 'total')) ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#FF5733'].slice(0, <?= count($categorias) ?>),
                borderColor: ['#fff', '#fff', '#fff', '#fff'].slice(0, <?= count($categorias) ?>),
                borderWidth: 1
            }]
        }
    });

    // Gráfico de Notícias Publicadas por Redator
    new Chart(document.getElementById('usuariosChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($usuarios, 'nome')) ?>,
            datasets: [{
                label: 'Notícias Publicadas por Redator',
                data: <?= json_encode(array_column($usuarios, 'total')) ?>,
                backgroundColor: '#FF5733',
                borderColor: '#ff5733',
                borderWidth: 1
            }]
        }
    });

    // Gráfico de Notícias - Hoje vs Esta Semana
    new Chart(document.getElementById('noticiasPeriodoChart'), {
        type: 'bar',
        data: {
            labels: ['Hoje', 'Esta Semana'],
            datasets: [{
                label: 'Notícias',
                data: [<?= $noticiasHoje ?>, <?= $noticiasSemana ?>],
                backgroundColor: ['#FF6384', '#36A2EB'],
                borderColor: ['#FF6384', '#36A2EB'],
                borderWidth: 1
            }]
        }
    });
    </script>
</body>
</html>
