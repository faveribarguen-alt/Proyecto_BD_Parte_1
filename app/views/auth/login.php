<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Encuestas Valle</title>
    <!-- Enlace al archivo CSS externo. La ruta parte desde public/ -->
    <link rel="stylesheet" href="assets/css/styleLogin.css">
</head>
<body>

<div class="login-container">
    <h2>Inicio de Sesión</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="index.php?action=procesar_login" method="POST">
        <div class="form-group">
            <label for="username">Usuario</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-login">Ingresar</button>
    </form>
</div>

</body>
</html>