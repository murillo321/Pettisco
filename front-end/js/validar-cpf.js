const cpf = document.getElementById("cpf") || document.getElementById("emailoucpf");
let alertaCPF = document.getElementById("alerta-cpf");

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

if (cpf) {
    const entradaEmailOuCpf = cpf.dataset.emailOuCpf === "true";

    if (!alertaCPF) {
        alertaCPF = document.createElement("p");
        alertaCPF.id = "alerta-cpf";
        cpf.insertAdjacentElement("afterend", alertaCPF);
    }

    cpf.addEventListener("input", function () {
        if (entradaEmailOuCpf && cpf.value.includes("@")) {
            alertaCPF.textContent = "";
            return;
        }

        validarCPF(cpf.value);
    });

    cpf.form?.addEventListener("submit", function (evento) {
        if (!(entradaEmailOuCpf && cpf.value.includes("@")) && !validarCPF(cpf.value)) {
            evento.preventDefault();
            cpf.focus();
        }
    });
}
