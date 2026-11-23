<?php
function verificaradm($pdo, $id_user){
    $sql = ('select adm from user where id_user = :id');
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id_user
    ]);
    $result = $stmt->fetchColumn();
    if(!$result){
        echo '<script>alert("Você não tem acesso a esta página"); window.location.href="home.php";</script>';
        exit;
    }
}