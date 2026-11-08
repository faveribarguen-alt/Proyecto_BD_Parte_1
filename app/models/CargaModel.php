<?php
require_once __DIR__ . '/../config/conexion.php';

// Una "carga" es la hoja de ruta: el grupo de solicitudes asignado a un encuestador
class CargaModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Solicitudes que todavía no están en ninguna hoja de ruta
    public function contarPendientes() {
        $query = "SELECT COUNT(*) FROM solicitudes WHERE estado = 'radicada' AND id_carga IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerEncuestadores() {
        $query = "SELECT id_usuario, nombres, apellidos
                  FROM usuarios
                  WHERE id_rol = 3 AND estado = 'activo'
                  ORDER BY nombres, apellidos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea la hoja de ruta con hasta $limite solicitudes, las más urgentes primero.
    // Todo ocurre en una transacción: o se guarda completo o no se guarda nada.
    public function generarCarga($idEncuestador, $idEnrutador, $fechaVisita, $limite = 20) {
        try {
            // TCL: inicio de la transacción
            $this->conn->beginTransaction();

            // El encuestador debe existir, estar activo y tener el rol 3
            $stmt = $this->conn->prepare(
                "SELECT id_usuario FROM usuarios WHERE id_usuario = :id AND id_rol = 3 AND estado = 'activo'"
            );
            $stmt->bindValue(':id', $idEncuestador, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt->fetch()) {
                $this->conn->rollBack();
                return ['ok' => false, 'error' => 'El encuestador seleccionado no es válido.'];
            }

            // Solicitudes pendientes: primero las que vencen antes, agrupadas por comuna y barrio.
            // FOR UPDATE las bloquea para que otro enrutador no las tome al mismo tiempo.
            $stmt = $this->conn->prepare(
                "SELECT s.id_solicitud
                 FROM solicitudes s
                 INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
                 INNER JOIN barrios b ON so.id_barrio = b.id_barrio
                 WHERE s.estado = 'radicada' AND s.id_carga IS NULL
                 ORDER BY s.fecha_limite ASC, b.id_comuna ASC, b.id_barrio ASC, s.id_solicitud ASC
                 LIMIT :limite
                 FOR UPDATE"
            );
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
            $stmt->execute();
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($ids) === 0) {
                $this->conn->rollBack();
                return ['ok' => false, 'error' => 'No hay solicitudes pendientes para asignar.'];
            }

            // DML: se crea la hoja de ruta
            $stmt = $this->conn->prepare(
                "INSERT INTO cargas (id_encuestador, id_enrutador, fecha_visita, estado)
                 VALUES (:enc, :enr, :fecha, 'programada')"
            );
            $stmt->bindValue(':enc', $idEncuestador, PDO::PARAM_INT);
            $stmt->bindValue(':enr', $idEnrutador, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $fechaVisita);
            $stmt->execute();
            $idCarga = (int) $this->conn->lastInsertId();

            // DML: cada solicitud queda ligada a la hoja y pasa a "asignada", con su registro en el historial
            $upd = $this->conn->prepare(
                "UPDATE solicitudes SET id_carga = :carga, estado = 'asignada' WHERE id_solicitud = :sol"
            );
            $hist = $this->conn->prepare(
                "INSERT INTO historial_estados (id_solicitud, estado, id_usuario) VALUES (:sol, 'asignada', :usr)"
            );
            foreach ($ids as $idSolicitud) {
                $upd->bindValue(':carga', $idCarga, PDO::PARAM_INT);
                $upd->bindValue(':sol', (int) $idSolicitud, PDO::PARAM_INT);
                $upd->execute();

                $hist->bindValue(':sol', (int) $idSolicitud, PDO::PARAM_INT);
                $hist->bindValue(':usr', $idEnrutador, PDO::PARAM_INT);
                $hist->execute();
            }

            // TCL: todo salió bien, se confirma
            $this->conn->commit();
            return ['ok' => true, 'id_carga' => $idCarga, 'cantidad' => count($ids)];

        } catch (PDOException $e) {
            // TCL: algo falló, se deshace todo
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo generar la hoja de ruta. Intenta de nuevo.'];
        }
    }

    // Lista de hojas de ruta para el enrutador
    public function listarCargas() {
        $query = "SELECT c.id_carga, c.fecha_visita, c.estado, c.creada_en, c.entregada_en,
                         CONCAT(u.nombres, ' ', u.apellidos) AS encuestador,
                         COUNT(s.id_solicitud) AS total
                  FROM cargas c
                  INNER JOIN usuarios u ON c.id_encuestador = u.id_usuario
                  LEFT JOIN solicitudes s ON s.id_carga = c.id_carga
                  GROUP BY c.id_carga, c.fecha_visita, c.estado, c.creada_en, c.entregada_en, u.nombres, u.apellidos
                  ORDER BY c.creada_en DESC, c.id_carga DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Datos de control de una hoja de ruta
    public function obtenerCarga($idCarga) {
        $query = "SELECT c.id_carga, c.id_encuestador, c.fecha_visita, c.estado, c.creada_en, c.entregada_en,
                         CONCAT(ue.nombres, ' ', ue.apellidos) AS encuestador,
                         ue.id_punto AS punto_encuestador,
                         p.nombre AS punto,
                         CONCAT(ur.nombres, ' ', ur.apellidos) AS enrutador,
                         CONCAT(ud.nombres, ' ', ud.apellidos) AS entregada_por
                  FROM cargas c
                  INNER JOIN usuarios ue ON c.id_encuestador = ue.id_usuario
                  INNER JOIN usuarios ur ON c.id_enrutador = ur.id_usuario
                  LEFT JOIN usuarios ud ON c.id_usuario_entrega = ud.id_usuario
                  LEFT JOIN puntos_atencion p ON ue.id_punto = p.id_punto
                  WHERE c.id_carga = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $idCarga, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Las solicitudes (visitas) que trae una hoja de ruta
    public function obtenerSolicitudesDeCarga($idCarga) {
        $query = "SELECT s.id_solicitud, s.radicado, s.fecha_limite,
                         so.nombres, so.apellidos, so.telefono, so.direccion,
                         b.nombre AS barrio, cm.nombre AS comuna,
                         g.latitud, g.longitud
                  FROM solicitudes s
                  INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
                  INNER JOIN barrios b ON so.id_barrio = b.id_barrio
                  INNER JOIN comunas cm ON b.id_comuna = cm.id_comuna
                  LEFT JOIN georreferenciacion g ON s.id_solicitud = g.id_solicitud
                  WHERE s.id_carga = :id
                  ORDER BY cm.id_comuna, b.nombre, so.direccion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $idCarga, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hojas de ruta de los encuestadores de un punto de atención (para que ventanilla las entregue)
    public function listarPorPunto($idPunto) {
        $query = "SELECT c.id_carga, c.fecha_visita, c.estado, c.entregada_en,
                         (c.fecha_visita <= CURDATE()) AS puede_entregar,
                         CONCAT(u.nombres, ' ', u.apellidos) AS encuestador,
                         (SELECT COUNT(*) FROM solicitudes s WHERE s.id_carga = c.id_carga) AS total
                  FROM cargas c
                  INNER JOIN usuarios u ON c.id_encuestador = u.id_usuario
                  WHERE u.id_punto = :punto AND c.estado IN ('programada', 'en_campo')
                  ORDER BY c.fecha_visita DESC, c.id_carga DESC
                  LIMIT 50";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ventanilla entrega la hoja: solo si es de su punto, está programada y ya llegó la fecha de visita
    public function marcarEntregada($idCarga, $idUsuario, $idPunto) {
        $query = "UPDATE cargas c
                  INNER JOIN usuarios ue ON c.id_encuestador = ue.id_usuario
                  SET c.estado = 'en_campo', c.entregada_en = NOW(), c.id_usuario_entrega = :usuario
                  WHERE c.id_carga = :id
                    AND c.estado = 'programada'
                    AND c.fecha_visita <= CURDATE()
                    AND ue.id_punto = :punto";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id', $idCarga, PDO::PARAM_INT);
        $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
