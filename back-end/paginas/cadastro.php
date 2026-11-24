<?php
    session_start();

    if (isset($_SESSION['cadastro_concluido'])) {
        header("location:home.php");
        exit();
    }

    require_once('../funcoes/conexao.php');
    
    $adm = 0;
    if (
        ($_GET['acesso'] ?? '') === 'adm'
        && isset($_GET['cpf'], $_SESSION['convite_adm_id'], $_SESSION['convite_adm_cpf'])
        && is_string($_GET['cpf'])
        && hash_equals($_SESSION['convite_adm_cpf'], $_GET['cpf'])
    ) {
    $cpfAdm = $_GET['cpf'];

    $sqlVerificarCpfAdm = $pdo->prepare('SELECT email, username FROM user WHERE cpf = :cpfAdm');
    $sqlVerificarCpfAdm->execute(['cpfAdm' => $cpfAdm]);
    $verificacaoAdm = $sqlVerificarCpfAdm->fetch(PDO::FETCH_ASSOC);

    if ($verificacaoAdm) {
        $emailAdm = $verificacaoAdm['email'];
        $nomeAdm = $verificacaoAdm['username'];
    }

    $verificarCpfConvidado = $pdo->prepare('SELECT id_convite FROM convite_adm WHERE id_convite = :idConvite AND cpf_convidado = :cpfAdm AND expira_em > CURRENT_TIMESTAMP AND usado_em IS NULL AND revogado_em IS NULL');
    $verificarCpfConvidado->execute([
        'idConvite' => $_SESSION['convite_adm_id'],
        'cpfAdm' => $cpfAdm,
    ]);
    $convite = $verificarCpfConvidado->fetch(PDO::FETCH_ASSOC);


    if (!$convite) {
        echo '<script>alert("Usuário não convidado")</script>';
    } else {
        $adm = 1;
    }


} else {
    $adm = 0;
}
    if (isset($_POST['submit'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
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

        
        if (!empty($nome) && !empty($email) && !empty($cpf) && !empty($senha) && (!$adm || $cpfFormatado === $cpfAdm)){
            $sqlVerificar = $pdo->prepare("SELECT * FROM user WHERE email=:email or cpf=:cpf");
            $sqlVerificar->execute(['email' => $email, 'cpf' => $cpfFormatado]); 
            $verificacao = $sqlVerificar->fetch(PDO::FETCH_ASSOC);

            if(!empty($verificacao)){
                echo '<script>alert("Usuário já cadastrado!")</script>';
            } else {
            $sqlDados = 'INSERT INTO user (username, email, cpf, senha, adm) VALUES (:nome, :email, :cpf, :senhahash, :adm)';
            $inserirDados = $pdo->prepare($sqlDados);
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            
            $inserirDados->execute(array(
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpfFormatado,
                ':senhahash' => $hash,
                ':adm' => $adm
            ));

            $idUser = $pdo->lastInsertId();
            if ($adm && isset($_SESSION['convite_adm_id'])) {
                $marcarConviteUsado = $pdo->prepare('UPDATE convite_adm SET id_adm_cadastrado = :idUser, usado_em = CURRENT_TIMESTAMP WHERE id_convite = :idConvite');
                $marcarConviteUsado->execute([
                    'idUser' => $idUser,
                    'idConvite' => $_SESSION['convite_adm_id'],
                ]);
                unset($_SESSION['convite_adm_id'], $_SESSION['convite_adm_cpf']);
            }

            $_SESSION['cadastro_concluido'] = true;
            $_SESSION['id_user'] = $idUser;
            $_SESSION['adm'] = true;
            header("location:/adm/adm.php");
        }} else {
            echo '</script>alert("Preencha todos os campos!")</script>';
        }
    }
    


    ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/cadastro.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <title>Pettisco - Cadastro</title>
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/header.php'; ?>
    <main class="cadastro">
        <section class="painel-esquerdo">
            <section class="conteudo">
                <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" class="logo" alt="logo pettisco, cachorro com um chapéu">
                <h2 class="titulo-esquerdo">Crie sua conta na <span class="destaque">Pettisco</span></h2>
                <p>Cadastre-se e aproveite muitos benefícios para você cuidar do seu pet da melhor maneira!</p>

                <ul class="beneficios">
                    <li>

                        <section><h3>Compre com facilidade</h3><p>Encontre os melhores produtos para o seu pet.</p></section>
                    </li>
                    <li>

                        <section><h3>Ofertas exclusivas</h3><p>Promoções e benefícios especiais para clientes cadastrados.</p></section>
                    </li>
                    <li>

                        <section><h3>Tudo para o bem-estar do seu pet</h3><p>Qualidade, variedade e cuidado em um só lugar.</p></section>
                    </li>
                </ul>
            </section>
        </section>

        <section class="painel-direito">
            <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" class="logo logo-mobile" alt="Pettisco">
            <h1 class="titulo-direito">Criar conta</h1>
            <span class="linha-destaque" aria-hidden="true"></span>
            <form action="#" method="POST" name="cadastro">

                <section class="campo-cadastro">
                <label for="nome">Nome Completo:</label>
                <section class="input">
                    <i data-lucide="user" class="user-lucide" aria-hidden="true"></i>
                    <input type="text" id="nome" name="nome" required placeholder="Digite seu nome completo">
                </section>
                </section>

                <section class="email-cpf">
                    <section class="campo-cadastro">
                    <label for="email">Email:</label>
                    <section class="input">
                        <i data-lucide="mail" class="user-lucide" aria-hidden="true"></i>
                        <input type="email" id="email" name="email" required placeholder="Digite seu email">
                    </section>
                    </section>

                    <section class="campo-cadastro">
                    <label for="cpf">CPF:</label>
                    <section class="input">
                        <i data-lucide="id-card" class="user-lucide" aria-hidden="true"></i>
                        <input type="text" id="cpf" name="cpf" required placeholder="Digite seu CPF" value="<?= htmlspecialchars($cpfAdm ?? '')?>" <?= !empty($cpfAdm) ? 'readonly' : '' ?>>
                    </section>
                    <p id="alerta-cpf"></p>
                    </section>
                </section>
                <section class="field">
            <label for="senha">Senha</label>
            
            <section class="input-wrap">    
                <section class="input">
                        <i data-lucide="lock" class="user-lucide" aria-hidden="true"></i>
                        <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres" required minlength="8" />
                </section>
                <button type="button" class="eye" data-target="senha" aria-label="Mostrar senha"><i data-lucide="eye" aria-hidden="true"></i></button>
            </section>

            <section class="requisitos-senha">
            <p>Sua senha deve conter:</p>
            <ul class="rules" id="rules">
                <li id="quantidade">8+ caracteres</li>
                <li id="maiuscula">Uma letra maiúscula</li>
                <li id="numeros">Um número</li>
                <li id="especiais">Um caractere especial</li>
            </ul>
            </section>

                </section>
                <section class="field">
                <label for="confirmar-senha">Confirmar senha</label>
                <section>
                
                <section class="input-wrap">
                    <section class="input">
                        <i data-lucide="lock" class="user-lucide" aria-hidden="true"></i>
                        <input type="password" id="confirmar-senha" name="confirmar-senha" placeholder="Repita a sua senha" required minlength="8" />
                    </section>
                    <button type="button" class="eye" data-target="confirmar-senha" aria-label="Mostrar senha"><i data-lucide="eye" aria-hidden="true"></i></button>
                </section>
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


    

    <script src="../../front-end/js/validar-cpf.js"></script>
    <script src="../../front-end/js/validacao-cadastro.js"></script>
    <script>
    (function () {
            const campoCpf = document.getElementById("cpf");

            campoCpf.addEventListener("input", function () {
                let valorCpf = this.value.replace(/\D/g, "").slice(0, 11);

                if (valorCpf.length > 9) {
                    valorCpf = valorCpf.replace(
                        /^(\d{3})(\d{3})(\d{3})(\d{1,2})$/,
                        "$1.$2.$3-$4"
                    );
                } else if (valorCpf.length > 6) {
                    valorCpf = valorCpf.replace(
                        /^(\d{3})(\d{3})(\d{1,3})$/,
                        "$1.$2.$3"
                    );
                } else if (valorCpf.length > 3) {
                    valorCpf = valorCpf.replace(
                        /^(\d{3})(\d{1,3})$/,
                        "$1.$2"
                    );
                }

                this.value = valorCpf;
            });
        })();
        
        lucide.createIcons();
    </script>


    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>