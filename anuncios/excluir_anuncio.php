<?php
require '../verifica_login.php';
require '../conexao.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $pdo->prepare("DELETE FROM anuncio WHERE id=?")->execute([$id]);
}
header('Location: anuncio.php');
exit;