document.addEventListener('DOMContentLoaded', function () {
    var formulario = document.getElementById('form-resumen');
    var aviso = document.getElementById('aviso-fechas');

    formulario.addEventListener('submit', function (evento) {
        var desde = document.getElementById('desde').value;
        var hasta = document.getElementById('hasta').value;

        if (desde > hasta) {                 // si "desde" es mayor que "hasta":
            evento.preventDefault();         //   - frena el envío con evento.preventDefault()
            aviso.hidden = false;            //   - muestra el aviso con aviso.hidden = false
        } else {                             // y si las fechas están bien:
            aviso.hidden = true;             //   - oculta el aviso con aviso.hidden = true
        }
    });
});