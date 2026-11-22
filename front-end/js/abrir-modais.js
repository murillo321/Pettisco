// Modal de cadastro
const btnAbrirModal = document.getElementById('abrirModal');
const modal = document.getElementById('modal');
const btnFecharModal = document.getElementById('fecharModal');

btnAbrirModal.addEventListener('click', () => modal.showModal());
btnFecharModal.addEventListener('click', () => modal.close());

// Modal de edição
const modalEditar = document.getElementById('modalEditar');
const btnFecharEditar = document.getElementById('fecharEditar');

document.querySelectorAll('.abrirEditar').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editarId').value    = btn.dataset.id;
        document.getElementById('editarNome').value  = btn.dataset.nome;
        document.getElementById('editarEmail').value = btn.dataset.email;
        document.getElementById('editarSenha').value = '';

        modalEditar.showModal();
    });
});

btnFecharEditar.addEventListener('click', () => modalEditar.close());
