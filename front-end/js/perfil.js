// configurar perfil
        const modalPerfil = document.getElementById('modal');

        document.getElementById('abrirModal').addEventListener('click', () => {
            modalPerfil.showModal();
        });

        document.getElementById('fecharModal').addEventListener('click', () => {
            modalPerfil.close();
        });

        const especie = document.getElementById('especie_pet');
        const raca = document.getElementById('raca_pet');

        // Filtrar raças no cadastro de pet
        especie.addEventListener('change', () => {
            const idEspecie = especie.value;
            raca.value = '';
            Array.from(raca.options).forEach(opcao => {
                if (opcao.value === '') return;
                opcao.hidden = opcao.dataset.especie !== idEspecie;
            });
            raca.disabled = !idEspecie;
        });

        // Começar com o campo de raça desabilitado
        raca.disabled = true;

        // cadastro pet
        const modalCadastrarPet = document.getElementById('modalCadastrarPet');

        document.getElementById('cadastrarPet').addEventListener('click', () => {
            modalCadastrarPet.showModal();
        });

        document.getElementById('fecharCadastrarPet').addEventListener('click', () => {
            modalCadastrarPet.close();
        });

        //editar pet
        const modalEditar = document.getElementById('modalEditar');

        if (modalEditar) {

            const botoesEditar = document.querySelectorAll('.abrirEditar');
            const fecharEditar = document.getElementById('fecharEditar');

            const editarIdPet = document.getElementById('editarIdPet');
            const editarNomePet = document.getElementById('editarNomePet');
            const editarEspeciePet = document.getElementById('editarEspeciePet');
            const editarRacaPet = document.getElementById('editarRacaPet');
            const editarPesoPet = document.getElementById('editarPesoPet');

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

            editarEspeciePet.addEventListener('change', () => {
                filtrarRacas(
                    editarEspeciePet,
                    editarRacaPet
                );
            });

            fecharEditar.addEventListener('click', () => {
                modalEditar.close();
            });
        }

        // validação senha
        const novaSenha = document.getElementById('editarSenha');
        const confirmarNovaSenha = document.getElementById('confirmarNovaSenha');
        const alertaSenha = document.getElementById('alertaSenha');
        const formularioPerfil = document.querySelector('#modal form');

        if (novaSenha && confirmarNovaSenha && alertaSenha && formularioPerfil) {

            function validarNovaSenha() {
                const valor = novaSenha.value;

                if (valor === '') {
                    alertaSenha.textContent = '';
                    return true;
                }

                const temMaiuscula = /[A-Z]/.test(valor);
                const temNumero = /[0-9]/.test(valor);
                const temEspecial = /[^a-zA-Z0-9]/.test(valor);

                const senhaValida =
                    valor.length >= 8 &&
                    temMaiuscula &&
                    temNumero &&
                    temEspecial;

                if (!senhaValida) {
                    alertaSenha.textContent = 'A senha precisa ter 8 caracteres, uma letra maiúscula, um número e um caractere especial.';
                    alertaSenha.style.color = 'red';
                    return false;
                }

                if (valor !== confirmarNovaSenha.value) {
                    alertaSenha.textContent = 'As senhas não coincidem.';
                    alertaSenha.style.color = 'red';
                    return false;
                }

                alertaSenha.textContent = 'Senha válida!';
                alertaSenha.style.color = 'green';
                return true;
            }

            novaSenha.addEventListener('input', validarNovaSenha);
            confirmarNovaSenha.addEventListener('input', validarNovaSenha);

            formularioPerfil.addEventListener('submit', function(event) {
                if (!validarNovaSenha()) {
                    event.preventDefault();
                }
            });
        }