<?php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para validar el login
    public function validarAcceso($username, $password) {
        // PREVENCIÓN DE INYECCIÓN SQL: Usamos consultas preparadas (prepare)
        $query = "SELECT id_usuario, nombres, apellidos, password, id_rol FROM usuarios WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // SEGURIDAD: Verificamos el hash de la contraseña
            if (password_verify($password, $row['password'])) {
                // Si la contraseña coincide, devolvemos los datos del usuario (sin el password)
                unset($row['password']);
                return $row;
            }
        }
        return false; // Retorna falso si el usuario no existe o la clave es incorrecta
    }
}
?>