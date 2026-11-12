<?php
// ARCHIVO PRINCIPAL - FRONT CONTROLLER

// Hora de Colombia (si no, PHP usa otra zona y las fechas quedan corridas)
date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/../app/config/AuthHelper.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/VentanillaController.php';
require_once __DIR__ . '/../app/controllers/EnrutadorController.php';
require_once __DIR__ . '/../app/controllers/PuntoController.php';
require_once __DIR__ . '/../app/controllers/HojaRutaController.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'login';

$authController = new AuthController();
$ventanillaController = new VentanillaController();
$enrutadorController = new EnrutadorController();
$puntoController = new PuntoController();
$hojaRutaController = new HojaRutaController();

switch ($action) {
    case 'login':
        $authController->mostrarLogin();
        break;
    case 'procesar_login':
        $authController->procesarLogin();
        break;
    case 'inicio':
        header("Location: index.php?action=login");
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'enrutador_inicio':
    $enrutadorController->mostrarInicio();
    break;

    // RUTAS DE VENTANILLA
    case 'ventanilla_inicio':
        $ventanillaController->mostrarInicio();
        break;

    case 'ventanilla_registrar':
        $ventanillaController->mostrarFormulario();
        break;

    case 'ventanilla_consultar':
        $ventanillaController->consultarSolicitudes();
        break;

    case 'guardar_solicitud':
        $ventanillaController->procesarSolicitud();
        break;

    case 'ventanilla_hojas':
        $hojaRutaController->listarEntregas();
        break;

    // RUTAS DE ENRUTADOR
    case 'bitacora':
        $enrutadorController->mostrarBitacora();
        break;
    case 'georreferenciacion':
        $enrutadorController->mostrarGeorreferenciacion();
        break;
    case 'distribucion_visitas':
        $enrutadorController->mostrarDistribucionVisitas();
        break;
    case 'confirmar_distribucion':
        $enrutadorController->confirmarDistribucion();
        break;
    case 'hojas_ruta':
        $hojaRutaController->listar();
        break;
    case 'entregar_hoja':
        $hojaRutaController->entregar();
        break;
    case 'generar_hoja':
        $hojaRutaController->generar();
        break;
    

    // VER UNA HOJA DE RUTA (enrutador, ventanilla, encuestador y administrador)
    case 'ver_hoja':
        $hojaRutaController->ver();
        break;

    // PUNTOS DE ATENCIÓN (mapa fijo, lo ven todos los perfiles)
    case 'puntos_atencion':
        $puntoController->mostrarPuntos();
        break;

    // PANELES EN CONSTRUCCIÓN (se reemplazan cuando se desarrolle cada módulo)
    case 'panel_encuestador':
        requerirRol([3]);
        echo "<h2>Panel del encuestador (en construcción)</h2><a href='index.php?action=logout'>Cerrar sesión</a>";
        break;
    case 'panel_calidad':
        requerirRol([4]);
        echo "<h2>Panel de calidad (en construcción)</h2><a href='index.php?action=logout'>Cerrar sesión</a>";
        break;
    case 'panel_admin':
        requerirRol([5]);
        echo "<h2>Panel del administrador (en construcción)</h2><a href='index.php?action=logout'>Cerrar sesión</a>";
        break;

    default:
        $authController->mostrarLogin();
        break;
}
?>
