
<?php
require_once 'conexao.php';
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    if ($nome && $email && $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $hash,]);
            $mensagem = "Usuário cadastrado com sucesso! <a href='login.php'>Faça login</a>";
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    } else {
        $mensagem = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Cadastro de Usuário</h2>
    <form method="POST" class="form-box">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Salvar</button>
    </form>
    <p class="msg-erro"><?= $mensagem ?></p>
    <p><a href="login.php">Já tem cadastro? Faça login</a></p>
</div>
</body>
<a href="index.php" class="btn-voltar">← Voltar</a>
</html>