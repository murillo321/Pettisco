<?php
    session_start();
    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:login.php");
        exit();
    }
    $idUser = $_SESSION['id_user'];
    require_once('../funcoes/conexao.php');

    echo "<script>confirm('Você realmente deseja excluir sua conta?')</script>";

?>