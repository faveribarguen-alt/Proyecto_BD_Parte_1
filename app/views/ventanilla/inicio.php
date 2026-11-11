<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inicio - Ventanilla | ENCUESTAS360</title>

    <link rel="stylesheet"
          href="/Proyecto_BD_Parte_1/public/assets/css/stylesVentanilla.css">
</head>

<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main">

    <header class="dashboard-header">
        <div>
            <span class="dashboard-label">PANEL DE CONTROL</span>

            <h1>¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?>!</h1>

            <p>Este es el resumen de actividades de tu punto de atención.</p>
        </div>

        <div class="header-date">
            <span>FECHA DE HOY</span>
            <strong><?= date('d/m/Y') ?></strong>
        </div>
    </header>


    <section class="quick-actions">

        <a href="index.php?action=ventanilla_registrar"
           class="btn-primary">
            <span>＋</span> Registrar solicitud
        </a>

        <a href="index.php?action=ventanilla_hojas"
           class="btn-secondary">
            Consultar entregas
        </a>

    </section>


    <section class="stats-grid">

        <article class="stat-card">
            <div class="stat-icon purple">▤</div>
            <div>
                <span>Total registradas</span>
                <h2><?= (int)($resumen['total_solicitudes'] ?? 0) ?></h2>
                <small>En tu punto de atención</small>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon lavender">▦</div>
            <div>
                <span>Registradas hoy</span>
                <h2><?= (int)($resumen['solicitudes_hoy'] ?? 0) ?></h2>
                <small>Durante el día de hoy</small>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon orange">⌖</div>
            <div>
                <span>Sin georreferenciar</span>
                <h2><?= (int)($resumen['sin_georreferenciar'] ?? 0) ?></h2>
                <small>Solicitudes sin coordenadas</small>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon green">✓</div>
            <div>
                <span>Hojas por entregar</span>
                <h2><?= (int)$hojasPendientes ?></h2>
                <small>Listas para entregar hoy</small>
            </div>
        </article>

    </section>


    <section class="alert-card">
        <div class="alert-icon">!</div>

        <div>
            <h3>Seguimiento de fechas límite</h3>
            <p>
                Tienes
                <strong><?= (int)($resumen['proximas_a_vencer'] ?? 0) ?></strong>
                solicitudes con fecha límite dentro de los próximos tres días.
                Revisa su estado para evitar retrasos.
            </p>
        </div>
    </section>


    <section class="table-section">

        <div class="section-heading">
            <div>
                <h2>Solicitudes recientes</h2>
                <p>Últimos registros de tu punto de atención.</p>
            </div>

            <a href="index.php?action=ventanilla_registrar">
                + Nueva solicitud
            </a>
        </div>

        <div class="table-responsive">

            <table class="requests-table">

                <thead>
                    <tr>
                        <th>Radicado</th>
                        <th>Solicitante</th>
                        <th>Ubicación</th>
                        <th>Fecha de registro</th>
                        <th>Georreferenciación</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($solicitudesRecientes)): ?>

                    <?php foreach ($solicitudesRecientes as $solicitud): ?>

                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($solicitud['radicado']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $solicitud['nombres'] . ' ' . $solicitud['apellidos']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($solicitud['barrio']) ?>
                                <small>
                                    <?= htmlspecialchars($solicitud['comuna']) ?>
                                </small>
                            </td>

                            <td>
                                <?= htmlspecialchars($solicitud['fecha_radicacion']) ?>
                            </td>

                            <td>
                                <?php if ($solicitud['tiene_georreferenciacion']): ?>
                                    <span class="status-badge status-success">
                                        Registrada
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-warning">
                                        Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="status-badge status-neutral">
                                    <?= htmlspecialchars($solicitud['estado']) ?>
                                </span>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="empty-table">
                            Todavía no hay solicitudes registradas en este punto.
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