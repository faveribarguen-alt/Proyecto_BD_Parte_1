<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hoja de Ruta #<?= (int)$carga['id_carga'] ?></title>

    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
    <link rel="stylesheet" href="assets/css/stylesHojas.css">
</head>

<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main detalle-hoja">

    <?php mostrarMensaje(); ?>

    <!-- ENCABEZADO -->
    <header class="detalle-hoja-header no-imprimir">

        <div>
            <span class="hojas-etiqueta">GESTIÓN DE VISITAS</span>

            <h1>Detalle de hoja de ruta</h1>

            <p>
                Consulta la información y las solicitudes asignadas al encuestador.
            </p>
        </div>

        <div class="detalle-hoja-acciones">
            <a href="index.php?action=hojas_ruta" class="hojas-btn-volver">
                ← Volver a hojas
            </a>

            <button
                type="button"
                class="hojas-boton"
                onclick="window.print()"
            >
                Imprimir hoja
            </button>
        </div>

    </header>

    <!-- IDENTIFICACIÓN DE LA HOJA -->
    <section class="detalle-hoja-identificacion">

        <div>
            <span>HOJA DE RUTA</span>
            <h2>#<?= (int)$carga['id_carga'] ?></h2>
        </div>

        <div class="detalle-hoja-total">
            <strong><?= count($solicitudes) ?></strong>
            <span>Visitas asignadas</span>
        </div>

    </section>

    <!-- INFORMACIÓN GENERAL -->
    <section class="detalle-hoja-panel">

        <div class="detalle-hoja-titulo">
            <div class="hojas-icono">▤</div>

            <div>
                <h2>Información general</h2>
                <p>Datos de programación y asignación.</p>
            </div>
        </div>

        <div class="detalle-hoja-grid">

            <div class="detalle-dato">
                <span>Encuestador</span>
                <strong>
                    <?= htmlspecialchars($carga['encuestador']) ?>
                </strong>
            </div>

            <div class="detalle-dato">
                <span>Punto de atención</span>
                <strong>
                    <?= htmlspecialchars($carga['punto'] ?? 'Sin punto') ?>
                </strong>
            </div>

            <div class="detalle-dato">
                <span>Fecha de visita</span>
                <strong>
                    <?= htmlspecialchars($carga['fecha_visita']) ?>
                </strong>
            </div>

            <div class="detalle-dato">
                <span>Estado de la hoja</span>

                <?php
                $estado = $carga['estado'] ?? '';
                $estadoClase = preg_replace(
                    '/[^a-z_]/',
                    '',
                    strtolower($estado)
                );
                ?>

                <strong>
                    <span class="hojas-estado estado-<?= htmlspecialchars($estadoClase) ?>">
                        <?= htmlspecialchars(
                            ucfirst(str_replace('_', ' ', $estado))
                        ) ?>
                    </span>
                </strong>
            </div>

            <div class="detalle-dato">
                <span>Generada por</span>
                <strong>
                    <?= htmlspecialchars($carga['enrutador']) ?>
                </strong>
            </div>

            <div class="detalle-dato">
                <span>Fecha de creación</span>
                <strong>
                    <?= htmlspecialchars($carga['creada_en']) ?>
                </strong>
            </div>

            <?php if (!empty($carga['entregada_en'])): ?>

                <div class="detalle-dato">
                    <span>Fecha de entrega</span>
                    <strong>
                        <?= htmlspecialchars($carga['entregada_en']) ?>
                    </strong>
                </div>

                <div class="detalle-dato">
                    <span>Entregada por</span>
                    <strong>
                        <?= htmlspecialchars($carga['entregada_por'] ?? '') ?>
                    </strong>
                </div>

            <?php endif; ?>

        </div>

    </section>

    <!-- LISTADO DE SOLICITUDES -->
    <section class="detalle-hoja-panel">

        <div class="detalle-hoja-titulo">
            <div class="hojas-icono hojas-icono-secundario">☷</div>

            <div>
                <h2>Solicitudes asignadas</h2>
                <p>
                    Información de los hogares que deben ser visitados.
                </p>
            </div>
        </div>

        <div class="detalle-tabla-contenedor">

            <table class="detalle-tabla">

                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Radicado</th>
                        <th>Solicitante</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Barrio / Comuna</th>
                        <th>Fecha límite</th>
                        <th class="no-imprimir">Mapa</th>
                        <th>Llamada confirmada</th>
                        <th>Visitada</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($solicitudes)): ?>

                        <?php foreach ($solicitudes as $i => $s): ?>

                            <tr>

                                <td class="detalle-numero">
                                    <?= $i + 1 ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($s['radicado']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $s['nombres'] . ' ' . $s['apellidos']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($s['telefono']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($s['direccion']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $s['barrio'] . ' (' . $s['comuna'] . ')'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($s['fecha_limite']) ?>
                                </td>

                                <td class="no-imprimir">

                                    <?php if (
                                        isset($s['latitud'], $s['longitud']) &&
                                        $s['latitud'] !== null &&
                                        $s['longitud'] !== null
                                    ): ?>

                                        <a
                                            class="detalle-mapa"
                                            href="https://maps.google.com/?q=<?= rawurlencode($s['latitud'] . ',' . $s['longitud']) ?>"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            Ver mapa
                                        </a>

                                    <?php else: ?>

                                        <span class="detalle-sin-mapa">
                                            Sin ubicación
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="detalle-casilla">
                                    <span>☐</span>
                                </td>

                                <td class="detalle-casilla">
                                    <span>☐</span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="10" class="detalle-tabla-vacia">
                                No hay solicitudes asignadas a esta hoja.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <p class="detalle-nota no-imprimir">
            Las casillas permiten llevar un control visual de las llamadas
            y visitas realizadas durante el recorrido.
        </p>

    </section>

    <!-- FIRMAS -->
    <section class="detalle-hoja-panel detalle-firmas">

        <div class="detalle-hoja-titulo">
            <div class="hojas-icono hojas-icono-secundario">✎</div>

            <div>
                <h2>Registro de entrega</h2>
                <p>Espacio para dejar constancia de la entrega de la hoja.</p>
            </div>
        </div>

        <div class="detalle-firmas-grid">

            <div class="detalle-firma">
                <div class="detalle-linea-firma"></div>
                <strong>Firma del encuestador</strong>
                <span>Nombre y firma de quien recibe</span>
            </div>

            <div class="detalle-firma">
                <div class="detalle-linea-firma"></div>
                <strong>Firma de Ventanilla</strong>
                <span>Nombre y firma de quien entrega</span>
            </div>

        </div>

    </section>

    <footer class="detalle-hoja-footer">
        ENCUESTAS360 · Control y seguimiento de visitas
    </footer>

</main>

</body>
</html>

