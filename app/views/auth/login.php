<?php
$error = $error ?? '';
?>

<link rel="stylesheet" href="/Proyecto_BD_Parte_1/public/assets/css/styleLogin.css">

<div class="login-container">

    <div class="logo">
        <h1>ENCUESTAS360</h1>
        <p>Sistema de gestión de encuestas</p>
    </div>

    <div class="login-header">
        <div class="welcome">BIENVENIDO</div>

        <h2>Iniciar sesión</h2>

        <p>Ingresa tus datos para acceder al sistema.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=procesar_login">

        <div class="form-group">
            <label for="username">Correo electrónico</label>

            <input
                type="email"
                id="username"
                name="username"
                placeholder="usuario@ejemplo.com"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Ingresa tu contraseña"
                required
            >
        </div>

        <button type="submit" class="login-button">
            Ingresar al sistema
        </button>

    </form>

    <div class="login-footer">
        Acceso exclusivo para usuarios autorizados.<br>
        <strong>ENCUESTAS360</strong> © 2026
    </div>

</div>
