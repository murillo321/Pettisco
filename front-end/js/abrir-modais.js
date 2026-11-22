const btnAbrirModal = document.getElementById('abrirModal');
const modal = document.getElementById('modal');
const btnFecharModal = document.getElementById('fecharModal');

btnAbrirModal.addEventListener("click", () => {
    modal.showModal();
});

btnFecharModal.addEventListener("click", () => {
    modal.close();
});
