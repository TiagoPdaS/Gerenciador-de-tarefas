<?php
session_start();
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = array();
}

// Validação e sanitização de entrada para adicionar ou editar tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_name'])) {
    $task_name = filter_input(INPUT_POST, 'task_name', FILTER_SANITIZE_STRING);
    if ($task_name && $task_name !== "") {
        if (isset($_POST['edit_key'])) { // Editar tarefa existente
            $edit_key = filter_input(INPUT_POST, 'edit_key', FILTER_VALIDATE_INT);
            if ($edit_key !== false && isset($_SESSION['tasks'][$edit_key])) {
                $_SESSION['tasks'][$edit_key] = $task_name;
                $_SESSION['message'] = "Tarefa editada com sucesso!";
            }
        } else { // Adicionar nova tarefa
            $_SESSION['tasks'][] = $task_name;
            $_SESSION['message'] = "Tarefa adicionada com sucesso!";
        }
    } else {
        $_SESSION['message'] = "O campo tarefa não pode ser vazio.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Limpar todas as tarefas com confirmação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear'])) {
    $_SESSION['tasks'] = array();
    $_SESSION['message'] = "Todas as tarefas foram removidas.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Remover tarefa específica
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['key'])) {
    $key = filter_input(INPUT_POST, 'key', FILTER_VALIDATE_INT);
    if ($key !== false && isset($_SESSION['tasks'][$key])) {
        array_splice($_SESSION['tasks'], $key, 1);
        $_SESSION['message'] = "Tarefa removida com sucesso!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz@1,6..12&family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <title>Gerenciador de Tarefas</title>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gerenciador de Tarefas</h1>
        </div>

        <div class="form">
            <form action="" method="post">
                <label for="task_name">Tarefa:</label>
                <input type="text" name="task_name" id="task_name" placeholder="Nome da tarefa" aria-label="Nome da tarefa">
                <button type="submit">Cadastrar</button>
            </form>

            <?php
            if (isset($_SESSION['message'])) {
                echo "<p style='color:#ef5350;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
                unset($_SESSION['message']);
            }
            ?>
        </div>

        <div class="separator"></div>

        <div class="list-tasks">
            <?php if (!empty($_SESSION['tasks'])): ?>
                <ul>
                    <?php foreach ($_SESSION['tasks'] as $key => $task): ?>
                        <li>
                            <form action="" method="post" style="display:flex; width:100%;">
                                <input type="text" name="task_name" value="<?= htmlspecialchars($task) ?>" style="flex-grow:1; margin-right:8px;">
                                <input type="hidden" name="edit_key" value="<?= $key ?>">
                                <button type="submit" class="btn-edit" aria-label="Salvar edição">Salvar</button>
                                <button type="submit" formaction="" name="key" value="<?= $key ?>" class="btn-clear" aria-label="Remover tarefa">Remover</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="" method="post">
                <input type="hidden" name="clear" value="1">
                <button type="submit" class="btn-clear" onclick="return confirm('Deseja realmente limpar todas as tarefas?');" aria-label="Limpar todas as tarefas">Limpar tarefas</button>
            </form>
        </div>

        <div class="footer">
            <p>Desenvolvido por @TiagoPdaS</p>
        </div>
    </div>
</body>
</html>