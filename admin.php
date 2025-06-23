<?php
require_once 'conexao.php';
session_start();

// Verifica se está logado como admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$titulo = "Painel do Administrador";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?></title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo" style="flex-direction: column; align-items: center;">
            <h2 style="color: #232b3f;">👑 <?= $titulo ?></h2>
            <p style="margin-bottom: 24px;">
                Bem-vindo, <strong><?= $_SESSION['usuario_nome'] ?></strong>! Acesse abaixo as áreas administrativas.
            </p>

            <div style="display: flex; gap: 24px; margin-top: 24px;">
                <a href="dashboard.php" class="link-header"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="usuarios.php" class="link-header"><i class="fas fa-users"></i> Usuários</a>
                <a href="painelSolicitacoes.php" class="link-header"><i class="fas fa-tasks"></i> Solicitações</a>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>