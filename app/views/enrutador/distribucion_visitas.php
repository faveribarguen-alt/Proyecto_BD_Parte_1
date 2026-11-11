<?php
// Escapar datos antes de mostrarlos en HTML.
function escaparDistribucion($valor) {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

// Valores de respaldo.
$asignaciones = $asignaciones ?? [];
$excepciones = $excepciones ?? [];
$encuestadores = $encuestadores ?? [];
$puntos = $puntos ?? [];

$resumen = $resumen ?? [
    'pendientes' => 0,
    'asignadas' => 0,
    'excepciones' => 0
];

$errorDistribucion = $errorDistribucion ?? null;
$fecha = $fecha ?? date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Distribución de visitas | ENCUESTAS360</title>

    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
    <link rel="stylesheet" href="assets/css/stylesDistribucion.css">
</head>

<body>

<?php require_once __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main distribucion-contenido">

    <section class="encabezado-distribucion">
        <div>
            <h1>Distribución de visitas</h1>
            <p>
                Asigna automáticamente las solicitudes pendientes
                según la ubicación geográfica y la disponibilidad
                de los encuestadores.
            </p>
        </div>
    </section>

    <?php mostrarMensaje(); ?>

    <!-- INFORMACIÓN DE LA JORNADA -->
    <section class="informacion-jornada">
        <div>
            <strong>Horario de trabajo:</strong>
            7:00 a. m. - 12:00 p. m.
        </div>

        <div>
            <strong>Recogida de encuestadores:</strong>
            12:30 p. m.
        </div>

        <div>
            <strong>Capacidad urbana:</strong>
            10 visitas por encuestador.
        </div>

        <div>
            <strong>Capacidad rural:</strong>
            5 visitas por encuestador.
        </div>
    </section>

    <!-- FILTRO DE FECHA -->
    <section class="panel-distribucion">

        <h2>Seleccionar fecha de visita</h2>

        <form method="GET" action="index.php" class="formulario-fecha">

            <input
                type="hidden"
                name="action"
                value="distribucion_visitas"
            >

            <div class="campo-fecha">
                <label for="fecha">Fecha de visita</label>

                <input
                    type="date"
                    id="fecha"
                    name="fecha"
                    value="<?= escaparDistribucion($fecha) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn-consultar">
                Consultar distribución
            </button>

        </form>

    </section>

    <?php if (!empty($errorDistribucion)): ?>

        <div class="mensaje-error">
            <?= escaparDistribucion($errorDistribucion) ?>
        </div>

    <?php else: ?>

        <!-- RESUMEN -->
        <section class="resumen-distribucion">

            <article class="tarjeta-resumen">
                <span>Solicitudes pendientes</span>
                <strong>
                    <?= (int) $resumen['pendientes'] ?>
                </strong>
            </article>

            <article class="tarjeta-resumen">
                <span>Solicitudes asignables</span>
                <strong>
                    <?= (int) $resumen['asignadas'] ?>
                </strong>
            </article>

            <article class="tarjeta-resumen">
                <span>Solicitudes con excepciones</span>
                <strong>
                    <?= (int) $resumen['excepciones'] ?>
                </strong>
            </article>

        </section>

        <!-- DISTRIBUCIÓN AUTOMÁTICA Y EDICIÓN MANUAL -->
        <section class="panel-distribucion">

            <div class="titulo-panel">
                <div>
                    <h2>Solicitudes asignadas</h2>

                    <p>
                        Revisa la propuesta automática y modifica
                        el encuestador o el punto de atención si
                        es necesario.
                    </p>
                </div>
            </div>

            <?php if (!empty($asignaciones)): ?>

                <form method="POST" action="index.php">

                    <input
                        type="hidden"
                        name="action"
                        value="confirmar_distribucion"
                    >

                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= escaparDistribucion(tokenCsrf()) ?>"
                    >

                    <input
                        type="hidden"
                        name="fecha"
                        value="<?= escaparDistribucion($fecha) ?>"
                    >

                    <div class="tabla-contenedor">

                        <table class="tabla-distribucion">

                            <thead>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Solicitante</th>
                                    <th>Dirección</th>
                                    <th>Barrio</th>
                                    <th>Comuna</th>
                                    <th>Zona</th>
                                    <th>Distancia</th>
                                    <th>Encuestador</th>
                                    <th>Punto asignado</th>
                                    <th>Capacidad</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($asignaciones as $asignacion): ?>

                                    <?php
                                    $idSolicitud = (int) $asignacion['id_solicitud'];
                                    $idEncuestadorActual = (int) $asignacion['id_encuestador'];
                                    $idPuntoActual = (int) $asignacion['id_punto'];
                                    ?>

                                    <tr>

                                        <td>
                                            <?= escaparDistribucion($asignacion['radicado']) ?>
                                        </td>

                                        <td>
                                            <?= escaparDistribucion(
                                                $asignacion['nombres'] . ' ' .
                                                $asignacion['apellidos']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= escaparDistribucion($asignacion['direccion']) ?>
                                        </td>

                                        <td>
                                            <?= escaparDistribucion($asignacion['barrio']) ?>
                                        </td>

                                        <td>
                                            <?= escaparDistribucion($asignacion['comuna']) ?>
                                        </td>

                                        <td>
                                            <span class="etiqueta-zona">
                                                <?= ucfirst(escaparDistribucion($asignacion['zona'])) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= number_format(
                                                (float) $asignacion['distancia_km'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>
                                            km
                                        </td>

                                        <!-- SELECCIÓN MANUAL DE ENCUESTADOR -->
                                        <td>

                                            <select
                                                name="asignaciones[<?= $idSolicitud ?>][id_encuestador]"
                                                required
                                            >

                                                <?php foreach ($encuestadores as $encuestador): ?>

                                                    <?php
                                                    $idEncuestador = (int) $encuestador['id_usuario'];
                                                    ?>

                                                    <option
                                                        value="<?= $idEncuestador ?>"
                                                        <?= $idEncuestador === $idEncuestadorActual ? 'selected' : '' ?>
                                                    >
                                                        <?= escaparDistribucion(
                                                            $encuestador['nombres'] . ' ' .
                                                            $encuestador['apellidos']
                                                        ) ?>
                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                        </td>

                                        <!-- SELECCIÓN MANUAL DE PUNTO -->
                                        <td>

                                            <select
                                                name="asignaciones[<?= $idSolicitud ?>][id_punto]"
                                                required
                                            >

                                                <?php foreach ($puntos as $punto): ?>

                                                    <?php
                                                    $idPunto = (int) $punto['id_punto'];
                                                    ?>

                                                    <option
                                                        value="<?= $idPunto ?>"
                                                        <?= $idPunto === $idPuntoActual ? 'selected' : '' ?>
                                                    >
                                                        <?= escaparDistribucion($punto['nombre']) ?>
                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                        </td>

                                        <td>
                                            <?= (int) $asignacion['visitas_asignadas'] ?>
                                            /
                                            <?= (int) $asignacion['limite_zona'] ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="acciones-distribucion">

                        <p>
                            Verifica todas las asignaciones antes
                            de confirmar. Después de confirmar,
                            las solicitudes quedarán asociadas
                            a sus respectivas hojas de ruta.
                        </p>

                        <button
                            type="submit"
                            class="btn-confirmar"
                            onclick="return confirm('¿Estás seguro de confirmar esta distribución? Se crearán las hojas de ruta y las solicitudes quedarán asignadas.');"
                        >
                            Confirmar distribución
                        </button>

                    </div>

                </form>

            <?php else: ?>

                <div class="mensaje-vacio">
                    No hay solicitudes disponibles para asignar
                    en la fecha seleccionada.
                </div>

            <?php endif; ?>

        </section>

        <!-- EXCEPCIONES -->
        <section class="panel-distribucion">

            <div class="titulo-panel">
                <div>
                    <h2>Solicitudes con excepciones</h2>

                    <p>
                        Solicitudes que no pudieron incluirse
                        en la distribución automática.
                    </p>
                </div>
            </div>

            <?php if (!empty($excepciones)): ?>

                <div class="tabla-contenedor">

                    <table class="tabla-distribucion">

                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th>Radicado</th>
                                <th>Motivo de la excepción</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($excepciones as $excepcion): ?>

                                <tr>

                                    <td>
                                        #<?= (int) $excepcion['id_solicitud'] ?>
                                    </td>

                                    <td>
                                        <?= escaparDistribucion($excepcion['radicado']) ?>
                                    </td>

                                    <td>
                                        <?= escaparDistribucion($excepcion['motivo']) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="mensaje-vacio">
                    No hay solicitudes con excepciones.
                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</main>

</body>
</html>


