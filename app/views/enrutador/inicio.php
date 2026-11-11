<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Enrutador</title>

    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
    <link rel="stylesheet" href="assets/css/stylesEnrutadorInicio.css">
</head>
<body>

<?php
require_once __DIR__ . '/../layouts/menu.php';

$total = (int)($resumen['total_solicitudes'] ?? 0);
$pendientes = (int)($resumen['pendientes_asignacion'] ?? 0);
$sinGeo = (int)($resumen['sin_georreferenciar'] ?? 0);
?>

<main class="ventanilla-main enrutador-inicio">

    <header class="inicio-header">
        <div>
            <span class="inicio-etiqueta">PANEL DE CONTROL</span>
            <h1>Bienvenido, Enrutador</h1>
            <p>
                Desde aquí puedes supervisar las solicitudes,
                las ubicaciones y las hojas de ruta.
            </p>
        </div>

        <div class="inicio-fecha">
            <span>Fecha actual</span>
            <strong><?= date('d/m/Y') ?></strong>
        </div>
    </header>

    <!-- TARJETAS DE ESTADÍSTICAS -->
    <section class="enrutador-estadisticas">

        <article class="estadistica-card">
            <div class="estadistica-icono morado">
                <i class="icono">▤</i>
            </div>
            <div>
                <span>Total de solicitudes</span>
                <strong><?= $total ?></strong>
                <small>Registradas en el sistema</small>
            </div>
        </article>

        <article class="estadistica-card">
            <div class="estadistica-icono naranja">
                <i class="icono">◷</i>
            </div>
            <div>
                <span>Pendientes de asignación</span>
                <strong><?= $pendientes ?></strong>
                <small>Esperando hoja de ruta</small>
            </div>
        </article>

        <article class="estadistica-card">
            <div class="estadistica-icono rosado">
                <i class="icono">⌖</i>
            </div>
            <div>
                <span>Sin georreferenciar</span>
                <strong><?= $sinGeo ?></strong>
                <small>Sin coordenadas completas</small>
            </div>
        </article>

        <article class="estadistica-card">
            <div class="estadistica-icono verde">
                <i class="icono">➤</i>
            </div>
            <div>
                <span>Hojas programadas</span>
                <strong><?= (int)$hojasProgramadas ?></strong>
                <small>Visitas pendientes de iniciar</small>
            </div>
        </article>

    </section>

    <!-- SOLICITUDES RECIENTES -->
    <section class="inicio-seccion">

        <div class="seccion-encabezado">
            <div>
                <h2>Solicitudes recientes</h2>
                <p>Últimas solicitudes registradas en todos los puntos.</p>
            </div>

            <a href="index.php?action=bitacora" class="enlace-seccion">
                Ver bitácora →
            </a>
        </div>

        <div class="tabla-contenedor">
            <table class="tabla-enrutador">
                <thead>
                    <tr>
                        <th>Radicado</th>
                        <th>Ciudadano</th>
                        <th>Ubicación</th>
                        <th>Punto de atención</th>
                        <th>Estado</th>
                        <th>Fecha límite</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($solicitudesRecientes)): ?>

                    <?php foreach ($solicitudesRecientes as $solicitud): ?>

                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($solicitud['radicado'] ?? '') ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    ($solicitud['nombres'] ?? '') . ' ' .
                                    ($solicitud['apellidos'] ?? '')
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($solicitud['barrio'] ?? '') ?>
                                <small>
                                    <?= htmlspecialchars($solicitud['comuna'] ?? '') ?>
                                </small>
                            </td>

                            <td>
                                <?= htmlspecialchars($solicitud['punto_atencion'] ?? 'No disponible') ?>
                            </td>

                            <td>
                                <span class="estado-enrutador estado-<?= htmlspecialchars($solicitud['estado'] ?? '') ?>">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $solicitud['estado'] ?? ''))) ?>
                                </span>
                            </td>

                            <td>
                                <?= !empty($solicitud['fecha_limite'])
                                    ? date('d/m/Y', strtotime($solicitud['fecha_limite']))
                                    : 'Sin fecha' ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="tabla-vacia">
                            Todavía no hay solicitudes registradas.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </section>

    <!-- HOJAS DE RUTA RECIENTES -->
    <section class="inicio-seccion">

        <div class="seccion-encabezado">
            <div>
                <h2>Hojas de ruta recientes</h2>
                <p>Control de las últimas visitas programadas.</p>
            </div>

            <a href="index.php?action=hojas_ruta" class="enlace-seccion">
                Ver hojas de ruta →
            </a>
        </div>

        <div class="tabla-contenedor">
            <table class="tabla-enrutador">
                <thead>
                    <tr>
                        <th>N.º de hoja</th>
                        <th>Encuestador</th>
                        <th>Fecha de visita</th>
                        <th>Solicitudes</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($hojasRecientes)): ?>

                    <?php foreach ($hojasRecientes as $hoja): ?>

                        <tr>
                            <td>
                                <strong>
                                    #<?= (int)$hoja['id_carga'] ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($hoja['encuestador'] ?? '') ?>
                            </td>

                            <td>
                                <?= !empty($hoja['fecha_visita'])
                                    ? date('d/m/Y', strtotime($hoja['fecha_visita']))
                                    : 'Sin fecha' ?>
                            </td>

                            <td>
                                <?= (int)($hoja['total_solicitudes'] ?? 0) ?>
                            </td>

                            <td>
                                <span class="estado-enrutador estado-<?= htmlspecialchars($hoja['estado'] ?? '') ?>">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $hoja['estado'] ?? ''))) ?>
                                </span>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="tabla-vacia">
                            Todavía no se han generado hojas de ruta.
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
