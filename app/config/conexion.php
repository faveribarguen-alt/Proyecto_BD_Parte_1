<?php
class Database {
    private $host = "localhost";
    private $db_name = "encuestas_db"; // El nombre de la BD local
    private $username = "encuestas_app";
    private $password = "20252724"; // La contraseña de la BD local
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Se utiliza PDO como exige el requerimiento
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Configurar PDO para que lance excepciones si hay errores (Buena práctica)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Forzar el uso de caracteres UTF-8
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>