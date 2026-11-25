<?php
    session_start();

    if (!isset($_SESSION['cadastro_concluido'])) {
        header('Location: login.php');
        exit();
    }

    $carrinho = $_SESSION['carrinho'] ?? [];
    $mensagemCarrinho = '';
    $tipoMensagemCarrinho = 'erro';
    $requisicaoAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_quantidade'])) {
        $idProduto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);

        if (!is_int($idProduto) || !isset($carrinho[$idProduto])) {
            $mensagemCarrinho = 'O produto informado não está no carrinho.';
        } elseif (!is_int($quantidade) || $quantidade < 1) {
            $mensagemCarrinho = 'Informe uma quantidade válida.';
        } else {
            $estoque = (int) ($carrinho[$idProduto]['quantidade_disponivel'] ?? 0);

            if ($quantidade > $estoque) {
                $mensagemCarrinho = 'A quantidade solicitada excede o estoque disponível.';
            } else {
                $carrinho[$idProduto]['quantidade'] = $quantidade;
                $_SESSION['carrinho'] = $carrinho;
                $_SESSION['quantidade_carrinho'] = array_sum(
                    array_map('intval', array_column($carrinho, 'quantidade'))
                );
                $mensagemCarrinho = 'Quantidade atualizada.';
                $tipoMensagemCarrinho = 'sucesso';
            }
        }

        if ($requisicaoAjax) {
            $itemAtualizado = is_int($idProduto) ? ($carrinho[$idProduto] ?? []) : [];
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'sucesso' => $tipoMensagemCarrinho === 'sucesso',
                'mensagem' => $mensagemCarrinho,
                'contador' => (int) ($_SESSION['quantidade_carrinho'] ?? 0),
                'quantidade' => (int) ($itemAtualizado['quantidade'] ?? 0),
                'subtotal' => number_format(
                    (float) ($itemAtualizado['preco'] ?? 0) * (int) ($itemAtualizado['quantidade'] ?? 0),
                    2,
                    ',',
                    '.'
                ),
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
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

    <main>
        <?php if (empty($carrinho)): ?>
            <p>Seu carrinho está vazio.</p>
        <?php else: ?>
            <section class="carrinho_itens">
                <?php foreach ($carrinho as $idProduto => $item):
                    $nome = (string) ($item['nome'] ?? 'Produto');
                    $imagem = (string) ($item['img_url'] ?? '');
                    if (trim($imagem) === '') {
                        $imagem = '../../front-end/img/produtos/produto-sem-foto.png';
                    }
                    $preco = (float) ($item['preco'] ?? 0);
                    $quantidadeAtual = (int) ($item['quantidade'] ?? 0);
                    $estoque = (int) ($item['quantidade_disponivel'] ?? 0);
                ?>
                    <section class="carrinho_item">
                        <section class="tudo">
                            <section class="parte-imagem">
                                <input type="checkbox" class="carrinho_checkbox" name="produtos[]" value="<?= (int) $idProduto ?>">
                                <img class="carrinho_imagem" src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($nome) ?>">
                            </section>    
                            <section>
                                <p class="nome-produto"><?= htmlspecialchars($nome) ?></p>
                                <form class="carrinho_form-quantidade" method="POST" data-preco="<?= $preco ?>">
                                    <input type="hidden" name="id_produto" value="<?= (int) $idProduto ?>">
                                    <input type="hidden" name="atualizar_quantidade" value="1">
                                    <button type="button" data-quantidade="diminuir" aria-label="Diminuir quantidade">-</button>
                                    <input type="number" id="quantidade-<?= (int) $idProduto ?>" name="quantidade" value="<?= $quantidadeAtual ?>" min="1" max="<?= $estoque ?>" required>
                                    <button type="button" data-quantidade="aumentar" aria-label="Aumentar quantidade">+</button>
                                </form>
                            </section>
                        </section>

                        <p class="carrinho_subtotal">Subtotal: R$ <?= number_format($preco * $quantidadeAtual, 2, ',', '.') ?></p>
                    </section>
                <?php endforeach; ?>
                </section>
        <?php endif; ?>

        <section class="total">
            <h2>Resumo da compra</h2>   
            <p>Valor compra</p>
            <form action="#" method="POST">
                <label for="cupom-desconto">Cupom de Desconto</label>
                <input name="cupom-desconto" type="text">
            </form>
        </section>
    </main>

    <script src="../../front-end/js/carrinho.js"></script>
</body>
</html>