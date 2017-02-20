$(function () {

    $('.date-partida').datetimepicker({
        format: 'L',
        minDate: new Date()
    });

    $('.date-retorno').datetimepicker({
        format: 'L',
        minDate: new Date()
    });

    $('.date-partida').datetimepicker().on('dp.change', function (e) {
        $('.date-retorno').data("DateTimePicker").minDate($('.date-partida').data("DateTimePicker").date());
    });

    $('.form-buscar-vuelo').on('submit', function (e) {
        var origen = $('.origen-vuelo').val();
        var destino = $('.destino-vuelo').val();
        if (!origen) {
            mostrarMensaje('Debe ingresar el origen del vuelo');
            e.preventDefault();
            return;
        }
        if (!destino) {
            mostrarMensaje('Debe ingresar el destino del vuelo');
            e.preventDefault();
            return;
        }
    });

});