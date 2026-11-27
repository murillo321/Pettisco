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

    $select_porte = "select * from porte order by id_porte;";
    $stmt = $pdo->prepare($select_porte);
    $stmt->execute();
    $portes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $select_valor_servico = "select vs.id,vs.valor,s.nome_servico,p.nome_porte
    from valor_servico vs inner join servico s on vs.id_servico = s.id_servico
    inner join porte p on vs.id_porte = p.id_porte order by s.nome_servico, p.id_porte;";
    $stmt = $pdo->prepare($select_valor_servico);
    $stmt->execute();
    $valores_servico = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(isset($_POST['cadastrar_valor'])){
        $servico = $_POST['id_servico'];
        $porte = $_POST['id_porte'];
        $valor = $_POST['valor'];

        if(empty($servico) || empty($porte) || empty($valor)){
            echo '<script>alert("Preencha todos os campos!")</script>';
        }

        try{
            $insert = "insert into valor_servico (id_servico,id_porte,valor) values (:id_servico,:id_porte,:valor);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([
                ':id_servico' => $servico,
                ':id_porte' => $porte,
                ':valor' => $valor
            ]);

            header('location:crud_valor_servicos.php');
        } catch (PDOException $e){
            echo 'Erro ao inserir: ' . $e->getMessage();
        }
    }

    if(isset($_POST['excluir_valor_servico'])){
        $id_valor_servico = $_POST['id_valor_servico'];

        try{
            $deletar = "delete from valor_servico where id = :id_valor_servico;";
            $stmt = $pdo->prepare($deletar);
            $stmt->execute([':id_valor_servico' => $id_valor_servico]);

            header('location: crud_valor_servicos.php');
        } catch (PDOException $e){
            echo 'Erro ao excluir: ' . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petisco - Valor dos Serviços</title>
</head>
<body>
    <header>
        <h1>Central de Valor de Serviços</h1>
    </header>
    <section>
        <button id="abrirModal">Cadastrar Serviço +</button>
    </section>

    <section>
        <dialog id="modal">
            <button id="fecharModal">x</button>

            <h3>Cadastre o valor do serviço de acordo com o porte:</h3>

                <form action="#" method="POST">
                    <select name="id_servico">
                        <option value="">Selecione um serviço</option>
                        <?php foreach($servicos as $servico): ?>
                            <option value="<?= $servico['id_servico'] ?>">
                                <?= $servico['nome_servico'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <select name="id_porte">
                        <option value="">Selecione um porte</option>
                        <?php foreach($portes as $porte): ?>
                            <option value="<?= $porte['id_porte'] ?>">
                                <?= $porte['nome_porte'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="valor">Digite o preço:</label><br>
                    <input type="number" step="0.01" id="valor" name="valor"><br><br>

                    <input type="submit" name="cadastrar_valor" value="Cadastrar">
                </form>
            </dialog>
    </section>

    <section>
        <table>
            <h3>Valor dos Serviços:</h3>
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Serviço</td>
                    <td>Porte</td>
                    <td>Valor</td>
                    <td>Excluir</td>
                    <td>Editar</td>
                </tr>
            </thead>
            <tbody>
                    
            <?php foreach ($valores_servico as $valor_servico): ?>
                <tr>
                    <td> <?= htmlspecialchars($valor_servico['id']) ?> </td>
                    <td> <?= htmlspecialchars($valor_servico['nome_servico']) ?> </td>
                    <td> <?= htmlspecialchars($valor_servico['nome_porte']) ?> </td>
                    <td>R$ <?= number_format($valor_servico['valor'], 2, ',', '.') ?> </td>
                    <td>
                        <form action="#" method="POST">
                            <input type="hidden" name="id_valor_servico" value="<?= $valor_servico['id'] ?>">
                            <button type="submit" name="excluir_valor_servico">Excluir</button>
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

    <script src="../../../front-end/js/abrir-modais.js"></script>
</body>
</html>