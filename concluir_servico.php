<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];

    // 1. Fetch the service data from 'servicos'
    $stmt_select = $conn->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $servico = $result_select->fetch_assoc();
    $stmt_select->close();

    if ($servico) {
        // 2. Insert the service data into 'servicos_concluidos'
        $stmt_insert = $conn->prepare("INSERT INTO servicos_concluidos (cliente, tipo_servico, descricao, tecnico, previsao_termino, prioridade, status, data_registro, data_conclusao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt_insert->bind_param(
            "ssssssss",
            $servico['cliente'],
            $servico['tipo_servico'],
            $servico['descricao'],
            $servico['tecnico'],
            $servico['previsao_termino'],
            $servico['prioridade'],
            'Concluído', // Set status to 'Concluído'
            $servico['data_registro']
        );

        if ($stmt_insert->execute()) {
            // 3. Delete the service from 'servicos'
            $stmt_delete = $conn->prepare("DELETE FROM servicos WHERE id = ?");
            $stmt_delete->bind_param("i", $id);
            if ($stmt_delete->execute()) {
                header('Location: index.php');
                exit;
            } else {
                die("Erro ao deletar da tabela de serviços ativos: " . $stmt_delete->error);
            }
            $stmt_delete->close();
        } else {
            die("Erro ao inserir na tabela de serviços concluídos: " . $stmt_insert->error);
        }
        $stmt_insert->close();
    } else {
        die("Tarefa não encontrada.");
    }
}
$conn->close();
?>