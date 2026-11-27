<?php
    session_start();
    $id_user = $_SESSION['id_user'];

    require_once(__DIR__ . '/../../funcoes/conexao.php');
    require_once('../../funcoes/funcoesadm.php');

    verificaradm($pdo, $id_user);

    if(isset($_POST['cadastrar'])){
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];

        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
        $cpfFormatado = '';
        for ($i = 0; $i < strlen($cpf); $i++) {
            $cpfFormatado .= $cpf[$i];

            if ($i === 2 || $i === 5) {
                $cpfFormatado .= '.';
            } elseif ($i === 8) {
                $cpfFormatado .= '-';
            }
        }

        $senha = $_POST['senha'];

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        if(empty($nome) || empty($email) || empty($cpf) || empty($email) || empty($senha)){
            echo '<script>alert("Preencha todos os campos!")</script>';
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

    if(isset($_POST['editar'])){
        $id_user = $_POST['id_user'];
        $novo_nome = $_POST['novo_nome'];
        $novo_email = $_POST['novo_email'];
        $nova_senha = $_POST['nova_senha'];

        try{
            $select_user = "select * from user where id_user = :id_user;";
            $stmt = $pdo->prepare($select_user);
            $stmt->execute([':id_user' => $id_user]);
            $dados_user = $stmt->fetch();
        } catch (PDOException $e){
            echo 'Erro ao consultar: '. $e->getMessage();
        }

        if(empty($novo_nome)){
            $novo_nome = $dados_user['username'];
        }

        if(empty($novo_email)){
            $novo_nome = $dados_user['email'];
        }

        if(empty($nova_senha)){
            $senha_hash = $dados_user['senha'];
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        }

        try{
            $atualizar = 'update user set username = :nome, email = :email, senha = :senha where id_user = :id_user;';
            $stmt = $pdo->prepare($atualizar);
            $stmt->execute([
                ':nome' => $novo_nome,
                ':email' => $novo_email,
                ':senha' => $senha_hash,
                ':id_user' => $id_user
            ]);
            header('location:usuarios.php');
        } catch (PDOException $e){
            echo 'Erro ao atualizar: '. $e->getMessage();
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
    <link rel="stylesheet" href="../../../front-end/css/main.css">
    <link rel="shortcut icon" href="../../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
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
                <input type="text" id="cpf" name="cpf" maxlength="14" required><br>
                <label for="senha">Digite a senha:</label><br>
                <input type="password" id="senha" name="senha" minlength="8" placeholder="Mínimo de 8 caracteres" required><br>
                <section class="requisitos-senha">
                    <p>A senha deve conter:</p>
                    <ul class="rules" id="rules">
                        <li id="quantidade">8+ caracteres</li>
                        <li id="maiuscula">Uma letra maiúscula</li>
                        <li id="numeros">Um número</li>
                        <li id="especiais">Um caractere especial</li>
                    </ul>
                </section>
                <input type="submit" name="cadastrar" value="Cadastrar">
            </form>
        </dialog>
        <br>
        <section>
            <table>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Foto</td>
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
                            <td>
                                <?php if($usuario['foto_url'] == null): ?>
                                    <img src="../../../front-end/img/perfil/usuario/usuario-sem-foto.png">
                                <?php else:?>
                                    <img src="../../../front-end/<?= htmlspecialchars($usuario['foto_url']) ?>" alt="Foto do usuario <?= $usuario['username'] ?>">
                                <?php endif; ?>
                            </td>
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
                                <button type="button" class="abrirEditar"
                                    data-id="<?= htmlspecialchars($usuario['id_user']) ?>"
                                    data-nome="<?= htmlspecialchars($usuario['username']) ?>"
                                    data-email="<?= htmlspecialchars($usuario['email']) ?>">
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
                <h3>Editar usuário</h3>
                <form action="" method="POST">
                    <input type="hidden" name="id_user" id="editarId">

                    <label for="editarNome">Nome:</label><br>
                    <input type="text" id="editarNome" name="novo_nome" required><br>

                    <label for="editarEmail">Email:</label><br>
                    <input type="email" id="editarEmail" name="novo_email" required><br>

                    <label for="editarSenha">Nova senha (deixe em branco para manter):</label><br>
                    <input type="password" id="editarSenha" minlength="8" placeholder="Mínimo de 8 caracteres" name="nova_senha"><br><br>

                    <input type="submit" name="editar" value="Salvar">
                </form>
            </dialog>
        </section>
        
    </main>
    <script>
        lucide.createIcons();

        const quantidade = document.getElementById("quantidade");
        const maiuscula = document.getElementById("maiuscula");
        const numeros = document.getElementById("numeros");
        const especiais = document.getElementById("especiais");
        const alertaSenha = document.getElementById("alerta-senha");
        const formulario = document.querySelector('form[name="cadastro"]');
        const modalErroSenha = document.getElementById("modal-erro-senha");
        const mensagemErroSenha = document.getElementById("mensagem-erro-senha");
    </script>
    <script src="../../../front-end/js/abrir-modais.js"></script>
    <script src="../../../front-end/js/validacao-cadastro.js"></script>
    <script src="../../../front-end/js/validar-cpf.js"></script>
</body>
</html>