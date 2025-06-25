<?php
require 'verifica_login.php';
require 'conexao.php';

$id = $_GET['id'] ?? 0;

// Verifica se o usuário tem permissão para excluir a notícia
if ($_SESSION['usuario_perfil'] === 'admin') {
    // Admin pode excluir qualquer notícia
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
} else {
    // Redator: só se tiver solicitação de exclusão aprovada
    $stmt = $pdo->prepare("
        SELECT n.* FROM noticias n
        JOIN solicitacoes s ON s.noticia_id = n.id
        WHERE n.id = ?
          AND n.autor = ?
          AND s.tipo = 'excluir'
          AND s.status = 'aprovada'
        ORDER BY s.data_solicitacao DESC
        LIMIT 1
    ");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
}

$noticia = $stmt->fetch();

if (!$noticia) {
    echo "<p style='text-align:center; color:red;'>Você não tem permissão para excluir esta notícia ou a solicitação ainda não foi aprovada.</p>";
    exit;
}

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
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo">
            <div class="form-box-noticia" style="max-width: 600px;">
                <h2>Excluir Notícia</h2>
                <p>Tem certeza que deseja excluir a notícia
                    <strong><?= htmlspecialchars($noticia['titulo']) ?></strong>?
                </p>
                <form method="POST" class="form-botoes">
                    <button type="submit" style="background: #c62828;">Sim, excluir</button>
                    <button type="button" onclick="window.location.href='dashboard.php'">Voltar</button>
                </form>


            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>