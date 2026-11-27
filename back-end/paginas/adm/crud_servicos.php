<?php
    session_start();
    $id_user = $_SESSION['id_user'];

    require_once(__DIR__ . '/../../funcoes/conexao.php');
    require_once('../../funcoes/funcoesadm.php');

    verificaradm($pdo, $id_user);

    $select_servicos = "select * from servico order by id_servico;";
    $stmt = $pdo->prepare($select_servicos);
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

   if(isset($_POST['cadastrar_servico'])){
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];

        if(empty($nome) || empty($descricao)){
            echo '<script>alert("Preencha todos os campos!")</script>';
            exit;
        }

        try{
            $insert = "insert into servico (nome_servico,descricao) values (:nome,:descricao);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao
            ]);

            header('location: crud_servicos.php');
        } catch (PDOException $e) {
            echo 'Erro ao inserir: ' . $e->getMessage();
        }
   }

   if(isset($_POST['excluir_servico'])){
        $id_servico = $_POST['id_servico'];

        try{
            $deletar = "delete from servico where id_servico = :id_servico;";
            $stmt = $pdo->prepare($deletar);
            $stmt->execute([':id_servico' => $id_servico]);

            header('location: crud_servicos.php');
        } catch (PDOException $e){
            echo 'Erro ao excluir: ' . $e->getMessage();
        }
   }

   if(isset($_POST['editar_servico'])){
        $id_servico = $_POST['id_servico'];
        $novo_nome = $_POST['novo_nome'];
        $nova_descricao = $_POST['nova_descricao'];

        $select_servico = "select * from servico where id_servico = :id_servico;";
        $stmt = $pdo->prepare($select_servico);
        $stmt->execute([':id_servico' => $id_servico]);
        $servico = $stmt->fetch();

        if(empty($novo_nome)){
            $novo_nome = $servico['nome_servico'];
        }

        if(empty($nova_descricao)){
            $nova_descricao = $servico['descricao'];
        }

        try{
            $atualizar = "update servico set nome_servico = :nome, descricao = :descricao where id_servico = :id_servico;";
            $stmt = $pdo->prepare($atualizar);
            $stmt->execute([
                ':nome' => $novo_nome,
                ':descricao' => $nova_descricao,
                ':id_servico' => $id_servico
            ]);

            header('location: crud_servicos.php');
        } catch(PDOException $e){
            echo 'Erro ao atualizar: ' . $e->getMessage();
        }
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
    <title>Pettisco - Serviços</title>
</head>
<body>
    <header>
        <h1>Central de Serviços</h1>
    </header>
    <main>
        <section>
            <button id="abrirModal">Cadastrar Serviço +</button>
        </section>

        <section>
            <dialog id="modal">
                <button id="fecharModal">x</button>

                <h3>Cadastre um serviço aqui:</h3>

                <form action="#" method="POST">
                    <label for="nome">Digite o nome do serviço:</label><br>
                    <input type="text" id="nome" name="nome" required><br>

                    <label for="descricao">Digite a descrição do serviço:</label><br>
                    <textarea name="descricao" id="descricao"></textarea><br><br>

                    <input type="submit" name="cadastrar_servico" value="Cadastrar">
                </form>
            </dialog>
        </section>

        <section>
            <table>
                <h3>Serviços:</h3>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Serviços</td>
                        <td>Descrição</td>
                        <td>Excluir</td>
                        <td>Editar</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($servicos as $servico): ?>
                        <tr>
                            <td> <?= htmlspecialchars($servico['id_servico']) ?> </td>
                            <td> <?= htmlspecialchars($servico['nome_servico']) ?> </td>
                            <td> <?= htmlspecialchars($servico['descricao']) ?> </td>
                            <td>
                                <form action="#" method="POST">
                                    <input type="hidden" name="id_servico" value="<?= $servico['id_servico'] ?>">
                                    <button type="submit" name="excluir_servico">Excluir</button>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="abrirEditarServico"
                                    data-id-servico ="<?= htmlspecialchars($servico['id_servico']) ?>"
                                    data-nome-servico ="<?= htmlspecialchars($servico['nome_servico']) ?>"
                                    data-descricao ="<?= htmlspecialchars($servico['descricao'])?>">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>        
        </section>

        <section>
            <dialog id="editarServico">
                <button id="fecharEditarServico">x</button>

                <h3>Edite o serviço aqui:</h3>

                <form action="#" method="POST">
                    <input type="hidden" id="editarIdServico" name="id_servico"> 

                    <label for="editarNomeServico">Digite o nome do serviço:</label><br>
                    <input type="text" id="editarNomeServico" name="novo_nome" required><br>

                    <label for="editarDescricao">Digite a descrição do serviço:</label><br>
                    <textarea name="nova_descricao" id="editarDescricao"></textarea><br><br>

                    <input type="submit" name="editar_servico" value="Salvar">
                </form>
            </dialog>
        </section>
    </main>
    <script>
        lucide.createIcons();

        const modalEditarServico = document.getElementById('editarServico');
        const btnFecharEditarServico = document.getElementById('fecharEditarServico');

        document.querySelectorAll('.abrirEditarServico').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editarIdServico').value = btn.dataset.idServico;
                document.getElementById('editarNomeServico').value = btn.dataset.nomeServico;
                document.getElementById('editarDescricao').value = btn.dataset.descricao;
                modalEditarServico.showModal();
            });
        });

        btnFecharEditarServico.addEventListener('click', () => {modalEditarServico.close()});
    </script>
    <script src="../../../front-end/js/abrir-modais.js"></script>
</body>
</html>