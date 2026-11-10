<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Solicitudes</title>
    <!-- Ruta actualizada a tu estructura de assets -->
    <link rel="stylesheet" href="assets/css/stylesBitacora.css">
</head>
<body>
    <h2>Panel del Enrutador - Bitácora de Solicitudes</h2>

<?php require __DIR__ . '/../layouts/menu.php'; ?>
    
    <table>
        <thead>
            <tr>
                <th>Radicado</th>
                <th>Fecha de Registro</th>
                <th>Ciudadano</th>
                <th>Dirección</th>
                <th>Ubicación (Barrio/Comuna)</th>
                <th>Estado</th>
                <th>GPS (Lat, Lng)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($solicitudes)): ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($solicitud['radicado']) ?></strong></td>
                        <td><?= htmlspecialchars($solicitud['fecha_radicacion']) ?></td>
                        <td><?= htmlspecialchars($solicitud['nombres'] . ' ' . $solicitud['apellidos']) ?></td>
                        <td><?= htmlspecialchars($solicitud['direccion']) ?></td>
                        <td><?= htmlspecialchars($solicitud['barrio'] . ' (' . $solicitud['comuna'] . ')') ?></td>
                        <td><span class="badge"><?= htmlspecialchars(strtoupper($solicitud['estado'])) ?></span></td>
                        <td>
                            <a href="https://maps.google.com/?q=<?= htmlspecialchars($solicitud['latitud']) ?>,<?= htmlspecialchars($solicitud['longitud']) ?>" target="_blank">
                                Ver en Mapa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No hay solicitudes registradas en el sistema.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>