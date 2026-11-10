<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hojas de Ruta</title>
    <link rel="stylesheet" href="assets/css/stylesHojas.css">
</head>
<body>
<?php require __DIR__ . '/../layouts/menu.php'; ?>

<div class="contenedor">
    <h2>Hojas de ruta</h2>
    <?php mostrarMensaje(); ?>

    <div class="tarjeta">
        <h3>Generar una hoja de ruta</h3>
        <p>Solicitudes pendientes por asignar: <strong><?= (int) $pendientes ?></strong>.
           Cada hoja lleva hasta 20 solicitudes, las que vencen primero.</p>

        <?php if ($pendientes > 0 && !empty($encuestadores)): ?>
            <form action="index.php?action=generar_hoja" method="POST" class="fila-form">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

                <div class="campo">
                    <label for="id_encuestador">Encuestador</label>
                    <select name="id_encuestador" id="id_encuestador" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($encuestadores as $e): ?>
                            <option value="<?= (int) $e['id_usuario'] ?>">
                                <?= htmlspecialchars($e['nombres'] . ' ' . $e['apellidos']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="fecha_visita">Fecha de visita</label>
                    <input type="date" name="fecha_visita" id="fecha_visita"
                           value="<?= htmlspecialchars($fechaSugerida) ?>" required>
                </div>

                <button type="submit" class="btn-principal">Generar hoja de ruta</button>
            </form>
        <?php elseif ($pendientes == 0): ?>
            <p class="aviso">No hay solicitudes pendientes. Cuando ventanilla registre nuevas, podrás armar la hoja.</p>
        <?php else: ?>
            <p class="aviso">No hay encuestadores activos.</p>
        <?php endif; ?>
    </div>

    <h3>Hojas generadas</h3>
    <table>
        <thead>
            <tr>
                <th>N.º</th>
                <th>Encuestador</th>
                <th>Fecha de visita</th>
                <th>Solicitudes</th>
                <th>Estado</th>
                <th>Creada</th>
                <th></th>
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
                        <td><?= htmlspecialchars($c['creada_en']) ?></td>
                        <td><a href="index.php?action=ver_hoja&id=<?= (int) $c['id_carga'] ?>">Ver hoja</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="centro">Todavía no hay hojas de ruta.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
