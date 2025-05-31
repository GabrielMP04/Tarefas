<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    // It's good practice to filter/sanitize all inputs, even with prepared statements.
    // real_escape_string is more for direct SQL queries, but harmless here.
    $cliente = $_POST['cliente'];
    $tipo_servico = $_POST['tipo_servico'];
    $descricao = $_POST['descricao'] ?? '';
    $tecnico = $_POST['tecnico'];
    $previsao_termino = $_POST['previsao_termino'];
    $prioridade = $_POST['prioridade'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE servicos SET
        cliente = ?,
        tipo_servico = ?,
        descricao = ?,
        tecnico = ?,
        previsao_termino = ?,
        prioridade = ?,
        status = ?
        WHERE id = ?");

    // "sssssssi" -> s = string, i = integer. Make sure types match
    $stmt->bind_param("sssssssi",
        $cliente,
        $tipo_servico,
        $descricao,
        $tecnico,
        $previsao_termino,
        $prioridade,
        $status,
        $id
    );

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        die("Erro ao atualizar tarefa: " . $stmt->error);
    }
    $stmt->close(); // Close statement after use
}
$conn->close();
?>