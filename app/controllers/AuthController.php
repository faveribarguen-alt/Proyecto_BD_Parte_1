<?php
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class AuthController {

    // Método que muestra la pantalla visual
    public function mostrarLogin() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Método que procesa los datos del formulario
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->validarAcceso($username, $password);

            if ($usuario) {
                iniciarSesionSegura();
                // Nueva identificación de sesión al entrar (evita robo de sesión)
                session_regenerate_id(true);

                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre_completo'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                $_SESSION['id_rol'] = $usuario['id_rol'];
                $_SESSION['id_punto'] = $usuario['id_punto']; // Punto de atención donde trabaja

                // Redirigir según el rol del usuario
                switch ((int) $_SESSION['id_rol']) {
                    case 1: // Enrutador
                        header("Location: index.php?action=bitacora");
                        break;
                    case 2: // Ventanilla
                        header("Location: index.php?action=ventanilla_registrar");
                        break;
                    case 3: // Encuestador
                        header("Location: index.php?action=panel_encuestador");
                        break;
                    case 4: // Calidad
                        header("Location: index.php?action=panel_calidad");
                        break;
                    case 5: // Administrador
                        header("Location: index.php?action=panel_admin");
                        break;
                    default:
                        header("Location: index.php?action=login");
                        break;
                }
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos.";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        }
    }

    // Método para cerrar sesión
    public function logout() {
        iniciarSesionSegura();
        $_SESSION = [];
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>
