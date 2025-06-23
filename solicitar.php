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
    die('Acesso negado à notícia.');
}

// Registra a solicitação
$stmt = $pdo->prepare("INSERT INTO solicitacoes (noticia_id, usuario_id, tipo) VALUES (?, ?, ?)");
$stmt->execute([$noticia_id, $usuario_id, $tipo]);

header("Location: dashboard.php?sucesso=solicitacao_enviada");
exit;