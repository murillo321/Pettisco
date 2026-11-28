const mensagemCarrinho = document.getElementById('mensagem-carrinho');
const contadorCarrinho = document.querySelector('.site-header-contador');
const controles = document.querySelectorAll('.carrinho-form-quantidade input[type="number"], .carrinho-form-quantidade button');

function exibirMensagem(texto) {
    mensagemCarrinho.textContent = texto;
    mensagemCarrinho.hidden = false;
    mensagemCarrinho.className = 'carrinho-mensagem carrinho-mensagem-erro';
}

document.querySelectorAll('.carrinho-form-quantidade').forEach((form) => {
    const quantidade = form.querySelector('input[name="quantidade"]');
    const diminuir = form.querySelector('[data-quantidade="diminuir"]');
    const aumentar = form.querySelector('[data-quantidade="aumentar"]');
    const subtotal = form.closest('.carrinho-item').querySelector('.carrinho-subtotal');

    let quantidadeSalva = quantidade.value;

    async function salvarQuantidade() {
        if (!quantidade.reportValidity()) {
            return;
        }

        const dados = new FormData(form);
        controles.forEach(controle => controle.disabled = true);

        try {
            const resposta = await fetch(window.location.href, {
                method: 'POST',
                body: dados
            });

            const resultado = await resposta.json();

            if (!resposta.ok || !resultado.sucesso) {
                quantidade.value = quantidadeSalva;
                exibirMensagem(resultado.mensagem || 'Erro ao atualizar quantidade.');
                return;
            }

            quantidadeSalva = resultado.quantidade;
            quantidade.value = quantidadeSalva;
            subtotal.textContent = `Subtotal: R$ ${resultado.subtotal}`;

            if (contadorCarrinho) {
                contadorCarrinho.textContent = resultado.contador;
            }

            mensagemCarrinho.hidden = true;
        } catch (erro) {
            quantidade.value = quantidadeSalva;
            exibirMensagem('Erro ao atualizar a quantidade.');
            console.error(erro);
        } finally {
            controles.forEach(controle => controle.disabled = false);
        }
    }

    quantidade.addEventListener('change', salvarQuantidade);

    form.addEventListener('submit', (evento) => {
        evento.preventDefault();
        salvarQuantidade();
    });

    diminuir.addEventListener('click', () => {
        quantidade.stepDown();
        salvarQuantidade();
    });

    aumentar.addEventListener('click', () => {
        quantidade.stepUp();
        salvarQuantidade();
    });
});