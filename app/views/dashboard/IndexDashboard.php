<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/stylesDashboard.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu_admin.php'; ?>

<main class="contenedor">
    <h2>Dashboard</h2>

    <section class="tarjetas">
        <article class="tarjeta-dato">
            <span class="numero"><?= (int) $totales['total'] ?></span>
            <span class="etiqueta">Solicitudes en total</span>
        </article>

        <article class="tarjeta-dato">
            <span class="numero"><?= (int) $totales['pendientes'] ?></span>
            <span class="etiqueta">Pendientes de asignar</span>
        </article>

        <article class="tarjeta-dato alerta">
            <span class="numero"><?= (int) $totales['vencidas'] ?></span>
            <span class="etiqueta">Vencidas</span>
        </article>

        <article class="tarjeta-dato">
            <span class="numero"><?= (int) $totales['en_campo'] ?></span>
            <span class="etiqueta">Hojas de ruta en campo</span>
        </article>
    </section>

    <section class="graficos">
        <article class="grafico">
            <h3>Solicitudes por estado</h3>
            <canvas id="grafico-estado"></canvas>
        </article>

        <article class="grafico">
            <h3>Solicitudes por comuna</h3>
            <canvas id="grafico-comuna"></canvas>
        </article>

        <article class="grafico ancho">
            <h3>Solicitudes de los últimos 7 días</h3>
            <canvas id="grafico-dias"></canvas>
        </article>
    </section>
</main>

<script>
    window.DASHBOARD = <?= json_encode([
        'estados' => $porEstado,
        'comunas' => $porComuna,
        'dias'    => $ultimosSieteDias   
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>