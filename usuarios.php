<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'verificaAdmin.php';

// Excluir usuário
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    if ($idExcluir != $_SESSION['usuario_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$idExcluir]);
    }
    header("Location: usuarios.php");
    exit;
}

// Atualizar perfil
if (isset($_GET['mudar_perfil']) && isset($_GET['novo'])) {
    $id = intval($_GET['mudar_perfil']);
    $novoPerfil = $_GET['novo'] === 'admin' ? 'admin' : 'comum';
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $pdo->prepare("UPDATE usuarios SET perfil = ? WHERE id = ?");
        $stmt->execute([$novoPerfil, $id]);
    }
    header("Location: usuarios.php");
    exit;
}

// Buscar todos os usuários
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/usuarios.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <section style="width: 100%;">
                <h2 class="titulo-pagina">👥 Usuários Cadastrados</h2>
                <div class="tabela-container">
                    <div class="form-botoes">
                        <button id="btnExportPdf" type="button">
                            <i class="fas fa-file-pdf"></i> Exportar em PDF
                        </button>
                    </div>
                    <table class="usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td data-label="ID"><?= $u['id'] ?></td>
                                    <td data-label="Nome"><?= htmlspecialchars($u['nome']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                                    <td data-label="Perfil"><?= htmlspecialchars($u['perfil']) ?></td>
                                    <td data-label="Ações">
                                        <div class="btn-acoes">
                                            <a href="editarUsuario.php?id=<?= $u['id'] ?>" class="btn-editar"
                                                title="Editar"><i class="fas fa-pen"></i></a>

                                            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                                <a href="usuarios.php?excluir=<?= $u['id'] ?>" class="btn-excluir"
                                                    title="Excluir" onclick="return confirm('Excluir este usuário?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>

                                                <?php if ($u['perfil'] === 'admin'): ?>
                                                    <a href="usuarios.php?mudar_perfil=<?= $u['id'] ?>&novo=comum"
                                                        class="btn-rebaixar" title="Rebaixar para comum">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="usuarios.php?mudar_perfil=<?= $u['id'] ?>&novo=admin"
                                                        class="btn-promover" title="Tornar admin">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="text-align: right; margin: -12px 24px 8px auto;">
                        <a href="cadastroUsuario.php" class="btn-cadastrar-usuario" title="Novo Usuário">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>

                    <div class="form-botoes">
                        <button onclick="window.location.href='dashboard.php'">Voltar</button>
                    </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>