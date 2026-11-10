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
}
?>