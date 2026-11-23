<?php
    //session_start();
    //verificar id do user e se é adm

    require_once(__DIR__ . '/../../funcoes/conexao.php');

    if(isset($_POST['cadastrar'])){
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $qntd = $_POST['qntd'];
        $preco_venda = $_POST['preco_venda'];
        $preco_custo = $_POST['preco_custo'];
        $tipo = $_POST['tipo'];
        $id_categoria = $_POST['id_categoria'];
        
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $img = $_FILES['img'];

            $tiposPermitidos = ['image/jpeg' => 'jpg','image/png' => 'png','image/webp' => 'webp'];
            $tipoImagem = mime_content_type($img['tmp_name']);

            if (!isset($tiposPermitidos[$tipoImagem])) {
                echo "<script>alert('Formato de imagem inválido!');</script>";
                exit;
            }

            $extensao = $tiposPermitidos[$tipoImagem];
            $nomeImagem = $nome . '.' . $extensao;

            $pastaDestino = __DIR__ . '/../../../front-end/img/produtos/';

            if (move_uploaded_file($img['tmp_name'],$pastaDestino . $nomeImagem)) {
                $img_url = 'img/produtos/' . $nomeImagem;
            } else {
                echo '<script>alert("Erro ao salvar a imagem!");</script>';
                exit;
            }

        } else {
            echo '<script>alert("Selecine uma imagem!");</script>';
            exit;
        }

        if(empty($nome) || empty($descricao) || empty($qntd) || empty($preco_venda) || empty($preco_custo) || empty($tipo) || empty($id_categoria) || empty($img)){
            echo '<script>alert("Preencha todos os campos!")</script>';
            exit;
        }
        
        try{
            $insert = "insert into produtos (nome,descricao,quantidade,preco_venda,preco_custo,tipo,id_categoria,img_url) values (:nome, :descricao, :qntd, :preco_venda, :preco_custo, :tipo, :id_categoria, :img_url);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':qntd' => $qntd,
                ':preco_venda' => $preco_venda,
                ':preco_custo' => $preco_custo,
                ':tipo' => $tipo,
                ':id_categoria' => $id_categoria,
                ':img_url' => $img_url
            ]);

            header('location:produtos.php');

        } catch (PDOException $e){
            echo 'Erro ao inserir: '. $e->getMessage();
        }
    }

    if (isset($_POST['excluir'])) {
        $id_produto = $_POST['id_produto'];

        try {
            $select = "select img_url from produtos where id_produto = :id_produto";
            $stmt = $pdo->prepare($select);
            $stmt->execute([':id_produto' => $id_produto]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                $delete = "delete from produtos where id_produto = :id_produto";
                $stmt = $pdo->prepare($delete);
                $stmt->execute([':id_produto' => $id_produto]);

                $caminhoImagem = __DIR__ . '/../../../front-end/' . $produto['img_url'];

                if (!empty($produto['img_url']) && file_exists($caminhoImagem)) {
                    unlink($caminhoImagem);
                }
            }

            header('Location: produtos.php');
            exit;

        } catch (PDOException $e) {
            echo 'Erro ao excluir: ' . $e->getMessage();
        }
    }

    if (isset($_POST['editar'])) {
        $id_produto = $_POST['id_produto'];
        $nome = $_POST['novo_nome'];
        $descricao = $_POST['nova_descricao'];
        $qntd = $_POST['nova_qntd'];
        $preco_custo = $_POST['novo_preco_custo'];
        $preco_venda = $_POST['novo_preco_venda'];
        $tipo = trim($_POST['novo_tipo']);
        $id_categoria = $_POST['id_categoria'];
        $ativo = $_POST['ativo'];

        $novaImagem = null;
        $caminhoNovaImagem = null;

        try {
            $select = "select img_url from produtos where id_produto = :id_produto";
            $stmt = $pdo->prepare($select);
            $stmt->execute([':id_produto' => $id_produto]);
            $produtoAtual = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produtoAtual) {
                echo '<script>alert("Produto não encontrado!")</script>';
                exit;
            }

            if (isset($_FILES['img']) && $_FILES['img']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['img']['error'] !== UPLOAD_ERR_OK) {
                    echo '<script>alert("Erro no envio da imagem!")</script>';
                    exit;
                }

                $tiposPermitidos = ['image/jpeg' => 'jpg','image/png' => 'png','image/webp' => 'webp'];
                $tipoImagem = mime_content_type($_FILES['img']['tmp_name']);

                if (!isset($tiposPermitidos[$tipoImagem])) {
                    echo "<script>alert('Formato de imagem inválido!');</script>";
                    exit;
                }

                $extensao = $tiposPermitidos[$tipoImagem];
                $nomeImagem = $nome . '.' . $extensao;

                $pastaDestino = __DIR__ . '/../../../front-end/img/produtos/';

                /*if (move_uploaded_file($_FILES['img']['tmp_name'], $caminhoNovaImagem)) {
                    $novaImagem = 'img/produtos/' . $nomeImagem;
                } else {
                    echo '<script>alert("Erro ao salvar a imagem!");</script>';
                    exit;
                }*/

                if (move_uploaded_file($_FILES['img']['tmp_name'],$pastaDestino . $nomeImagem)) {
                    $img_url = 'img/produtos/' . $nomeImagem;
                } else {
                    echo '<script>alert("Erro ao salvar a imagem!");</script>';
                    exit;
                }

                $caminhoNovaImagem = $pastaDestino . $nomeImagem;

                $novaImagem = 'img/produtos/' . $nomeImagem;
            }

            $img_url = $novaImagem ?? $produtoAtual['img_url'];

            $update = "update produtos set nome = :nome, descricao = :descricao,
                        quantidade = :qntd, preco_custo = :preco_custo, preco_venda = :preco_venda,
                        tipo = :tipo, id_categoria = :id_categoria, ativo = :ativo, img_url = :img_url
                        where id_produto = :id_produto";
            $stmt = $pdo->prepare($update);
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':qntd' => $qntd,
                ':preco_custo' => $preco_custo,
                ':preco_venda' => $preco_venda,
                ':tipo' => $tipo,
                ':id_categoria' => $id_categoria,
                ':ativo' => $ativo,
                ':img_url' => $img_url,
                ':id_produto' => $id_produto
            ]);


            if ($novaImagem !== null && !empty($produtoAtual['img_url'])) {
                $pastaDestino = __DIR__ . '/../../../front-end/img/produtos/';
                $caminhoAntigo = $pastaDestino . basename($produtoAtual['img_url']);
                if (is_file($caminhoAntigo)) {
                    unlink($caminhoAntigo);
                }
            }

            header('Location: produtos.php');
            exit;

        } catch (PDOException $e) {
            if ($caminhoNovaImagem !== null && is_file($caminhoNovaImagem)) {
                unlink($caminhoNovaImagem);
            }
            echo "Erro ao editar produto: " . getMessage();
        }
    }

    try{
        $select_produtos = "select p.*, c.nome_categoria from produtos p inner join categoria c on p.id_categoria = c.id_categoria;";
        $stmt = $pdo->prepare($select_produtos);
        $stmt->execute();
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $select_categorias = "select id_categoria, nome_categoria from categoria where ativo = 1 order by nome_categoria;";
        $stmt = $pdo->prepare($select_categorias);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e){
        echo 'Erro ao consultar: '. $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Produtos</title>
</head>
<body>
    <header>
        <h1>
            Central de Produtos
        </h1>
    </header>
    <main>
        <section>
            <button id="abrirModal">Cadastrar Produtos +</button>
        </section>
        <dialog id="modal">
            <button id="fecharModal">X</button>
            <h3>Cadastre um produto</h3>
            <form action="#" method="POST" enctype="multipart/form-data">
                <label for="img">Selecione uma imagem:</label><br><!--mostrar pré visualização da img com js-->
                <input type="file" name="img" id="img" accept="image/png, image/jpeg, image/webp" required><br>

                <label for="nome">Digite um nome:</label><br>
                <input type="text" id="nome" name="nome" required><br>

                <label for="descricao">Digite a descrição:</label><br>
                <input type="textarea" id="descricao" name="descricao" required><br>

                <label for="qntd">Digite a quantidade:</label><br>
                <input type="number" id="qntd" name="qntd" required><br>

                <label for="preco_custo">Digite o custo:</label><br><!--talvez fazer um js maneiro pra deixar como money-->
                <input type="number" step="0.01" id="preco_custo" name="preco_custo" required><br>

                <label for="preco_venda">Digite o preço de venda:</label><br><!--talvez fazer um js maneiro pra deixar como money-->
                <input type="number" step="0.01" id="preco_venda" name="preco_venda" required><br>

                <label for="tipo">Digite o tipo:</label><br>
                <input type="text" id="tipo" name="tipo" required><br>

                <label for="categoria">Categoria do produto:</label><br>
                <select name="id_categoria" id="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id_categoria'] ?>">
                            <?= htmlspecialchars($categoria['nome_categoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <input type="submit" name="cadastrar" value="Cadastrar">
            </form>
        </dialog>
        <br>
        <section>
            <table>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Imagem</td>
                        <td>Nome</td>
                        <td>Descrição</td>
                        <td>Quantidade</td>
                        <td>Custo</td>
                        <td>Preço de Venda</td>
                        <td>Lucro</td>
                        <td>Tipo</td>
                        <td>Categoria</td>
                        <td>Status</td>
                        <td>Excluir</td>
                        <td>Editar</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($produtos as $produto):
                        if($produto['ativo'] === 1){
                            $status = 'Disponível';
                        } else {
                            $status = 'Indisponível';
                        }

                         $lucro = $produto['preco_venda'] - $produto['preco_custo'];
                    ?>
                        <tr>
                            <td> <?= htmlspecialchars($produto['id_produto']) ?> </td>
                            <td> <img src="../../../front-end/<?= htmlspecialchars($produto['img_url']) ?>" alt="Foto do produto <?= $produto['nome'] ?>"> </td>
                            <td> <?= htmlspecialchars($produto['nome']) ?> </td>
                            <td> <?= htmlspecialchars($produto['descricao']) ?> </td>
                            <td> <?= htmlspecialchars($produto['quantidade']) ?> </td>
                            <td>R$ <?= number_format($produto['preco_custo'], 2, ',', '.') ?> </td>
                            <td>R$ <?= number_format($produto['preco_venda'], 2, ',', '.') ?> </td>
                            <td>R$ <?= number_format($lucro, 2, ',', '.') ?> </td>
                            <td> <?= htmlspecialchars($produto['tipo']) ?> </td>
                            <td> <?= htmlspecialchars($produto['nome_categoria']) ?> </td>
                            <td> <?= htmlspecialchars($status) ?> </td>
                            <td>
                                <form action="#" method="POST">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
                                    <button type="submit" name="excluir">Excluir</button>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="abrirEditar"
                                    data-id="<?= htmlspecialchars($produto['id_produto']) ?>"
                                    data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                                    data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                                    data-qntd="<?= htmlspecialchars($produto['quantidade']) ?>"
                                    data-custo="<?= htmlspecialchars($produto['preco_custo']) ?>"
                                    data-venda="<?= htmlspecialchars($produto['preco_venda']) ?>"
                                    data-tipo="<?= htmlspecialchars($produto['tipo']) ?>"
                                    data-categoria="<?= htmlspecialchars($produto['id_categoria']) ?>"
                                    data-status="<?= htmlspecialchars($produto['ativo']) ?>">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
        <section>
            <dialog id="modalEditar">
                <button type="button" id="fecharEditar">X</button>
                <h3>Editar produtos</h3>
                <form action="#" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_produto" id="editarId">

                    <label for="img">Selecione uma imagem:</label><br><!--mostrar pré visualização da img com js-->
                    <input type="file" name="img" id="img" accept="image/png, image/jpeg, image/webp"><br>

                    <label for="editarNome">Digite um nome:</label><br>
                    <input type="text" id="editarNome" name="novo_nome" required><br>

                    <label for="editarDescricao">Digite a descrição:</label><br>
                    <input type="textarea" id="editarDescricao" name="nova_descricao" required><br>

                    <label for="editarQntd">Digite a quantidade:</label><br>
                    <input type="number" id="editarQntd" name="nova_qntd" required><br>

                    <label for="editarPreco_custo">Digite o custo:</label><br><!--talvez fazer um js maneiro pra deixar como money-->
                    <input type="number" step="0.01" id="editarCusto" name="novo_preco_custo" required><br>

                    <label for="editarPreco_venda">Digite o preço de venda:</label><br><!--talvez fazer um js maneiro pra deixar como money-->
                    <input type="number" step="0.01" id="editarVenda" name="novo_preco_venda" required><br>

                    <label for="editarTipo">Digite o tipo:</label><br>
                    <input type="text" id="editarTipo" name="novo_tipo" required><br>

                    <label for="editarCategoria">Categoria do produto:</label><br>
                    <select name="id_categoria" id="editarCategoria" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id_categoria'] ?>">
                                <?= htmlspecialchars($categoria['nome_categoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="editarStatus">Status:</label>
                    <select name="ativo" id="editarStatus">
                        <option value="1">Disponível</option>
                        <option value="0">Indisponível</option>
                    </select><br><br>

                <input type="submit" name="editar" value="Salvar">
            </form>
            </dialog>
        </section>
        
    </main>
    <script>
        const btnAbrirModal = document.getElementById('abrirModal');
        const modal = document.getElementById('modal');
        const btnFecharModal = document.getElementById('fecharModal');

        btnAbrirModal.addEventListener('click', () => modal.showModal());
        btnFecharModal.addEventListener('click', () => modal.close());

        const modalEditar = document.getElementById('modalEditar');
        const btnFecharEditar = document.getElementById('fecharEditar');

        document.querySelectorAll('.abrirEditar').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editarId').value = btn.dataset.id;
                document.getElementById('editarNome').value = btn.dataset.nome;
                document.getElementById('editarDescricao').value = btn.dataset.descricao;
                document.getElementById('editarQntd').value = btn.dataset.qntd;
                document.getElementById('editarCusto').value = btn.dataset.custo;
                document.getElementById('editarVenda').value = btn.dataset.venda;
                document.getElementById('editarTipo').value = btn.dataset.tipo;
                document.getElementById('editarCategoria').value = btn.dataset.categoria;
                document.getElementById('editarStatus').value = btn.dataset.status;

                modalEditar.showModal();
            });
        });

        btnFecharEditar.addEventListener('click', () => modalEditar.close());
    </script>
</body>
</html>