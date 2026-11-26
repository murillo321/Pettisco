<?php
    session_start();
    require_once('../funcoes/conexao.php');

    if (isset($_SESSION['cadastro_concluido'])) {
        header(!empty($_SESSION['adm_supremo']) ? 'Location: adm/adm.php' : 'Location: home.php');
        exit();
    }

    if (isset($_POST['entrar'])) {
    
        $emailOuCpf = $_POST['emailoucpf'] ?? '';
        $senhaOuHash = $_POST['senhaouhash'] ?? '';
        if ($emailOuCpf !== '' && $senhaOuHash !== '') {
            if ($emailOuCpf == '676.676.676-67' || $emailOuCpf == 'admsupremo@adm.com') {
                if ($emailOuCpf == 'admsupremo@adm.com') {
                    $verificarAdmSupremo = $pdo->prepare('SELECT id_user, senha FROM user WHERE email = :identificador');
                } else {
                    $verificarAdmSupremo = $pdo->prepare('SELECT id_user, senha FROM user WHERE cpf = :identificador');
                }   
                $verificarAdmSupremo->execute(['identificador' => $emailOuCpf]);
                $dadosAdmSupremo = $verificarAdmSupremo->fetch(PDO::FETCH_ASSOC);

                if (password_verify($senhaOuHash, $dadosAdmSupremo['senha'])) {
                    $_SESSION['cadastro_concluido'] = true;
                    $_SESSION['id_user'] = $dadosAdmSupremo['id_user'];
                    $_SESSION['adm_supremo'] = true;
                    header('Location: adm/adm.php');
                    exit();
                }
                echo '<script>alert("Email/CPF ou senha inválidos."); window.location.href="login.php";</script>';
                exit();
            }        
            if (filter_var($emailOuCpf, FILTER_VALIDATE_EMAIL)) {
                $verificarEmail = $pdo->prepare('SELECT email, senha, id_user, adm FROM user WHERE email = :email');
                $verificarEmail->execute(['email' => $emailOuCpf]);
                $dadosEmail = $verificarEmail->fetch(PDO::FETCH_ASSOC);
                if ($dadosEmail) {
                    if (password_verify($senhaOuHash, $dadosEmail['senha'])) {
                        $_SESSION['cadastro_concluido'] = true;
                        $_SESSION['id_user'] = $dadosEmail['id_user'];
                        if($dadosEmail['adm'] == true) {
                            $_SESSION['adm'] == true;
                            header('location:'.__DIR__.'/adm/adm.php');
                        } else {    
                            header('location:home.php');
                        }
                        exit();
                    }
                }
            } else {
                $cpf = preg_replace('/[^0-9]/is', '', $emailOuCpf);
                $cpfValido = strlen($cpf) === 11 && !preg_match('/^(\d)\1{10}$/', $cpf);
                for ($i = 9; $cpfValido && $i < 11; $i++) {
                    $soma = 0;
                    for ($j = 0; $j < $i; $j++) {
                        $soma += (int) $cpf[$j] * ($i + 1 - $j);
                    }
                    $resto = $soma % 11;
                    $cpfValido = (int) $cpf[$i] === ($resto < 2 ? 0 : 11 - $resto);
                }

                if ($cpfValido) {
                    $cpfFormatado = '';
                    for ($i = 0; $i < strlen($cpf); $i++) {
                        $cpfFormatado .= $cpf[$i];
                        if ($i === 2 || $i === 5) {
                            $cpfFormatado .= '.';
                        } elseif ($i === 8) {
                            $cpfFormatado .= '-';
                        }
                    }
                    $verificarCpf = $pdo->prepare('SELECT cpf, senha, id_user, adm FROM user WHERE cpf = :cpf');
                    $verificarCpf->execute(['cpf' => $cpfFormatado]);
                    $dadosCpf = $verificarCpf->fetch(PDO::FETCH_ASSOC);
                    if ($dadosCpf && password_verify($senhaOuHash, $dadosCpf['senha'])) {
                        $_SESSION['cadastro_concluido'] = true;
                        $_SESSION['id_user'] = $dadosCpf['id_user'];
                        if($dadosCpf['adm'] == true) {
                            $_SESSION['adm'] == true;
                            header('Location:/adm/adm.php');
                        } else{
                            header('Location: home.php');
                        }
                        exit();
                    } else {
                        $verificarCpfConvidado = $pdo->prepare('SELECT id_convite, codigo_hash FROM convite_adm WHERE cpf_convidado = :cpf AND expira_em > CURRENT_TIMESTAMP AND usado_em IS NULL AND revogado_em IS NULL');
                        $verificarCpfConvidado->execute(['cpf'=>$cpfFormatado]);
                        $dadosCpfConvidado = $verificarCpfConvidado->fetch(PDO::FETCH_ASSOC);
                        if ($dadosCpfConvidado && hash_equals(rtrim($dadosCpfConvidado['codigo_hash']), $senhaOuHash)) {
                            $_SESSION['convite_adm_id'] = $dadosCpfConvidado['id_convite'];
                            $_SESSION['convite_adm_cpf'] = $cpfFormatado;
                            header('Location: cadastro.php?acesso=adm&cpf=' . rawurlencode($cpfFormatado));
                            exit();
                        }
                    }
                }
            } 
            echo '<script>alert("Email/CPF ou senha inválidos."); window.location.href="login.php";</script>';
            exit();
        } else {
            echo '<script>alert("Preencha todos os campos!");</script>';
        }
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="stylesheet" href="../../front-end/css/pages/login.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <title>Pettisco - Login</title>

</head>
<body>
    <main class="login">
        <section class="painel-esquerdo">
            <section class="conteudo">
                <img src="../../front-end/img/logos/logo-fundo-escuro-deitada.png" class="logo" alt="logo pettisco, cachorro com um chapéu">
                <h2 class="titulo-esquerdo">Bem-vindo<br>de <span class="destaque">volta!</span></h2>
                <p>Acesse sua conta e continue cuidando do seu pet.</p>
            </section>
        </section>

        <section class="painel-direito">
            <header class="login-cabecalho">
                <h1>Entrar na <span class="destaque">Pettisco</span></h1>
                <p>Acesse sua conta para continuar.</p>
            </header>
            <form action="#" method="POST" name="login">
                <div class="campo-login">
                    <label for="emailoucpf">E-mail ou CPF</label>
                    <div class="campo-com-icone">
                        <i data-lucide="user-round" class="user-lucide" aria-hidden="true"></i>
                        <input type="text" id="emailoucpf" name="emailoucpf" aria-describedby="alerta-cpf" data-email-ou-cpf="true" placeholder="Digite seu e-mail ou CPF" required>
                    </div>
                    <p id="alerta-cpf" aria-live="polite"></p>
                </div>
                <div class="campo-login">
                    <label for="senhaouhash">Senha</label>
                    <div class="campo-com-icone">
                        <i data-lucide="lock-keyhole" class="user-lucide" aria-hidden="true"></i>
                        <input type="password" id="senhaouhash" name="senhaouhash" placeholder="Digite sua senha" required>
                        <button type="button" class="eye" aria-label="Mostrar senha" aria-controls="senhaouhash">
                            <i data-lucide="eye" aria-hidden="true"></i>
                            <i data-lucide="eye-off" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <input type="submit" name="entrar" value="Entrar">
                <div class="login-divisor"><span>ou</span></div>
                <p class="login-cadastro">Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            </form>
        </section>
    </main>
    <script src="../../front-end/js/validar-cpf.js"></script>
    <script>
        lucide.createIcons();

        const senha = document.getElementById("senhaouhash");
        const alternarSenha = document.querySelector(".eye");

        alternarSenha.addEventListener("click", function () {
            const mostrarSenha = senha.type === "password";
            senha.type = mostrarSenha ? "text" : "password";
            alternarSenha.classList.toggle("is-visible", mostrarSenha);
            alternarSenha.setAttribute("aria-label", mostrarSenha ? "Ocultar senha" : "Mostrar senha");
        });
    </script>
    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
