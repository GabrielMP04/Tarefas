<?php
include 'config.php';

$id = (int)($_GET['id'] ?? 0); // Ensure ID is an integer
$sql = "SELECT * FROM servicos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Tarefa não encontrada."); // Or redirect to an error page
}
$stmt->close(); // Close statement after use
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Tarefa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Editar Tarefa</h1>
    <form action="update_servico.php" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <input type="text" name="cliente" placeholder="Cliente" value="<?= htmlspecialchars($row['cliente']) ?>" required>
        <input type="text" name="tipo_servico" placeholder="Tipo de Serviço" value="<?= htmlspecialchars($row['tipo_servico']) ?>" required>
        <input type="text" name="tecnico" placeholder="Técnico" value="<?= htmlspecialchars($row['tecnico']) ?>" required>
        <label for="previsao_termino">Previsão de Término:</label>
        <input type="date" name="previsao_termino" value="<?= htmlspecialchars($row['previsao_termino']) ?>" required>

        <select name="prioridade" required>
            <option value="Média" <?= $row['prioridade'] == 'Média' ? 'selected' : '' ?>>Média</option>
            <option value="Baixa" <?= $row['prioridade'] == 'Baixa' ? 'selected' : '' ?>>Baixa</option>
            <option value="Alta" <?= $row['prioridade'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
            <option value="Urgente" <?= $row['prioridade'] == 'Urgente' ? 'selected' : '' ?>>Urgente</option>
        </select>

        <select name="status" required>
            <option value="Pendente" <?= $row['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="Em andamento" <?= $row['status'] == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="Aguardando peças" <?= $row['status'] == 'Aguardando peças' ? 'selected' : '' ?>>Aguardando peças</option>
            </select>

        <textarea name="descricao" placeholder="Descrição detalhada"><?= htmlspecialchars($row['descricao']) ?></textarea>

        <button type="submit" class="btn-save">Atualizar Tarefa</button>
    </form>
    <button onclick="location.href='index.php'" class="btn-voltar">Voltar para Tarefas</button>
</body>
</html>
<?php $conn->close(); ?>