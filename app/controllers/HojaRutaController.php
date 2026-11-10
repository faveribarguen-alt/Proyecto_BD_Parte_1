<?php
require_once __DIR__ . '/../models/CargaModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class HojaRutaController {

    // ENRUTADOR: pantalla para generar hojas de ruta y ver las ya creadas
    public function listar() {
        requerirRol([1]);

        $modelo = new CargaModel();
        $pendientes = $modelo->contarPendientes();
        $encuestadores = $modelo->obtenerEncuestadores();
        $cargas = $modelo->listarCargas();
        $fechaSugerida = (new DateTime('tomorrow'))->format('Y-m-d');
        $csrf = tokenCsrf();

        require_once __DIR__ . '/../views/hojas/lista.php';
    }

    // ENRUTADOR: genera una hoja de ruta de hasta 20 solicitudes
    public function generar() {
        requerirRol([1]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf()) {
            guardarMensaje('error', 'La solicitud no es válida. Intenta de nuevo.');
            header("Location: index.php?action=hojas_ruta");
            exit();
        }

        $idEncuestador = filter_var($_POST['id_encuestador'] ?? '', FILTER_VALIDATE_INT);
        $fecha = $_POST['fecha_visita'] ?? '';
        $fechaObj = DateTime::createFromFormat('!Y-m-d', $fecha);
        $hoy = new DateTime('today');

        if (!$idEncuestador || !$fechaObj || $fechaObj->format('Y-m-d') !== $fecha || $fechaObj < $hoy) {
            guardarMensaje('error', 'Elige un encuestador y una fecha de visita válida (hoy o después).');
            header("Location: index.php?action=hojas_ruta");
            exit();
        }

        $modelo = new CargaModel();
        $resultado = $modelo->generarCarga($idEncuestador, $_SESSION['id_usuario'], $fecha, 20);

        if ($resultado['ok']) {
            guardarMensaje('ok', 'Hoja de ruta #' . $resultado['id_carga'] . ' generada con ' . $resultado['cantidad'] . ' solicitudes.');
            header("Location: index.php?action=ver_hoja&id=" . $resultado['id_carga']);
        } else {
            guardarMensaje('error', $resultado['error']);
            header("Location: index.php?action=hojas_ruta");
        }
        exit();
    }

    // Ver una hoja de ruta completa (imprimible)
    public function ver() {
        requerirRol([1, 2, 3, 5]);

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $modelo = new CargaModel();
        $carga = $id ? $modelo->obtenerCarga($id) : false;

        if (!$carga) {
            http_response_code(404);
            echo "<h2>Hoja de ruta no encontrada.</h2>";
            exit();
        }

        // Ventanilla solo ve las hojas de su punto; el encuestador solo la suya
        $rol = (int) $_SESSION['id_rol'];
        if ($rol === 2 && (int) $carga['punto_encuestador'] !== (int) $_SESSION['id_punto']) {
            http_response_code(403);
            echo "<h2>Esta hoja de ruta no es de tu punto de atención.</h2>";
            exit();
        }
        if ($rol === 3 && (int) $carga['id_encuestador'] !== (int) $_SESSION['id_usuario']) {
            http_response_code(403);
            echo "<h2>Esta hoja de ruta no es tuya.</h2>";
            exit();
        }

        $solicitudes = $modelo->obtenerSolicitudesDeCarga($id);
        require_once __DIR__ . '/../views/hojas/ver.php';
    }

    // VENTANILLA: hojas de ruta por entregar a los encuestadores de su punto
    public function listarEntregas() {
        requerirRol([2]);

        $modelo = new CargaModel();
        $cargas = $modelo->listarPorPunto((int) $_SESSION['id_punto']);
        $csrf = tokenCsrf();

        require_once __DIR__ . '/../views/hojas/entregas.php';
    }

    // VENTANILLA: registra que la hoja fue entregada al encuestador
    public function entregar() {
        requerirRol([2]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf()) {
            guardarMensaje('error', 'La solicitud no es válida. Intenta de nuevo.');
            header("Location: index.php?action=ventanilla_hojas");
            exit();
        }

        $id = filter_var($_POST['id_carga'] ?? '', FILTER_VALIDATE_INT);
        $modelo = new CargaModel();

        if ($id && $modelo->marcarEntregada($id, (int) $_SESSION['id_usuario'], (int) $_SESSION['id_punto'])) {
            guardarMensaje('ok', 'Hoja de ruta #' . $id . ' entregada al encuestador.');
        } else {
            guardarMensaje('error', 'No se pudo entregar: revisa que sea de tu punto, que esté programada y que ya sea el día de la visita.');
        }
        header("Location: index.php?action=ventanilla_hojas");
        exit();
    }
}
?>
