<?php
require 'verifica_login.php';
require 'conexao.php';
$id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    if ($nome && $email) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
        $stmt->execute([$nome, $email, $id]);
        $msg = "Perfil atualizado!";
        // Atualiza sessão
        $_SESSION['usuario_nome'] = $nome;
    } else {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Alterar Perfil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- <style>
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
            max-width: 420px;
            margin: 40px auto 32px auto;
        }

        .form-box h2 {
            margin: 0 0 10px 0;
            font-size: 1.5em;
            color: #232b3f;
            text-align: center;
            letter-spacing: 1px;
        }

        .form-box label {
            font-weight: 500;
            color: #232b3f;
            margin-bottom: 4px;
        }

        .form-box input[type="text"],
        .form-box input[type="email"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border: 1.5px solid #bfc6d1;
            border-radius: 7px;
            font-size: 1.08em;
            background: #f7f9fc;
            margin-bottom: 8px;
            transition: border 0.18s;
            display: block;
        }

        .form-box input[type="text"]:focus,
        .form-box input[type="email"]:focus {
            border: 1.5px solid #232b3f;
            outline: none;
        }

        .form-botoes {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-top: 10px;
        }

        .form-botoes button {
            flex: 0 0 auto;
            min-width: 110px;
            background: #232b3f;
            color: #fff;
            border: none;
            padding: 10px 0;
            border-radius: 6px;
            font-size: 1.08em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, transform 0.13s;
        }

        .form-botoes button:hover {
            background: #3a4666;
            transform: scale(1.04);
        }

        .msg-erro {
            color: #c62828;
            background: #ffeaea;
            border: 1px solid #ffcdd2;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 1em;
            text-align: center;
        }

        .msg-sucesso {
            color: #388e3c;
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 1em;
            text-align: center;
        }

        @media (max-width: 600px) {
            .form-box {
                max-width: 98vw;
                padding: 18px 4vw 16px 4vw;
            }

            .form-botoes {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style> -->
</head>

<body>
    <header class="topo">
        <div class="cabecalho-container">
            <img src="img/logoFofoca500.png" alt="Logo" class="logo">

        </div>
    </header>
    <div class="container">
        <form method="POST" class="form-box">
            <h2>Alterar Perfil</h2>
            <?php if ($msg): ?>
                <p class="<?= strpos($msg, 'atualizad') !== false ? 'msg-sucesso' : 'msg-erro' ?>">
                    <?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            <div class="form-botoes">
                <button type="submit">Salvar</button>
                <button type="button" onclick="window.location.href='dashboard.php'">Voltar</button>
            </div>
        </form>
    </div>
    <footer>
        <div class="redes">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
        <small>© Fofoquei News — Todos os direitos reservados</small>
    </footer>
</body>

</html>