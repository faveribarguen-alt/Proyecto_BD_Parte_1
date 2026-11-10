// Dibuja los puntos de atención fijos sobre el mapa de Cali
document.addEventListener('DOMContentLoaded', function () {
    var puntos = window.PUNTOS || [];

    var mapa = L.map('mapa').setView([3.4372, -76.5225], 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapa);

    puntos.forEach(function (p) {
        if (p.latitud === null || p.longitud === null) {
            return;
        }

        // El contenido del globo se arma con textContent para que sea seguro
        var contenido = document.createElement('div');
        var titulo = document.createElement('strong');
        titulo.textContent = p.nombre;
        contenido.appendChild(titulo);
        contenido.appendChild(document.createElement('br'));
        contenido.appendChild(document.createTextNode(p.comuna || ''));
        if (String(p.es_principal) === '1') {
            contenido.appendChild(document.createElement('br'));
            var etiqueta = document.createElement('em');
            etiqueta.textContent = 'Oficina principal';
            contenido.appendChild(etiqueta);
        }

        L.marker([parseFloat(p.latitud), parseFloat(p.longitud)])
            .addTo(mapa)
            .bindPopup(contenido);
    });
});
