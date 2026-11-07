<?php
require_once __DIR__ . '/../models/SolicitudModel.php';

class VentanillaController {
    
    // Muestra el formulario y le pasa la lista de barrios
    public function mostrarFormulario() {
        session_start();
        // Validación de seguridad: Solo el rol 2 (Ventanilla) debería ver esto
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $modelo = new SolicitudModel();
        $barrios = $modelo->obtenerBarrios(); // Traemos los barrios para el <select>
        
        require_once __DIR__ . '/../views/ventanilla/registrar.php';
    }

    // Recibe los datos del POST y los manda a guardar
    public function procesarSolicitud() {
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $modelo = new SolicitudModel();
            
            // Agregamos el ID del usuario que está logueado haciendo el registro
            $_POST['id_usuario_ventanilla'] = $_SESSION['id_usuario'];
            
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