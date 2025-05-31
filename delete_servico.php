<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $from = $_POST['from'] ?? 'index'; // Default to 'index' if not set

    if ($from === 'historico') {
        $stmt = $conn->prepare("DELETE FROM servicos_concluidos WHERE id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM servicos WHERE id = ?");
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($from === 'historico') {
            header('Location: historico.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        // Log error instead of just dying for production, but for debugging, die is fine.
        die("Erro ao deletar: " . $stmt->error);
    }
    $stmt->close(); // Close statement after use
}
$conn->close();
?>