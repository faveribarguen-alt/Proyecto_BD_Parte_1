<?php
require_once __DIR__ . '/../config/conexion.php';

class InformeModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Cuántas solicitudes hay por estado en un rango de fechas
    public function solicitudesPorEstado($desde, $hasta) {
        $query = "SELECT estado, COUNT(*) AS total
                  FROM solicitudes
                  WHERE fecha_radicacion >= :desde
                    AND fecha_radicacion < DATE_ADD(:hasta, INTERVAL 1 DAY)
                  GROUP BY estado";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':desde', $desde);
        $stmt->bindValue(':hasta', $hasta);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // cuántas solicitudes hay por punto de atención
    public function solicitudesPorPunto($desde, $hasta) {
        $query = "SELECT p.nombre, COUNT(*) AS total
                    FROM solicitudes s
                    INNER JOIN puntos_atencion p ON s.id_punto = p.id_punto
                    WHERE s.fecha_radicacion >= :desde
                    AND s.fecha_radicacion < DATE_ADD(:hasta, INTERVAL 1 DAY)
                    GROUP BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':desde', $desde);
        $stmt->bindValue(':hasta', $hasta);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Solicitudes vencidas o que vencen en los próximos $dias días
    public function solicitudesPorVencer($dias) {
        $query = "SELECT v.radicado, so.nombres, so.apellidos, p.nombre AS punto,
                    v.estado, v.fecha_limite, v.dias_restantes

                FROM v_solicitudes_por_vencer v
                INNER JOIN solicitudes s ON s.id_solicitud = v.id_solicitud
                INNER JOIN solicitantes so ON so.id_solicitante = s.id_solicitante
                INNER JOIN puntos_atencion p ON p.id_punto = s.id_punto
                WHERE v.dias_restantes <= :dias
                ORDER BY v.dias_restantes ASC"; 

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':dias', (int) $dias, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>