<?php
require 'verifica_login.php';
require 'conexao.php';

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $noticia = $_POST['noticia'] ?? '';
    $categoria_id = $_POST['categoria'] ?? '';
    $imagem = '';

    $pastaUploads = __DIR__ . '/uploads';
    if (!is_dir($pastaUploads)) {
        mkdir($pastaUploads, 0777, true);
    }

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $permitidas)) {
            $nome_arquivo = uniqid('img_') . '.' . $ext;
            $destino = $pastaUploads . '/' . $nome_arquivo;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $imagem = 'uploads/' . $nome_arquivo;
            } else {
                $msg = "Erro ao salvar a imagem.";
            }
        } else {
            $msg = "Formato de imagem não permitido.";
        }
    }

    if ($titulo && $noticia && $categoria_id && !$msg) {
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, noticia, imagem, categoria_id, autor) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $noticia, $imagem, $categoria_id, $_SESSION['usuario_id']]);
        header("Location: index.php");
        exit;
    } elseif (!$msg) {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Notícia</title>
     <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: #f0f4ff;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header.topo {
            background: #3A5EFF;
            color: white;
            padding: 10px 20px;
            border-bottom: 4px solid #1A237E;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            height: 64px;
        }
        @keyframes girarLogo {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-box {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0001;
            display: flex;
            flex-direction: column;
            gap: 18px;
            width: 100%;
            max-width: 450px;
        }
        .form-box input[type="text"],
        .form-box textarea,
        .form-box select,
        .form-box input[type="file"] {
            padding: 12px;
            border: 1.5px solid #bfc9d1;
            border-radius: 8px;
            font-size: 1em;
            background: #f8fafc;
        }
        .form-box textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-box input:focus,
        .form-box textarea:focus,
        .form-box select:focus {
            border-color: #3A5EFF;
            outline: none;
            background: #fff;
        }
        .form-box button {
            background: #3A5EFF;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
        }
        .form-box button:hover {
            background: #1A237E;
        }
        
        .msg-erro {
            color: #c0392b;
            text-align: center;
            font-size: 0.95em;
        }
       
        footer {
            background: #3A5EFF;
            color: white;
            text-align: center;
            padding: 16px 10px;
        }
        .redes img {
            width: 24px;
            margin: 0 8px;
            vertical-align: middle;
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
    <header class="topo">

        <img src="img/logoFofoca500.png" alt="Logo" class="logo">
        
    </header>

    <div class="container">
        <form method="POST" class="form-box" enctype="multipart/form-data">
            <h2>Cadastrar Notícia</h2>
            <?php if (!empty($msg)): ?>
                <p class="msg-erro"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="noticia" placeholder="Conteúdo da notícia" required></textarea>
            <select name="categoria" required>
                <option value="">Selecione a categoria</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="imagem" accept="image/*" required>
                        
           <div class="form-buttons">
                <button type="submit">Salvar</button>
               <button type="button" onclick="window.location.href='index.php'">Voltar</button>
            </div>
        </form>
    </div>

    <footer>
        <div class="redes">
            <a href="#"><i class="fab fa-instagram"></i></a>
             <a href="#"><i class="fab fa-facebook-f"></i></a>
             <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
        <small>© Fofocas Brasil — Todos os direitos reservados</small>
    </footer>
</body>
</html>