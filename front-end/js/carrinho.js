const mensagemCarrinho = document.getElementById('mensagem-carrinho');
const contadorCarrinho = document.querySelector('.site-header__contador');

function exibirMensagem(texto, sucesso) {
    mensagemCarrinho.textContent = texto;
    mensagemCarrinho.hidden = false;
    mensagemCarrinho.className = `carrinho__mensagem carrinho__mensagem--${sucesso ? 'sucesso' : 'erro'}`;
}

document.querySelectorAll('.carrinho__form-quantidade').forEach((form) => {
    const quantidade = form.querySelector('input[name="quantidade"]');
    const diminuir = form.querySelector('[data-quantidade="diminuir"]');
    const aumentar = form.querySelector('[data-quantidade="aumentar"]');
    const subtotal = form.closest('.carrinho__item').querySelector('.carrinho__subtotal');
    let quantidadeSalva = quantidade.value;
    let temporizador;
    let revisao = 0;

    function atualizarPrevia() {
        let contadorPrevio = 0;
        document.querySelectorAll('.carrinho__form-quantidade input[name="quantidade"]').forEach((input) => {
            contadorPrevio += Number(input.value) || 0;
        });
        if (contadorCarrinho) {
            contadorCarrinho.textContent = contadorPrevio;
        }

        const subtotalPrevio = Number(form.dataset.preco) * Number(quantidade.value);
        subtotal.textContent = `Subtotal: R$ ${subtotalPrevio.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    }

    async function salvarQuantidade(revisaoRequisicao) {
        if (!quantidade.value || !quantidade.checkValidity()) {
            quantidade.reportValidity();
            return;
        }

        try {
            const resposta = await fetch(window.location.href, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const resultado = await resposta.json();
            if (revisaoRequisicao !== revisao) {
                return;
            }

            if (!resposta.ok || !resultado.sucesso) {
                quantidade.value = quantidadeSalva;
                atualizarPrevia();
                exibirMensagem(resultado.mensagem || 'Não foi possível atualizar a quantidade.', false);
                return;
            }

            quantidadeSalva = String(resultado.quantidade);
            quantidade.value = quantidadeSalva;
            subtotal.textContent = `Subtotal: R$ ${resultado.subtotal}`;
            if (contadorCarrinho) {
                contadorCarrinho.textContent = resultado.contador;
            }
            exibirMensagem(resultado.mensagem, true);
        } catch (erro) {
            if (revisaoRequisicao !== revisao) {
                return;
            }
            console.error(erro);
            quantidade.value = quantidadeSalva;
            atualizarPrevia();
            exibirMensagem('Erro ao salvar a quantidade. Tente novamente.', false);
        }
    }

    function agendarAtualizacao() {
        clearTimeout(temporizador);
        revisao += 1;
        if (!quantidade.value || !quantidade.checkValidity()) {
            return;
        }

        atualizarPrevia();
        temporizador = setTimeout(() => salvarQuantidade(revisao), 300);
    }

    quantidade.addEventListener('input', agendarAtualizacao);
    form.addEventListener('submit', (evento) => {
        evento.preventDefault();
        agendarAtualizacao();
    });

    diminuir.addEventListener('click', () => {
        const minimo = Number(quantidade.min);
        const maximo = Number(quantidade.max);
        const atual = Number(quantidade.value) || minimo;
        quantidade.value = Math.max(minimo, Math.min(maximo, atual - 1));
        agendarAtualizacao();
    });

    aumentar.addEventListener('click', () => {
        const minimo = Number(quantidade.min);
        const maximo = Number(quantidade.max);
        const atual = Number(quantidade.value) || minimo;
        quantidade.value = Math.max(minimo, Math.min(maximo, atual + 1));
        agendarAtualizacao();
    });
});
