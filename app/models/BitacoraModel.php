<?php
require_once __DIR__ . '/../config/conexion.php';

class BitacoraModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Solicitudes radicadas en un día, de todos los puntos de atención
    public function obtenerSolicitudesDelDia($fecha) {
        $query = "SELECT s.id_solicitud, s.radicado, s.estado, s.fecha_radicacion,
                         so.nombres, so.apellidos, so.direccion,
                         b.nombre AS barrio, c.nombre AS comuna,
                         p.nombre AS punto,
                         g.latitud, g.longitud
                  FROM solicitudes s
                  INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
                  INNER JOIN barrios b ON so.id_barrio = b.id_barrio
                  INNER JOIN comunas c ON b.id_comuna = c.id_comuna
                  INNER JOIN puntos_atencion p ON s.id_punto = p.id_punto
                  LEFT JOIN georreferenciacion g ON s.id_solicitud = g.id_solicitud
                  WHERE s.fecha_radicacion >= :inicio
                    AND s.fecha_radicacion < DATE_ADD(:fin, INTERVAL 1 DAY)
                  ORDER BY s.fecha_radicacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':inicio', $fecha);
        $stmt->bindValue(':fin', $fecha);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
