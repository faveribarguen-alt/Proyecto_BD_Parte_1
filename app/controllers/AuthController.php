<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {
    
    // Método que muestra la pantalla visual
    public function mostrarLogin() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Método que procesa los datos del formulario
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->validarAcceso($username, $password);

            if ($usuario) {
                // Iniciamos la sesión segura y guardamos los datos importantes
                session_start();
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre_completo'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                $_SESSION['id_rol'] = $usuario['id_rol'];

                // Redirigir según el rol (Por ahora, todos van al inicio, luego los separamos)
                header("Location: index.php?action=inicio");
                exit();
            } else {
                // Si falla, volvemos a mostrar el login con un mensaje de error
                $error = "Usuario o contraseña incorrectos.";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        }
    }

    // Método para cerrar sesión
    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>