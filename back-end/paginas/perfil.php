<?php
    session_start(); //arrumar remover img e organizar o codigo, padronizar nome da img do editar pet, arrumar modais
    if (!isset($_SESSION['cadastro_concluido'])) {
        header("location:login.php");
        exit();
    }
    $idUser = $_SESSION['id_user'];
    require_once('../funcoes/conexao.php');

    $select_user = "select * from user where id_user = :id_user;";
    $stmt = $pdo->prepare($select_user);
    $stmt->execute([':id_user' => $idUser]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    $select_pets = "select p.*,e.nome_especie,
    r.nome_raca from pet p left join especie e on p.id_especie = e.id_especie
    left join raca r ON p.id_raca = r.id_raca where p.id_user = :id_user;";
    $stmt = $pdo->prepare($select_pets);
    $stmt->execute([':id_user' => $idUser]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $qntd_pets = count($pets);

    $select_especies = "select * from especie order by nome_especie;";
    $stmt = $pdo->prepare($select_especies);
    $stmt->execute();
    $especies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $select_racas = "select * from raca order by nome_raca;";
    $stmt = $pdo->prepare($select_racas);
    $stmt->execute();
    $racas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(isset($_POST['editar'])){
        $id_user = $_SESSION['id_user'];
        $novo_nome = $_POST['novo_nome'];
        $novo_email = $_POST['novo_email'];
        $nova_senha = $_POST['nova_senha'];

        $nova_foto = null;
        $caminho_nova_foto = null;

        try{
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
                    echo '<script>alert("Erro no envio da imagem!")</script>';
                    exit;
                }

                $tiposPermitidos = ['image/jpeg' => 'jpg','image/png' => 'png','image/webp' => 'webp'];
                $tipoImagem = mime_content_type($_FILES['foto_perfil']['tmp_name']);

                if (!isset($tiposPermitidos[$tipoImagem])) {
                    echo "<script>alert('Formato de imagem inválido!');</script>";
                    exit;
                }

                $extensao = $tiposPermitidos[$tipoImagem];
                $nomeImagem = 'foto_perfil_' . $usuario['username'] . '.' . $extensao;

                $pastaDestino = __DIR__ . '/../../front-end/img/perfil/usuario/';

                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'],$pastaDestino . $nomeImagem)) {
                    $img_url = 'img/perfil/usuario/' . $nomeImagem;
                } else {
                    echo '<script>alert("Erro ao salvar a imagem!");</script>';
                    exit;
                }

                $caminho_nova_imagem = $pastaDestino . $nomeImagem;
                $nova_foto = 'img/perfil/usuario/' . $nomeImagem;
            }

            $foto_url = $nova_foto ?? $usuario['foto_url'];

        } catch (PDOException $e){
            echo 'Erro ao receber imagem: '. $e->getMessage();
        }

        if(empty($novo_nome)){
            $novo_nome = $usuario['username'];
        }

        if(empty($novo_email)){
            $novo_email = $usuario['email'];
        }

        if(empty($nova_senha)){
            $senha_hash = $usuario['senha'];
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        }

        try{
            $atualizar = 'update user set username = :nome, email = :email, senha = :senha, foto_url = :foto_url where id_user = :id_user;';
            $stmt = $pdo->prepare($atualizar);
            $stmt->execute([
                ':nome' => $novo_nome,
                ':email' => $novo_email,
                ':senha' => $senha_hash,
                ':foto_url' => $foto_url,
                ':id_user' => $id_user
            ]);
            header('location:perfil.php');
        } catch (PDOException $e){
            echo 'Erro ao atualizar: '. $e->getMessage();
        }
    }

    //parte do pet maneiro
    if (isset($_POST['editar_pet'])) {

        $id_pet = $_POST['id_pet'];
        $novo_nome = trim($_POST['nome_pet']);
        $nova_especie = $_POST['especie_pet'];
        $nova_raca = !empty($_POST['raca_pet']) ? $_POST['raca_pet'] : null;
        $novo_peso = !empty($_POST['peso_pet']) ? $_POST['peso_pet'] : null;

        try {

            // Verificar se o pet pertence ao usuário logado
            $select_pet = "select * from pet 
                        where id_pet = :id_pet 
                        and id_user = :id_user;";

            $stmt = $pdo->prepare($select_pet);
            $stmt->execute([
                ':id_pet' => $id_pet,
                ':id_user' => $idUser
            ]);

            $pet_atual = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pet_atual) {
                echo "<script>alert('Pet não encontrado!');</script>";
                exit;
            }

            // Verificar se a raça pertence à espécie selecionada
            if ($nova_raca !== null) {

                $select_raca = "select count(*) from raca 
                                where id_raca = :id_raca 
                                and id_especie = :id_especie;";

                $stmt = $pdo->prepare($select_raca);
                $stmt->execute([
                    ':id_raca' => $nova_raca,
                    ':id_especie' => $nova_especie
                ]);

                if ($stmt->fetchColumn() == 0) {
                    echo "<script>alert('Raça inválida para esta espécie!');</script>";
                    exit;
                }
            }

            // Manter a foto atual caso nenhuma nova seja enviada
            $foto_url = $pet_atual['foto_pet_url'];

            // Verificar envio de uma nova foto
            if (isset($_FILES['foto_pet']) && $_FILES['foto_pet']['error'] !== UPLOAD_ERR_NO_FILE) {

                if ($_FILES['foto_pet']['error'] !== UPLOAD_ERR_OK) {
                    echo "<script>alert('Erro no envio da imagem!');</script>";
                    exit;
                }

                $tiposPermitidos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $tipoImagem = mime_content_type($_FILES['foto_pet']['tmp_name']);

                if (!isset($tiposPermitidos[$tipoImagem])) {
                    echo "<script>alert('Formato de imagem inválido!');</script>";
                    exit;
                }

                $extensao = $tiposPermitidos[$tipoImagem];

                $nomeImagem = 'foto_pet_' . $id_pet . '.' . $extensao;

                $pastaDestino = __DIR__ . '/../../front-end/img/perfil/pet/';

                if (!is_dir($pastaDestino)) {
                    mkdir($pastaDestino, 0777, true);
                }

                if (move_uploaded_file(
                    $_FILES['foto_pet']['tmp_name'],
                    $pastaDestino . $nomeImagem
                )) {

                    $foto_url = 'img/perfil/pet/' . $nomeImagem;

                } else {
                    echo "<script>alert('Erro ao salvar a imagem!');</script>";
                    exit;
                }
            }

            // Atualizar os dados do pet
            $atualizar_pet = "update pet set 
                            nome = :nome,
                            id_especie = :id_especie,
                            id_raca = :id_raca,
                            peso = :peso,
                            foto_pet_url = :foto_pet_url
                            where id_pet = :id_pet 
                            and id_user = :id_user;";

            $stmt = $pdo->prepare($atualizar_pet);

            $stmt->execute([
                ':nome' => $novo_nome,
                ':id_especie' => $nova_especie,
                ':id_raca' => $nova_raca,
                ':peso' => $novo_peso,
                ':foto_pet_url' => $foto_url,
                ':id_pet' => $id_pet,
                ':id_user' => $idUser
            ]);

            header('location:perfil.php');
            exit;

        } catch (PDOException $e) {
            echo 'Erro ao atualizar pet: ' . $e->getMessage();
        }
    }

    if(isset($_POST['excluir_pet'])){
        $id_pet = $_POST['id_pet'];

        try{
            $deletar = "delete from pet where id_pet = :id_pet and id_user = :id_user;";
            $stmt = $pdo->prepare($deletar);
            $stmt->execute([
                ':id_pet' => $id_pet,
                ':id_user' => $idUser
            ]);

            header('location:perfil.php');
        } catch (PDOException $e){
            echo 'Erro ao deletar: ' . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../front-end/css/main.css">
    <link rel="shortcut icon" href="../../front-end/img/logos/favicon.ico" type="image/x-icon"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        img{
            width: 200px;
            height: auto;
        }
    </style>
    <title>Pettisco - Perfil</title>
</head>
<body>

    <?php require_once __DIR__ . '/../componentes/header.php'; ?>
    
    <main>
        <section>
            <?php if($usuario['foto_url'] == null): ?>
                <img src="../../front-end/img/perfil/usuario/usuario-sem-foto.png">
            <?php else:?>
                <img src="../../front-end/<?= htmlspecialchars($usuario['foto_url']) ?>" alt="Foto do usuario <?= $usuario['username'] ?>">
            <?php endif; ?>
            <h4><?= htmlspecialchars($usuario['username']) ?></h4>
            <p><?= htmlspecialchars($usuario['email']) ?></p>
            <p>CPF: <?= htmlspecialchars($usuario['cpf']) ?></p>
            <button id="abrirModal">Configurar Perfil</button>

            <dialog id="modal">
                <button id="fecharModal">x</button>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_user" id="editarId">

                    <label for="foto_perfil">
                        <?php if($usuario['foto_url'] == null): ?>
                            <img src="../../front-end/img/perfil/usuario/usuario-sem-foto.png">
                        <?php else:?>
                            <img src="../../front-end/<?= htmlspecialchars($usuario['foto_url']) ?>" alt="Foto do usuario <?= $usuario['username'] ?>">
                        <?php endif; ?>
                    </label><br><!--mostrar pré visualização da img com js-->
                    <input type="file" name="foto_perfil" id="foto_perfil" accept="image/png, image/jpeg, image/webp"><br>

                    <label for="editarNome">Nome:</label><br>
                    <input type="text" id="editarNome" name="novo_nome" value="<?= htmlspecialchars($usuario['username'])?>" required><br>

                    <label for="editarEmail">Email:</label><br>
                    <input type="email" id="editarEmail" name="novo_email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br>

                    <label for="editarSenha">Nova senha (deixe em branco para manter):</label><br>
                    <input type="password" id="editarSenha" minlength="8" placeholder="Mínimo de 8 caracteres" name="nova_senha"><br><br>

                    <a href="<? __DIR__ ?>../funcoes/deletar_conta.php?id_user=<?= htmlspecialchars($idUser)?>">Deletar Conta</a><br><br>

                    <input type="submit" name="editar" value="Salvar">
                </form>
            </dialog>
        </section>
        <br>

        <hr>

        <br>
        <section>
            <h3>Meus <?= htmlspecialchars($qntd_pets) ?> pets:</h3>
            <button id="cadastrarPet">Cadastrar Pet +</button>
            <dialog id="modalCadastrarPet">
                <button type="button" id="fecharCadastrarPet">x</button>
                <h2>Cadastrar Pet</h2>

                <form action="../funcoes/cadastrar_pet.php" method="POST" enctype="multipart/form-data">
                    <label for="foto_pet">Foto do pet:</label><br>
                    <input type="file" name="foto_pet" id="foto_pet" accept="image/png, image/jpeg, image/webp">
                    <br>

                    <label for="nome_pet">Nome:</label><br>
                    <input type="text" name="nome_pet" id="nome_pet" required>
                    <br>

                    <label for="especie_pet">Espécie:</label><br>
                    <select name="especie_pet" id="especie_pet" required>
                        <option value="">Selecione uma espécie</option>
                        <?php foreach($especies as $especie): ?>
                            <option value="<?= $especie['id_especie'] ?>">
                                <?= htmlspecialchars($especie['nome_especie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>

                    <label for="raca_pet">Raça:</label><br>
                    <select name="raca_pet" id="raca_pet" disabled>
                        <option value="">Selecione uma espécie primeiro</option>
                        <?php foreach($racas as $raca): ?>
                            <option value="<?= $raca['id_raca'] ?>" data-especie="<?= $raca['id_especie'] ?>">
                                <?= htmlspecialchars($raca['nome_raca']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>

                    <label for="peso_pet">Peso (kg):</label><br>
                    <input type="number" name="peso_pet" id="peso_pet" step="0.01" min="0">
                    <br><br>

                    <input type="submit" name="cadastrar_pet" value="Cadastrar">
                </form>
            </dialog>
            <br>
            <?php if($qntd_pets > 0): ?>
                <section>
                    <?php foreach($pets as $pet): ?>
                        <article class="card-pet">
                            <?php if($pet['foto_pet_url'] == null): ?>
                                <img src="../../front-end/img/perfil/pet/pet-sem-foto.png">
                            <?php else:?>
                                <img src="../../front-end/<?= htmlspecialchars($pet['foto_pet_url']) ?>" alt="Foto do pet <?= $pet['nome'] ?>">
                            <?php endif; ?>

                            <h3><?= htmlspecialchars($pet['nome']) ?></h3>
                            <p>Espécie: <?= htmlspecialchars($pet['nome_especie'] ?? 'Não informada') ?></p>
                            <p>Raça: <?= htmlspecialchars($pet['nome_raca'] ?? 'Não informada') ?></p>
                            <p>Peso: <?= htmlspecialchars($pet['peso'] ?? 'Não informado') ?> kg</p>

                            <button type="button" class="abrirEditar"
                                data-idpet="<?= $pet['id_pet'] ?>"
                                data-nomepet="<?= htmlspecialchars($pet['nome'], ENT_QUOTES) ?>"
                                data-especie="<?= $pet['id_especie'] ?>"
                                data-raca="<?= $pet['id_raca'] ?? '' ?>"
                                data-peso="<?= $pet['peso'] ?? '' ?>">
                                Editar
                            </button>
                            <form action="#" method="POST">
                                <input type="hidden" name="id_pet" value="<?= $pet['id_pet'] ?>">
                                <input type="submit" name="excluir_pet" value="Excluir">
                            </form>
                        </article>
                    <?php endforeach; ?>
                </section>

                <dialog id="modalEditar">
                    <button type="button" id="fecharEditar">x</button>

                    <h2>Editar Pet</h2>

                    <form action="" method="POST" enctype="multipart/form-data">

                        <input type="hidden" name="id_pet" id="editarIdPet">

                        <label for="editarFotoPet">Foto do pet:</label><br>
                        <input type="file" name="foto_pet" id="editarFotoPet"
                            accept="image/png, image/jpeg, image/webp">
                        <br>

                        <label for="editarNomePet">Nome:</label><br>
                        <input type="text" name="nome_pet" id="editarNomePet" required>
                        <br>

                        <label for="editarEspeciePet">Espécie:</label><br>

                        <select name="especie_pet" id="editarEspeciePet" required>
                            <option value="">Selecione uma espécie</option>

                            <?php foreach($especies as $especie): ?>
                                <option value="<?= $especie['id_especie'] ?>">
                                    <?= htmlspecialchars($especie['nome_especie']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                        <br>

                        <label for="editarRacaPet">Raça:</label><br>

                        <select name="raca_pet" id="editarRacaPet" disabled>
                            <option value="">Selecione uma espécie primeiro</option>

                            <?php foreach($racas as $raca): ?>
                                <option value="<?= $raca['id_raca'] ?>"
                                    data-especie="<?= $raca['id_especie'] ?>">

                                    <?= htmlspecialchars($raca['nome_raca']) ?>

                                </option>
                            <?php endforeach; ?>

                        </select>
                        <br>

                        <label for="editarPesoPet">Peso (kg):</label><br>
                        <input type="number" name="peso_pet" id="editarPesoPet"
                            step="0.01" min="0">
                        <br><br>

                        <input type="submit" name="editar_pet" value="Salvar alterações">

                    </form>
                </dialog>
            <?php else: ?>
                <p>Você ainda não tem nenhum pet cadastrado!</p>
            <?php endif; ?>
        </section>
    </main>

    <?php require_once __DIR__ . '/../componentes/footer.php'; ?>
    <script>
        lucide.createIcons();

        const modalCadastrarPet = document.getElementById('modalCadastrarPet');
        document.getElementById('cadastrarPet').onclick = () => {
            modalCadastrarPet.showModal();
        };
        document.getElementById('fecharCadastrarPet').onclick = () => {
            modalCadastrarPet.close();
        };

        const especie = document.getElementById('especie_pet');
        const raca = document.getElementById('raca_pet');
        especie.addEventListener('change', () => {
            const idEspecie = especie.value;
            raca.value = '';

            Array.from(raca.options).forEach(opcao => {

                if (opcao.value === '') return;

                opcao.hidden = opcao.dataset.especie !== idEspecie;

            });
            raca.disabled = !idEspecie;

        });

        const modalEditar = document.getElementById('modalEditar');

        const botoesEditar = document.querySelectorAll('.abrirEditar');

        const fecharEditar = document.getElementById('fecharEditar');

        const editarIdPet = document.getElementById('editarIdPet');
        const editarNomePet = document.getElementById('editarNomePet');
        const editarEspeciePet = document.getElementById('editarEspeciePet');
        const editarRacaPet = document.getElementById('editarRacaPet');
        const editarPesoPet = document.getElementById('editarPesoPet');


        // Filtrar raças de acordo com a espécie selecionada
        function filtrarRacas(selectEspecie, selectRaca, idRaca = '') {

            const idEspecie = selectEspecie.value;

            selectRaca.value = '';

            Array.from(selectRaca.options).forEach(opcao => {

                if (opcao.value === '') return;

                opcao.hidden = opcao.dataset.especie !== idEspecie;

            });

            selectRaca.disabled = !idEspecie;

            if (idRaca && idEspecie) {
                selectRaca.value = idRaca;
            }
        }


        // Abrir modal e preencher os campos
        botoesEditar.forEach(botao => {

            botao.addEventListener('click', () => {

                editarIdPet.value = botao.dataset.idpet;

                editarNomePet.value = botao.dataset.nomepet;

                editarEspeciePet.value = botao.dataset.especie;

                editarPesoPet.value = botao.dataset.peso;

                filtrarRacas(
                    editarEspeciePet,
                    editarRacaPet,
                    botao.dataset.raca
                );

                modalEditar.showModal();

            });

        });


        // Atualizar raças ao trocar espécie
        editarEspeciePet.addEventListener('change', () => {

            filtrarRacas(editarEspeciePet, editarRacaPet);

        });


        // Fechar modal
        fecharEditar.onclick = () => {
            modalEditar.close();
        };
    </script>
    <script src="../../front-end/js/abrir-modais.js"></script>
</body>
</html>