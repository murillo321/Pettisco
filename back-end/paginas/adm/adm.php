<?php
    require_once('../../funcoes/conexao.php');
    require('../../funcoes/funcoesadm.php');
    session_start();
    /*if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:cadastro.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    */
    $id_user = 3;
    verificaradm($pdo, $id_user);
    $sql = $pdo->query('select * from user where adm = true;');
    $adm = $sql->fetchAll();

    if(isset($_POST['convite'])){
        $cpf = $_POST['cpf'];
        $expira = str_replace('T', ' ', $_POST['expira']);

        if(!empty($cpf) && !empty($expira)){
            $sql = ('select * from convite_adm where cpf_convidado = :cpf and usado_em is null;');
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
            exit;
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
    if(isset($_GET['id'])){
        $sql = $pdo->prepare('select * from user where id_user = :id;');
        $sql->execute([
            ':id' => $id_user
        ]);

        $result = $sql->fetch(PDO::FETCH_ASSOC);
        if($result['cpf'] !== '676.767.676-67'){
            echo '<script>alert("Você não tem permissão para isso!"); window.location.href="adm.php";</script>';
            exit;
        }
        $sql = $pdo->prepare('select * from user where id_user = :id;');
        $sql->execute([
            ':id' => $_GET['id']
        ]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        $excluir = $_GET['id'];
        $sql = $pdo->prepare('update user set adm = 0 where id_user = :id');
        $sql->execute([
            ':id' => $excluir
        ]);
        echo '<script>alert("O user: '.$result['username'].', com o cpf: '.$result['cpf'].' não é mais um administrador!"); window.location.href="adm.php";</script>';
        exit;
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
                if($adm['cpf'] == '676.767.676-67'){

                }
                else{
                        echo 'Nome: '.$adm['username'].' Cpf: '.$adm['cpf'].' <a href="?id='.$adm['id_user'].'" onclick="return confirm(\'Deseja mesmo remover de administrador o usuário: '.$adm['username'].'?\')">Excluir adm</a></br>';
                }
            }
        ?>
    </section>
    <a href="convites.php">Ver convites</a>
    <script src="../../../front-end/js/adm.js"></script>
</body>
</html>