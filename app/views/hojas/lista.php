
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hojas de ruta - Enrutador</title>

    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
    <link rel="stylesheet" href="assets/css/stylesHojas.css">
</head>
<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main hojas-main">

    <header class="hojas-header">
        <div>
            <span class="hojas-etiqueta">GESTIÓN DE VISITAS</span>
            <h1>Hojas de ruta</h1>
            <p>
                Organiza y administra las solicitudes asignadas a los encuestadores.
            </p>
        </div>

        <div class="hojas-fecha">
            <span>Fecha actual</span>
            <strong><?= date('d/m/Y') ?></strong>
        </div>
    </header>

    <?php mostrarMensaje(); ?>

    <!-- GENERAR HOJA DE RUTA -->
    <section class="hojas-tarjeta">

        <div class="hojas-titulo-seccion">
            <div class="hojas-icono">+</div>
            <div>
                <h2>Generar una hoja de ruta</h2>
                <p>Asigna solicitudes pendientes a un encuestador.</p>
            </div>
        </div>

        <div class="hojas-resumen">
            <div>
                <span class="resumen-numero">
                    <?= (int)$pendientes ?>
                </span>
                <span class="resumen-texto">
                    Solicitudes pendientes por asignar
                </span>
            </div>

            <p>
                Cada hoja puede contener hasta 20 solicitudes.
                Se priorizan las que tienen una fecha límite más próxima.
            </p>
        </div>

        <?php if ($pendientes > 0 && !empty($encuestadores)): ?>

            <form
                action="index.php?action=generar_hoja"
                method="POST"
                class="hojas-formulario"
            >

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= htmlspecialchars($csrf) ?>"
                >

                <div class="hojas-campo">
                    <label for="id_encuestador">Encuestador</label>

                    <select
                        name="id_encuestador"
                        id="id_encuestador"
                        required
                    >
                        <option value="">Seleccione un encuestador</option>

                        <?php foreach ($encuestadores as $e): ?>
                            <option value="<?= (int)$e['id_usuario'] ?>">
                                <?= htmlspecialchars(
                                    $e['nombres'] . ' ' . $e['apellidos']
                                ) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="hojas-campo">
                    <label for="fecha_visita">Fecha de visita</label>

                    <input
                        type="date"
                        name="fecha_visita"
                        id="fecha_visita"
                        value="<?= htmlspecialchars($fechaSugerida) ?>"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>

                <button type="submit" class="hojas-boton">
                    <span>+</span> Generar hoja de ruta
                </button>

            </form>

        <?php elseif ($pendientes == 0): ?>

            <div class="hojas-aviso">
                <strong>No hay solicitudes pendientes.</strong>
                <p>
                    Cuando Ventanilla registre nuevas solicitudes,
                    podrás generar una hoja de ruta.
                </p>
            </div>

        <?php else: ?>

            <div class="hojas-aviso">
                <strong>No hay encuestadores activos.</strong>
                <p>
                    Solicita al administrador que revise los usuarios registrados.
                </p>
            </div>

        <?php endif; ?>

    </section>

    <!-- LISTADO DE HOJAS -->
    <section class="hojas-tarjeta">

        <div class="hojas-titulo-seccion">
            <div class="hojas-icono hojas-icono-secundario">
                <span>▤</span>
            </div>

            <div>
                <h2>Hojas de ruta generadas</h2>
                <p>Consulta las visitas que han sido programadas.</p>
            </div>
        </div>

        <div class="hojas-tabla-contenedor">

            <table class="hojas-tabla">

                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Encuestador</th>
                        <th>Fecha de visita</th>
                        <th>Solicitudes</th>
                        <th>Estado</th>
                        <th>Fecha de creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($cargas)): ?>

                        <?php foreach ($cargas as $c): ?>

                            <tr>
                                <td>
                                    <strong class="hojas-numero">
                                        #<?= (int)$c['id_carga'] ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="hojas-encuestador">
                                        <?= htmlspecialchars($c['encuestador']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= !empty($c['fecha_visita'])
                                        ? date('d/m/Y', strtotime($c['fecha_visita']))
                                        : 'Sin fecha' ?>
                                </td>

                                <td>
                                    <span class="hojas-cantidad">
                                        <?= (int)$c['total'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    $estado = $c['estado'] ?? '';
                                    $estadoClase = preg_replace(
                                        '/[^a-z_]/',
                                        '',
                                        strtolower($estado)
                                    );
                                    ?>

                                    <span class="hojas-estado estado-<?= htmlspecialchars($estadoClase) ?>">
                                        <?= htmlspecialchars(
                                            ucfirst(str_replace('_', ' ', $estado))
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= !empty($c['creada_en'])
                                        ? date('d/m/Y H:i', strtotime($c['creada_en']))
                                        : 'No disponible' ?>
                                </td>

                                <td>
                                    <a
                                        href="index.php?action=ver_hoja&id=<?= (int)$c['id_carga'] ?>"
                                        class="hojas-enlace"
                                    >
                                        Ver hoja →
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="hojas-vacia">
                                <span>▤</span>
                                <strong>Todavía no hay hojas de ruta.</strong>
                                <small>Cuando generes una hoja, aparecerá aquí.</small>
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>
