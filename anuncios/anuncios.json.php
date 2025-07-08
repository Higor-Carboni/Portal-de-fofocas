<?php
require_once '../conexao.php';
header('Content-Type: application/json');
$anuncios = $pdo->query("SELECT * FROM anuncio WHERE ativo = 1")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($anuncios);