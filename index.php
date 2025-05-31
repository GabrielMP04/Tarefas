<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Painel de Tarefas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Tarefas em Andamento</h1>

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
        <select name="status">
            <option value="">Todos os status</option>
            <option value="Pendente" <?= ($_GET['status'] ?? '') === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="Em andamento" <?= ($_GET['status'] ?? '') === 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="Aguardando peças" <?= ($_GET['status'] ?? '') === 'Aguardando peças' ? 'selected' : '' ?>>Aguardando peças</option>
            </select>
        <label for="data_inicio_reg">Data Início Registro:</label>
        <input type="date" name="data_inicio_registro" id="data_inicio_reg" value="<?= htmlspecialchars($_GET['data_inicio_registro'] ?? '') ?>">
        <label for="data_fim_reg">Data Fim Registro:</label>
        <input type="date" name="data_fim_registro" id="data_fim_reg" value="<?= htmlspecialchars($_GET['data_fim_registro'] ?? '') ?>">

        <label for="previsao_termino_inicio">Previsão Término Início:</label>
        <input type="date" name="previsao_termino_inicio" id="previsao_termino_inicio" value="<?= htmlspecialchars($_GET['previsao_termino_inicio'] ?? '') ?>">
        <label for="previsao_termino_fim">Previsão Término Fim:</label>
        <input type="date" name="previsao_termino_fim" id="previsao_termino_fim" value="<?= htmlspecialchars($_GET['previsao_termino_fim'] ?? '') ?>">

        <button type="submit" class="btn-filtrar">Filtrar</button>
        <button type="button" class="btn-voltar" onclick="location.href='index.php'">Limpar Filtros</button>
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
                    <th>Status</th>
                    <th>Previsão Término</th>
                    <th>Data Registro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM servicos WHERE 1=1";
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
            if (!empty($_GET['status'])) {
                $sql .= " AND status = ?";
                $params[] = $_GET['status'];
                $types .= "s";
            }
            if (!empty($_GET['data_inicio_registro'])) {
                $sql .= " AND data_registro >= ?";
                $params[] = $_GET['data_inicio_registro'] . ' 00:00:00';
                $types .= "s";
            }
            if (!empty($_GET['data_fim_registro'])) {
                $sql .= " AND data_registro <= ?";
                $params[] = $_GET['data_fim_registro'] . ' 23:59:59';
                $types .= "s";
            }
            if (!empty($_GET['previsao_termino_inicio'])) {
                $sql .= " AND previsao_termino >= ?";
                $params[] = $_GET['previsao_termino_inicio'];
                $types .= "s";
            }
            if (!empty($_GET['previsao_termino_fim'])) {
                $sql .= " AND previsao_termino <= ?";
                $params[] = $_GET['previsao_termino_fim'];
                $types .= "s";
            }

            $sql .= " ORDER BY data_registro DESC"; // Order by most recent registration

            $stmt = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($servico = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($servico['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($servico['cliente']) . "</td>";
                        echo "<td>" . htmlspecialchars($servico['tipo_servico']) . "</td>";
                        echo "<td>" . htmlspecialchars($servico['descricao']) . "</td>";
                        echo "<td>" . htmlspecialchars($servico['tecnico']) . "</td>";
                        echo "<td class='prioridade-" . htmlspecialchars(strtolower($servico['prioridade'])) . "'>" . htmlspecialchars($servico['prioridade']) . "</td>";
                        echo "<td class='status-" . htmlspecialchars(str_replace(' ', '-', strtolower($servico['status']))) . "'>" . htmlspecialchars($servico['status']) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($servico['previsao_termino'])) . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($servico['data_registro'])) . "</td>";
                        echo "<td>
                                <form action='editar_servico.php' method='get' style='display:inline'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($servico['id']) . "'>
                                    <button type='submit' class='btn-save'>Editar</button>
                                </form>
                                <form action='concluir_servico.php' method='post' style='display:inline' onsubmit=\"return confirm('Deseja marcar esta tarefa como concluída?');\">
                                    <input type='hidden' name='id' value='" . htmlspecialchars($servico['id']) . "'>
                                    <button type='submit' class='btn-concluir'>Concluir</button>
                                </form>
                                <form action='delete_servico.php' method='post' style='display:inline' onsubmit=\"return confirm('Tem certeza que deseja excluir este registro permanentemente?');\">
                                    <input type='hidden' name='id' value='" . htmlspecialchars($servico['id']) . "'>
                                    <input type='hidden' name='from' value='index'>
                                    <button type='submit' class='btn-delete'>Excluir</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Nenhuma tarefa encontrada</td></tr>";
                }
            } else {
                echo "<tr><td colspan='10'><p>Erro ao executar a consulta: " . htmlspecialchars($conn->error) . "</p></td></tr>";
            }
            $stmt->close();
            $conn->close();
            ?>
            </tbody>
        </table>
    </div>

    <button onclick="location.href='add_servico.php'" class="btn-add">+ Nova Tarefa</button>
    <button onclick="location.href='historico.php'" class="btn-historico">Histórico</button>

    <script src="script.js"></script>
</body>
</html>