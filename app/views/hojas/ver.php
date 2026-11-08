<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Ruta #<?= (int) $carga['id_carga'] ?></title>
    <link rel="stylesheet" href="assets/css/stylesHojas.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu.php'; ?>

<div class="contenedor">
    <?php mostrarMensaje(); ?>

    <h2>Hoja de ruta #<?= (int) $carga['id_carga'] ?></h2>

    <div class="tarjeta datos-control">
        <p><strong>Encuestador:</strong> <?= htmlspecialchars($carga['encuestador']) ?></p>
        <p><strong>Punto de atención:</strong> <?= htmlspecialchars($carga['punto'] ?? 'Sin punto') ?></p>
        <p><strong>Fecha de visita:</strong> <?= htmlspecialchars($carga['fecha_visita']) ?></p>
        <p><strong>Generada por:</strong> <?= htmlspecialchars($carga['enrutador']) ?> el <?= htmlspecialchars($carga['creada_en']) ?></p>
        <p><strong>Estado:</strong> <span class="badge"><?= htmlspecialchars(strtoupper($carga['estado'])) ?></span></p>
        <?php if (!empty($carga['entregada_en'])): ?>
            <p><strong>Entregada:</strong> <?= htmlspecialchars($carga['entregada_en']) ?>
               por <?= htmlspecialchars($carga['entregada_por'] ?? '') ?></p>
        <?php endif; ?>
        <p><strong>Total de visitas:</strong> <?= count($solicitudes) ?></p>
        <button type="button" class="btn-principal no-imprimir" onclick="window.print()">Imprimir hoja</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Radicado</th>
                <th>Solicitante</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Barrio (Comuna)</th>
                <th>Vence</th>
                <th class="no-imprimir">Mapa</th>
                <th>Llamada confirmada</th>
                <th>Visitada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($s['radicado']) ?></td>
                    <td><?= htmlspecialchars($s['nombres'] . ' ' . $s['apellidos']) ?></td>
                    <td><?= htmlspecialchars($s['telefono']) ?></td>
                    <td><?= htmlspecialchars($s['direccion']) ?></td>
                    <td><?= htmlspecialchars($s['barrio'] . ' (' . $s['comuna'] . ')') ?></td>
                    <td><?= htmlspecialchars($s['fecha_limite']) ?></td>
                    <td class="no-imprimir">
                        <?php if ($s['latitud'] !== null): ?>
                            <a href="https://maps.google.com/?q=<?= htmlspecialchars($s['latitud']) ?>,<?= htmlspecialchars($s['longitud']) ?>" target="_blank" rel="noopener">Ver</a>
                        <?php endif; ?>
                    </td>
                    <td class="casilla">☐</td>
                    <td class="casilla">☐</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="firmas">
        <div>Entregada a (nombre y firma): ______________________________</div>
        <div>Entregada por (ventanilla): ______________________________</div>
    </div>
</div>
</body>
</html>
