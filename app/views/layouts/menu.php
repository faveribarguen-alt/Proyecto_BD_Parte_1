<?php
// Menú superior según el rol. Se incluye justo después de <body>.
iniciarSesionSegura();
$rolMenu = (int) ($_SESSION['id_rol'] ?? 0);
?>
<link rel="stylesheet" href="assets/css/stylesMenu.css">
<nav class="menu no-imprimir">
    <span class="menu-usuario"><?= htmlspecialchars($_SESSION['nombre_completo'] ?? '') ?></span>
    <?php if ($rolMenu === 1): ?>
        <a href="index.php?action=bitacora">Bitácora</a>
        <a href="index.php?action=hojas_ruta">Hojas de ruta</a>
    <?php elseif ($rolMenu === 2): ?>
        <a href="index.php?action=ventanilla_registrar">Registrar solicitud</a>
        <a href="index.php?action=ventanilla_hojas">Entrega de hojas de ruta</a>
    <?php endif; ?>
    <a href="index.php?action=puntos_atencion">Puntos de atención</a>
    <a href="index.php?action=logout" class="menu-salir">Cerrar sesión</a>
</nav>
