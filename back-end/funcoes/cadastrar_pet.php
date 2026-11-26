<?php
    session_start();
    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:login.php");
        exit();
    }
    $idUser = $_SESSION['id_user'];
    require_once('../funcoes/conexao.php');

    if(isset($_POST['cadastrar_pet'])){
        $nome = $_POST['nome_pet'];
        $especie = $_POST['especie_pet'];
        $raca = $_POST['raca_pet'];
        $peso = $_POST['peso_pet']; 

        $foto_pet = null;
        $caminho_foto_pet = null;

        try{
            if (isset($_FILES['foto_pet']) && $_FILES['foto_pet']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['foto_pet']['error'] !== UPLOAD_ERR_OK) {
                    echo '<script>alert("Erro no envio da imagem!")</script>';
                    exit;
                }

                $tiposPermitidos = ['image/jpeg' => 'jpg','image/png' => 'png','image/webp' => 'webp'];
                $tipoImagem = mime_content_type($_FILES['foto_pet']['tmp_name']);

                if (!isset($tiposPermitidos[$tipoImagem])) {
                    echo "<script>alert('Formato de imagem inválido!');</script>";
                    exit;
                }

                $extensao = $tiposPermitidos[$tipoImagem];
                $nomeImagem = 'foto_pet_' . $nome . '_user_' . $idUser . '.' . $extensao;

                $pastaDestino = __DIR__ . '/../../front-end/img/perfil/pet/';

                if (move_uploaded_file($_FILES['foto_pet']['tmp_name'],$pastaDestino . $nomeImagem)) {
                    $img_url = 'img/perfil/pet/' . $nomeImagem;
                } else {
                    echo '<script>alert("Erro ao salvar a imagem!");</script>';
                    exit;
                }

                $caminho_foto_pet = $pastaDestino . $nomeImagem;
                $foto_pet = 'img/perfil/pet/' . $nomeImagem;
            }

            $foto_pet_url = $foto_pet;

        } catch (PDOException $e){
            echo 'Erro ao receber imagem: '. $e->getMessage();
        }

        if(empty($nome) || empty($especie) || empty($raca) || empty($peso)){
            echo '<script>alert("Preencha todos os campos!")</script>';
            exit;
        }

        try{
            $insert = "insert into pet (id_user,nome,id_especie,id_raca,foto_pet_url,peso) values (:id_user, :nome, :id_especie, :id_raca, :foto_pet_url, :peso);";
            $stmt = $pdo->prepare($insert);
            $stmt->execute([
                ':id_user' => $idUser,
                ':nome' => $nome,
                ':id_especie' => $especie,
                ':id_raca' => $raca,
                ':foto_pet_url' => $foto_pet_url,
                ':peso' => $peso
            ]);

            header('location:../paginas/perfil.php');

        } catch (PDOException $e){
            echo 'Erro ao inserir: '. $e->getMessage();
        }
    }
?>