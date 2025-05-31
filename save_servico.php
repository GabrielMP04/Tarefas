<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $cliente = $conn->real_escape_string($_POST['cliente']);
    $tipo_servico = $conn->real_escape_string($_POST['tipo_servico']);
    $descricao = $conn->real_escape_string($_POST['descricao'] ?? '');
    $tecnico = $conn->real_escape_string($_POST['tecnico']);
    $previsao_termino = $conn->real_escape_string($_POST['previsao_termino']);
    $prioridade = $conn->real_escape_string($_POST['prioridade']);
    $status = $conn->real_escape_string($_POST['status']);
    $data_registro = date('Y-m-d H:i:s');

    // Save to MySQL database
    $stmt = $conn->prepare("INSERT INTO servicos (cliente, tipo_servico, descricao, tecnico, previsao_termino, prioridade, status, data_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $cliente, $tipo_servico, $descricao, $tecnico, $previsao_termino, $prioridade, $status, $data_registro);

    if ($stmt->execute()) {
        // Save to Google Sheet
        $data_for_sheet = [
            'Cliente' => $cliente,
            'Tipo de Serviço' => $tipo_servico,
            'Descrição' => $descricao,
            'Técnico' => $tecnico,
            'Previsão Término' => $previsao_termino,
            'Prioridade' => $prioridade,
            'Status' => $status,
            'Data Registro' => $data_registro
        ];
        $sheet_result = appendSheetData('servicos', $data_for_sheet); // Assuming 'servicos' is your sheet name

        if ($sheet_result) {
            header('Location: index.php');
            exit;
        } else {
            // Log error for Google Sheet, but still proceed as DB save was successful
            error_log("Erro ao salvar tarefa no Google Sheets para o serviço: " . $cliente);
            header('Location: index.php?sheet_error=1'); // Redirect with a flag for sheet error
            exit;
        }
    } else {
        die("Erro ao salvar tarefa no banco de dados: " . $stmt->error);
    }
    $stmt->close();
}
$conn->close();
?>