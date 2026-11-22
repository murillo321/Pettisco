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
    $sql = $pdo->query('select * from convite_adm where revogado_em is null;');
    $convites = $sql->fetchAll();
?>
<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettisco - Convites</title>
</head>
<body>
    <section>
        <?php foreach($convites as $convites){
            echo 'Token: '.$convites['codigo_hash'].' Cpf: '.$convites['cpf_convidado'].' Válido até: '.$convites['expira_em'];
            if($convites['usado_em'] == null){
                echo ' Convite não utilizado <a href="convites.php?cancelar='.$convites['id_convite'].'">Cancelar convite</a></br>';
            }
            else{
                echo ' Convite já utilizado <p>Cancelar convite</p></br>';
            }
        } ?>
    </section>
    <a href="adm.php">Voltar para página de administradores</a>
    <script src="../../../front-end/js/adm.js"></script>
</body>
</html>