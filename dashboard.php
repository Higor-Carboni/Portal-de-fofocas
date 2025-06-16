<?php
require 'verifica_login.php';
require 'conexao.php';

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE autor = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$noticias = $stmt->fetchAll();

$totalNoticias = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$noticiasHoje = $pdo->query("SELECT COUNT(*) FROM noticias WHERE DATE(data) = CURDATE()")->fetchColumn();
$noticiasSemana = $pdo->query("SELECT COUNT(*) FROM noticias WHERE YEARWEEK(data, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();

$categorias = $pdo->query("SELECT c.nome, COUNT(n.id) AS total FROM categorias c LEFT JOIN noticias n ON n.categoria_id = c.id GROUP BY c.id")->fetchAll();
$usuarios = $pdo->query("SELECT u.id, u.nome, COUNT(n.id) AS total FROM usuarios u LEFT JOIN noticias n ON n.autor = u.id GROUP BY u.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Portal de Notícias</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f4ff;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-top: 0;
        }
        .buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .buttons a {
            background: #3A5EFF;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
        }
        .buttons a:hover {
            background: #1A237E;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f0f4ff;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

<a href="index.php" class="buttons">← Voltar</a>
<h1>📊 Painel de Controle</h1>

<div class="buttons">
    <a href="cadastroNoticia.php">+ Nova Notícia</a>
    <a href="alterarUsuario.php">Editar Perfil</a>
    <a href="usuarios.php">Gerenciar Usuários</a>
</div>

<div class="dashboard-cards">
    <div class="card">
        <h3>Total de Notícias</h3>
        <p><strong><?= $totalNoticias ?></strong></p>
    </div>
    <div class="card">
        <h3>Hoje</h3>
        <p><strong><?= $noticiasHoje ?></strong></p>
    </div>
    <div class="card">
        <h3>Esta Semana</h3>
        <p><strong><?= $noticiasSemana ?></strong></p>
    </div>
</div>

<div class="chart-container">
    <h3>Notícias por Categoria</h3>
    <canvas id="categoriasChart"></canvas>
</div>
<div class="chart-container">
    <h3>Usuários / Redatores</h3>
    <canvas id="usuariosChart"></canvas>
</div>
<div class="chart-container">
    <h3>Hoje x Esta Semana</h3>
    <canvas id="noticiasPeriodoChart"></canvas>
</div>

<h2>📝 Suas Notícias</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
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
            <td><?= $n['id'] ?></td>
            <td><?= htmlspecialchars($n['titulo']) ?></td>
            <td><?= substr(strip_tags($n['noticia']), 0, 30) ?>...</td>
            <td><?= $n['data'] ?></td>
            <td>
                <?php if ($n['imagem']): ?>
                    <img src="<?= htmlspecialchars($n['imagem']) ?>" style="max-width:60px;">
                <?php endif; ?>
            </td>
            <td>
                <a href="alterarNoticia.php?id=<?= $n['id'] ?>" class="buttons">✏️</a>
                <a href="excluir_noticia.php?id=<?= $n['id'] ?>" class="buttons" style="background:#c00;">❌</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
new Chart(document.getElementById('categoriasChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($categorias, 'nome')) ?>,
        datasets: [{
            label: 'Total de Notícias',
            data: <?= json_encode(array_column($categorias, 'total')) ?>,
            backgroundColor: '#36A2EB',
            borderColor: '#2c6ecb',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                }
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
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                }
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
            data: [<?= $noticiasHoje ?>, <?= $noticiasSemana ?>],
            backgroundColor: ['#FF6384', '#36A2EB']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                }
            }
        }
    }
});
</script>
</body>
</html>
