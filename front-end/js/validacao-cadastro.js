const senha = document.getElementById("senha");
const confirmarSenha = document.getElementById("confirmar-senha");
const cpf = document.getElementById("cpf");
const alertaCPF = document.getElementById("alerta-cpf");

const quantidade = document.getElementById("quantidade");
const maiuscula = document.getElementById("maiuscula");
const numeros = document.getElementById("numeros");
const especiais = document.getElementById("especiais");
const alertaSenha = document.getElementById("alerta-senha");

const formulario = document.querySelector('form[name="cadastro"]');
const modalErroSenha = document.getElementById("modal-erro-senha");
const mensagemErroSenha = document.getElementById("mensagem-erro-senha");

const email = document.getElementById("email");

const caracteresMinusculos = [
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
    'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
];

const caracteresMaiusculos = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
];

const verNumeros = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

const verEspeciais = [
    '!', '@', '#', '$', '%', '^', '&', '*', '(', ')',
    '-', '_', '=', '+', '[', ']', '{', '}', ';', ':',
    ',', '.', '<', '>', '/', '?'
];

function validarSenha() {
    const valor = senha.value;

    const temMinusculo = caracteresMinusculos.some(char => valor.includes(char));
    const temMaiusculo = caracteresMaiusculos.some(char => valor.includes(char));
    const temNumero = verNumeros.some(char => valor.includes(char));
    const temEspecial = verEspeciais.some(char => valor.includes(char));

    if (valor.length < 8) {
        quantidade.style.color = "red";
    } else {
        quantidade.style.color = "green";
    }

    if (!temMaiusculo) {
        maiuscula.style.color = "red";
    } else {
        maiuscula.style.color = "green";
    }

    if (!temNumero) {
        numeros.style.color = "red";
    } else {
        numeros.style.color = "green";
    }

    if (!temEspecial) {
        especiais.style.color = "red";
    } else {
        especiais.style.color = "green";
    }

    const senhaValida =
        valor.length >= 8 &&
        temMaiusculo &&
        temNumero &&
        temEspecial;

    return senhaValida;
}

function validarConfirmacao() {
    if (confirmarSenha.value === "") {
        alertaSenha.textContent = "";
        return false;
    }

    if (confirmarSenha.value !== senha.value) {
        alertaSenha.textContent = "As senhas não coincidem.";
        alertaSenha.style.color = "red";
        return false;
    } else {
        alertaSenha.textContent = "As senhas coincidem.";
        alertaSenha.style.color = "green";
        return true;
    }
}

function verificarFormulario() {
    validarSenha();
    validarConfirmacao();
}

const validarEmail = (email) => {
    const formatoValido = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    return formatoValido.test(email.trim());
};

cpf.addEventListener("input", function () {
    validarCPF(cpf.value);
});

// VALIDAÇÃO EM TEMPO REAL
senha.addEventListener("input", function () {
    verificarFormulario();
});

confirmarSenha.addEventListener("input", function () {
    verificarFormulario();
});

formulario.addEventListener("invalid", function (evento) {
    if (evento.target === senha) {
        evento.preventDefault();
        mensagemErroSenha.textContent =
            "A senha deve atender aos requisitos indicados (8+ caracteres, uma letra maiúscula, um número e um caractere especial).";
        modalErroSenha.showModal();
    }
}, true);

// BLOQUEIO DO ENVIO
formulario.addEventListener("submit", function (evento) {
    const senhaValida = validarSenha();
    const senhasIguais = validarConfirmacao();
    const emailValido = validarEmail(email.value);
    const cpfValido = validarCPF(cpf.value);

    if (!senhaValida || !senhasIguais || !emailValido || !cpfValido) {
        evento.preventDefault();
        mensagemErroSenha.textContent = !senhaValida
            ? "A senha não atende aos requisitos indicados."
            : !senhasIguais
                ? "As senhas não coincidem."
                : !emailValido
                    ? "O email informado é inválido."
                    : "O CPF informado é inválido.";
        modalErroSenha.showModal();
        if (!cpfValido) {
            cpf.focus();
        }
    }
});

// MOSTRAR E OCULTAR SENHAS
const botoesOlho = document.querySelectorAll(".eye");

botoesOlho.forEach(function (botao, indice) {
    botao.type = "button";
    botao.addEventListener("click", function (evento) {
        evento.preventDefault();

        const campo = indice === 0 ? senha : confirmarSenha;

        if (!campo) {
            return;
        }

        if (campo.type === "password") {
            campo.type = "text";
            botao.setAttribute("aria-label", "Ocultar senha");
        } else {
            campo.type = "password";
            botao.setAttribute("aria-label", "Mostrar senha");
        }
    });
});

verificarFormulario();


function validarCPF(valorCPF) {
    const cpfNumerico = valorCPF.replace(/\D/g, "");

    if (cpfNumerico.length !== 11 || /^(\d)\1{10}$/.test(cpfNumerico)) {
        alertaCPF.textContent = valorCPF.length === 0
            ? ""
            : "Informe um CPF válido com 11 dígitos.";
        alertaCPF.style.color = "red";
        return false;
    }

    const calcularDigitoVerificador = (digitos) => {
        let soma = 0;

        for (let i = 0; i < digitos.length; i++) {
            soma += Number(digitos[i]) * (digitos.length + 1 - i);
        }

        const resto = soma % 11;
        return resto < 2 ? 0 : 11 - resto;
    };

    const primeiroDigito = calcularDigitoVerificador(cpfNumerico.slice(0, 9));
    const segundoDigito = calcularDigitoVerificador(
        cpfNumerico.slice(0, 9) + primeiroDigito
    );
    const cpfValido =
        cpfNumerico === cpfNumerico.slice(0, 9) + primeiroDigito + segundoDigito;

    alertaCPF.textContent = cpfValido ? "CPF válido." : "CPF inválido, digite novamente.";
    alertaCPF.style.color = cpfValido ? "green" : "red";

    return cpfValido;
}