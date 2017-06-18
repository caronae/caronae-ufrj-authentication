function displayTermsAlert() {
    swal({
            title: "Você já leu nossos termos e condições de uso?",
            text: "Para obter sua chave de acesso, você deve ler e concordar com nossos termos e condições de uso.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ok, li e concordo!",
            cancelButtonText: "Ler termos de uso",
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger",
            buttonsStyling: false,
            closeOnConfirm: true,
            closeOnCancel: false,
            allowOutsideClick: false
        },
        function (isConfirm) {
            if (isConfirm === false) {
                window.open("https://docs.google.com/viewerng/viewer?url=https://caronae.ufrj.br/termos_de_uso.pdf");
            }
        }
    )
}

function getCredentials() {
    var user = document.querySelector('#user').value;
    var token = document.querySelector('#app_token').value;
    return {
        user: user,
        token: token
    };
}

function getCredentialsJSON() {
    return JSON.stringify(getCredentials());
}

function openTermsOfUse() {
    window.open("https://docs.google.com/viewerng/viewer?url=https://caronae.ufrj.br/termos_de_uso.pdf");
    return false;
}

var clipboard = new Clipboard('.token');

clipboard.on('success', function (e) {
    document.querySelector('.copy-text').innerHTML =
        '<span class="text-success">' +
        'Copiado! Agora é só colar no app do Caronaê.' +
        '</span>';

    e.clearSelection();
});

clipboard.on('error', function (e) {
    document.querySelector('.copy-text').innerHTML =
        '<span class="text-danger">' +
        'Erro... É preciso copiar manualmente.' +
        '</span>';
});
