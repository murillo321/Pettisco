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