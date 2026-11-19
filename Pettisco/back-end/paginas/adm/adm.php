<?php
    require_once('../../funcoes/conexao.php');
    if(isset($_POST['pesquisa'])){
        $sql = $pdo->query('select * from user where adm = true;');
        $adm = $sql->fetchAll();
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
    <form name="pesquisa_adm" action="#" method="POST">
        <input type="submit" value="Pesquisa" name="pesquisa">
    </form>
    <section>
        <?php
        if(!empty($adm)){
            foreach ($adm as $adm){
                echo 'Nome: '.$adm['username'].' Cpf: '.$adm['cpf'].'</br>';
            }
        }
        ?>
    </section>
</body>
</html>