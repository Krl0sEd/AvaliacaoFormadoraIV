document.querySelectorAll('.form-status').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch('../Assets/php/atualizar-status.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.text())
        .then(txt => {
            alert("Status atualizado!");
        })
        .catch(err => alert("Erro ao atualizar."));
    });
});