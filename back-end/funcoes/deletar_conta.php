<?php
    session_start();
    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:../paginas/login.php");
        exit();
    }
    $idUser = $_SESSION['id_user'];
    require_once('conexao.php');

    try{
        $delete = "delete from user where id_user = :id_user;";
        $stmt = $pdo->prepare($delete);
        $stmt->execute([':id_user' => $idUser]);

        session_destroy();
        header('location: ../paginas/cadastro.php');
    } catch (PDOException $e){
        echo 'Erro ao excluir: ' . $e->getMessage();
    }
?>