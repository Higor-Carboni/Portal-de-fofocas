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

        <main class="conteudo-admin">
            <h2>👑 <?= $titulo ?></h2>
            <p>
                Bem-vindo, <strong><?= $_SESSION['usuario_nome'] ?></strong>! Acesse abaixo as áreas administrativas.
            </p>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>