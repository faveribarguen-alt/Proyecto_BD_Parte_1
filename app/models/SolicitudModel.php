<?php
require_once __DIR__ . '/../config/conexion.php';

class SolicitudModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para obtener la lista de barrios y sus comunas para el <select>
    public function obtenerBarrios() {
        $query = "SELECT b.id_barrio, b.nombre, c.nombre as comuna 
                  FROM barrios b 
                  INNER JOIN comunas c ON b.id_comuna = c.id_comuna 
                  ORDER BY c.nombre, b.nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para guardar la solicitud usando TCL (Transacciones)
    public function guardarSolicitud($datos) {
        try {
            // TCL: Iniciamos la transacción (BEGIN TRANSACTION)
            $this->conn->beginTransaction();

            // 1. Guardar primero al ciudadano en 'solicitantes'
            $querySolicitante = "INSERT INTO solicitantes (tipo_documento, numero_documento, nombres, apellidos, telefono, direccion, id_barrio) 
                                 VALUES (:tipo_doc, :num_doc, :nombres, :apellidos, :telefono, :direccion, :id_barrio)";
            $stmt1 = $this->conn->prepare($querySolicitante);
            $stmt1->bindParam(':tipo_doc', $datos['tipo_documento']);
            $stmt1->bindParam(':num_doc', $datos['numero_documento']);
            $stmt1->bindParam(':nombres', $datos['nombres']);
            $stmt1->bindParam(':apellidos', $datos['apellidos']);
            $stmt1->bindParam(':telefono', $datos['celular']); // Mapeamos 'celular' a 'telefono'
            $stmt1->bindParam(':direccion', $datos['direccion']);
            $stmt1->bindParam(':id_barrio', $datos['id_barrio']);
            $stmt1->execute();

            // Capturamos el ID del solicitante recién creado
            $id_solicitante = $this->conn->lastInsertId();

            // 2. Guardar el trámite en 'solicitudes'
            $radicado = 'RAD-' . date('Ymd-His'); // Generamos un radicado único automático
            $fecha_limite = date('Y-m-d', strtotime('+15 days')); // Fecha límite por defecto (15 días)
            $id_punto = $datos['id_punto']; // Punto de atención del usuario que atiende
            
            $querySolicitud = "INSERT INTO solicitudes (radicado, id_solicitante, id_punto, id_usuario_radica, fecha_limite, estado) 
                               VALUES (:radicado, :id_solicitante, :id_punto, :id_usuario, :fecha_limite, 'radicada')";
            $stmt2 = $this->conn->prepare($querySolicitud);
            $stmt2->bindParam(':radicado', $radicado);
            $stmt2->bindParam(':id_solicitante', $id_solicitante);
            $stmt2->bindParam(':id_punto', $id_punto);
            $stmt2->bindParam(':id_usuario', $datos['id_usuario_ventanilla']);
            $stmt2->bindParam(':fecha_limite', $fecha_limite);
            $stmt2->execute();

            // Capturamos el ID de la solicitud recién creada
            $id_solicitud = $this->conn->lastInsertId();

            // 3. Guardar las coordenadas en 'georreferenciacion'
            $queryGeo = "INSERT INTO georreferenciacion (id_solicitud, latitud, longitud, fuente) 
                         VALUES (:id_solicitud, :latitud, :longitud, 'ventanilla')";
            $stmt3 = $this->conn->prepare($queryGeo);
            $stmt3->bindParam(':id_solicitud', $id_solicitud);
            $stmt3->bindParam(':latitud', $datos['latitud']);
            $stmt3->bindParam(':longitud', $datos['longitud']);
            $stmt3->execute();

            // TCL: Si todo salió bien, confirmamos los cambios (COMMIT)
            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            // TCL: Si hubo algún error, revertimos todo para no guardar basura (ROLLBACK)
            $this->conn->rollBack();
            die("Error al guardar: " . $e->getMessage()); 
            return false;
        }
    }

    // Método para la bitácora del enrutador: trae todas las solicitudes con su ubicación completa
    public function obtenerTodasSolicitudes() {
        $query = "SELECT 
                    s.id_solicitud, 
                    s.radicado, 
                    s.estado,
                    so.nombres, 
                    so.apellidos, 
                    so.direccion, 
                    b.nombre as barrio, 
                    c.nombre as comuna, 
                    s.fecha_radicacion, 
                    g.latitud, 
                    g.longitud 
                  FROM solicitudes s
                  INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
                  INNER JOIN barrios b ON so.id_barrio = b.id_barrio
                  INNER JOIN comunas c ON b.id_comuna = c.id_comuna
                  LEFT JOIN georreferenciacion g ON s.id_solicitud = g.id_solicitud
                  ORDER BY s.fecha_radicacion DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Estadísticas del dashboard de Ventanilla
public function obtenerResumenVentanilla($idPunto) {

    $query = "SELECT
                COUNT(*) AS total_solicitudes,

                SUM(
                    CASE
                        WHEN DATE(s.fecha_radicacion) = CURDATE()
                        THEN 1 ELSE 0
                    END
                ) AS solicitudes_hoy,

                SUM(
                    CASE
                        WHEN g.id_solicitud IS NULL
                        THEN 1 ELSE 0
                    END
                ) AS sin_georreferenciar,

                SUM(
                    CASE
                        WHEN s.fecha_limite BETWEEN CURDATE()
                        AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                        THEN 1 ELSE 0
                    END
                ) AS proximas_a_vencer

              FROM solicitudes s
              LEFT JOIN georreferenciacion g
                ON s.id_solicitud = g.id_solicitud
              WHERE s.id_punto = :punto";

    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Solicitudes recientes del punto de atención
public function obtenerSolicitudesRecientes($idPunto) {

    $query = "SELECT
                s.id_solicitud,
                s.radicado,
                s.estado,
                s.fecha_radicacion,
                s.fecha_limite,
                so.nombres,
                so.apellidos,
                b.nombre AS barrio,
                cm.nombre AS comuna,
                g.id_solicitud AS tiene_georreferenciacion

              FROM solicitudes s

              INNER JOIN solicitantes so
                ON s.id_solicitante = so.id_solicitante

              INNER JOIN barrios b
                ON so.id_barrio = b.id_barrio

              INNER JOIN comunas cm
                ON b.id_comuna = cm.id_comuna

              LEFT JOIN georreferenciacion g
                ON s.id_solicitud = g.id_solicitud

              WHERE s.id_punto = :punto

              ORDER BY s.fecha_radicacion DESC

              LIMIT 8";

    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }       


// Consultar solicitudes del punto de atención

public function obtenerSolicitudesVentanilla($idPunto, $busqueda = '', $estado = '') {

    $query = "SELECT
                s.id_solicitud,
                s.radicado,
                s.estado,
                s.fecha_radicacion,
                s.fecha_limite,
                so.tipo_documento,
                so.numero_documento,
                so.nombres,
                so.apellidos,
                so.telefono,
                so.direccion,
                b.nombre AS barrio,
                cm.nombre AS comuna
              FROM solicitudes s
              INNER JOIN solicitantes so
                ON s.id_solicitante = so.id_solicitante
              INNER JOIN barrios b
                ON so.id_barrio = b.id_barrio
              INNER JOIN comunas cm
                ON b.id_comuna = cm.id_comuna
              WHERE s.id_punto = :punto";

    if ($busqueda !== '') {

        if (ctype_digit($busqueda)) {
            // Si se escribe un número, la cédula debe coincidir exactamente.
            $query .= " AND (
                so.numero_documento = :documento
                OR s.radicado LIKE :radicado
            )";

        } else {
            // Permite buscar por nombre, apellido o nombre completo.
            $query .= " AND (
                so.nombres LIKE :nombres
                OR so.apellidos LIKE :apellidos
                OR CONCAT(so.nombres, ' ', so.apellidos) LIKE :nombre_completo
                OR CONCAT(so.apellidos, ' ', so.nombres) LIKE :nombre_invertido
                OR s.radicado LIKE :radicado
            )";
        }
    }

    if ($estado !== '') {
        $query .= " AND s.estado = :estado";
    }

    $query .= " ORDER BY s.fecha_radicacion DESC";

    $stmt = $this->conn->prepare($query);

    $stmt->bindValue(':punto', (int)$idPunto, PDO::PARAM_INT);

    if ($busqueda !== '') {

        if (ctype_digit($busqueda)) {
            $stmt->bindValue(':documento', $busqueda);
            $stmt->bindValue(':radicado', '%' . $busqueda . '%');

        } else {
            $termino = '%' . $busqueda . '%';

            $stmt->bindValue(':nombres', $termino);
            $stmt->bindValue(':apellidos', $termino);
            $stmt->bindValue(':nombre_completo', $termino);
            $stmt->bindValue(':nombre_invertido', $termino);
            $stmt->bindValue(':radicado', $termino);
        }
    }

    if ($estado !== '') {
        $stmt->bindValue(':estado', $estado);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Estados existentes en las solicitudes del punto
public function obtenerEstadosVentanilla($idPunto) {

    $query = "SELECT DISTINCT estado
              FROM solicitudes
              WHERE id_punto = :punto
              ORDER BY estado";

    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':punto', (int)$idPunto, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


// Estadísticas para la pantalla de consulta
public function obtenerEstadisticasConsulta($idPunto) {

    $query = "SELECT
                COUNT(*) AS total,
                SUM(
                    CASE
                        WHEN DATE(fecha_radicacion) = CURDATE()
                        THEN 1 ELSE 0
                    END
                ) AS hoy,
                SUM(
                    CASE
                        WHEN fecha_limite < CURDATE()
                        THEN 1 ELSE 0
                    END
                ) AS vencidas
              FROM solicitudes
              WHERE id_punto = :punto";

    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':punto', (int)$idPunto, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Resumen general para el dashboard del Enrutador
public function obtenerResumenEnrutador() {

    $query = "SELECT
                COUNT(*) AS total_solicitudes,

                SUM(
                    CASE
                        WHEN s.estado = 'radicada'
                             AND s.id_carga IS NULL
                        THEN 1 ELSE 0
                    END
                ) AS pendientes_asignacion,

                SUM(
                    CASE
                        WHEN g.id_solicitud IS NULL
                             OR g.latitud IS NULL
                             OR g.longitud IS NULL
                        THEN 1 ELSE 0
                    END
                ) AS sin_georreferenciar

              FROM solicitudes s
              LEFT JOIN georreferenciacion g
                ON s.id_solicitud = g.id_solicitud";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Solicitudes recientes de todos los puntos de atención
public function obtenerSolicitudesRecientesEnrutador() {

    $query = "SELECT
                s.id_solicitud,
                s.radicado,
                s.estado,
                s.fecha_radicacion,
                s.fecha_limite,
                so.nombres,
                so.apellidos,
                so.direccion,
                b.nombre AS barrio,
                cm.nombre AS comuna,
                p.nombre AS punto_atencion,
                g.latitud,
                g.longitud

              FROM solicitudes s

              INNER JOIN solicitantes so
                ON s.id_solicitante = so.id_solicitante

              INNER JOIN barrios b
                ON so.id_barrio = b.id_barrio

              INNER JOIN comunas cm
                ON b.id_comuna = cm.id_comuna

              LEFT JOIN puntos_atencion p
                ON s.id_punto = p.id_punto

              LEFT JOIN georreferenciacion g
                ON s.id_solicitud = g.id_solicitud

              ORDER BY s.fecha_radicacion DESC, s.id_solicitud DESC

              LIMIT 8";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // ==========================================
    // GEORREFERENCIACIÓN Y ASIGNACIÓN DE PUNTOS
    // ==========================================

    // Obtener solicitudes con sus ubicaciones y puntos asignados
    public function obtenerDatosGeorreferenciacion() {

        $query = "SELECT
                    s.id_solicitud,
                    s.radicado,
                    s.estado,
                    s.id_punto,
                    s.id_punto_asignado,
                    s.fecha_radicacion,

                    so.nombres,
                    so.apellidos,
                    so.direccion,

                    b.nombre AS barrio,
                    cm.nombre AS comuna,

                    g.latitud,
                    g.longitud,

                    po.nombre AS punto_original,
                    pa.nombre AS punto_asignado

                  FROM solicitudes s

                  INNER JOIN solicitantes so
                    ON s.id_solicitante = so.id_solicitante

                  LEFT JOIN barrios b
                    ON so.id_barrio = b.id_barrio

                  LEFT JOIN comunas cm
                    ON b.id_comuna = cm.id_comuna

                  LEFT JOIN georreferenciacion g
                    ON s.id_solicitud = g.id_solicitud

                  LEFT JOIN puntos_atencion po
                    ON s.id_punto = po.id_punto

                  LEFT JOIN puntos_atencion pa
                    ON s.id_punto_asignado = pa.id_punto

                  ORDER BY s.fecha_radicacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Calcular distancia entre dos coordenadas usando Haversine
    private function calcularDistanciaKm($lat1, $lon1, $lat2, $lon2) {

        $radioTierra = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1))
           * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
    }


    // Asignar automáticamente el punto de atención más cercano
    public function asignarPuntosAutomaticamente() {

        // Consultar puntos activos que tengan coordenadas
        $queryPuntos = "SELECT
                            id_punto,
                            latitud,
                            longitud
                        FROM puntos_atencion
                        WHERE activo = 1
                          AND latitud IS NOT NULL
                          AND longitud IS NOT NULL";

        $stmtPuntos = $this->conn->prepare($queryPuntos);
        $stmtPuntos->execute();

        $puntos = $stmtPuntos->fetchAll(PDO::FETCH_ASSOC);

        // Sin puntos georreferenciados no se puede asignar
        if (empty($puntos)) {
            return 0;
        }

        // Consultar solicitudes que todavía no tienen punto asignado
        $querySolicitudes = "SELECT
                                s.id_solicitud,
                                g.latitud,
                                g.longitud
                             FROM solicitudes s
                             INNER JOIN georreferenciacion g
                                ON s.id_solicitud = g.id_solicitud
                             WHERE s.id_punto_asignado IS NULL
                               AND s.estado <> 'cancelada'
                               AND g.latitud IS NOT NULL
                               AND g.longitud IS NOT NULL";

        $stmtSolicitudes = $this->conn->prepare($querySolicitudes);
        $stmtSolicitudes->execute();

        $solicitudes = $stmtSolicitudes->fetchAll(PDO::FETCH_ASSOC);

        $asignadas = 0;

        $this->conn->beginTransaction();

        try {

            foreach ($solicitudes as $solicitud) {

                $puntoMasCercano = null;
                $distanciaMinima = PHP_FLOAT_MAX;

                foreach ($puntos as $punto) {

                    $distancia = $this->calcularDistanciaKm(
                        (float) $solicitud['latitud'],
                        (float) $solicitud['longitud'],
                        (float) $punto['latitud'],
                        (float) $punto['longitud']
                    );

                    if ($distancia < $distanciaMinima) {

                        $distanciaMinima = $distancia;
                        $puntoMasCercano = $punto['id_punto'];
                    }
                }

                if ($puntoMasCercano !== null) {

                    // Solo asignar si todavía no tiene un punto.
                    // Así se respetan los cambios manuales del Enrutador.
                    $queryActualizar = "UPDATE solicitudes
                                        SET id_punto_asignado = :punto
                                        WHERE id_solicitud = :solicitud
                                          AND id_punto_asignado IS NULL";

                    $stmtActualizar = $this->conn->prepare($queryActualizar);

                    $stmtActualizar->bindValue(
                        ':punto',
                        (int) $puntoMasCercano,
                        PDO::PARAM_INT
                    );

                    $stmtActualizar->bindValue(
                        ':solicitud',
                        (int) $solicitud['id_solicitud'],
                        PDO::PARAM_INT
                    );

                    $stmtActualizar->execute();

                    $asignadas += $stmtActualizar->rowCount();
                }
            }

            $this->conn->commit();

            return $asignadas;

        } catch (PDOException $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }


    // Cambiar manualmente el punto asignado por el Enrutador
    public function cambiarPuntoAsignado($idSolicitud, $idPunto) {

        // Comprobar que el punto exista y esté activo
        $queryPunto = "SELECT id_punto
                       FROM puntos_atencion
                       WHERE id_punto = :punto
                         AND activo = 1";

        $stmtPunto = $this->conn->prepare($queryPunto);
        $stmtPunto->bindValue(':punto', (int) $idPunto, PDO::PARAM_INT);
        $stmtPunto->execute();

        if (!$stmtPunto->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }

        // Actualizar únicamente el punto asignado.
        // El punto original de Ventanilla permanece intacto.
        $query = "UPDATE solicitudes
                  SET id_punto_asignado = :punto
                  WHERE id_solicitud = :solicitud
                    AND estado <> 'cancelada'";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':punto', (int) $idPunto, PDO::PARAM_INT);
        $stmt->bindValue(':solicitud', (int) $idSolicitud, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
?>  