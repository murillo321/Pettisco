<?php
    session_start();
    //verificar id do user e se é adm

    require_once(__DIR__ . '../../../funcoes/conexao.php');

    if(isset($_POST['cadastrar'])){
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $senha = $_POST['senha'];

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        if(empty($nome) || empty($email) || empty($cpf) || empty($email) || empty($senha)){
            echo '<script>alert("Preencha todos os campos!")</scritp>';
            exit;
        }
        
        try{
            $insert = "insert into user (username,email,cpf,senha) values (:nome, :email, :cpf, :senha);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpf,
                ':senha' => $senha_hash
            ]);

            header('location:usuarios.php');

        } catch (PDOException $e){
            echo 'Erro ao inserir: '. $e->getMessage();
        }
    }

    if(isset($_POST['excluir'])){
        $id_user = $_POST['id_user'];

        try{
            $excluir = "delete from user where id_user = :id_user;";
            $stmt = $pdo->prepare($excluir);
            $stmt->execute([':id_user' => $id_user]);

            header('location:usuarios.php');

        } catch (PDOException $e){
            echo 'Erro ao excluir: '.$e->getMessage();
        }
    }

    try{
        $select_users = 'select * from user order by id_user;';
        $stmt = $pdo->prepare($select_users);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e){
        echo 'Erro ao consultar: '. $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Usuários</title>
</head>
<body>
    <header>
        <h1>
            Central de Usuários
        </h1>
    </header>
    <main>
        <section>
            <button id="abrirModal">Cadastrar Usuário +</button>
        </section>
        <dialog id="modal">
            <button id="fecharModal">X</button>
            <h3>Cadastre um usuário</h3>
            <form action="#" method="POST">
                <label for="nome">Digite um nome:</label><br>
                <input type="text" id="nome" name="nome" required><br>
                <label for="email">Digite o email:</label><br>
                <input type="email" id="email" name="email" required><br>
                <label for="cpf">Digite o CPF:</label><br>
                <input type="text" id="cpf" name="cpf" maxlength="11" required><br>
                <label for="senha">Digite a senha:</label><br>
                <input type="password" id="senha" name="senha" required><br><br>
                <input type="submit" name="cadastrar" value="Cadastrar">
            </form>
        </dialog>
        <br>
        <section>
            <table>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Nome</td>
                        <td>Email</td>
                        <td>CPF</td>
                        <td>Excluir</td>
                        <td>Editar</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td> <?= htmlspecialchars($usuario['id_user']) ?> </td>
                            <td> <?= htmlspecialchars($usuario['username']) ?> </td>
                            <td> <?= htmlspecialchars($usuario['email']) ?> </td>
                            <td> <?= htmlspecialchars($usuario['cpf']) ?> </td>
                            <td>
                                <form action="#" method="POST">
                                    <input type="hidden" name="id_user" value="<?= $usuario['id_user'] ?>">
                                    <button type="submit" name="excluir">Excluir</button>
                                </form>
                            </td>
                            <td>
                                <a href="../../utilitarios/editarUser.php?id_user=<?= $usuario['id_user'] ?>">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
        
    </main>
    <script src="../../../front-end/js/abrir-modais.js"></script>
</body>
</html>