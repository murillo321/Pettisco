<?php
session_start();

if (!isset($_SESSION['cadastro_concluido'])) {
    header('Location: login.php');
    exit();
}

$carrinho = $_SESSION['carrinho'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_quantidade'])) {
    $idProduto = (int) ($_POST['id_produto'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 0);

    $sucesso = false;
    $mensagem = '';

    if (!isset($carrinho[$idProduto])) {
        $mensagem = 'Produto não encontrado no carrinho.';
    } elseif ($quantidade < 1) {
        $mensagem = 'Informe uma quantidade válida.';
    } elseif ($quantidade > $carrinho[$idProduto]['quantidade_disponivel']) {
        $mensagem = 'Quantidade superior ao estoque.';
    } else {
        $carrinho[$idProduto]['quantidade'] = $quantidade;

        $_SESSION['carrinho'] = $carrinho;
        $_SESSION['quantidade_carrinho'] = array_sum(array_column($carrinho, 'quantidade'));

        $sucesso = true;
        $mensagem = 'Quantidade atualizada.';
    }

    $item = $carrinho[$idProduto] ?? [];

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem,
        'contador' => $_SESSION['quantidade_carrinho'] ?? 0,
        'quantidade' => $item['quantidade'] ?? 0,
        'subtotal' => number_format(($item['preco'] ?? 0) * ($item['quantidade'] ?? 0), 2, ',', '.')
    ]);

    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/carrinho.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon">
    <title>Pettisco - Carrinho</title>
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/header.php'; ?>

    <main class="carrinho-pagina">
        <h1>Carrinho</h1>
        <p id="mensagem-carrinho" role="status" hidden></p>

        <?php if (empty($carrinho)): ?>
            <section class="carrinho-vazio">
                <section class="parte-icone">
                    <i class="carrinho-sem-itens" data-lucide="shopping-cart" aria-hidden="true"></i>
                </section>
                <p>Seu carrinho está vazio</p>
            </section>
        <?php else: ?>
            <section class="carrinho-itens">
                <?php foreach ($carrinho as $idProduto => $item):
                    $nome = $item['nome'];
                    $imagem = $item['img_url'] ?: '../../front-end/img/produtos/produto-sem-foto.png';
                    $preco = $item['preco'];
                    $quantidadeAtual = $item['quantidade'];
                    $estoque = $item['quantidade_disponivel'];
                ?>
                    <section class="carrinho-item">
                        <section class="tudo">
                            <section class="parte-imagem">
                                <input type="checkbox" class="carrinho-checkbox" name="produtos[]" data-status="" value="<?= (int) $idProduto ?>">
                                <img class="carrinho-imagem" src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($nome) ?>">
                            </section>

                            <section>
                                <p class="nome-produto"><?= htmlspecialchars($nome) ?></p>

                                <form class="carrinho-form-quantidade" method="POST">
                                    <input type="hidden" name="id_produto" value="<?= (int) $idProduto ?>">
                                    <input type="hidden" name="atualizar_quantidade" value="1">
                                    <button type="button" data-quantidade="diminuir" aria-label="Diminuir quantidade">-</button>
                                    <input type="number" id="quantidade-<?= (int) $idProduto ?>" name="quantidade" value="<?= $quantidadeAtual ?>" min="1" max="<?= $estoque ?>" required>
                                    <button type="button" data-quantidade="aumentar" aria-label="Aumentar quantidade">+</button>
                                </form>
                            </section>
                        </section>

                        <p class="carrinho-subtotal">Subtotal: R$ <?= number_format($preco * $quantidadeAtual, 2, ',', '.') ?></p>
                    </section>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <section class="total">
            <h2>Resumo da compra</h2>
            <p>Valor compra</p>

            <a href="">Cupom de desconto</a>
            <form action="#" method="POST">
                <label for="cupom-desconto">Cupom de Desconto</label>
            </form>
        </section>
    </main>

    <script src="../../front-end/js/carrinho.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>