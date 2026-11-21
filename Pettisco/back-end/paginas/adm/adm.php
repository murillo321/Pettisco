<?php
    require_once('../../funcoes/conexao.php');
    session_start();
    /*if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:cadastro.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    */
    $id_user = 1;
    $sql = $pdo->query('select * from user where adm = true;');
    $adm = $sql->fetchAll();

    if(isset($_POST['convite'])){
        $cpf = $_POST['cpf'];
        $expira = str_replace('T', ' ', $_POST['expira']);

        if(!empty($cpf) && !empty($expira)){
            $sql = ('select * from convite_adm where cpf_convidado = :cpf;');
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cpf' => $cpf
            ]);
            $existe = $stmt->fetchColumn();
            if($existe){
                echo '<script>alert("Convite já criado para esse cpf"); window.location.href="adm.php";</script>';
                exit;
            }
            
            $token = bin2hex(random_bytes(4));
            $sql = ('insert into convite_adm (codigo_hash,cpf_convidado,id_adm_criador,expira_em) values (:token,:cpf,:id,:expira);');
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':token' => $token,
                ':cpf' => $cpf,
                ':id' => $id_user,
                ':expira' => $expira
            ]);
            header('location: ?token='.$token);
        }
        else{
            echo '<script>alert("Preencha todos os dados!"); window.location.href="adm.php";</script>';
            exit;
        }
    }
    if(isset($_GET['token'])){
        $token = $_GET['token'];
        echo '<script>const abrirmodal2 = true;</script>';
    }

?>
<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Adm</title>
</head>
<body>
    <button id="abrir_modal" onclick="abrir_modal()">Gerar código de convite</button>
    <dialog id="modal1">
        <form name="cadastro_adm" action="#" method="POST">
            <label for="cpf">Cpf</label>
            <input type="text" name="cpf" id="cpf" maxlength="14">
            <label for="expira">Data de expiração</label>
            <input type="datetime-local" name="expira" id="expira" min="<?= date('Y-m-d\TH:i') ?>">
            <input type="submit" value="Gerar convite" name="convite">
        </form>
        <button id="fechar_modal" onclick="fechar_modal()">Cancelar</button>
    </dialog>
    <dialog id="modal2">
        <p>Token gerado é:<?php echo $token; ?></p>
        <button id="fechar_modal" onclick="fechar_modal2()">Fechar</button>
    </dialog>
    <section>
        <?php
            foreach ($adm as $adm){
                echo 'Nome: '.$adm['username'].' Cpf: '.$adm['cpf'].'</br>';
            }
        ?>
    </section>
    <script src="../../../front-end/js/adm.js"></script>
</body>
</html>