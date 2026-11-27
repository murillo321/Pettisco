<?php
    session_start();
    $id_user = $_SESSION['id_user'];

    require_once(__DIR__ . '/../../funcoes/conexao.php');
    require_once('../../funcoes/funcoesadm.php');

    verificaradm($pdo, $id_user);

    if(isset($_POST['cadastrar_raca'])){
        $nome_raca = $_POST['nome_raca'];
        $id_especie = $_POST['id_especie'];
        $id_porte = $_POST['id_porte'];

        if(empty($nome_raca) || empty($id_especie) || empty($id_porte)){
            echo '<script>alert("Preencha todos os campos!")</script>';
            exit;
        }
        
        try{
            $insert = "insert into raca (nome_raca,id_especie,id_porte) values (:nome, :id_especie, :id_porte);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([
                ':nome' => $nome_raca,
                ':id_especie' => $id_especie,
                ':id_porte' => $id_porte
            ]);

            header('location:racas.php');

        } catch (PDOException $e){
            echo 'Erro ao inserir: '. $e->getMessage();
        }
    }

    if(isset($_POST['excluir_raca'])){
        $id_raca = $_POST['id_raca'];
        try{
            $excluir = "delete from raca where id_raca = :id_raca;";
            $stmt = $pdo->prepare($excluir);
            $stmt->execute([':id_raca' => $id_raca]);

            header('location:racas.php');

        } catch (PDOException $e){
            echo 'Erro ao excluir: '.$e->getMessage();
        }
    }

    if(isset($_POST['editar'])){
        $id_raca = $_POST['id_raca'];
        $novo_nome_raca = $_POST['novo_nome'];
        $novo_id_especie = $_POST['novo_id_especie'];
        $novo_id_porte = $_POST['novo_id_porte'];

        try{
            $select_raca = "select * from raca where id_raca = :id_raca;";
            $stmt = $pdo->prepare($select_raca);
            $stmt->execute([':id_raca' => $id_raca]);
            $dados_raca = $stmt->fetch();
        } catch (PDOException $e){
            echo 'Erro ao consultar: '. $e->getMessage();
        }

        if(empty($novo_nome_raca)){
            $novo_nome_raca = $dados_raca['nome_raca'];
        }

        if(empty($novo_id_especie)){
            $novo_id_especie = $dados_raca['id_especie'];
        }

        if(empty($novo_id_porte)){
            $novo_id_porte = $dados_raca['id_porte'];
        }

        try{
            $atualizar = 'update raca set nome_raca = :nome, id_especie = :id_especie, id_porte = :id_porte where id_raca = :id_raca;';
            $stmt = $pdo->prepare($atualizar);
            $stmt->execute([
                ':nome' => $novo_nome_raca,
                ':id_especie' => $novo_id_especie,
                ':id_porte' => $novo_id_porte,
                ':id_raca' => $id_raca
            ]);
            header('location:racas.php');
        } catch (PDOException $e){
            echo 'Erro ao atualizar: '. $e->getMessage();
        }
    }

    //Início parte de Espécie

    if(isset($_POST['cadastrar_especie'])){
        $nome_especie = $_POST['nome_especie'];

        if(empty($nome_especie)){
            echo '<script>alert("Preencha todos os campos!")</script>';
            exit;
        }
        
        try{
            $insert = "insert into especie (nome_especie) values (:nome);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([':nome' => $nome_especie]);

            header('location:racas.php');

        } catch (PDOException $e){
            echo 'Erro ao inserir: '. $e->getMessage();
        }
    }

    if(isset($_POST['excluir_especie'])){
        $id_especie = $_POST['id_especie'];
        //fazer on delete cascade
        try{
            $excluir = "delete from especie where id_especie = :id_especie;";
            $stmt = $pdo->prepare($excluir);
            $stmt->execute([':id_especie' => $id_especie]);

            header('location:racas.php');

        } catch (PDOException $e){
            echo 'Erro ao excluir: '.$e->getMessage();
        }
    }

    if(isset($_POST['editar_especie'])){
        $id_especie = $_POST['id_especie'];
        $novo_nome_especie = $_POST['novo_nome_especie'];

        try {
            $busca_especie = 'select nome_especie from especie where id_especie = :id_especie;';
            $stmt = $pdo->prepare($busca_especie);
            $stmt->execute([':id_especie' => $id_especie]);
            $dados_especie = $stmt->fetch(); 
        } catch (PDOException $e){
            echo 'Erro ao consultar: ' . $e->getMessage();
        }

        if(empty($novo_nome_especie)){
            $novo_nome_especie = $dados_especie['nome_especie'];
        }

        try {
            $atualizar = 'update especie set nome_especie = :nome_especie where id_especie = :id_especie;';
            $stmt = $pdo->prepare($atualizar);
            $stmt->execute([
              ':nome_especie' => $novo_nome_especie,
              ':id_especie' => $id_especie
            ]);

            header('location:racas.php');
        } catch(PDOException $e){
            echo 'Erro ao atualizar: ' . $e->getMessage();
        }
    }

    try{
        $select_racas = "select r.*, e.nome_especie, p.nome_porte from raca r inner join especie e on r.id_especie = e.id_especie inner join porte p on r.id_porte = p.id_porte order by id_raca;";
        $stmt = $pdo->prepare($select_racas);
        $stmt->execute();
        $racas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $select_especie = 'select * from especie order by id_especie;';
        $stmt = $pdo->prepare($select_especie);
        $stmt->execute();
        $especies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $select_porte = 'select * from porte order by id_porte;';
        $stmt = $pdo->prepare($select_porte);
        $stmt->execute();
        $portes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e){
        echo 'Erro ao consultar: '. $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../front-end/css/main.css">
    <link rel="shortcut icon" href="../../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <title>Pettisco - Raças e Espécies</title>
</head>
<body>
    <header>
        <h1>
            Central de Raças e Espécies
        </h1>
    </header>
    <main>
        <section>
            <button id="abrirModal">Cadastrar Raça +</button>
        </section>
        <dialog id="modal">
            <button id="fecharModal">X</button>
            <h3>Cadastre uma raça:</h3>
            <form action="#" method="POST">
                <label for="nome_raca">Digite o nome:</label><br>
                <input type="text" id="nome_raca" name="nome_raca" required><br>

                <label for="especie">Espécie do animal:</label><br>
                <select name="id_especie" id="especie" required>
                    <option value="">Selecione uma espécie</option>
                    <?php foreach ($especies as $especie): ?>
                        <option value="<?= $especie['id_especie'] ?>">
                            <?= htmlspecialchars($especie['nome_especie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="porte">Porte do animal:</label><br>
                <select name="id_porte" id="porte" required>
                    <option value="">Selecione um porte</option>
                    <?php foreach ($portes as $porte): ?>
                        <option value="<?= $porte['id_porte'] ?>">
                            <?= htmlspecialchars($porte['nome_porte']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <input type="submit" name="cadastrar_raca" value="Cadastrar">
            </form>
        </dialog>

        <section>
            <table>
                <h3>Raças:</h3>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Raça</td>
                        <td>Espécie</td>
                        <td>Porte</td>
                        <td>Excluir</td>
                        <td>Editar</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($racas as $raca): ?>
                        <tr>
                            <td> <?= htmlspecialchars($raca['id_raca']) ?> </td>
                            <td> <?= htmlspecialchars($raca['nome_raca']) ?> </td>
                            <td> <?= htmlspecialchars($raca['nome_especie']) ?> </td>
                            <td> <?= htmlspecialchars($raca['nome_porte']) ?> </td>
                            <td>
                                <form action="#" method="POST">
                                    <input type="hidden" name="id_raca" value="<?= $raca['id_raca'] ?>">
                                    <button type="submit" name="excluir_raca">Excluir</button>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="abrirEditarRaca"
                                    data-idRaca ="<?= htmlspecialchars($raca['id_raca']) ?>"
                                    data-nomeRaca="<?= htmlspecialchars($raca['nome_raca']) ?>"
                                    data-idEspecie ="<?= htmlspecialchars($raca['id_especie']) ?>"
                                    data-idPorte ="<?= htmlspecialchars($raca['id_porte']) ?>">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>

        <section>
            <dialog id="modalEditarRaca">
                <button type="button" id="fecharEditarRaca">X</button>
                <h3>Edite a raça:</h3>
                <form action="#" method="POST">
                    <input type="hidden" id="editarIdRaca" name="id_raca">

                    <label for="nome">Digite o nome:</label><br>
                    <input type="text" id="editarNomeRaca" name="novo_nome" required><br>

                    <label for="editarSelectEspecie">Espécie do animal:</label><br>
                    <select name="novo_id_especie" id="editarSelectEspecie" required>
                        <option value="">Selecione uma espécie</option>
                        <?php foreach ($especies as $especie): ?>
                            <option value="<?= $especie['id_especie'] ?>">
                                <?= htmlspecialchars($especie['nome_especie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="porte">Porte do animal:</label><br>
                    <select name="novo_id_porte" id="editarPorte" required>
                        <option value="">Selecione um porte</option>
                        <?php foreach ($portes as $porte): ?>
                            <option value="<?= $porte['id_porte'] ?>">
                                <?= htmlspecialchars($porte['nome_porte']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <input type="submit" name="editar" value="Salvar">
                </form>
            </dialog>
        </section>

        <br><br>

                        <!--Inicio parte da especie-->
        <section>
            <button id="abrirCadastroEspecie">Cadastrar Espécie +</button>
        </section>
        <dialog id="cadastroEspecie">
            <button id="fecharCadastroEspecie">X</button>
            <h3>Cadastre uma espécie:</h3>
            <form action="#" method="POST">
                <label for="nome_especie">Digite o nome:</label><br>
                <input type="text" id="nome_especie" name="nome_especie" required><br><br>

                <input type="submit" name="cadastrar_especie" value="Cadastrar">
            </form>
        </dialog>
        <br>
        <section>
            <table>
                <h3>Espécies:</h3>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Espécie</td>
                        <td>Excluir</td>
                        <td>Editar</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($especies as $especie): ?>
                        <tr>
                            <td> <?= htmlspecialchars($especie['id_especie']) ?> </td>
                            <td> <?= htmlspecialchars($especie['nome_especie']) ?> </td>
                            <td>
                                <form action="#" method="POST">
                                    <input type="hidden" name="id_especie" value="<?= $especie['id_especie'] ?>">
                                    <button type="submit" name="excluir_especie">Excluir</button>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="abrirEditarEspecie"
                                    data-id-especie ="<?= htmlspecialchars($especie['id_especie']) ?>"
                                    data-nome-especie ="<?= htmlspecialchars($especie['nome_especie']) ?>">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
        
        <!--fazer editar da especie-->
        <section>
            <dialog id="editarEspecie">
                <button id="fecharEditarEspecie">X</button>
                <h3>Edite a espécie:</h3>
                <form action="#" method="POST">
                    <input type="hidden" id="editarIdEspecie" name="id_especie">

                    <label for="editarNomeEspecie">Digite o nome:</label><br>
                    <input type="text" id="editarNomeEspecie" name="novo_nome_especie" required><br><br>

                    <input type="submit" name="editar_especie" value="Salvar">
                </form>
            </dialog>
        </section>
        
    </main>
    <script>
        lucide.createIcons();

        const btnAbrirCadastroEspecie = document.getElementById('abrirCadastroEspecie');
        const cadastroEspecie = document.getElementById('cadastroEspecie');
        const btnFecharCadastroEspecie = document.getElementById('fecharCadastroEspecie');

        btnAbrirCadastroEspecie.addEventListener('click', () => cadastroEspecie.showModal());
        btnFecharCadastroEspecie.addEventListener('click', () => cadastroEspecie.close());

        const modalEditarEspecie = document.getElementById('editarEspecie');
        const btnFecharEditarEspecie = document.getElementById('fecharEditarEspecie');

        document.querySelectorAll('.abrirEditarEspecie').forEach(btn => {
            btn.addEventListener('click', () => {
                console.log('Botão de editar espécie clicado!');
                console.log(btn.dataset.idEspecie);
                console.log(btn.dataset.nomeEspecie);

                document.getElementById('editarIdEspecie').value = btn.dataset.idEspecie;
                document.getElementById('editarNomeEspecie').value = btn.dataset.nomeEspecie;
                modalEditarEspecie.showModal();
            });
        });

        btnFecharEditarEspecie.addEventListener('click', () => {modalEditarEspecie.close()});

        const modalEditarRaca = document.getElementById('modalEditarRaca');
        const btnFecharEditarRaca = document.getElementById('fecharEditarRaca');

        document.querySelectorAll('.abrirEditarRaca').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editarIdRaca').value = btn.dataset.idraca;
                document.getElementById('editarNomeRaca').value = btn.dataset.nomeraca;
                document.getElementById('editarSelectEspecie').value = btn.dataset.idespecie;
                document.getElementById('editarPorte').value = btn.dataset.idporte;
                modalEditarRaca.showModal();
            });
        });

        btnFecharEditarRaca.addEventListener('click', () => modalEditarRaca.close());
    </script>
    <script src="../../../front-end/js/abrir-modais.js"></script>
</body>
</html>