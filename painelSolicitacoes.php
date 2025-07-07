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
    <link rel="stylesheet" href="css/headerAdmin.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="conteudo-page">
        <?php include 'includes/header.php'; ?>

        <main class="conteudo-dashboard">
            <h1 class="titulo-dashboard">📥 Solicitações de Usuários</h1>

            <?php if (empty($solicitacoes)): ?>
                <p>Nenhuma solicitação encontrada.</p>
            <?php else: ?>
                <div class="form-botoes">
                    <button id="btnExportPdf" type="button">
                        <i class="fas fa-file-pdf"></i> Exportar em PDF
                    </button>
                </div>

                <!-- Tabela Desktop -->
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
                                        <a href="processarSolicitacao.php?id=<?= $s['id'] ?>&acao=aprovar"
                                            class="btn-editar">Aprovar</a>
                                        <a href="processarSolicitacao.php?id=<?= $s['id'] ?>&acao=rejeitar" class="btn-excluir"
                                            onclick="return confirm('Deseja rejeitar esta solicitação?')">Rejeitar</a>
                                    <?php else: ?>
                                        <em>Processada</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- CARDS MOBILE -->
                <div class="cards-lista-solicitacao">
                    <?php foreach ($solicitacoes as $s): ?>
                        <div class="card-tabela">
                            <div><strong>Usuário:</strong> <?= htmlspecialchars($s['usuario_nome']) ?></div>
                            <div><strong>Notícia:</strong> <?= htmlspecialchars($s['titulo']) ?></div>
                            <div><strong>Tipo:</strong> <?= ucfirst($s['tipo']) ?></div>
                            <div><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></div>
                            <div><strong>Status:</strong> <?= ucfirst($s['status']) ?></div>
                            <div>
                                <strong>Ações:</strong>
                                <?php if ($s['status'] === 'pendente'): ?>
                                    <a href="processarSolicitacao.php?id=<?= $s['id'] ?>&acao=aprovar"
                                        class="btn-editar">Aprovar</a>
                                    <a href="processarSolicitacao.php?id=<?= $s['id'] ?>&acao=rejeitar" class="btn-excluir"
                                        onclick="return confirm('Deseja rejeitar esta solicitação?')">Rejeitar</a>
                                <?php else: ?>
                                    <em>Processada</em>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- ======= FIM DOS CARDS ======= -->

            <?php endif; ?>

            <!-- Botão Voltar -->
            <div class="form-botoes">
                <button onclick="window.location.href='dashboard.php'">Voltar</button>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>

    <script>
        document.getElementById('btnExportPdf')?.addEventListener('click', function () {
            var tabela = document.querySelector('.tabela-noticias');
            if (!tabela) return;

            // CAPTURA OS DADOS DA TABELA
            var headers = [];
            tabela.querySelectorAll('thead th').forEach(th => headers.push(th.innerText));
            var data = [];
            tabela.querySelectorAll('tbody tr').forEach(tr => {
                var row = [];
                tr.querySelectorAll('td').forEach(td => row.push(td.innerText));
                if (row.length) data.push(row);
            });

            const { jsPDF } = window.jspdf;
            var doc = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });

            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const headerHeight = 90;
            const footerHeight = 35;

            // Centralizar logo + título (calcula largura total)
            const logoW = 48;
            const logoH = 48;
            const title = "Portal de Notícias";
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(26);
            // Mede largura do texto
            const textWidth = doc.getTextWidth(title);
            // Espaço entre logo e texto
            const space = 18;
            // Largura total do grupo (logo + espaço + texto)
            const totalWidth = logoW + space + textWidth;
            // Posição X inicial do grupo para centralizar
            const groupX = (pageWidth - totalWidth) / 2;
            // Posição Y para centralizar verticalmente o header
            const groupY = 46;

            // --------- FUNÇÕES DE DESENHO HEADER/FOOTER ---------
            function drawHeader() {
                // Fundo do header
                doc.setFillColor("#f5f6fa");
                doc.rect(0, 0, pageWidth, headerHeight, 'F');
                // Logo mais à esquerda (ex: X = 38)
                doc.addImage('img/logoFofoca500.png', 'PNG', 38, groupY - logoH / 2, logoW, logoH);
                // Título centralizado (sem deslocar por causa do logo)
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(26);
                doc.setTextColor("#232b3f");
                doc.text(title, pageWidth / 2, groupY + 8, { align: "center" });
            }

            function drawFooter() {
                doc.setFillColor("#f5f6fa");
                doc.rect(0, pageHeight - footerHeight, pageWidth, footerHeight, 'F');
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(12);
                doc.setTextColor("#232b3f");
                doc.text(
                    '© ' + new Date().getFullYear() + ' Fofoquei News. Todos os direitos reservados.',
                    pageWidth / 2,
                    pageHeight - 15,
                    { align: "center" }
                );
            }

            // --------- HEADER/FOOTER PRIMEIRA PÁGINA ---------
            drawHeader();

            // Título do relatório (logo abaixo do header)
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(16);
            doc.setTextColor("#3a4666");
            const titleY = headerHeight + 28;
            doc.text("Relatório de Solicitações", pageWidth / 2, titleY, { align: "center" });

            // --------- GERA TABELA ---------
            doc.autoTable({
                head: [headers],
                body: data,
                startY: titleY + 12,
                margin: { left: 30, right: 30 },
                styles: {
                    font: 'helvetica',
                    fontSize: 10,
                    textColor: "#232b3f",
                    fillColor: "#fff",
                    lineWidth: 0.3,
                    lineColor: "#cfd8dc",
                    halign: 'left'
                },
                headStyles: {
                    fillColor: "#23304a",
                    textColor: "#fff",
                    fontStyle: 'bold',
                    halign: 'center',
                    lineWidth: 0.8,
                    lineColor: "#23304a"
                },
                alternateRowStyles: {
                    fillColor: "#f5f6fa"
                },
                tableLineColor: "#b0bec5",
                tableLineWidth: 0.6,
                // HEADER/FOOTER NAS OUTRAS PÁGINAS
                didDrawPage: function (data) {
                    drawHeader();
                    // Título do relatório também em todas páginas
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(16);
                    doc.setTextColor("#3a4666");
                    doc.text("Relatório de Solicitações", pageWidth / 2, headerHeight + 28, { align: "center" });

                    drawFooter();
                }
            });

            // RODAPÉ ÚLTIMA PÁGINA
            drawFooter();

            doc.save('relatorio-solicitacoes.pdf');
        });
    </script>
</body>

</html>