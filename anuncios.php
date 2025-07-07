<?php
require 'verifica_login.php';
require 'conexao.php';
require_once 'verificaAdmin.php';

// Excluir anúncio
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    $stmt = $pdo->prepare("DELETE FROM anuncios WHERE id = ?");
    $stmt->execute([$idExcluir]);
    header("Location: anuncios.php?msg=excluido");
    exit;
}

// Ativar/Desativar anúncio
if (isset($_GET['toggle_ativo'])) {
    $id = intval($_GET['toggle_ativo']);
    $stmt = $pdo->prepare("UPDATE anuncios SET ativo = NOT ativo WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: anuncios.php?msg=status_alterado");
    exit;
}

// Toggle destaque
if (isset($_GET['toggle_destaque'])) {
    $id = intval($_GET['toggle_destaque']);
    $stmt = $pdo->prepare("UPDATE anuncios SET destaque = NOT destaque WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: anuncios.php?msg=destaque_alterado");
    exit;
}

// Buscar todos os anúncios
$stmt = $pdo->query("SELECT * FROM anuncios ORDER BY data_cadastro DESC");
$anuncios = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Anúncios</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/usuarios.css">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <section style="width: 100%;">
                <h2 class="titulo-pagina">📢 Gerenciar Anúncios</h2>
                
                <?php if ($msg): ?>
                    <?php
                    $mensagem = '';
                    if ($msg === 'excluido') {
                        $mensagem = 'Anúncio excluído com sucesso!';
                    } elseif ($msg === 'status_alterado') {
                        $mensagem = 'Status do anúncio alterado!';
                    } elseif ($msg === 'destaque_alterado') {
                        $mensagem = 'Destaque do anúncio alterado!';
                    }
                    ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: '<?= $mensagem ?>',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    </script>
                <?php endif; ?>

                <div class="tabela-container">
                    <table class="usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagem</th>
                                <th>Nome</th>
                                <th>Texto</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Destaque</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anuncios as $a): ?>
                                <tr>
                                    <td data-label="ID"><?= $a['id'] ?></td>
                                    <td data-label="Imagem">
                                        <img src="<?= htmlspecialchars($a['imagem']) ?>" 
                                             alt="<?= htmlspecialchars($a['nome']) ?>" 
                                             style="width: 50px; height: 30px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td data-label="Nome"><?= htmlspecialchars($a['nome']) ?></td>
                                    <td data-label="Texto"><?= htmlspecialchars($a['texto']) ?></td>
                                    <td data-label="Valor">R$ <?= number_format($a['valorAnuncio'], 2, ',', '.') ?></td>
                                    <td data-label="Status">
                                        <span class="status-badge <?= $a['ativo'] ? 'ativo' : 'inativo' ?>">
                                            <?= $a['ativo'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td data-label="Destaque">
                                        <span class="status-badge <?= $a['destaque'] ? 'destaque' : 'normal' ?>">
                                            <?= $a['destaque'] ? 'Destaque' : 'Normal' ?>
                                        </span>
                                    </td>
                                    <td data-label="Data"><?= date('d/m/Y H:i', strtotime($a['data_cadastro'])) ?></td>
                                    <td data-label="Ações">
                                        <div class="btn-acoes">
                                            <a href="editarAnuncio.php?id=<?= $a['id'] ?>" class="btn-editar"
                                                title="Editar"><i class="fas fa-pen"></i></a>

                                            <a href="anuncios.php?toggle_ativo=<?= $a['id'] ?>" 
                                               class="btn-toggle <?= $a['ativo'] ? 'btn-desativar' : 'btn-ativar' ?>"
                                               title="<?= $a['ativo'] ? 'Desativar' : 'Ativar' ?>"
                                               onclick="return confirm('<?= $a['ativo'] ? 'Desativar' : 'Ativar' ?> este anúncio?')">
                                                <i class="fas fa-<?= $a['ativo'] ? 'eye-slash' : 'eye' ?>"></i>
                                            </a>

                                            <a href="anuncios.php?toggle_destaque=<?= $a['id'] ?>" 
                                               class="btn-toggle <?= $a['destaque'] ? 'btn-remove-destaque' : 'btn-add-destaque' ?>"
                                               title="<?= $a['destaque'] ? 'Remover destaque' : 'Adicionar destaque' ?>"
                                               onclick="return confirm('<?= $a['destaque'] ? 'Remover' : 'Adicionar' ?> destaque deste anúncio?')">
                                                <i class="fas fa-<?= $a['destaque'] ? 'star' : 'star-o' ?>"></i>
                                            </a>

                                            <a href="anuncios.php?excluir=<?= $a['id'] ?>" class="btn-excluir"
                                                title="Excluir" onclick="return confirm('Excluir este anúncio?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div style="text-align: right; margin: -12px 24px 8px auto;">
                        <a href="cadastroAnuncio.php" class="btn-cadastrar-usuario" title="Novo Anúncio">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>

                    <div class="form-botoes">
                        <button onclick="window.location.href='dashboard.php'">Voltar</button>
                    </div>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html> 