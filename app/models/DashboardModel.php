<?php
require_once __DIR__ . '/../config/conexion.php';

class DashboardModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Los cuatro números de las tarjetas, en una sola consulta
    public function totales() {
        $query = "SELECT
                    (SELECT COUNT(*) FROM solicitudes) AS total,
                    (SELECT COUNT(*) FROM solicitudes
                      WHERE estado = 'radicada' AND id_carga IS NULL) AS pendientes,
                    (SELECT COUNT(*) FROM v_solicitudes_por_vencer
                      WHERE dias_restantes < 0) AS vencidas,
                    (SELECT COUNT(*) FROM cargas WHERE estado = 'en_campo') AS en_campo";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Solicitudes radicadas en cada uno de los últimos 7 días
    public function ultimosSieteDias() {
        $query = "SELECT DATE(fecha_radicacion) AS dia, COUNT(*) AS total
                  FROM solicitudes
                  WHERE fecha_radicacion >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                  GROUP BY DATE(fecha_radicacion)
                  ORDER BY dia ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //cuántas solicitudes hay en cada estado (todas las fechas)
    public function porEstado() {
        $query = "SELECT estado, COUNT(*) AS total
                  FROM solicitudes
                  GROUP BY estado";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //cuántas solicitudes hay en cada comuna 
    public function porComuna() {
        $query = "SELECT c.nombre AS comuna, COUNT(*) AS total
                  FROM solicitudes s
                  INNER JOIN solicitantes so ON so.id_solicitante = s.id_solicitante
                  INNER JOIN barrios b ON b.id_barrio = so.id_barrio
                  INNER JOIN comunas c ON c.id_comuna = b.id_comuna
                  GROUP BY c.nombre
                  ORDER BY total DESC;";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>