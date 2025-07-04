<?php
require 'verifica_login.php';
require 'conexao.php';

if ($_SESSION['usuario_perfil'] !== 'admin') {
    die('Acesso restrito.');
}

$id = intval($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

// Verifica se a solicitação existe e está pendente
$stmt = $pdo->prepare("SELECT * FROM solicitacoes WHERE id = ? AND status = 'pendente'");
$stmt->execute([$id]);
$solicitacao = $stmt->fetch();

if (!$solicitacao) {
    die('Solicitação não encontrada ou já processada.');
}

// Atualiza status
if ($acao === 'aprovar') {
    $stmt = $pdo->prepare("UPDATE solicitacoes SET status = 'aprovada' WHERE id = ?");
    $stmt->execute([$id]);

    if ($solicitacao['tipo'] === 'excluir') {
        // Exclui a notícia
        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$solicitacao['noticia_id']]);

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
                    title: 'Notícia Excluída!',
                    text: 'A notícia foi excluída com sucesso.',
                    confirmButtonText: 'Voltar ao Painel'
                }).then(() => {
                    window.location.href = 'painelSolicitacoes.php';
                });
            </script>
        </body>
        </html>";
        exit;

    } elseif ($solicitacao['tipo'] === 'editar') {
        // Redireciona para a página de edição
        header("Location: alterarNoticia.php?id=" . $solicitacao['noticia_id']);
        exit;
    }

    elseif ($solicitacao['tipo'] === 'nova') {
        // Aprovação de notícia recém-cadastrada
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
                    title: 'Notícia Aprovada!',
                    text: 'A notícia foi aprovada com sucesso.',
                    confirmButtonText: 'Voltar ao Painel'
                }).then(() => {
                    window.location.href = 'painelSolicitacoes.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }

} elseif ($acao === 'rejeitar') {
    $stmt = $pdo->prepare("UPDATE solicitacoes SET status = 'rejeitada' WHERE id = ?");
    $stmt->execute([$id]);

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
                icon: 'info',
                title: 'Solicitação Rejeitada',
                text: 'A solicitação foi rejeitada com sucesso.',
                confirmButtonText: 'Voltar ao Painel'
            }).then(() => {
                window.location.href = 'painelSolicitacoes.php';
            });
        </script>
    </body>
    </html>";
    exit;
} else {
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
                title: 'Ação Inválida',
                text: 'A ação solicitada não é válida.',
                confirmButtonText: 'Voltar ao Painel'
            }).then(() => {
                window.location.href = 'painelSolicitacoes.php';
            });
        </script>
    </body>
    </html>";
    exit;
}