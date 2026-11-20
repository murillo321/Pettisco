<?php
    session_start();

    if (isset($_SESSION['cadastro_concluido'])) {
        header("location:home.php");
        exit();
    }

    require_once('../funcoes/conexao.php');

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Cadastro</title>
</head>
<body>

    <form action="#" method="POST" name="cadastro">

        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="cpf">CPF:</label>
        <input type="text" id="cpf" name="cpf" required>
        <p id="alerta-cpf"></p>

        <section class="field">
      <label for="senha">Senha</label>
      
      <section class="input-wrap">
        <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres" required minlength="8" />
        <button type="button" class="eye" data-target="senha" aria-label="Mostrar senha">👁</button>
      </section>

      <ul class="rules" id="rules">
        <li id="quantidade">8+ caracteres</li>
        <li id="maiuscula">Uma letra maiúscula</li>
        <li id="numeros">Um número</li>
        <li id="especiais">Um caractere especial</li>
      </ul>

    </section>
    <section class="field">
      <label for="confirmar-senha">Confirmar senha</label>
      <section>
        <input type="password" id="confirmar-senha" name="confirmar-senha" placeholder="Repita a senha" required />
        <button type="button" class="eye" data-target="senha" aria-label="Mostrar senha">👁</button>
      </section>
      <p id="alerta-senha"></p>
    </section>
    <p>já possui uma conta? <a href="login.php">Entre aqui!</a></p>

        <input type="submit" value="Cadastrar" name="submit">
    </form>

    <dialog id="modal-erro-senha" aria-labelledby="titulo-erro-senha">
        <h2 id="titulo-erro-senha">Não foi possível cadastrar</h2>
        <p id="mensagem-erro-senha"></p>
        <form method="dialog">
            <button type="submit">Entendi</button>
        </form>
    </dialog>


    <?php
    if (isset($_POST['submit'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $senha = $_POST['senha'];

        
        if (!empty($nome) && !empty($email) && !empty($cpf) && !empty($senha)){
            $sqlVerificar = $pdo->prepare("SELECT * FROM user WHERE email=:email or cpf=:cpf");
            $sqlVerificar->execute(['email' => $email, 'cpf' => $cpf]); 
            $verificacao = $sqlVerificar->fetch();
            if(!empty($verificacao)){
                echo '<script>alert("Usuário já cadastrado!")</script>';
            } else {
            $sqlDados = 'insert into user (username, email, cpf, senha) values (:nome, :email, :cpf, :senhahash)';
            $inserirDados = $pdo->prepare($sqlDados);
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            
            $inserirDados->execute(array(
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpf,
                ':senhahash' => $hash
            ));

            $_SESSION['cadastro_concluido'] = true;
            $_SESSION['id_user'] = $pdo->lastInsertId();
            header("location:home.php");
        }} else {
            echo '</script>alert("Preencha todos os campos!")</script>';
        }
    }
    


    ?>

    <script src="../../front-end/js/validacao-cadastro.js"></script>


</body>
</html>