<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Nova Tarefa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Adicionar Nova Tarefa</h1>
    <form action="save_servico.php" method="post">
        <input type="text" name="cliente" placeholder="Cliente" required>
        <input type="text" name="tipo_servico" placeholder="Tipo de Serviço" required>
        <input type="text" name="tecnico" placeholder="Técnico" required>
        <label for="previsao_termino">Previsão de Término:</label>
        <input type="date" name="previsao_termino" required>

        <select name="prioridade" required>
            <option value="">Selecione a Prioridade</option>
            <option value="Baixa">Baixa</option>
            <option value="Média">Média</option>
            <option value="Alta">Alta</option>
            <option value="Urgente">Urgente</option>
        </select>

        <select name="status" required>
            <option value="">Selecione o Status</option>
            <option value="Pendente">Pendente</option>
            <option value="Em andamento">Em andamento</option>
            <option value="Aguardando peças">Aguardando peças</option>
        </select>

        <textarea name="descricao" placeholder="Descrição detalhada"></textarea>

        <button type="submit" class="btn-add">Salvar Tarefa</button>
    </form>
    <button onclick="location.href='index.php'" class="btn-voltar">Voltar para Tarefas</button>
</body>
</html>