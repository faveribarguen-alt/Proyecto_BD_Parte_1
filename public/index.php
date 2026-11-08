<?php
// ARCHIVO PRINCIPAL - FRONT CONTROLLER
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/VentanillaController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'login';

$authController = new AuthController();
$ventanillaController = new VentanillaController();

switch ($action) {
    case 'login':
        $authController->mostrarLogin();
        break;
    case 'procesar_login':
        $authController->procesarLogin();
        break;
    case 'inicio':
        // Redirigimos al formulario de ventanilla como pantalla principal temporal
        header("Location: index.php?action=ventanilla_registrar");
        break;
    case 'logout':
        $authController->logout();
        break;
        
    // NUEVAS RUTAS DE VENTANILLA
    case 'ventanilla_registrar':
        $ventanillaController->mostrarFormulario();
        break;
    case 'guardar_solicitud':
        $ventanillaController->procesarSolicitud();
        break;
        
    default:
        $authController->mostrarLogin();
        break;
}
?>