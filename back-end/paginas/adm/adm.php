<?php
session_start();

if (empty($_SESSION['cadastro_concluido']) || empty($_SESSION['adm_supremo']) || !isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit();
}

$idUser = $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="pr-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - ADM</title>
</head>
<body>
    <h1>Bem-vindo à página inicial!</h1>
    <p>Você está logado como usuário com ID: <?php echo $idUser; ?></p>
    <a href="../../funcoes/logout.php">Sair</a>
    
</body>
</html>