<?php
require_once __DIR__ . '/../models/SolicitudModel.php';
require_once __DIR__ . '/../models/CargaModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class VentanillaController {

    // Dashboard principal
    public function mostrarInicio() {
        requerirRol([2]);

        $idPunto = (int) $_SESSION['id_punto'];

        $solicitudModel = new SolicitudModel();
        $cargaModel = new CargaModel();

        $resumen = $solicitudModel->obtenerResumenVentanilla($idPunto);
        $solicitudesRecientes = $solicitudModel->obtenerSolicitudesRecientes($idPunto);
        $hojasPendientes = $cargaModel->contarHojasListasEntrega($idPunto);

        require_once __DIR__ . '/../views/ventanilla/inicio.php';
    }

    // Muestra el formulario y la lista de barrios
    public function mostrarFormulario() {
        requerirRol([2]);

        $modelo = new SolicitudModel();
        $barrios = $modelo->obtenerBarrios();

        require_once __DIR__ . '/../views/ventanilla/registrar.php';
    }

    // Procesa el formulario de registro
    public function procesarSolicitud() {
        requerirRol([2]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $modelo = new SolicitudModel();

            $_POST['id_usuario_ventanilla'] = $_SESSION['id_usuario'];
            $_POST['id_punto'] = $_SESSION['id_punto'];

            if (empty($_POST['id_punto'])) {
                echo "Tu usuario no tiene un punto de atención asignado. Habla con el administrador.";
                return;
            }

            $resultado = $modelo->guardarSolicitud($_POST);

            if ($resultado) {
                $mensaje = "Solicitud guardada con éxito y georreferenciada correctamente.";
                $barrios = $modelo->obtenerBarrios();

                require_once __DIR__ . '/../views/ventanilla/registrar.php';

            } else {
                echo "Hubo un error al guardar la solicitud.";
            }
        }
    }

// Consultar solicitudes registradas en el punto de atención
public function consultarSolicitudes() {

    requerirRol([2]);

    $idPunto = (int)($_SESSION['id_punto'] ?? 0);

    if ($idPunto <= 0) {
        die("Tu usuario no tiene un punto de atención asignado.");
    }

    $modelo = new SolicitudModel();

    $busqueda = trim($_GET['buscar'] ?? '');
    $estado = trim($_GET['estado'] ?? '');

    $solicitudes = $modelo->obtenerSolicitudesVentanilla(
        $idPunto,
        $busqueda,
        $estado
    );

    $estados = $modelo->obtenerEstadosVentanilla($idPunto);
    $estadisticas = $modelo->obtenerEstadisticasConsulta($idPunto);

    // Evita filtrar por un estado que no pertenece a este punto.
    if ($estado !== '' && !in_array($estado, $estados, true)) {
        $estado = '';
        $solicitudes = $modelo->obtenerSolicitudesVentanilla(
            $idPunto,
            $busqueda,
            ''
        );
    }

    require __DIR__ . '/../views/ventanilla/consultar.php';
}

}
?>
