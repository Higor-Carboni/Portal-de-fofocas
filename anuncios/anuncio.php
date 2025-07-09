<?php
require '../verifica_login.php';
require '../conexao.php';
$anunciantes = $pdo->query("SELECT * FROM anuncio ORDER BY data_cadastro DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Anunciantes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-cadastrar-noticia {
            background-color: #4CAF50;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-cadastrar-noticia:hover {
            background-color: #45a049;
        }

        .table img {
            max-width: 80px;
            max-height: 60px;
            border-radius: 6px;
        }

        .conteudo {
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
            margin: 20px auto;
            max-width: 98%;
        }

        .titulo-pagina {
            font-size: 1.8rem;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #232b3f;
        }

        .form-botoes {
            margin-top: 20px;
            text-align: center;
        }

        .form-botoes button {
            background-color: #6c757d;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-botoes button:hover {
            background-color: #5a6268;
        }

        .btn-acoes {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            min-width: 180px !important; /* Garante largura mínima para todos os botões */
            min-height: 44px !important;
            height: 44px !important;
        }

        .btn-acoes a {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 40px !important;
            height: 40px !important;
            border-radius: 6px !important;
            background: #f1f1f1 !important;
            transition: background 0.2s !important;
            font-size: 20px !important;
            color: #333 !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .btn-acoes a:hover {
            background: #e0e0e0 !important;
            color: #007bff !important;
        }

        .btn-acoes i {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1 !important;
            font-size: 20px !important;
        }

        .table td, .table th {
            vertical-align: middle !important;
            text-align: center !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
            height: 60px !important;
            min-width: 60px !important;
        }
    </style>
</head>
<body>
<div class="conteudo-page">
    <?php include '../includes/header.php'; ?>

    <main class="conteudo container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="titulo-pagina"><i class="fas fa-bullhorn"></i> Anunciantes</h2>
            <a href="novo_anuncio.php" class="btn-cadastrar-noticia" title="Novo Anunciante">
                <i class="fas fa-plus"></i> Novo Anunciante
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Imagem</th>
                        <th>Link</th>
                        <th>Texto</th>
                        <th>Ativo</th>
                        <th>Destaque</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($anunciantes as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nome']) ?></td>
                        <td>
                            <?php if ($a['imagem']): ?>
                                <img src="<?= htmlspecialchars($a['imagem']) ?>" alt="Imagem">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($a['link']): ?>
                                <a href="<?= htmlspecialchars($a['link']) ?>" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($a['texto']) ?></td>
                        <td>
                            <?php if ($a['ativo']): ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i> Sim</span>
                            <?php else: ?>
                                <span class="text-danger"><i class="fas fa-times-circle"></i> Não</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($a['destaque']): ?>
                                <span style="color: #ffb300;"><i class="fas fa-star"></i> Sim</span>
                            <?php else: ?>
                                <span style="color: #888;"><i class="far fa-star"></i> Não</span>
                            <?php endif; ?>
                        </td>
                        <td>R$ <?= number_format($a['valorAnuncio'], 2, ',', '.') ?></td>
                        <td>
                            <div class="btn-acoes">
                                <a href="editar_anuncio.php?id=<?= $a['id'] ?>" title="Editar">
                                    <i class="fas fa-pen fa-lg"></i>
                                </a>
                                <a href="excluir_anuncio.php?id=<?= $a['id'] ?>" onclick="return confirm('Excluir este anúncio?')" title="Excluir">
                                    <i class="fas fa-trash fa-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>    
        </div>

        <div class="form-botoes">
            <button onclick="window.location.replace('../index.php')">Voltar</button>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</div>

<!-- Scripts carregados no footer específico -->
</body>
</html>
