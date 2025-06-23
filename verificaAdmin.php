<?php
// Inicia a sessão caso ainda não tenha sido iniciada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'admin') {
    // Redireciona para a página inicial caso não tenha permissão
    header('Location: index.php');
    exit;
}
?>