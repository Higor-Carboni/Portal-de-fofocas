<?php
require_once 'verifica_login.php';
require_once 'verificaAdmin.php';
require_once 'conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'comum';

    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'E-mail inválido.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensagem = 'Este e-mail já está cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senhaHash, $perfil]);

            $mensagem = 'Usuário cadastrado com sucesso!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/usuarios.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <div class="container">
                <form method="POST" class="form-box">
                    <h2 class="titulo-pagina">✏️ Cadastrar Usuário</h2>
                    <?php if (!empty($mensagem)): ?>
                        <p class="<?= strpos($mensagem, 'sucesso') !== false ? 'msg-sucesso' : 'msg-erro' ?>">
                            <?= htmlspecialchars($mensagem) ?>
                        </p>
                    <?php endif; ?>

                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                        required>

                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required>

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>

                    <label for="perfil">Perfil</label>
                    <select id="perfil" name="perfil" required>
                        <option value="comum" <?= ($_POST['perfil'] ?? '') === 'comum' ? 'selected' : '' ?>>Comum
                        </option>
                        <option value="admin" <?= ($_POST['perfil'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin
                        </option>
                    </select>

                    <div class="form-botoes">
                        <button type="submit">Salvar</button>
                        <button type="button" onclick="window.location.replace('index.php')">Voltar</button>
                    </div>
                </form>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>