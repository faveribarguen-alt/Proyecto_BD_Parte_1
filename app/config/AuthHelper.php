<?php
// Funciones de apoyo: sesión, permisos por rol, protección de formularios y mensajes.

function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Roles: 1 enrutador, 2 ventanilla, 3 encuestador, 4 calidad, 5 administrador
function requerirRol(array $rolesPermitidos) {
    iniciarSesionSegura();

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: index.php?action=login");
        exit();
    }

    if (!in_array((int) $_SESSION['id_rol'], $rolesPermitidos, true)) {
        http_response_code(403);
        echo "<h2>No tienes permiso para ver esta página.</h2>";
        echo "<a href='index.php?action=logout'>Cerrar sesión</a>";
        exit();
    }
}

// Protección de formularios (CSRF): cada formulario lleva un código secreto de la sesión
function tokenCsrf() {
    iniciarSesionSegura();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validarCsrf() {
    iniciarSesionSegura();
    $enviado = $_POST['csrf'] ?? '';
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $enviado);
}

// Mensajes que se muestran una sola vez después de una acción
function guardarMensaje($tipo, $texto) {
    iniciarSesionSegura();
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

function mostrarMensaje() {
    iniciarSesionSegura();
    if (!empty($_SESSION['mensaje'])) {
        $m = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        $clase = ($m['tipo'] === 'ok') ? 'alerta-ok' : 'alerta-error';
        echo '<div class="' . $clase . '">' . htmlspecialchars($m['texto']) . '</div>';
    }
}
?>
