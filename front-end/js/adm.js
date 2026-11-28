//Arrumar cpf
const cpf = document.getElementById('cpf');

cpf.addEventListener('input', function(info) {
    let valor = info.target.value;
    valor = valor.replace(/\D/g, '');
    valor = valor.slice(0, 11);

    valor = valor.replace(/^(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    valor = valor.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');

    info.target.value = valor;
});
const modal1 = document.getElementById('modal1');
const modal2 = document.getElementById('modal2');

//Abrir-fechar modais
function abrir_modal(){
    modal1.showModal();
}
function fechar_modal(){
    modal1.close();
}

function abrir_modal2(){
    modal2.showModal();
}
function fechar_modal2(){
    modal2.close();
    window.location.href="adm.php";
}

if(abrirmodal2){
    abrir_modal2();
}

//Filtragem
const ordem = document.getElementById('ordem');
const listaAdm = document.getElementById('lista-adm');

ordem.addEventListener('change', async () => {
    const resposta = await fetch('filtrar_adm.php?ordem='+ordem.value);
    const adms = await resposta.json();

    listaAdm.innerHTML = '';

    adms.forEach(adm => {

        const perfil = document.createElement('section');
        perfil.classList.add('perfil');

        const foto = document.createElement('img');
        foto.src = '../../../front-end/img/' + adm.foto_url;
        foto.classList.add('foto-perfil');

        const nome = document.createElement('p');
        nome.classList.add('nome');
        nome.textContent = adm.username;

        const cpf = document.createElement('p');
        cpf.classList.add('cpf');
        cpf.textContent = adm.cpf;

        const email = document.createElement('p');
        email.classList.add('email');
        email.textContent = adm.email;

        const excluir = document.createElement('a');
        excluir.href = `?id=${adm.id_user}`;
        excluir.classList.add('excluir-adm');
        excluir.textContent = 'Excluir adm';

        excluir.addEventListener('click', (e) => {
            if (!confirm(
                `Deseja mesmo remover de administrador o usuário: ${adm.username}?`
            )) {
                e.preventDefault();
            }
        });

        perfil.append(foto, nome, cpf, email, excluir);

        listaAdm.appendChild(perfil);
    });
});