<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consultar solicitudes | ENCUESTAS360</title>

    <link rel="stylesheet"
          href="/Proyecto_BD_Parte_1/public/assets/css/stylesVentanilla.css">

    <link rel="stylesheet"
          href="/Proyecto_BD_Parte_1/public/assets/css/stylesConsulta.css">
</head>

<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main">

    <header class="dashboard-header">
        <div>
            <span class="dashboard-label">GESTIÓN DE SOLICITUDES</span>
            <h1>Consultar solicitudes</h1>
            <p>Busca y consulta las solicitudes registradas en tu punto de atención.</p>
        </div>

        <div class="header-date">
            <span>FECHA DE HOY</span>
            <strong><?= date('d/m/Y') ?></strong>
        </div>
    </header>

    <!-- ESTADISTICAS -->
    <section class="consulta-stats">

        <article class="consulta-stat-card">
            <div class="consulta-stat-icon">▤</div>
            <div>
                <span>Total de solicitudes</span>
                <strong><?= (int)($estadisticas['total'] ?? 0) ?></strong>
                <small>En tu punto de atención</small>
            </div>
        </article>

        <article class="consulta-stat-card">
            <div class="consulta-stat-icon">◷</div>
            <div>
                <span>Registradas hoy</span>
                <strong><?= (int)($estadisticas['hoy'] ?? 0) ?></strong>
                <small>Durante el día de hoy</small>
            </div>
        </article>

        <article class="consulta-stat-card">
            <div class="consulta-stat-icon vencidas-icon">!</div>
            <div>
                <span>Fecha límite vencida</span>
                <strong><?= (int)($estadisticas['vencidas'] ?? 0) ?></strong>
                <small>Requieren revisión</small>
            </div>
        </article>

    </section>

    <!-- BUSCADOR -->
    <section class="consulta-filter-section">

        <div class="consulta-heading">
            <div>
                <h2>Buscar solicitudes</h2>
                <p>Utiliza el radicado, nombre, apellido o documento del solicitante.</p>
            </div>
        </div>

        <form method="GET" action="index.php" class="consulta-form">

            <input type="hidden" name="action" value="ventanilla_consultar">

            <div class="consulta-search">
                <label for="buscar">Buscar</label>
                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    placeholder="Ej. RAD-2026, María o número de documento"
                    value="<?= htmlspecialchars($busqueda) ?>">
            </div>

            <div class="consulta-select">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="">Todos los estados</option>

                    <?php foreach ($estados as $e): ?>
                        <option
                            value="<?= htmlspecialchars($e) ?>"
                            <?= $estado === $e ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $e))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="consulta-btn">
                Buscar
            </button>

            <a href="index.php?action=ventanilla_consultar"
               class="consulta-clear-btn">
                Limpiar
            </a>

        </form>

    </section>

    <!-- TABLA -->
    <section class="table-section consulta-table-section">

        <div class="section-heading">
            <div>
                <h2>Listado de solicitudes</h2>
                <p>Resultados correspondientes a tu punto de atención.</p>
            </div>

            <span class="consulta-counter">
                <?= count($solicitudes) ?> resultado(s)
            </span>
        </div>

        <div class="table-responsive">

            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Radicado</th>
                        <th>Solicitante</th>
                        <th>Documento</th>
                        <th>Ubicación</th>
                        <th>Fecha de registro</th>
                        <th>Fecha límite</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($solicitudes)): ?>

                        <?php foreach ($solicitudes as $s): ?>

                            <?php
                                $fechaVencida =
                                    !empty($s['fecha_limite']) &&
                                    $s['fecha_limite'] < date('Y-m-d');
                            ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($s['radicado']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($s['nombres'] . ' ' . $s['apellidos']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $s['tipo_documento'] . ' ' . $s['numero_documento']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($s['barrio']) ?>
                                    <small>
                                        <?= htmlspecialchars($s['comuna']) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= htmlspecialchars($s['fecha_radicacion'] ?? '') ?>
                                </td>

                                <td>
                                    <span class="<?= $fechaVencida ? 'fecha-vencida' : 'fecha-normal' ?>">
                                        <?= htmlspecialchars($s['fecha_limite'] ?? '') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="status-badge status-neutral">
                                        <?= htmlspecialchars(
                                            ucfirst(str_replace('_', ' ', $s['estado']))
                                        ) ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="empty-table">
                                <div class="consulta-empty">
                                    <span>▤</span>
                                    <strong>No se encontraron solicitudes</strong>
                                    <p>
                                        Prueba con otro término de búsqueda
                                        o selecciona un estado diferente.
                                    </p>
                                </div>
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

</body>
</html>
