<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Georreferenciación | ENCUESTAS360</title>

    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
    <link rel="stylesheet" href="assets/css/stylesGeorreferenciacion.css">

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>

<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main">

    <header class="geo-header">
        <div>
            <span class="geo-label">GESTIÓN TERRITORIAL</span>
            <h1>Georreferenciación</h1>
            <p>Visualiza las solicitudes y administra su asignación geográfica.</p>
        </div>
    </header>

    <?php
    $total = count($solicitudes);
    $conUbicacion = 0;
    $sinUbicacion = 0;
    $asignadasTotal = 0;

    foreach ($solicitudes as $solicitud) {
        if ($solicitud['latitud'] !== null &&
            $solicitud['longitud'] !== null) {
            $conUbicacion++;
        } else {
            $sinUbicacion++;
        }

        if (!empty($solicitud['id_punto_asignado'])) {
            $asignadasTotal++;
        }
    }
    ?>

    <section class="geo-resumen">

        <article class="geo-card">
            <span>Total de solicitudes</span>
            <strong><?= $total ?></strong>
        </article>

        <article class="geo-card">
            <span>Con ubicación GPS</span>
            <strong><?= $conUbicacion ?></strong>
        </article>

        <article class="geo-card">
            <span>Sin ubicación GPS</span>
            <strong><?= $sinUbicacion ?></strong>
        </article>

        <article class="geo-card">
            <span>Con punto asignado</span>
            <strong><?= $asignadasTotal ?></strong>
        </article>

    </section>

    <section class="geo-panel">

        <div class="geo-panel-heading">
            <div>
                <h2>Mapa de solicitudes</h2>
                <p>Consulta la ubicación de las solicitudes y los puntos de atención.</p>
            </div>
        </div>

        <div class="geo-leyenda">
            <span><i class="geo-punto solicitud"></i> Solicitud</span>
            <span><i class="geo-punto atencion"></i> Punto de atención</span>
        </div>

        <div id="mapaGeorreferenciacion"></div>

    </section>

    <section class="geo-panel">

        <div class="geo-panel-heading">
            <div>
                <h2>Registro de solicitudes</h2>
                <p>Consulta las ubicaciones y los puntos asignados.</p>
            </div>
        </div>

        <div class="geo-tabla-contenedor">

            <table class="geo-tabla">

                <thead>
                    <tr>
                        <th>Radicado</th>
                        <th>Ciudadano</th>
                        <th>Dirección</th>
                        <th>Barrio / Comuna</th>
                        <th>Ubicación</th>
                        <th>Punto original</th>
                        <th>Punto asignado</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($solicitudes)): ?>

                    <?php foreach ($solicitudes as $solicitud): ?>

                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($solicitud['radicado']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $solicitud['nombres'] . ' ' .
                                    $solicitud['apellidos']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($solicitud['direccion'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($solicitud['barrio'] ?? '') ?>
                                /
                                <?= htmlspecialchars($solicitud['comuna'] ?? '') ?>
                            </td>

                            <td>
                                <?php if (
                                    $solicitud['latitud'] !== null &&
                                    $solicitud['longitud'] !== null
                                ): ?>
                                    <span class="geo-estado-con">
                                        Con GPS
                                    </span>
                                <?php else: ?>
                                    <span class="geo-estado-sin">
                                        Sin GPS
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $solicitud['punto_original'] ?? 'No disponible'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $solicitud['punto_asignado'] ?? 'Pendiente'
                                ) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="geo-vacio">
                            No hay solicitudes registradas.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

    <footer class="geo-footer">
        ENCUESTAS360 © <?= date('Y') ?> · Sistema de gestión de encuestas
    </footer>

</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const solicitudes = <?= json_encode(
        $solicitudes,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    ) ?>;

    const puntos = <?= json_encode(
        $puntos,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    ) ?>;

    const mapa = L.map('mapaGeorreferenciacion');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapa);

    const coordenadas = [];

    // Mostrar puntos de atención
    puntos.forEach(function (punto) {

        if (punto.latitud === null || punto.longitud === null) {
            return;
        }

        const lat = Number(punto.latitud);
        const lon = Number(punto.longitud);

        if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
            return;
        }

        L.marker([lat, lon])
            .addTo(mapa)
            .bindPopup(
                '<strong>' + escapar(punto.nombre) + '</strong><br>' +
                'Punto de atención<br>' +
                escapar(punto.direccion)
            );

        coordenadas.push([lat, lon]);
    });

    // Mostrar solicitudes
    solicitudes.forEach(function (solicitud) {

        if (solicitud.latitud === null || solicitud.longitud === null) {
            return;
        }

        const lat = Number(solicitud.latitud);
        const lon = Number(solicitud.longitud);

        if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
            return;
        }

        L.circleMarker([lat, lon], {
            radius: 7,
            color: '#8656b5',
            fillColor: '#b99add',
            fillOpacity: 0.9
        })
        .addTo(mapa)
        .bindPopup(
            '<strong>' + escapar(solicitud.radicado) + '</strong><br>' +
            escapar(solicitud.nombres + ' ' + solicitud.apellidos) +
            '<br>' + escapar(solicitud.direccion)
        );

        coordenadas.push([lat, lon]);
    });

    // Ajustar el mapa a los elementos encontrados
    if (coordenadas.length > 0) {
        mapa.fitBounds(coordenadas, { padding: [35, 35] });
    } else {
        // Vista inicial si todavía no hay coordenadas
        mapa.setView([3.4516, -76.5320], 11);
    }

    function escapar(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, function (caracter) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[caracter];
        });
    }

});
</script>

</body>
</html>
