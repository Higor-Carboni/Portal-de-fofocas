<?php
require 'verifica_login.php';
require 'conexao.php';

$noticia_id = intval($_GET['id'] ?? 0);
$tipo = ($_GET['tipo'] === 'excluir') ? 'excluir' : 'editar';
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o usuário é o autor da notícia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$noticia_id, $usuario_id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>
        <link rel='stylesheet' href='css/style.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Acesso Negado',
                text: 'Você não tem permissão para acessar esta notícia.',
                confirmButtonText: 'Voltar ao Dashboard'
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        </script>
    </body>
    </html>";
    exit;
}

// Verifica se já existe uma solicitação pendente para esta notícia
$stmt = $pdo->prepare("SELECT * FROM solicitacoes WHERE noticia_id = ? AND usuario_id = ? AND tipo = ? AND status = 'pendente'");
$stmt->execute([$noticia_id, $usuario_id, $tipo]);
$solicitacao_existente = $stmt->fetch();

if ($solicitacao_existente) {
    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>
        <link rel='stylesheet' href='css/style.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Solicitação Já Existe',
                text: 'Você já possui uma solicitação pendente para esta notícia.',
                confirmButtonText: 'Voltar ao Dashboard'
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        </script>
    </body>
    </html>";
    exit;
}

// Registra a solicitação
$stmt = $pdo->prepare("INSERT INTO solicitacoes (noticia_id, usuario_id, tipo, status, data_solicitacao) VALUES (?, ?, ?, 'pendente', NOW())");
$stmt->execute([$noticia_id, $usuario_id, $tipo]);

echo "<!DOCTYPE html>
<html lang='pt-br'>
<head>
    <meta charset='UTF-8'>
    <title>Sucesso</title>
    <link rel='stylesheet' href='css/style.css'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Solicitação Enviada!',
            text: 'Sua solicitação foi enviada com sucesso e está aguardando aprovação.',
            confirmButtonText: 'Voltar ao Dashboard'
        }).then(() => {
            window.location.href = 'dashboard.php';
        });
    </script>
</body>
</html>";