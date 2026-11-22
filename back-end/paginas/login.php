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
                $verificarEmail = $pdo->prepare('SELECT email, senha, id_user FROM user WHERE email = :email');
                $verificarEmail->execute(['email' => $emailOuCpf]);
                $dadosEmail = $verificarEmail->fetch(PDO::FETCH_ASSOC);
                if ($dadosEmail) {
                    if (password_verify($senhaOuHash, $dadosEmail['senha'])) {
                        $_SESSION['cadastro_concluido'] = true;
                        $_SESSION['id_user'] = $dadosEmail['id_user'];    
                        header('location: home.php');
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
                    $verificarCpf = $pdo->prepare('SELECT cpf, senha, id_user FROM user WHERE cpf = :cpf');
                    $verificarCpf->execute(['cpf' => $cpfFormatado]);
                    $dadosCpf = $verificarCpf->fetch(PDO::FETCH_ASSOC);
                    if ($dadosCpf && password_verify($senhaOuHash, $dadosCpf['senha'])) {
                        $_SESSION['cadastro_concluido'] = true;
                        $_SESSION['id_user'] = $dadosCpf['id_user'];
                        header('Location: home.php');
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
            <form action="#" method="POST" name="login">
                <label for="emailoucpf">Digite seu Email ou CPF</label>
                <input type="text" id="emailoucpf" name="emailoucpf" data-email-ou-cpf="true" required>
                <label for="senhaouhash">Digite a sua senha</label>
                <input type="password" name="senhaouhash">

                <input type="submit" name="entrar" value="Entrar">
            </form>
            <p>Ainda não tem uma conta? <a href="cadastro.php">crie uma!</a></p>
        </section>
    </main>
    <script src="../../front-end/js/validar-cpf.js"></script>
</body>
</html>
