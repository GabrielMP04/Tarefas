<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Histórico</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Histórico de Tarefas</h1>

    <form method="GET" class="filtros">
        <input type="text" name="busca" placeholder="Buscar por cliente, serviço ou descrição" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        <input type="text" name="tecnico" placeholder="Filtrar por técnico" value="<?= htmlspecialchars($_GET['tecnico'] ?? '') ?>">

        <select name="prioridade">
            <option value="">Todas as prioridades</option>
            <option value="Baixa" <?= ($_GET['prioridade'] ?? '') === 'Baixa' ? 'selected' : '' ?>>Baixa</option>
            <option value="Média" <?= ($_GET['prioridade'] ?? '') === 'Média' ? 'selected' : '' ?>>Média</option>
            <option value="Alta" <?= ($_GET['prioridade'] ?? '') === 'Alta' ? 'selected' : '' ?>>Alta</option>
            <option value="Urgente" <?= ($_GET['prioridade'] ?? '') === 'Urgente' ? 'selected' : '' ?>>Urgente</option>
        </select>

        <label for="data_inicio_hist">Data Início Conclusão:</label>
        <input type="date" name="data_inicio" id="data_inicio_hist" value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
        <label for="data_fim_hist">Data Fim Conclusão:</label>
        <input type="date" name="data_fim" id="data_fim_hist" value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">

        <button type="submit" class="btn-filtrar">Filtrar</button>
        <button type="button" class="btn-voltar" onclick="location.href='historico.php'">Limpar Filtros</button>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Descrição</th>
                    <th>Técnico</th>
                    <th>Prioridade</th>
                    <th>Previsão Término</th>
                    <th>Data Conclusão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM servicos_concluidos WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($_GET['busca'])) {
                $busca = '%' . $_GET['busca'] . '%';
                $sql .= " AND (cliente LIKE ? OR tipo_servico LIKE ? OR descricao LIKE ?)";
                $params[] = $busca;
                $params[] = $busca;
                $params[] = $busca;
                $types .= "sss";
            }
            if (!empty($_GET['tecnico'])) {
                $sql .= " AND tecnico = ?";
                $params[] = $_GET['tecnico'];
                $types .= "s";
            }
            if (!empty($_GET['prioridade'])) {
                $sql .= " AND prioridade = ?";
                $params[] = $_GET['prioridade'];
                $types .= "s";
            }
            if (!empty($_GET['data_inicio'])) {
                $sql .= " AND data_conclusao >= ?";
                $params[] = $_GET['data_inicio'] . ' 00:00:00';
                $types .= "s";
            }
            if (!empty($_GET['data_fim'])) {
                $sql .= " AND data_conclusao <= ?";
                $params[] = $_GET['data_fim'] . ' 23:59:59';
                $types .= "s";
            }

            $sql .= " ORDER BY data_conclusao DESC";

            $stmt = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cliente']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tipo_servico']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tecnico']) . "</td>";
                        echo "<td class='prioridade-" . htmlspecialchars(strtolower($row['prioridade'])) . "'>" . htmlspecialchars($row['prioridade']) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['previsao_termino'])) . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($row['data_conclusao'])) . "</td>";
                        echo "<td>
                                <form action='delete_servico.php' method='post' style='display:inline' onsubmit=\"return confirm('Tem certeza que deseja excluir este registro permanentemente?');\">
                                    <input type='hidden' name='id' value='" . (int)$row['id'] . "'>
                                    <input type='hidden' name='from' value='historico'>
                                    <button type='submit' class='btn-delete'>Excluir</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Nenhuma tarefa encontrada no histórico</td></tr>";
                }
            } else {
                echo "<tr><td colspan='9'><p>Erro ao executar a consulta: " . htmlspecialchars($conn->error) . "</p></td></tr>";
            }

            $stmt->close();
            $conn->close();
            ?>
            </tbody>
        </table>
    </div>

    <button onclick="location.href='index.php'" class="btn-voltar">Voltar para Tarefas Ativas</button>
    <script src="script.js"></script>
</body>
</html>