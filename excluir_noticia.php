<?php
require 'verifica_login.php';
require 'conexao.php';

$id = $_GET['id'] ?? 0;

// Verifica se o usuário tem permissão para excluir a notícia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$noticia = $stmt->fetch();

if (!$noticia) die('Acesso negado.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->execute([$id]);

    // Mostra mensagem com SweetAlert2 e redireciona
    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Notícia Excluída</title>
        <link rel='stylesheet' href='css/style.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Notícia excluída com sucesso!',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        </script>
    </body>
    </html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Notícia</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <div class="form-box-noticia" style="max-width: 600px;">
                <h2>Excluir Notícia</h2>
                <p>Tem certeza que deseja excluir a notícia <strong><?= htmlspecialchars($noticia['titulo']) ?></strong>?</p>
                <form method="POST" class="form-botoes">
                    <button type="submit" style="background: #c62828;">Sim, excluir</button>
                    <a href="dashboard.php" class="btn-voltar">Cancelar</a>
                </form>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>