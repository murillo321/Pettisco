<?php

    session_start();
    if (!isset($_SESSION['cadastro_concluido'])) {
            header("location:login.php");
            exit();
        }
    $idUser = $_SESSION['id_user'];
    require_once('../funcoes/conexao.php');

    $sqlPedidos = $pdo->prepare('SELECT * FROM produtos');
    $sqlPedidos->execute();
    $todosPedidos = $sqlPedidos->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/produtos.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <title>Pettisco - Produtos</title>
</head>
<body>

<main class="produtos">
    <?php

        foreach ($todosPedidos as $produto) {
            $nome = htmlspecialchars($produto['nome']);
            $img = htmlspecialchars($produto['img_url'] != '' ?: '../../front-end/img/produtos/produto-sem-foto.png');
            $preco = 'R$ '.$produto['preco_venda'];
            echo '<a href="visao-produto.php?produto='.$produto['id_produto'].'">' ."<img src='$img'".' alt="imagem produto">'.$nome .'<br>'.$preco;

            echo '</a>';
        }
    ?>
</main>

</body>
</html>