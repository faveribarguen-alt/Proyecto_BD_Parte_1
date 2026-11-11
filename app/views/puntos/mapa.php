<?php
$totalPuntos = count($puntos ?? []);
$totalPrincipales = 0;

foreach (($puntos ?? []) as $p) {
    if ((string)($p['es_principal'] ?? '0') === '1') {
        $totalPrincipales++;
    }
}

$totalSecundarios = $totalPuntos - $totalPrincipales;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Puntos de atención | ENCUESTAS360</title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <link rel="stylesheet"
          href="/Proyecto_BD_Parte_1/public/assets/css/stylesVentanilla.css">

    <link rel="stylesheet"
          href="/Proyecto_BD_Parte_1/public/assets/css/stylesPuntos.css">
</head>

<body class="pagina-puntos">

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main puntos-main">

    <header class="dashboard-header">
        <div>
            <span class="dashboard-label">UBICACIONES Y COBERTURA</span>
            <h1>Puntos de atención</h1>
            <p>Consulta la ubicación y la información de las oficinas de atención en Cali.</p>
        </div>

        <div class="header-date">
            <span>FECHA DE HOY</span>
            <strong><?= date('d/m/Y') ?></strong>
        </div>
    </header>

    <section class="puntos-stats">

        <article class="punto-stat-card">
            <div class="punto-stat-icon">⌖</div>
            <div>
                <span>Total de puntos</span>
                <strong><?= $totalPuntos ?></strong>
                <small>Registrados en el sistema</small>
            </div>
        </article>

        <article class="punto-stat-card">
            <div class="punto-stat-icon principal-icon">★</div>
            <div>
                <span>Oficinas principales</span>
                <strong><?= $totalPrincipales ?></strong>
                <small>Sedes principales</small>
            </div>
        </article>

        <article class="punto-stat-card">
            <div class="punto-stat-icon secundario-icon">⌖</div>
            <div>
                <span>Puntos de atención</span>
                <strong><?= $totalSecundarios ?></strong>
                <small>Otros puntos de servicio</small>
            </div>
        </article>

    </section>

    <section class="puntos-map-section">

        <div class="puntos-section-heading">
            <div>
                <h2>Mapa de ubicaciones</h2>
                <p>Explora los puntos de atención registrados en la ciudad.</p>
            </div>

            <span class="map-status">
                <span></span>
                Mapa interactivo
            </span>
        </div>

        <div id="mapa"></div>

        <div class="map-legend">
            <span class="legend-item">
                <span class="legend-dot principal-dot"></span>
                Oficina principal
            </span>

            <span class="legend-item">
                <span class="legend-dot secondary-dot"></span>
                Punto de atención
            </span>
        </div>

    </section>

    <section class="puntos-table-section">

        <div class="puntos-section-heading">
            <div>
                <h2>Directorio de puntos</h2>
                <p>Información de las ubicaciones disponibles.</p>
            </div>

            <span class="puntos-counter">
                <?= $totalPuntos ?> punto(s)
            </span>
        </div>

        <div class="puntos-table-wrapper">
            <table class="puntos-table">
                <thead>
                    <tr>
                        <th>Punto de atención</th>
                        <th>Comuna</th>
                        <th>Dirección</th>
                        <th>Tipo</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($puntos)): ?>

                        <?php foreach ($puntos as $p): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($p['nombre']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['comuna'] ?? 'No registrada') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['direccion'] ?? 'No registrada') ?>
                                </td>

                                <td>
                                    <?php if ((string)($p['es_principal'] ?? '0') === '1'): ?>
                                        <span class="punto-type principal-type">
                                            Oficina principal
                                        </span>
                                    <?php else: ?>
                                        <span class="punto-type secondary-type">
                                            Punto de atención
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="puntos-empty">
                                No hay puntos de atención registrados.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </section>

    <footer class="dashboard-footer">
        ENCUESTAS360 © <?= date('Y') ?> · Sistema de gestión de encuestas
    </footer>

</main>

<script>
    window.PUNTOS = <?= json_encode(
        $puntos,
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    ) ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="/Proyecto_BD_Parte_1/public/assets/js/puntos.js"></script>

</body>
</html>
