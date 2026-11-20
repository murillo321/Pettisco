<?php
    session_start();

    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:cadastro.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Home</title>
</head>
<body>
    <h1>Bem-vindo à página inicial!</h1>
    <p>Você está logado como usuário com ID: <?php echo $id_user; ?></p>
    <a href="../funcoes/logout.php">Sair</a>
    
</body>
</html>