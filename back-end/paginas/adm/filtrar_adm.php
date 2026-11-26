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

    header('Content-Type: application/json; charset=utf-8');
    $ordem = $_GET['ordem'];

    switch ($ordem) {
        case 'nome_desc':
            $ordenar = 'username desc';
            break;
        case 'data_asc':
            $ordenar = 'criado_em asc';
            break;
        case 'data_desc':
            $ordenar = 'criado_em desc';
            break;
        default:
            $ordenar = 'username asc';
            break;
    }

    $sql = $pdo->query('select id_user, username, cpf, email, foto_url from user where adm = true and cpf != "676.767.676-67" order by '.$ordenar.';');
    $adms = $sql->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($adms);