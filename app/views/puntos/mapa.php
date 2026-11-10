<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntos de Atención</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="assets/css/stylesPuntos.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu.php'; ?>

    <h2>Puntos de Atención de Cali</h2>

    <div id="mapa"></div>

    <table>
        <thead>
            <tr>
                <th>Punto</th>
                <th>Comuna</th>
                <th>Dirección</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($puntos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['comuna'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['direccion']) ?></td>
                    <td><?= $p['es_principal'] ? 'Oficina principal' : 'Punto de atención' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        window.PUNTOS = <?= json_encode($puntos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/puntos.js"></script>
</body>
</html>
