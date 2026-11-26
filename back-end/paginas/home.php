<?php
    session_start();

    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:cadastro.php");
        exit();
    }
    $idUser = $_SESSION['id_user'];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Home</title>
    <link rel="stylesheet" href="../../front-end/css/main.css">
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/header.php'; ?>
    <h1>Bem-vindo à página inicial!</h1>
    <p>Você está logado como usuário com ID: <?php echo $idUser; ?></p>
    <a href="../funcoes/logout.php">Sair</a>
    <a href="produtos.php">Página de produtos</a>
    
    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>