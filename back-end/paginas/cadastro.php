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
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/cadastro.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <title>Pettisco - Cadastro</title>
</head>
<body>
    <main class="cadastro">
        <section class="painel-esquerdo">
            <section class="conteudo">
                <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" class="logo" alt="logo pettisco, cachorro com um chapéu">
                <span class="linha-destaque" aria-hidden="true"></span>
                <h2 class="titulo-esquerdo">Crie sua conta na <span class="destaque">Pettisco</span></h2>
                <p>Cadastre-se e aproveite muitos benefícios para você cuidar do seu pet da melhor maneira!</p>

                <ul class="beneficios">
                    <li>

                        <div><h3>Compre com facilidade</h3><p>Encontre os melhores produtos para o seu pet.</p></div>
                    </li>
                    <li>

                        <div><h3>Ofertas exclusivas</h3><p>Promoções e benefícios especiais para clientes cadastrados.</p></div>
                    </li>
                    <li>

                        <div><h3>Tudo para o bem-estar do seu pet</h3><p>Qualidade, variedade e cuidado em um só lugar.</p></div>
                    </li>
                </ul>
            </section>
        </section>

        <section class="painel-direito">
            <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" class="logo logo-mobile" alt="Pettisco">
            <h1 class="titulo-direito">Criar conta</h1>
            <p class="introducao-cadastro">Cadastre-se na Pettisco e tenha acesso a ofertas exclusivas, compras rápidas e muito mais para o seu pet.</p>
            <span class="linha-destaque" aria-hidden="true"></span>
            <form action="#" method="POST" name="cadastro">

                <div class="campo-cadastro">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
                </div>

                <section class="email-cpf">
                    <div class="campo-cadastro">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    </div>

                    <div class="campo-cadastro">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" required>
                    <p id="alerta-cpf"></p>
                    </div>
                </section>
                <section class="field">
            <label for="senha">Senha</label>
            
            <section class="input-wrap">
                <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres" required minlength="8" />
                <button type="button" class="eye" data-target="senha" aria-label="Mostrar senha">👁</button>
            </section>

            <div class="requisitos-senha">
            <p>Sua senha deve conter:</p>
            <ul class="rules" id="rules">
                <li id="quantidade">8+ caracteres</li>
                <li id="maiuscula">Uma letra maiúscula</li>
                <li id="numeros">Um número</li>
                <li id="especiais">Um caractere especial</li>
            </ul>
            </div>

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
        </section>

        <dialog id="modal-erro-senha" aria-labelledby="titulo-erro-senha">
            <h2 id="titulo-erro-senha">Não foi possível cadastrar</h2>
            <p id="mensagem-erro-senha"></p>
            <form method="dialog">
                <button type="submit">Entendi</button>
            </form>
        </dialog>
</main>


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