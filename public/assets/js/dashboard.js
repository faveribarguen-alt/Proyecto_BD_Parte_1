document.addEventListener('DOMContentLoaded', function () {
    var datos = window.DASHBOARD;

    // Gráfico 1: solicitudes por estado (dona)
    new Chart(document.getElementById('grafico-estado'), {
        type: 'doughnut',
        data: {
            labels: datos.estados.map(function (fila) { return fila.estado; }),
            datasets: [{
                data: datos.estados.map(function (fila) { return Number(fila.total); })
            }]
        }
    });

    // Gráfico 2, solicitudes por comuna (barras)
    new Chart(document.getElementById('grafico-comuna'), {
        type: 'bar',
        data: {
            labels: datos.comunas.map(function (fila) { return fila.comuna; }),
            datasets: [{
                label: 'Solicitudes',
                data: datos.comunas.map(function (fila) { return Number(fila.total); })
            }]
        }
    });

    // Gráfico 3: solicitudes por día (líneas)
    new Chart(document.getElementById('grafico-dias'), {
        type: 'line',
        data: {
            labels: datos.dias.map(function (fila) { return fila.dia; }),
            datasets: [{
                label: 'Solicitudes',
                data: datos.dias.map(function (fila) { return Number(fila.total); }),
                tension: 0.3
            }]
        }
    });
});