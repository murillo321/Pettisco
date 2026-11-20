<?php
    require_once (__DIR__ . '../../funcoes/conexao.php');

    $id_user = $_GET['id_user'];

    if(!$id_user){
        header('location:'. __DIR__ . '../paginas/adm/usuarios.php');
    }

    if(isset($_GET['edit_user'])){
        $id_user = $_GET['edit_user'];

        try{
            $select_user = "select * from user where id_user = :id_user;";
            $stmt = $pdo->prepare($select_user);
            $stmt->execute([':id_user' => $id_user]);
            $dados_user = $stmt->fetch();
        } catch (PDOException $e){
            echo 'Erro ao consultar: '. $e->getMessage();
        }
    }
?>

    
        <button id="fecharEditar">X</button>
        <form action="#" method="POST">
            <input type="hidden" name="id_user" value="<?= htmlspecialchars($dados_user['id_user']) ?>">
            <label for="nome">Digite o novo nome:</label><br>
            <input type="text" id="nome" name="novo_nome" value="<?= htmlspecialchars($dados_user['username']) ?>" required><br>
            <label for="email">Digite o email:</label><br>
            <input type="email" id="email" name="novo_email" value="<?= htmlspecialchars($dados_user['email']) ?>" required><br>
            <label for="senha">Redefina a senha:</label><br>
            <input type="password" id="senha" name="senha" required><br><br>
            <input type="submit" name="atualizar_dados" value="Atualizar Dados">
        </form>
    </dialog>