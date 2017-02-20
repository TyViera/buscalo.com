function getAbsolutePath() {
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

function mostrarMensaje(mensaje) {
    $('#mensaje-modal-data').text('');
    $('#modal-content-body').load(getAbsolutePath() + 'shared/mensaje-modal.html', function () {
        $('#mensaje-modal-data').text(mensaje);
        $('#modal-alerta-mensaje').modal();
    })
}

$(function () {
    $('#buscador-vuelo-panel').load(getAbsolutePath() + 'shared/buscador-vuelos.html');
});