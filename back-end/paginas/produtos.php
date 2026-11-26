<?php

    session_start();
    if (!isset($_SESSION['cadastro_concluido'])) {
            header("location:login.php");
            exit();
        }
    $idUser = $_SESSION['id_user'];
    require_once('../funcoes/conexao.php');

    $sqlProdutos = $pdo->prepare('SELECT * FROM produtos');
    $sqlProdutos->execute();
    $todosProdutos = $sqlProdutos->fetchAll(PDO::FETCH_ASSOC);

    $sqlCategorias = $pdo->prepare('SELECT * FROM categoria');
    $sqlCategorias->execute();
    $todasCategorias = $sqlCategorias->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['aplicar'])) {
    $ordenar = $_POST['ordenar'] ?? '';
    $categoria = $_POST['categoria'] ?? [];


    $where = '';
    $params = [];

    if (!empty($categoria)) {
        if (count($categoria) === 1) {
            $where = 'WHERE c.identificador = ?';
            $params[] = $categoria[0];
        } else {
            $placeholders = implode(',', array_fill(0, count($categoria), '?'));
            $where = "WHERE c.identificador IN ($placeholders)";
            $params = $categoria;
        }
    }
    $orderBy = '';

    switch ($ordenar) {
        case 'menor':
            $orderBy = 'ORDER BY p.preco_venda ASC';
            break;
        case 'maior':
            $orderBy = 'ORDER BY p.preco_venda DESC';
            break;
        case 'recente':
            $orderBy = 'ORDER BY p.id_produto DESC';
            break;
        case 'antigo':
            $orderBy = 'ORDER BY p.id_produto ASC';
            break;
    }

    // --- Monta a query final ---
    $sql = "SELECT p.id_produto, p.nome, p.img_url, p.preco_venda, c.id_categoria
            FROM produtos AS p
            INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
            $where
            $orderBy";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $todosProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
    <?php require_once __DIR__ . '/../componentes/header.php'; ?>

    <form action="#" method="POST">
        <label for="ordenar">Ordenar por:</label>

        <select name="ordenar" id="ordenar">
                <option value="" disabled selected>-- Selecione --</option>
                <option value="menor">Menor Preço</option>
                <option value="maior">Maior Preço</option>
                <option value="recente">Últimos Adicionados</option>
                <option value="antigo">Primeiros Adicionados</option>
        </select>

        <label for="categoria">Categorias:</label>
        <select name="categoria[]" multiple size="1" id="categoria">
            <?php
                foreach ($todasCategorias as $categoria) {
                    $identificador = htmlspecialchars($categoria['identificador']);
                    $nomeCategoria = htmlspecialchars($categoria['nome_categoria']);
                    echo '<option value="'.$identificador.'">'.$nomeCategoria.'</option>';
                }
            ?>
        </select>
        <button name="aplicar" type="submit">Aplicar</button>
    </form>

<main class="produtos">
    
    <?php    
        foreach ($todosProdutos as $produto) {
            $nome = htmlspecialchars($produto['nome']);
            $img = htmlspecialchars($produto['img_url'] != '' ?: '../../front-end/img/produtos/produto-sem-foto.png');
            $preco = 'R$ '.$produto['preco_venda'];
            echo '<a href="visao-produto.php?produto='.$produto['id_produto'].'">' ."<img src='$img'".' alt="imagem produto">'.$nome .'<br>'.$preco;
            echo '</a>';
        }
    ?>
</main>

    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>