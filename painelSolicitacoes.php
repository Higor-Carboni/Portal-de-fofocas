<?php
require 'verifica_login.php';
require 'conexao.php';

// Verifica se é admin
if ($_SESSION['usuario_perfil'] !== 'admin') {
    die('Acesso restrito ao administrador.');
}

// Consulta todas as solicitações
$stmt = $pdo->query("
    SELECT s.*, 
           u.nome AS usuario_nome, 
           n.titulo 
    FROM solicitacoes s
    JOIN usuarios u ON s.usuario_id = u.id
    JOIN noticias n ON s.noticia_id = n.id
    ORDER BY s.data_solicitacao DESC
");
$solicitacoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Solicitações</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<main class="conteudo-dashboard">
    <h1 class="titulo-dashboard">📥 Solicitações de Usuários</h1>

    <?php if (empty($solicitacoes)): ?>
        <p>Nenhuma solicitação encontrada.</p>
    <?php else: ?>
        <table class="tabela-noticias">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Notícia</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitacoes as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['usuario_nome']) ?></td>
                        <td><?= htmlspecialchars($s['titulo']) ?></td>
                        <td><?= ucfirst($s['tipo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>
                        <td><?= ucfirst($s['status']) ?></td>
                        <td>
                            <?php if ($s['status'] === 'pendente'): ?>
                                <a href="processarSolicitacao.php?id=<?= $s['id'] ?>&acao=aprovar" class="btn-editar">Aprovar</a>
                                <a href="processarSolicitacao.php?id=<?= $s['id'] ?>&acao=rejeitar" class="btn-excluir" onclick="return confirm('Deseja rejeitar esta solicitação?')">Rejeitar</a>
                            <?php else: ?>
                                <em>Processada</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>