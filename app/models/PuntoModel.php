<?php
require_once __DIR__ . '/../config/conexion.php';

class PuntoModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Puntos de atención activos, con el nombre de su comuna
    public function obtenerPuntos() {
        $query = "SELECT p.id_punto, p.nombre, p.direccion, p.es_principal,
                         p.latitud, p.longitud, c.nombre AS comuna
                  FROM puntos_atencion p
                  LEFT JOIN comunas c ON p.id_comuna = c.id_comuna
                  WHERE p.activo = 1
                  ORDER BY p.es_principal DESC, p.id_comuna";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
