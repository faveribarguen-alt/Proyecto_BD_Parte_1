<?php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para validar el login (el "username" del formulario es el correo)
    public function validarAcceso($username, $password) {
        // Consulta preparada para evitar inyección SQL. Solo entran usuarios activos.
        $query = "SELECT id_usuario, nombres, apellidos, password_hash AS password, id_rol, id_punto
                  FROM usuarios
                  WHERE correo = :correo AND estado = 'activo'
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos el hash de la contraseña
            if (password_verify($password, $row['password'])) {
                unset($row['password']);
                return $row;
            }
        }
        return false;
    }
}
?>
