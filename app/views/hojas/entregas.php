<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega de Hojas de Ruta</title>
    <link rel="stylesheet" href="assets/css/stylesHojas.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu.php'; ?>

<div class="contenedor">
    <h2>Entrega de hojas de ruta</h2>
    <p>Hojas de los encuestadores de tu punto de atención. Se entregan en la mañana de la visita.</p>
    <?php mostrarMensaje(); ?>

    <table>
        <thead>
            <tr>
                <th>N.º</th>
                <th>Encuestador</th>
                <th>Fecha de visita</th>
                <th>Solicitudes</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cargas)): ?>
                <?php foreach ($cargas as $c): ?>
                    <tr>
                        <td><?= (int) $c['id_carga'] ?></td>
                        <td><?= htmlspecialchars($c['encuestador']) ?></td>
                        <td><?= htmlspecialchars($c['fecha_visita']) ?></td>
                        <td><?= (int) $c['total'] ?></td>
                        <td><span class="badge"><?= htmlspecialchars(strtoupper($c['estado'])) ?></span></td>
                        <td>
                            <a href="index.php?action=ver_hoja&id=<?= (int) $c['id_carga'] ?>">Ver / imprimir</a>
                            <?php if ($c['estado'] === 'programada'): ?>
                                <?php if ($c['puede_entregar']): ?>
                                    <form action="index.php?action=entregar_hoja" method="POST" class="en-linea">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="id_carga" value="<?= (int) $c['id_carga'] ?>">
                                        <button type="submit" class="btn-principal">Marcar entregada</button>
                                    </form>
                                <?php else: ?>
                                    <span class="aviso">Disponible desde el <?= htmlspecialchars($c['fecha_visita']) ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="centro">No hay hojas de ruta pendientes en tu punto.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
