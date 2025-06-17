<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT noticias.*, usuarios.nome AS autor_nome FROM noticias JOIN usuarios ON noticias.autor = usuarios.id WHERE noticias.id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) die('Notícia não encontrada.');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Fofocas Brasil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
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
            height: 200px;
        }
        @keyframes girarLogo {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .topo h1 {
            font-size: 1.6em;
            margin: 0;
        }
        main {
            flex: 1;
            padding: 30px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0001;
        }
        .container h2 {
            color: #3A5EFF;
            text-align: center;
        }
        .container img {
            max-width: 100%;
            border-radius: 12px;
            margin: 20px auto;
            display: block;
        }
        .container p {
            font-size: 1.05em;
            line-height: 1.6;
        }
        
        .form-buttons {
    text-align: center;
    margin-top: 20px;
}

.btn-voltar {
    background: #3A5EFF;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 1.1em;
    font-weight: bold;
    cursor: pointer;
}
.btn-voltar:hover {
    background: #1A237E;
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
        #topo {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #3A5EFF;
            color: white;
            padding: 12px 14px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 2px 8px #0003;
            border: none;
            display: none;
            z-index: 999;
        }
        #topo:hover {
            background: #1A237E;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header class="topo">
        <img src="img/logoFofoca500.png" alt="Logo" class="logo">        
    </header>

    <main>
        <div class="container">
            <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>

            <?php if ($noticia['imagem']): ?>
                <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem da notícia">
            <?php endif; ?>

            <p><?= nl2br(htmlspecialchars($noticia['noticia'])) ?></p>

            <p><small>Por <strong><?= htmlspecialchars($noticia['autor_nome']) ?></strong> em <?= date("d/m/Y H:i", strtotime($noticia['data'])) ?></small></p>
        </div>
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

<button id="topo" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });" title="Voltar ao topo">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    window.addEventListener('scroll', function () {
        const btn = document.getElementById('topo');
        btn.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
</script>
</body>
</html>