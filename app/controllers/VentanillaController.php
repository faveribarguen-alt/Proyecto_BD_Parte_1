<?php
require_once __DIR__ . '/../models/SolicitudModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class VentanillaController {

    // Muestra el formulario y le pasa la lista de barrios
    public function mostrarFormulario() {
        requerirRol([2]); // Solo ventanilla

        $modelo = new SolicitudModel();
        $barrios = $modelo->obtenerBarrios();

        require_once __DIR__ . '/../views/ventanilla/registrar.php';
    }

    // Recibe los datos del POST y los manda a guardar
    public function procesarSolicitud() {
        requerirRol([2]); // Solo ventanilla

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $modelo = new SolicitudModel();

            // Datos de quien está atendiendo: usuario y punto de atención
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
}
?>
