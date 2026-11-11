
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bitácora de solicitudes | ENCUESTAS360</title>

    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
    <link rel="stylesheet" href="assets/css/stylesBitacora.css">
</head>

<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main">

    <header class="bitacora-header">
        <div>
            <span class="bitacora-label">GESTIÓN OPERATIVA</span>
            <h1>Bitácora de solicitudes</h1>
            <p>Consulta y supervisa las solicitudes registradas en el sistema.</p>
        </div>

        <div class="bitacora-fecha">
            <span>FECHA SELECCIONADA</span>
            <strong>
                <?= htmlspecialchars(date('d/m/Y', strtotime($fecha))) ?>
            </strong>
        </div>
    </header>

    <section class="bitacora-resumen">
        <div class="bitacora-resumen-icono">▤</div>
        <div>
            <span>Solicitudes registradas en el día seleccionado</span>
            <strong><?= count($solicitudes) ?></strong>
        </div>
    </section>

    <form action="index.php" method="GET" class="bitacora-filtro">

        <input type="hidden" name="action" value="bitacora">

        <div class="bitacora-campo">
            <label for="fecha">Consultar por fecha</label>
            <input
                type="date"
                id="fecha"
                name="fecha"
                value="<?= htmlspecialchars($fecha) ?>"
                required
            >
        </div>

        <button type="submit" class="bitacora-boton">
            <span>⌕</span>
            Consultar solicitudes
        </button>

    </form>

    <section class="table-section">

        <div class="section-heading">
            <div>
                <h2>Registro de solicitudes</h2>
                <p>Detalle de las solicitudes recibidas durante la fecha seleccionada.</p>
            </div>
        </div>

        <div class="bitacora-tabla-contenedor">

            <table class="bitacora-tabla">

                <thead>
                    <tr>
                        <th>Radicado</th>
                        <th>Fecha de registro</th>
                        <th>Punto de atención</th>
                        <th>Ciudadano</th>
                        <th>Dirección</th>
                        <th>Barrio / Comuna</th>
                        <th>Estado</th>
                        <th>Ubicación GPS</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($solicitudes)): ?>

                        <?php foreach ($solicitudes as $solicitud): ?>

                            <tr>

                                <td>
                                    <span class="bitacora-radicado">
                                        <?= htmlspecialchars($solicitud['radicado']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($solicitud['fecha_radicacion']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($solicitud['punto']) ?>
                                </td>

                                <td>
                                    <span class="bitacora-ciudadano">
                                        <?= htmlspecialchars(
                                            $solicitud['nombres'] . ' ' . $solicitud['apellidos']
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($solicitud['direccion']) ?>
                                </td>

                                <td>
                                    <span class="bitacora-ubicacion">
                                        <?= htmlspecialchars($solicitud['barrio']) ?>
                                        (<?= htmlspecialchars($solicitud['comuna']) ?>)
                                    </span>
                                </td>

                                <td>
                                    <span class="bitacora-estado">
                                        <?= htmlspecialchars(strtoupper($solicitud['estado'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    $latitud = $solicitud['latitud'] ?? null;
                                    $longitud = $solicitud['longitud'] ?? null;
                                    ?>

                                    <?php if ($latitud !== null && $longitud !== null): ?>

                                        <a
                                            class="bitacora-mapa"
                                            href="https://www.google.com/maps?q=<?= urlencode($latitud . ',' . $longitud) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            ⌖ Ver ubicación
                                        </a>

                                    <?php else: ?>

                                        <span class="bitacora-sin-gps">
                                            Sin coordenadas
                                        </span>

                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="bitacora-vacia">
                                <span class="bitacora-vacia-icono">⌕</span>
                                No hay solicitudes registradas para esta fecha.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

    <footer class="bitacora-footer">
        ENCUESTAS360 © <?= date('Y') ?> · Sistema de gestión de encuestas
    </footer>

</main>

</body>
</html>
