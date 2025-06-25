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

        header("Location: painelSolicitacoes.php?msg=excluida");
        exit;

    } elseif ($solicitacao['tipo'] === 'editar') {
        // Redireciona para a página de edição
        header("Location: alterarNoticia.php?id=" . $solicitacao['noticia_id']);
        exit;
    }

    elseif ($solicitacao['tipo'] === 'nova') {
    // Aprovação de notícia recém-cadastrada
    // Neste caso, nenhuma ação adicional é necessária além de marcar como aprovada
    header("Location: painelSolicitacoes.php?msg=aprovada");
    exit;
}

} elseif ($acao === 'rejeitar') {
    $stmt = $pdo->prepare("UPDATE solicitacoes SET status = 'rejeitada' WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: painelSolicitacoes.php?msg=rejeitada");
    exit;
} else {
    die('Ação inválida.');
}