function formatarCPF(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 3) v = v.replace(/(\d{3})(\d)/, '$1.$2');
    if (v.length > 6) v = v.replace(/(\d{3})(\d)/, '$1.$2');
    if (v.length > 9) v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    input.value = v;
}

function formatarTelefone(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 2) v = v.replace(/(\d{2})(\d)/, '($1) $2');
    if (v.length > 7) v = v.replace(/(\d{5})(\d)/, '$1-$2');
    input.value = v;
}

function formatarCNH(campo) {
    campo.value = campo.value.replace(/\D/g, '').slice(0, 11);
}

const checkboxEntregador = document.querySelector('input[name="cadastroEntregador"]');
const camposEntregador = document.getElementById('campos-entregador');

checkboxEntregador.addEventListener('change', function() {
    if (this.checked) {
        camposEntregador.classList.add('mostrar');
    } else {
        camposEntregador.classList.remove('mostrar');
    }
});