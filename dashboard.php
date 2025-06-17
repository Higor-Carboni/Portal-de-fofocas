<?php
declare(strict_types=1);
require 'verifica_login.php';
require 'conexao.php';

// Dados

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: #f0f4ff;
            font-family: 'Segoe UI', sans-serif;
            height: 100%;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header.topo {
            background: #3A5EFF;
            color: white;
            padding: 12px 20px;
            border-bottom: 4px solid #1A237E;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .logo {
            height: 70px;
            animation: girarLogo 20s linear infinite;
        }
        @keyframes girarLogo {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
        }
        footer {
            background: #3A5EFF;
            color: white;
            text-align: center;
            padding: 20px 10px;
        }
        .redes a {
            color: white;
            margin: 0 10px;
            font-size: 22px;
        }
        .redes a:hover {
            color: #cfd8ff;
        }
        .form-buttons {
            margin: 20px 0;
            text-align: center;
        }
        .btn-voltar {
            background: #3A5EFF;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-voltar:hover {
            background: #1A237E;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header class="topo">
        <img src="img/logoFofoca500.png" alt="Logo" class="logo">
        <h1>Fofocas Brasil 💬</h1>
    </header>

    <main style="flex: 1; padding: 20px;">
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

        <h2>Suas Notícias</h2>
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

        <div class="form-buttons">
            <button class="btn-voltar" type="button" onclick="window.location.href='index.php'">Voltar</button>
        </div>
    </main>

    <footer>
        <div class="redes">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
        <small>© Fofocas Brasil — Todos os direitos reservados</small>
    </footer>
</div>

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
    }
});
</script>
</body>
</html>
