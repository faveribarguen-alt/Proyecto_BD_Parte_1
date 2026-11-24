<?php
// Menú superior del administrador
iniciarSesionSegura();
?>
<link rel="stylesheet" href="assets/css/stylesMenu.css">
<nav class="menu no-imprimir">
    <span class="menu-usuario"><?= htmlspecialchars($_SESSION['nombre_completo'] ?? '') ?></span>
    <a href="index.php?action=panel_admin">Inicio</a>
    <a href="index.php?action=informes">Informes PDF</a>
    <a href="index.php?action=puntos_atencion">Puntos de atención</a>
    <a href="index.php?action=dashboard">Dashboard</a>
    <a href="index.php?action=logout" class="menu-salir">Cerrar sesión</a>
</nav>