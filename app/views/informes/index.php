<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes en PDF</title>
    <link rel="stylesheet" href="assets/css/stylesInformes.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu_admin.php'; ?>

<main class="contenedor">
    <h2>Informes en PDF</h2>
    <?php mostrarMensaje(); ?>

    <section class="tarjeta">
        <h3>Resumen de solicitudes</h3>
        <p>Cuántas solicitudes hay por estado y por punto de atención en el período que elijas.</p>

        <div id="aviso-fechas" class="alerta-error" hidden>
            La fecha "Desde" no puede ser posterior a "Hasta".
        </div>

        <form id= "form-resumen" action="index.php" method="GET" target="_blank" class="fila-form">
            <input type="hidden" name="action" value="informe_resumen">

            <div class="campo">
                <label for="desde">Desde</label>
                <input type="date" id="desde" name="desde" value="<?= htmlspecialchars($inicioMes) ?>" required>
            </div>

            <div class="campo">
                <label for="hasta">Hasta</label>
                <input type="date" id="hasta" name="hasta" value="<?= htmlspecialchars($hoy) ?>" required>
            </div>

            <button type="submit" class="btn-principal">Generar PDF</button>
        </form>
    </section>

    <section class="tarjeta">
        <h3>Solicitudes por vencer</h3>
        <p>Las solicitudes que vencen en los próximos días o ya han vencido.</p>

        <form id= "form-resumen" action="index.php" method="GET" target="_blank" class="fila-form">
            <input type="hidden" name="action" value="informe_vencer">

        <input type="number" id="dias" name="dias" min="1" max="30" value="5" required>

            <button type="submit" class="btn-principal">Generar PDF</button>
        </form>
    </section>
</main>

<script src="assets/js/informes.js"></script>
</body>
</html>