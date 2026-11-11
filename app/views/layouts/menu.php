<?php

iniciarSesionSegura();

$rolMenu = (int) ($_SESSION['id_rol'] ?? 0);
$accionActual = $_GET['action'] ?? '';

$nombreUsuario = $_SESSION['nombre_completo'] ?? 'Usuario';

$inicialUsuario = strtoupper(
    substr($nombreUsuario, 0, 1)
);

?>

<?php if ($rolMenu === 2): ?>

    <!-- =====================================
    MENÚ DE VENTANILLA
    ====================================== -->

    <aside class="sidebar no-imprimir">

        <div class="sidebar-brand">
            <div class="brand-icon">E</div>

            <div>
                <h2>ENCUESTAS360</h2>
                <span>Gestión de encuestas</span>
            </div>
        </div>

        <div class="sidebar-profile">

            <div class="profile-avatar">
                <?= htmlspecialchars($inicialUsuario) ?>
            </div>

            <div>
                <strong>
                    <?= htmlspecialchars($nombreUsuario) ?>
                </strong>

                <span>Personal de ventanilla</span>
            </div>

        </div>

        <span class="sidebar-section">MENÚ PRINCIPAL</span>

        <nav class="sidebar-nav">

            <a href="index.php?action=ventanilla_inicio"
            class="sidebar-link <?= $accionActual === 'ventanilla_inicio' ? 'active' : '' ?>">
                <span>⌂</span>
                Inicio
            </a>

            <a href="index.php?action=ventanilla_registrar"
            class="sidebar-link <?= $accionActual === 'ventanilla_registrar' || $accionActual === 'guardar_solicitud' ? 'active' : '' ?>">
                <span>＋</span>
                Registrar solicitud
            </a>

            <a href="index.php?action=ventanilla_consultar"
            class="sidebar-link <?= $accionActual === 'ventanilla_consultar' ? 'active' : '' ?>">
                <span>▤</span>
                Consultar solicitudes
            </a>

            <a href="index.php?action=ventanilla_hojas"
            class="sidebar-link <?= $accionActual === 'ventanilla_hojas' || $accionActual === 'ver_hoja' ? 'active' : '' ?>">
                <span>◷</span>
                Consultar entregas
            </a>

            <a href="index.php?action=puntos_atencion"
            class="sidebar-link <?= $accionActual === 'puntos_atencion' ? 'active' : '' ?>">
                <span>⌖</span>
                Puntos de atención
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="index.php?action=logout" class="sidebar-logout">
                <span>↪</span>
                Cerrar sesión
            </a>

        </div>

    </aside>


<?php elseif ($rolMenu === 1): ?>

    <!-- =====================================
        MENÚ DE ENRUTADOR
    ====================================== -->

    <aside class="sidebar no-imprimir">

        <div class="sidebar-brand">
            <div class="brand-icon">E</div>

            <div>
                <h2>ENCUESTAS360</h2>
                <span>Gestión de encuestas</span>
            </div>
        </div>

        <div class="sidebar-profile">

            <div class="profile-avatar">
                <?= htmlspecialchars($inicialUsuario) ?>
            </div>

            <div>
                <strong>
                    <?= htmlspecialchars($nombreUsuario) ?>
                </strong>

                <span>Personal de enrutamiento</span>
            </div>

        </div>

        <span class="sidebar-section">MENÚ PRINCIPAL</span>

        <nav class="sidebar-nav">

            <a href="index.php?action=enrutador_inicio"
            class="sidebar-link <?= $accionActual === 'enrutador_inicio' ? 'active' : '' ?>">
                <span>⌂</span>
                Inicio
            </a>

            <a href="index.php?action=bitacora"
            class="sidebar-link <?= $accionActual === 'bitacora' ? 'active' : '' ?>">
                <span>▤</span>
                Bitácora de solicitudes
            </a>

            <a href="index.php?action=georreferenciacion"
            class="sidebar-link <?= $accionActual === 'georreferenciacion' ? 'active' : '' ?>">
                <span>⌖</span>
                Georreferenciación
            </a>

            <a href="index.php?action=distribucion_visitas"
            class="sidebar-link <?= $accionActual === 'distribucion_visitas' ? 'active' : '' ?>">
                <span>⇄</span>
                Distribución de visitas
            </a>

            <a href="index.php?action=hojas_ruta"
            class="sidebar-link <?= in_array($accionActual, ['hojas_ruta', 'generar_hoja', 'ver_hoja'], true) ? 'active' : '' ?>">
                <span>◷</span>
                Hojas de ruta
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="index.php?action=logout" class="sidebar-logout">
                <span>↪</span>
                Cerrar sesión
            </a>

        </div>

    </aside>


<?php elseif (in_array($rolMenu, [3, 4, 5], true)): ?>

    <!-- =====================================
    MENÚ DE LOS DEMÁS PERFILES
    ====================================== -->

    <link rel="stylesheet" href="assets/css/stylesMenu.css">

    <nav class="menu no-imprimir">

        <span class="menu-usuario">
            <?= htmlspecialchars($nombreUsuario) ?>
        </span>

        <a href="index.php?action=puntos_atencion">
            Puntos de atención
        </a>

        <a href="index.php?action=logout" class="menu-salir">
            Cerrar sesión
        </a>

    </nav>

<?php endif; ?>
