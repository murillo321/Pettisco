<?php
    session_start();

    require_once('../funcoes/conexao.php');

    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:login.php");
        exit();
    }

    $idUser = $_SESSION['id_user'];

    if (!isset($_GET['produto']) || !ctype_digit($_GET['produto'])) {
    header('Location: produtos.php');
    exit();
}

$idProduto = (int) $_GET['produto'];

$sqlProduto = $pdo->prepare(
    'SELECT * FROM produtos WHERE id_produto = :produto'
);
$sqlProduto->execute(['produto' => $idProduto]);
$infoProduto = $sqlProduto->fetch(PDO::FETCH_ASSOC);

if (!$infoProduto) {
    http_response_code(404);
    echo 'Produto não encontrado.';
    exit();
}

$mensagemSacola = '';

if (isset($_POST['adicionar_sacola'])) {
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
    $estoque = (int) $infoProduto['quantidade'];

    if ($quantidade === false || $quantidade < 1) {
        $mensagemSacola = 'Informe uma quantidade válida.';
        header('Location:visao-produtophp?produto=1');
    } elseif ($quantidade > $estoque) {
        $mensagemSacola = 'Quantidade maior que o estoque disponível.';
    } else {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        $quantidadeAtual = $_SESSION['carrinho'][$idProduto]['quantidade'] ?? 0;
        $novaQuantidade = $quantidadeAtual + $quantidade;

        if ($novaQuantidade > $estoque) {
            $mensagemSacola = 'A quantidade total na sacola excede o estoque disponível.';
        } else {
            $_SESSION['carrinho'][$idProduto] = [
                'nome' => $infoProduto['nome'],
                'preco' => $infoProduto['preco_venda'],
                'quantidade' => $novaQuantidade,
            ];
            $mensagemSacola = 'Produto adicionado à sacola.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/visao-produto.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <title>Pettisco - <?php echo htmlspecialchars($infoProduto['nome']); ?></title>
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/header.php'; ?>
    <?php
        if ($infoProduto['ativo'] == 1){
        
    ?>
    <main>
        <section class="imagens">
            <img src="<?php echo htmlspecialchars($infoProduto['img_url'] ?: '../../front-end/img/produtos/produto-sem-foto.png'); ?>" alt="Imagem do produto <?php echo htmlspecialchars($infoProduto['nome']); ?>">
        </section>
        <section class="descricao-produto">
            <h1 class="nome-produto"><?php echo htmlspecialchars($infoProduto['nome'] ?: 'Produto sem nome!'); ?></h1>
            <h2 class="descricao-produto"><?php echo htmlspecialchars($infoProduto['descricao'] ?: 'Produto sem descrição'); ?></h2>  
            <section>
                <h3>Quantidade em disponível:</h3>
                <p class="estoque"><?php echo $infoProduto['quantidade']?></p>
            </section>
            <section>
                <h4>Preço:</h4>
                <p class="preco"><?php echo 'R$ '.$infoProduto['preco_venda']; ?></p>
            </section>

            <form action="" method="POST" name="quantidade-compra">
                <button type="button" id="diminuir">-</button>
                <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="<?php echo (int) $infoProduto['quantidade']; ?>" required >
                <button type="button" id="aumentar">+</button>
                <button type="submit" name="adicionar_sacola" value="1">
                    Adicionar à sacola
                </button>
            </form>
            <?php if ($mensagemSacola !== ''): ?>
                <p id="mensagem-sacola"><?php echo htmlspecialchars($mensagemSacola); ?></p>
            <?php endif; ?>
        </section>
    <?php
        } else {
            echo'<h1>Produto indisponível</h1>';
        }
    ?>
    <a href="produtos.php">Voltar</a>
    </main>
    <script>
        const quantidade = document.getElementById('quantidade');
        const diminuir = document.getElementById('diminuir');
        const aumentar = document.getElementById('aumentar');
        const mensagem = document.getElementById('mensagem-sacola');
        if (mensagem) {
            mensagem.style.color = 'green';
        }

        if (quantidade && diminuir && aumentar) {
            diminuir.addEventListener('click', () => {
                quantidade.value = Math.max(
                    Number(quantidade.min),
                    Number(quantidade.value) - 1
                );
            });

            aumentar.addEventListener('click', () => {
                quantidade.value = Math.min(
                    Number(quantidade.max),
                    Number(quantidade.value) + 1
                );
            });
        }
    </script>
    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>