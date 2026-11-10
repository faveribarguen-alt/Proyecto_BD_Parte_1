<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventanilla - Registrar Solicitud</title>
    <!-- Enlace correcto a la carpeta de assets -->
    <link rel="stylesheet" href="assets/css/stylesVentanilla.css">
</head>
<body>

<?php require __DIR__ . '/../layouts/menu.php'; ?>

<div class="form-container">
    <h2>Registrar Solicitud de Encuesta</h2>
    
    <?php if(isset($mensaje)): ?>
        <div class="alert-success">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=guardar_solicitud" method="POST">
        
        <div class="grid-2">
            <div class="form-group">
                <label>Tipo de Documento</label>
                <select name="tipo_documento" required>
                    <option value="">Seleccione...</option>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="TI">Tarjeta de Identidad</option>
                    <option value="CE">Cédula de Extranjería</option>
                </select>
            </div>
            <div class="form-group">
                <label>Número de Documento</label>
                <input type="text" name="numero_documento" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="nombres" required>
            </div>
            <div class="form-group">
                <label>Apellidos</label>
                <input type="text" name="apellidos" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Celular</label>
                <input type="text" name="celular" required>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Barrio (y Comuna)</label>
                <select name="id_barrio" required>
                    <option value="">Seleccione el barrio...</option>
                    <?php if(isset($barrios)): ?>
                        <?php foreach($barrios as $b): ?>
                            <option value="<?php echo $b['id_barrio']; ?>">
                                <?php echo $b['nombre'] . ' (' . $b['comuna'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
        
        <!-- Sección de Georreferenciación -->
        <h3>Georreferenciación</h3>
        <button type="button" class="btn-geo" onclick="obtenerUbicacion()">📍 Capturar Coordenadas Actuales</button>
        <div class="grid-2">
            <div class="form-group">
                <label>Latitud</label>
                <!-- OJO: Aún no hemos creado estos campos en la base de datos, lo haremos en el modelo -->
                <input type="text" id="latitud" name="latitud" readonly required>
            </div>
            <div class="form-group">
                <label>Longitud</label>
                <input type="text" id="longitud" name="longitud" readonly required>
            </div>
        </div>

        <button type="submit" class="btn-submit">Guardar Solicitud</button>
    </form>
</div>

<!-- Script para obtener la ubicación (Punto 3 del PDF) -->
<script>
function obtenerUbicacion() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitud').value = position.coords.latitude;
            document.getElementById('longitud').value = position.coords.longitude;
            alert("Coordenadas capturadas con éxito.");
        }, function(error) {
            alert("Error al obtener la ubicación. Verifique los permisos de su navegador.");
        });
    } else {
        alert("La geolocalización no es soportada por este navegador.");
    }
}
</script>

</body>
</html>