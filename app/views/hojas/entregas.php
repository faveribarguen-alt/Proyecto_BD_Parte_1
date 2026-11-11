<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas de hojas de ruta | ENCUESTAS360</title>

    <link rel="stylesheet"
          href="/Proyecto_BD_Parte_1/public/assets/css/stylesVentanilla.css">
</head>
<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<main class="ventanilla-main">

    <header class="dashboard-header">
        <div>
            <span class="dashboard-label">GESTIÓN DE ENCUESTADORES</span>
            <h1>Entrega de hojas de ruta</h1>
            <p>Consulta las hojas asignadas a los encuestadores de tu punto de atención y registra su entrega.</p>
        </div>

        <div class="header-date">
            <span>FECHA DE HOY</span>
            <strong><?= date('d/m/Y') ?></strong>
        </div>
    </header>

    <?php mostrarMensaje(); ?>

    <section class="delivery-intro">
        <div class="delivery-intro-icon">▣</div>
        <div>
            <h2>Control de entregas</h2>
            <p>
                Desde aquí puedes consultar las hojas de ruta, revisar su fecha
                de visita y marcar como entregadas aquellas que estén disponibles.
            </p>
        </div>
    </section>

    <section class="table-section delivery-table-section">

        <div class="section-heading">
            <div>
                <h2>Hojas de ruta de tu punto</h2>
                <p>Listado de hojas asignadas a los encuestadores.</p>
            </div>

            <span class="delivery-total">
                <?= count($cargas ?? []) ?> registro(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Encuestador</th>
                        <th>Fecha de visita</th>
                        <th>Solicitudes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($cargas)): ?>

                        <?php foreach ($cargas as $c): ?>
                            <tr>
                                <td>
                                    <strong>#<?= (int) $c['id_carga'] ?></strong>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($c['encuestador']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($c['fecha_visita']) ?>
                                </td>

                                <td>
                                    <span class="delivery-quantity">
                                        <?= (int) $c['total'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($c['estado'] === 'programada'): ?>
                                        <span class="status-badge status-warning">
                                            Programada
                                        </span>
                                    <?php elseif ($c['estado'] === 'en_campo'): ?>
                                        <span class="status-badge status-success">
                                            En campo
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge status-neutral">
                                            <?= htmlspecialchars(strtoupper($c['estado'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="delivery-actions">

                                        <a
                                            href="index.php?action=ver_hoja&id=<?= (int) $c['id_carga'] ?>"
                                            class="delivery-view-btn">
                                            Ver / imprimir
                                        </a>

                                        <?php if ($c['estado'] === 'programada'): ?>

                                            <?php if ($c['puede_entregar']): ?>

                                                <form
                                                    action="index.php?action=entregar_hoja"
                                                    method="POST"
                                                    class="delivery-form">

                                                    <input
                                                        type="hidden"
                                                        name="csrf"
                                                        value="<?= htmlspecialchars($csrf) ?>">

                                                    <input
                                                        type="hidden"
                                                        name="id_carga"
                                                        value="<?= (int) $c['id_carga'] ?>">

                                                    <button
                                                        type="submit"
                                                        class="delivery-submit-btn">
                                                        Marcar entregada
                                                    </button>
                                                </form>

                                            <?php else: ?>

                                                <span class="delivery-wait">
                                                    Disponible desde el
                                                    <?= htmlspecialchars($c['fecha_visita']) ?>
                                                </span>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="empty-table">
                                <div class="delivery-empty">
                                    <span>▣</span>
                                    <strong>No hay hojas de ruta para mostrar</strong>
                                    <p>
                                        Cuando se asignen hojas a los encuestadores
                                        de tu punto, aparecerán en este listado.
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
