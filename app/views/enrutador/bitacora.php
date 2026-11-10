<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Solicitudes</title>
    <link rel="stylesheet" href="assets/css/stylesBitacora.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu.php'; ?>

    <h2>Bitácora de solicitudes del día</h2>

    <form action="index.php" method="GET" class="filtro">
        <input type="hidden" name="action" value="bitacora">
        <label for="fecha">Día:</label>
        <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
        <button type="submit">Ver</button>
        <span class="total">Solicitudes del día: <strong><?= count($solicitudes) ?></strong></span>
    </form>

    <table>
        <thead>
            <tr>
                <th>Radicado</th>
                <th>Fecha de Registro</th>
                <th>Punto de atención</th>
                <th>Ciudadano</th>
                <th>Dirección</th>
                <th>Ubicación (Barrio/Comuna)</th>
                <th>Estado</th>
                <th>GPS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($solicitudes)): ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($solicitud['radicado']) ?></strong></td>
                        <td><?= htmlspecialchars($solicitud['fecha_radicacion']) ?></td>
                        <td><?= htmlspecialchars($solicitud['punto']) ?></td>
                        <td><?= htmlspecialchars($solicitud['nombres'] . ' ' . $solicitud['apellidos']) ?></td>
                        <td><?= htmlspecialchars($solicitud['direccion']) ?></td>
                        <td><?= htmlspecialchars($solicitud['barrio'] . ' (' . $solicitud['comuna'] . ')') ?></td>
                        <td><span class="badge"><?= htmlspecialchars(strtoupper($solicitud['estado'])) ?></span></td>
                        <td>
                            <?php if ($solicitud['latitud'] !== null): ?>
                                <a href="https://maps.google.com/?q=<?= htmlspecialchars($solicitud['latitud']) ?>,<?= htmlspecialchars($solicitud['longitud']) ?>" target="_blank" rel="noopener">Ver en Mapa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No hay solicitudes registradas en este día.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
