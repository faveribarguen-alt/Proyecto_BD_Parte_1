
document.addEventListener('DOMContentLoaded', function () {

    const puntos = window.PUNTOS || [];

    const mapa = L.map('mapa').setView([3.4372, -76.5225], 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapa);

    const marcadores = [];

    puntos.forEach(function (p) {

        if (p.latitud === null || p.longitud === null ||
            p.latitud === '' || p.longitud === '') {
            return;
        }

        const latitud = parseFloat(p.latitud);
        const longitud = parseFloat(p.longitud);

        if (!Number.isFinite(latitud) || !Number.isFinite(longitud)) {
            return;
        }

        const esPrincipal = String(p.es_principal) === '1';

        const icono = L.divIcon({
            className: 'punto-marcador-personalizado',
            html: `
                <div style="
                    width: 18px;
                    height: 18px;
                    background: ${esPrincipal ? '#7438c8' : '#a879ed'};
                    border: 3px solid white;
                    border-radius: 50%;
                    box-shadow: 0 2px 7px rgba(0,0,0,0.35);
                "></div>
            `,
            iconSize: [18, 18],
            iconAnchor: [9, 9],
            popupAnchor: [0, -10]
        });

        const contenido = document.createElement('div');

        const titulo = document.createElement('strong');
        titulo.textContent = p.nombre || 'Punto de atención';
        contenido.appendChild(titulo);

        contenido.appendChild(document.createElement('br'));
        contenido.appendChild(
            document.createTextNode(p.comuna || 'Comuna no registrada')
        );

        contenido.appendChild(document.createElement('br'));
        contenido.appendChild(
            document.createTextNode(p.direccion || 'Dirección no registrada')
        );

        if (esPrincipal) {
            contenido.appendChild(document.createElement('br'));

            const etiqueta = document.createElement('em');
            etiqueta.textContent = 'Oficina principal';
            contenido.appendChild(etiqueta);
        }

        const marcador = L.marker([latitud, longitud], {
            icon: icono
        }).addTo(mapa);

        marcador.bindPopup(contenido);

        marcadores.push(marcador);
    });

    // Ajustar el mapa para mostrar todos los puntos registrados.
    if (marcadores.length > 0) {

        const grupo = L.featureGroup(marcadores);

        mapa.fitBounds(grupo.getBounds(), {
            padding: [35, 35],
            maxZoom: 14
        });

    }

    // Corregir el tamaño del mapa cuando termina de cargar la vista.
    setTimeout(function () {
        mapa.invalidateSize();
    }, 200);

});

